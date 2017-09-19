<?php

class webCAT extends NQ_Auth_No
{
	public $class_title = "Web ChIP Association Tester";
	public $module_title = "CAT";

	public function __construct()
	{
		parent::__construct();

//		require_once( dirname(__FILE__)."/nconfig.php" );

		define("CAT_TYPE", "webCAT");
		define("CAT_TYPE_LC", strtolower(CAT_TYPE));

		define("SPECIES_ID", null);

		$this->uniq_id = uniqid( md5(microtime()) ); //a unique ID for this run
		$this->cron_dir = SYSTEM_DATA_ROOT . "/cron";
		$this->prefs = new stdClass();
		$this->prefs->tmp_dir = SYSTEM_TMPDIR . "/" . $this->uniq_id; //where we will store data
		$this->files = new stdClass();
		$this->error = array();


		if ( isset($_SESSION['errors']) )
			unset($_SESSION['errors']);
		if ( isset($_SESSION['success']) )
			unset($_SESSION['success']);

		if ( !defined("MAX_FILE_SIZE") || MAX_FILE_SIZE == 0)
			$this->set('max_filesize', 99999999999999);
		else
			$this->set('max_filesize', MAX_FILE_SIZE);

		if ( !defined("GENE_BEDFILE_MAX_REGIONS") )
			define("GENE_BEDFILE_MAX_REGIONS", 30);
		$this->set('max_gene_regions', GENE_BEDFILE_MAX_REGIONS);


		if ( !defined("MAX_CHIP_BEDFILES") )
			define("MAX_CHIP_BEDFILES", 50);
		$this->set('max_chip_bedfiles', MAX_CHIP_BEDFILES);

		if ( !defined("MIN_CHIP_BEDFILES") )
			define("MIN_CHIP_BEDFILES", 1);
		$this->set('min_chip_bedfiles', MIN_CHIP_BEDFILES);

		$this->prefs->genomes = get_genomes();
		$this->set('genomes', $this->prefs->genomes);
		unset($this->prefs->genomes);

        $load = @file_get_contents('/proc/loadavg');
        $load = explode(' ', $load, 1);
        $load = $load[0];

        $max_capacity = round( ($load / SYSTEM_CORES) * 100);
        if ( $max_capacity >= 100 ) {
   	        $this->session->load_alert = "<h2>ALERT</h2>All server processors are currently at {$max_capacity}% capacity.<br />If you submit an analysis, it will not execute until the capacity drops below 100%.";
	        $this->session->load_warnings = false;
		}elseif ( $load > LOAD_ALERT ) {
	        $this->session->load_warnings = "<h3>WARNING</h3>The server processors are currently at {$max_capacity}% capacity.<br />This means that runs will currently take longer than normal to complete.";
			$this->session->load_alert = false;
        } else {
	        $this->session->load_warnings = false;
	        $this->session->load_alert = false;
        }

	}

	public function __default()
	{


		if ( isset($_POST['formSubmitBtn']) ) {

			$this->process_data($_POST, $_FILES);

		}


	}

