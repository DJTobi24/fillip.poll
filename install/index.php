<?php
$php_version=phpversion();
if($php_version<5)
{
  $error=true;
  $php_error="PHP version is $php_version - too old!";
}
?>