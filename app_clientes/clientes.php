<?php
declare(strict_types=1);
include 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

try {
    $response = match ($method) {
        'GET' => handleGet($conn, $_GET),
        'POST' => handlePost($conn, json_decode(file_get_contents("php://input"), true)),
        'PUT' => handlePut($conn, $_GET, json_decode(file_get_contents("php://input"), true)),
        'DELETE' => handleDelete($conn, $_GET),
        default => json_encode(["error" => "Método no soportado"])
    };
    echo $response;
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

/**
 * Maneja las solicitudes GET
 */
function handleGet(mysqli $conn, array $params): string {
    if (isset($params['id'])) {
        $id = intval($params['id']);
        $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return json_encode($result ?: ["error" => "Cliente no encontrado"]);
    } else {
        $result = $conn->query("SELECT * FROM clientes");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode($data);
    }
}

/**
 * Maneja las solicitudes POST
 */
function handlePost(mysqli $conn, ?array $data): string {
    if (!$data) {
        throw new Exception("Datos no válidos");
    }
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $data['nombre'], $data['email'], $data['telefono']);
    $stmt->execute();
    $stmt->close();
    return json_encode(["message" => "Cliente creado con éxito"]);
}

/**
 * Maneja las solicitudes PUT
 */
function handlePut(mysqli $conn, array $params, ?array $data): string {
    if (!$data || !isset($params['id'])) {
        throw new Exception("Datos o ID no válidos");
    }
    $id = intval($params['id']);
    $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ? WHERE id = ?");
    $stmt->bind_param("sssi", $data['nombre'], $data['email'], $data['telefono'], $id);
    $stmt->execute();
    $stmt->close();
    return json_encode(["message" => "Cliente actualizado con éxito"]);
}

/**
 * Maneja las solicitudes DELETE
 */
function handleDelete(mysqli $conn, array $params): string {
    if (!isset($params['id'])) {
        throw new Exception("ID no válido");
    }
    $id = intval($params['id']);
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    return json_encode(["message" => "Cliente eliminado con éxito"]);
}
?>
