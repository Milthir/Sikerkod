<?

define("VISIT_TEMPLATE_CAT",1);
define("VISIT_TEMPLATE_VALUE",2);

define("VISIT_MESSAGE_ITEM_DEL_OK",1);

class visit extends forefather
{
	var $deep;
	var $args=Array();
	
	function visit($deep,$set_default_template=true)
	{
		if(!isNumber($deep))
		{
			report_error("visit::visit nem szám a deep(".$deep.")");	
			$deep = 0;
		}
		$this->deep = $deep;
		if($set_default_template)
		{	
		
			$this->message_text[VISIT_MESSAGE_ITEM_DEL_OK] = "Törlés sikeres";

			$this->templates[VISIT_TEMPLATE_CAT] = new template('<div style="border:1px dashed"><div style="background:#999999;">{cat_name}Összesen:{sum}<br>
																				<img src="visit_graph.php?kyz={conditions}&kxl={prefix}" alt="diagram" />																		
																	</div><div style="margin-left:10px">{body}</div></div>');
			$this->templates[VISIT_TEMPLATE_VALUE] = new template('<div style="border:1px dashed"><div style="background:#F0F0F0;">{cat_val}<br><form action="" method="post">{hidden_zone}
																	<input type="submit" value="Törlés" /></form></div><div style="margin-left:10px">{body}</div></div>');
		}
		else report_error("visit::visit nem számot kapott");
	}
	
	function install()
	{
		if($this->deep == 0)return ;
		mysql_query("CREATE TABLE IF NOT EXISTS `".$this->prefix."visit` 
					(`visit_time` INT UNSIGNED NOT NULL )");
		
		for ($i=1; $i<=$this->deep; $i++)
		{	
			mysql_query("ALTER TABLE `".$this->prefix."visit` ADD `visit_".$i."` INT UNSIGNED NOT NULL");
		}
		
	}

	function explicate($order,$conditions=Array(),$from=0)
	{
		$link_conds="";
		$picture_conds="";
		$result="";
		$count=0;
		foreach($this->args as $key=>$val)if($val!='') 
		{
			if(!$count)$count++;
			$link_conds.= ($count?" AND ":"")."`visit_".$key."`=".$val;
			$picture_conds.=($count?"_":"").$key."-".$val;
		}
		foreach($conditions as $key=>$val)
		{
			if($count)
			{
				$link_conds.=" AND ";
				$picture_conds.="_";
			}
			else $count++;
			$link_conds.="`visit_".$key."`=".$val;
			$picture_conds.=$key."-".$val;
		}
		foreach(doSelect("SELECT `visit_".$order[$from]."` FROM `".$this->prefix."visit` ".($link_conds==''?"":" WHERE ".$link_conds)." GROUP BY `visit_".$order[$from]."`") as $val)
		{
			$del_button_name="";
			$isnotfirst=0;
			$local_conditions=$conditions;
			$local_conditions[$order[$from]]=$val["visit_".$order[$from]];
			foreach($local_conditions as $key=>$val2)
			{
				if($isnotfirst)$del_button_name.="_";	
				else $isnotfirst++;
				$del_button_name.=$key."=".$val2;
			}
			$result.=$this->templates[VISIT_TEMPLATE_VALUE]->getTrans(Array("body"=>$this->explicate($order,$local_conditions,$from+1),
											 "cat_val"=>$val["visit_".$order[$from]],
											"hidden_zone"=>'<input type="hidden" name="action" value="del" /><input type="hidden" name="ids" value="'.$del_button_name.'" />'));
		}
		return $this->templates[VISIT_TEMPLATE_CAT]->getTrans(Array("body"=>$result, "cat_name"=>"<strong>".($order[$from])."</strong>".($order[$from]==NULL?"":"<br/>"), //itt a cat_name az hogy pl Fõoldal...(melyik oszlop)
																	"sum"=>selectMezo("SELECT count(*) cnt FROM `".$this->prefix."visit` ".($link_conds==''?"":" WHERE ".$link_conds),"cnt"),
																	"conditions"=>$picture_conds, "prefix"=>$this->prefix));
	}

	function visited()
	{
		foreach(func_get_args() as $val)if(!is_numeric($val)) 
		{
			report_error("visit(".$this->prefix.")::visited nem számot kapott");
			return false;
		}
		$attributes=func_get_args();
		$values=time();
		for ($i=0; $i<$this->deep; $i++) $values.=", ".($attributes[$i]==''?0:$attributes[$i]);
		return mysql_query("INSERT INTO `".$this->prefix."visit` VALUES (".$values.")"); 
	}

	function setArg($which, $value='')
	{
		if(is_numeric($which) && (is_numeric($value) || $value==''))
		{
			if($value=='')unset($this->args[$which]);
			else $this->args[$which]=$value;
			
			return true;
		}
		report_error("visit::setArg nem számot kapott");
		return false;
	}
	
	function process()
	{
		switch(getPOST("action","a"))
		{
			case 'del':
				if(isset($_POST['ids']))
				{
					$conds="";
					$count=0;
					foreach(explode("_",$_POST['ids']) as $val)
					{
						if (preg_match("/^([0-9]+)=([0-9]+)$/",$val,$pos))
						{					
							if($count)$conds.=" AND ";
							else $count++;
							$conds.="`visit_".$pos[1]."`=".$pos[2];
						}
					}
					mysql_query("DELETE FROM `".$this->prefix."visit` WHERE (".$conds.")");
					$this->message(VISIT_MESSAGE_ITEM_DEL_OK);
				}
 				break;	
		}
	}

	function show()
	{
		foreach(func_get_args() as $val)if(!is_numeric($val)) 
		{
			report_error("visit::show nem számot kapott");
			return false;
		}
		echo ($this->explicate(func_get_args()));
		return true;
	}
}
?>
