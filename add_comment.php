<?php
require_once 'db.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$ref_id = (int)($_POST['reference_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$uid = current_user_id();

if ($ref_id <= 0 || $comment === '') {
    header('Location: index.php');
    exit;
}

// Verify that the reference exists
$stmt = $pdo->prepare('SELECT id FROM references_entity WHERE id = ?');
$stmt->execute([$ref_id]);
if (!$stmt->fetch()) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO comments (reference_id, user_id, comment) VALUES (?, ?, ?)');
$stmt->execute([$ref_id, $uid, $comment]);

header('Location: index.php#ref-' . $ref_id);
exit;