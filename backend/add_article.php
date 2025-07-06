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

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "请先登录"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $thumbnail = $_FILES['thumbnail']['name'] ? '/php/php_project/picture/' . basename($_FILES['thumbnail']['name']) : '/php/php_project/picture/default.jpg';
    $summary = mysqli_real_escape_string($conn, $_POST['summary']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    // 上传图片
    if ($_FILES['thumbnail']['name']) {
        $target_dir = "../picture/";
        $target_file = $target_dir . basename($_FILES['thumbnail']['name']);
        if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
            echo json_encode(["success" => false, "message" => "图片上传失败"]);
            exit;
        }
    }

    $sql = "INSERT INTO articles (title, thumbnail, summary, content, source, date, category_id) 
            VALUES ('$title', '$thumbnail', '$summary', '$content', '$source', '$date', '$category_id')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "添加成功"]);
    } else {
        echo json_encode(["success" => false, "message" => "添加失败: " . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>