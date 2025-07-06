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
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $thumbnail = $_FILES['thumbnail']['name'] ? '/php/php_project/picture/' . basename($_FILES['thumbnail']['name']) : $_POST['existing_thumbnail'];
    $summary = mysqli_real_escape_string($conn, $_POST['summary']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    // 上传新图片
    if ($_FILES['thumbnail']['name']) {
        $target_dir = "../picture/";
        $target_file = $target_dir . basename($_FILES['thumbnail']['name']);
        if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
            echo json_encode(["success" => false, "message" => "图片上传失败"]);
            exit;
        }
    }

    $sql = "UPDATE articles SET title='$title', thumbnail='$thumbnail', summary='$summary', content='$content', 
            source='$source', date='$date', category_id='$category_id' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "修改成功"]);
    } else {
        echo json_encode(["success" => false, "message" => "修改失败: " . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>