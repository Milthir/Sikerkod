<?
function getGET($id,$alap)
{
	return isset($_GET[$id])?$_GET[$id]:$alap;
}
function getNumberGET($id,$alap)
{
	return (isset($_GET[$id])&&isNumber($_GET[$id]))?$_GET[$id]:$alap;
}
function getFullGET()
{
	$szov="?";
	foreach ($_GET as $kulcs => $ertek)
	{
		$szov .= $kulcs.'='.$ertek.'&';
	}
	return $szov;
}

function isInGET($getAr)
{
	foreach($getAr as $key)
	{
		if(!isset($_GET[$key]))
		{
			return false;
		}
	}
	return true;
}

$addGET = 1;
function addDelGET($add,$del)
{
	$addDelGET_START = 1;
	$addDelGET_szov="?";
	foreach ($_GET as $kulcs => $ertek)
	{
		$addDelGET_del = 1;
		foreach ($del as $kulcs2)
		{
			if ($kulcs==$kulcs2)
			{
				$addDelGET_del=0;
			}
		}
		$addDelGET_add = 1;
		foreach ($add as $kulcs2 => $ertek2)
		{
			if ($kulcs==$kulcs2)
			{
				$addDelGET_add=0;
			}
		}
		if ($addDelGET_add && $addDelGET_del)
		{
			$addDelGET_szov .= ($addDelGET_START?"":"&").$kulcs.'='.$ertek;
			$addDelGET_START = 0;
		}
	}
	foreach ($add as $kulcs => $ertek)
	{
		$addDelGET_szov .= ($addDelGET_START?"":"&").$kulcs.'='.$ertek;
		$addDelGET_START = 0;
	}

	return $addDelGET_szov;
}
function delGET($del)
{
	$delGET_START = 1;
	$delGET_szov="?";
	foreach ($_GET as $kulcs => $ertek)
	{
		$delGET_add = 1;
		foreach ($del as $kulcs2)
		{
			if ($kulcs==$kulcs2)
			{
				$delGET_add=0;
			}
		}
		if ($delGET_add)
		{
			$delGET_szov .= ($delGET_START?"":"&").$kulcs.'='.$ertek;
			$delGET_START = 0;
		}
	}
	return $delGET_szov;
}	
function addGET($get)
{
	$addGET_START = 1;
	$addGET_szov="?";
	foreach ($_GET as $kulcs => $ertek)
	{
		$addGET_add = 1;
		foreach ($get as $kulcs2 => $ertek2)
		{
			if ($kulcs==$kulcs2)
			{
				$addGET_add=0;
			}
		}
		if ($addGET_add)
		{
			$addGET_szov .= ($addGET_START?"":"&").$kulcs.'='.$ertek;
			$addGET_START = 0;
		}
	}
	foreach ($get as $kulcs => $ertek)
	{
		$addGET_szov .= ($addGET_START?"":"&").$kulcs.'='.$ertek;
		$addGET_START = 0;
	}
	return $addGET_szov;
}
?>