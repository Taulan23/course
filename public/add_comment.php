<?php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['document_id']) || !isset($_POST['comment'])) {
    echo json_encode(['success' => false, 'error' => 'Недостаточно данных']);
    exit();
}

$document_id = intval($_POST['document_id']);
$user_id = $_SESSION['user_id'];
$comment = $_POST['comment'];

$sql = "INSERT INTO comments (document_id, user_id, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $document_id, $user_id, $comment);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
