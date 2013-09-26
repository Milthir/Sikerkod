<?php
	include 'assist/debug.php';
	include 'connect.php';

	include 'assist/security.php';
	include 'assist/feedback.php';
	include 'assist/post.php';
	include 'assist/get.php';
	include 'assist/string.php';
	include 'assist/sql.php';
	include 'assist/pic.php';
	include 'assist/message_handler.php';
	include 'assist/template.php';
	include 'assist/date.php';
	include 'assist/mailer.php';
	include 'classes/forefather.php';
	include 'classes/profile.php';
	include 'classes/exports.php';


	include 'model/event.php';
	installLogger();
	
	$ADMIN_MODE = isset($_GET['admin']);

	$event->setAdminMode($ADMIN_MODE);
	
	mh_startMessageCapture();
	$DEL_ARRAY = array("op","type");
function drawSomething(){
	echo '<script>alert("Meleg")</script>';
	return 0;
}

?>

<!DOCTYPE html>

<html>
<head>
<meta charset="utf-8">
<title>MyCalendar</title>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

<script>
 
function ownAlert(id) {
	alert("Clicked ID = " + id);
}
</script>
</head>
<body>
 <? //startCapture(); ?>
<!--[if {admin_mode}]
	<form method="post" action="">{hidden_zone}
		Kezdő dátum: {field_startDate} <br /> 
		Befejező dátum: {field_endDate} <br /> 
		Név: {field_name} <br />
		Email: {field_email} <br />
		Számlázási cím: {field_billAddr} <br />
		Telefonszám: {field_phoneNumber} <br />
		Accepted: {field_accepted} <br />
		Token: {field_token} <br />
		<input type="submit" value="Találka módosítása" />
	</form>
	[else]
		Kezdő dátum: {field_startDate} <br /> 
		Befejező dátum: {field_endDate} <br /> 
		Név: {name} <br />
		Email: {email} <br />
		Számlázási cím: {billAddr} <br />
		Telefonszám: {phoneNumber} <br />
		Accepted: {accepted} <br />
		Token: {token} <br />
[/if] -->
<?
/*
$event->process();
$event->setTemplate(PROFILE_TEMPLATE_MAIN,new template(endCapture()));
$event->setTemplate(PROFILE_TEMPLATE_FRAME, new template("{body} [if {admin_mode}]<hr/>Új hozzáadása{newbody}[/if]"));
$event->show();
*/
?>
<?php 
include 'calendar.php'; 
?>
<hr />
</body>
</html>

<? mh_endMessageCapture(); ?>
