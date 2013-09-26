<?
function randomString($str_len,$str_ar="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz")
{
	$temp_str = "";
	for($a=0;$a<$str_len;$a++)
	{
		$temp_str .= $str_ar[rand(0,strlen($str_ar)-1)];
	}
	return $temp_str;
}


function ifNoname($text,$def)
{
	return trim($text)==""?$def:$text;
}

function arrayToString($ar)
{
	if(!is_array($ar)) return "";
	$str = "";
	$first = true;
	foreach($ar as $key => $val)
	{
		$str .= ($first?"":"|").$key."=>".$val;
	}
	return $str;
}

function stringToArray($str)
{
	$ar = explode("|",$str);
	$retar = array();
	foreach($ar as $key=> $val)
	{
		preg_match("/^([^=]+)=(.+)$/",$val,$reg);
		$retar[$reg[1]] = $reg[2];
	}
	return $retar;
}


function startCapture()
{
	ob_start();
}

function endCapture()
{
	return ob_get_clean();
}

function searchTag($tags,$str,&$out)
{
	$tag = "";
	if(is_array($tags)) $tag = "(?:".implode("|",$tags).")";
	else $tag = $tags;
	if(preg_match("/<(".$tag.")/",$str,$reg,PREG_OFFSET_CAPTURE))
	{
		$out = $reg[1][0]; 
		return $reg[0][1];
	}
	return false;
}

function textCut($str,$cnt,$tags=array("ul","div","span","form","strong","em","i"))
{
	if($cnt >= strlen($str))return $str;
	$start = 0;
	do	
	{
		//echo "<hr>".htmlspecialchars(substr($str,$start))."<br/>";
		if(!preg_match("/<(".implode("|",$tags).")[^>]*>(?:[^<]|(?R)|<(?!\/\\1))*<\/\\1\s*>/",substr($str,$start),$reg,PREG_OFFSET_CAPTURE))
		{
		//	echo "BREAK1<br/>";
			break;
		}
		
		if($reg[0][1] > $cnt)
		{
		//	echo "BREAK1<br/>";
			break;
		}
		if($reg[0][1]+strlen($reg[0][0]) > $cnt)
		{
		//	echo $reg[0][1]."<br/>";
			return rtrim(substr($str,0,$reg[0][1]));
		}	
	}while(($start = $reg[0][1]+strlen($reg[0][0])) < $cnt);
		
	for($a=$cnt;$a>=0;$a--)
	{
		if($str[$a] == ' ') return rtrim(substr($str,0,$a));
		if($str[$a] == '>') return rtrim(substr($str,0,$a+1));
	}
	return "";
}

function toSimpleText($str)
{
	$search  = array('á', 'é', 'í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű','Á', 'É', 'Í', 'Ó', 'Ö', 'Ő', 'Ú', 'Ü', 'Ű');
	$replace = array('a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u','a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u');
	return str_replace($search,$replace,$str);	
}

?>
