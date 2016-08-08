<!DOCTYPE html>
<html>
<head>
	<meta name="robots" content="noindex, nofollow">
	<meta name="googlebot" content="noindex"><?php 
	
	cf_leadya_head();
	
	$url			= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$lastSegment	= basename( parse_url($url, PHP_URL_PATH) );
	
	?><meta http-equiv="Refresh" content="0; url=http://www.allding.com/red.php?id=<?php echo $lastSegment; ?>">
</head>
<body>
</body>
</html>