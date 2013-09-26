<?
$GLOBALS['month_name_ar'] = array("január","február","március","április","május","június","július","augusztus","szeptember","október","november","december");
$GLOBALS['month_short_name_ar'] = array("jan","febr","márc","ápr","máj","jún","júl","aug","szept","okt","nov","dec");
function getMonthName($id)
{
	return $GLOBALS['month_name_ar'][$id-1];
}
function getMonthShortName($id)
{
	return $GLOBALS['month_short_name_ar'][$id-1];
}

?>
