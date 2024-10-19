<?php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$document_id = intval($_GET['id']);

// Проверяем, имеет ли пользователь доступ к документу
$sql = "SELECT file_path, title FROM documents WHERE id = ? AND (uploaded_by = ? OR shared_with = ? OR id IN (SELECT document_id FROM document_shares WHERE shared_with = ?))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $document_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("У вас нет доступа к этому документу.");
}

$document = $result->fetch_assoc();
$file_path = $document['file_path'];
$file_name = $document['title'];

if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    die("Файл не найден.");
}
