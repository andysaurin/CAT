<?php
$mailSubject = "test from mailer-daemn";
$mailTo = 'andrew@saurin.com';
$mailBody = "This is a test message

you will see it if it works!";

$replyTo = "no-reply@biotools.fr";


	$mailBody = preg_replace("|\n|", "\r\n", $mailBody);

	define('DISPLAY_XPM4_ERRORS', false);

	require_once 'MAIL.php'; // path to 'SMTP.php' file from XPM4 package

$f = 'postmaster@biotools.fr'; // from mail address
$t = $mailTo; // to mail address
if ( !$replyTo )
	$replyTo = 'geoPlugin Mailer <no-reply@geoplugin.com>'; // return-path
$p = 'XPqwUumd';

	$m = 'From: BioTools.fr Mailer <no-reply@biotools.fr>'."\r\n".
        'To: '.$t."\r\n".
        'Date: '.date('r')."\r\n".
        'Message-Id: <'.md5(microtime(true).$t)."@biotools.fr>\r\n";
    if (isset($replyTo))
    	$m .= "Reply-To: {$replyTo}\r\n";
    $m .= 'Subject: '.$mailSubject."\r\n".
        'Content-Type: text/plain; charset=iso-8859-1'."\r\n\r\n".
        $mailBody;

$c = fsockopen('tls://mailer-daemon.fr', 465, $errno, $errstr, 10) or die($errstr);

if (!SMTP5::recv($c, 220)) die("Cannot send email");
// EHLO/HELO
if (!SMTP5::ehlo($c, 'mailer-daemon.fr')) SMTP5::helo($c, 'mailer-daemon.fr') or die("Cannot send email");
// AUTH LOGIN/PLAIN

if (!SMTP5::auth($c, $f, $p, 'login')) SMTP5::auth($c, $f, $p, 'plain') or die("Cannot send email");
// MAIL FROM
SMTP5::from($c, $f) or die("Cannot send email");
// RCPT TO
SMTP5::to($c, $t) or die("Cannot send email");
// DATA
SMTP5::data($c, $m) or die("Cannot send email");
// RSET, optional if you need to send another mail using this connection '$c'
// SMTP::rset($c) or die(print_r($_RESULT));
// QUIT
SMTP5::quit($c);
// close connection
@fclose($c);


exit();


$email = 'andrew@saurin.com';
$msg = "This is a test message";

$m = new MAIL; // initialize MAIL class
$m->From('postmaster@biotools.fr'); // set from address
$m->AddTo('andrew@saurin.com'); // add to address
$m->Subject('Hello World2!'); // set subject
$m->Text('Text message.'); // set text message

// connect to MTA server 'smtp.hostname.net' port '25' with authentication: 'username'/'password'
$pop3 = POP35::Connect('pop3.biotools.fr', 'postmaster@biotools.fr', 'XPqwUumd') or die("no pop3 connect\n".print_r($_RESULT));
$c = $m->Connect('smtp.biotools.fr', 25, 'postmaster@biotools.fr', 'XPqwUumd') or die("no smtp connect\n".print_r($m->Result));

// send mail relay using the '$c' resource connection
echo $m->Send($c) ? 'Mail sent !' : 'Error !';

$m->Disconnect(); // disconnect from server
POP35::disconnect($pop3); // disconnect
print_r($m->History); // optional, for debugging

?>