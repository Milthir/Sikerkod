<?
function report_error($error_text)
{
	mysql_query("INSERT INTO `log`(`log_ip`,`log_get`,`log_post`,`log_text`,`log_session`,`log_time`) 
				VALUES(	'".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."',
						'".mysql_real_escape_string(listArray($_GET,false))."',
						'".mysql_real_escape_string(listArray($_POST,false))."',
						'".mysql_real_escape_string($error_text)."',
						'".mysql_real_escape_string(listArray($_SESSION,false))."',
						".time().")");
}

function installLogger()
{
	return mysql_query("CREATE TABLE IF NOT EXISTS `log` (
  `log_ip` text COLLATE utf8_hungarian_ci NOT NULL,
  `log_get` text COLLATE utf8_hungarian_ci NOT NULL,
  `log_post` text COLLATE utf8_hungarian_ci NOT NULL,
  `log_text` text COLLATE utf8_hungarian_ci NOT NULL,
  `log_session` text COLLATE utf8_hungarian_ci NOT NULL,
  `log_time` int(10) unsigned NOT NULL,
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci AUTO_INCREMENT=1 ;");
}

function showLog()
{
	if(isAction("log_delall"))
	{
		mysql_query("TRUNCATE TABLE `log`");
		reload();
	}
	if(isAction("log_del") && isNumber(getPOST('log_id','a')))
	{
		mysql_query("DELETE FROM `log` WHERE `log_id`=".$_POST['log_id']);
		reload();
	}
	echo '<form method="post" action="">
			<input type="hidden" name="action" value="log_delall" />
			<input type="submit" value="Összes log törlése" />
		</form>';
	
	foreach(doSelect("SELECT * FROM `log` ORDER BY `log_time` DESC") as $val)
	{
		echo '
			<div style="background:#eeeeee;margin-bottom:20px;border:1px dashed;">
				<div style="background:#bbbbbb;"><strong>'.$val['log_text'].'</strong>('.date("Y-m-d H:i.s",$val['log_time']).')</div>
				<div style="margin:10px;background:#999999">
					<strong>GET:</strong><br/>'.nl2br($val['log_get']).'
				</div>
				<div style="margin:10px;background:#999999">
					<strong>POST:</strong><br/>'.nl2br($val['log_post']).'
				</div>
				<div style="margin:10px;background:#999999">
					<strong>SESSION:</strong><br/>'.nl2br($val['log_session']).'
				</div>
				<div style="background:#999999">'.$val['log_ip'].'</div>
				<form method="post" action="">
					<input type="hidden" name="action" value="log_del" />
					<input type="hidden" name="log_id" value="'.$val['log_id'].'" />
					<input type="submit" value="Törlés" />
				</form>
			</div>';
	}
}


function analyzeText($text,$where_from)
{
	if(ereg('(INSERT|SELECT|UPDATE|DELETE|insert|select|update|delete)',$text))
	{
		report_error('gyanús elem "'.$text.'"('.$where_from.':#102)');
	}
}

function isNumber($num)
{
	if(is_array($num))
	{
		$ok = true;
		foreach($num as $val)$ok &=isNumber($val);
		return $ok;
	}
	else return preg_match("/^-?[0-9]+$/",$num);
}

function reportedIsNumber($num,$where_from)
{
	if(isNumber($num))return true;
	report_error($where_from." nem számot kapott [".$num."]");
	return false;
}

function safeNumber($num,$def=0)
{
	if(isNumber($num))
	{
		settype($num, "integer");
		return $num;
	}
	return $def;
}

function specChars($text,$where_from)
{
	analyzeText($text,$where_from);
	//echo htmlentities($text,ENT_QUOTES);
	//return htmlspecialchars($text,ENT_QUOTES);
	return str_replace('\'','&#39',$text);
}

function safeText2Echo($text)
{
	return str_replace(array("<",">"),array("&lt;","&gt;"),$text);
}

/*
function tagFilter($str,$ar)
{
	$
	return ;
}
*/


function wordFilter($str,$ar,$def)
{
	if(in_array($str,$ar)) return $str;
	else return $def;
}
?>
