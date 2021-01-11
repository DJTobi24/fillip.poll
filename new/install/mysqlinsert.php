<?php
include('../config/dbdata.php');
try {
     $db = new PDO('mysql:host=' . $DATENBANK_HOST . ';dbname=' . $DATENBANK_NAME . ';charset=utf8', $DATENBANK_BENUTZER, $DATENBANK_PASSWORT);
	 $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling

     $sql ="CREATE DATABASE IF NOT EXISTS `phpumfrage` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;" ;
		$db->exec($sql1;
		print("Erstelle phpumfrage Datenbank, wen sie noch nicht existiert.\n");


     $sql1 ="CREATE TABLE IF NOT EXISTS umfragen(
     id int(11) NOT NULL AUTO_INCREMENT,
     frage text NOT NULL,
     besch text NOT NULL,
	 PRIMARY KEY (id)
	 )ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;;" ;
     	$db->exec($sql1);
	 	print("Erstelle Tabelle: umfragen.\n");

     $sql2 ="INSERT INTO `umfragen` (`id`, `frage`, `besch`) VALUES (1, 'Was ist deine Lieblings Programmiersprache?', '');" ;
		$db->exec($sql2);
		print("Impotiere Demo Kontent in die Tabelle: umfrage.\n");


} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}
?>