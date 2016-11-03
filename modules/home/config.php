<?php

function gat_score($l2fold=0, $pvalue=1, $percent_overlap_size_track=0, $percent_overlap_size_annotation=0, $increase_sensitivity=false) {

	if ($increase_sensitivity === true) {
		if ( $percent_overlap_size_track > 100 )
			$percent_overlap_size_track = 100;
		if ( $percent_overlap_size_track == 0 )
			$percent_overlap_size_track = 0.1;

		$percent_overlap_size_annotation = ($percent_overlap_size_annotation + 0.1);
		if ( $percent_overlap_size_annotation > 100 )
			$percent_overlap_size_annotation = 100;
		if ( $percent_overlap_size_annotation == 0 )
			$percent_overlap_size_annotation = 0.1;

		$percent_overlap_size_coefficient = ( log10($percent_overlap_size_track) * log10($percent_overlap_size_annotation) );
	} else {
		$percent_overlap_size_coefficient = 1;
	}

	$score = round ( ( (-(log10($pvalue)) * $l2fold ) * $percent_overlap_size_coefficient), 4);

	return $score;
}

function z_score($observed, $expected, $stddev) {

	$score = round( ( ($observed - $expected)/$stddev ), 2 );
	if ($score > 300)
		$score = 300;
	return $score;
}

function data_matrix($results, $files, $genes=false) {

	if ( !is_array($results) || !is_array($files) )
		return false;

	/**
	 the results data matrix
	 **/

	//first the header row
	$matrix = "";
	if ( $genes === false || !is_array($genes) ) { //regular ChIP bed file
		foreach ( $results as $key=>$array ) {
			$matrix .= "\t\"" . $files['chipfiles'][$key]['id'] . '"';
		}
		$matrix .= "\n";
	} else {
		foreach ($files['chipfiles'] as $id=>$array) {
			$matrix .= "\t\"" . $array['id'] . '"';
		}
		$matrix .= "\n";
	}

	//now each result
	foreach ( $results as $key=>$array ) {
		//first column
		if ( $genes === false || !is_array($genes) ) { //regular ChIP bed file
			$matrix .= '"' . $files['chipfiles'][$key]['id'] . '"';

		} else {
			$matrix .= '"' . $genes[$key] . '"'; //inidividual gene IDs are the rows
		}
		//data columns
		foreach ( $array as $score ) {
			$matrix .= "\t{$score}";
		}
		$matrix .= "\n";
	}
//echo $matrix;
	return $matrix;

}


function get_genomes() {

	$dir_list = scandir( SYSTEM_DATA_ROOT.'/chromSizes' );

	if ( !is_array($dir_list) )
		return( array() );

	$genome_array = array();

	foreach ($dir_list as $file) {

		if ( !preg_match("/\.chrom\.sizes$/", $file) )
			continue;

		$genome_array[] = preg_replace("/\.chrom\.sizes$/", "", $file);

	}

	return $genome_array;

}

function create_chromSizes_bed($genome) {

	$lines = file( SYSTEM_DATA_ROOT.'/chromSizes/'.$genome.'.chrom.sizes', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

	$bed = "";
	foreach($lines as $line) {
		list($chr, $end) = explode("\t", $line);
		$bed .= "{$chr}\t0\t{$end}\tws\n";
	}

	$bedfile = SYSTEM_DATA_ROOT."/chromSizes_BED/{$genome}.bed";

	file_put_contents($bedfile, $bed);

	return $bedfile;

}

function create_heatmap($tmp_results_dir, $plot_title=false) {

	if ($plot_title === false)
		$plot_title = CAT_TYPE." heatmap";

	copy( SYSTEM_DATA_ROOT . "/".CAT_TYPE."_heatmap.R", $tmp_results_dir . "/".CAT_TYPE."_heatmap.R");
	chmod($tmp_results_dir . "/".CAT_TYPE."_heatmap.R", 0755);

	$command = "cd {$tmp_results_dir} && ./".CAT_TYPE."_heatmap.R '{$plot_title}'";

	exec( $command, $output, $retvar );

	if ( is_file($tmp_results_dir . "/Rplots.pdf") )
		unlink($tmp_results_dir . "/Rplots.pdf");

	if ( $retvar == 0 && is_file($tmp_results_dir . "/".CAT_TYPE."_heatmap.pdf") )
		return true;
	else
		return false;

}

function create_zip($output_dir, $title=false) {
//echo "zipping {$output_dir}\n";
	/**
	package the results up
	**/
	if ( $title !== false )
		$title = CAT_TYPE."_results-" . preg_replace("|[\s]|", "_", $title);
	else
		$title = CAT_TYPE."_results";


//echo "\t$zipname\n";
	if ($handle = opendir( "{$output_dir}/results" )) {
		while (false !== ($file = readdir($handle))) {
			if ( !preg_match("/\.pdf$/", $file) && preg_match("/\.bed$/", $file) && preg_match("/\.tsv$/", $file) ) {
				unlink("{$output_dir}/results/{$file}");
			}
		}
		closedir($handle);
	} else {
		die("Could not read directory {$output_dir}/results");
	}

	rename("{$output_dir}/results", "{$output_dir}/{$title}");
	$cat_type = CAT_TYPE;
	`cd {$output_dir} && /bin/tar -cf {$cat_type}_results.tar {$title}`;
//	`cd {$output_dir} && /usr/bin/zip {$cat_type}_results {$title}`;

	return $output_dir;

}


?>