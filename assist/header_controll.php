<?
function hc_startHeaderControll()
{
	ob_start();
}

function hc_addToHeader($str)
{
	$GLOBALS['hc_actual_header'] .= $str;
}

function hc_endHeaderControll()
{
	$temp = ob_get_contents();
	ob_end_clean();
	echo $GLOBALS['hc_actual_header'];
	echo $temp;
}

?>