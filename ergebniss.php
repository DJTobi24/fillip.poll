<?php
include 'config/config.inc.php';
// Mit dem Mysql Server Verbinden
$pdo = pdo_connect_mysql();
// Schaut ob der die "id" im GET Befehl existiert.
if (isset($_GET['id'])) {
    // MySQL query wählt von der Tabelle "poll" mit dem GET Request und der ID das ERgebniss aus.
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Holt sich den Datensatzt
    $umfrag = $stmt->fetch(PDO::FETCH_ASSOC);
    // Schaut ob der Datensatz mit der angegebenen ID vorhanden ist.
    if ($umfrag) {
        // Alle Antworten aus der Tabelle "poll_answers" werden nach der Anzahl der Stimmen (absteigend) Sotiert
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ? ORDER BY votes DESC');
        $stmt->execute([$_GET['id']]);
        // Holt sich alle Stimmen
        $umfrag_antworten = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Alle Stimmen werden zusammen gezählt um die Prozentzahl richtig auszurechen.
        $total_abstimmungen = 0;
        foreach ($umfrag_antworten as $umfrag_antwort) {
            // Jede Stimmen werden zu den Gesammtstimmen addiert.
            $total_abstimmungen += $umfrag_antwort['votes'];
        }
    } else {
        die ('Die Angeforderte Umfrage ID gibt es nicht.');
    }
} else {
    die ('Sie haben keine Umfrage ID angegeben.');
}
?>

<?=template_header('Umfrage Ergebnisse')?>

<div class="content poll-result">
	<h2><?=$umfrag['title']?></h2>
	<p><?=$umfrag['desc']?></p>
    <div class="wrapper">
        <?php foreach ($umfrag_antworten as $umfrag_antwort): ?>
        <div class="poll-question">
            <p><?=$umfrag_antwort['title']?> <span>(<?=$umfrag_antwort['votes']?> Stimme(n))</span></p>
            <div class="result-bar" style= "width:<?=@round(($umfrag_antwort['votes']/$total_abstimmungen)*100)?>%">
                <?=@round(($umfrag_antwort['votes']/$total_abstimmungen)*100)?>%
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?=template_footer()?>