	private function process_data($data=null, $files=null) {

		$data = $this->validate_form($data, $files);

		if ( !is_array($data) )
			return false;


		if ( !is_dir($this->prefs->tmp_dir) )
			mkdir( $this->prefs->tmp_dir );

		$this->prefs->chrom_sizes = $data['chrom_sizes'];
		$this->prefs->chrom_sizes_bed = $data['chrom_sizes_bed'];

		/*
		If the submitted files are ChIP peaks, we need to make all peak names identical
		For genomic regions, just sort the bed files
		*/
		$this->prefs->unified_bed = true; //this is to make the cron script run correctly with mouseCAT and drosoCAT

		/* Increase the sensitivity in gat scoring for chip-ship analyses? */
		$this->prefs->increase_sensitivity = ( $data['increase_sensitivity'] == 'on' ? true : false );
		if ( $data['analysisType'] == 'genechip' ) // we don't increase sensitivity for chip vs gene profiles
			$this->prefs->increase_sensitivity = false;

		/*
		How are reporting self vs self results?
		*/
		$this->prefs->selfReport = ( $data['selfReport'] ? $data['selfReport'] : 'NA' );


		$this->prefs->userBed = $data['analysisType'];
		$this->prefs->analysisType = $data['analysisType'];

		/*
		prepare the BED files that we will use and setup the $this->files array
		*/
		$this->prepare_files( $data['chipfiles'], $data['genefile'] );

		if ( is_array($this->error) && count($this->error) > 0 ) {

			$errors = "<h4>The following fatal errors occurred with the submitted BED files</h4>";
			//problems with user submitted files
			foreach ( $this->error as $error ) {

				$errors .= "<strong><pre class=\"alert alert-danger\">{$error}</pre></strong>\n";

			}

			$this->session->errors = $errors;

			//clean up tmp dir
			`rm -Rf {$this->prefs->tmp_dir}`;
			return false;

		}


		/**
		 set up the results array
		**/
		//$file_keys = array_keys( $this->files );
		$this->results = array();
		//sort the array by key index
		//ksort($this->files);

		if ( $this->prefs->analysisType == 'chipchip' ) {

			/*
				setup the all-vs-all 2-dimensional results array with NAs
				we will then replace them as we go through the collections

					A	B	C ...
				A	NA	NA	NA...
				B	NA	NA	NA...
				C	NA	NA	NA...
			*/

			foreach( $this->files->chipfiles as $key=>$array) {

				$this->results[$key] = array();

				foreach( $this->files->chipfiles as $key2=>$array2) {
					$this->results[$key][$key2] = 'NA';
				}
			}


/*
			foreach( $this->files as $key=>$array) {

				ksort($this->files[$key]);

				if ( !isset( $array['collection_id'] ) ) { //user-submitted file
					continue;
				}


			}
*/

		} elseif ( $this->prefs->analysisType == 'genechip' ) {
			/**
				a gene bed files is different as we don't care about pre-computed GAT data

						A	B	C ...
				Gene1	NA	NA	NA
				Gene2	NA	NA	NA
				Gene3	NA	NA	NA
			*/

			foreach ( $this->genes as $k=> $gene_name ) {

				$this->results[$k] = array();

				foreach( $this->files->chipfiles as $key2=>$array2) {
					$this->results[$k][$key2] = 'NA';
				}


			}

			$this->prefs->genes = $this->genes;
		}

		if (isset($files))
			unset($files);
		$files['genefile'] = $this->files->genefile;
		$files['chipfiles'] = $this->files->chipfiles;

		/**
		Write data to cron_vars file
		*/
		$cron_vars = '<?php
/**
Automatically generated file - do not edit
*/
$prefs = ' . var_export( (array)$this->prefs, true ) . ';
$files = ' . var_export( $files, true ) . ';
$results = ' . var_export( $this->results, true ) . ';
?>';
		file_put_contents( $this->prefs->tmp_dir . "/cron_vars.php" , $cron_vars);

		/**
		Put the job in the cron queue
		*/
		file_put_contents( $this->cron_dir . "/".CAT_TYPE_LC."_queue", $this->uniq_id . "\n", FILE_APPEND | LOCK_EX);

		/**
		find out where we are in the queue
		*/
		$queue_array = file( $this->cron_dir . "/".CAT_TYPE_LC."_queue" );
		if ( !is_array($queue_array) )
			$pos = 0;
		else
			$pos = array_search( $this->uniq_id, $queue_array );

		if ( $pos > 0 ) {
			$msg = "There are {$pos} jobs waiting to be run prior to yours";
		} else {
			$msg = "Your job will start momentarily";
		}

		mkdir( SYSTEM_DOCUMENT_ROOT . "/downloads/" . $this->uniq_id );

		$template = file_get_contents( dirname(__FILE__) . "/results_index.tpl.php" );
		$template = str_ireplace("##CAT_TYPE##", CAT_TYPE_LC, $template);

		file_put_contents(SYSTEM_DOCUMENT_ROOT . "/downloads/" . $this->uniq_id . "/index.php", $template);

		$success = "<h2>Your BED file(s) have been successfully processed</h2>";
		$success .= "<h4>".CAT_TYPE." will run on your files shortly. You can check on the status of your job <a href=\"/downloads/{$this->uniq_id}/\" target=\"_blank\">HERE</a>.</h4>";
		if ( $data['emailAddress'] ) {
			$success .= "<h4>Once completed, you will be informed by email at ".$data['emailAddress']."</h4>";
			$this->mail_receipt($data['emailAddress']);
		}
		$this->session->success = $success;

		return true;

	}

