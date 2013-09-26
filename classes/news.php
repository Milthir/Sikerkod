<?
define("NEWS_TEMPLATE_NEWS",1);
define("NEWS_TEMPLATE_NEWS_MOD",2);
define("NEWS_TEMPLATE_ADD_NEWS",3);


define("NEWS_MESSAGE_ADD_OK",1);
define("NEWS_MESSAGE_ADD_FAIL",2);
define("NEWS_MESSAGE_DEL_OK",3);
define("NEWS_MESSAGE_DEL_FAIL",4);
define("NEWS_MESSAGE_MOD_OK",5);
define("NEWS_MESSAGE_MOD_FAIL",6);
define("NEWS_MESSAGE_PUB_OK",7);
define("NEWS_MESSAGE_PUB_FAIL",8);
define("NEWS_MESSAGE_UNPUB_OK",9);
define("NEWS_MESSAGE_UNPUB_FAIL",10);

class news extends forefather
{
	var $admin_mode;
	var $mod_get;

	function news($admin_mode=false,$mod_get="modnews",$set_def_templates=true)
	{
		$this->admin_mode = $admin_mode;
		$this->mod_get =  $mod_get;
		
		if($set_def_templates)
		{
			$this->templates[NEWS_TEMPLATE_NEWS] = new template('
			<div style="border:1px dashed;margin-bottom:10px;[if !{news_public}]background:silver;[/if]">
				<div style="height:25px;background:#aaaaaa;width:100%;">
					[if {under_mod}]<form action="" method="post">{mod_hidden_zone}<input type="text" name="{mod_title_input_name}" value="{news_title}" /> 
					[else]{news_title}[/if]
					({news_time:Y-m-d H:i.s})
				</div>
				[if {under_mod}]
					<textarea style="width:300px;height:200px;" name="{mod_body_input_name}">{news_body}</textarea><br />
					<input type="submit" value="Módosítás" />
					</form>
				[else]
					{nl2br("{news_body}")}
				[/if]
				[if {admin_mode}]
					<form action="" method="post">{pub_hidden_zone}
					<input type="submit" name="[if {news_public}]unpub[else]pub[/if]" value="[if {news_public}]Publikálás visszavonása[else]Publikálás[/if]" />
					</form>
					<br /><a href="{mod_get}">Módosítás</a>
					<form action="" method="post">
						{del_hidden_zone}
						<input type="checkbox" onclick="document.getElementById(\'delsubmitbutt{news_id}\').disabled=!this.checked" />
						<input type="submit" id="delsubmitbutt{news_id}" name="delsubmitbutt{news_id}" disabled="disabled" value="Törlés" />
					</form>
				[/if]
			</div>');
				
			$this->templates[NEWS_TEMPLATE_ADD_NEWS] = new template('
			<form action="" method="post">
				<fieldset>
					<legend>Új hír hozzáadása</legend>
					{hidden_zone}
					Cím:<input type="text" name="{input_title_name}"><br />
					<textarea style="width:300px;height:150px;" name="{textarea_name}"></textarea><br />
					<input type="submit" value="Mehet" />
				</fieldset>
			</form>');
		}
		
		$this->message_text[NEWS_MESSAGE_ADD_OK] = "A hír hozzáadása sikertelen.";
		$this->message_text[NEWS_MESSAGE_ADD_FAIL] = "A hír hozzáadása sikeres.";
		$this->message_text[NEWS_MESSAGE_DEL_OK] = "A hír törlése sikertelen.";
		$this->message_text[NEWS_MESSAGE_DEL_FAIL] = "A hír törlése sikeres.";
		$this->message_text[NEWS_MESSAGE_MOD_OK] = "A hír módosítása sikeres.";
		$this->message_text[NEWS_MESSAGE_MOD_FAIL] = "A hír módosítása sikertelen.";
		$this->message_text[NEWS_MESSAGE_PUB_OK] = "A hír publikálása sikeres.";
		$this->message_text[NEWS_MESSAGE_PUB_FAIL] = "A hír publikálása sikertelen.";
		$this->message_text[NEWS_MESSAGE_UNPUB_OK] = "A hír publikálásának visszavonása sikeres.";
		$this->message_text[NEWS_MESSAGE_UNPUB_FAIL] = "A hír  publikálásának visszavonása sikertelen.";
		
	}

	function process()
	{
		switch(getPOST('action','a'))
		{
			case 'addnews':
				if(isInPOST(array("title","body")))
				{
					if($this->addNews($_POST['title'],$_POST['body'])) $this->message(NEWS_MESSAGE_ADD_OK);
					else $this->message(NEWS_MESSAGE_ADD_FAIL);
				}
				break;
			case 'modnews':
				if(isInPOST(array("title","body")))
				{
					if($this->modNews($_POST['newsid'],$_POST['title'],$_POST['body']))$this->message(NEWS_MESSAGE_MOD_OK,delGET(array($this->mod_get)));
					else $this->message(NEWS_MESSAGE_MOD_FAIL,delGET(array($this->mod_get)));
				}
				break;
			case 'delnews':
				if(isset($_POST['newsid']))
				{
					if($this->delNews($_POST['newsid']))$this->message(NEWS_MESSAGE_DEL_OK);
					else $this->message(NEWS_MESSAGE_DEL_FAIL);
				}
				break;
			case 'pubnews':
				if(isset($_POST['newsid']))
				{
					if($this->pubNews($_POST['newsid'],isset($_POST['pub'])))$this->message(isset($_POST['pub'])?NEWS_MESSAGE_PUB_OK:NEWS_MESSAGE_UNPUB_OK);
					else $this->message(isset($_POST['pub'])?NEWS_MESSAGE_PUB_FAIL:NEWS_MESSAGE_UNPUB_FAIL);
				}
				break;
		}
	}


	function install()
	{
		mysql_query(	"CREATE TABLE `test`.`".$this->prefix."news` (".
						"`news_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,".
						"`news_title` TEXT NOT NULL ,".
						"`news_time` INT UNSIGNED NOT NULL ,".
						"`news_body` TEXT NOT NULL ,".
						"`news_public` BOOL NOT NULL DEFAULT '0');");
	}

	function show()
	{
		foreach(doSelect("SELECT * FROM `".$this->prefix."news` ".($this->admin_mode?"":"WHERE `news_public`<>0 ")."ORDER BY `news_time` DESC") as $val)
		{
		
			echo $this->templates[NEWS_TEMPLATE_NEWS]->getTrans(array_merge($val,array(
														"admin_mode"=>$this->admin_mode,
														"mod_get"=>addGET(array($this->mod_get=>$val['news_id'])),
														"under_mod"=>($this->admin_mode&&$_GET[$this->mod_get]==$val['news_id']),
														"del_hidden_zone"=>'<input type="hidden" name="action" value="delnews" /><input type="hidden" name="newsid" value="'.$val['news_id'].'" />',
														"mod_hidden_zone"=>'<input type="hidden" name="action" value="modnews" /><input type="hidden" name="newsid" value="'.$val['news_id'].'" />',
														"pub_hidden_zone"=>'<input type="hidden" name="action" value="pubnews" /><input type="hidden" name="newsid" value="'.$val['news_id'].'" />',
														"mod_title_input_name"=>"title",
														"mod_body_input_name"=>"body"
													)));
		}
		if($this->admin_mode)
		{
			echo $this->templates[NEWS_TEMPLATE_ADD_NEWS]->getTrans(array(
															"hidden_zone"=>'<input type="hidden" name="action" value="addnews" />',
															"input_title_name"=>"title",
															"textarea_name"=>"body"
														));
		}
	}







	function addNews($title,$body)
	{
		if(mysql_query("INSERT INTO `".$this->prefix."news`(`news_title`,`news_body`,`news_time`)".
						" VALUES('".specChars($title,"news::addNews")."','".specChars($body,"hirek::addNews")."',".time().")"))return true;
		report_error("news::addNews nemsikerült a hír feltöltése.");
		return false;
	}
	
	function delNews($id)
	{
		if(isNumber($id))
		{
			if(mysql_query("DELETE FROM `".$this->prefix."news` WHERE `news_id`=".$id))return true;
			report_error("news::delNews nemsikerült");
			return false;
		}
		report_error("news::delNews nem számot kapott");
		return false;
	}
	
	function modNews($id,$title,$body)
	{
		if(isNumber($id))
		{
			if(mysql_query("UPDATE `".$this->prefix."news` SET `news_title`='".specChars($title,"news::modNews")."',".
											"	`news_body`='".specChars($body,"news::modNews")."' ".
							"WHERE `news_id`=".$id))		return true;
			report_error("news::modNews nemsikerült");
			return false;
		}
		report_error("news::modNews nem számot kapott");
		return false;
	}
	
	function pubNews($id,$pub=true)
	{
		if(isNumber($id))
		{
			if(mysql_query("UPDATE `".$this->prefix."news` SET	`news_public`=".($pub?1:0)."".
							" WHERE `news_id`=".$id))	return true;
			report_error("news::pubNews sikertelen publikálás");
			return false;
		}
		report_error("news::pubNews nem számot kapott");
		return false;
	}
	

}
?>
