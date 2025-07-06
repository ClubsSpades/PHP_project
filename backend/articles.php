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

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($article_id) {
    // 单篇文章查询
    $sql = "SELECT a.id, a.title, a.thumbnail, a.summary, a.content, a.source, a.date, a.category_id, c.name AS category_name
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $article_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $article = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'thumbnail' => $row['thumbnail'] ?: '/php/php_project/picture/default.jpg',
            'summary' => $row['summary'],
            'content' => $row['content'],
            'source' => $row['source'],
            'date' => $row['date'],
            'category_id' => (int)$row['category_id'],
            'category_name' => $row['category_name']
        ];
        echo json_encode($article);
    } else {
        echo json_encode(['error' => '文章不存在']);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

// 文章列表查询
$sql = "SELECT a.id, a.title, a.thumbnail, a.summary, a.content, a.source, a.date, a.category_id, c.name AS category_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE 1=1";
$params = [];
$types = '';

if ($search) {
    $sql .= " AND a.title LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}
if ($category_id) {
    $sql .= " AND a.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$articles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $articles[] = [
        'id' => (int)$row['id'],
        'title' => $row['title'],
        'thumbnail' => $row['thumbnail'] ?: '/php/php_project/picture/default.jpg',
        'summary' => $row['summary'],
        'content' => $row['content'],
        'source' => $row['source'],
        'date' => $row['date'],
        'category_id' => (int)$row['category_id'],
        'category_name' => $row['category_name']
    ];
}

echo json_encode($articles);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>