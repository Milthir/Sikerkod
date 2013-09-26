<?


define("EXPORTS_TEMPLATE_MAIN",1);

class exports extends forefather
{
	function exports($set_default_template=true)
	{
		if($set_default_template)
		{
			$this->templates[EXPORTS_TEMPLATE_MAIN] = new template('
				<form method="post" action="">
					{excel_hidden_zone}
					<input type="submit" value="Mentés Excel állományba" />
				</form>
				<form action="" method="post">
					{print_hidden_zone}
					<input type="submit" value="Nyomtatható formátum" />
				</form>');
		}
	}
	function excelExportWithHead($name,$head,$excel_table)
	{
		exports::excelExport($name,array_merge(array($head),$excel_table));
	}

	
	
	function excelExport($name,$excel_table)
	{
		while(ob_get_level())
		{
			ob_end_clean();
		}
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
	//	header("Content-Disposition: attachment; filename=".iconv("UTF-8","ISO 8859-1",$name).";");
		header("Content-Disposition: attachment; filename=".utf8_decode($name).";");
		header("Pragma: no-cache");
		header("Expires: 0");

		$data = "<"."?xml version='1.0' encoding='UTF-8' ?".">
<"."?mso-application progid='Excel.Sheet'?".">
<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet' xmlns:html='http://www.w3.org/TR/REC-html40'><DocumentProperties xmlns='urn:schemas-microsoft-com:office:office'>
    <Author>Tiszk felnőttképzési rendszer</Author>
    <LastAuthor>Tiszk felnőttképzési rendszer</LastAuthor>
    <Created>".date("Y-m-dTH:i:jZ")."</Created>
    <Company>TISZK</Company>
    <Version>1.0</Version>
  </DocumentProperties>
  <ExcelWorkbook xmlns='urn:schemas-microsoft-com:office:excel'>
    <WindowHeight>6795</WindowHeight>
    <WindowWidth>8460</WindowWidth>
    <WindowTopX>120</WindowTopX>
    <WindowTopY>15</WindowTopY>
    <ProtectStructure>False</ProtectStructure>
    <ProtectWindows>False</ProtectWindows>
	<ss:Styles>
        <ss:Style ss:ID='1'>
            <ss:Font ss:Bold='1'/>
        </ss:Style>
    </ss:Styles>

  </ExcelWorkbook>
   <Styles>
  <Style ss:ID='Default' ss:Name='Normal'>
   <Alignment ss:Vertical='Bottom'/>
   <Borders/>
   <Font ss:FontName='Calibri' x:CharSet='238' x:Family='Swiss' ss:Size='11'
    ss:Color='#000000'/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID='s65'>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='Calibri' x:CharSet='238' x:Family='Swiss' ss:Size='11'
    ss:Color='#FFFFFF' ss:Bold='1'/>
   <Interior ss:Color='#AF1414' ss:Pattern='Solid'/>
  </Style>
  <Style ss:ID='s66'>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
  </Style>
 </Styles>
  ";
  

		$multisheet = is_array($excel_table) && is_array(reset($excel_table)) && is_array(reset(reset($excel_table)));
  		if($multisheet)
  		{
			foreach ($excel_table as $key => $val)
			{
				$data .= exports::export1Table($key,$val);
			}	
  		}
  		else
  		{
  			$data .=  exports::export1Table(reset(explode(".",$name)),$excel_table);
  		}
  		
  		echo $data."</Workbook>";
		die;
	}
	
	function export1Table($name,$table)
	{
		$data = "<Worksheet ss:Name='".$name."'><Table>";
		$first=true;
		$data_temp = "";
		$size = array();
		foreach($table as $row)
		{
			if($first)
			{
				$first = false;
				$data_temp .= "<Row ss:StyleID='s65'>";
				$sorcnt = 0;
				foreach ($row as $in_val)
				{
					$data_temp .= "<Cell><Data ss:Type='".(isNumber($in_val)?"Number":"String")."'>".$in_val."</Data></Cell>";
					$size[$sorcnt++] = strlen($in_val); 
				}
				$data_temp .= "</Row>";
				
			}
			else  
			{
				$data_temp .= "<Row ss:StyleID='s66'>";
				$sorcnt = 0; 
				foreach ($row as $in_val)
				{
					$data_temp .= "<Cell><Data ss:Type='".(isNumber($in_val)?"Number":"String")."'>".$in_val."</Data></Cell>";
					if(strlen($in_val) > $size[$sorcnt])$size[$sorcnt] = strlen($in_val);
					$sorcnt++;
				}
				$data_temp .= "</Row>";
			}
			
		}
		foreach($size as $val)
		{
			$data .= "<Column ss:Width='".($val*6)."'/>";
		}
		return $data.$data_temp."</Table></Worksheet>";
	}

	function tableExpand($name,$table)
	{
		deliveryfmt_initialize();
		deliveryfmt_set_format('XLS', false);
		deliveryfmt_header();

		deliveryfmt_set_column_names(array('Elso oszlop','Második oszlop','Harmadik oszlop'));

		deliveryfmt_put_2d_array($tablazat_rekordok);
		deliveryfmt_trailer();
		$buffer = deliveryfmt_finish();

		//http headerek segítségével böngészõ kényszerítése a fájl letöltésére
		header("Cache-Control: public, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: application/octet-stream; charset=utf-8");
		header("Content-Disposition: attachment; filename=proba.xls");
		header("Content-Transfer-Encoding: binary\n");

		echo $buffer;
		
		
		/*echo '<table border="0">';
		foreach ($table as $val)
		{
			echo '<tr>';
			foreach ($val as $in_val)
			{
				echo '<td>'.ifNoName($in_val,"&nbsp;").'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';*/
	
		/*foreach ($table as $val)
		{
			foreach ($val as $in_val)
			{
				echo utf8_decode($in_val)."\t";
			}
			echo "\n";
		}*/
	}

	function printExport($name,$data)
	{
		echo '<script type="text/javascript">var printwnd = window.open("","'.$name.'","width=900,height=500,left=100,top=50");printwnd.document.write(\'';
		if(is_array($date))$this->tableExpand($data);
		else echo $data;
		echo '<a href="#" onclick="this.style.display=\\\'none\\\';setTimeout(\\\'window.print();window.close();\\\',500);return false;">Nyomtatás</a>\');</script>';
	}
	 
	function process()
	{
		
	} 	 	
	 	
	function show($name,$data)
	{
		switch(getPOST('action','a'))
		{
			case 'excelexport':
				$this->excelExport($name,$data);
				break;
			case 'printexport':
				$this->printExport($name,$data);
				break;
		}

		echo $this->templates[EXPORTS_TEMPLATE_MAIN]->getTrans(array(	
												"excel_hidden_zone"=>'<input type="hidden" name="action" value="excelexport" />',
												"print_hidden_zone"=>'<input type="hidden" name="action" value="printexport" />'
												));
	}
}
?>
