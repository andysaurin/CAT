<?php

class cron extends NQ_Auth_No
{
	public function __construct()
	{
		parent::__construct();

		if ( php_sapi_name() != 'cli' )
			die("CLI execution only");

		if ( !$_GET['action'] )
			die("An action is required");

		define ("CAT_TYPE", $_GET['action']);
		define ("CAT_TYPE_LC", strtolower(CAT_TYPE));

//		require_once( dirname(__FILE__)."/nconfig.php" );

		$this->remove_old_results();

		$this->cron_dir = SYSTEM_DATA_ROOT . "/cron";

		if ( CAT_TYPE != 'gat_cache' ) {
	
			$this->processing_array = file( "{$this->cron_dir}/".CAT_TYPE_LC."_processing", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
			if ( is_array($this->processing_array) && count($this->processing_array) >= CRON_MAX_CONCURRENT ) {
				// already running maximum number of cron jobs
				echo "Max cron job limit reached - exiting\n";
				exit(0);
			}
	
			$this->queue_array = file( "{$this->cron_dir}/".CAT_TYPE_LC."_queue", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
			if ( !is_array($this->queue_array) || count($this->queue_array) == 0 ) {
				// no data in queue
				echo "No data to process - exiting\n";
				exit(0);
			}
		
		}
		
        $load = @file_get_contents('/proc/loadavg');
        $load = explode(' ', $load, 1);
        $load = $load[0];
        
        $max_capacity = round( ($load / SYSTEM_CORES) * 100);
        if ( $max_capacity > 100 ) {
			echo "Server capacity at {$max_capacity}% - aborting run\n";
			exit();
	    }
	    
        if ( $load > LOAD_ALERT ) {
	        $this->num_threads = 1;
        } else {
	        $this->num_threads = GAT_RUN_THREADS;
        }
		
	}

	public function __default()
	{

		if ( CAT_TYPE == 'gat_cache' ) {
			$this->gat_cache('gat_data');
			$this->gat_cache('gat_data_noHOT');
			exit;
		}
		
		$this->uniq_id = $this->queue_array[0];

		if ( !$this->uniq_id ||  strlen($this->uniq_id) < 1 || preg_match('|![a-zA-Z0-9]+|', $this->uniq_id) )
			exit(0);

		$this->tmp_dir = SYSTEM_TMPDIR . "/{$this->uniq_id}";
		$this->tmp_results_dir = "{$this->tmp_dir}/results";

		if ( !is_file( "{$this->tmp_dir}/cron_vars.php" ) ) {
			//no data to run for this ID
			echo "{$this->tmp_dir}/cron_vars.php does not exist - removing {$this->uniq_id} from queue\n.";

			$this->remove_from_queue( $this->uniq_id );
			//cleanup
			`rm -Rf {$this->tmp_dir}`;
			exit(0);
		}

		/** add job to processing list **/
	 	$this->remove_from_queue( $this->uniq_id );
	 	$this->processing( $this->uniq_id );

		require_once( SYSTEM_TMPDIR . "/{$this->uniq_id}/cron_vars.php");

		$this->prefs = $prefs;
		$this->files = $files;
		$this->results = $results;

/* echo "<pre>"; */

		if ( CAT_TYPE == "webCAT" ) {

			if ( $this->prefs['analysisType'] == 'chipchip' ) {
				echo "Running chip versus chip analysis\n";
				$matrix = $this->chip_bed_comparison(); // all vs all analysis
			} else {
				echo "Running gene versus chip analysis\n";
				$matrix = $this->gene_bed_comparison();	// individual genes with chip data
			}

		} else {

			if ( $this->prefs['unified_bed'] === true )
				$matrix = $this->chip_bed_comparison(); // all vs all analysis
			else
				$matrix = $this->gene_bed_comparison();	// individual genes with all modeENCODE chip data

		}

		$this->tmp_results_dir = $this->prefs['tmp_dir'] . "/results";
		mkdir($this->tmp_results_dir);
		$fh = fopen($this->tmp_results_dir . "/".CAT_TYPE."_scores.xls", "w+") or die("Execution error - could not write score matrix to disk.");
		fwrite($fh, $matrix);
		fclose($fh);


		/**
		 create the heatmap
		 **/
		//copy( dirname( __FILE__ ) . "/heatmap.R", $this->tmp_results_dir . "/heatmap.R");
		if ( !create_heatmap($this->tmp_results_dir, $this->prefs['title']) )
			$no_pdf = true;
		else
			copy($this->tmp_results_dir . "/".CAT_TYPE."_heatmap.pdf", $this->prefs['tmp_dir'] . "/".CAT_TYPE."_heatmap.pdf");

		$output_dir = SYSTEM_DOCUMENT_ROOT . "/downloads/" . $this->uniq_id;

		unlink("{$output_dir}/index.php");
		rmdir($output_dir);

		rename($this->prefs['tmp_dir'], $output_dir);

		if ( isset($this->prefs['supplied_title']) )
			$this->results_dir = create_zip( $output_dir, $this->prefs['title'] );
		else
			$this->results_dir = create_zip( $output_dir );

		$success = "<h2>".CAT_TYPE." was successfully run</h2>";
		if ( isset($no_pdf) ) {
			$success .= "<h4>You can download the results using <a href=\"/downloads/{$this->uniq_id}/".CAT_TYPE."_results.tar\" target=\"_new\">this link</a>.</h4>";
		} else {
			$success .= "<h4>You can view the generated heatmap <a href=\"/downloads/{$this->uniq_id}/".CAT_TYPE."_heatmap.pdf\" target=\"_new\">HERE</a></h4>";
			$success .= "<h4>and download the results using <a href=\"/downloads/{$this->uniq_id}/".CAT_TYPE."_results.tar\" target=\"_new\">this link</a>.</h4>";
		}
		$success .= "<br />Results will be available for 48 h.\n";

//		$this->session->success = $success;

		file_put_contents( $this->results_dir . "/index.php", "<html><body>{$success}</body></html>");

		$this->remove_from_processlist( $this->uniq_id );
		$this->mail_results( $this->prefs['email'] );

		echo $success;
		exit;
	}

	private function gat_cache($db='gat_data') {
		
		// sets all GAT scores into the query cache to speed up CAT submit times
		$query = "SELECT `id` FROM `collections` ORDER BY `id`";
		$results = $this->db->get_results($query);

		$start = time();
				
		foreach( $results as $result ) {
			
			$query2 = "SELECT `collection_id_annotations`, `observed`, `expected`, `stddev`, `l2fold`,`pvalue`, `percent_overlap_size_track`, `percent_overlap_size_annotation` FROM `{$db}` FORCE INDEX (collection_id_segments) WHERE `collection_id_segments`=".$result->id." ORDER BY `collection_id_segments`";
			$this->db->get_results($query2);
			
		}
		$end = time();
		echo "MySQL Cache for {$db} loaded in ".($end-$start)." seconds\n";	
		
	}
	
	private function chip_bed_comparison() {

		$num_ids = count( $this->results );

		foreach ( $this->results  as $id => $score_array ) {

			//A user-submitted BED file will be an array of 'NA's
			if ( count( array_keys($score_array, 'NA') ) == $num_ids ) {

				foreach ( $score_array as $file_id => $score ) {

					if ( isset($this->results[$file_id][$id]) && $this->results[$file_id][$id] != 'NA' ) { //we've already calculated the complementary score
						if ($this->results[$id][$file_id] == 'NA')
							$this->results[$id][$file_id] = $this->results[$file_id][$id];
						continue;
					}
					if ( isset($this->results[$id][$file_id]) && $this->results[$id][$file_id] != 'NA' ) { //we've already calculated the complementary score
						if ($this->results[$file_id][$id] == 'NA')
							$this->results[$file_id][$id] = $this->results[$id][$file_id];
						continue;
					}


					if ( $file_id == $id && $this->prefs['unified_bed'] === true ) { // all vs all array and we are at self vs self in the array
						if ( $this->prefs['selfReport'] != 'actual' ) {
							$this->results[$id][$file_id] = $this->prefs['selfReport'];
							continue;
						}
					}

					//run gat
					echo CAT_TYPE." run on ID {$file_id} vs {$id} - ";

					$scores = $this->gat_run( $this->files['chipfiles'][$file_id]['filepath'], $this->files['chipfiles'][$id]['filepath'] );
					//gives eg: array[H3k4_Peak] => 11.8161
					$score = array_pop($scores); //for chip bed files, there is only one score

					$this->results[$id][$file_id] = $score;
					$this->results[$file_id][$id] = $score; //reciprocal is same

				}
			}

		}

		/**
		 the TSV results data matrix
		 **/
		return data_matrix($this->results, $this->files);

	}

	private function gene_bed_comparison() {

		$gene_bed_file = $this->files['genefile']['filepath'];

		if ( !is_file($gene_bed_file) ) {
			echo "Error - no such file: {$gene_bed_file}\n";
			return false;
		}
//die(print_r($this->results));

		foreach ( $this->results  as $gene_id => $score_array ) {

			foreach ( $score_array as $file_id => $score ) {
				//run gat
				echo "\n\nGAT run on fileID {$file_id} (".$this->files['chipfiles'][$file_id]['id'].") vs gene_bed_file - ";

				$scores = $this->gat_run( $this->files['chipfiles'][$file_id]['filepath'], $gene_bed_file );


				if ( is_array($scores) && count($scores) ) {
					foreach ($scores as $gene_name => $score) {
						echo "\n{$gene_name} = $score";

						$gene_key = array_search($gene_name, $this->prefs['genes']);

						if ( isset($this->results[$gene_key][$file_id]) )
							$this->results[$gene_key][$file_id] = $score;
						else
							echo "\nERROR - failed finding '{$gene_name}' in gene array, so no result could be set in this->results[{$gene_key}][{$file_id}]\n";
					}
				} else {

					echo "\nERROR - no scores returned\n";

				}

			}
			break; //only need to run GAT once for the genes (each gene ID runs GAT many times on the ChIP files)
		}

		/**
		 the TSV results data matrix
		 **/

		return data_matrix( $this->results, $this->files, $this->prefs['genes'] );

	}

	private function gat_run($segments, $annotations, $id=null) {

		if ( !is_file($segments) || !is_file($annotations) ) {
			echo "gat_run() warning: no file: either\n\t{$segments}\nor\n\t{$annotations}\n";
			return $this->prefs['selfReport'];
		}

		//gat prefs
		$gat = GAT_RUN;
		$num_threads = $this->num_threads;
		$workspace = $this->prefs['chrom_sizes_bed'];
		$sensitivity = $this->prefs['increase_sensitivity'];
		
		$gat_log = SYSTEM_TMPDIR . "/{$this->uniq_id}/gat.log";
		$gat_output = SYSTEM_TMPDIR . "/{$this->uniq_id}/gat.tsv";

		//cleanup any old tmp log/run files
		if ( is_file($gat_log) )
			unlink($gat_log);

		if ( is_file($gat_output) )
			unlink($gat_output);

		$gat_command = "nice -n 19 {$gat} --num-threads={$num_threads} --workspace='{$workspace}' --segments='{$segments}' --annotations='{$annotations}' --ignore-segment-tracks --num-samples=1000 --nbuckets=500000 --log={$gat_log} > {$gat_output} 2>/dev/null";
		//run gat on the bed files
		echo "\nRunning: {$gat_command}\n";
		`$gat_command`;

		if ( !is_file($gat_output) ) {
/* 			@unlink($gat_log); */
			echo "\tError - no file {$gat_output}\n";
			return $this->prefs['selfReport'];

		}
		$output_array = file($gat_output, FILE_IGNORE_NEW_LINES);
		if ( !is_array($output_array) || count($output_array) < 2 ) {
/*
			@unlink($gat_log);
			@unlink($gat_output);
*/
			echo "\tError - no gat output\n";
			return $this->prefs['selfReport'];
		}

		if ( is_array($output_array) && count($output_array) > 1 ) {
			foreach ( $output_array as $data ) {
				list($track, $annotation, $observed, $expected, $CI95low, $CI95high, $stddev, $fold, $l2fold, $pvalue, $qvalue, $track_nsegments, $track_size, $track_density, $annotation_nsegments, $annotation_size, $annotation_density, $overlap_nsegments, $overlap_size, $overlap_density, $percent_overlap_nsegments_track, $percent_overlap_size_track, $percent_overlap_nsegments_annotation, $percent_overlap_size_annotation ) = explode("\t", $data);

				$annotation = addslashes($annotation); //for gene names

				if ( is_numeric($l2fold) && is_numeric($track_nsegments) && is_numeric($annotation_nsegments) ) {

					$scores[$annotation] = gat_score($l2fold, $pvalue, $percent_overlap_size_track, $percent_overlap_size_annotation, $sensitivity);

				}
			}
		}
		return $scores;

	}


	private function processing( $uniq_id ) {

		file_put_contents( $this->cron_dir . "/".CAT_TYPE_LC."_processing", $uniq_id . "\n", FILE_APPEND | LOCK_EX);
		return;

	}

	private function remove_from_queue( $uniq_id ) {

		if ( !is_array($this->queue_array) ) {
			return false;
		}

		foreach ( $this->queue_array as $k => $v ) {
			if ( $v == $uniq_id ) {
				unset( $this->queue_array[$k] );
				if ( count($this->queue_array > 0) ) {
					$toWrite = implode("\n", $this->queue_array);
					$toWrite .= "\n";
					file_put_contents( $this->cron_dir . "/".CAT_TYPE_LC."_queue", $toWrite,  LOCK_EX);
				
				} else
					file_put_contents( $this->cron_dir . "/".CAT_TYPE_LC."_queue", '',  LOCK_EX);
			}
		}

		return;
	}

	private function remove_from_processlist( $uniq_id ) {

		$this->processing_array = file( "{$this->cron_dir}/".CAT_TYPE_LC."_processing", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

		if ( !is_array($this->processing_array) ) {
			return false;
		}

		foreach ( $this->processing_array as $k => $v ) {
			if ( $v == $uniq_id ) {
				unset( $this->processing_array[$k] );
				if ( count($this->processing_array > 0) ) {
					$toWrite = implode("\n", $this->processing_array);
					$toWrite .= "\n";
					file_put_contents( $this->cron_dir . "/".CAT_TYPE_LC."_processing", $toWrite,  LOCK_EX);
				} else
					file_put_contents( $this->cron_dir . "/".CAT_TYPE_LC."_processing", '',  LOCK_EX);
			}
		}

		return;
	}

	private function remove_old_results() {

		$max_time = ( time() - STORE_DATA_TIME );

		$download_dir = SYSTEM_DOCUMENT_ROOT . "/downloads";
		$tmp_dir = SYSTEM_TMPDIR;

		if ($handle = opendir($download_dir)) {
			while (false !== ($dir = readdir($handle))) {
				if ( $dir == '.' || $dir == '..' )
					continue;

				if ( filectime($download_dir . "/" . $dir) < $max_time ) {
					if ( !is_link($download_dir . "/" . $dir) ) {
						echo "Deleting old data: rm -Rf {$download_dir}/{$dir}\n";
						`rm -Rf {$download_dir}/{$dir}\n`;
					}
				}

			}
		}

	}

	private function mail_results($email=false) {

/*
		if (!$email)
			return false;
*/
		if (!$email)
			return;

		$msg  = "This is the auto-mailer from " . SYSTEM_PAGE_TITLE . "\n".
CAT_TYPE." has finished computing the association of your BED file(s) with your selected ENCODE ChIP data.\n\n".

"The results are available for download at:\n".
"https://".DOMAIN_NAME."/downloads/{$this->uniq_id}/".CAT_TYPE."_results.tar\n\n".

"If you cannot open the .tar file on your PC, download and install the free open source software, 7-Zip ( http://www.7-zip.org )\n\n".

"Note: The results will automatically be erased after 48 hours.\n\n".
"We hope the results prove useful for your research and hope that you will cite it in any publication that makes use of ".CAT_TYPE."!\n\n";


		require_once("MAIL.php");

		$mailBody = preg_replace("|\n|", "\r\n", $msg);
		if (!isset($mailSubject) || !$mailSubject)
			$mailSubject = "";

		$m = 'From: '.CAT_TYPE.' Mailer <'.MAIL_FROM.'>'."\r\n".
	        'To: '.$email."\r\n".
	        'Date: '.date('r')."\r\n".
	        'Message-Id: <'.md5(microtime(true).$email)."@biotools.fr>\r\n";
	    $m .= "Reply-To: ".MAIL_FROM."\r\n";

		if ( isset($this->prefs['supplied_title']) )
			$mailSubject .= CAT_TYPE.' results for your run "'.$this->prefs['title'].'"';
		else
			$mailSubject .= CAT_TYPE.' results';

	    $m .= 'Subject: '.$mailSubject."\r\n".
	    	  'Content-Type: text/plain; charset=iso-8859-1'."\r\n\r\n".
	        $mailBody;


		$f = SMTP_USERNAME; // from mail address
		$p = SMTP_PASSWORD;


		$c = fsockopen('tls://'.SMTP_HOST, 465, $errno, $errstr, 10) or die($errstr);

		if (!SMTP5::recv($c, 220)) die("Cannot send email");
		// EHLO/HELO
		if (!SMTP5::ehlo($c, SMTP_HOST)) SMTP5::helo($c, SMTP_HOST) or die("Cannot send email");
		// AUTH LOGIN/PLAIN

		if (!SMTP5::auth($c, $f, $p, 'login')) SMTP5::auth($c, $f, $p, 'plain') or die("Cannot send email");
		// MAIL FROM
		SMTP5::from($c, $f) or die("Cannot send email");
		// RCPT TO
		SMTP5::to($c, $email) or die("Cannot send email");
		// DATA
		SMTP5::data($c, $m) or die("Cannot send email");
		// RSET, optional if you need to send another mail using this connection '$c'
		// SMTP::rset($c) or die(print_r($_RESULT));
		// QUIT
		SMTP5::quit($c);
		// close connection
		@fclose($c);

	}


	public function __destruct()
	{
		parent::__destruct();
	}

}