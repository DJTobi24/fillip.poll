<?php
function pdo_connect_mysql() {
    // Update the details below with your MySQL details
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'poll4';
    $DATABASE_PASS = '25152515?';
    $DATABASE_NAME = 'phppoll';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// If there is an error with the connection, stop the script and display the error.
    	die ('Failed to connect to database!');
    }
}

// Template header, feel free to customize this
function template_header($title) {
    echo <<<EOT
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>$title</title>
            <link href="style.css" rel="stylesheet" type="text/css">
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        </head>
        <body>
        <nav class="navtop">
            <div>
                <h1>Umfrage Php Script</h1>
                <a href="index.php"><i class="fas fa-poll-h"></i>Umfragen</a>
            </div>
        </nav>
    EOT;
    }

    // Template footer
function template_footer() {
    echo <<<EOT
        </body>
    </html>
    EOT;
    }