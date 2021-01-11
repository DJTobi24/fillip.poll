<?php
function OpenCon()
 {
  include 'dbdata.php';

 $conn = new mysqli($DATENBANK_HOST, $DATENBANK_BENUTZER, $DATENBANK_PASSWORT,$DATENBANK_NAME) or die("Verbindung Fehlgeschlagen: %s\n". $conn -> error);
 
 return $conn;
 }
 
function CloseCon($conn)
 {
 $conn -> close();
 }

 echo '<script>console.log("Consolen LOG: Datenbank Verbunden")</script>';
 ?>