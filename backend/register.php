<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$database = "psych_db";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "连接失败: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // 验证用户名首字母为字母
    if (!preg_match('/^[a-zA-Z]/', $username)) {
        echo json_encode(["success" => false, "message" => "用户名必须以字母开头"]);
        exit;
    }

    // 验证密码长度 6-8 位
    if (strlen($password) < 6 || strlen($password) > 8) {
        echo json_encode(["success" => false, "message" => "密码长度必须为6-8位"]);
        exit;
    }

    // 验证两次密码一致
    if ($password !== $password_confirm) {
        echo json_encode(["success" => false, "message" => "两次密码不一致"]);
        exit;
    }

    // 验证邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "邮箱格式不正确"]);
        exit;
    }

    // 检查用户名或邮箱是否已存在
    $sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["success" => false, "message" => "用户名或邮箱已存在"]);
        exit;
    }

    // 加密密码并插入用户
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, address) VALUES ('$username', '$email', '$hashed_password', '$address')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "注册成功"]);
    } else {
        echo json_encode(["success" => false, "message" => "注册失败: " . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
