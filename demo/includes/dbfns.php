<?php
require_once('pollDbAccess.php');
date_default_timezone_set('UTC');
/* ----- This file is for database functions ----- */

// This function connects to the database.
function connect ($host,$user,$passwd,$db) 
{
	if(!$connect = new mysqli ($host,$user,$passwd,$db)) 
	{
		die("Could not connect to the database, please try again later. <br />");
	}else{return $connect;}
} 

// Querying the database
// arguments to be passed in are (the connection,the query) 
function query($connect,$query)
{
	$result =$connect->query($query);
	return $result;
}

// retreive data of the query !
function retrieve_data($result)
{	
	$num_result = $result->num_rows;
	for($i=0;$i<$num_result;$i++)
	{
		$result_arr[$i]=$result->fetch_assoc();
	}
	return @$result_arr;
}

//cleans the inputs (in case of sql injection or special charecters)
function cleanInput($method,$input,$sql=0){
	
		// Cleaning for $_POST vars
		if($method == 'p'){
			$cleaned = @trim($_POST["{$input}"]);
		// Cleaning for $_GET vars
		}elseif($method == 'g'){
			$cleaned = @trim($_GET["{$input}"]);
		// Cleaning for string vars
		}elseif($method == 's'){
			$cleaned = @trim($input);
		}
		
		$cleaned = @htmlentities(strip_tags($cleaned));
		if(@$sql == 1){
		// needs reference to $connect ! or else it won't work
		global $connect;
		$cleaned = mysqli_real_escape_string($connect,$cleaned);
		}
		return $cleaned;
}

// for closing the connection when we're done with it
function close($connect) {
	if($connect){
		if(!$connect->close()){
			die("Database logging out has failed !");		
		}
	}
}
?>