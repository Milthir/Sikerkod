<?

define("FORUM_PRIV_CAT_CREATE",1);
define("FORUM_PRIV_CAT_DEL",2);
define("FORUM_PRIV_CAT_MOD",4);
define("FORUM_PRIV_CAT_PUB",8);
define("FORUM_PRIV_TOPIC_CREATE",16);
define("FORUM_PRIV_TOPIC_DEL",32);
define("FORUM_PRIV_TOPIC_PUB",64);
define("FORUM_PRIV_POST_DEL",128);
define("FORUM_PRIV_POST_PUB",256);
define("FORUM_PRIV_PRIV_CHANGE",512);

define("FORUM_TEMPLATE_POSTLIST_ELEM",1);
define("FORUM_TEMPLATE_CATLIST_ELEM",2);
define("FORUM_TEMPLATE_TOPICLIST_ELEM",3);
define("FORUM_TEMPLATE_POSTVIEW",4);
define("FORUM_TEMPLATE_CATVIEW",5);
define("FORUM_TEMPLATE_NEWCAT_FORM",6);
define("FORUM_TEMPLATE_NEWTOPIC_FORM",7);
define("FORUM_TEMPLATE_PRIVPAGE",8);


define("FORUM_MESSAGE_PRIV_ERROR",1);
define("FORUM_MESSAGE_NOT_EMPTY_CAT",2);
define("FORUM_MESSAGE_SQL_FAILED",3);
define("FORUM_MESSAGE_DEL_BIZT",4);
define("FORUM_MESSAGE_MUST_LOGIN",5);
define("FORUM_MESSAGE_SEND_MESSAGE_OK",6);
define("FORUM_MESSAGE_PUB_CAT_OK",7);
define("FORUM_MESSAGE_UNPUB_CAT_OK",8);
define("FORUM_MESSAGE_DEL_CAT_OK",9);

define("FORUM_MESSAGE_PUB_TOPIC_OK",10);
define("FORUM_MESSAGE_UNPUB_TOPIC_OK",11);
define("FORUM_MESSAGE_DEL_TOPIC_OK",12);

define("FORUM_MESSAGE_PUB_POST_OK",13);
define("FORUM_MESSAGE_UNPUB_POST_OK",14);
define("FORUM_MESSAGE_DEL_POST_OK",15);

define("FORUM_URES_MEZO",16);

class forum extends forefather
{
	var $topic_id;
	var $cat_id;
	var $reply_to_id;

	var $akt_user;
	var $akt_privs;

	var $cat_get;
	var $topic_get;
	var $reply_to_get;

	var $post_pager_get;
	var $topic_pager_get;
	var $cat_pager_get;

	var $post_per_page;
	var $topic_per_page;
	var $cat_per_page;

	var $forum_text;

	var $smiles;


