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
            $aktiv = "1";
            $sql1 = "SELECT * FROM `poll`";
            $umfragen = mysqli_query( $conn, $sql1 );
            $anzahl = mysqli_num_rows($umfragen);


            $sql = "INSERT INTO `poll` (`ID`, `Frage`, `Datum`, `Aktiv`)VALUES ('".$anzahl."', '".$_POST["frage"]."', NOW(), '".$aktiv."')";

            if (mysqli_query($conn, $sql)) {
               echo "Umfrage Erfolgreich erstellt";
            } else {
               echo '<script>console.log("Consolen LOG: ' . $sql . mysqli_error($conn) . '")</script>';
            }
            $conn->close();
         }
      ?>
   </body>
</html>