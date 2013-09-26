<?
abstract class forefather
{
	var $prefix;
	var $templates;
	var $loading_template_id;
	var $admin_mode=false;
	
	var $message_text;
	var $MessageFunc;
	
//	abstract function show();
	abstract function process();
	
	function getShow($a='',$b='',$c='',$d='')
	{
		ob_start();
			$this->show($a,$b,$c,$d);
			$temp =ob_get_contents();
		ob_end_clean();
		return $temp;
	}
	
	function instantStart($prefix,$message_func)
	{
		$this->setPrefix($prefix);
		$this->setMessageFunc($message_func);
		$this->process();
		$this->show();
	}
	
	
	function setAdminMode($set=true)
	{
		$this->admin_mode = $set;
	}
	
	function setPrefix($new_prefix)
	{
		$this->prefix = $new_prefix;
	}
	
	
	
	function setMessageFunc($func)
	{
		$this->MessageFunc = $func;
	}
	
	function message($message_id,$reload="")
	{
		if(trim($this->MessageFunc) != "")
			return eval("return ".$this->MessageFunc."('".$this->message_text[$message_id]."','".$reload."',".$message_id.");");
		return false;
	}
	
	
	
	function setTemplate($template_id,$template)
	{
		$this->templates[$template_id] = $template;
	}

	function startTemplateRead($template_id)
	{
		if($this->loading_template_id)$this->endTemplateRead();
		$this->loading_template_id = $template_id;
		if($this->templates[$template_id]) $this->templates[$template_id]->startTemplateRead();
	}
	function endTemplateRead()
	{
		if($this->templates[$this->loading_template_id])$this->templates[$this->loading_template_id]->endTemplateRead();
		$this->loading_template_id = 0;
	}
}

?>
