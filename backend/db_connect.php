<?php
//此文件没有参与项目，原本想做成接口就不用重复这段代码连接数据库
//但是引用没成功，索性就多复制几遍用了。
$servername = "localhost";
$username = "root";
$password = "123456";
$database = "psych_db";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "连接失败: " . $conn->connect_error]));
}
?>