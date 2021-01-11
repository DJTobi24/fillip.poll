<?php
// PHP Version Prüfen
$php_version=phpversion();
if($php_version<7)
{
  $error=true;
  $php_error="Die PHP Version: $php_version - ist zu alt!";
}

 if(empty($php_error)) echo "<span style='color:green;'>$php_version - OK!</span>";
else echo "<span style='color:red;'>$php_error</span>";

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