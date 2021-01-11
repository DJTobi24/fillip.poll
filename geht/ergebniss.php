<?php
include 'config/config.inc.php';
// Mit dem Mysql Server Verbinden
$pdo = pdo_connect_mysql();
// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // MySQL query that selects the poll records by the GET request "id"
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the record
    $umfrag = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the poll record exists with the id specified
    if ($umfrag) {
        // MySQL Query that will get all the answers from the "poll_answers" table ordered by the number of votes (descending)
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ? ORDER BY votes DESC');
        $stmt->execute([$_GET['id']]);
        // Fetch all poll answers
        $umfrag_antworten = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Total number of votes, will be used to calculate the percentage
        $total_abstimmungen = 0;
        foreach ($umfrag_antworten as $umfrag_antwort) {
            // Every poll answers votes will be added to total votes
            $total_abstimmungen += $umfrag_antwort['votes'];
        }
    } else {
        die ('Poll with that ID does not exist.');
    }
} else {
    die ('No poll ID specified.');
}
?>

<?=template_header('Umfrage Ergebnisse')?>

<div class="content poll-result">
	<h2><?=$umfrag['title']?></h2>
	<p><?=$umfrag['desc']?></p>
    <div class="wrapper">
        <?php foreach ($umfrag_antworten as $umfrag_antwort): ?>
        <div class="poll-question">
            <p><?=$umfrag_antwort['title']?> <span>(<?=$umfrag_antwort['votes']?> Votes)</span></p>
            <div class="result-bar" style= "width:<?=@round(($umfrag_antwort['votes']/$total_abstimmungen)*100)?>%">
                <?=@round(($umfrag_antwort['votes']/$total_abstimmungen)*100)?>%
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?=template_footer()?>