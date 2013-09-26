<? 
define("BANNERS_MESSAGES_NOT_AREA_NAME",1);
define("BANNERS_MESSAGES_ISSET_AREA",2);
define("BANNERS_MESSAGES_ISSET_CONTACT",3);
define("BANNERS_MESSAGES_NOT_RENAME",4);
define("BANNERS_MESSAGES_NOT_UPFILE",5);
define("BANNERS_MESSAGES_UPLOAD_NOTVALID",6);
define("BANNERS_MESSAGES_NOT_AREA",7);
define("BANNERS_MESSAGES_ISNOTNUMBER",8);
define("BANNERS_MESSAGES_ISNOTSIZE",9);
define("BANNERS_MESSAGES_BIGNUMBER",10);
define("BANNERS_MESSAGES_ISNOTSELECT",11);
define("BANNERS_MESSAGES_UPLOAD_NAME",12);
define("BANNERS_MESSAGES_NOT_RENAME_AREA",13);
define("BANNERS_MESSAGES_NOT_HREF",14);

define("BANNERS_TEMPLATE_MAIN",1);
define("BANNERS_TEMPLATE_AREA",2);
define("BANNERS_TEMPLATE_AREA_UPLOAD_FORM",3);
define("BANNERS_TEMPLATE_AREA_RENAME_DEL_FORM",4);
define("BANNERS_TEMPLATE_BANNER",5);
define("BANNERS_TEMPLATE_BANNER_UPLOAD_FORM",6);
define("BANNERS_TEMPLATE_BANNER_RENAME_DEL_FORM",7);
define("BANNERS_TEMPLATE_SHOW_SWF",8);
define("BANNERS_TEMPLATE_SHOW_IMAGE",9);
define("BANNERS_TEMPLATE_CONTACT",10);
define("BANNERS_TEMPLATE_CONTACT_IMAGE",11);
define("BANNERS_TEMPLATE_CONTACT_SWF",12);
define("BANNERS_TEMPLATE_CONTACT_NAME",13);
define("BANNERS_TEMPLATE_CONTACT_DEL",14);

