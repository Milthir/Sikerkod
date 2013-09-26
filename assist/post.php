<?
function getPOST($id,$alap)
{
	return isset($_POST[$id])?$_POST[$id]:$alap;
}
function isInPOST($postAr)
{
	foreach($postAr as $key)
	{
		if(!isset($_POST[$key]))
		{
			return false;
		}
	}
	return true;
}
function getFullPOST()
{
	$szov="";
	foreach ($_POST as $kulcs => $ertek)
	{
		$szov .= $kulcs.'='.$ertek.'<br />';
	}
	return $szov;
}
function isAction($action)
{
	return isset($_POST['action'])?($_POST['action']==$action):false;
}

?>