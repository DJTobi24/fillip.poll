<?php
// PHP Version Prüfen
$php_version=phpversion();
if($php_version<7)
{
  $error=true;
  $php_error="Die PHP Version: $php_version - ist zu alt!";
}



// PHP Email Aktiv
if(!function_exists('mail'))
{
  $mail_error="PHP Mail Funktion ist nicht Aktiviert!";
}



// PHP Safe Mode 
if( ini_get("safe_mode") )
{
  $error=true;
  $safe_mode_error="Bitte gehen sie aus dem PHP Safe Mode heraus";
}



?>

<?php
// declare function
function find_SQL_Version() {
    $output = shell_exec('mysql -V');
    preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
    return @$version[0]?$version[0]:-1;
  }
   
  $mysql_version=find_SQL_Version();
  if($mysql_version<5)
  {
    if($mysql_version==-1) $mysql_error="Die MySQL Version wird beim Nächsten schritt überprüft.";
    else $mysql_error="Die Mysql Version: $mysql_version - ist zu Alt. Version 5 oder höher wird benötigt.";
  }

  $_SESSION['umfrage_sessions_work']=1;
  if(empty($_SESSION['umfrage_sessions_work']))
  {
    $error=true;
    $session_error="Sessions sind deaktiviert!";
  }

?>




<?php
if(empty($php_error)) echo "<span style='color:green;'>PHP Version: $php_version - OK!</span><br>";
else echo "<span style='color:red;'>$php_error</span><br>";

if(empty($mail_error)) echo "<span style='color:green;'>PHP-Mail - OK!</span><br>";
else echo "<span style='color:red;'>$mail_error</span><br>";

if(empty($safe_mode_error)) echo "<span style='color:green;'>SafeMode - OK!</span><br>";
else echo "<span style='color:red;'>$php_error</span><br>";

$connect_code="<?php
define('DBSERVER','".$_POST['dbhost']."');
define('DBNAME','".$_POST['dbname']."');
define('DBUSER','".$_POST['dbuser']."');
define('DBPASS','".$_POST['dbpass']."');
?>";



if(!is_writable("db_connect.php"))
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

?>