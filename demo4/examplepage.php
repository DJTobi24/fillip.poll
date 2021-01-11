<?php
// examplepage.php
include 'function.php';
$pdo = pdo_connect_mysql();
?>

<?=template_header('Example Page')?>

<p>Example Seite Test 123 Hallöö</p>

<?=template_footer()?>