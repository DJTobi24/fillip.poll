<?php
include 'config/config.inc.php';
$pdo = pdo_connect_mysql();
$msg = '';
// Schaut ob es die Umfrage ID gibt.
if (isset($_GET['id'])) {
    // Sucht die Zeile raus die gelöscht werden soll. 
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$poll) {
        die ('Poll doesn\'t exist with that ID!');
    }
    // Nachfragen ob die Umfrage gelöscht werden soll
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'ja') {
            // Wen der Benutzer ja klick wird der Datensatz gelöscht.
            $stmt = $pdo->prepare('DELETE FROM polls WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            // Die Antworten der Umfrage müssen auch gelöscht werden
            $stmt = $pdo->prepare('DELETE FROM poll_answers WHERE poll_id = ?');
            $stmt->execute([$_GET['id']]);
            // Ausgangsnachricht
            $msg = 'Du hast die Umfrage Erfolgreich Gelöscht! | Du wirst in 3 sek weitergeleitet';
            sleep(3);
            header('Location: index.php');
            exit;
        } else {
            // Wen der Benutzer auf ein Klickt kommt er auf die Startseite
            header('Location: index.php');
            exit;
        }
    }
} else {
    die ('Du hast keine Unfrage ID Angegeben');
}
?>

<?=template_header('Umfrage Löschen')?>

<div class="content delete">
	<h2>Lösche Umfrage mit der ID #<?=$poll['id']?></h2>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php else: ?>
	<p>Bist du sicher die Umfrage mit der ID #<?=$poll['id']?> zu löschen?</p>
    <div class="yesno">
        <a href="loeschen.php?id=<?=$poll['id']?>&confirm=ja">Ja</a>
        <a href="loeschen.php?id=<?=$poll['id']?>&confirm=nein">Nein</a>
    </div>
    <?php endif; ?>
</div>

<?=template_footer()?>