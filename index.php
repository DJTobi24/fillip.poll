<form method="post" action="config/vote.php">
<?php
include("config/database.inc.php"); // <- Datenbank einbinden


$sql = "SELECT * FROM `poll` WHERE `Aktiv` = 1 LIMIT 0,1"; // SQL String. Limit ist 1
$query = mysqli_query($conn, $sql); // Query ausführen
$row = mysqli_fetch_row($query); // In Array packen
echo $row[1]."<br>"; // row[1] ist im unserem fall die Frage
echo "<input type='hidden' name='pollid' value='".$row[0]."' />"; //Wir speichern uns die Poll ID für spätere Verwendung

$sql = "SELECT * FROM `poll_answers` WHERE `pollid` = ".$row[0]; // Abfrage der Antwort wo die ID des Polls die unserer Frage ist
$query = mysqli_query($conn, $sql); // Query ausführen
while($row = mysql_fetch_row($query)) // So lange bis das ende erreicht ist
{
    echo "<input type='radio' name='answer' value='".$row[0]."' />".$row[1]."<br>"; // Radiobutton mit Frage ausgeben
}
?>
<input type='submit' value='Abstimmen!' />
</form>