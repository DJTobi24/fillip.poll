<form method="post" action="config/vote.php">
<?php
include("config/database.inc.php"); // <- Datenbank einbinden

$sql = "SELECT * FROM `poll` WHERE `Aktiv` = 1 LIMIT 0,1"; // SQL String. Limit ist 1
$query = mysqli_query($conn, $sql); // Query ausführen
$row = mysqli_fetch_row($query); // In Array packen

//$sql2 = "SELECT * FROM `poll_ip` WHERE `IP` LIKE '%".substr($_SERVER['REMOTE_ADDR'], 0, strpos($_SERVER['REMOTE_ADDR'], ".", 4))."%' AND `PollID` = ".$row[0].";"; //Hier wird nach der IP des Benutzers gesucht. Hierbei werden die zeichen gelöscht, die nach dem zweiten Punkt vorkommen (wir errinnern uns, dass die ersten zwei Stellen immer gleich bleiben).
//$query2 = mysqli_query($conn, $sql2); // Ausführen
//$voted = mysqli_fetch_row($query2); // In Array
//if($voted != "") //Wenn nichts gefunden wurde, gabs nichts. Aber wenn was gefunden wurde (!=), hat der User schon gevoted
//{
//echo "Du hast leider schon gevotet!";
//}
//else
//{
echo "<b>".$row[1]."</b><br>";
echo "<input type='hidden' name='pollid' value='".$row[0]."' />";
$sql = "SELECT * FROM `poll_answers` WHERE `pollid` = ".$row[0];
$query = mysqli_query($conn, $sql);
while($row = mysqli_fetch_row($query))
{
    echo "<input type='radio' name='answer' value='".$row[0]."' />".$row[1]."<br>";
}
?>
<input type='submit' value='Abstimmen!'>

</form>