	function forum($akt_user,$prefix,$set_def_templates=true)
	{		$this->prefix = $prefix;
		$this->cat_get="c";
		$this->topic_get = "t";
		$this->reply_to_get = "rt";

		$this->post_pager_get = "pp";
		$this->topic_pager_get = "tp";
		$this->cat_pager_get = "cp";

		$this->post_per_page = 10;
		$this->topic_per_page = 5;
		$this->cat_per_page = 5;

		$this->akt_user = safeNumber($akt_user,0);		$this->smiles = array();

		if(isNumber(getGET($this->topic_get,'a')))$this->topic_id = $_GET[$this->topic_get];
		else $this->topic_id = 0;
		
		if($this->topic_id)
		{
			$this->cat_id = $this->getCatFromTopic($this->topic_id);		}
		else 
		{
			if(isNumber(getGET($this->cat_get,'a')))$this->cat_id = $_GET[$this->cat_get];
			else $this->cat_id = 0;
		}

		$this->reply_to_id = 0;
		if(isNumber(getGET($this->reply_to_get,'a')))
		{
			$this->reply_to_id = $_GET[$this->reply_to_get];
		}

		$this->akt_privs = $this->getPriv();

		$this->message_text = array(
						FORUM_MESSAGE_PRIV_ERROR		=>"Nincs jog a művelethez.",
						FORUM_MESSAGE_NOT_EMPTY_CAT		=>"A törölendő kategória nem tartalmazhat alkategóriát!",
						FORUM_MESSAGE_SQL_FAILED		=>"Adatbázishiba történt a művelet nemfejeződött be teljesen.",
						FORUM_MESSAGE_DEL_BIZT			=>"A törléshez a biztonsági négyzetet be kell jelölni.",
						FORUM_MESSAGE_MUST_LOGIN		=>"Üzenet küldéséhez be kell jelentkezni!",
						FORUM_MESSAGE_SEND_MESSAGE_OK	=>"Üzenet elküldése sikeres!",
						FORUM_MESSAGE_PUB_CAT_OK		=>"Kategória publikálása sikeres!",
						FORUM_MESSAGE_UNPUB_CAT_OK		=>"Kategória publikálásának visszavonása sikeres!",
						FORUM_MESSAGE_DEL_CAT_OK		=>"Kategória törlése sikeres!",

						FORUM_MESSAGE_PUB_TOPIC_OK		=>"Topik publikálása sikeres!",
						FORUM_MESSAGE_UNPUB_TOPIC_OK	=>"Topik publikálásának visszavonása sikeres!",
						FORUM_MESSAGE_DEL_TOPIC_OK		=>"Topik törlése sikeres!",

						FORUM_MESSAGE_PUB_POST_OK		=>"Üzenet publikálása sikeres!",
						FORUM_MESSAGE_UNPUB_POST_OK		=>"Üzenet publikálásának visszavonása sikeres!",
						FORUM_MESSAGE_DEL_POST_OK		=>"Üzenet törlése sikeres!",


						FORUM_URES_MEZO					=>"Mi értelme ha nem töltöd ki?"
					);



		if($set_def_templates)
		{
			$this->templates[FORUM_TEMPLATE_POSTLIST_ELEM] = new template('					<div class="forum_postlist_elem" style="margin-top:10px;border:1px outset;background:#D8D8D8">						<div class="forum_postlist_head">{post_sender} ({post_time:Y-m-d H:i.s}) 							[if {post_reply_to}!=0]Válasz {reply_to_user} {reply_to_post_time:Y-m-d H:i.s} üzenetére[/if]							<a href="{reply_to_link}">Válasz erre az üzenetre.</a>						</div>					{post_body}						[if {pub_priv}==1||{del_priv}==1]							<form method="post" action="">{hidden_zone}							[if {pub_priv}==1]<input type="submit" name="{pub}" value="[if {post_pub}==1]Publikálás visszavonása[else]Publikálás[/if]" />[/if]							[if {del_priv}==1]<input type="submit" name="{del}" value="Törlés" /><input type="checkbox" name="{del_bizt}" />[/if]							</form>						[/if]					</div>');

			$this->templates[FORUM_TEMPLATE_CATLIST_ELEM] = new template('				<div class="forum_catlist_elem" style="border:1px dashed;"> 					<a href="{link}">{cat_name}</a> 					[if {pub_priv}==1||{del_priv}==1] 						<form method="post" action=""> {hidden_zone} 							[if {pub_priv}==1]<input type="submit" name="{pub}" value="[if {cat_pub}==1]Publikálás visszavonása[else]Publikálás[/if]" />[/if]							[if {del_priv}==1]<input type="submit" name="{del}" value="Törlés" /> <input type="checkbox" name="{del_bizt}" />[/if]						</form> 					[/if]				</div>');

			$this->templates[FORUM_TEMPLATE_TOPICLIST_ELEM] = new template('				<div class="forum_topiclist_elem"> 					<a href="{link}">{topic_name}</a>					[if {pub_priv}==1||{del_priv}==1] 						<form method="post" action=""> {hidden_zone} 							[if {pub_priv}==1]<input type="submit" name="{pub}" value="[if {topic_pub}==1]Publikálás visszavonása[else]Publikálás[/if]" />[/if]							[if {del_priv}==1]<input type="submit" name="{del}" value="Törlés" /> <input type="checkbox" name="{del_bizt}" />[/if]						</form> 					[/if]				</div>');

			$this->templates[FORUM_TEMPLATE_POSTVIEW] = new template('				<div class="forum_postlist" style="border:2px solid;">					{navigator}					{pager}{body}{pager}<hr />					<form method="post" action="">						[if {reply_to_id}!=0]Válasz {reply_to} üzenetére[/if]{hidden_zone}						<textarea name="{textarea}" rows="10" cols="30"></textarea>						<input type="submit" />					</form>				</div>');

			$this->templates[FORUM_TEMPLATE_CATVIEW] = new template('				<div class="forum_catlist" style="border:2px solid;">KATEGÓRIÁK<br />					{navigator}					{cat_pager}{cat_body}{cat_pager}<hr />					{new_cat_form}				</div>				<div class="forum_topiclist" style="border:2px solid;">TOPIKOK<br />					{topic_pager}{topic_body}{topic_pager}<hr />					{new_topic_form}				</div>');

			$this->templates[FORUM_TEMPLATE_NEWCAT_FORM] = new template('				<fieldset>					<legend>Új kategória</legend>					<form method="post" action="">{hidden_zone}						<input type="text" name="{cat_name_input_name}" />						<input type="submit" />					</form>				</fieldset>');

			$this->templates[FORUM_TEMPLATE_NEWTOPIC_FORM] = new template('				<fieldset>					<legend>Új topic</legend>					<form method="post" action="">{hidden_zone}						<input type="text" name="{topic_name_input_name}" />						<input type="submit" />					</form>				</fieldset>');

		}
	}

