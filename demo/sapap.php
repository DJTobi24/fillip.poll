<?php
$relative_path=''; // the path to the smooth poll folder from this script (the index) 
				   //#example => "/folder/smooth_poll/"
require_once($relative_path.'includes/pollfns.php');	 /* require the basic functions */
require_once($relative_path.'includes/adminfns.php');	//the admin functionalities
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Smooth Ajax Poll</title>
<?php 
includeScripts($relative_path); 							 /* include the scripts */
?>	
</head>
<body>
<?php
	 newPoll('admin');	// the first poll
?>
</body>
</html>