<?
function resizePic($src_url,$dest,$newWidth,$newHeight)
{
	$picext = strtolower(end(explode(".", $src_url)));
	switch($picext)
	{
		case 'jpeg':case 'jpg':$src = imagecreatefromjpeg($src_url);break;
		case 'png':$src = imagecreatefrompng($src_url);break;
		case 'gif':$src = imagecreatefromgif($src_url);break;
		default: return false;
	}
	list($width,$height)=getimagesize($src_url);

	if($width/$height < $newWidth/$newHeight)
	{
		$newwidth=($width/$height)*$newHeight;
		$newheight = $newHeight;
	}
	else
	{
		$newwidth = $newWidth;
		$newheight=($height/$width)*$newWidth;
	}

	$tmp=imagecreatetruecolor($newwidth,$newheight);

	imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height); 
	$path_parts = pathinfo($dest);
	report_error($path_parts['dirname'].'/'.$path_parts['filename']);
	
	switch($picext)
	{
		case 'jpeg':case 'jpg':imagejpeg($tmp,$path_parts['dirname'].'/'.$path_parts['filename'].".jpg",100);break;
		case 'png':imagepng($tmp,$path_parts['dirname'].'/'.$path_parts['filename'].".png");break;
		case 'gif':imagegif($tmp,$path_parts['dirname'].'/'.$path_parts['filename'].".gif");break;
		default: return false;
	}
	
	imagedestroy($tmp);
	return $filename;
}
?>