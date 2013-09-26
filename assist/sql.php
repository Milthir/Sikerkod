<?
function selectMezo($query,$mezo="")
{
	$query_v =0;
	$query_q = mysql_query($query);	
	if(mysql_error() == "")
	{
		if($mezo == "")
		{
			$query_v = mysql_fetch_row($query_q);
			mysql_free_result($query_q);
			return $query_v[0];	
		}
		else 
		{
			$query_v = mysql_fetch_assoc($query_q);
			mysql_free_result($query_q);
			return $query_v[$mezo];
		}
	}
	report_error("SQL::selectMezo sikertelen [".$query."]-[".$mezo."] (".mysql_error().")");
	return false;
}

function selectOne($query)
{
	$query_v = false;
	$query_q = mysql_query($query);	
	if(mysql_error() == "")
	{
		$query_v = mysql_fetch_assoc($query_q);
		mysql_free_result($query_q);
		return $query_v;
	}
	report_error("SQL::selectOne sikertelen [".$query."] (".mysql_error().")");
	return $query_v;
}

function doSelect($query)
{
	$out = array();
	$query_q = mysql_query($query);

	if(mysql_error() == "")
	{	
		while($query_v = mysql_fetch_assoc($query_q))
		{
			$out[] = $query_v;
		}
		mysql_free_result($query_q);
		return $out;
	}
	report_error("SQL::doSelect sikertelen [".$query."] (".mysql_error().")");
	return $out;
}
function selectAll($query)
{
	return doSelect($query);
}
?>
