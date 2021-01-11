<?php
include('../config/dbdata.php');
try {
     $db = new PDO('mysql:host=' . $DATENBANK_HOST . ';dbname=' . $DATENBANK_NAME . ';charset=utf8', $DATENBANK_BENUTZER, $DATENBANK_PASSWORT);
	 $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling


    $sql1 ="CREATE TABLE IF NOT EXISTS umfragen(
     		id int(11) NOT NULL AUTO_INCREMENT,
     		frage text NOT NULL,
     		besch text NOT NULL,
	 		PRIMARY KEY (id)
	 		)ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;;" ;
     	$db->exec($sql1);
	 	print("Erstelle Tabelle: umfragen.\n");
sleep(2);
	$sql2 ="CREATE TABLE IF NOT EXISTS `umfrage_antwort` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`umfrage_id` int(11) NOT NULL,
			`antworten` text NOT NULL,
			`stimmen` int(11) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;;" ;
		$db->exec($sql2);
		print("Erstelle Tabelle: umfrage_antwort.\n");
sleep(2);
	$sql3 ="INSERT INTO `umfrage_antwort` (`id`, `umfrage_id`, `antworten`, `stimmen`) VALUES (1, 1, 'PHP', 0), (2, 1, 'Python', 0), (3, 1, 'C#', 0), (4, 1, 'Java', 0);" ;
		$db->exec($sql3);
		print("Impotiere Demo Kontent in die Tabelle: umfrage_antwort.\n");
sleep(2);	
	$sql4 ="INSERT INTO `umfragen` (`id`, `frage`, `besch`) VALUES (1, 'Was ist deine Lieblings Programmiersprache?', '');" ;
		$db->exec($sql4);
		print("Impotiere Demo Kontent in die Tabelle: umfrage.\n");

} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}
?>