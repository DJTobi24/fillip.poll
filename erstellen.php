<?php
include 'config/config.inc.php';
$pdo = pdo_connect_mysql();
$msg = '';

// Check if POST data is not empty

if (!empty($_POST)) {

    // Post data not empty insert a new record
    // Check if POST variable "frage" exists, if not default the value to blank, basically the same for all variables
    $frage = isset($_POST['frage']) ? $_POST['frage'] : 'Keine Frage';
    $beschr = isset($_POST['beschr']) ? $_POST['beschr'] : 'Keine Beschreibung';
    // Insert new record into the "polls" table
    $stmt = $pdo->prepare('INSERT INTO polls VALUES (NULL, ?, ?)');
    $stmt->execute([$frage, $beschr]);
    // Below will get the last insert ID, this will be the poll id
    $poll_id = $pdo->lastInsertId();
    // Get the antwort and convert the multiline string to an array, so we can add each answer to the "poll_answers" table
    $antwort = isset($_POST['antwort']) ? explode(PHP_EOL, $_POST['antwort']) : 'Keine Antworten';
    foreach ($antwort as $antwort) {
        // If the answer is empty there is no need to insert
        if (empty($antwort)) continue;
        // Add answer to the "poll_antwort" table
        $stmt = $pdo->prepare('INSERT INTO poll_answers VALUES (NULL, ?, ?, 0)');
        $stmt->execute([$poll_id, $antwort]);
    }
    // Output message
    $msg = 'Created Successfully!';
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