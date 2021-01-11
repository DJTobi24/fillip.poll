<?php
include('../config/dbdata.php');
try {
     $db = new PDO('mysql:host=' . $DATENBANK_HOST . ';dbname=' . $DATENBANK_NAME . ';charset=utf8', $DATENBANK_BENUTZER, $DATENBANK_PASSWORT);
	 $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling


    $sql1 ="CREATE TABLE IF NOT EXISTS `polls` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`title` text NOT NULL,
			`desc` text NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;;" ;
     	$db->exec($sql1);
	 	print("Erstelle Tabelle: umfragen.\n");
sleep(2);
	$sql2 ="CREATE TABLE IF NOT EXISTS `poll_answers` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`poll_id` int(11) NOT NULL,
			`title` text NOT NULL,
			`votes` int(11) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;;" ;
		$db->exec($sql2);
		print("Erstelle Tabelle: umfrage_antwort.\n");
sleep(2);
	$sql3 ="INSERT INTO `poll_answers` (`id`, `poll_id`, `title`, `votes`) VALUES (1, 1, 'PHP', 0), (2, 1, 'Python', 0), (3, 1, 'C#', 0), (4, 1, 'Java', 0);" ;
		$db->exec($sql3);
		print("Impotiere Demo Kontent in die Tabelle: umfrage_antwort.\n");
sleep(2);	
	$sql4 ="INSERT INTO `polls` (`id`, `title`, `desc`) VALUES (1, 'Was ist deine Lieblings Programmiersprache?', '');" ;
		$db->exec($sql4);
		print("Impotiere Demo Kontent in die Tabelle: umfrage.\n");

} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}
?>