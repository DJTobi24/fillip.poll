<?php
include('dbdata.php');
try {
     $db = new PDO('mysql:host=' . $DATENBANK_HOST . ';dbname=' . $DATENBANK_NAME . ';charset=utf8', $DATENBANK_BENUTZER, $DATENBANK_PASSWORT);
     $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
     $sql ="CREATE TABLE IF NOT EXISTS `umfragen` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`frage` text NOT NULL,
		`besch` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;" ;
     $db->exec($sql);
     print("Created Umfrage Table.\n");

} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}
?>