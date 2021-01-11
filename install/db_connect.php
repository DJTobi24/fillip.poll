<?php 

$connect_code="<?php
define('DBSERVER','".$_POST['dbhost']."');
define('DBNAME','".$_POST['dbname']."');
define('DBUSER','".$_POST['dbuser']."');
define('DBPASS','".$_POST['dbpass']."');
?>";


$db_error=false;
// try to connect to the DB, if not display error
if(!@mysqli_connect($_POST['dbhost'],$_POST['dbuser'],$_POST['dbpass']))
{
  $db_error=true;
  $error_msg="Sorry, these details are not correct.
  Here is the exact error: ".mysqli_error();
}
 
if(!$db_error and !@mysqli_select_db($_POST['dbname']))
{
  $db_error=true;
  $error_msg="The host, username and password are correct.
  But something is wrong with the given database.
  Here is the MySQL error: ".mysqli_error();
}


?>