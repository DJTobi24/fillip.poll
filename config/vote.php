<?php
include("database.inc.php");
if(!isset($_REQUEST['answer'])) // Überprüfen ob Etwas angeklickt wurde
{
    echo "Du musst etwas anklicken! <a href='index.php'>Zurück</a>"; // Wenn nicht dann bitte zurück
}
else //Ansonsten
{
$sql = "SELECT * FROM `poll_answers` WHERE `pollid` = ".$_REQUEST['pollid']." AND `ID` = ".$_REQUEST['answer'].""; // $_REQUEST['PollID'] ist die ID des Polls, das Hidden Field. 'answer' ist der Wert des Radio Buttons, also die ID der Antwort. Somit wird hier die Antwort geholt, die ausgewählt wurde
$query = mysqli_query($conn, $sql); //Antwort holen
$row = mysqli_fetch_row($query); // In Array schreiben
$klicks = $row[3] + 1; // Hier werden die Klicks genommen, und einer hinzugefügt.
$sql = "UPDATE `poll_answers` SET `Klicks` = ".$klicks." WHERE `ID` = ".$_REQUEST['answer']; // Den Klick noch hinzufügen, die Tabelle also updaten. Hier wird wieder nur der Eintrag geupdatet, den der Benutzer ausgewählt hat (mit der Where abfrage, 'answer' war ja die ID)
mysqli_query($sql); // Ausführen
$sql = "INSERT INTO `poll_ip` (`IP`, `Datum`, `PollID`) VALUES ('".$_SERVER['REMOTE_ADDR']."', NOW(), '".$_REQUEST['PollID']."');"; // Hier wird die IP ($_SERVER['REMOTE_ADDR']) des Benutzers eingefügt. Ausserdem wird die Poll ID aus unserem Hidden Field geholt
mysqli_query($sql); // Ausführen
echo "Danke für deine Teilnahme! <a href='ergebnisse.php'>Ergebnisse</a></p>"; // Bestätigung und weiterleitung zu Ergebnissen.
}
?>