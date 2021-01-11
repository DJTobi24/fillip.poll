<?php
$php_version=phpversion();
if($php_version<5)
{
  $error=true;
  $php_error="PHP version is $php_version - too old!";
}

// declare function
function find_SQL_Version() {
    $output = shell_exec('mysql -V');
    preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
    return @$version[0]?$version[0]:-1;
  }
   
  $mysql_version=find_SQL_Version();
  if($mysql_version<5)
  {
    if($mysql_version==-1) $mysql_error="MySQL version will be checked at the next step.";
    else $mysql_error="MySQL version is $mysql_version. Version 5 or newer is required.";
  }

  if(!function_exists('mail'))
{
  $mail_error="PHP Mail function is not enabled!";
}

if( ini_get("safe_mode") )
{
  $error=true;
  $safe_mode_error="Please switch of PHP Safe Mode";
}

$_SESSION['myscriptname_sessions_work']=1;
if(empty($_SESSION['myscriptname_sessions_work']))
{
  $error=true;
  $session_error="Sessions must be enabled!";
}

<?php if(empty($php_error)) echo "<span style='color:green;'>$php_version - OK!</span>";
else echo "<span style='color:red;'>$php_error</span>";?>

$db_error=false;
// try to connect to the DB, if not display error
if(!@mysql_connect($_POST['dbhost'],$_POST['dbuser'],$_POST['dbpass']))
{
  $db_error=true;
  $error_msg="Sorry, these details are not correct.
  Here is the exact error: ".mysql_error();
}
 
if(!$db_error and !@mysql_select_db($_POST['dbname']))
{
  $db_error=true;
  $error_msg="The host, username and password are correct.
  But something is wrong with the given database.
  Here is the MySQL error: ".mysql_error();
}

// try to create the config file and let the user continue
$connect_code="<?php
define('DBSERVER','".$_POST['dbhost']."');
define('DBNAME','".$_POST['dbname']."');
define('DBUSER','".$_POST['dbuser']."');
define('DBPASS','".$_POST['dbpass']."');
?>";

if(!is_writable("inc/db_connect.php"))
{
  $error_msg="<p>Sorry, I can't write to <b>inc/db_connect.php</b>.
  You will have to edit the file yourself. Here is what you need to insert in that file:<br /><br />
  <textarea rows='5' cols='50' onclick='this.select();'>$connect_code</textarea></p>";
}
else
{
  $fp = fopen('inc/db_connect.php', 'wb');
  fwrite($fp,$connect_code);
  fclose($fp);
  chmod('inc/db_connect.php', 0666);
}