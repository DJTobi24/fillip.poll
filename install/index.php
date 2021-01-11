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

<!DOCTYPE html>
<html>
<head>
<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>
</head>
<body>
<table id="customers">
  <tr>
    <th>Name</th>
    <th>Version</th>
    <th>Status</th>
  </tr>
  <tr>
<?php
if(empty($php_error)) echo "<td>PHP</td> <td>$php_version</td> <td>OK!</td>";
else echo "<td style='color:red;'>$php_error/td>";?>
  </tr>
  </table>
<?php
if(empty($mail_error)) echo "<td>PHP-Mail</td> <td>OK!</td>";
else echo "<span style='color:red;'>$mail_error</span><br>";

if(empty($safe_mode_error)) echo "<td>SafeMode</td><td>OK!</td>";
else echo "<span style='color:red;'>$php_error</span><br>";

if(empty($mysql_error)) echo "<td>MySQL</td> <td>$mysql_version</td> <td>OK!</td>";
else echo "<span style='color:red;'>$mysql_error</span><br>";
?>
</body>
</html>