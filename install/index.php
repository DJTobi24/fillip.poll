<?php
$php_version=phpversion();
if($php_version<5)
{
  $error=true;
  $php_error="PHP version is $php_version - too old!";
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
    if($mysql_version==-1) $mysql_error="MySQL version will be checked at the next step.";
    else $mysql_error="MySQL version is $mysql_version. Version 5 or newer is required.";
  }
  ?>