	function addSmile($short,$url,$name)
	{
		$this->smiles[] = array("short"=>$short,"url"=>$url,"name"=>$name);
	}


	function debug()
	{
		?>
		Topic id: <?=$this->topic_id;?><br />
		Cat id: <?=$this->cat_id;?><br />
		Akt user id: <?=$this->akt_user;?><br />
		Akt privs id: <?=$this->akt_privs;?><br />
		Cat get: <?=$this->cat_get;?><br />
		Topic get: <?=$this->topic_get;?><br />
		Reply to get: <?=$this->reply_to_get;?><br />
		<?
	}


	
	function install()
	{
		if(!mysql_query(
		"CREATE TABLE `".$this->prefix."cat` (".
		"`cat_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,".
		"`cat_name` TEXT NOT NULL ,".
		"`cat_parent` INT UNSIGNED NOT NULL ,".
		"`cat_created_by` INT UNSIGNED NOT NULL ,".
		"`cat_created_time` INT UNSIGNED NOT NULL ,".
		"`cat_pub` TINYINT( 1 ) DEFAULT '1' NOT NULL ,".
		"PRIMARY KEY ( `cat_id` ) );"))
		{
			return false;
		}
		/*
		topic 
			topic_id
			topic_cat
			topic_name
			topic_created_by
			topic_created_time
			topic_pub
		*/
		if(!mysql_query(
		"CREATE TABLE `".$this->prefix."topic` (".
		"`topic_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,".
		"`topic_name` TEXT NOT NULL ,".
		"`topic_cat` INT UNSIGNED NOT NULL ,".
		"`topic_created_by` INT UNSIGNED NOT NULL ,".
		"`topic_created_time` INT UNSIGNED NOT NULL ,".
		"`topic_pub` TINYINT( 1 ) DEFAULT '1' NOT NULL ,".
		"PRIMARY KEY ( `topic_id` ) );"))
		{
			return false;
		}

		/*
		post
			post_id
			post_topic
			post_time
			post_sender
			post_body
			post_reply_to
			post_pub
		*/

		if(!mysql_query(
		"CREATE TABLE `".$this->prefix."post` (".
		"`post_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,".
		"`post_topic` INT UNSIGNED NOT NULL ,".
		"`post_time` INT UNSIGNED NOT NULL ,".
		"`post_sender` INT UNSIGNED NOT NULL ,".
		"`post_body` TEXT NOT NULL ,".
		"`post_reply_to` INT UNSIGNED NOT NULL ,".
		"`post_pub` TINYINT( 1 ) DEFAULT '1' NOT NULL ,".
		"PRIMARY KEY ( `post_id` ) );"))
		{
			return false;
		}

		/*
		privs
			privs_user_id
			privs_cat_id
			privs_privs
		*/
		if(!mysql_query(
		"CREATE TABLE `".$this->prefix."privs` (".
		"`privs_user_id` INT UNSIGNED NOT NULL ,".
		"`privs_cat_id` INT UNSIGNED NOT NULL ,".
		"`privs_privs` INT UNSIGNED NOT NULL ,".
		"PRIMARY KEY ( `privs_user_id` , `privs_cat_id` ));"))
		{
			return false;
		}
		return true;
	}




