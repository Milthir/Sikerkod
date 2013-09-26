<?

function sendEMail($from,$to,$subject,$uzi,$html=true)
{
	$head = 'From: '.$from."\r\n".
		'MIME-Version: 1.0' . "\r\n".
		'Content-type: text/'.($html?'html':'plain').'; charset=utf-8' . "\r\n".
		'Reply-To: '.$from."\r\n".
		'Return-Path: '.$from."\r\n".
		'Organization: Bojti ékszer'."\r\n".
		'X-Mailer: PHP/' . phpversion();

	if($html)
	{
		$uzi = '<html><head><title>'.$subject.'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> </title></head><body>'.$uzi.'</body></html>';
	}

	if(is_array($to))
	{
		$to = implode(", ",$to);
	}
	if(!@mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $uzi, $head)){
		$GLOBALS['mailError'] = error_get_last();
	}
}

?>
