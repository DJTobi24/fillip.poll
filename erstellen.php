<?php
include 'config/config.inc.php';
$pdo = pdo_connect_mysql();
$msg = '';

// Überprüfen ob $_POST leer ist

if (!empty($_POST)) {

    // Füge die Eingegebenen Datensetze ein
    // Schaue ob in der Post Variable etwas drinnen steht, wen nicht das bleibt es leer.
    $frage = isset($_POST['frage']) ? $_POST['frage'] : '';
    $beschr = isset($_POST['beschr']) ? $_POST['beschr'] : '';
    // Neuer Eintrag in die polls Tabelle mit den obrigen daten
    $stmt = $pdo->prepare('INSERT INTO polls VALUES (NULL, ?, ?)');
    $stmt->execute([$frage, $beschr]);
    // Unten erhalten Sie die letzte Einfügungs-ID. Dies ist die Umfrage-ID
    $poll_id = $pdo->lastInsertId();
    // Schreibe die Antworten in einen array, damit wir jede Antwort in die "poll_answers" Tabelle einfügen können.
    $antwort = isset($_POST['antwort']) ? explode(PHP_EOL, $_POST['antwort']) : 'Keine Antworten';
    foreach ($antwort as $antwort) {
        // Wen die Antworten leer sind wird dortzdem Fortgefahren
        if (empty($antwort)) continue;
        // Antwort in die "poll_answers" Tabelle Schreiben
        $stmt = $pdo->prepare('INSERT INTO poll_answers VALUES (NULL, ?, ?, 0)');
        $stmt->execute([$poll_id, $antwort]);
    }
    // Ausgabe Nachricht
    $msg = 'Umfrage Erfolgreich Erstellt!';
}
?>

<?=template_header('Umfrage Erstellen')?>


<div class="content update">
	<h2>Umfrage Erstellen</h2>
    <form action="erstellen.php" method="post" >
        <label for="frage">Frage</label>
        <input type="text" name="frage" id="frage">
        <label for="beschr">Beschreibung</label>
        <input type="text" name="beschr" id="beschr">
        <label for="antwort">Antworten (pro Zeile 1)</label>
        <textarea name="antwort" id="antwort"></textarea>
        <input type="submit" value="Create">
    </form>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php endif; ?>
</div>

<?=template_footer()?>