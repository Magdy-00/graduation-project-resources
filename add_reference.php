<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO references_entity (title, url, comment, category, created_by, created_at) VALUES (:title, :url, :comment, :category, :created_by, NOW())");
    $stmt->execute([
        'title' => $_POST['title'],
        'url' => $_POST['url'],
        'comment' => $_POST['comment'],
        'category' => $_POST['category'],
        'created_by' => $_SESSION['user_id']
    ]);
}
header("Location: index.php");
exit;
?>
