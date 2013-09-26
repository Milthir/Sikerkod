<?
/*
Készítette: Hudoba Péter hpete@freemail.hu
vendegkonyv class 0.95v
	Szükséges:
		-assist
		-lapozo class

funkciók:
	constructor 
	function setUser($user)		ezzel adod meg, hogy éppen mi az ID-je a USER-nek

	function setUserFunction($user_func)		ide egy függvényt adhatsz meg paraméterül ami visszaadja az adott USER-nek a nevét

	function setTraceFunction($trace_func)	ide is egy FGV-t adhacc meg aminek az osztály paraméterül adja át a hibaüzeneteket


	function echoSenderForm()	kiírja a send formot
	function echoUzik($is_admin)	kiírja az üziket(első paraméter ha igaz akkor lehet törölni)



	function setPrevButt($src)			előző lap link szövege(kép is lehet)
	function setNextButt($src)			következő lap link szövege(kép is lehet)
	function setTerjedelem($ter)		mennyi lapnak a számát jelenítse meg egyszerre
	function setUziPerLap($uziperlap)	mennyi üzenet jelenjen meg 1 lapon

	function setAdminMode()		admin módot bekapcsolja
	function setIdleMessage($new_msg)	a töltési szöveget lehet beállítani vele(js-el irányít át)
										ha ob_start-al a constructorig blokkolod az adatküldést vagy a konstruktorát minden szöveg elé rakod akkor header-el átirányít

	function setTemplate($new_template)		
		egy üzenetnek a template-je (speciális jelek benne ##SENDER## ##BODY## és ##TIME:_date_time_forma##)
		DEFAULT: <div><div>##SENDER## ##TIME:Y-M-d H:i.s##</div>##BODY####ADMIN##</div>


	function setSendTemplate($new_template)		
		megadhatod a FORMOT ami kell lényegében felesleges manuálisan is megoldható(a feldolgozása a constructor-ban van)		
		DEFAULT:	<form method="post" action=""><input type="hidden" name="action" value="vk_send_msg">'.(($params & VK_PARAM_USER)?'':'<input type="text" name="sender"><br/>').'<textarea name="msg" rows="10" cols="30"></textarea><br /><input type="submit"></form>


	function setAdminTemplate($new_template)	HIDDENS nélkül nemfog menni
		DEFAULT: <form method=post action=""><fieldset><legend>Admin</legend><form method=post action="">##HIDDENS##<input type="checkbox" name="delok" /><input type="submit" value="Törlés" /></form></fieldset></form>

		
*/

define("VK_PARAM_USER",1); //usert 
class vendegkonyv extends lapozo
{
	var $params;

	var $user_func; //ez hívódik meg ha kérem az adott ID-jű USER nevét
	var $trace_func; // Ez hívódik meg ha valamit közölni akar a meghívóval az osztály

	var $user;
	
	var $prefix; //tábla előnév 
	var $template; // vendégkönyv bejegyzései
	var $send_template; // Küldő Form
	var $admin_template;

	var $prev_butt;
	var $next_butt;
	var $terjedelem;

	function defUser(){ return "";}
	function vendegkonyv($params=0,$can_proc=1,$prefix='')
	{
		$this->prefix = $prefix;
		if(!$this->isTableExists())
		{
			echo "Installálás";
			$this->installSQL();
		}
		$this->prev_butt='&lt;&lt;';	
		$this->next_butt='&gt;&gt;';	
		$this->terjedelem=3;

		$this->is_admin = false;
		$this->idle_message = "Betöltés alatt...";

		
		$this->params = $params;
		$this->adat_per_lap = 10;
		$this->template = "<div><div>##SENDER## ##TIME:Y-M-d H:i.s##</div>##BODY##</div>";
		$this->send_template = '<form method="post" action=""><input type="hidden" name="action" value="vk_send_msg">'.(($params & VK_PARAM_USER)?'':'<input type="text" name="sender"><br/>').'<textarea name="msg" rows="10" cols="30"></textarea><br /><input type="submit"></form>';
		$this->admin_template = '<form method=post action=""><fieldset><legend>Admin</legend><form method=post action="">##HIDDENS##<input type="checkbox" name="delok" /><input type="submit" value="Törlés" /></form></fieldset></form>';

		$this->user_func = $this->defUser();
		$this->trace_func = null;

		if($can_proc)$this->process();
	}

	function isTableExists()
	{
		foreach(doSelect("SHOW TABLES;") as $tabel_it)
		{
			$tname = current($tabel_it);
			foreach($tabel_it as $key => $tname)
			{
				if($tname == $this->getTableName())
				{
					$ell_ar = array("uzi_id"=>false,"uzi_sender"=>false,"uzi_message"=>false,"uzi_time"=>false);
					$type_ar = array("uzi_id"=>"int(10) unsigned",
									"uzi_sender"=>($this->isParam(VK_PARAM_USER)?"int(10) unsigned":"text"),
									"uzi_message"=>"text",
									"uzi_time"=>"int(10) unsigned");

						
					foreach(doSelect("DESCRIBE ".$tname) as $col_it)
					{
						foreach($ell_ar as $ell_it_key => $ell_it)
						{
							if($ell_it_key == $col_it['Field'] && $type_ar[$ell_it_key] == $col_it['Type'])
							{
								$ell_ar[$ell_it_key] = true;
								break;
							}
						}
					}

					foreach($ell_ar as $val2)
					{
						if(!$val2)
						{
							echo 'A használt tábla('.$this->getTableName().') szerkezete nem megfelelő vagy módosítsa a táblanevet vagy törölje a mostani táblát';
							break;
						}
					}
					return 1;
				}
			}
		}
		return 0;
	}
	function getTableName()
	{
		if($this->params & VK_PARAM_USER)
		{
			return $this->prefix."vendegkonyv_u";
		}
		else
		{
			return $this->prefix."vendegkonyv";
		}
	}

