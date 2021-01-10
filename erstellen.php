<html>
   <head>
      <title>Add New Record in MySQLi Database</title>
      <link rel = "stylesheet" type = "text/css" href = "style.css">
   </head>
   
   <body>
      <div id = "main">
         <form action = "" method = "post">
            <label>Frage :</label>
            <input type = "text" name = "frage" id = "frage" />
            <br />
            <br />
            <input type = "submit" value ="Submit" name = "Erstellen"/>
            <br />
         </form>
      </div>
      
      <?php
         if(isset($_POST["submit"])){
            include("config/database.inc.php");

            $conn = new mysqli($servername, $username, $password, $dbname);


            if ($conn->connect_error) {
               die("Verbindung Fehlgeschlagen: " . $conn->connect_error);
            } 
            $sql = "INSERT INTO poll(frage)VALUES ('".$_POST["frage"]."')";

            if (mysqli_query($conn, $sql)) {
               echo "Umfrage Erfolgreich erstellt";
            } else {
               echo "Error: " . $sql . "" . mysqli_error($conn);
            }
            $conn->close();
         }
      ?>
   </body>
</html>