	function process()
	{
		switch(getPOST('action',''))
		{
			case 'forum_send':
				if(isInPOST(array("body")))
				{
					if($this->sendMessage($_POST['body'],getGET($this->reply_to_get,0)))
						$this->message(FORUM_MESSAGE_SEND_MESSAGE_OK);
				}
				break;
			case 'cat_operation':
				if(isset($_POST['cat_id']))
				{
					if(isset($_POST['pub']))
					{
						if($this->pubCat($_POST['cat_id'],true))
							$this->message(FORUM_MESSAGE_PUB_CAT_OK);
					}
					else if (isset($_POST['unpub']))
					{
						if($this->pubCat($_POST['cat_id'],false))
							$this->message(FORUM_MESSAGE_UNPUB_CAT_OK);
					}
					else if (isset($_POST['del']))
					{
						if(isset($_POST['del_bizt'])) 
						{
							if($this->delCat($_POST['cat_id']))
								$this->message(FORUM_MESSAGE_DEL_CAT_OK);
						}
						else $this->message(FORUM_MESSAGE_DEL_BIZT);
					}
				}
				break;
			case 'topic_operation':
				if(isset($_POST['topic_id']))
				{
					if(isset($_POST['pub']))
					{
						if($this->pubTopic($_POST['topic_id'],true))
							$this->message(FORUM_MESSAGE_PUB_TOPIC_OK);
					}
					else if (isset($_POST['unpub']))
					{
						if($this->pubTopic($_POST['topic_id'],false))
							$this->message(FORUM_MESSAGE_UNPUB_TOPIC_OK);
					}
					else if (isset($_POST['del']))
					{
						if(isset($_POST['del_bizt']))
						{
							if($this->delTopic($_POST['topic_id']))
								$this->message(FORUM_MESSAGE_DEL_TOPIC_OK);
						}
						else $this->message(FORUM_MESSAGE_DEL_BIZT);
					}
				}
				break;
			case 'post_operation':
				if(isset($_POST['post_id']))
				{
					if(isset($_POST['pub']))
					{
						if($this->pubMessage($_POST['post_id'],true))
							$this->message(FORUM_MESSAGE_PUB_POST_OK);
					}
					else if (isset($_POST['unpub']))
					{
						if($this->pubMessage($_POST['post_id'],false))
							$this->message(FORUM_MESSAGE_UNPUB_POST_OK);
					}
					else if (isset($_POST['del']))
					{
						if(isset($_POST['del_bizt']))
						{
							if($this->delMessage($_POST['post_id']))
								$this->message(FORUM_MESSAGE_DEL_POST_OK);
						}
						else $this->message(FORUM_MESSAGE_DEL_BIZT);
					}
				}
				break;
			case 'new_topic':
				if(isset($_POST['topic_name']))
				{
					$this->createTopic($_POST['topic_name']);
				}
				break;
			case 'new_cat':
				if(isset($_POST['cat_name']))
				{
					$this->createCat($_POST['cat_name']);
				}
				break;
		}
	}