	function isParam($param)
	{
		return $this->params & $param;
	}

	function installSQL()
	{
		if(mysql_query("CREATE TABLE `".$this->prefix."vendegkonyv".($this->isParam(VK_PARAM_USER)?"_u":"")."` (".
		"`uzi_id` INT UNSIGNED AUTO_INCREMENT ,".
		"`uzi_sender` ".($this->isParam(VK_PARAM_USER)?"INT UNSIGNED":"TEXT").",".
		"`uzi_message` TEXT ,".
		"`uzi_time` INT UNSIGNED,".
		"PRIMARY KEY ( `uzi_id` )".
		")"))
		{
			echo 'Adatbázis létrehozása sikeres';
		}
		else
		{
			echo 'Adatbázis létrehozása sikertelen';
		}
	}

	function process()
	{
		$is_proc = false;
		if(isAction("vk_send_msg") && isset($_POST['msg']))
		{
			if($this->params & VK_PARAM_USER)
			{
				$this->addUzi($this->user,$_POST['msg']);
			}
			else if(isset($_POST['sender']))
			{
				$this->addUzi($_POST['sender'],$_POST['msg']);
			}
			$is_proc = true;
		}
		if(isAction('del') && isInPOST(array('delok','uzi_id')))
		{
			
			$this->delUzi($_POST['uzi_id']);
			$is_proc = true;
		}
		
		if($is_proc)
		{
			if(!headers_sent())
			{
				header("Location: ".getFullGET());
			}
			echo $this->idle_message;
			die("<script>window.location=window.location;</script>");
		}
	}

	function echoSenderForm()
	{
		if(!($this->params & VK_PARAM_USER) || $this->user)
		{
			echo $this->send_template;
		}
	}
	function echoUzik()
	{
		$this->lapozo($this->getTableName(),"","lp",$this->adat_per_lap);

		$uzik_q = mysql_query("SELECT * FROM `".$this->getTableName()."` ORDER BY `uzi_time` DESC ".$this->getLimit());
		if($uzik_q)
		{
			while($uzik_v = mysql_fetch_assoc($uzik_q))
			{
				$body_replaces = array("/(http:\/\/[\w\.\/]+)/");
				$body_replaces2 = array('<a onclick="window.open(this.href,\'_blank\');return false;" href="\1">\1</a>');

				$temp = str_replace("##BODY##",nl2br(preg_replace($body_replaces,$body_replaces2,$uzik_v['uzi_message'])),$this->template);
				if($this->is_admin)
				{
					$admin_tmplate = str_replace("##HIDDENS##",'<input type="hidden" name="action" value="del"><input type="hidden" name="uzi_id" value="'.$uzik_v['uzi_id'].'">',$this->admin_template);

					$temp = str_replace("##ADMIN##",$admin_tmplate,$temp);
				}
				else
				{
					$temp = str_replace("##ADMIN##",'',$temp);
				}
				
				if($this->params & VK_PARAM_USER)
				{
					$temp = str_replace("##SENDER##",$this->user_func($uzik_v['uzi_sender']),$temp);
				}
				else
				{
					$temp = str_replace("##SENDER##",$uzik_v['uzi_sender'],$temp);
				}
				
				
				if(ereg("##TIME:([^#]+)##",$temp,$regs))
				{
					$temp = str_replace($regs[0],date($regs[1],$uzik_v['uzi_time']+60*60),$temp);
				}
				

				echo $temp;
			}
			mysql_free_result($uzik_q);
		}

		echo $this->getButtons($this->terjedelem,$this->prev_butt,$this->next_butt);
	}

	function delUzi($uzi_id)
	{
		if(isNumber($uzi_id))
		{
			mysql_query("DELETE FROM `".$this->getTableName()."` WHERE `uzi_id`=".$uzi_id);
		}
		else
		{
			$this->trace("delUzi számot kapott");
		}
	}

	function addUzi($sender,$message)
	{
		
		if($this->params & VK_PARAM_USER && !isNumber($sender)) return false;
		if(!($this->params & VK_PARAM_USER)) $sender = specChars($sender,'vendegkonyv::addUzi');

		if(mysql_query("INSERT INTO `".$this->getTableName()."`(`uzi_sender`,`uzi_message`,`uzi_time`) VALUES(".
								"'".$sender."',".
								"'".specChars($message,'vendegkonyv::addUzi')."',".
								time().
					")"))
		{
			return true;
		}
		else
		{
			$this->TRACE('HIBA: vendegkonyv::addUzi');
			return false;
		}
	}
	


	function setUser($user)	{$this->user = $user;				}

	function TRACE($text)	{if($this->trace_func)$this->trace_func($text);}

	function setUserFunction($user_func)	{$this->user_func = $user;		}
	function setTraceFunction($trace_func)	{$this->trace_func = $trace;	}

	function setTemplate($new_template)		{$this->template = $new_template;	}
	function setAdminTemplate($new_template)	{$this->admin_template = $new_template;	}
	function setSendTemplate($new_template)		{$this->send_template = $new_template;	}

	function setAdminMode()	{$this->is_admin = true;}
	function setIdleMessage($new_msg) {$this->idle_message = $new_msg;}

	function setPrevButt($src)		{$this->prev_butt=$src;	}
	function setNextButt($src)		{$this->next_butt=$src;	}
	function setTerjedelem($ter)	{$this->terjedelem=$ter;	}
	function setUziPerLap($uziperlap)	{$this->adat_per_lap=$uziperlap;	}
}

?>