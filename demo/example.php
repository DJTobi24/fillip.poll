<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Smooth Ajax Poll</title>
<?php 

######################################################   COPY THIS BLOCK 	 ####################################################################################

$poll_path = "/smooth_poll/"; /* Tell the script where is your poll folder */
							  /* Example ( Windows) : C:/htdocs/smooth_poll/ ( put just /smooth_poll/ ) , we ignore the root (C:/htdocs/) */
							  /* Example ( Linux ) : ..public_html/folder1/smooth_poll ( put just /folder1/smooth_poll/ ) , ignore the root */


function getRelativePath($f, $t){$f = is_dir($f) ? rtrim($f, '\/') . '/' : $f; $t = is_dir($t)   ? rtrim($t, '\/') . '/'   : $t; $f = str_replace('\\', '/', $f); $t = str_replace('\\', '/', $t); $f = explode('/', $f); $t = explode('/', $t); $rp = $t; foreach($f as $dp => $dr) {if($dr === $t[$dp]) {array_shift($rp); } else {$rm = count($f) - $dp; if($rm > 1) {$pl = (count($rp) + $rm - 1) * -1; $rp = array_pad($rp, $pl, '..'); break; } else {$rp[0] = './' . $rp[0]; } } } return implode('/', $rp); }

$root = $_SERVER['DOCUMENT_ROOT'];
$relative_path = getRelativePath(__DIR__,$root.$poll_path);
require_once($relative_path.'includes/pollfns.php');	 /* require the basic functions */
includeScripts($relative_path); 						 /* include the scripts */


######################################################## END OF BLOCK ################################################################################



?>	
</head>
<body>
<?php

######################################################   COPY THIS BLOCK 	 ####################################################################################

	newPoll('poll');

############################################################ END OF BLOCK ################################################################################


?>
</body>
</html>