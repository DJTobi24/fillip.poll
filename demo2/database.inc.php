<?php
function OpenCon()
 {
 $dbhost = "127.0.0.1";
 $dbuser = "poll2";
 $dbpass = "25152515?";
 $db = "poll2";
 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
 
 return $conn;
 }
 
function CloseCon($conn)
 {
 $conn -> close();
 }
 $conn = OpenCon();
 echo '<script>console.log("Consolen LOG: Datenbank Verbunden")</script>';
 ?>