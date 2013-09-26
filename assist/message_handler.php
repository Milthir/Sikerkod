<?

function reload($new_location="")
{
	if($new_location == "" || gettype($new_location) != "string")
	{
		header("Location: ".getFullGET());
		die('<script>setTimeout("window.location=window.location",500)</script>');
	}
	else
	{
		header("Location: ".$new_location);
		die('<script>setTimeout("window.location=\''.$new_location.'\'",500)</script>');
	}
}

function message($text,$location="")
{
	$_SESSION['mh_msg'] .= $text;
	if($location != "")$_SESSION['mh_msg_location'] = $location;
}

function ask($text,$ask_yes_field="ask_yes")
{
	if($_POST[$ask_yes_field])
	{
		return 1;
	}
	else 
	{
		$_SESSION['mh_ask'] = $text;
		$_SESSION['mh_ask_post'] = $_POST;
		mh_endMessageCapture();
		return 0;
	}
}
function mh_isHaveMessage()
{	
	return $_SESSION['mh_reloaded'] && (mh_isAsk() || mh_isMessage());
}
function mh_isAsk()
{
	return trim($_SESSION['mh_ask2']) != "";/*$GLOBALS['mh_ask_volt'];*/
}
function mh_isMessage()
{
	return trim($_SESSION['mh_msg2']) != "";/*$GLOBALS['mh_msg_volt'];*/
}

function mh_echoMessages()
{	
	if(trim($_SESSION['mh_msg2']) != "")
	{
		echo $_SESSION['mh_msg2'];
	}
	if(trim($_SESSION['mh_ask2']) != "")
	{
		echo $_SESSION['mh_ask2'];
	}
}

function mh_echoAskPostInputs()
{
	foreach($_SESSION['mh_ask_post'] as $key => $val)
	{
		echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
	}
	unset($_SESSION['mh_ask_post']);
	?>
	<input type="hidden" name="ask_yes" value="1" />
	<?
}

function mh_startMessageCapture()
{	
	if(session_id() == "")
	{
		session_start();
	}
	ob_start();
}

function mh_endMessageCapture()
{
	unset($_SESSION['mh_msg2']);
	unset($_SESSION['mh_ask2']); 
	if((isset($_SESSION['mh_msg']) || isset($_SESSION['mh_ask'])))
	{
		if($_SESSION['mh_reloaded'])
		{
			unset($_SESSION['mh_msg']);
			unset($_SESSION['mh_ask']);
			unset($_SESSION['mh_reloaded']);
			if(!isset($_GET['notreload']))report_error("message_handler::endMessageCapture hurok valószínűleg.");
		}
		$_SESSION['mh_reloaded'] = 1;
		
		$_SESSION['mh_msg2'] = $_SESSION['mh_msg'];
		$_SESSION['mh_ask2'] = $_SESSION['mh_ask'];	
		unset($_SESSION['mh_msg']);
		unset($_SESSION['mh_ask']);
		
		$temp="";
		if($_SESSION['mh_msg_location']) 
		{		
			$temp = $_SESSION['mh_msg_location'];
			unset($_SESSION['mh_msg_location']);
		}
		if(!isset($_GET['notreload']))reload($temp);/*üres string-re csak frissít*/
	}
	else
	{
		unset($_SESSION['mh_reloaded']);
		ob_end_flush();
	}
}
?>
