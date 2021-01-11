<?php
include 'config/config.inc.php';
// Mit dem Mysql Server Verbinden
$pdo = pdo_connect_mysql();
// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // MySQL query that selects the poll records by the GET request "id"
    $stmt = $pdo->prepare('SELECT * FROM umfragen WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the record
    $umfrage = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the poll record exists with the id specified
    if ($umfrage) {
        // MySQL Query that will get all the answers from the "poll_answers" table ordered by the number of votes (descending)
        $stmt = $pdo->prepare('SELECT * FROM umfrage_amtwort WHERE umfrage_id = ? ORDER BY stimmen DESC');
        $stmt->execute([$_GET['id']]);
        // Fetch all poll answers
        $umfrage_antworten = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Total number of votes, will be used to calculate the percentage
        $total_abstimmungen = 0;
        foreach ($umfrage_antworten as $umfrage_antwort) {
            // Every poll answers votes will be added to total votes
            $total_abstimmungen += $umfrage_antwort['stimmen'];
        }
    } else {
        die ('Umfrage ID Existiert Nicht.');
    }
} else {
    die ('Umfrage ID nicht angegeben.');
}
?>

<?=template_header('Umfrage Ergebnisse')?>

<div class="content poll-result">
	<h2><?=$umfrage['frage']?></h2>
	<p><?=$umfrage['besch']?></p>
    <div class="wrapper">
        <?php foreach ($umfrage_antworten as $umfrage_antwort): ?>
        <div class="poll-question">
            <p><?=$umfrage_antwort['antworten']?> <span>(<?=$umfrage_antwort['stimmen']?> Votes)</span></p>
            <div class="result-bar" style= "width:<?=@round(($umfrage_antwort['stimmen']/$total_abstimmungen)*100)?>%">
                <?=@round(($umfrage_antwort['stimmen']/$total_abstimmungen)*100)?>%
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?=template_footer()?>