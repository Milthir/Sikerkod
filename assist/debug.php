<?
function listArray($ar,$echo=true)
{
	$str = "";
	if(is_array($ar))
	{
		$str .= '<ul>';
		foreach($ar as $key => $val)
		{
			$str .= '<li>'.$key.'=&gt;</li>';
			$str .=listArray($val,false);
		}
		$str .= '</ul>';
	}
	else if(is_string($ar))
	{
		$str .= htmlspecialchars($ar);
	}
	else if(is_scalar($ar))
	{
		$str .= $ar;
	}
	else
	{
		$str .= var_export($ar,true);
	}
	if($echo)echo $str;
	return $str;
}

function echoDebugZone()
{
	?>
	<div onclick="document.getElementById('debug_div').style.display='block';">DEBUG</div><div id="debug_div" style="display:none;">
		<hr>TEMPLATE:<br />
		<?		
		if(is_array($GLOBALS['template_class_errors']))
		{
			foreach($GLOBALS['template_class_errors'] as $val)
			{
				echo htmlspecialchars($val);
			}
		}
		?>
		
		<hr>MYSQL:<br />
		<?=mysql_error();?>
		<hr>POST:<br />
		<?listArray($_POST);?>
		<?	
		if(isset($_SESSION))
		{
			?>
			<hr>SESSION:<br />
			<?
			listArray($_SESSION);
		}
		?>
	
	</div>
	<?
}

function getMicrotime()
{
	$starttime = explode(' ', microtime());
	return $starttime[1] + $starttime[0];
}

function logToFile($text)
{
	$file = fopen("log.txt","at");
	fwrite($file,date("Y-M-d H:i.s")." - ".$text." \n\n");
	
	fclose($file);
	//file_put_contents("log.txt",date("Y-M-d H:i.s")." - ".$text."",FILE_APPEND );
}
function log2File($text)
{
	logToFile($text);
}

?>
