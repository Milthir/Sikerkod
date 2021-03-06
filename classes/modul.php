<?
/*
Ver.: 2.0


*/
define("PROFILE_TEMPLATE_MAIN",1);
define("PROFILE_TEMPLATE_FRAME",2);

define("PROFILE_PARAM_NO_AUTOINSERT",1);

define("PROFILE_MESSAGE_MOD_OK",1);
define("PROFILE_MESSAGE_MOD_FAIL",2);
function profile_checkbox_function($a)
{
	return isset($a)?1:0;
}

class profile
{
	var $fields;
	var $types;
	var $arg_sets;
	var $params;
	var $auto_inc;
	
	var $primary;
	var $fix_fields;
	var $mod_reload;
	var $insert_fields;
	var $insert_id;
	var $tableName;

	function profile($types,$fields,$arg_sets=array(),$params=0,$set_default_templates=true)
	{
 		$this->fields = $fields;
		$this->params = $params;
		$this->insert_id = false;
		
		$this->arg_sets = $arg_sets;	
		$this->auto_inc = false;
		
		$this->mod_reload = '';
		if(!is_array($this->arg_sets))$this->arg_sets = array();
		$this->fix_fields = array();
		$this->insert_fields = array();
		$this->message_text[PROFILE_MESSAGE_MOD_OK] = "Módosítás sikeres.";
		$this->message_text[PROFILE_MESSAGE_MOD_FAIL] = "Módosítás közben valami hiba merült fel.";
		if($set_default_templates)
		{ 
			/*$this->templates[PROFILE_TEMPLATE_MAIN] = new template('
				<div style="border:1px dashed;">
					[if {can_mod}]<form method="post" action="">{hidden_zone}[/if]
					{field_valami}
					[if {can_mod}]<input type="submit" value="Mentés" /></form>[/if]
				</div>');*/
			$this->templates[PROFILE_TEMPLATE_FRAME] = new template("{body}{newbody}");
		
			$this->types = array_merge(
					array(
						
						"textfield"=>array(	"type"=>"TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
											"view"=>new template("{field_val}"),
											"edit"=>new template('<input type="text" name="{field_name}" value="{field_val}" />')
											),
						"widetextfield"=>array(	"type"=>"TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
											"view"=>new template("{field_val}"),
											"edit"=>new template('<input type="text" name="{field_name}" value="{field_val}" style="width:100%" />')
											),
						"email"=>array(	"type"=>"TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
											"view"=>new template('<a href="mailto:{field_val}">{field_val}</a>'),
											"edit"=>new template('<input type="text" name="{field_name}" value="{field_val}" />')
											),
						"textarea"=>array(	"type"=>"TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
											"view"=>new template("{nl2br(htmlspecialchars('{field_val}'));}"),//itt a spec charst kikéne cserélni...
											"edit"=>new template('<textarea name="{field_name}">{field_val}</textarea>')
											),
						"number"=>array(	"type"=>"INT UNSIGNED NOT NULL",
											"view"=>new template("{field_val}"),
											"edit"=>new template('<input type="text" name="{field_name}" value="{field_val}" onkeypress="var charCode = (event.which) ? event.which : event.keyCode;return !(charCode > 31 && (charCode < 48 || charCode > 57));" />'),
											"check_function"=>isNumber
											),
						"link"=>array(	"type"=>"TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
											"textview"=>new template('[if {substr_compare(ltrim("{field_val}"),"http://",0,7)!=0}]http://[/if]{field_val}'),
											"view"=>new template('<a href="[if {substr_compare(ltrim("{field_val}"),"http://",0,7)!=0}]http://[/if]{field_val}" onclick="window.open(this.href,\'_blank\');return false;">{field_val}</a>'),
											"edit"=>new template('<input type="text" name="{field_name}" value="{field_val}" />')
											),
						"date"=>array(	"type"=>"INT UNSIGNED NOT NULL",
											"view"=>new template("{date('Y-m-d',{field_val})}"),
											"edit"=>new template('<input type="text" name="{field_name}" value="{date("Y-m-d",{field_val})}" />'),
											"process_function"=>strtotime,
											"check_function"=>isNumber,
											"no_report_check_error"=>true
											),
						"datetime"=>array(	"type"=>"INT UNSIGNED NOT NULL",
											"view"=>new template("{date('Y-m-d H:i.s',{field_val})}"),
											"edit"=>new template('<input type="text" name="{field_name}" value="{date("Y-m-d",{field_val})}" />'),
											"process_function"=>strtotime,
											"check_function"=>isNumber,
											"no_report_check_error"=>true
											),
						"check"=>array(	"type"=>"INT (1) UNSIGNED NOT NULL",
											"view"=>new template('<input type="checkbox" [if {field_val}]checked="checked"[/if] disabled="disabled" />'),
											"textview"=>new template('[if {field_val}]x[/if]'),
											"edit"=>new template('<input type="checkbox" name="{field_name}" [if {field_val}]checked="checked"[/if] />'),
											"process_function"=>profile_checkbox_function,
											"check_function"=>isNumber
											),
						"password"=>array(	"type"=>"TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
											"view"=>new template(""),
											"edit"=>new template('<input type="password" id="{field_name}" name="{field_name}" value="" />'),//<input type="text" id="brother_{field_name}" name="" value="" onkeydown="if(jQuery(\'#{field_name}\').val() != jQuery(\'#brother_{field_name}\').val())jQuery(\'#{field_name}\').closest(\':submit\').attr(\'disabled\', \'disabled\');else jQuery(\'#{field_name}\').closest(\':submit\').removeAttr(\'disabled\');" />
											"process_function"=>create_function('$a','return empty($a)?"":md5("hudi".$a."profile");'),
											"check_function"=>create_function('$a','return !empty($a)?1:0;'),
											"no_report_check_error"=>true
											)

						),
						is_array($types)?$types:array());	
		}
		else
		{
			$this->types = $types;
		}
	}
	function setTableName($setTo)
	{
		$this->tableName = $setTo;
	}
	