	function show()
	{
		$this->process();
		
		if($this->topic_id)
		{
			$akt_topic = selectOne("SELECT * FROM `".$this->prefix."topic` WHERE `topic_id`=".$this->topic_id);

			$posts_str = "";

			$post_where = "WHERE `post_topic`=".$this->topic_id.($this->isHavePriv(FORUM_PRIV_POST_PUB)?"":" AND `post_pub`=1")."  ORDER BY `post_time` DESC";

			$post_pager = new lapozo($this->prefix.'post',$post_where,$this->post_pager_get,$this->post_per_page); 

			foreach(doSelect("SELECT * FROM `".$this->prefix."post` ".$post_where." ".$post_pager->getLimit()) as $post)
			{				$post['post_body'] = nl2br($post['post_body']);
				foreach($this->smiles as $val)
				{
					if(is_array($val['short'])) 
					{
						foreach($val['short'] as $val2)
						{
							$post['post_body'] = str_replace($val2,'<img src="'.$val['url'].'" alt="'.$val['name'].'" />',$post['post_body']);
						}
					}
					else $post['post_body'] = str_replace($val['short'],'<img src="'.$val['url'].'" alt="'.$val['name'].'" />',$post['post_body']);

				}
				$reply_to_v = 0;
				if($post['post_reply_to'])
				{
					$reply_to_v = selectOne("SELECT `post_time`,`post_sender` FROM `".$this->prefix."post` WHERE `post_id`=".$post['post_reply_to']);
				}
				$posts_str .= $this->templates[FORUM_TEMPLATE_POSTLIST_ELEM]->getTrans(
									array_merge($post,array(
									"reply_to_user"=>$reply_to_v?$reply_to_v['post_sender']:0,
									"reply_to_post_id"=>$post['post_reply_to'],
									"reply_to_post_time"=>$reply_to_v?$reply_to_v['post_time']:0,
									"reply_to_link"=>addDelGET(array($this->reply_to_get=>$post['post_id']),array("rt")),
									"pub"=>$post['post_pub']?"unpub":"pub",
									"del"=>"del",
									"del_bizt"=>"del_bizt",
									"hidden_zone"=>'<input type="hidden" name="action" value="post_operation"><input type="hidden" name="post_id" value="'.$post['post_id'].'">',
									"del_priv"=>$this->isHavePriv(FORUM_PRIV_POST_DEL)?1:0,
									"pub_priv"=>$this->isHavePriv(FORUM_PRIV_POST_PUB)?1:0
										)));		
			}

			echo $this->templates[FORUM_TEMPLATE_POSTVIEW]->getTrans(array(
														"body"=>$posts_str,
														"pager"=>$post_pager->getButtons(),
														"reply_to"=>$this->getUserFromPost($this->reply_to_id),
														"reply_to_id"=>$this->reply_to_id,
														"hidden_zone"=>'<input type="hidden" name="action" value="forum_send">',
														"textarea"=>'body',
														"navigator"=>$this->getPosition($this->cat_id),
														"position"=>$akt_topic['topic_name'],
														"user_id"=>isNumber($this->akt_user)?$this->akt_user:0
														));
			
		}
		else 
		{
			$akt_cat = selectOne("SELECT * FROM `".$this->prefix."cat` WHERE `cat_id`=".$this->cat_id);


			$cat_where = "WHERE `cat_parent`=".$this->cat_id." ".($this->isHavePriv(FORUM_PRIV_CAT_PUB)?"":" AND `cat_pub`=1")."  ORDER BY `cat_name` ";

			$cat_pager = new lapozo($this->prefix.'cat',$cat_where,$this->cat_pager_get,$this->cat_per_page); 

			$catlist_str = "";
			
			$cat_sorsz=0;

			foreach(doSelect("SELECT * FROM `".$this->prefix."cat` ".$cat_where." ".$cat_pager->getLimit()) as $cat)
			{
				$catlist_str .= $this->templates[FORUM_TEMPLATE_CATLIST_ELEM]->getTrans(
											array_merge($cat,array(
										"link"=>addDelGET(array($this->cat_get=>$cat['cat_id']),array("rt")),
										"pub"=>$cat['cat_pub']?"unpub":"pub",
										"del"=>"del",
										"del_bizt"=>"del_bizt",
										"hidden_zone"=>'<input type="hidden" name="action" value="cat_operation"><input type="hidden" name="cat_id" value="'.$cat['cat_id'].'">',
										"del_priv"=>$this->isHavePriv(FORUM_PRIV_CAT_DEL)?1:0,
										"pub_priv"=>$this->isHavePriv(FORUM_PRIV_CAT_PUB)?1:0,
										"sorszam"=>$cat_sorsz++,
										"created_by"=>$cat['cat_created_by']
											)));
			}



			$topic_where = "WHERE `topic_cat`=".$this->cat_id." ".($this->isHavePriv(FORUM_PRIV_TOPIC_PUB)?"":" AND `topic_pub`=1")." ORDER BY `topic_name` ";

			$topic_pager = new lapozo($this->prefix.'topic',$topic_where,$this->topic_pager_get,$this->topic_per_page); 
			
			$topic_sorsz = 0;

			$topiclist_str = "";
			foreach(doSelect("SELECT * FROM `".$this->prefix."topic` ".$topic_where." ".$topic_pager->getLimit()) as $topic)
			{
				$topiclist_str .= $this->templates[FORUM_TEMPLATE_TOPICLIST_ELEM]->getTrans(
										array_merge($topic,array(
										"link"=>addDelGET(array($this->topic_get=>$topic['topic_id']),array("rt")),
										"pub"=>$topic['topic_pub']?"unpub":"pub",
										"del"=>"del",
										"del_bizt"=>"del_bizt",
										"hidden_zone"=>'<input type="hidden" name="action" value="topic_operation"><input type="hidden" name="topic_id" value="'.$topic['topic_id'].'">',
										"del_priv"=>$this->isHavePriv(FORUM_PRIV_TOPIC_DEL)?1:0,
										"pub_priv"=>$this->isHavePriv(FORUM_PRIV_TOPIC_PUB)?1:0,
										"sorszam"=>$topic_sorsz++,
										"created_by"=>$topic['topic_created_by'],
										"last_msg"=>selectMezo("SELECT max(`post_time`) as maxtime FROM `".$this->prefix."post` WHERE `post_topic`=".$topic['topic_id'],"maxtime")
											)));
			}

			echo $this->templates[FORUM_TEMPLATE_CATVIEW]->getTrans(
												array(
												"cat_pager"=>$cat_pager->getButtons(),
												"cat_body"=>$catlist_str,
												"topic_cnt"=>$topic_sorsz,
												"cat_cnt"=>$cat_sorsz,
												"cat_del_priv"=>$this->isHavePriv(FORUM_PRIV_CAT_DEL)?1:0,
												"cat_pub_priv"=>$this->isHavePriv(FORUM_PRIV_CAT_PUB)?1:0,
												"topic_del_priv"=>$this->isHavePriv(FORUM_PRIV_TOPIC_DEL)?1:0,
												"topic_pub_priv"=>$this->isHavePriv(FORUM_PRIV_TOPIC_PUB)?1:0,
												"new_cat_form"=>$this->isHavePriv(FORUM_PRIV_CAT_CREATE)?$this->templates[FORUM_TEMPLATE_NEWCAT_FORM]->getTrans(array(
																				"cat_name_input_name"=>"cat_name",
																				"hidden_zone"=>'<input type="hidden" name="action" value="new_cat" />'
																				)):"",
												"topic_pager"=>$topic_pager->getButtons(),
												"topic_body"=>$topiclist_str,
												"new_topic_form"=>$this->isHavePriv(FORUM_PRIV_TOPIC_CREATE)?$this->templates[FORUM_TEMPLATE_NEWTOPIC_FORM]->getTrans(array(
																				"topic_name_input_name"=>"topic_name",
																				"hidden_zone"=>'<input type="hidden" name="action" value="new_topic" />'	
																				)):"",
												"navigator"=>$this->getPosition($this->cat_id),
												"position"=>$akt_cat['cat_name']
													));
			
		}
	}





