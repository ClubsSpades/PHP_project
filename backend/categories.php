<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "123456";
$database = "psych_db";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "连接失败: " . $conn->connect_error]));
}

$sql = "SELECT id, name FROM categories ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = [
        'id' => (int)$row['id'],
        'name' => $row['name']
    ];
}

echo json_encode($categories);

mysqli_close($conn);
?>