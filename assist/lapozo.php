<?
class lapozo
{
	var $adat_per_lap;
	var $akt_lap;
	var $lap_count;
	var $get_param;
	function lapozo($table,$where,$get_param,$ered_per_lap)
	{
		$this->get_param = $get_param;
		$count = 0;
		if($count_q =  mysql_query("SELECT count(*) FROM `".$table."` ".$where))
		{
			if($count_v = mysql_fetch_assoc($count_q))
			{
				$count = $count_v['count(*)'];
			}
			mysql_free_result($count_q);
		}
		$this->adat_per_lap = $ered_per_lap;
		$this->lap_count = ceil($count/$this->adat_per_lap);

		$this->akt_lap = 0;
		if(isNumber(getGET($this->get_param,'a')))
		{
			$this->akt_lap = $_GET[$this->get_param];
		}
		if($this->akt_lap >= $this->lap_count)$this->akt_lap = $this->lap_count-1;
		if($this->akt_lap < 0) $this->akt_lap = 0;		
	}	
	function getLimit()
	{
		return 'LIMIT '.($this->adat_per_lap*$this->akt_lap).','.$this->adat_per_lap;
	}
	function getButtons($terjed=3,$prev_button='&lt;',$next_button='&gt;')
	{
		$str = '';
		if($this->lap_count > 1)
		{
			
			if($this->akt_lap > 0)
			{
				$str .= '<a href="'.addGET(array($this->get_param=>($this->akt_lap-1))).'">'.$prev_button.'</a>';
			}

			if($this->akt_lap-$terjed > 0)
			{
				$str .= '<a href="'.addGET(array($this->get_param=>0)).'">[1]</a> ... ';
			}

			for($a=max(0,$this->akt_lap-$terjed);$a<min($this->lap_count,$this->akt_lap+$terjed);$a++)
			{
				$str .=  ' '.($a == $this->akt_lap?"<b>":"").'<a href="'.addGET(array($this->get_param=>($a))).'">['.($a+1).']</a>'.($a == $this->akt_lap?"</b>":"").' ';
			}

			if($this->akt_lap+$terjed < $this->lap_count)
			{
				$str .= ' ... <a href="'.addGET(array($this->get_param=>$this->lap_count-1)).'">['.($this->lap_count).']</a>';
			}

			if($this->akt_lap < $this->lap_count-1)
			{
				$str .=  '<a href="'.addGET(array($this->get_param=>($this->akt_lap+1))).'">'.$next_button.'</a>';
			}
		}
		return $str;
	}
	function echoButtons($terjed=3,$prev_button='&lt;&lt;',$next_button='&gt;&gt;')
	{
		echo $this->getButtons($terjed,$prev_button,$next_button);
	}
}
?>
