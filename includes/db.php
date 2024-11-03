<?php
$servername = "localhost";
$username = "hangosfilmek";
$password = "HuHJA9-1";
$dbname = "hangosfilmek";

// Kapcsolat létrehozása
$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}
?>