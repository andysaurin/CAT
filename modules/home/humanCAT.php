<?php

class humanCAT extends NQ_Auth_No
{
	public $class_title = "humanCAT";
	public $module_title = "CAT";

	public function __construct()
	{
		parent::__construct();

		$tissue_offline = array(); //Add tissue names to take them offline (essential when we are importing new data for this tissue)
		$this->set("tissue_offline", $tissue_offline);

		define("CAT_TYPE", "humanCAT");
		define("CAT_TYPE_LC", strtolower(CAT_TYPE));

		define("SPECIES_ID", 3);
		define("GENOME", 'hg19');

		define("MAX_BED_FILES", 20);

		$this->prefs = new stdClass();
		$this->prefs->chrom_sizes_bed = SYSTEM_DATA_ROOT . "/chromSizes_BED/".GENOME.".bed";
		$this->files = new stdClass();

		$this->uniq_id = uniqid( md5(microtime()) ); //a unique ID for this run
		$this->prefs->tmp_dir = SYSTEM_TMPDIR . "/" . $this->uniq_id; //where we will store data

		$this->cron_dir = SYSTEM_DATA_ROOT . "/cron";

		if ( isset($_SESSION['errors']) )
			unset($_SESSION['errors']);
		if ( isset($_SESSION['success']) )
			unset($_SESSION['success']);

		/***
		Factors
		***/
		//$results = $this->db->get_results("SELECT cf.* FROM `collection_factors` cf ORDER BY cf.factor_group ASC, cf.`factor_name` ASC");
		$results = $this->db->get_results("SELECT cf.* FROM `collection_factors` cf, `collections` c WHERE cf.`id`=c.`factor_id` AND c.`species_id` = ".SPECIES_ID." AND c.`exclude` != 1 ORDER BY cf.factor_group ASC, cf.`factor_name` ASC");
		$all_factors = array();
		foreach($results as $result) {
			$all_factors[$result->id]['id'] = $result->id;
			$all_factors[$result->id]['name'] = $result->factor_name;
			$all_factors[$result->id]['fullname'] = $result->factor_fullname;
			$all_factors[$result->id]['group'] = $result->factor_group;

		}
		$this->set('all_factors', $all_factors);

		/***
		Factor groups
		***/
		$results = $this->db->get_results("SELECT DISTINCT(cfg.id) AS uid, cfg.* FROM `collection_factor_groups` cfg, `collection_factors` cf, `collections` c WHERE cfg.abbreviation=cf.factor_group AND cf.id=c.factor_id AND c.species_id=".SPECIES_ID." AND c.`exclude` != 1 ORDER BY `display_order` ASC");
		$factor_groups = array();
		foreach($results as $result) {
			$factor_groups[$result->abbreviation]['name'] = $result->name;
			$factor_groups[$result->abbreviation]['factors'] = array();
			$factor_groups[$result->abbreviation]['total_counts'] = 0; //this will be modified by the ajax request
		}
		//add proteins to the group
		foreach($all_factors as $k=> $factor_arr) {
			$group = $factor_arr['group'];
			if ( is_array($factor_groups[$group]) )
				foreach ($factor_arr as $array2) {
					$factor_id = $factor_arr['id'];
					$factor_groups[$group]['factors'][$factor_id] = $factor_arr;
				}
				/* $factor_groups[$group]['factors'][] = $factor_arr; */

		}
		$this->factor_groups = $factor_groups;
		$this->set('factor_groups', $factor_groups);

		if ( !defined("MAX_FILE_SIZE") || MAX_FILE_SIZE == 0)
			$this->set('max_filesize', 99999999999999);
		else
			$this->set('max_filesize', MAX_FILE_SIZE);

		if ( !defined("GENE_BEDFILE_MAX_REGIONS") )
			$this->set('max_gene_regions', 500);
		else
			$this->set('max_gene_regions', GENE_BEDFILE_MAX_REGIONS);

		if ( !defined("MAX_BED_FILES") )
			$this->set('max_bed_files', 8);
		else
			$this->set('max_bed_files', MAX_BED_FILES);

        $load = @file_get_contents('/proc/loadavg');
        $load = explode(' ', $load, 1);
        $load = $load[0];

        $max_capacity = round( ($load / SYSTEM_CORES) * 100);
        if ( $max_capacity >= 100 ) {
   	        $this->session->load_alert = "<h2>ALERT</h2>All server processors are currently at {$max_capacity}% capacity.<br />If you submit a BED file, your job will not execute until the capacity drops below 100%.";
	        $this->session->load_warnings = false;
		}elseif ( $load > LOAD_ALERT ) {
	        $this->session->load_warnings = "<h3>WARNING</h3>The server processors are currently at {$max_capacity}% capacity.<br />This means that runs involving user-submitted BED files will currently take longer than normal to complete.";
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

		/***
		ENCODE tissues
		***/
		$results = $this->db->get_results("SELECT * FROM `collection_tissues` ct WHERE ct.`species_id`=".SPECIES_ID." ORDER BY ct.`cell_line` DESC, min_age ASC,  max_age ASC, ct.`tissue_name` ASC");
		$abbreviation=0;
		foreach ($results as $result) {

			$tissue_id = $result->id;
			if ( $result->cell_line == 1 ) {
				$abbreviation = 'Cell Lines';
			} elseif ($result->primary_cells == 1) {
				$abbreviation = 'Primary Cells';
			} else {
				$abbreviation = "Tissues"; //$abbreviation = $result->abbreviation;
			}
			//$tissue_abbreviations[$abbreviation]['name'] = $result->tissue_name;
			if (!$tissue_abbreviations[$abbreviation]['abbreviation_name']) {
				$tissue_abbreviations[$abbreviation] = array( 'abbreviation_name'=>$abbreviation,  'tissues'=>array(), 'num_datassets'=>0);
			}

			$tissue_abbreviations[$abbreviation]['tissues'][$tissue_id] = $result;

			//number of datasets with precomputed GAT data stored
			$collections = $this->db->get_results("SELECT c.id FROM `collections` c, `gat_data` gd WHERE c.`species_id`=".SPECIES_ID." AND gd.collection_id_segments=c.id AND c.tissue_id={$tissue_id} AND c.`exclude` != 1 GROUP BY c.id");

			if ( !is_array($collections) ) {

				unset($tissue_abbreviations[$abbreviation]['tissues'][$tissue_id]);

			} else {

				$tissue_abbreviations[$abbreviation]['num_datasets'] = ( $tissue_abbreviations[$abbreviation]['num_datasets'] + count($collections) );
				$tissue_abbreviations[$abbreviation]['tissues'][$tissue_id]->num_datasets = count($collections);
			}

		}

		foreach($tissue_abbreviations as $k=>$v) {
			if ( $v['num_datasets'] < 1 ) {
				unset($tissue_abbreviations[$v]);
			}
		}
		$this->set('tissue_abbreviations', $tissue_abbreviations);

	}

	private function get_collections($data) {

		$this->collections = array();

		//Get all factors for the selected tissues
		foreach ( $data['tissues'] as $tissue_id => $v ) {
			$tissue_id = (int)$tissue_id;
			$query = "SELECT c.`id` AS 'collection_id', c.`factor_id` , c.`source` , c.`source_id` , c.`source_name`, c.`peculiarity`, t.`tissue_name` , t.`abbreviation` AS 'tissue_abbreviation' , f.`factor_name` , f.`factor_fullname` , cf.`filename`
						FROM `collections` c, `collection_tissues` t, `collection_factors` f , `collection_files` cf
						WHERE c.`tissue_id` ={$tissue_id} AND c.`tissue_id`=t.`id` AND c.`factor_id`=f.`id` AND c.`id`=cf.`collection_id` AND c.`exclude` != 1 ORDER BY c.`id` ASC";

			$results = $this->db->get_results($query);
			if (!is_array($results))
				continue;

			foreach( $results as $result) {
				if ( in_array($result->source_id, $data['omit_factors'])  ) { //we're omitting this factor ID
					continue;
				}
				if ( isset( $data['factors'][$result->factor_id] ) && !isset($this->collections[$result->collection_id]) ) {
					$this->collections[$result->collection_id] = $result;
					if ($result->peculiarity != '')
						$this->collections[$result->collection_id]->id = "{$result->factor_name} {$result->tissue_name} '{$result->peculiarity}' ({$result->source_id})";
					else
						$this->collections[$result->collection_id]->id = "{$result->factor_name} {$result->tissue_name} ({$result->source_id})";

					//the path to the file=
					$path = SYSTEM_DATA_ROOT . DS . "encode_bedfiles" . DS . "human" . DS . $result->source_id;


					//the BED file - only set if file is present (if not present, score will be automatically NA)
					if ( is_file( $path . DS . $result->filename ) )
						$this->collections[$result->collection_id]->filepath = $path . DS . $result->filename;

					if ( $data['omitHOT'] ) {
						//the BED file with HOT regions removed - only set if file is present (if not present, score will be automatically NA if requesting HOT region removal)
						$noHOT_bedfile = str_ireplace(".unified.bed", ".unified.noHOT.bed", $result->filename);
						if ( is_file( $path . DS . $noHOT_bedfile ) ) {
							$this->collections[$result->collection_id]->filepath_noHOT = $path . DS . $noHOT_bedfile;
							$this->collections[$result->collection_id]->filename_noHOT = $noHOT_bedfile;
						}
					}
				}

			}

		}
		if ( count($this->collections) < 1 ) {

			die("No Data");
		}

	}

	private function add_encode_files() {

		if ( count($this->files->chipfiles) > 0 )
			$files_key = count($this->files->chipfiles);
		else
			$files_key = 0;

		//add the ENCODE bed files to the files list
		foreach($this->collections as $k=>$array) {

			$this->files->chipfiles[$files_key]['id'] = $array->id;
			if ( $this->prefs->nohot === true && isset($array->filename_noHOT) ) {
				$this->files->chipfiles[$files_key]['filename'] = $array->filename_noHOT;
				$this->files->chipfiles[$files_key]['filepath'] = $array->filepath_noHOT;
			} else {
				$this->files->chipfiles[$files_key]['filename'] = $array->filename;
				$this->files->chipfiles[$files_key]['filepath'] = $array->filepath;
			}
			$this->files->chipfiles[$files_key]['collection_id'] = $k;
			$files_key++;
		}

	}

	private function process_data($data=null, $files=null) {

$func_start = time();

		$data = $this->validate_form($data, $files);
		if ( !is_array($data) )
			return false;

		//populate the $this->collections array
$start = time();
		$this->get_collections($data);
$end = time();
$time['get_collections'] = ($end-$start);

		if ( !is_dir($this->prefs->tmp_dir) )
			mkdir( $this->prefs->tmp_dir );

		/*
		If the submitted files are ChIP peaks, we need to make all peak names identical
		For genomic regions, just sort the bed files
		*/
		$this->prefs->unified_bed = ( $data['analysisType'] == 'chipchip' ? true : false );

		/* Increase the sensitivity in gat scoring for chip-ship analyses? */
		$this->prefs->increase_sensitivity = ( $data['increase_sensitivity'] == 'on' ? true : false );
		if ( $data['analysisType'] == 'genechip' ) // we don't increase sensitivity for chip vs gene profiles
			$this->prefs->increase_sensitivity = false;


		/*
		Do we want to exclude HOT regions?
		*/
		$this->prefs->nohot = ( $data['omitHOT'] ? true : false );

		if ( $this->prefs->nohot === true )
			$this->prefs->gat_db = 'gat_data_noHOT';
		else
			$this->prefs->gat_db = 'gat_data';

		/*
		How are reporting self vs self results?
		*/
		$this->prefs->selfReport = ( $data['selfReport'] ? $data['selfReport'] : 'NA' );

		if ( $data['userBedSubmit'] ) {

			$this->prefs->userBedSubmit = true;

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

		} else {
			$this->files->genefile = null;
			$this->files->chipfiles = array();
			$this->prefs->userBedSubmit = false;
		}

		//add selected ENCODE datasets to the $this->files->chipfiles array
		$this->add_encode_files();


		/**
		 set up the results array
		**/
		$this->results = array();

		if ( $this->prefs->userBed == 'chipchip' || $this->prefs->unified_bed === true || $this->prefs->userBedSubmit === false) {

$results_start=time();

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

			//set up the collection_id -> chipfiles['key'] mapping
			foreach( $this->files->chipfiles as $key=>$array) {
				if ( isset( $array['collection_id'] ) && $array['collection_id'] > 0 ) {
					$collection_id = $array['collection_id'];
					$collection2key[$collection_id] = $key;
				}
			}

			foreach( $this->files->chipfiles as $key => $array) {

				if ( !isset( $array['collection_id'] ) ) { //a user-submitted file
					continue;
				}

				/* obtain pre-computed GAT data results */
				/* This code will not execute for webCAT because all files in webCAT are user-submitted */
				$query = "SELECT `collection_id_annotations`, `observed`, `expected`, `stddev`, `l2fold`,`pvalue`, `percent_overlap_size_track`, `percent_overlap_size_annotation` FROM `{$this->prefs->gat_db}` FORCE INDEX (collection_id_segments) WHERE `collection_id_segments`=".$array['collection_id']." ORDER BY `collection_id_segments`";
$start = time();
				$results = $this->db->get_results($query);
$end = time();
$time['mysql'] = ($end-$start);
//die( "mysql took ".($end-$start)."secs" );

				if ( is_array($results) && count($results) ) {
$start = time();
					foreach( $results as $result ) {

						$collection_id = $result->collection_id_annotations;

						if ( isset($this->collections[$collection_id]) ) {

							$collection_key = $collection2key[$collection_id];

							if ( !is_numeric($result->pvalue) ) {
								$result->pvalue = 1;
							}
							$score = gat_score($result->l2fold, $result->pvalue, $result->percent_overlap_size_track, $result->percent_overlap_size_annotation, $this->prefs->increase_sensitivity);
							$this->results[$key][$collection_key] = $score;
							$this->results[$collection_key][$key] = $score; //reciprocal is same

							if ( $key == $collection_key && $this->prefs->selfReport != 'actual' ) { //we are not interested in self vs self score
								$this->results[$key][$collection_key] = $this->prefs->selfReport;
								$this->results[$collection_key][$key] = $this->prefs->selfReport;
							}

						}
					}
$end = time();
$time['array'] = ($end-$start);

				}

			}


$results_end=time();
$time['results'] = ($results_end-$results_start);

		}elseif ( $this->prefs->userBed == 'genechip') {
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


		if ( isset($this->prefs->userBed) && $this->prefs->userBedSubmit === true ) {

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

		/**
			We haven't uploaded any files, so just generate the heatmap from pre-calculated scores
		**/

		/**
		 the TSV results data matrix
		 **/
$start=time();
		$matrix = data_matrix((array)$this->results, (array)$this->files) or die("No Call");
$end=time();
$time['matrix'] = ($end-$start);

		/**
		write scoring matrix to disk
		**/
$start=time();
		$this->tmp_results_dir = $this->prefs->tmp_dir . "/results";
		mkdir($this->tmp_results_dir);
		$fh = fopen($this->tmp_results_dir . "/".CAT_TYPE."_scores.xls", "w+") or die("Execution error - could not write score matrix to disk.");
		fwrite($fh, $matrix);
		fclose($fh);
$end=time();
$time['writing'] = ($end-$start);

		/**
		 create the heatmap
		 **/
$start=time();
		//copy( dirname( __FILE__ ) . "/heatmap.R", $this->tmp_results_dir . "/heatmap.R");
		if ( !create_heatmap($this->tmp_results_dir, $this->prefs->title) )
			$no_pdf = true;
		else
			copy($this->tmp_results_dir . "/".CAT_TYPE."_heatmap.pdf", $this->prefs->tmp_dir . "/".CAT_TYPE."_heatmap.pdf");

		$output_dir = SYSTEM_DOCUMENT_ROOT . "/downloads/" . $this->uniq_id;
		rename($this->prefs->tmp_dir, $output_dir);

		if ( isset($this->prefs->supplied_title) )
			$this->results_dir = create_zip( $output_dir, $this->prefs->title );
		else
			$this->results_dir = create_zip( $output_dir );
$end=time();
$time['zip_create'] = ($end-$start);

		$success = "<h2>".CAT_TYPE." was successfully run</h2>";
		if ( isset($no_pdf) ) {
			$success .= "<h4>You can download the results using <a href=\"/downloads/{$this->uniq_id}/".CAT_TYPE."_results.tar\" target=\"_blank\">this link</a>.</h4>";
		} else {
			$success .= "<h4>You can view the generated heatmap <a href=\"/downloads/{$this->uniq_id}/".CAT_TYPE."_heatmap.pdf\" target=\"_blank\">HERE</a></h4>";
			$success .= "<h4>and download the results using <a href=\"/downloads/{$this->uniq_id}/".CAT_TYPE."_results.tar\" target=\"_blank\">this link</a>.</h4>";
			$success .= "<h5>If you cannot open the .tar file on your PC, download and install the free open source software, <a href='http://www.7-zip.org' target='_blank'>7-Zip</a></h5>";
		}
		$success .= "<br />Results will be available for 48 h.";

		$this->session->success = $success;


		file_put_contents( $this->results_dir . "/index.php", "<html><body>{$success}</body></html>");

$time['function'] = (time() - $func_start);
//debug - print out what's taking so long

		return true;

	}

	private function validate_form($data=null, $files=null) {

		$errors = array();

		if ( !$data || !is_array($data) || count($data) < 1 )
			$errors[] = "No Data";

		if ( !is_array($data['tissues']) || !count($data['tissues']) )
			$errors[] = "No Tissues Selected.";

		if ( !is_array($data['factors']) || !count($data['factors']) || count($data['factors']) == 0 ) {
			$errors[] = "No Factors Selected.";

		}

		$data['chrom_sizes'] = SYSTEM_DATA_ROOT.'/chromSizes/'.GENOME.'.chrom.sizes';
		$data['chrom_sizes_bed'] = SYSTEM_DATA_ROOT.'/chromSizes_BED/'.GENOME.'.bed';

		if ( !is_file( $data['chrom_sizes'] ) || !is_readable($data['chrom_sizes']) ) {
			unset($data['chrom_sizes']);
			$errors[] = "The available genome \"".GENOME."\" is not available.";
		} else {
			if ( !is_file( $data['chrom_sizes_bed']) ) {
				create_chromSizes_bed(GENOME);
				if ( !is_file( $data['chrom_sizes_bed']) ) {
					$errors[] = "The available genome \"".GENOME."\" is not available.";
				}
			}
		}

		if ( preg_match("/[0-9\n\r]+/", $data['omit_factors']) ) {

			$data['omit_factors'] = preg_replace("/[\r]+/", "", $data['omit_factors']);
			$data['omit_factors'] = preg_replace("/[\n]+/", "", $data['omit_factors']);
			$data['omit_factors'] = preg_replace("/[\s]+/", "", $data['omit_factors']);
			$data['omit_factors'] = explode(",", $data['omit_factors']);

			if ( is_array($data['omit_factors']) && count($data['omit_factors']) ) {
				foreach ($data['omit_factors'] as $k=>$factor) {
					if ( !preg_match( "|[0-9]+|", $factor ) ) {
						unset($data['omit_factors'][$k]);
					}
				}
			}
		} else {
			$data['omit_factors'] = array();
		}

		if ( !$data['title']  )
			$this->prefs->title = "Untitled ".CAT_TYPE." run";
		else {
			$this->prefs->title = preg_replace("|[^a-zA-Z0-9 \-_]|", "", $data['title']);
			$this->prefs->supplied_title = 1; //we will use the user-supplied title as tar filename
		}
		/*
		else { //minimum of 2 factors required unless submitting a BED file
			$numFactors = count($data['factors']);
			if ( !$data['userBedSubmit'] && $numFactors < 2 )
				$errors[] = "At least 2 Factors need to be selected for comparison.";
		}
		*/

		/** requirements check when submitting a BED file **/
		if ( $data['userBedSubmit'] ) {

			if ( !$data['emailAddress']  )
				$errors[] = "Email Address Required.";
			else
				$this->prefs->email = $data['emailAddress'];

			if ( !$data['analysisType'] )
				$errors[] = "No BED file type selected.";

			if ( $data['analysisType'] == 'chipchip' ) {

				$data['chipfiles'] = $files['ChipBedFiles'];
				//$data['files'] = $_FILES['ChipBedFiles'];
				$data['chipfiles']['id']['file'] = $data['ChipBedFiles']['file_id'];

				unset($data['ChipBedFiles']);

				foreach ($data['chipfiles']['name']['file'] as $tmp) {
					if ($tmp)
						$total_num_files++;
				}

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

			if ( $data['analysisType'] == 'genechip' ) {

				$data['genefile'] = $files['GeneBedFile'];

				if ( !$data['genefile']['name'] )
					$errors[] = "No Genomic Region BED file provided.";
				elseif ( $data['genefile']['error'] != 0 )
					$errors[] = "Errors occurred in uploading the Genomic Region BED file.";

			} else {
				$data['genefile'] = null;
			}

			if ( $data['analysisType'] == 'chipchip' && $this->uploaded_file_number($data['chipfiles']) < 1 )
				$errors[] = "No ChIP BED files provided.";

			if ( $data['analysisType'] == 'genechip' &&  $data['genefile']['error'] != 0 )
				$errors[] = "No Genomic Region BED file provided.";

			if ( $data['analysisType'] == 'genechip' && $data['omitHOT'] ) {
				//removing HOT regions from gene bed files serves no purpose
				unset($data['omitHOT']);
			}

		} else {
			$data['chipfiles'] = array();
			$data['genefile'] = null;
		}

		if ( $data['omitHOT'] && ( !is_file(SYSTEM_DATA_ROOT . '/HOT_regions.hg19.bed') || !is_readable(SYSTEM_DATA_ROOT . '/HOT_regions.hg19.bed') ) )
			$errors[] = "Installation error: The file data/HOT_regions.hg19.bed is missing or is not readable.";

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

	private function uploaded_file_number($files=array()) { //count number of real uploaded files

		if ( !isset($files['name']['file']) || !is_array($files['name']['file']) )
			return 0;
		$num_files = 0;

		foreach ($files['name']['file'] as $k=>$val ) {
			if (!$val || $files['error']['file'][$k] != 0)
				continue;
			$num_files++;
		}

		return $num_files;

	}

	private function prepare_files( $chipfiles, $genefile=false) {

		$this->files->genefile = $genefile;
		$this->files->chipfiles = $chipfiles;

		if ( isset($this->files->genefile) && $this->files->genefile !== false ) {

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

		if (isset($this->files->chipfiles['name']['file']) && is_array($this->files->chipfiles['name']['file']) && count($this->files->chipfiles['name']['file']) > 0 ) {
			foreach ( $this->files->chipfiles['name']['file'] as $k => $fname ) {


				$orig_fname = $this->files->chipfiles['name']['file'][$k];
				$orig_fname = preg_replace("/[^a-zA-Z0-9\-_\.\/]/", "", $orig_fname);

				if ( !preg_match("/\.bed$/", $orig_fname) )
					$orig_fname = "{$orig_fname}.bed";

				$tmpfile = $this->files->chipfiles['tmp_name']['file'][$k];
				$sorted_file = $this->prefs->tmp_dir . "/" .  $orig_fname . ".sorted.bed";

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

					$fname = $orig_fname; //$this->files->chipfiles['name']['file'][$k];
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


					if ( $this->prefs->nohot === true ) {

						$hot_bedfile = SYSTEM_DATA_ROOT . "/HOT_regions.hg19.bed";
						$cmd = BEDTOOLS . " subtract -A -a \"{$this->prefs->tmp_dir}/{$fname}.valid.bed\" -b \"{$hot_bedfile}\" > \"{$this->prefs->tmp_dir}/{$fname}.nohot.bed\" 2>&1 ";
						exec( "cd {$this->prefs->tmp_dir} && {$cmd}", $out, $retvar );
						if ( $retvar > 0 ) {
							$this->files[$files_key]['error'] = file_get_contents("{$this->prefs->tmp_dir}/{$fname}.nohot.bed");
							$this->error[] = "Error removing HOT regions from {$fname}";
							continue;
						}
						rename("{$this->prefs->tmp_dir}/{$fname}.nohot.bed", "{$this->prefs->tmp_dir}/{$fname}.valid.bed");

					}


				} else {
					return false;
				}

				$chipfile_array[$files_key]['id'] = $fid;
				$chipfile_array[$files_key]['filename'] = "{$fname}.valid.bed";
				$chipfile_array[$files_key]['filepath'] = "{$this->prefs->tmp_dir}/{$fname}.valid.bed";

				$files_key++;

			}
		}

		$this->files->chipfiles = $chipfile_array;
		return true;

	}

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

			if ( $num_cols < 3 ) {
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
		$m->From(MAIL_FROM, 'Biotools.fr '.CAT_TYPE.' Mailer');
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

	public function ajax()
	{

		if ( $_GET['do'] == 'get_factor_numbers' ) {
			/* request for number of factors for selected tissue */
			if ( !is_array($_POST['tissues']) )
				exit();
			foreach ($_POST['tissues'] as $tissue_id => $val ) {

				$tissue_id = (int)$tissue_id;
				$query = "SELECT id, factor_id FROM `collections` WHERE `tissue_id`={$tissue_id} AND `exclude`!=1";
				$results = $this->db->get_results($query);
				if (!is_array($results))
					exit();

				foreach( $results as $result) {
					$query = "SELECT COUNT(*) FROM `gat_data` WHERE `collection_id_segments`={$result->id} LIMIT 1";
					if ( $this->db->get_var($query) > 0 ) {
						$factors[$result->factor_id] = $factors[$result->factor_id] + 1;
					}
				}
			}

			if ( !is_array($factors) )
				exit();

			/* calculate the total number of datasets for each factor group */

			foreach ($factors as $factor_id => $count) {
				/* $this->factor_groups is set by __construct */
				foreach ($this->factor_groups as $abbreviation => $array) {

					if ( isset($this->factor_groups[$abbreviation]['factors'][$factor_id]) ) {
						$this->factor_groups[$abbreviation]['total_counts'] = $this->factor_groups[$abbreviation]['total_counts'] + $count;
					}
				}
			}

			/* for debugging, lets remove the factors part of the array that serves no purpose now in ajax responses */
			foreach ($this->factor_groups as $abbreviation => $array) {
				unset($this->factor_groups[$abbreviation]['factors']);
			}

			/* print out the json array of factor IDs that have pre-computed data */
			$output['factor_group_counts'] = $this->factor_groups;
			$output['factor_counts'] = $factors;
			//echo "[".json_encode($output)."]";
			echo json_encode($output);
			exit;

		}

	}

	public function __destruct()
	{
		parent::__destruct();
	}

}

?>