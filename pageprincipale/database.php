<?php
   $db_server = "localhost";
   $db_user = "root";
   $db_password = "";
   $db_name = "achats";
   $db_port = 3307;

   $conn = mysqli_connect($db_server, $db_user, $db_password, $db_name, $db_port);

   if (!$conn) {
       die("Connection failed: " . mysqli_connect_error());
   }
?>
