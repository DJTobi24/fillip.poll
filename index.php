<form method="post" action="config/vote.php">
<?php
include("config/database.inc.php"); // Einbinden der Verbindung zur Datenbank
$conn = OpenCon();
echo "Connected Successfully";
$sql = "SELECT * FROM `poll` WHERE `Aktiv` = 1 LIMIT 0,1"; // SQL String. Limit ist 1
$query = mysqli_query($sql); // Query ausführen
$row = mysqli_fetch_row($query); // In Array packen
echo $row[1]."<br>"; // row[1] ist im unserem fall die Frage
echo "<input type='hidden' name='pollid' value='".$row[0]."' />"; //Wir speichern uns die Poll ID für spätere Verwendung
?>
</form>