	function setFixFields()
	{
		$this->fix_fields = func_get_args();
	}

	function setInsertFields($new)
	{
		$this->insert_fields = $new;
	}	
	function setPrimary()
	{
		$this->primary = func_get_args();
	}
	
	function setArg($key,$val)
	{
		$this->arg_sets[$key] = $val;
	}
	
	function unsetArg($key)
	{
		unset($this->arg_sets[$key]);
	}
	function setAutoIncrement($set)
	{
		if(in_array($set,$this->primary))
			$this->auto_inc = $set;
		else 
			report_error("A mező nem lehet AutoInc, mert nem Primary.");
	}	
		
	function setModReload($set)
	{
		$this->mod_reload = $set;
	}	
	function getModReload()
	{
		return $this->mod_reload;
	}
	function getInsertId()
	{
		return $this->insert_id;
	}
	
		
	function install(/*argumentum indexek tömbjei*/)
	{
		$query = "CREATE TABLE `".$this->tableName."` (";
		$first = true;		
		foreach($this->fields as $name => $type)
		{
			if(!isset($this->types[$type]))
			{
				report_error("profile::install ismeretlen mező típus (".$name."=>".$type.")");
				return false;
			}
			$query .= ($first?"":",")."`".$name."` ".$this->types[$type]['type']." ".($this->auto_inc==$name?"AUTO_INCREMENT":"");
			
			$first = false;
		}
		
		if(is_array($this->primary))
		{
			if(count($this->primary) > 0)
			{
				$query .= ", PRIMARY KEY(";
				$first = true;
				foreach($this->primary as $val)
				{
					if(strpos($this->types[$this->fields[$val]]['type'],"INT") === false)
					{
						report_error("profile::install Primary mező csak szám lehet");
						return false;
					}
					$query .= ($first?"":",")."`".$val."`";
					$first = false;
				}
				$query .= ")";
			}
		}
		else
		{
			report_error("profile::install primary argumentum nem tömb");
			return false;
		}		

		$args = func_get_args();

		for($a=0;$a<func_num_args();$a++)
		{
			if(is_array($args[$a]) && count($args[$a]) >0)
			{
				$query .= ", INDEX(";
				foreach($args[$a] as $val)
				{
					$query .= "`".$val."`";
				}
				$query .= ")";
			}
			else 
			{
				report_error("profile::install egyik indexe nem tömb, vagy üres");
			}
		}
		$query .= " );";
		return mysql_query($query);
	}
	
