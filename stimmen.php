<?php
include 'config/config.inc.php';
// Mit dem Mysql Server Verbinden
$pdo = pdo_connect_mysql();
// Schaut ob der Datensatz mit der angegebenen ID vorhanden ist.
if (isset($_GET['id'])) {
    // MySQL query such aus der Tabelle die GET Anforderung ID heraus
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Holt sich die Daten
    $umfrage = $stmt->fetch(PDO::FETCH_ASSOC);
    // Schaut ob die ID existiert
    if ($umfrage) {
        // MySQL query wählt alle antworten aus
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ?');
        $stmt->execute([$_GET['id']]);
        // holt sich die Daten
        $umfrage_antwort = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //Wen der Benutzer den Abstimmen Button Betätigt
        if (isset($_POST['frage_antwort'])) {
            // Update die Stimmen
            $stmt = $pdo->prepare('UPDATE poll_answers SET votes = votes + 1 WHERE id = ?');
            $stmt->execute([$_POST['frage_antwort']]);
            // Schickt den Benutzer zu den ergebnissen.
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
	<h2><?=$umfrage['title']?></h2>
	<p><?=$umfrage['desc']?></p>
    <form action="stimmen.php?id=<?=$_GET['id']?>" method="post">
        <?php for ($i = 0; $i < count($umfrage_antwort); $i++): ?>
        <label>
            <input type="radio" name="frage_antwort" value="<?=$umfrage_antwort[$i]['id']?>"<?=$i == 0 ? ' checked' : ''?>>
            <?=$umfrage_antwort[$i]['title']?>
        </label>
        <?php endfor; ?>
        <div>
            <input type="submit" value="Abstimmen">
            <a href="ergebniss.php?id=<?=$umfrage['id']?>">Stimmen Anzeigen</a>
        </div>
    </form>
</div>

<?=template_footer()?>