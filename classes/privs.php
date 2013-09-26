<?
define("PRIVS_MESSAGE_PRIV_SET_OK",1);
define("PRIVS_MESSAGE_PRIV_SET_FAIL",2);

define("PRIVS_TEMPLATE_MAIN",0);
define("PRIVS_TEMPLATE_USER",1);
define("PRIVS_TEMPLATE_CHECK",2);
define("PRIVS_TEMPLATE_CELL",3);

class privs extends forefather
{
	var $text_message;
	var $default_user_id;
	var $privs_names;
	
	function privs($privs_names)
	{
		$this->privs_names=$privs_names;
		
		if (!(count($privs_names))) report_error("privs::privs üres a jogok nevét tartalmazó tömb");
		if(count($privs_names) > 31) report_error("privs::privs több mint 31 jognév lett átadva");
		
		$this->message_text[PRIVS_MESSAGE_PRIV_SET_OK] = "A jogosultság beállítása sikeres.";
		$this->message_text[PRIVS_MESSAGE_PRIV_SET_FAI] = "A jogosultság beállítása sikertelen.";
		
		$this->templates[PRIVS_TEMPLATE_MAIN] = new template('
			<form action="" method="post">
				<table border=2>
				<tr>
					<td rowspan="2" align="center">User id</td>
					<td colspan="{privs_count}" align="center">Privileges</td>
				</tr>
				<tr>
					</td>{cells}</tr>{body}
				</table>
				<input type="submit" name="save" value="Mentés" />
				<input type="submit" name="cancel" value="Mégse" />
			</form>');
		$this->templates[PRIVS_TEMPLATE_USER] = new template('
					<tr>
						<td align="center">
							{user_id}
							<input type="hidden" name="reference_text_{user_id}" />
						</td>
						{checks}
					</tr>');	
					
		$this->templates[PRIVS_TEMPLATE_CHECK] = new template('
		<td align="center">
			<input type="checkbox"[if {checked}] checked="true"[/if] name="priv_check_{user_id}_{count}" />
		</td>');	
		
		$this->templates[PRIVS_TEMPLATE_CELL] = new template('<td align="center">{name}</td>');
	}
	
	function install()
	{
			mysql_query("CREATE TABLE IF NOT EXISTS `" . $this->prefix . "privs` 
					(`privs_user_id` INT UNSIGNED NOT NULL ,
					 `privs_priv` INT UNSIGNED NOT NULL ,
					  PRIMARY KEY ( `privs_user_id` )
					  )");
	}

	function setUser($user_id)
	{
		$this->default_user_id = $user_id;
	}

	function isHavePriv($user_id, $priv_id=-1)
	{	
		if($priv_id==-1)
		{
			$priv_id=$user_id-1;
			$user_id = $this->default_user_id;
		}
		else $priv_id++;
		if (is_numeric($priv_id) && is_numeric($user_id))
		{
			

			$result=selectOne("SELECT `privs_priv` FROM `" . $this->prefix . "privs` WHERE `privs_user_id`=" . $user_id);
				
			if (isset($result['privs_priv'])) return ((($result['privs_priv']>>$priv_id) & 1)?true:false);
			return false;			
		}
		report_error("privs::isHavePriv nem számot kapott");
		return false;
	}	

	function removePriv($user_id, $priv_id = -1)
	{	
		if($priv_id==-1)
		{
			$priv_id=$user_id-1;
			$user_id = $this->default_user_id;
		}
		else $priv_id++;
		
		if (is_numeric($priv_id) && is_numeric($user_id))
		{
			$result=selectOne("SELECT `privs_priv` FROM `" . $this->prefix . "privs` WHERE `privs_user_id`=" . $user_id);

			if (isset($result['privs_priv']))return $this->setPrivs($user_id, $result['privs_priv'] &  ~(1<<$priv_id));
			return false;
		}
		report_error("privs::removePriv nem számot kapott");
		return false;
	}

	function addPriv($user_id, $priv_id = -1)
	{	
		if($priv_id==-1)
		{
			$priv_id=$user_id-1;
			$user_id = $this->default_user_id;
		}
		else $priv_id++;
	
		if (is_numeric($priv_id) && is_numeric($user_id))
		{
			$result=selectOne("SELECT `privs_priv` FROM `" . $this->prefix . "privs` WHERE `privs_user_id`=" . $user_id);
		
			if (isset($result['privs_priv'])) $mask = (1<<$priv_id) | $result['privs_priv'];
			else $mask = (1<<$priv_id);
		
			return $this->setPrivs($user_id, $mask);
		}
		report_error("privs::addPriv nem számot kapott");
		return false;
	}

	function setPrivs($user_id, $privs = -1)
	{	
		if($priv_id==-1)
		{
			$priv_id=$user_id-1;
			$user_id = $this->default_user_id;
		}
		else $priv_id++;
	
		if (is_numeric($privs) && is_numeric($user_id))
		{
			if(selectMezo("SELECT `privs_user_id` FROM `" . $this->prefix . "privs` WHERE `privs_user_id`=" . $user_id,"privs_user_id"))
			{
				if (mysql_query("UPDATE `" . $this->prefix . "privs` SET `privs_priv`=" . $privs . " WHERE `privs_user_id`=" . $user_id)) return true;
				report_error("privs::setPrivs mySQL 'UPDATE' sikertelen");
			}
			else 
			{
				if (mysql_query("INSERT INTO `" . $this->prefix . "privs` VALUES (" . $user_id . ", " . $privs . ")")) return true;
				report_error("privs::setPrivs mySQL 'INSERT' sikertelen");
			}
			/*
			logToFile("IF (EXISTS (SELECT `privs_user_id` FROM `" . $this->prefix . "privs` WHERE `privs_user_id`=" . $user_id."))
			BEGIN
			  UPDATE `" . $this->prefix . "privs` SET `privs_priv`=" . $privs . " WHERE `privs_user_id`=" . $user_id."
			END
			ELSE
			BEGIN
			 	INSERT INTO `" . $this->prefix . "privs` VALUES (" . $user_id . ", " . $privs . ")
			END");
			mysql_query("IF (EXISTS (SELECT `privs_user_id` FROM `" . $this->prefix . "privs` WHERE `privs_user_id`=" . $user_id."))
			BEGIN
			  UPDATE `" . $this->prefix . "privs` SET `privs_priv`=" . $privs . " WHERE `privs_user_id`=" . $user_id."
			END
			ELSE
			BEGIN
			 	INSERT INTO `" . $this->prefix . "privs` VALUES (" . $user_id . ", " . $privs . ")
			END");*/
			
			return false;
		}
		report_error("privs::setPrivs nem számot kapott");
		return false;
	}
	
	function process()
	{
		if (isset($_POST['save']))
		{			foreach($_POST as $key => $variable)
			{
				if (preg_match("/^reference_text_([0-9]+)$/",$key,$pos))
				{
					if(!isset($prives[$pos[1]]))$prives[$pos[1]] = 0;
				}
				if (preg_match("/^priv_check_([0-9]+)_([0-9]+)$/",$key,$pos))
				{					
					$prives[$pos[1]]+= (1 << $pos[2]);
				}
			}
			foreach ($prives as $key => $local)
			{
				if(!$this->setPrivs($key, $local))$this->message(PRIVS_MESSAGE_PRIV_SET_FAIL);
			}
			$this->message(PRIVS_MESSAGE_PRIV_SET_OK);
		}
	}

	function show()
	{
		$lapozo = new lapozo($this->prefix . "privs","","Hudi_lámaaaa:D",10);	
		$body="";
		$cells="";
		for ($i=0; $i<count($this->privs_names); $i++)
		{
			$cells .= $this->templates[PRIVS_TEMPLATE_CELL]->getTrans(array("name"=>$this->privs_names[$i]));
		}
		
		foreach (doSelect("SELECT * FROM `" . $this->prefix . "privs` ORDER BY privs_user_id " . $lapozo->getLimit()) as $row)
		{			
			$inside="";
			for ($i=0; $i<count($this->privs_names); $i++)
			{				
				$inside .= ($this->templates[PRIVS_TEMPLATE_CHECK]->getTrans(array("checked"=>(($row['privs_priv'] >> $i) & 1), "user_id"=>$row['privs_user_id'], "count"=>$i)));
			}
			$body .= ($this->templates[PRIVS_TEMPLATE_USER]->getTrans(array("user_id"=>($row['privs_user_id']), "checks"=>$inside)));
		}
		echo ($this->templates[PRIVS_TEMPLATE_MAIN]->getTrans(array("cells"=>$cells, "body"=>$body, "privs_count"=>count($this->privs_names))));
		$lapozo->echoButtons();
	}
}
?>
