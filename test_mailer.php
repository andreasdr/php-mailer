<?php 
/**
 * @author andreas.drewke
 * @version $Id: test_mail.php 3194 2011-11-09 12:09:22Z andreasdrewke $
 */

require_once './libs/html2text/html2text.php';
require_once './classes/MailAttachment.php';
require_once './classes/Mail.php';

// subject
$subject = 
	'Test mail with german Umlaute in subject(äöüÄÖÜß)';
$subjectPlusAttachments =
	' and attachments';
$subjectTextPlain =
	' (text/plain)';
$subjectTextHTML =
	' (text/html)';

// message
$message =
	'<html>' .
	'	<body>' .
	'		<h1>Hello</h1>' .
	'		<p>I am a test link: <a href="http://slipshift.net/">SlipShift GmbH</a>.</p>' .
	'		<p>I am another test link: <a href="http://drewke.net/">drewke.net</a>.</p>' .
	'		<p>I am a paragraph.</p>' .
	'		<p>I am some german Umlaute: äöüÄÖÜß.</p>' .
	'		<p>I am some more text: Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>' .
	'		<p>See you!</p>' .
	'	</body>' .
	'</html>';

// sender, receiver
$sendFrom = $_GET['sendfrom'];
$sendTo = explode(';', $_GET['sendto']);

// attachments
$attachments = array(
	MailAttachment::createFromString(
		'lorem.ipsum-1.txt',
		'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
		'text/plain'
	),
	MailAttachment::createFromString(
		'lorem.ipsum-2.txt',
		'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
		'text/plain'
	),
	MailAttachment::createFromString(
		'lorem.ipsum-3.txt',
		'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
		'text/plain'
	),
);

?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>Mailer</title>
	</head>
	<body>
		<h1>1. Mailer</h1>
		<h2>2. Sending test mails</h2>
		<ul>
<?php

// text/plain mail
$mail = Mail::composeMail(
	$sendFrom,
	$sendTo,
	Mail::CONTENTTYPE_PLAIN,
	$subject . $subjectTextPlain,
	convert_html_to_text($message)
);
$success = $mail->send();

?>
		<li>Sending mail(text/plain): <?php echo $success == true?'OK':'FAILED'; ?></li>
<?php

// text/html mail
$mail = Mail::composeMail(
	$sendFrom,
	$sendTo,
	Mail::CONTENTTYPE_HTML,
	$subject . $subjectTextHTML,
	$message
);
$success = $mail->send();

?>
		<li>Sending mail(text/html): <?php echo $success == true?'OK':'FAILED'; ?></li>
<?php

// text/plain, attachment mail
$mail = Mail::composeMail(
	$sendFrom,
	$sendTo,
	Mail::CONTENTTYPE_PLAIN,
	$subject . $subjectPlusAttachments . $subjectTextPlain,
	convert_html_to_text($message),
	$attachments
);
$success = $mail->send();

?>
		<li>Sending mail(text/plain + attachments): <?php echo $success == true?'OK':'FAILED'; ?></li>
<?php

// text/html, attachment mail
$mail = Mail::composeMail(
	$sendFrom,
	$sendTo,
	Mail::CONTENTTYPE_HTML,
	$subject . $subjectPlusAttachments . $subjectTextHTML,
	$message,
	$attachments
);
$success = $mail->send();

?>
		<li>Sending mail(text/html + attachments): <?php echo $success == true?'OK':'FAILED'; ?></li>
		</ul>
		Get <a href="./index.php">back</a>.
	</body>
</html>