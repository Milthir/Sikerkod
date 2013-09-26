<?
define("POLL_TEMPLATE_MAIN",1);
define("POLL_TEMPLATE_ELEM",2);
define("POLL_TEMPLATE_ADD_POLL",3);
define("POLL_TEMPLATE_ADD_OPTION",4);
define("POLL_TEMPLATE_POLL_ELEM",5);


define("POLL_PARAM_WITH_USER",1);


class poll extends forefather
{	
	var $akt_poll;
	var $params;
	var $admin_mode;
	
	var $poll_get;
	
	function poll($prefix="",$params=0,$admin_mode=false,$set_default_template=true)
	{
		$this->poll_get = "poll";
	
		$this->prefix = $prefix;
		$this->params = $params;
		$this->admin_mode = $admin_mode;
		
		
		if($set_default_template)
		{
		
			$this->templates[POLL_TEMPLATE_MAIN] = new template('[if {pollcnt}>0]<form method="post" action=""><select name="{polls_select_name}" size="1">{polls}</select></form>[/if][if {pollcnt}>0]<form method="post" action=""><fieldset><legend></legend>{hidden_zone}<select name="{select_name}" size="1">{options}</select><input type="submit" value="Szavazok" /></fieldset></form>[/if][if {admin_mode}]<a href="{mod_get}">Szerkesztés</a>[/if]');
			
			$this->templates[POLL_TEMPLATE_ELEM] = new template('<option name="{option_name}" value="{option_value}">{option_text}</option>');
			
			$this->templates[POLL_TEMPLATE_ADD_POLL] = new template('<form method="post" action=""><fieldset><legend>Új Szavazás létrehozása</legend>{hidden_zone}<label for="{poll_name_input_name}">Szavazás kérdése:</label><input type="text" name="{poll_name_input_name}" id="{poll_name_input_name}" /><input type="submit" value="Mehet" /></fieldset></form>');
			
			$this->templates[POLL_TEMPLATE_ADD_OPTION] = new template('<form method="post" action=""><fieldset><legend>Vállasztási lehetőség hozzáadása</legend>{hidden_zone}<label for="{option_name_input_name}">Válaszlehetőség:</label><input type="text" name="{option_name_input_name}" id="{option_name_input_name}" /><input type="submit" value="Mehet" /></fieldset></form>');
			
			$this->templates[POLL_TEMPLATE_POLL_ELEM] = new template('<option value="{poll_id}">{poll_name}</option>');		
		
		}

		$this->process();
	}
	
	function setPollGet($new)
	{
		$this->poll_get = $new;
	}
	
	function install()
	{
	/*
		a_poll
			-poll_id
			-poll_name
			-poll_create_time
	
		
		if($this->params&POLL_PARAM_WITH_USER)
		{
			a_poll_option
				-poll_option_parent
				-poll_option_id
				-poll_option_name
				
			a_poll_poll
				-poll_poll_user_id
				-poll_poll_option
				-poll_poll_time
		}
		else 
		{
			a_poll_option
				-poll_option_parent
				-poll_option_id
				-poll_option_name
				-poll_option_poll_cnt def 0
		}
	*/
		mysql_query("");
	}
	
	function process()
	{
		switch(getPOST('action','a'))
		{
			case 'poll':
				if(isInPOST(array("pollsel")))
				{
					$this->doPoll($_POST['pollsel']);
				}
				break;
			case 'addpoll':
				if(isInPOST(array("pollname")))
				{
					$this->addPoll($_POST['pollname']);
				}
				break;
		}
	}
	
	function show($params=0)
	{
		$options = "";
		$optioncnt=0;
		foreach(doSelect("SELECT * FROM `".$this->prefix."poll_option` WHERE `poll_option_parent`=".$this->akt_poll) as $option)
		{
			$optioncnt++;
			$options .= $this->templates[POLL_TEMPLATE_ELEM]->getTrans(array(
																"option_name"=>"poll",
																"option_value"=>$option['poll_option_id'],
																"option_text"=>$option['poll_option_name']
																));
		}
		
		$polls = "";
		$pollcnt = 0;
		foreach(doSelect("SELECT * FROM `".$this->prefix."poll` ORDER BY `poll_id` DESC") as $poll)
		{
			$pollcnt++;
			$polls .= $this->templates[POLL_TEMPLATE_POLL_ELEM]->getTrans(array(
																			"poll_id"=>$poll['poll_id'],
																			"poll_name"=>$poll['poll_name']
																		));
		}
		
		echo $this->templates[POLL_TEMPLATE_MAIN]->getTrans(array(
																"hidden_zone"=>'<input type="hidden" name="action" value="poll" />',
																"options"=>$options,
																"select_name"=>'pollsel',
																"admin_mode"=>$this->admin_mode?1:0,
																"mod_get"=>addGET(array("mod"=>"1")),
																"polls_hidden_zone"=>'<input type="hidden" name="action" value="pollssel" />',
																"polls_select_name"=>"pollssel",
																"polls"=>$polls,
																"option_cnt"=>$optioncnt,
																"poll_cnt"=>$pollcnt
																));
		if($this->admin_mode)
		{
			echo $this->templates[POLL_TEMPLATE_ADD_POLL]->getTrans(array(
																		"hidden_zone"=>'<input type="hidden" name="action" value="addpoll" />',
																		"poll_name_input_name"=>'pollname'
																		));	
		}
	}
	
	
	
	
	
	function addPoll($poll_name)
	{
		mysql_query("INSERT INTO `".$this->prefix."poll`(`poll_name`,`poll_create_time`) VALUES('".specChars($poll_name,"poll::addPoll")."',".time().")");
	}
	
	function modPoll($poll_id,$poll_name)
	{
		if(isNumber($poll_id))mysql_query("UPDATE `".$this->prefix."poll` SET `poll_name`='".specChars($poll_name,"poll::modPoll")."' WHERE `poll_id`=".$poll_id);
		else report_error("poll::modPoll");
	}
	
	
	
	function addPollOption($poll_id,$option_name)
	{
		if(isNumber($poll_id)) mysql_query("INSERT INTO `".$this->prefix."poll_option`(`poll_option_name`,`poll_option_parent`)".
												" VALUES('".specChars($option_name,"poll_addPollOption")."',".$poll_id.")");
		else report_error("poll::addPollOption nem számot kapott");
	}
	
	function modPollOption($poll_id,$option_id,$option_name)
	{
		if(isNumber($poll_id) && isNumber($option_id))	mysql_query("UPDATE `".$this->prefix."poll_option` SET `poll_option_name`='".specChars($option_name,"poll::modPollOption")."' WHERE `poll_option_id`=".$option_id." AND `poll_option_parent`=".$poll_id);
		else report_error("poll::modPollOption nem számot kapott");
	}
	
	
	
	function doPoll($choice,$user_id=0)
	{
		if(isNumber($choice) && $choice > 0)
		{
			if($this->params&POLL_PARAM_WITH_USER)
			{
				if($choice<= selectMezo("SELECT max(`poll_option_id`) AS `max` FROM `".$this->prefix."poll_option` WHERE `poll_option_parent`=".$this->akt_poll,"max"))
					mysql_query("INSERT INTO `".$this->prefix."poll_poll` VALUES(".$user_id.",".$choice.",".time().")");
			}
			else 
				mysql_query("UPDATE `".$this->prefix."poll_option` SET `poll_option_poll_cnt`=`poll_option_poll_cnt`+1 ".
								" WHERE `poll_option_parent`=".$this->akt_poll." AND `poll_option_id`=".$choice);
		}
		else report_error("poll::addPolls nem számot kapott");
	}
	
}
?>