	function isHavePriv($priv,$cat=-1,$user=-1)
	{
		if($this->admin_mode) return true;
		if(!isNumber($cat) || $cat == -1)$cat = $this->cat_id;
		if(!isNumber($user) || $user == -1)$user = $this->akt_user;

		if($this->cat_id != $cat || $this->akt_user != $user)
		{
			if(selectMezo("SELECT `privs_privs` FROM `".$this->prefix."privs` WHERE (`privs_user_id`=".($user?$user:$this->akt_user)." || `privs_user_id`=0) AND ".
																	  "`privs_cat_id`=".($cat?$cat:$this->cat_id)   ,'privs_privs')&$priv) return true;
			else if($cat) return $this->isHavePriv($priv,$this->getCatParent($cat),$user);
			else return false;
		}
		return $this->akt_privs & $priv;
	}
	function getPriv($cat=-1,$user=-1,$reqursive=true)
	{
		if(!isNumber($cat) || $cat == -1)$cat = $this->cat_id;
		if(!isNumber($user) || $user == -1)$user = $this->akt_user;
		$priv = selectMezo("SELECT `privs_privs` FROM `".$this->prefix."privs` WHERE `privs_user_id`=".$user." AND ".
																	  "`privs_cat_id`=".$cat   ,'privs_privs');		$priv |= selectMezo("SELECT `privs_privs` FROM `".$this->prefix."privs` WHERE `privs_user_id`=0 AND ".
																	  "`privs_cat_id`=".$cat   ,'privs_privs');																	  
		if($cat && $reqursive) return $priv|($this->getPriv($this->getCatParent($cat),$user));
		else return $priv;
	}