	function process()
	{
		if(!preg_match("/^".$this->tableName."_(.+)$/",getPOST('action','a'),$reg))return false;
		switch($reg[1])
		{
			case 'mod':
				$ok = true;
				if(isset($_POST['prims']) && trim($_POST['prims']) != '')
				{
					$prim_ar = explode("|",$_POST['prims']);
					$prim_preg = str_repeat("_(-?[0-9]+)",count($prim_ar));
					$mods = array();

					foreach($_POST as $key => $val)
					{
						//echo $key."<br/>";
						if(preg_match("/^(.+)".$prim_preg."$/",$key,$reg2))
						{		
						//	listArray($reg2);
	
							$modkey = "";
							for($a=2;$a<count($reg2);$a++)$modkey .= $reg2[$a]."_";
							$mods[$modkey][$reg2[1]] = $val;
							array_shift($reg2);
							array_shift($reg2);
							
							if(!isset($mods[$modkey]['keys']))$mods[$modkey]['keys'] = array_combine($prim_ar,$reg2);
						}
					}
					$oldargs = $this->arg_sets;
			//		listArray($mods);
					foreach($mods as $key => $val)
					{
						$this->arg_sets = array_merge($val['keys'],$oldargs);
						$ok &= $this->modArgs($val);
					}
					$this->arg_sets = $oldargs;
				}
				else
				{ 
/*					if(isset($this->arg_sets[$this->auto_inc]) && $this->arg_sets[$this->auto_inc] == -1)
					{
						$this->insertEmpty();
					}
	*/				$ok = $this->modArgs($_POST);
				}
				if($ok)$this->message(PROFILE_MESSAGE_MOD_OK,$this->mod_reload);
				else $this->message(PROFILE_MESSAGE_MOD_FAIL);
				break;
		}
	}
	
