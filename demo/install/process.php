<?php
define('DIR_APP', str_replace('\'', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_SMOOTH_POLL', str_replace('\'', '/', realpath(DIR_APP . '../')) . '/');
$error=array();

$host=$_POST['db_host'];
$db_name=$_POST['db_name'];
$db_pass=$_POST['db_password'];
$db_pref=$_POST['db_prefix'];
$db_user=$_POST['db_user'];
$vars=$host.'*_@#/'.$db_user.'*_@#/'.$db_pass.'*_@#/'.$db_name.'*_@#/'.$db_pref;
	if (!$host) {
		$error['db_host'] = 'Host required !';
	}
	if (!$db_user) {
		$error['db_user'] = 'User required !';
	}
	if (!$db_name) {
		$error['db_name'] = 'Database Name required !';
	}
	if (!$connection = @mysql_connect($host, $db_user, $db_pass)) {
		$error['warning'] = 'Error: Could not connect to the database please make sure the database host, username and password is correct !';
	} else {
		if (!@mysql_select_db($db_name, $connection)) {
			$error['warning'] = 'Error: Database does not exist!';
		}
		mysql_close($connection);
	}
	if (!is_writable(DIR_SMOOTH_POLL . 'includes/pollDbAccess.php')) {
		$error['warning'] = 'Error: Could not write to pollDbAccess.php please check you have set the correct permissions on: ' .DIR_SMOOTH_POLL . 'includes/pollDbAccess.php !';
	}
	
	$errors=count($error);
	if($errors!=0){
		die(print_r($error).'@>#__!'.$vars);
	}

$output  = '<?php' . "\n";
$output .= ' $hostName=\''.$host.'\'; ' . "\n";
$output .= ' $userName=\''.$db_user.'\'; ' . "\n";
$output .= ' $password=\''.$db_pass.'\'; ' . "\n";
$output .= ' $database=\''.$db_name.'\'; ' . "\n";
$output .= '?>';				
		
$file = fopen(DIR_SMOOTH_POLL . 'includes/pollDbAccess.php', 'w');
if($file){
	fwrite($file, $output);
	fclose($file);
	require_once('../setup.php');
}else{
		echo 'Error : Unable to write the database data to the file pollDbAccess.php';	
	}
?>