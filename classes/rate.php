<?
define("RATE_TEMPLATE_MAIN",1);
define("RATE_TEMPLATE_ELEM",2);

define("RATE_PARAM_WITH_USER",1);

define("RATE_SHOW_PARAM_ADMIN",1);


define("RATE_MESSAGE_RATE_OK",1);
class rate extends forefather
{	
	var $akt_poll;
	var $params;
	
	var $poll_get;
	
	var $lower_value;
	var $higher_value;
	
	var $akt_user;
	var $akt_target;

	var $message_text;
	
	function rate($akt_user,$akt_target,$set_default_template=true)
	{	
		$this->akt_user =$akt_user;
		$this->akt_target =$akt_target;
		
		$this->lower_value = 0;
		$this->higher_value =10;
		
		if($set_default_template)
		{
		
			$this->templates[RATE_TEMPLATE_MAIN] = new template('<form method="post" action="">{hidden_zone}<select name="{select_name}" size="1">{options}</select><input type="submit" value="Szavazok" /></form>');
			
			$this->templates[RATE_TEMPLATE_ELEM] = new template('<option value="{option_value}">{option_text}</option>');
			
		}

		$this->message_text[RATE_MESSAGE_RATE_OK] = "Az értékelés rögzítése sikeres.";
	}
	

	
	function setLowerValue($new_val)
	{
		$this->lower_value = $new_val;
	}
	function setHigherValue($new_val)
	{
		$this->higher_value = $new_val;
	}
	
	function setPollGet($new)
	{
		$this->poll_get = $new;
	}
	
	function install()
	{
		mysql_query("CREATE TABLE `".$this->prefix."rate` (
						`rate_target` INT UNSIGNED NOT NULL ,
						`rate_from` INT UNSIGNED NOT NULL ,
						`rate_value` TINYINT UNSIGNED NOT NULL ,
						`rate_time` INT UNSIGNED NOT NULL ,
						PRIMARY KEY ( `rate_target` , `rate_from` , `rate_time` )
						);");
	}
	
	function process()
	{
		switch(getPOST('action','a'))
		{
			case $this->prefix.'rate':
				if(isInPOST(array("rate")))
				{
					if($this->doRate($_POST['rate']))$this->message(RATE_MESSAGE_RATE_OK);
				}
				break;

		}
	}
	
	function show($params=0)
	{
		if(RATE_SHOW_PARAM_ADMIN&$params)
		{
			;
		}
		else
		{
			$options = "";
			for($a=$this->lower_value;$a<=$this->higher_value;$a++)
			{
				$options .= $this->templates[RATE_TEMPLATE_ELEM]->getTrans(array(
																	"option_value"=>$a,
																	"option_text"=>$a
																	));
			}
		
		
			echo $this->templates[RATE_TEMPLATE_MAIN]->getTrans(array(
																	"hidden_zone"=>'<input type="hidden" name="action" value="'.$this->prefix.'rate" />',
																	"options"=>$options,
																	"select_name"=>'rate'
																	));
		}
	}
	
	function getRankedList($limit)
	{
		return doSelect("SELECT `rate_target` FROM `".$this->prefix."rate` as tr1 WHERE rate_time=(SELECT max(rate_time) FROM `".$this->prefix."rate` WHERE `rate_from`=`tr1`.`rate_from`) AND `rate_target`=".$target_id." ORDER BY avg(rate_value)");
	}

	function getAvg($target_id)
	{
		if(isNumber($target_id))
			return selectMezo("SELECT avg(rate_value) as avg FROM `".$this->prefix."rate` as tr1 WHERE rate_time=(SELECT max(rate_time) FROM `".$this->prefix."rate` WHERE `rate_from`=`tr1`.`rate_from`) AND `rate_target`=".$target_id,"avg");
		else report_error("rate::getAvg nem számot kapott");
	}
	
	function doRate($rate)
	{
		if(isNumber($rate))
		{
			return mysql_query("INSERT INTO `".$this->prefix."rate`(`rate_target`,`rate_from`,`rate_value`,`rate_time`) VALUES(".$this->akt_target.",".$this->akt_user.",".$rate.",".time().")");
		}
		report_error("rate::doRate nem számot kapott");
		return false;
	}
	
}

?>
