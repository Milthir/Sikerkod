<?
class template 
{
	var $text;
	var $classes;
	var $functions;
	var $trans_ar;
	
	
	function template($text='',$arg1='',$arg2='')
	{
		if(trim($text)== '')return;
		else if(file_exists($text) && $arg1 != '' && $arg2 != '')$this->loadFromFile($text,$arg1,$arg2);
		else $this->text = $text;

		if(is_array($arg1))$this->functions = $arg1;
		else $this->functions = array();
		
		if(is_array($arg2))$this->classes = $arg2;
		else $this->classes = array();			
	}

	function copy()
	{
		return new template($this->text,$this->functions,$this->classes);
	}
	
	function doTrans($ar)
	{
		$this->text = $this->getTrans($ar);
	}
	
	function parseControls($input)
	{
		$regex = '#\[(if|repeat) ([^]]+)\]((?:[^[]|\[(?!/?\1[^]]*\]|else\])|(?R))*)(?:\[else]((?:[^[]|\[(?!/?(?:if|repeat)[^]]*\])|(?R))*))?\[\/\1]#';
		if (is_array($input)) // 1-es művelet 2-es a művelet paramétere 3-as okés ág 4-es else ág
		{
			switch($input[1])
			{
				case 'if':
					$ret = @eval("return (".$input[2].")?1:0;");			
					if($ret === false)
					{
						report_error("template::rossz kifejezés:".listArray($input,false));
						return preg_replace_callback($regex, array(template,'parseControls'), $input[4]);
					}
					else if($ret) return preg_replace_callback($regex, array(template,'parseControls'), $input[3]);
					else return preg_replace_callback($regex, array(template,'parseControls'), $input[4]);
					break;
			
				case 'repeat':
					$ret = @eval("return ".$input[2].";");
					if($ret === false || !isNumber($ret))
					{
						report_error("template::rossz kifejezés:".print_r($input,true));
						return "";
					}
					else return preg_replace_callback($regex, array(template,'parseControls'), str_repeat($input[3],$ret));
					break;
			}
		}
		else return preg_replace_callback($regex, array(template,'parseControls'), $input);
	}
	
	function parseFunctions($input)
	{
		$regex = '#\{((?:[^{}]|(?R))+)\}#';
		if (is_array($input))
		{	
			if(preg_match("/^([^:]+)\:([^{}]+)$/",$input[1],$reg))
			{	
				if(isset($this->trans_ar[$reg[1]]))return date($reg[2],$this->trans_ar[$reg[1]]);
				else
				{
					$ret = preg_replace_callback($regex, array(&$this,'parseFunctions'), $reg[1]);
					if(isNumber($ret))return date($reg[2],$ret);
				}
			}		
			if(preg_match("/^([^({}]+)\(([^)]*)\)\$/",$input[1],$reg))
			{
				if($this->functions[$reg[1]])
				{
					$ret2 = @eval("return \$this->functions['".$reg[1]."'](".$reg[2].");");
					if(!($ret2 === false)) return $ret2;
				}
				else if(preg_match("/^([^{}-]+)->(.+)$/",$reg[1],$reg2))
				{
					if($this->classes[$reg2[1]])
					{
						$ret2 = @eval("return \$this->classes['".$reg2[1]."']->".$reg2[2]."(".preg_replace_callback($regex, array(&$this,'parseFunctions'), $reg[2]).");");
						if(!($ret2 === false)) return $ret2;
					}
				}
			}
			$input[1] = preg_replace_callback($regex, array(&$this,'parseFunctions'), $input[1]);
			$ret = @eval("\$template_temp=".$input[1].";return \$template_temp===false?0:\$template_temp;");
		
			if($ret === false || $input[1] === $ret)
			{
				return "{".$input[1]."}";
			}
			else 
			{
				return $ret;
			}
		}
		else return preg_replace_callback($regex, array(&$this,'parseFunctions'), $input);
	}
	
	function parseVars($input)
	{
	/*	echo htmlspecialchars($input[1])."<br />";*/
		$regex = '#\{((?:[^{}]|(?R))+)\}#';
		if (is_array($input))
		{
			if(array_key_exists($input[1],$this->trans_ar))
			{
				if(gettype($this->trans_ar[$input[1]]) == "boolean") return $this->trans_ar[$input[1]]?"true":"false";
				return $this->trans_ar[$input[1]];
			}
			return "{".preg_replace_callback($regex, array(&$this,'parseVars'),$input[1])."}";
		}
		else return preg_replace_callback($regex, array(&$this,'parseVars'), $input);
	}
	
