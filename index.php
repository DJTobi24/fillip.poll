
<form method="post" action="config/vote.php">
<?php
include("config/database.inc.php"); // <- Datenbank einbinden

$sql = "SELECT * FROM `poll` WHERE `Aktiv` = 1 LIMIT 0,1 ORDER BY `Datum` DESC LIMIT 0,5"; // SQL String. Limit ist 1
$query = mysqli_query($conn, $sql); // Query ausführen
while ( $row = mysqli_fetch_row($query)) // In Array packen
{


echo "<b>Frage: ".$row[1]."</b><br>";
echo "<input type='hidden' name='pollid' value='".$row[0]."' />";
$sql = "SELECT * FROM `poll_answers` WHERE `pollid` = ".$row[0];
$query = mysqli_query($conn, $sql);
while($row = mysqli_fetch_row($query))
    {
        echo "<input type='radio' name='answer' value='".$row[0]."' />".$row[1]."<br>";
    }
}
?>
<input type='submit' value='Abstimmen!'>
</form>





<?php
$sql1 = "SELECT * FROM `poll`";

$umfragen = mysqli_query( $conn, $sql1 );
if ( ! $umfragen )
{
    die('Ungültige Abfrage: ' . mysqli_error());
}
$anzahl_eintraege = mysqli_num_rows($umfragen);
echo "<p>Anzahl der Umfragene: $anzahl_eintraege </p>";
?>