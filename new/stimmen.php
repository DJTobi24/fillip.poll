<?php
include 'config/config.inc.php';
// Mit dem Mysql Server Verbinden
$pdo = pdo_connect_mysql();
// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // MySQL query that selects the poll records by the GET request "id"
    $stmt = $pdo->prepare('SELECT * FROM umfragem WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the record
    $umfrage = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the poll record exists with the id specified
    if ($umfrage) {
        // MySQL query that selects all the poll answers
        $stmt = $pdo->prepare('SELECT * FROM umfrage_antwort WHERE umfrage_id = ?');
        $stmt->execute([$_GET['id']]);
        // Fetch all the poll anwsers
        $umfrage_antwort = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // If the user clicked the "Vote" button...
        if (isset($_POST['frage_antwort'])) {
            // Update and increase the vote for the answer the user voted for
            $stmt = $pdo->prepare('UPDATE umfrage_antwort SET antworten = antworten + 1 WHERE id = ?');
            $stmt->execute([$_POST['frage_antwort']]);
            // Redirect user to the result page
            header ('Location: ergebniss.php?id=' . $_GET['id']);
            exit;
        }
    } else {
        die ('Eine Umfrage mit dieser ID Existiert nicht.');
    }
} else {
    die ('Keine ID ausgewählt.');
}
?>

<?=template_header('Umfrage Ergebniss')?>

<div class="content poll-vote">
	<h2><?=$umfrage['frage']?></h2>
	<p><?=$umfrage['besch']?></p>
    <form action="stimmen.php?id=<?=$_GET['id']?>" method="post">
        <?php for ($i = 0; $i < count($umfrage_antwort); $i++): ?>
        <label>
            <input type="radio" name="frage_antwort" value="<?=$umfrage_antwort[$i]['id']?>"<?=$i == 0 ? ' checked' : ''?>>
            <?=$umfrage_antwort[$i]['antworten']?>
        </label>
        <?php endfor; ?>
        <div>
            <input type="submit" value="Abstimmen">
            <a href="ergebniss.php?id=<?=$umfrage['id']?>">Stimmen Anzeigen</a>
        </div>
    </form>
</div>

<?=template_footer()?>