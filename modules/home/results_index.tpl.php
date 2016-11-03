<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . "/../config/config.php" );

$uniq_id = basename( (  dirname(__FILE__) ) );

exec("grep {$uniq_id} " . SYSTEM_DATA_ROOT . "/cron/##CAT_TYPE##_processing", $output, $retvar);

if ( $retvar == 0 && is_array($output) ) {

	$msg = "Your job is currently running.<h3>If you supplied an email address, then you will be informed by email when the analysis has been completed.<br>If not, leave this window open and the results will appear here when completed.</h2>";

} else {

	/**
	find out where we are in the queue
	*/
	$queue_array = file( SYSTEM_DATA_ROOT . "/cron/##CAT_TYPE##_queue", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	if ( !is_array($queue_array) ) {

		die("Job neither in queue, nor being processed. Please resubmit.");

	}

	$pos = array_search( $uniq_id, $queue_array );

	if ( $pos > 0 ) {
		if ( $pos == 1 )
			$msg = "There is 1 other job waiting to be run prior to yours.";
		else
			$msg = "There are {$pos} other jobs waiting to be run prior to yours.";
	} else {
		$msg = "Your job will start momentarily...";
	}
}

?>
<html>
	<head>
		<meta http-equiv="refresh" content="15">
	</head>
	<body>
		<h2><?php echo $msg;?></h2>
	</body>
</html>