	function parseVarsFunctions($input)
	{
	/*	echo htmlspecialchars($input[1])."<br />";
		*/$regex = '#\{((?:[^{}]|(?R))+)\}#';
		if (is_array($input))
		{
			if(array_key_exists($input[1],$this->trans_ar))
			{
				if(gettype($this->trans_ar[$input[1]]) == "boolean") return $this->trans_ar[$input[1]]?"true":"false";
				return $this->trans_ar[$input[1]];
			}
			if(preg_match("/^([^:]+)\:([^{}]+)$/",$input[1],$reg))
			{	
				if(isset($this->trans_ar[$reg[1]]))return date($reg[2],$this->trans_ar[$reg[1]]);
				else
				{
					$ret = preg_replace_callback($regex, array(&$this,'parseVarsFunctions'), $reg[1]);
					if(isNumber($ret))return date($reg[2],$ret);
				}
			}
			//echo htmlspecialchars($input[1])."##<br/>";
			if(preg_match("/^([^(]+)\((.*)(?=\))\)$/s",$input[1],$reg))
			{
				//echo htmlspecialchars($reg[0])."#".htmlspecialchars($reg[2])."<br/>";
				if($this->functions[$reg[1]])
				{
					//$ret2 = @eval("return \$this->functions['".$reg[1]."'](".$reg[2].");");
					$ret2 = @eval("return \$this->functions['".$reg[1]."'](".preg_replace_callback($regex, array(&$this,'parseVarsFunctions'), $reg[2]).");");
					
				//	echo "<strong>".$ret2."</strong><br/>";
					if(!($ret2 === false)) return $ret2;
				}
				else if(preg_match("/^([^{}-]+)->(.+)$/",$reg[1],$reg2))
				{
/*					echo "<hr>";
					listArray(array_keys($this->classes));
					echo "<hr>";
	*/				if($this->classes[$reg2[1]])
					{
					//echo @eval("return \$this->classes['".$reg2[1]."']->prefix;")."<br/>";
						$ret2 = @eval("return \$this->classes['".$reg2[1]."']->".$reg2[2]."(".preg_replace_callback($regex, array(&$this,'parseVarsFunctions'), $reg[2]).");");				
				//		echo htmlspecialchars($reg2[2])."<br/><strong>".$ret2.'</strong><br/>';
						
						if(!($ret2 === false)) return $ret2;
					}
				}
			}
			//echo htmlspecialchars($input[1])."#-#<br/>";
		//	echo "########".$input[1]."<hr>";
			$input[1] = preg_replace_callback($regex, array(&$this,'parseVarsFunctions'), $input[1]);
		//	echo "########".$input[1];

			$ret = @eval("\$template_temp=".$input[1].";return \$template_temp===false?0:\$template_temp;");
		/*	echo "\$template_temp=".$input[1].";return \$template_temp===false?0:\$template_temp;";
			var_dump($ret);
			echo "<br>";			
			*/
			if($ret === false || $input[1] === $ret)
			{
				return "{".$input[1]."}";
			}
			else 
			{
				return $ret;
			}
		}
		else return preg_replace_callback($regex, array(&$this,'parseVarsFunctions'), $input);
	}

	function getTrans($ar)
	{
		$this->trans_ar = $ar;
		$temp = $this->text;
		
		$temp = $this->parseVarsFunctions($temp);
		$temp = template::parseControls($temp);

		return $temp;
	}
	
	function doVarTrans($ar)
	{
		$this->trans_ar = $ar;
		$this->text = $this->parseVars($this->text);
	}
	function getVarTrans($ar)
	{
		$this->trans_ar = $ar;
		return  $this->parseVars($this->text);
	}

	function getText()
	{
		return $this->text;
	}
	function startTemplateRead()
	{
		ob_start();
	}
	function endTemplateRead()
	{
		$this->text = ob_flush();
	}
	function loadFromFile($filename,$start='',$end='')
	{
		if($start != '' && $end != '')
		{
			$temp = file_get_contents($filename);
			$this->text = substr($temp,strpos($temp,$start)+strlen($start),strpos($temp,$end)-strpos($temp,$start)-strlen($start));
		}
		else $this->text = file_get_contents($filename); 
	}
}
?>
