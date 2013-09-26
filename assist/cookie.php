<?

function echoAllCookies()
{
	foreach($_COOKIE as $key => $val)
	{
		echo $key.'=>'.$val."<br>";
	} 
	foreach($HTTP_COOKIE_VARS as $key => $val)
	{
		echo $key.'=>'.$val."<br>";
	} 
}

function getCookie($id,$alap)
{
	return isset($HTTP_COOKIE_VARS[$id])?$HTTP_COOKIE_VARS[$id]:($_COOKIE[$id]?$_COOKIE[$id]:$alap);
}

?>