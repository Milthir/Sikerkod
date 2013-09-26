<?

function handleFatalPhpError() {
	$last_error = error_get_last();
	if($last_error['type'] === E_ERROR || $last_error['type'] === E_USER_ERROR) {
		sendEMail("info@h3tech.hu","hudi1989@gmail.com","HIBA TÖRTÉNT", $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]." ".listArray($_GET,false)." ".listArray($_POST,false)."".listArray($_SESSION,false));
	}
}

register_shutdown_function('handleFatalPhpError');


?>