	/*
	*/
	function show($order=array(),$where="")
	{		
		//$prim_ar = array_diff($this->primary,array_keys($this->arg_sets));
		$prim_ar = $this->primary;
		
		$template = $this->templates[PROFILE_TEMPLATE_MAIN]->copy();
		
		$def_trans =  array("admin_mode"=>$this->admin_mode,"can_mod"=>$this->admin_mode,
						"hidden_zone"=>'<input type="hidden" name="action" value="'.$this->tableName.'_mod" />
										<input type="hidden" name="prims" value="'.implode("|",$prim_ar).'" />');
		$trans_ar = $def_trans;
				
		foreach($this->fields as $name => $type)
		{
		
			if(array_key_exists($type,$this->types))
			{	
				$trans_ar = array_merge($trans_ar,array(
						("field_".$name)=>$this->getTemplateByType($type,$this->admin_mode && !in_array($name,$this->fix_fields))->getVarTrans(array(
																				"field_name"=>$name."{field_category}",
																				"field_val"=>"{".$name."}"
																					))
									));
			}
			else 
			{
				report_error($type.' mező típus nem ismert.');
			}
		}
		$template->doVarTrans($trans_ar);

		$body = "";
		$newbody = "";
		/*!isset($this->arg_sets[$this->auto_inc]) || $this->arg_sets[$this->auto_inc] < 0*/
		$data = $this->getProfile($order,$where);

		$counter = 0;
		foreach($data as $user_v)// kilistázzuk ami a megadásból megvan
		{
			$user_v['field_category'] = "";
			foreach($prim_ar as $val)
			{
				$user_v['field_category'] .= "_".$user_v[$val];
			}
			$user_v['rowid'] = $counter++; 
 			$body .=$template->getTrans($user_v);
		}
		
		if(		$this->admin_mode && !($this->params&PROFILE_PARAM_NO_AUTOINSERT) && 
				(($this->isInsertUnique() && count($data) == 0) || // ha egyértelműen van megadva és nemvolt még listázva
			 	(!$this->isUnique())) ) // van már adat de van autoinsert 
		{	
			$trans_ar = array();
			$trans_ar['field_category'] = "";
			foreach($prim_ar as $val)
			{
				$trans_ar['field_category'] .= "_".($val==$this->auto_inc && !($this->arg_sets[$val])?-1:$this->arg_sets[$val]);
			} 
			foreach($this->fields as $key => $val)
			{
				if(stripos($this->types[$val]['type'],"INT") === false)$trans_ar[$key] = '';
				else $trans_ar[$key] = 0;
			}
			$trans_ar = array_merge($trans_ar,$this->arg_sets);
			$newbody.=$template->getTrans($trans_ar);
		}
		
		
		echo $this->templates[PROFILE_TEMPLATE_FRAME]->getTrans(
			array_merge(
				$def_trans,
				array("body"=>$body,"newbody"=>$newbody,"admin_mode"=>$this->admin_mode)
			));
		/*echo $this->templates[PROFILE_TEMPLATE_MAIN]->getTrans($trans_ar);
		*/
		
		/*foreach($this->getProfile($order) as $user_v)
		{		
			$trans_ar = array("admin_mode"=>$this->admin_mode,"can_mod"=>$this->admin_mode,"hidden_zone"=>'<input type="hidden" name="action" value="'.$this->prefix.'profile_mod" /><input type="hidden" name="sets" value="'.implode("|",$prim_ar).'" />');

			foreach($this->fields as $name => $type)
			{
				$temp = "";
				foreach($prim_ar as $val)
				{
					$temp .= "_".$user_v[$val];
				} 
				$trans_ar = array_merge($trans_ar,array(
							("field_".$name)=>$this->getTemplateByType($type,$this->admin_mode && !in_array($name,$this->fix_fields))->getTrans(array(
																					"field_name"=>$name.$temp,
																					"field_val"=>$user_v[$name]
																					)),
							$name=>$user_v[$name]
								));
			}
			echo $this->templates[PROFILE_TEMPLATE_MAIN]->getTrans($trans_ar);
		}*/
	}
	
	function isUnique()
	{
		foreach($this->primary as $val)
		{
			if(!array_key_exists($val,$this->arg_sets))return false;
		}
		return true;
	}	
	function isInsertUnique()
	{
		foreach($this->primary as $val)
		{
			if(!array_key_exists($val,$this->arg_sets) && $val != $this->auto_inc)return false;
		}
		return true;
	}
	
	function insertEmpty($fieldset=array())
	{
		$keys = "";
		$vals = "";
		$first = true;
		foreach(array_merge($this->arg_sets,$fieldset,$this->insert_fields) as $key => $val)
		{
			$keys .= ($first?"":",")."`".specChars($key,"profile::insertEmpty")."`";
			$vals .= ($first?"":",")."'".specChars($val,"profile::insertEmpty")."'";
			$first = false;
		}
		$res = mysql_query("INSERT INTO `".$this->tableName."`(".$keys.") VALUES(".$vals.")");
		$this->insert_id = mysql_insert_id();
		return $res;
	}
	
	function getProfileView($order=array(),$where="",$select="*")
	{
		$tempprof = $this->getProfile($order,$where);
		foreach($tempprof as $profkey => $prof)
		{
			foreach($prof as $key => $val)
			{
				$tempprof[$profkey][$key] = $this->getTemplateByType($this->fields[$key],false,true)->getTrans(array("field_val"=>$val));
			}
		}
		return $tempprof;
	}

	function getProfile($order=array(),$where="",$select="*")
	{
		$orderstr = "";
		if(is_array($order))
		{
			$first = true;
			foreach($order as $key => $val)
			{
				$orderstr .= ($first?"":",")."`".$key."` ".($val=="DESC"?"DESC":"ASC");
				$first = false;
			}
			if(!$first)$orderstr = "ORDER BY ".$orderstr; 
		}
		
		if(!($user_v = doSelect("SELECT ".specChars($select,"profile::getProfile")." FROM `".$this->tableName."` ".$this->getWhere($where)." ".$orderstr.(empty($limit)?" ".$limit:"") )))
		{
			/*if(!($this->params&PROFILE_PARAM_NO_AUTOINSERT))
			{
				$this->insertEmpty($this->insert_fields);
				return doSelect("SELECT ".specChars($select,"profile::getProfile")." FROM `".$this->tableName."` ".$this->getWhere()." ".$orderst);
			}*/
			return array();
		}
		return $user_v;
	}
	
	
	
	function modArgs($mod_pairs)// array -> key a mező neve, a val hogy mire 
	{
		if(!$this->admin_mode)return false;	
		/*if(!isset($mod_pairs['keys'][$this->auto_inc]) || $mod_pairs['keys'][$this->auto_inc] < 0)*/
		if($this->isInsertUnique())
		{
//			if(count(array_diff($this->primary,array_keys($mod_pairs['keys']))) == 0)
			if(count($this->getProfile()) == 0)
			{
				$this->insertEmpty();
				$this->arg_sets[$this->auto_inc] = $this->insert_id;
			} 
//			else report_error("profile::modArgs auto beszúrás lett volna de primary mezők értékei hiányoznak");
		}

	
		$query = "UPDATE `".$this->tableName."` SET ";
		$first = true;
		
		foreach($this->fields as $name => $typename)
		{
			if(in_array($name,$this->fix_fields))continue;
			
			$type = $this->types[$typename];
			$temp = $mod_pairs[$name];
//			if($temp) ITT VAN EGY OLYAN PROBLÉMA HOGY VAGY A NEM MEGJELENÍTETTEKKEL VAN A BAJ VAGY A CHECK-ekkel...
			if(isset($temp) || $typename == "check")
			{
				$okey = true;
				if(isset($type['process_function']))
				{
					//$temp =  @eval("\$profile_temp=".$type['process_function']."(".(isset($mod_pairs[$name])?"'".$mod_pairs[$name]."'":"").");return \$profile_temp===false?0:\$profile_temp;");
					$temp =  @eval("\$profile_temp=\$this->types['".$typename."']['process_function'](".(isset($mod_pairs[$name])?"'".$mod_pairs[$name]."'":"").");return \$profile_temp===false?0:\$profile_temp;");
					
					if($temp === false)
					{
						report_error("profile::modArg sikertelen process függvényhívás ( ".$type['process_function']."(".$mod_pairs[$name].") )");
						$okey = false;
					}
				}
				if(isset($type['check_function']))
				{
					//echo "[".$temp."]";
					//$ret = @eval("return ".$type['check_function']."('".$temp."')?1:0;");
					$ret = @eval("return \$this->types['".$typename."']['check_function']('".$temp."')?1:0;");
					//var_export($ret);
					if($ret === false)
					{
						report_error("profile::modArg sikertelen check függvényhívás (".$type['check_function'].")");
						$okey = false;
					}
					if(!$ret)
					{
						if(!isset($type['no_report_check_error']))report_error("profile::modArg nem ment át az ellenőrzésen (".$name."=>".$val.")");
						$okey = false;
					}
				}
				
				if($okey)
				{
					$query .= ($first?"":",")."`".$name."`='".specChars($temp,"profile::modArg")."'";				
					if($first)$first=false;
				}
			}
		}

		///report_error($query." ".$this->getWhere());
		if(mysql_query($query." ".$this->getWhere())) return true;
		report_error("profile::modArgs [".$query." ".$this->getWhere()."]");
		return false;
	}
	
	function getWhere($extra_where="",$forshow=true)
	{
		$where = "";
		$first = true;
		foreach($forshow?$this->arg_sets:array_intersect_key($this->arg_sets,array_flip(array_values($this->primary))) as $key => $val)
		{
			$where .= ($first?"":" AND ")."`".$key."`='".$val."'";
			$first = false;
		}
		
		$where .= trim($extra_where)==""?"":((trim($where)==''?"":" AND ")."(".$extra_where.")");
		
		return (trim($where)==''?"":" WHERE ".$where);
	}
	
	function getTemplateByType($type,$admin_mode,$text=false)
	{
		foreach($this->types as $key => $val)
		{
			if($key == $type)
			{
				if($admin_mode)return $val['edit'];
				else 
				{
					if($text && isset($val['textview'])) return $val['textview'];
					else return $val['view'];
				}
			}
		}
	}
	
	
	function createOptionType($options)
	{
		$view_str = "";
		$edit_str = "";
		$cnt = 1;
		foreach($options as $val)
		{
			$view_str .= "[if {field_val}==".$cnt."]".$val."[/if]";
			$edit_str .= '<option value="'.$cnt.'" [if {field_val}=='.$cnt.']selected="selected"[/if]>'.$val.'</option>';
			$cnt++;
		}
		return array(	
			"type"=>"INT(".ceil(log10(count($options)+1)).") UNSIGNED NOT NULL",
			"view"=>new template($view_str),
			"edit"=>new template('<select name="{field_name}">'.$edit_str.'</select>'),
			);
	}
	
	function getOptionsShow($options,$selected)
	{
		$str = "";
		$cnt = 1;
		foreach($options as $val)
		{
			$str .= '<option value="'.$cnt.'"'.($selected==$cnt?'selected="selected"':'').'>'.$val.'</option>';
			$cnt++;
		}
		return $str;
	}
}

?>
