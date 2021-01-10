<?php
$link = mysqli_connect("127.0.0.1", "nutzer", "passwort", "datenbank");

if (!$link) {
    echo "Fehler: konnte nicht mit MySQL verbinden." . PHP_EOL;
    echo "Debug-Fehlernummer: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debug-Fehlermeldung: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

echo "Erfolg: es wurde ordnungsgemäß mit MySQL verbunden! Die Datenbank \"datenbank\" ist toll." . PHP_EOL;
echo "Host-Informationen: " . mysqli_get_host_info($link) . PHP_EOL;

mysqli_close($link);
?>