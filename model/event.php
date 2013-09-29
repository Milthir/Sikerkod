<?
$event = new profile(0,
					array(
						"id" => "number",
						"date" => "date",
						"startTime" => "daytime",
						"endTime" => "daytime",
						"name" =>  "textfield",
						"email" => "textfield",
						"billAddr" => "textfield",
						"phoneNumber" => "textfield",
						"accepted" => "check",
						"token" => "textfield"
						
));

$event->setPrefix("event_");
$event->setPrimary("id");
$event->setAutoIncrement("id");
$event->setMessageFunc(message);

if(INSTALL_MODE)$event->install();

?>