<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

function pdo_connect_mysql() {
    // Hier sind die MySql Lgoindaten abgespeichert
    $DATENBANK_HOST = 'localhost';
    $DATENBANK_BENUTZER = 'poll4';
    $DATENBANK_PASSWORT = '25152515?';
	$DATENBANK_NAME = 'phppoll';
	'hostname' => '%HOSTNAME%',
	'username' => '%USERNAME%',
	'password' => '%PASSWORD%',
	'database' => '%DATABASE%',

    try {
    	return new PDO('mysql:host=' . $DATENBANK_HOST . ';dbname=' . $DATENBANK_NAME . ';charset=utf8', $DATENBANK_BENUTZER, $DATENBANK_PASSWORT);
    } catch (PDOException $exception) {
    	// Wen es einen Error bei der Verbindung gibt wird er hier angezeigt.
    	die ('Konnte keine Verbindung zur Datenbank herstellen! | conf.inc.php');
    }
}

// HTML Header der überall eingefügt wird
function template_header($title) {
    echo <<<EOT
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>$title</title>
            <link href="style.css" rel="stylesheet" type="text/css">
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        </head>
        <body>
        <nav class="navtop">
            <div>
                <h1>Umfragen Erstelle / Löschen / Auswerten</h1>
                <a href="index.php"><i class="fas fa-poll-h"></i>Umfragen</a>
            </div>
        </nav>
    EOT;
    }

    // HTML Footer der überall eingefügt wird um z.B. das Impressum Anzuzeigen
function template_footer() {
    echo <<<EOT
        </body>
    </html>
    EOT;
	}
?>