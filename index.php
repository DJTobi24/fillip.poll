<?php
include 'config/config.inc.php';
// Mit dem Mysql Server Verbinden
$pdo = pdo_connect_mysql();
// MySQL query that selects all the polls and poll answers
$stmt = $pdo->query('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id GROUP BY p.id');
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?=template_header('Unfrage Website')?>

<div class="content home">
	<h2>Umfragen</h2>
	<p>Hier Findest du alle Umfragen die Erstellt wurden</p>
	<a href="erstellen.php" class="create-poll">Umfrage Erstellen</a>
	<table>
        <thead>
            <tr>
                <td>#</td>
                <td>Frage</td>
				<td>Antworten</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($polls as $poll): ?>
            <tr>
                <td><?=$poll['id']?></td>
                <td><?=$poll['title']?></td>
				<td><?=$poll['answers']?></td>
                <td class="actions">
					<a href="stimmen.php?id=<?=$poll['id']?>" class="view" title="Umfrage Anzeigen"><i class="fas fa-eye fa-xs"></i></a>
                    <a href="loeschen.php?id=<?=$poll['id']?>" class="trash" title="Umfrage Löschen"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?=template_footer()?>