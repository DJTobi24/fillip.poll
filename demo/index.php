<?php

require_once('includes/pollDbAccess.php');
if(empty($hostName) || empty($userName) || empty($database) ){
	header('Location: install/install.php');
}else{
	header('Location: example.php');
}

?>