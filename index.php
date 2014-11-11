<?php 
/**
 * @author andreas.drewke
 * @version $Id$
 */
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>Mailer</title>
	</head>
	<body>
		<h1>1. Mailer</h1>
		<h2>1.1 What is it?</h2>
		<p>This is a small and easy to use mailer class set which is capable of:</p>
		<ul>
			<li>Sending UTF8(only) mails (subject and content)</li>
			<li>Using text/plain and text/html</li>
			<li>Sending an text/plain alternative if using text/html(using customized html2text)</li>
			<li>Sending attachments</li> 
		</ul>
		<h2>1.2 Why?</h2>
		<p>
			I developed Mailer because I did not find an easy to use, light open source solution.
		</p>
		<h2>1.2 Who did this?</h2>
		<p>
			Mailer was programmed by <a href="https://github.com/andreasdr/">Andreas Drewke</a> at <a href="http://www.slipshift.net">SlipShift GmbH</a>. SlipShift allowed me to open source this! Thank you. Maybe someone has a use for it.
		</p>
		<h2>1.3 Test</h2>
		<p>
			<form action="./test_mailer.php" method="get">
				Send from:<br>
				<input type="text" name="sendfrom"><br>
				Send to(multiple email adresses can be separated by semicoln:<br>
				<input type="text" name="sendto"><br>
				<br>
				<input type="submit" value="Test Mailer" ><br>
			</form>
		<p>
	</body>
</html>