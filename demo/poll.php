<?php
session_start();
require_once('includes/dbfns.php');	//the database functionalities
require_once('includes/pollfns.php');	//the basic poll functionalities
require_once('includes/adminfns.php');	//the admin functionalities
if(empty($_SESSION['pollAdmin'])){
	$admin=null;
}else{
	$admin=$_SESSION['pollAdmin'];  // ( the admin session )
}
$auth=$_POST['auth']; 			// authentification variable
if(!empty($_POST['r'])){
	$ref=$_POST['r']; // the reference of the poll
} else{
	$ref='';
}
	global	$hostName ;	
	global	$userName ;
	global	$password ;
	global	$database ;
	
$connect=connect($hostName,$userName,$password,$database);
if(!$connect){echo'Connection failed ! , please try again later ';exit;}

// if($auth!='y'){die('You don\'t have the permission to access this file directly');}

if(!empty($_POST['cmd'])){
	$cmd=$_POST['cmd']; // the command sent by ajax
	if(!empty($cmd))
	{
		switcher($cmd,$ref); // switch to the command sent by ajax and do the necessairy
	}
}


if(!empty($admin) && $ref=='wrap-admin'){	// if the admin is logged in (in the admin page)
		admContent($ref); //	show the dash board
		close($connect);
		exit;
}else if(empty($admin) && $ref=='wrap-admin'){	// if the admin is logged out (in the admin page)
	$adm=@cleanInput('p','u',1);
	$pass=@cleanInput('p','p',1);
	if(empty($adm) || empty($pass)){ // if no one is trying to login
		letMeIn($ref); //show the login page
	}else{ // connect the admin
		aConnect($adm,$pass,$ref);
	}	
}else{ //else it's an ordinary user 
		notAdmin($ref);	// handle the functions that must be called when there is no admin
	}	
?>