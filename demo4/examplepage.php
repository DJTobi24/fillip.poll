<?php
// examplepage.php
include 'functions.php';
$pdo = pdo_connect_mysqli();
?>

<?=template_header('Example Page')?>

<p>Hello World! Welcome to my custom page!</p>

<?=template_footer()?>