class banners extends forefather
{
	var $upload_dir;
	public function banners($dir,$set_templates=true)
	{

		$this->message_text[BANNERS_MESSAGES_NOT_AREA_NAME] = "Nem adta meg a létrehozni kívánt terület nevét!";
		$this->message_text[BANNERS_MESSAGES_ISSET_AREA] = "Adatbázis hiba vagy már létezik ilyen nevű terület!";
		$this->message_text[BANNERS_MESSAGES_ISSET_CONTACT] = "Ez a hozzárendelés már létezik!";
		$this->message_text[BANNERS_MESSAGES_NOT_RENAME] = "Nem adott meg fájlnevet!";
		$this->message_text[BANNERS_MESSAGES_NOT_RENAME_AREA] = "Nem adta meg a terület új nevét!";
		$this->message_text[BANNERS_MESSAGES_NOT_UPFILE] = "Nem adta meg a feltölteni kívánt fájlt!";
		$this->message_text[BANNERS_MESSAGES_UPLOAD_NOTVALID] = "Nem megfelelő fájlformátum!";
		$this->message_text[BANNERS_MESSAGES_NOT_AREA] = "Még nem hozott létre területet!";
		$this->message_text[BANNERS_MESSAGES_ISNOTNUMBER] = "Nem számot adott meg a terület méretéhez!";
		$this->message_text[BANNERS_MESSAGES_ISNOTSIZE] = "Nem adta meg a terület méretét!";
		$this->message_text[BANNERS_MESSAGES_BIGNUMBER] = "A szélesség min 10 max 1024 a magasság min 10 max 768 lehet!";
		$this->message_text[BANNERS_MESSAGES_ISNOTSELECT] = "Adatbázis hiba, vagy nem jelölt ki semmit!";
		$this->message_text[BANNERS_MESSAGES_UPLOAD_NAME] = "Nem megengedett karakterek a fájlnévben!";
		$this->message_text[BANNERS_MESSAGES_ISCONTACT] = "";
		$this->message_text[BANNERS_MESSAGES_NOT_HREF] = "Nem adta meg a linket!";

		$this->upload_dir = $dir;
	
		if($set_templates)
		{
			$this->templates[BANNERS_TEMPLATE_AREA] = new template('<option id="{area_id}" value="{area_id}">{area_name}</option><br />');
			$this->templates[BANNERS_TEMPLATE_BANNER] = new template('<option value="{banner_id}">{banner_name}</option><br />');
			$this->templates[BANNERS_TEMPLATE_CONTACT] = new template('
				<tr>
					<td style="border: solid 1px #BC002D; border-spacing: 0px;">
						{contact_image}
					</td>
					<td style="border: solid 1px #BC002D; border-spacing: 0px;">
						{contact_name}
					</td>
					<td style="border: solid 1px #BC002D; border-spacing: 0px;">
						{contact_del}
					</td>
				</tr>');
			$this->templates[BANNERS_TEMPLATE_MAIN] = new template('
			<script type="text/javascript">
			function isNumberKey(evt)
			{
				var charCode = (evt.which) ? evt.which : event.keyCode
				if (charCode > 31 && (charCode < 48 || charCode > 57))
					return false;
				return true;
			}
			</script>
			<table style="border: solid 1px #BC002D; border-spacing: 0px; width: 100%;">
				<tr>
					<td style="vertical-align: top; border: solid 1px #BC002D; border-spacing: 0px">
						Terület létrehozása:<br />
						{area_upload_form}
						<form action="" method="POST">
							<select name="delete_area">
								{areas}
							</select>
							{area_rename_del_form}
						</form>
					</td>
					<td style="vertical-align: top; border: solid 1px #BC002D; border-spacing: 0px">
						Bannerek feltöltése: <br />
						{banner_upload_form}
						<form action="" method="POST">
							<select name="delete_file">
								{banners}
							</select>
							{banner_rename_del_form}
						</form>
					</td>
					<td style="vertical-align: top; border: solid 1px #BC002D; border-spacing: 0px">
						Bannerek hozzárendelés az egyes területekhez: <br />
						<form action="" method="POST">
							<select name="select_banner">
								{banners}
							</select>
							<input type="submit" value="<<--Hozzárendel!-->>" />
							<select name="select_area">
								{areas}
							</select>
							<input type="hidden" name="action" value="to_area" />
						</form>
					</td>
					<td style="vertical-align: top; border: solid 1px #BC002D; border-spacing: 0px">
						Megmutatja, hogy mely területhez <br /> mely bannerek vannak rendelve: <br />
						<form action="" method="GET" id="contact_form">
							<select name="sel_area">
								{areas}
							</select>
							<input type="submit" value="Mutasd!" />
							<input type="hidden" name="action" value="contact_banner" />
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center; border: solid 1px #BC002D; border-spacing: 0px">
						{images}
					</td>
					<td colspan="2" style="text-align: center; border: solid 1px #BC002D; border-spacing: 0px">
						<form action="" method="POST">
							<table cellspancing="0" cellpadding="2" style="border: solid 1px #BC002D; border-spacing: 0px; text-align: center; margin-left: auto; margin-right: auto;">
								{contacts}
							</table>
						</form>
					</td>
				</tr>
			</table>');
			$this->templates[BANNERS_TEMPLATE_AREA_UPLOAD_FORM] = new template('
			<form action="" method="post">
				Név: <input type="text" name="make_area_name" /><br />
				Szélesség: <input type="text" size="4" maxlength="4" name="make_area_width" onkeypress="return isNumberKey(event);" />
				Magasság: <input type="text" size="4" maxlength="4" name="make_area_height" onkeypress="return isNumberKey(event);" />
				{hidden_zone}
				<input type="submit" value="Létrehoz!" />
			</form>');
			$this->templates[BANNERS_TEMPLATE_AREA_RENAME_DEL_FORM] = new template('
				{hidden_zone}
				<input type="submit" name="del_area" value="Törlés!" /><br />
				<input type="submit" name="rn_area" value="Átnevez->" />
				<input type="text" name="area_new_name" /><br />
				Szélesség és magasság módosítása:<br />
				Szélesség: <input type="text" size="4" maxlength="4" name="new_area_width" onkeypress="return isNumberKey(event);" />
				Magasság: <input type="text" size="4" maxlength="4" name="new_area_height" onkeypress="return isNumberKey(event);" />
				<input type="submit" name="change_areawh" value="Módosít">');
			$this->templates[BANNERS_TEMPLATE_BANNER_UPLOAD_FORM] = new template('
			<form enctype="multipart/form-data" action="" method="post">
				Elérési út:<br />
				<input type="file" name="up_file" />
				{hidden_zone}
				<input type="submit" value="Feltölt!" />
			</form>');
			$this->templates[BANNERS_TEMPLATE_BANNER_RENAME_DEL_FORM] = new template('
				{hidden_zone}
				<input type="submit" name="del_banner" value="Törlés!" /><br />
				<input type="submit" name="rn_banner" value="Átnevez!->" />
				<input type="text" name="banner_new_name" /><br />
				<input type="submit" name="link_banner" value="Hozzáad!->" />
				<input tyep="text" name="banner_href" />');
			$this->templates[BANNERS_TEMPLATE_SHOW_IMAGE] = new template('
					<a href="{link}" target="_blank">
						<img src="{url}" width="{width}" height="{height}" border="0" />
					</a>');
			$this->templates[BANNERS_TEMPLATE_SHOW_SWF] = new template('
					<a href="{link}" target="_blank">
						<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="{width}" height="{height}">
						   <param name="movie" value="{url}" />
						   <param name="quality" value="high" />
						   <param name="menu" value="false" />
						   <param name="wmode" value="" />
						   <embed src="{url}" wmode="" quality="high" menu="false" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="{width}" height="{height}"></embed>
						</object>
						</a>');
			$this->templates[BANNERS_TEMPLATE_CONTACT_IMAGE] = new template('<img src="{url}" width="{width}" height="{height}" border="0" />');
			$this->templates[BANNERS_TEMPLATE_CONTACT_SWF] = new template('
							<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="{width}" height="{height}">
							   <param name="movie" value="{url}" />
							   <param name="quality" value="high" />
							   <param name="menu" value="false" />
							   <param name="wmode" value="" />
							   <embed src="{url}" wmode="" quality="high" menu="false" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="{width}" height="{height}"></embed>
							</object>');
			$this->templates[BANNERS_TEMPLATE_CONTACT_NAME] = new template('<a href="{url}" target="_blank">{name}</a>');
			$this->templates[BANNERS_TEMPLATE_CONTACT_DEL] = new template('<input type="radio" name="del_contact" value="{banner_id}" /><input type="hidden" name="action" value="contact_del" /><input type="submit" value="Törlés">');
		}
	}

	public function install()
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `".$this->prefix."banners_data` (`banner_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `banner_name` TEXT NOT NULL, `banner_type` TEXT NOT NULL, `banner_url` TEXT NOT NULL, `banner_link` TEXT NOT NULL , PRIMARY KEY (`banner_id`));");
		mysql_query("CREATE TABLE IF NOT EXISTS `".$this->prefix."banners_contact` (`banner_id` INT UNSIGNED NOT NULL, `area_id` INT UNSIGNED NOT NULL);");
		mysql_query("CREATE TABLE IF NOT EXISTS `".$this->prefix."banners_area` (`area_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `area_name` TEXT NOT NULL, `area_width` INT UNSIGNED NOT NULL, `area_height` INT UNSIGNED NOT NULL, PRIMARY KEY (`area_id`));");
	}

	
	public function showArea($area_id)
	{
		if(!($get_banner = $this->getAreaObj($area_id)))return "";
		switch($get_banner['type'])
		{
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				echo $this->templates[BANNERS_TEMPLATE_SHOW_IMAGE]->getTrans(array(	"url"=>$get_banner['url'],
																						"width"=>$get_banner['width'],
																						"height"=>$get_banner['height'],
																						"link"=>$get_banner['link']));
				return $get_banner['id'];
			case 'application/x-shockw':
			case 'application/x-shockwave-flash':
				echo $this->templates[BANNERS_TEMPLATE_SHOW_SWF]->getTrans(array(	"url"=>$get_banner['url'],
																					"width"=>$get_banner['width'],
																					"height"=>$get_banner['height'],
																					"link"=>$get_banner['link']));
				return $get_banner['id'];
			default:
				echo  "";
				return 0;
		}

	}
	public function getArea($area_id)
	{
		if(!($get_banner = $this->getAreaObj($area_id)))return "";
		switch($get_banner['type'])
		{
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				return $this->templates[BANNERS_TEMPLATE_SHOW_IMAGE]->getTrans(array(	"url"=>$get_banner['url'],
																						"width"=>$get_banner['width'],
																						"height"=>$get_banner['height'],
																						"link"=>$get_banner['link']));
				break;
			case 'application/x-shockw':
			case 'application/x-shockwave-flash':
				return $this->templates[BANNERS_TEMPLATE_SHOW_SWF]->getTrans(array(	"url"=>$get_banner['url'],
																					"width"=>$get_banner['width'],
																					"height"=>$get_banner['height'],
																					"link"=>$get_banner['link']));
				break;
			default:
				return "";
				break;
		}

		
	}

	public function getAreaObj($area_id)
	{
		if(!reportedIsNumber($area_id,"banners::getAreaObj"))return false; 
		foreach(doSelect("SELECT `banner_id` FROM `".$this->prefix."banners_contact` WHERE `area_id`=".$area_id.";") as $val)
		{
			$banner_id[] = $val['banner_id'];
		}
		if(count($banner_id)==0)
		{
			return false;
		}
		$select_contact_num = selectMezo("SELECT COUNT(*) as num FROM `".$this->prefix."banners_contact` WHERE `area_id`='".$area_id."';","num");
		$random = rand(0,($select_contact_num-1));

		$temp = selectOne("SELECT `banner_url`, `banner_type`, `banner_link` FROM `".$this->prefix."banners_data` WHERE `banner_id`=".$banner_id[$random]." LIMIT 0,1");
		if(isset($area_id) && isset($temp['banner_url']))
			{
			$infos = getimagesize($this->upload_dir."/".$temp['banner_url']);

			list($temp_size) = doSelect("SELECT `area_width`,`area_height` FROM `".$this->prefix."banners_area` WHERE `area_id`='".$area_id."';");

			if(($temp_size['area_width']/$temp_size['area_height']) > ($infos[0]/$infos[1]))
			{
				$new_height = $temp_size['area_height'];
				$new_width = $new_height*($infos[0]/$infos[1]);
			}
			elseif(($temp_size['area_width']/$temp_size['area_height']) < ($infos[0]/$infos[1]))
			{
				$new_width = $temp_size['area_width'];
				$new_height = $new_width/($infos[0]/$infos[1]);
			}
			elseif(($temp_size['area_width']/$temp_size['area_height']) == ($infos[0]/$infos[1]))
			{
				$new_height = $temp_size['area_height'];
				$new_width = $new_height*($infos[0]/$infos[1]);
			}
		}
		return array("type"=>$temp['banner_type'], "url"=>$this->upload_dir."/".$temp['banner_url'], "width"=>$new_width, "height"=>$new_height, "link"=>$temp['banner_link'], "id"=>$banner_id[$random]);
	}

	public function uploadFile($file)
	{
		$ok = true;
		if($this->isValid($file['type']))
		{

			$ok &= mysql_query("INSERT INTO `".$this->prefix."banners_data` (`banner_name`, `banner_type`, `banner_link`) VALUES ('".specChars($file['name'],"banners::uploadFile")."', '".specChars($file['type'],"banners::uploadFile")."', '');");

			if($ok)
			{
				$new_name = "banner_".mysql_insert_id().".".$this->findExt($file['name']);
				$tmp_name = $file['tmp_name'];
				$ok &= move_uploaded_file($tmp_name, "$this->upload_dir/$new_name");
				if($ok)
				{
					$ok &= mysql_query("UPDATE `".$this->prefix."banners_data` SET  `banner_url`='".specChars($new_name,"banners::uploadFile")."' WHERE `banner_id`=".mysql_insert_id().";");
					if(!$ok)
					{
						report_error("banners::uploadFile SQL hiba, nem tudtam frissíteni a banner_data táblát");
						return $ok;
					}
					return $ok;
				}
				else
				{
					report_error("banners::uploadFile Hiba a feltöltésben...");
					return $ok;
				}
			}
			else
			{
				report_error("banners::uploadFile SQL hiba, nem tudtam a banner_data táblába írni");
				return $ok;
			}
		}
		else
		{
			echo "<p></p>";
			return false;
		}
	}

	public function delFile($del_file)
	{
		$ok = true;
		$file_url = selectMezo("SELECT `banner_url` FROM `".$this->prefix."banners_data` WHERE banner_id='".$del_file."';", "banner_url");
		$ok &= unlink($this->upload_dir."/".$file_url);
		if($ok)
		{
			$ok &= mysql_query("DELETE FROM `".$this->prefix."banners_data` WHERE banner_id='".$del_file."';");
			if($ok)
			{
				$ok &= mysql_query("DELETE FROM `".$this->prefix."banners_contact` WHERE banner_id='".$del_file."';");
				if(!$ok)
				{
					report_error("banners::delFile SQL hiba, nem tudtam törölni a contact táblából");
					return $ok;
				}
			}
			else
			{
				report_error("banners:delFile SQL hiba, nem tudtam törölni a banner_data táblából");
				return $ok;
			}
		}
		else
		{
			report_error("banners::delFile Nem tudtam törölni a bannert");
			return $ok;
		}

	return $ok;
	}

	public function makeArea($name,$width,$height)
	{
		if(!selectMezo("SELECT `area_name` FROM `".$this->prefix."banners_area` WHERE `area_name`='".specChars($name,"banners::makeArea")."';","area_name"))
		{
			if(!mysql_query("INSERT INTO `".$this->prefix."banners_area` (`area_name`, `area_width`, `area_height`) VALUES ('".specChars($name,"banners::makeArea")."',".$width.",".$height.");"))
			{
				report_error("banners::makeArea SQL hiba, nem tudtam az area táblába írni");
				return false;
			}
			return true;
		}
		return false;
	}

	public function toArea($area_id,$banner_id)
	{
		$ok = true;
		$contact = selectMezo("SELECT COUNT(*) AS num FROM `".$this->prefix."banners_contact` WHERE `banner_id`='".$banner_id."' and `area_id`='".$area_id."';","num");
		if(!$contact)
		{
			$ok &= mysql_query("INSERT INTO `".$this->prefix."banners_contact` (`banner_id`, `area_id`) VALUES ('".$banner_id."', '".$area_id."')");
			if(!$ok)
			{
				report_error("banners::toArea SQL hiba, nem tudtam a contact táblába írni");
				return $ok;
			}
			return $ok;
		}
		else
		{
			return false;
		}
		
	}

	public function delArea($del_area)
	{
		$ok = true;
		$ok &= mysql_query("DELETE FROM `".$this->prefix."banners_area` WHERE area_id='".$del_area."';");
		if($ok)
		{
			$ok &= mysql_query("DELETE FROM `".$this->prefix."banners_contact` WHERE area_id='".$del_area."';");
			if(!$ok)
			{
				report_error("banners::delArea nem tudtam törölni a contact táblából");
				return $ok;
			}
		}
		else
		{
			report_error("banners::delArea SQL hiba, nem tudtam törölni az area táblából");
			return $ok;
		}
	}

	public function renameBanner($id, $new_name)
	{
		$ok = true;
		if($new_name != "")
		{
			$ok &= mysql_query("UPDATE `".$this->prefix."banners_data` SET `banner_name`='".specChars($new_name,"banners::renameBanner")."' WHERE `banner_id`='".$id."'");
			if(!$ok)
			{
				report_error("banners::renameBanner SQl hiba, nem tudtam frissíteni a banner_data táblát");
				return $ok;
			}
			return $ok;
		}
		else
		{
			return false;
		}
	}
	
	public function contactBanner($area_id,$dist)
	{
		if(isset($area_id))
		{
			$name = selectMezo("SELECT `area_name` FROM `".$this->prefix."banners_area` WHERE `area_id`=".$area_id." ","area_name");
			if(selectMezo("SELECT COUNT(*) AS num FROM `".$this->prefix."banners_contact` WHERE `area_id`=".$area_id.";","num"))
			{
				$contact_images = "";
				$contact_names = "";
				$contact_del = "";
				$contact = "<strong>".$name.'</strong> területhez rendelt bannerek:<table class="table"><thead><tr><td>Banner kép</td><td>Banner neve</td><td>Banner törlése</td></tr></thead><tbody>';
				foreach(doSelect("SELECT `banner_name`,`banner_url`,`banner_type` FROM `".$this->prefix."banners_data` INNER JOIN `".$this->prefix."banners_contact` ON (`area_id`='".$area_id."') WHERE (".$this->prefix."banners_data.banner_id=".$this->prefix."banners_contact.banner_id);") as $val)
				{
					$banner_id = selectMezo("SELECT `banner_id` FROM `".$this->prefix."banners_data` WHERE `banner_url`='".specChars($val['banner_url'],"banners::contactBanner")."' ;","banner_id");
					$infos = getimagesize($this->upload_dir."/".$val['banner_url']);
					$temp_width = $dist;
					$temp_height = $dist/(1024/768);
					if(($temp_width/$temp_height) > ($infos[0]/$infos[1]))
					{
						$new_height = $temp_height;
						$new_width = $new_height*($infos[0]/$infos[1]);
					}
					elseif(($temp_width/$temp_height) < ($infos[0]/$infos[1]))
					{
						$new_width = $temp_width;
						$new_height = $new_width/($infos[0]/$infos[1]);
					}
					elseif(($temp_width/$temp_height) == ($infos[0]/$infos[1]))
					{
						$new_height = $temp_height;
						$new_width = $new_height*($infos[0]/$infos[1]);
					}
					if($val['banner_type'] == 'application/x-shockwave-flash' || $val['banner_type'] == 'application/x-shockw')
					{
						$contact_images = $this->templates[BANNERS_TEMPLATE_CONTACT_SWF]->getTrans(array(	"url"=>$this->upload_dir."/".$val['banner_url'],
																											"width"=>$new_width,
																											"height"=>$new_height));
						$contact_name = $this->templates[BANNERS_TEMPLATE_CONTACT_NAME]->getTrans(array(	"name"=>$val['banner_name'],
																											"url"=>$this->upload_dir."/".$val['banner_url']));
						$contact_del = $this->templates[BANNERS_TEMPLATE_CONTACT_DEL]->getTrans(array("banner_id"=>$banner_id));
					}
					else
					{
						$contact_images = $this->templates[BANNERS_TEMPLATE_CONTACT_IMAGE]->getTrans(array(	"url"=>$this->upload_dir."/".$val['banner_url'],
																											"width"=>$new_width,
																											"height"=>$new_heigth));
						$contact_name = $this->templates[BANNERS_TEMPLATE_CONTACT_NAME]->getTrans(array(	"name"=>$val['banner_name'],
																											"url"=>$this->upload_dir."/".$val['banner_url']));
						$contact_del = $this->templates[BANNERS_TEMPLATE_CONTACT_DEL]->getTrans(array("banner_id"=>$banner_id,"area_id"=>$area_id));
					}
					$contact .= $this->templates[BANNERS_TEMPLATE_CONTACT]->getTrans(array(	"contact_image"=>$contact_images,
																							"contact_name"=>$contact_name,
																							"contact_del"=>$contact_del));
				}
				$contact .= "</tobdy></table>";
			}
			else
			{
				$contact = "<strong>".$name."</strong> területhez nincs aktív banner hozzárendelve!";
			}
		}
		return $contact;
	}
	public function renameArea($id,$new_name)
	{
		$ok = true;
		if($new_name != "")
		{
			$ok &= mysql_query("UPDATE `".$this->prefix."banners_area` SET `area_name`='".specChars($new_name,"banners::renameArea")."' WHERE `area_id`='".$id."'");
			if(!$ok)
			{
				report_error("banners::renameArea SQl hiba, nem tudtam frissíteni az area táblát");
				return $ok;
			}
			return $ok;
		}
		else
		{
			return false;
		}
	}

	public function delContact($banner_id,$area_id)
	{

		if(!mysql_query("DELETE FROM `".$this->prefix."banners_contact` WHERE `banner_id`='".$banner_id."' and `area_id`='".$area_id."';"))
		{
			report_error("banners::delContact SQL hiba, nem tudtam törölni a banners_contact táblából!");
			return false;
		}
		else
		{
			return true;
		}
	}

	public function changeAreaWH($area_id,$area_width,$area_height)
	{
		$ok = true;
		if($area_width != "" && $area_height == "")
		{
			$ok &= mysql_query("UPDATE `".$this->prefix."banners_area` SET `area_width`=".$area_width." WHERE `area_id`=".$area_id.";");
			if(!$ok)
			{
				report_error("banners::changeAreaWH SQL hiba, nem tudtam frissíteni a banners_area tábla area_width mezőjét!");
				return $ok;
			}
		}
		elseif($area_height != "" && $area_width == "")
		{
			$ok &= mysql_query("UPDATE `".$this->prefix."banners_area` SET `area_height`=".$area_height." WHERE `area_id`=".$area_id.";");
			if(!$ok)
			{
				report_error("banners::changeAreaWH SQL hiba, nem tudtam frissíteni a banners_area tábla area_height mezőjét!");
				return $ok;
			}
		}
		else
		{
			$ok &= mysql_query("UPDATE `".$this->prefix."banners_area` SET `area_height`=".$area_height." WHERE `area_id`=".$area_id.";");
			if(!$ok)
			{
				report_error("banners::changeAreaWH SQL hiba, nem tudtam frissíteni a banners_area tábla area_height mezőjét!");
				return $ok;
			}
			else
			{
				$ok &= mysql_query("UPDATE `".$this->prefix."banners_area` SET `area_width`=".$area_width." WHERE `area_id`=".$area_id.";");
				if(!$ok)
				{
					report_error("banners::changeAreaWH SQL hiba, nem tudtam frissíteni a banners_area tábla area_height mezőjét!");
					return $ok;
				}
			}
		}
		return $ok;
	}

	public function linkBanner($banner_id,$link)
	{
		if(mysql_query("UPDATE `".$this->prefix."banners_data` SET `banner_link`='".$link."' WHERE `banner_id`=".$banner_id.";"))
		{
			return true;
		}
		else
		{
			report_error("banners::linkBanner SQL hiba, nem tudtam frissíteni a banners_data tábla banner_link mezőjét!");
			return false;
		}
	}
	
	public function process()
	{
		switch(getPOST('action','a'))
		{
			case 'up_load':
				if($_FILES['up_file']['name'] != NULL)
				{
					if(preg_match("([\.]{2,})",$_FILES['up_file']['name']))
					{
						$this->message(BANNERS_MESSAGES_UPLOAD_NAME);
					}
					else
					{
						if(!$this->uploadFile($_FILES['up_file']))
						{				
							$this->message(BANNERS_MESSAGES_UPLOAD_NOTVALID);
						}
					}
				}
				else
					$this->message(BANNERS_MESSAGES_NOT_UPFILE);
				break;
			case 'make_area':
				if($_POST['make_area_name'] == NULL)
				{
					$this->message(BANNERS_MESSAGES_NOT_AREA_NAME);				
				}
				else
				{
					if($_POST['make_area_width'] != "" && $_POST['make_area_height'] != "")
					{
						if(!isNumber($_POST['make_area_width']) || !isNumber($_POST['make_area_height']))
						{
							$this->message(BANNERS_MESSAGES_ISNOTNUMBER);
						}
						else
						{
							if($_POST['make_area_width'] > 1024 || $_POST['make_area_width'] < 10 || $_POST['make_area_height'] > 768 || $_POST['make_area_height'] < 10)
							{
								$this->message(BANNERS_MESSAGES_BIGNUMBER);
							}
							else
							{
								if(!$this->makeArea($_POST['make_area_name'],$_POST['make_area_width'],$_POST['make_area_height']))
								{
									$this->message(BANNERS_MESSAGES_ISSET_AREA);
								}
							}
						}
					}
					else
					{
						$this->message(BANNERS_MESSAGES_ISNOTSIZE);
					}
				}
				break;
			case 'to_area':
				if(!$this->toArea($_POST['select_area'],$_POST['select_banner']))
				{
					$this->message(BANNERS_MESSAGES_ISSET_CONTACT);
				}
				else
				{
					$area_name = selectMezo("SELECT `area_name` FROM `".$this->prefix."banners_area` WHERE `area_id`=".$_POST['select_area'].";","area_name");
					$banner_name = selectMezo("SELECT `banner_name` FROM `".$this->prefix."banners_data` WHERE `banner_id`=".$_POST['select_banner'].";","banner_name");
					//$this->message_text[BANNERS_MESSAGES_ISCONTACT] = $banner_name." hozzárendelve ehhez: ".$area_name;
					$this->message(BANNERS_MESSAGES_ISCONTACT);
				}
				break;
			case 'del_area':
				if(isset($_POST['delete_area']))
				{
					$this->delArea($_POST['delete_area']);
				}
				else
					$this->message(BANNERS_MESSAGES_NOT_SELECT);
				break;
			case 'rn_del_banner':
				if(isset($_POST['rn_banner']))
				{
					if(!$this->renameBanner($_POST['delete_file'],$_POST['banner_new_name']))
					{
						$this->message(BANNERS_MESSAGES_NOT_RENAME);
					}
				}
				elseif(isset($_POST['del_banner']))
				{
					$this->delFile($_POST['delete_file']);
				}
				elseif(isset($_POST['link_banner']))
				{
					if(!$this->linkBanner($_POST['delete_file'],$_POST['banner_href']))
					{
						//$this->message(BANNERS_MESSAGES_NOT_HREF);
					}
				}
				break;
			case 'rn_del_area':
				if(isset($_POST['rn_area']))
				{
					if(!$this->renameArea($_POST['delete_area'],$_POST['area_new_name']))
					{
						$this->message(BANNERS_MESSAGES_NOT_RENAME_AREA);
					}
				}
				elseif(isset($_POST['del_area']))
				{
					$this->delArea($_POST['delete_area']);
				}
				elseif(isset($_POST['change_areawh']))
				{
					if($_POST['new_area_width'] != "" || $_POST['new_area_height'] != "")
					{
						if(!isNumber($_POST['make_area_width']) || !isNumber($_POST['make_area_height']))
						{
							$this->message(BANNERS_MESSAGES_ISNOTNUMBER);
						}
						else
						{
							if(($_POST['new_area_width'] > 1024 || $_POST['new_area_width'] < 10) && ($_POST['new_area_height'] > 768 || $_POST['new_area_height'] < 10))
							{
									$this->message(BANNERS_MESSAGES_BIGNUMBER);
							}
							else
							{
								$this->changeAreaWH($_POST['delete_area'],$_POST['new_area_width'],$_POST['new_area_height']);
							}
						}
					}
					else
					{
						$this->message(BANNERS_MESSAGES_ISNOTSIZE);
					}
				}
				break;
			case 'contact_del':
				if(isset($_POST['del_contact']) && isset($_POST['area_id']))
				{
					$this->delContact($_POST['del_contact'],$_POST['area_id']);
				}
				else
				{
					$this->message(BANNERS_MESSAGES_ISNOTSELECT);
				}
				break;
		}
	}


	public function show()
	{
		$make_area_form = $this->templates[BANNERS_TEMPLATE_AREA_UPLOAD_FORM]->getTrans(array("hidden_zone"=>'<input type="hidden" name="action" value="make_area" />'));

		$areas = "";
		foreach(doSelect("SELECT `area_name`, `area_id` FROM `".$this->prefix."banners_area` ORDER BY `area_name` ASC") as $val)
		{
			$areas .= $this->templates[BANNERS_TEMPLATE_AREA]->getTrans(array(	"area_name"=>$val['area_name'],
																				"area_id"=>$val['area_id']));
		}
		
		$banners = "";
		foreach(doSelect("SELECT `banner_name`,`banner_id` FROM `".$this->prefix."banners_data` ORDER BY `banner_name` ASC") as $val)
		{
			$banners .= $this->templates[BANNERS_TEMPLATE_BANNER]->getTrans(array(	"banner_name"=>$val['banner_name'],
																					"banner_id"=>$val['banner_id']));
		}

		$make_banner_form = $this->templates[BANNERS_TEMPLATE_BANNER_UPLOAD_FORM]->getTrans(array("hidden_zone"=>'<input type="hidden" name="action" value="up_load" />'));
		$make_banner_rename_del_form = "";
		if($banners != "")
		{
			$make_banner_rename_del_form = $this->templates[BANNERS_TEMPLATE_BANNER_RENAME_DEL_FORM]->getTrans(array(
						"hidden_zone"=>'<input type="hidden" name="action" value="rn_del_banner" />'
											));
		}

		$make_area_rename_del_form = $this->templates[BANNERS_TEMPLATE_AREA_RENAME_DEL_FORM]->getTrans(array("hidden_zone"=>'<input type="hidden" name="action" value="rn_del_area" />'));

		$contacts = "";
		foreach(doSelect("SELECT `area_name`,`area_id` FROM ".$this->prefix."banners_area") as $val)
		{
			$contacts .= $this->contactBanner($val['area_id'],80)."<br/>";
		}

		echo $this->templates[BANNERS_TEMPLATE_MAIN]->getTrans(array(	"areas"=>$areas,
																		"area_upload_form"=>$make_area_form,
																		"banners"=>$banners,
																		"banner_upload_form"=>$make_banner_form,
																		"banner_rename_del_form"=>$make_banner_rename_del_form,
																		"area_rename_del_form"=>$make_area_rename_del_form,
																		"images"=>"",
																		"contacts"=>$contacts));
	}

	public function findExt($filename) 
	{ 
		return end(explode(".", strtolower($filename)));
	}

	private function isValid($file_type)
	{
		switch($file_type)
		{
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
			case 'application/x-shockw':
			case 'application/x-shockwave-flash':
				return true;
		}
		report_error("nem megfelelő fájlformátum aszondja:".$file_type);
		return false;
	}

}
?>
