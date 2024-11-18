<?php
// db.php
declare(strict_types=1);

$host = "localhost";
$user = "root";
$password = "";
$dbname = "clientes_db";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}
?>