	function setPriv($priv,$cat=-1,$user=-1)
	{
		if(!isNumber($cat) || $cat == -1)$cat = $this->cat_id;
		if(!isNumber($user) || $user == -1)$user = $this->akt_user;

		if($this->isHavePriv(FORUM_PRIV_PRIV_CHANGE,$cat))
		{
			$akt_priv = selectMezo("SELECT `privs_privs` FROM `".$this->prefix."privs` WHERE `privs_cat_id`=".$cat." AND `privs_user_id`=".$user,"privs_privs");

			return mysql_query("UPDATE `".$this->prefix."privs` SET `privs_privs`=".($priv|$akt_priv)." WHERE `privs_cat_id`=".$cat." AND `privs_user_id`=".$user);
		}
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}

	function unsetPriv($priv,$cat=-1,$user=-1)
	{
		if(!isNumber($cat) || $cat == -1)$cat = $this->cat_id;
		if(!isNumber($user) || $user == -1)$user = $this->akt_user;

		if($this->isHavePriv(FORUM_PRIV_PRIV_CHANGE,$cat))
		{
			$akt_priv = selectMezo("SELECT `privs_privs` FROM `".$this->prefix."privs` WHERE `privs_cat_id`=".$cat." AND `privs_user_id`=".$user,"privs_privs");

			return mysql_query("UPDATE `".$this->prefix."privs` SET `privs_privs`=".((65535^$priv)&$akt_priv)." WHERE `privs_cat_id`=".$cat." AND `privs_user_id`=".$user);
		}
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}

	function delPrivs2Cat($cat)
	{
		return mysql_query("DELETE FROM `".$this->prefix."privs` WHERE `privs_cat_id`=".$cat);
	}






	function sendMessage($body,$reply_to=0)
	{
		if(trim($body) == "")return $this->message(FORUM_URES_MEZO);
		if(isNumber($reply_to) && $this->akt_user && isNumber($this->akt_user))
			return mysql_query("INSERT INTO `".$this->prefix."post`(`post_sender`,`post_body`,`post_time`,`post_topic`,`post_reply_to`) ".
						"VALUES(".$this->akt_user.",'".specChars($body,'forum::sendMessage')."',".time().",".$this->topic_id.",".$reply_to.")");
		else return $this->message(FORUM_MESSAGE_MUST_LOGIN);
	}
	function delMessage($id)
	{
		if(isNumber($id) && $this->isHavePriv(FORUM_PRIV_POST_DEL,$this->getCatFromPost($id)))
			return mysql_query("DELETE FROM `".$this->prefix."post` WHERE `post_id`=".$id);
		else 
			return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}
	function pubMessage($id,$is_true=true)
	{
		if(isNumber($id) && $this->isHavePriv(FORUM_PRIV_POST_PUB,$this->getCatFromPost($id)))
			return mysql_query("UPDATE `".$this->prefix."post` SET `post_pub`=".($is_true?1:0)." WHERE `post_id`=".$id);
		else 
			return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}



	function createTopic($name)
	{
		if(trim($name) == "")return $this->message(FORUM_URES_MEZO);
		if($this->isHavePriv(FORUM_PRIV_TOPIC_CREATE))
			return mysql_query(	"INSERT INTO `".$this->prefix."topic`(`topic_name`,`topic_cat`,`topic_created_by`,`topic_created_time`) ".
							"VALUES('".specChars($name,"forum::createTopic")."',".$this->cat_id.",".$this->akt_user.",".time().")");
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}

	function delTopic($id)
	{
		if(isNumber($id) && $this->isHavePriv(FORUM_PRIV_TOPIC_DEL,$this->getCatFromTopic($id)))
		{
			return mysql_query("DELETE FROM `".$this->prefix."post` WHERE `post_topic`=".$id.";")&mysql_query("DELETE FROM `".$this->prefix."topic` WHERE `topic_id`=".$id);
		}
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}

