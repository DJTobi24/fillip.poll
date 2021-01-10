<?php
include("database.inc.php");
$sql = "SELECT * FROM `poll` ORDER BY `Datum` DESC LIMIT 0,5"; // Alle Fragen nach Datum sortieren, maximal 5
$query = mysqli_query($conn, $sql); // Ausführen
while( $row= mysqli_fetch_row($query)) //Für alle 5 wiederholen
{
    echo "<b>Ergenisse der Umfrage:<br>".$row[1]." vom ".$row[2]."</b>:<br>"; //$row[1] ist die Frage, $row[2] das Datum
$sql2 = "SELECT COUNT(`PollID`) FROM `poll_ip` WHERE `PollID` = ".$row[0];
$query2 = mysqli_query($sql2);
$row2 = mysqli_fetch_row($query2);
    $teilnehmer = $row2[0]; //Teilnehmer in andere Variable schreiben
    $sql3 = "SELECT * FROM `poll_answers` WHERE `PollID` = ".$row[0];
    $query3= mysqli_query($sql3);
    while($row3 = mysqli_fetch_row($query3))
    {
        echo "<table>"; // Tabelle...
        echo "<tr><td>".$row3[1].":</td><td>".round($row3[3] / $teilnehmer * 100) ."%</td></tr>"; // Neue Tabellenreihe, $row[1] war die Frage, $row[3] die Klicks geteilt durch die Anzahl der Teilnehmer * 100
        echo "</table>"; //...
    }   
}
?>