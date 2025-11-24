<?php


$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bios_errors';



$conn = mysqli_connect($host, $username, $password, $database);


if (!$conn) {
    
    die('Błąd połączenia: ' . mysqli_connect_error());
}



mysqli_set_charset($conn, 'utf8');
?>
