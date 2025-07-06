<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    echo json_encode([
        "username" => $_SESSION['username'],
        "user_id" => $_SESSION['user_id'],
        "session_id" => session_id()
    ]);
} else {
    echo json_encode(["username" => null, "session_id" => session_id()]);
}
?>