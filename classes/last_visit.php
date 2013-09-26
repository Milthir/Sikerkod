<?
/*
konstruktor 2. paramétere a mezők számát adja meg
install fgv paraméterei a tipusokat adják meg SQL tipusként (TEXT, INTEGER...)
*/
class last_visit
{
	var $prefix;
	var $mezo_num;
	
	function last_visit($prefix,$mezo_num)
	{
		$this->prefix = $prefix;
		$this->mezo_num = $mezo_num;
	}
	
	function install()
	{
		$arg_num = func_num_args();
		$argsq = "";
		$argnameq = "";
		for($a=0;$a<$arg_num;$a++)
		{
			$argsq .= ($a?",":"")."`last_visit_arg".$a."` ".func_get_arg($a)."";
			$argnameq .= ($a?",":"")."`last_visit_arg".$a."`"
		}
		mysql_query("CREATE TABLE `".$this->prefix."last_visit`  (`last_visit_time` INTEGER UNSIGNED,PRIMARY KEY(".$argnameq.")) ");
	}
	
	function getLastVisit()
	{
		$arg_num = func_num_args();
		
		$sqlq = "SELECT `last_visit_time` FROM `".$this->prefix."last_visit` WHERE ";
		for($a=0;$a<$arg_num;$a++)
		{
			$sqlq .= ($a?" AND ":"")."`arg".$a."`='".specChars(func_get_arg($a),"last_visit::getLastVisit()")."'"
		}
		return selectMezo($sqlq,"last_visit_time");
	}
	
	function visited()
	{
		$arg_num = func_num_args();

		$where_ = "";
		$insert = "";
		for($a=0;$a<$arg_num;$a++)
		{
			$where .= ($a?" AND ":"")."`arg".$a."`='".specChars(func_get_arg($a),"last_visit::visited()")."'";
			$insert .= "'".specChars(func_get_arg($a),"last_visit::visited()")."'";
		}
		//mysql_query("DELETE FROM `".$this->prefix."last_visit` WHERE ".$where);
		//	mysql_query("UPDATE `".$this->prefix."last_visit` SET `last_visit_time`=".time()." WHERE ".$where);
		mysql_query(
			"IF (EXISTS (SELECT `last_visit_time` FROM `".$this->prefix."last_visit` WHERE ".implode(" AND ",$where)."))
			begin
			  UPDATE `".$this->prefix."last_visit` SET `last_visit_time`=".time()." WHERE ".implode(" AND ",$where)."
			end
			else
			begin
			  INSERT INTO `".$this->prefix."last_visit` VALUES(".implode(",",$where).",".time().")
			end");
	
	}
};

?>
