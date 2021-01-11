<?php



$sql = file_get_contents('database.sql');

include("db_connect.php");

/* execute multi query */
$mysqli->multi_query($sql);
?>