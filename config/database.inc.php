<?php
function OpenCon()
 {
 $dbhost = "127.0.0.1";
 $dbuser = "poll";
 $dbpass = "25152515?";
 $db = "poll";
 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
 
 return $conn;
 }
 
function CloseCon($conn)
 {
 $conn -> close();
 }
   
