
 
<form method="post" action="config/vote.php">
<?php
include("config/database.inc.php"); // <- Datenbank einbinden

$sql = "SELECT * FROM `poll` WHERE `Aktiv` = 1 LIMIT 0,1"; // SQL String. Limit ist 1
$query = mysqli_query($conn, $sql); // Query ausführen
$row = mysqli_fetch_row($query); // In Array packen



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