	private function validate_form($data=null, $files=null) {

//print_r($files);

		$errors = array();

		if ( !$data || !is_array($data) || count($data) < 1 )
			$errors[] = "No Data";

		if ( !$data['title']  )
			$this->prefs->title = "Untitled ".CAT_TYPE." run";
		else {
			$this->prefs->title = preg_replace("|[^a-zA-Z0-9 \-_]|", "", $data['title']);
			$this->prefs->supplied_title = 1; //we will use the user-supplied title as tar filename
		}

		if ( !$data['genome'] )
			$errors[] = "No genome selected.";
		else {
			$data['chrom_sizes'] = SYSTEM_DATA_ROOT.'/chromSizes/'.$data['genome'].'.chrom.sizes';
			$data['chrom_sizes_bed'] = SYSTEM_DATA_ROOT.'/chromSizes_BED/'.$data['genome'].'.bed';

			if ( !is_file( $data['chrom_sizes'] ) || !is_readable($data['chrom_sizes']) ) {
				unset($data['chrom_sizes']);
				$errors[] = "The available genome \"".$data['genome']."\" is not available.";
			} else {
				if ( !is_file( $data['chrom_sizes_bed']) ) {
					create_chromSizes_bed($data['genome']);
					if ( !is_file( $data['chrom_sizes_bed']) ) {
						$errors[] = "The available genome \"".$data['genome']."\" is not available.";
					}
				}
			}
		}
		/*
		else { //minimum of 2 factors required unless submitting a BED file
			$numFactors = count($data['factors']);
			if ( !$data['userBedSubmit'] && $numFactors < 2 )
				$errors[] = "At least 2 Factors need to be selected for comparison.";
		}
		*/

		/** requirements check when submitting a BED file **/

		if ( !$data['emailAddress']  )
			$errors[] = "Email Address Required.";
		else
			$this->prefs->email = $data['emailAddress'];

		if ( isset($data['userBedSubmit']) && !is_null($data['userBedSubmit']) ) {

			if ( !$data['analysisType'] )
				$errors[] = "No 'Type of Analysis' selected.";
	/*
	print_r($_FILES);
	exit;
	*/
	//print_r($files);

			if ( $data['analysisType'] == 'genechip' ) {

				$data['genefile'] = $files['GeneBedFile'];

				if ( !$data['genefile']['name'] )
					$errors[] = "No Genomic Region BED file provided.";
				elseif ( $data['genefile']['error'] != 0 )
					$errors[] = "Errors occurred in uploading the Genomic Region BED file.";

			} else {
				$data['genefile'] = null;
			}

/***
	create a function to process the uploaded chip files
***/

			if ( !is_array($files['ChipBedFiles']) && !isset($data['tissues']) ) { //webCAT requires ChIP BED file input from the user. drosoCAT & mouseCAT do not and can be identified by the presence of the _POST['tissues'] array
//not sure  if this will ever equate as the ChIPBedFiles array is always set , even if empty
				$errors[] = "ChIP BED files are required, but none were provided.";

			} else {

				$data['chipfiles'] = $files['ChipBedFiles'];

				$data['chipfiles']['id']['file'] = $data['ChipBedFiles']['file_id'];
				unset($data['ChipBedFiles']);

				foreach ($data['chipfiles']['name']['file'] as $tmp) {
					if ($tmp)
						$total_num_files++;
				}
				if ( $total_num_files < MIN_CHIP_BEDFILES )
					$errors[] = "At least ".MIN_CHIP_BEDFILES." ChIP BED files are required ({$total_num_files} were uploaded).";
				else {

					//check for upload errors
					foreach ($data['chipfiles']['error']['file'] as $k=>$v) {
						if ( $v != 0 && $data['chipfiles']['name']['file'][$k] ) {
							$errors[] = "Errors occurred in uploading ".$data['chipfiles']['name']['file'][$k];
							continue;
						}
						if ( !$data['chipfiles']['name']['file'][$k] ) { //reduce the size of the array by removing indices with no file attribute
							unset($data['chipfiles']['name']['file'][$k]);
							unset($data['chipfiles']['type']['file'][$k]);
							unset($data['chipfiles']['tmp_name']['file'][$k]);
							unset($data['chipfiles']['error']['file'][$k]);
							unset($data['chipfiles']['size']['file'][$k]);
							unset($data['chipfiles']['id']['file'][$k]);

						}
					}

				}
			}
		}

		if ( count($errors) > 0 ) {
			$output = "<h4>Could not continue - the following fatal errors occurred:</h4>\n";
			foreach ($errors as $error) {
				$output .=  "<strong><pre class='alert alert-danger'>{$error}</pre></strong>\n";
			}
			$this->session->errors = $output;

			//clean up tmp dir
			`rm -Rf {$this->prefs->tmp_dir}`;
			return false;
		}

		return $data;
	}

