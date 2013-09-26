<?
$GLOBALS['fc_actual_max_feedback_id'] = 0;
function fb_startFeedback($id=-1)
{
	if($id==-1)$id = ++$GLOBALS['fc_actual_max_feedback_id'];
	$GLOBALS['fc_actual_feedback'.$id] = "";
	ob_start();
}

function fb_addFeedback($str,$id=1)
{
	$GLOBALS['fc_actual_feedback'.$id] .= $str;
}

function fb_endFeedback()
{
	for($id = $GLOBALS['fc_actual_max_feedback_id'];$id>0;$id--)
	{
		$temp = ob_get_contents();
		ob_end_clean();
		//report_error($GLOBALS['fc_actual_feedback'.$id].$temp);
		
		echo $GLOBALS['fc_actual_feedback'.$id].$temp;
	}
}

?>
