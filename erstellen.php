<html>
   <head>
      <title>Umfrage Erstellen</title>
   </head>
   
   <body>
      <div id = "main">
         <form action = "" method = "post">
            <label>Frage :</label>
            <input type = "text" name = "frage" id = "frage" />
            <br />
            <br />
            <input type = "submit" value ="Umfrage Erstellen" name = "submit"/>
            <br />
         </form>
      </div>
      
      <?php
         if(isset($_POST["submit"])){
            include("config/database.inc.php");

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