	private function prepare_files( $chipfiles, $genefile=null ) {

		$this->files->genefile = $genefile;
		$this->files->chipfiles = $chipfiles;

		if ( !is_null($this->files->genefile) ) {

			$tmpfile = $this->files->genefile['tmp_name'];
			$sorted_file = $this->prefs->tmp_dir . "/" . $this->files->genefile['name'] . ".sorted.bed";

			if ( $this->copy_and_sort($tmpfile, $sorted_file, $this->files->genefile['name']) != false ) {

				$fname = $this->files->genefile['name'];
				/**
				check number of unique BED id's (ie gene names) of individual genomic region BED file
				*/
				$genes = array();
				exec( "awk '{print \$4}' '{$sorted_file}' | sort | uniq ", $genes, $retvar );

				$out = count($genes);
				if ( !is_numeric($out) || $out < 1 ) {
					$this->error[] = "The supplied genomic regions BED file has no regions.";
				}
				if ( $out > GENE_BEDFILE_MAX_REGIONS ) {
					$this->error[] = "The supplied genomic regions BED file has {$out} regions, which is greater than the maximum allowed (".GENE_BEDFILE_MAX_REGIONS.").";
				}

				exec( "cd {$this->prefs->tmp_dir} && cp '{$fname}.sorted.bed' '{$fname}.valid.bed' ", $out, $retvar );
				if ( $retvar > 0 ) {
					$this->error[] = "An error occurred copying the genomic regions BED file.";
					continue;
				}
				foreach ($genes as $key=>$gname) {
					$genes[$key] = trim($gname);
				}
				$this->genes = $genes;

			} else {
				return false;
			}
			if (is_array($genes)) {

				//$genefile contains orig values
				$gene_file['filename'] = "{$fname}.valid.bed";
				$gene_file['filepath'] = "{$this->prefs->tmp_dir}/{$fname}.valid.bed";
				$gene_file['number_regions'] = count($genes);

				$this->files->genefile = $gene_file;

			} else {
				$this->files->genefile = null;
			}
		}

		//$files_key = max( array_keys( $this->collections ) );
		$files_key=0;
		$chip_file_ids = array(); //we need unique file IDs
		foreach ( $this->files->chipfiles['name']['file'] as $k => $fname ) {

			$orig_fname = $this->files->chipfiles['name']['file'][$k];
			$tmpfile = $this->files->chipfiles['tmp_name']['file'][$k];
			$sorted_file = $this->prefs->tmp_dir . "/" .  $this->files->chipfiles['name']['file'][$k] . ".sorted.bed";

/*
			if ( !is_file($tmp_loc) )
				continue;
*/

			$this->files->chipfiles['id']['file'][$k] = preg_replace("/[^A-Za-z0-9 \-\(\)]/", '_', $this->files->chipfiles['id']['file'][$k]);
			//$this->files[$files_key]['name'] = $files_arr['name']['file'][$k];

			if ( $this->copy_and_sort($tmpfile, $sorted_file, $orig_fname) != false ) {

				// ChIP BED files need to be unified so the peak names (4th column) are all the same

				//$this->files[$files_key]['id'] = preg_replace("/[^A-Za-z0-9 \-\(\)]/", '_', $files_arr['id']['file'][$k]);
				$this->files->chipfiles['id']['file'][$k] = preg_replace( "/[^A-Za-z0-9\-\(\)]/", '_', $this->files->chipfiles['id']['file'][$k] );

				$fname = $this->files->chipfiles['name']['file'][$k];
				$fid = $this->files->chipfiles['id']['file'][$k];

				if (!$fid || in_array($fid, $chip_file_ids)) {
					$new_fid = "ChIP_".($files_key+1);

					if ( in_array($fid, $chip_file_ids) )
						$fid = $fid."_".$new_fid;
					else
						$fid = $new_fid;
					//$chip_file_ids[] = $new_fid;

				}
				$chip_file_ids[] = $fid;

				$command = "awk '{print $1\"\t\"$2\"\t\"$3\"\t{$fid}_Peak\"}' '{$this->prefs->tmp_dir}/{$fname}.sorted.bed' > '{$this->prefs->tmp_dir}/{$fname}.valid.bed'";

				exec( "{$command} ", $out, $retvar );

				if ( $retvar > 0 ) {
					$this->error[] = "Error unifying file {$fname}";
					continue;
				}



/*
				$this->files[$files_key]['filename'] = "{$fname}.valid.bed";
				$this->files[$files_key]['filepath'] = "{$this->prefs->tmp_dir}/{$fname}.valid.bed";
*/

			} else {
				return false;
			}

			$chipfile_array[$files_key]['id'] = $fid;
			$chipfile_array[$files_key]['filename'] = "{$fname}.valid.bed";
			$chipfile_array[$files_key]['filepath'] = "{$this->prefs->tmp_dir}/{$fname}.valid.bed";

			$files_key++;

		}

		$this->files->chipfiles = $chipfile_array;
		return true;

	}

/*
	private function normalize($s) {
		// Normalize line endings using Global
		// Convert all line-endings to UNIX format
		$s = str_replace(CRLF, LF, $s);
		$s = str_replace(CR, LF, $s);
		// Don't allow out-of-control blank lines
		$s = preg_replace("/\n{2,}/", LF . LF, $s);
		return $s;
	}
*/
	private function copy_and_sort( $oldfile, $newfile, $fname=false ) {

		if ( preg_match("/\.sorted\.bed$/", $newfile) )
			$copied_file = preg_replace("/\.sorted\.bed$/", "", $newfile);
		else
			$copied_file = $newfile;

		if ( $fname === false )
			$fname = $copied_file;

		if ( !is_file($oldfile) || !is_readable($oldfile) ) {
			$this->error[] = "No such file: $oldfile";
			return false;
		}

		$file_list = file($oldfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		//get chromomosome sizes
		$chrom_sizes_bed = file($this->prefs->chrom_sizes_bed, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($chrom_sizes_bed as $line) {
			list($chr, $start, $end) = explode("\t", $line);
			$chrom_sizes[$chr]['start'] = $start;
			$chrom_sizes[$chr]['end'] = $end;
		}

		//validate the given BED file
		$i = 0;

		foreach($file_list as $ln => $line) {
			$i++;
			list($chr, $start, $end, $rest) = explode("\t", $line);
			if ( preg_match("/^#/", $line) ) { //remove comment from BED file
				unset($file_list[$ln]);
				continue;
			}

			$num_cols_array = explode("\t", $line);
			if (is_array($num_cols_array))
				$num_cols = count($num_cols_array);
			else
				$num_cols = 0;

			if ( $num_cols < 4 ) {
				$this->error[] = "Line {$i} (excluding empty lines) of the BED file:<br>&nbsp;&nbsp;&nbsp;&nbsp;<i>".basename($fname)."</i><br>is not in valid BED format:<br />&nbsp;&nbsp;&nbsp;&nbsp;Number of columns encountered = {$num_cols}";
				return false;
			}
			if ( !isset($prev_row_numcols) )
				$prev_row_numcols = $num_cols;

			if ( $prev_row_numcols != $num_cols ) {
				$this->error[] = "Lines {$i} and ".($i-1)." (excluding empty lines) of the BED file:<br>&nbsp;&nbsp;&nbsp;&nbsp;<i>".basename($fname)."</i><br>contain differing column numbers (invalid BED format): <br />&nbsp;&nbsp;&nbsp;&nbsp;Line ".($i-1)." has {$prev_row_numcols} columns.<br />&nbsp;&nbsp;&nbsp;&nbsp;Line {$i} has {$num_cols} columns.";
				return false;
			}

			if ( !isset($chrom_sizes[$chr] ) ) { //line is invalid
				$this->error[] = "Line {$i} (excluding empty lines) of the BED file:<br>&nbsp;&nbsp;&nbsp;&nbsp;<i>".basename($fname)."</i><br>contains an invalid chromosome identifier:<br />&nbsp;&nbsp;&nbsp;&nbsp;'{$chr}' is not a valid chromosome";
				return false;
			}
			if ( !is_numeric($start) ) {
				$this->error[] = "The start coordinate (column 2) on line {$i} (excluding empty lines) of the BED file:<br>&nbsp;&nbsp;&nbsp;&nbsp;<i>".basename($fname)."</i><br>is not numeric.<br />&nbsp;&nbsp;&nbsp;&nbsp;Start coordinate encountered was: {$start}";
				return false;

			}
			if ( !is_numeric($end) ) {
				$this->error[] = "The end coordinate (column 3) on line {$i} (excluding empty lines) of the BED file:<br>&nbsp;&nbsp;&nbsp;&nbsp;<i>".basename($fname)."</i><br>is not numeric.<br />&nbsp;&nbsp;&nbsp;&nbsp;End coordinate encountered was: {$end}";
				return false;

			}

			if ( $start < $chrom_sizes[$chr]['start'] ) { //less than zero
				$this->error[] = "Line {$i} (excluding empty lines) of the BED file:<br>&nbsp;&nbsp;&nbsp;&nbsp;<i>".basename($fname)."</i><br>contains a coordinate less than ".$chrom_sizes[$chr]['start']." for chromosome {$chr}.<br />&nbsp;&nbsp;&nbsp;&nbsp;Start coordinate encountered was: {$start}";
				return false;
			}
			if ( $end > $chrom_sizes[$chr]['end'] ) { //less than zero
				$this->error[] = "Line {$i} (excluding empty lines) of the BED file:<br>&nbsp;&nbsp;&nbsp;&nbsp;<i>".basename($fname)."</i><br>contains a coordinate greater than ".$chrom_sizes[$chr]['end']." for chromosome {$chr}.<br />&nbsp;&nbsp;&nbsp;&nbsp;End coordinate encountered was: {$end}";
				return false;
			}

		}



		if (!is_array($file_list) || count($file_list) < 1) {
			$this->error[] = "The BED file ".basename($oldfile)." contains no coordinates";
			return false;
		}


		$list = implode("\n", $file_list); //normalises line feeds  //file_get_contents( $oldfile );

		file_put_contents($copied_file, $list);

		//use awk to convert MacOS line feeds to univ line feeds
		$cmd = "awk '{ gsub(\"\\r\", \"\\n\"); print $0;}' \"{$copied_file}\" | " . BEDTOOLS . " sort -i - > \"{$copied_file}.sorted.bed\" 2>&1 ";
		exec( "{$cmd}", $output, $retvar );

		if ( $retvar > 0 ) {
			$this->error[] = "Sorting BED file gave a fatal error";
			return false;
		}

		return "{$copied_file}.sorted.bed";

	}


	private function mail_receipt($email) {

		$msg  = "This is the auto-mailer from " . SYSTEM_PAGE_TITLE . "\n".
"Your BED file(s) have been validated and pre-processed.\n\n".

CAT_TYPE." will run on your files shortly. You can check on the status of your job at :\n".
"https://".DOMAIN_NAME."/downloads/{$this->uniq_id}/\n\n".

"Once completed, you will be informed by email at this address.";

		$m = new MAIL;
		$m->From(MAIL_FROM, "BioTools.fr ".CAT_TYPE." Mailer");
		$m->AddTo($email);
		if ( isset($this->prefs->supplied_title) )
			$m->Subject(CAT_TYPE.' will shortly execute on your run "'.$this->prefs->title.'"');
		else
			$m->Subject(CAT_TYPE.' will run shortly on your BED file(s)');
		$m->Name($_SERVER['HTTP_HOST']);

		$m->Text = array(
			'content'  => $msg, // required
			'charset'  => 'utf-8', // optional
			'encoding' => 'quoted-printable' // optional
		);
		//print_r($m->Result);
		//print_r($m->History);
		return $m->Send('sendmail');

	}



	public function __destruct()
	{
		parent::__destruct();
	}

}

?>