	function pubTopic($id,$is_true=true)
	{
		if(isNumber($id) && $this->isHavePriv(FORUM_PRIV_TOPIC_PUB,$this->getCatFromTopic($id)))
				return mysql_query("UPDATE `".$this->prefix."topic` SET `topic_pub`=".($is_true?1:0)." WHERE `topic_id`=".$id);
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}

	function createCat($name)
	{
		if(trim($name) == "")return $this->message(FORUM_URES_MEZO);
		if($this->isHavePriv(FORUM_PRIV_CAT_CREATE))
			return mysql_query(	"INSERT INTO `".$this->prefix."cat`(`cat_name`,`cat_parent`,`cat_created_by`,`cat_created_time`) ".
							"VALUES('".specChars($name,"forum::createCat")."',".$this->cat_id.",".$this->akt_user.",".time().")");
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}
	function delCat($id)
	{
		if(isNumber($id) && $this->isHavePriv(FORUM_PRIV_CAT_DEL))
		{
			if(selectMezo("SELECT count(*) FROM `".$this->prefix."cat` WHERE `cat_parent`=".$id,"count(*)") == 0)
			{
				$okes = true;
				foreach(doSelect("SELECT `topic_id` FROM `".$this->prefix."topic` WHERE `topic_cat`=".$id) as $topic)
				{
					$okes &= $this->delTopic($topic['topic_id']);
				}
				if(!$okes)return $this->message(FORUM_MESSAGE_SQL_FAILED);
				$this->delPrivs2Cat($id);

				if($okes)return mysql_query("DELETE FROM `".$this->prefix."cat` WHERE `cat_id`=".$id);
				else return $this->message(FORUM_MESSAGE_SQL_FAILED);
			}
			else return $this->message(FORUM_MESSAGE_NOT_EMPTY_CAT);
		}
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}
	function pubCat($id,$is_true=true)
	{
		if(isNumber($id) && $this->isHavePriv(FORUM_PRIV_CAT_PUB))
			return mysql_query("UPDATE `".$this->prefix."cat` SET `cat_pub`=".($is_true?1:0)." WHERE `cat_id`=".$id);
		else return $this->message(FORUM_MESSAGE_PRIV_ERROR);
	}








	
	function getCatParent($id)
	{
		if(reportedIsNumber($id,"forum::getCatParent"))return selectMezo("SELECT `cat_parent` FROM `".$this->prefix."cat` WHERE `cat_id`=".$id,"cat_parent");
		return 0;
	}
	function getCatFromTopic($id)
	{		if(reportedIsNumber($id,"forum::getCatFromTopic"))return selectMezo("SELECT `topic_cat` FROM `".$this->prefix."topic` WHERE `topic_id`=".$id,"topic_cat");
		return 0;
	}
	function getCatFromPost($id)
	{
		if(reportedIsNumber($id,"forum::getCatFromPost"))return $this->getCatFromTopic(selectMezo("SELECT `post_topic` FROM `".$this->prefix."post` WHERE `post_id`=".$id,"post_topic"));
		return 0;
	}
	function getUserFromPost()
	{
		return selectMezo("SELECT `post_sender` FROM `".$this->prefix."post` WHERE `post_id`=".$this->reply_to_id,'post_sender');
	}





	function getPosition($id,$first=true)
	{		if(reportedIsNumber($id,"forum::getPosition"))		{
			$str = "";

			$akt_v = selectOne("SELECT `cat_parent`,`cat_name` FROM `".$this->prefix."cat` WHERE `cat_id`=".$id);
		
			if($id)
			{
				$str .= $this->getPosition($akt_v['cat_parent'],false);
				$str .= ' &gt;&gt; <a href="'.addDelGet(array($this->cat_get=>$id),array($this->topic_get,"rt")).'">'.$akt_v['cat_name']."</a>";
			}
			else $str .= '<a href="'.delGet(array($this->cat_get,$this->topic_get,"rt")).'">Főkönyvtár</a>';

			if($this->topic_id && $first)  
			{
				$str .= ' &gt;&gt; <a href="'.addDelGET(array($this->topic_get=>$this->topic_id),array("rt")).'">'.selectMezo("SELECT `topic_name` FROM `".$this->prefix."topic` WHERE `topic_id`=".$this->topic_id,'topic_name')." topic</a>";
			}			return $str;		}		return "";
	}

	function echoPosition($id)
	{
		echo $this->getPosition($id);
	}
}


?>
