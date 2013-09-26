<?
define("LOGINER_ENTER_BY_NAME",1);
define("LOGINER_ENTER_BY_EMAIL",2);
define("LOGINER_ENTER_BY_NAME_OR_EMAIL",3);



define("LOGINER_PARAM_REG",1);
define("LOGINER_PARAM_LOSTPASS",2);
define("LOGINER_PARAM_ACTIVATION",4);
define("LOGINER_PARAM_NEED_EMAIL",8);


define("LOGINER_TEMPLATE_LOGIN_FORM",1);
define("LOGINER_TEMPLATE_REG_FORM",2);
define("LOGINER_TEMPLATE_HELLO",3);

define("LOGINER_MESSAGE_LOGIN_BAD",1);
define("LOGINER_MESSAGE_LOGIN_OK",2);
define("LOGINER_MESSAGE_DIFFERENT_PASSES",3);
define("LOGINER_MESSAGE_REG_OK",4);
define("LOGINER_MESSAGE_REG_USERNAME_EXISTS",5);
define("LOGINER_MESSAGE_NEED_EMAIL",6);


class user 
{
	var $data;
	
}

class loginer extends forefather
{	var $names;	var $params;

	var $akt_user_data;
	var $field_plus;
	
	//$field_plus key=név $val=típus;
	function loginer(&$user_data,$params=0,$field_plus=array(),$set_def_templates=true)
	{
		$this->params = $params;
		if(is_array($field_plus))$this->field_plus = $field_plus;
 		else $this->field_plus = array();

		$this->names['table'] = "user";
		$this->names['id'] = "user_id";
		$this->names['username'] = "user_name";
		$this->names['pass'] = "user_pass";
		$this->names['ip'] = "user_ip";
		$this->names['email'] = "user_email";
		$this->names['identifier'] = "user_identifier_str";
		$this->names['login_time'] = "user_login_time";
	
		$this->message_text[LOGINER_MESSAGE_LOGIN_BAD] = "Rossz felhasználónév vagy jelszó.";
		$this->message_text[LOGINER_MESSAGE_LOGIN_OK] = "Sikeres bejelentkezés.";
		$this->message_text[LOGINER_MESSAGE_DIFFERENT_PASSES] = "A két jelszó nem egyezik meg.";
		$this->message_text[LOGINER_MESSAGE_REG_OK] = "A regisztráció sikeres.";
		$this->message_text[LOGINER_MESSAGE_REG_USERNAME_EXISTS] = "A megadott usernév már foglalt.";
		$this->message_text[LOGINER_MESSAGE_NEED_EMAIL] = "E-mail címet kötelező megadni.";


		$this->akt_user_data = &$user_data;
		
		if(isNumber($this->getID()))
		{
			if(selectMezo("SELECT `".$this->names['identifier']."` FROM `".$this->prefix.$this->names['table']."` 
															WHERE `".$this->names['id']."`=".$this->getID(),$this->names['identifier'])  
					!= $this->akt_user_data[$this->names['identifier']])
			{
				$this->akt_user_data = 0;
			}
		}
		else $this->akt_user_data = 0;

		if($set_def_templates)
		{
			$this->templates[LOGINER_TEMPLATE_LOGIN_FORM] = new template('
					<form method="post" action="{action_url}">
						<div>{hidden_zone}
							<label for="{name_input_name}">Felhasználónév:</label>
								<input type="text" id="{name_input_name}" name="{name_input_name}" alt="Felhasználónév" />
							<label for="{pass_input_name}">Jelszó:</label>
								<input type="password" id="{pass_input_name}" name="{pass_input_name}" alt="Jelszó" /><br />
							<input type="submit" value="Belépés" />
						[if {can_get_lost_pass}]<br /><a href="{lostpass_link}">Elfelejtetted a jelszavad?</a>[/if]
						[if {can_reg}]<br /><a href="{reg_link}">Regisztráció</a>[/if]
						</div>
					</form>');

			$this->templates[LOGINER_TEMPLATE_REG_FORM] = new template('
				<form method="post" action="">
					<div>
						{hidden_zone}
						<label for="{user_name_input_name}">Név:</label>
							<input type="text" name="{user_name_input_name}" id="{user_name_input_name}" value="{user_name_input_value}" />
						<label for="{user_pass_input_name}">Jelszó:</label>
							<input type="password" name="{user_pass_input_name}" id="{user_pass_input_name}" value="{user_pass_input_value}" /><br />
						<label for="{user_email_input_name}">E-mail cím:</label>
							<input type="text" name="{user_email_input_name}" id="{user_email_input_name}" value="{user_email_input_value}" />
						<label for="{user_pass2_input_name}">Jelszó mégegyszer:</label>
							<input type="password" name="{user_pass2_input_name}" id="{user_pass2_input_name}" value="{user_pass_input_value}" /><br />
						<input type="submit" value="Regisztrálok" />
						<p>
							<a href="{cancel_login_get}">Mégsem szeretnék regisztrálni</a>
						</p>
					</div>
				</form>');

			$this->templates[LOGINER_TEMPLATE_HELLO] = new template('Szervusz {user_name}<br /><a href="{logout_get}">Kijelentkezés</a>');
		}
	}

	function install()
	{
		$query = "CREATE TABLE `".$this->names['table']."` (
								`".$this->names['id']."` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`".$this->names['username']."` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
								`".$this->names['pass']."` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
								`".$this->names['email']."` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
								`".$this->names['ip']."` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
								`".$this->names['identifier']."` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
								`".$this->names['login_time']."` INT UNSIGNED NOT NULL
								
								";
		
		foreach($this->field_plus as $name => $type)
		{
			$query .= ", `".$name."` ".$type;
		}		
		$query.=");";
		
		mysql_query($query);
	}


	function isLogined()
	{
		return $this->akt_user_data?true:false;
	}

	function getName(){return $this->akt_user_data[$this->names['username']];}
	function getId(){return $this->akt_user_data[$this->names['id']];}
//	function getID(){return $this->getId();}
	function getEmail(){return $this->akt_user_data[$this->names['email']];}
	function getField($arg_name){return $this->akt_user_data[$arg_name];}	

	function setField($arg_name,$set_to)
	{
		mysql_query("UPDATE ".$this->names['table']." SET `".specChars($arg_name,"loginer::setField")."`='".specChars($set_to,"loginer::setField")."' WHERE `".$this->names['id']."`=".$this->getId());
		$this->akt_user_data[$arg_name]=$set_to;
	}

	function setTableName($new){$this->names['table'] =$new;}
	function setIdFieldName($new){$this->names['id'] =$new;}
	function setUsernameFieldName($new){$this->names['username'] =$new;}
	function setPassFieldName($new){$this->names['pass'] =$new;}
	function setIpFieldName($new){$this->names['ip'] =$new;}
	function setEmailFieldName($new){$this->names['email'] =$new;}
	function setIdentifierFieldName($new){$this->names['identifier'] =$new;}
	function setLoginTimeFieldName($new){$this->names['login_time'] =$new;}



	function getUserNameById($id)
	{
		if(isNumber($id))return selectMezo("SELECT `".$this->names['username']."` FROM ".$this->names['table']." WHERE `".$this->names['id']."`=".$id,$this->names['username']);
		report_error("loginer::getUserNameById nem számot kapott");
		return false;
	}	


	function process()
	{
		if(getGET('a','a') == "logout")
		{
			$this->akt_user_data = 0;
		}

		switch(getPOST('action','a'))
		{
			case 'login':
				if(isInPOST(array("user_name","user_pass")))
				{
					if($this->enter($_POST['user_name'],$_POST['user_pass']))$this->message(LOGINER_MESSAGE_LOGIN_OK); 
					else $this->message(LOGINER_MESSAGE_LOGIN_BAD);
				}
				break;
			case 'reg':
				if(isInPOST(array("user_name","user_pass","user_pass2","user_email")))
				{ 
					if($this->params&LOGINER_PARAM_NEED_EMAIL && trim($_POST['user_email']) == "")
					{
						$this->message(LOGINER_MESSAGE_NEED_EMAIL);
					}
					else 
					{
						if(selectOne("SELECT `".$this->names['id']."` FROM `".$this->names['table']."` WHERE `".$this->names['username']."`='".specChars($_POST['user_name'],"loginer::process")."'"))
						{
							$this->message(LOGINER_MESSAGE_REG_USERNAME_EXISTS);
						}
						else
						{
							if($_POST['user_pass'] == $_POST['user_pass2'])
							{
								if($this->addUser($_POST['user_name'],$_POST['user_pass'],$_POST['user_email']))
								{
									$this->message(LOGINER_MESSAGE_REG_OK,delGET(array("a")));
								}
							}
							else 
							{//Meg kell oldani, hogy POST adatot is megtartson a message
								$this->message(LOGINER_MESSAGE_DIFFERENT_PASSES);
							}
						}
					}
				}
				break;
		}
	}

	function show()
	{
		if($this->isLogined())
		{
			echo $this->templates[LOGINER_TEMPLATE_HELLO]->getTrans(
														array_merge($this->akt_user_data,array(
																		"logout_get"=>addGET(array('a'=>"logout"))
																		)));
		}
		else switch(getGET('a','a'))
		{
			case 'a':default:
				echo $this->templates[LOGINER_TEMPLATE_LOGIN_FORM]->getTrans(array(	
																	"hidden_zone"=>'<input type="hidden" name="action" value="login" />',
																	"lostpass_link"=>addGET(array("a"=>"lostpass")),
																	"reg_link"=>addGET(array("a"=>"reg")),
																	"action_url"=>delGET(array("a")),
																	"pass_input_name"=>"user_pass",
																	"name_input_name"=>"user_name",
																	"can_reg"=>$this->params&LOGINER_PARAM_REG,
																	"can_get_lost_pass"=>$this->params&LOGINER_PARAM_LOSTPASS
																	));
				break;
			case 'reg':
				echo $this->getRegForm();
				break;
		}
	}

	function getRegForm()
	{
		return  $this->templates[LOGINER_TEMPLATE_REG_FORM]->getTrans(array(
									"hidden_zone"=>'<input type="hidden" name="action" value="reg" />',
									"cancel_login_get"=>delGET(array("a")),
									"user_name_input_name"=>"user_name",
									"user_pass_input_name"=>"user_pass",
									"user_email_input_name"=>"user_email",
									"user_pass2_input_name"=>"user_pass2",
									"user_name_input_value"=>$_POST['user_name'],
									"user_email_input_value"=>$_POST['user_email'],
									"user_pass_input_value"=>$_POST['user_pass']
									));
	}




	function mailToUsers($sender,$subject,$body,$where="")
	{
		foreach(doSelect("SELECT `".$this->names['email']."` FROM `".$this->prefix.$this->names['table']."` ".($where==''?"":"WHERE ".$where)) as $val)
		{
			sendEMail($sender,$val[$this->names['email']],$subject,$body);
		}
	}



	function enter($name,$pass,$type=1)//type:1 - csaknév 2- csakemail 3-mindkettő alapján való belépés
	{
		$sql_q = "SELECT * FROM `".$this->prefix.$this->names['table']."` WHERE `".$this->names['pass']."`='".md5($pass)."' ";
		switch($type)
		{
			case 1:
				$sql_q .=  "AND `".$this->names['username']."`='".specChars($name,'getUser')."'";
				break;
			case 2:
				$sql_q .=  "AND `".$this->names['email']."`='".specChars($name,'getUser')."'";
				break;
			case 3:
				$sql_q .=  "AND (`".$this->names['username']."`='".specChars($name,'getUser')."' ";
				$sql_q .=  "OR `".$this->names['email']."`='".specChars($name,'getUser')."')";
				break;
		}

		if($this->akt_user_data = selectOne($sql_q))
		{
			$this->akt_user_data[$this->names['identifier']] = randomString(10);

			return mysql_query("UPDATE `".$this->prefix.$this->names['table']."` 
									SET  `".$this->names['ip']."`='".$_SERVER['REMOTE_ADDR']."',
									  `".$this->names['identifier']."`='".$this->akt_user_data[$this->names['identifier']]."',
									  `".$this->names['login_time']."`=".time()."  ".	
											"WHERE `".$this->names['id']."`=".$this->getId());
		}	
		return false;
	}


	function addUser($name,$pass,$mail,$activate_link="",$activate_subject="")
	{
		return mysql_query("INSERT INTO `".$this->prefix.$this->names['table']."` (`".$this->names['username']."`,`".$this->names['pass']."`,`".$this->names['email']."`) VALUES('".specChars($name,'addUser')."','".md5($pass)."','".specChars($mail,'addUser')."')");
	}





	function setUserPass($user_id,$newpass)
	{
		if(reportedIsNumber($user_id,"loginer::setUserPass")) return mysql_query("UPDATE `".$this->prefix.$this->names['table']."` SET `".$this->names['pass']."`='".md5($newpass)."' WHERE `".$this->names['id']."`=".$user_id);
		return false;
	}



	/*

	function activate($activate_code)
	{
		if(!isNumber($user_id)) 
		{
			report_error('user::activateUser ');
			return false;
		}
		if($is_code_ok_v = selectOne("SELECT code FROM `activate_code` WHERE `user_id`=".$user_id))
		{
			if($is_code_ok_v['code'] == $activate_code)
			{
				if(mysql_query("UPDATE `user` SET `user_is_active`=1 WHERE `user_id`=".$user_id))
				{
					mysql_query("DELETE FROM `activate_code` WHERE `user_id`=".$user_id);	
					return true;
				}
				else
				{
					report_error('user::activateUser sql hiba aktiválás');
				}
			}
			else
			{
				report_error('user::activateUser hibás aktiválási kód');
			}
		}
		else
		{
			report_error('user::activateUser sql hiba select');
		}
		return false;
	}


	 
	 

	
	function setParam($user_id,$param_val_ar)
	{
		$PARAM_AR =array('user_first_name','user_last_name','user_pass','user_addr','user_nem');
		if(!isNumber($user_id) || !is_array($param_val_ar)) return false;

		$sql_q = '"UPDATE `user` SET ';
		foreach($param_val_ar as $key => $val)
		{
			if(isNumber($key) && $key >= 0 && $key < count($PARAM_AR))
				$sql_q .= "`".$key."`='".specChars($val,'setUserParam')."' ";
		}

		if(mysql_query($sql_q." WHERE `user_id`='".$user_id."'"))
		{
			return true;
		}
		else
		{
			report_error('user::setUserParam sql hiba');
			return false;
		}
	}
*/
};


/*	function updateSession()
	{
		$user_q = mysql_query("SELECT * FROM `user` WHERE `user_login_name`='".$_SESSION['user']['user_login_name']."' AND `user_pass`='".$_SESSION['user']['user_pass']."'");
		if($user_q)
		{
			$_SESSION['user'] = mysql_fetch_assoc($user_q);
			mysql_free_result($user_q);
			return true;
		}	
		else
		{
			report_error('user::updatetSession sql hiba');
		}
		return false;
	}

	function updateFromSession()
	{
		mysql_query("UPDATE `user` SET `user_first_name`='".specChars($_SESSION['user']['user_first_name'],'user::updateFromSession')."',".
				"`user_nick_name`='".specChars($_SESSION['user']['user_nick_name'],'user::updateFromSession')."',".
				"`user_last_name`='".specChars($_SESSION['user']['user_last_name'],'user::updateFromSession')."',".
				"`user_addr`='".specChars($_SESSION['user']['user_addr'],'user::updateFromSession')."',".
				"`user_born`='".specChars($_SESSION['user']['user_born'],'user::updateFromSession')."',".
				"`user_nem`='".specChars($_SESSION['user']['user_nem'],'user::updateFromSession')."' WHERE `user_id`=".$_SESSION['user']['user_id']);
	}
*/
?>
