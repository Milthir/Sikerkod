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
	
	function doTrans($ar)
	{
		$this->text = $this->getTrans($ar);
	}
/*	function expProc(&$szov,$start=0) // PRIVÁT FUNKCIÓ
	{
		
		if(!preg_match("/\[if ([^\]]+)\]/",$szov,$startif,PREG_OFFSET_CAPTURE,$start))return false;
		
		preg_match("/\[if [^\]]+\]/",$szov,$nextif,PREG_OFFSET_CAPTURE,$start+strlen($startif[0][0]));
		preg_match("/\[\/if\]/",$szov,$endif,PREG_OFFSET_CAPTURE,$start);

		if($nextif[0][1] && $nextif[0][1] < $endif[0][1])
		{
			$this->expProc($szov,$nextif[0][1]);
			$this->expProc($szov,$start);
			return true;
		}
		preg_match("/\[else\]/",$szov,$else,PREG_OFFSET_CAPTURE,$start);

		if(eval("return ".$startif[1][0].";"))
		{
			$szov = substr_replace($szov,'',$endif[0][1],strlen($endif[0][0]));//Töröljük a [/if] -et
			
			if($else[0][1] && $else[0][1] < $endif[0][1])//ha van töröljük az [else]-t és az else ágat
			{
				$szov = substr_replace($szov,'',$else[0][1],$endif[0][1]-$else[0][1]);
			}
			$szov = substr_replace($szov,'',$startif[0][1],strlen($startif[0][0]));//Töröljük az [if ...] -t

		}
		else 
		{	
			if($else[0][1] && $else[0][1] < $endif[0][1])
			{
				$szov = substr_replace($szov,'',$endif[0][1],strlen($endif[0][0]));
				$szov = substr_replace($szov,'',$startif[0][1],$else[0][1]-$startif[0][1]+strlen($else[0][0]));
			}
			else 
			{
				$szov = substr_replace($szov,'',$startif[0][1],$endif[0][1]-$startif[0][1]+strlen($endif[0][0]));
			}
		}
		return true;
	}
*/
	
	
	function parse($input)
	{
		//$regex = '#\[if ([^]]+)\]((?:[^[]|\[(?!/?if[^]]*\])|(?R))+)\[\/if]#';
	//	$regex = '#\[(if|repeat) ([^]]+)\]((?:[^[]|\[(?!/?if[^]]*\]|else\])|(?R))+)(?:\[else]((?:[^[]|\[(?!/?if[^]]*\])|(?R))+))?\[\/\1]#';
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
						return preg_replace_callback($regex, array(template,'parse'), $input[4]);
					}
					else if($ret) return preg_replace_callback($regex, array(template,'parse'), $input[3]);
					else return preg_replace_callback($regex, array(template,'parse'), $input[4]);
					break;
			
				case 'repeat':
					$ret = @eval("return ".$input[2].";");
					if($ret === false || !isNumber($ret))
					{
						report_error("template::rossz kifejezés:".print_r($input,true));
						return "";
					}
					else return preg_replace_callback($regex, array(template,'parse'), str_repeat($input[3],$ret));
					break;
			}
		}
		else return preg_replace_callback($regex, array(template,'parse'), $input);
	}
	
	function parseVars($input)
	//	echo "<hr>";
	{
		//print_r($input);
		$regex = '#\{((?:[^{}]|(?R))+)\}#';
		if (is_array($input))
		{
			if(array_key_exists($input[1],$this->trans_ar))
			{
				if(gettype($this->trans_ar[$input[1]]) == "boolean") return $this->trans_ar[$input[1]]?"true":"false";
				return $this->trans_ar[$input[1]];
			}
			if(preg_match("/^([^:]+)\:([^{}]+)$/",$input[1],$reg))
			{	
				if($this->trans_ar[$reg[1]])return date($reg[2],$this->trans_ar[$reg[1]]);
				else
				{
					$ret = preg_replace_callback($regex, array(&$this,'parseVars'), $reg[1]);
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
						$ret2 = @eval("return \$this->classes['".$reg2[1]."']->".$reg2[2]."(".preg_replace_callback($regex, array(&$this,'parseVars'), $reg[2]).");");
						if(!($ret2 === false)) return $ret2;
					}
				}
			}
			$input[1] = preg_replace_callback($regex, array(&$this,'parseVars'), $input[1]);
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
		else return preg_replace_callback($regex, array(&$this,'parseVars'), $input);
	}
	

	function getTrans($ar)
	{
		$this->trans_ar = $ar;
		$temp = $this->text;
		$temp = $this->parseVars($temp);
		$temp = template::parse($temp);
		//$temp = template::parseRepeat($temp);

		
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
