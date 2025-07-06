<?php
session_start(); // 初始化 SESSION
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "123456";
$database = "psych_db";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "数据库连接失败: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username = '$input_username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // 防止会话固定攻击
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            error_log('SESSION set: ' . print_r($_SESSION, true)); // 调试
            echo json_encode([
                "success" => true,
                "message" => "登录成功",
                "session_id" => session_id()
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "无效的用户名或密码"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "无效的用户名或密码"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "无效请求"]);
}

mysqli_close($conn);
?>