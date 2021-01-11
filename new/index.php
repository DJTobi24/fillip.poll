<?php
include 'config/config.inc.php';
// Mit dem Mysql Server Verbinden
$pdo = pdo_connect_mysql();
// MySQL query that selects all the polls and poll answers
$stmt = $pdo->query('SELECT p.*, GROUP_CONCAT(pa.frage ORDER BY pa.id) AS antworten FROM umfragen p LEFT JOIN umfrage_antwort pa ON pa.umfrage_id = p.id GROUP BY p.id');
$umfragen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?=template_header('Umfragen Website')?>

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
            <?php foreach ($umfragen as $umfrage): ?>
            <tr>
                <td><?=$umfrage['id']?></td>
                <td><?=$umfrage['frage']?></td>
				<td><?=$umfrage['antworten']?></td>
                <td class="actions">
					<a href="stimmen.php?id=<?=$umfrage['id']?>" class="view" title="Umfrage Anzeigen"><i class="fas fa-eye fa-xs"></i></a>
                    <a href="loeschen.php?id=<?=$umfrage['id']?>" class="trash" title="Umfrage Löschen"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?=template_footer()?>