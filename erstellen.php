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

            $sql1 = "SELECT * FROM `poll`";

                $umfragen = mysqli_query( $conn, $sql1 );
                if ( ! $umfragen )
                {
                    die('Ungültige Abfrage: ' . mysqli_error());
                }
                $anzahl_eintraege = mysqli_num_rows($umfragen);

            //$sql = "INSERT INTO poll(Frage)VALUES ('".$_POST["frage"]."') . poll(id)VALUES ('.$umfragen.')";
            $sql = "INSERT INTO `poll` (`ID`, `Frage`, `Datum`, `Aktiv`) VALUES ('".$umfragen."', '".$_POST["frage"]."', NOW(), '"1"');";
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