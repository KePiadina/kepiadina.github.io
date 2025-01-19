<?php
$server = "localhost"; 
$username = "catbot"; 
$password = "S5JNeQDGGhMBJ4DeUUKhjeVuJGaJKgZ3gbCLwKu9eJMx9TzaBCoVyedDs59sirdw"; 
$database = "felini";

$conn = new mysqli($server, $username, $password, $database);

if ($conn->connect_error) {
    echo "Errore di connessione: " . $conn->connect_error;
}
?>
