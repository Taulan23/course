<?php
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('memory_limit', '256M');

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log("Upload attempt: " . print_r($_FILES, true));
error_log("POST data: " . print_r($_POST, true));
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$title = $_POST['title'] ?? '';
$target_dir = "../uploads/";

if (!file_exists($target_dir)) {
    if (!mkdir($target_dir, 0777, true)) {
        echo json_encode(['error' => 'Failed to create upload directory']);
        exit();
    }
}

if (!isset($_FILES["document"]) || $_FILES["document"]["error"] != UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'File upload error: ' . ($_FILES["document"]["error"] ?? 'Unknown error')]);
    exit();
}

$target_file = $target_dir . basename($_FILES["document"]["name"]);
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Проверка размера файла
if ($_FILES["document"]["size"] > 50000000) { // 50 MB limit
    echo json_encode(['error' => 'File is too large. Maximum size is 50 MB.']);
    exit();
}

// Разрешенные типы файлов
$allowed_types = ['pdf', 'doc', 'docx', 'txt'];
if (!in_array($fileType, $allowed_types)) {
    echo json_encode(['error' => 'Only PDF, DOC, DOCX and TXT files are allowed.']);
    exit();
}

if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
    $sql = "INSERT INTO documents (title, file_path, file_type, uploaded_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $target_file, $fileType, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'File ' . basename($_FILES["document"]["name"]) . ' has been uploaded.']);
    } else {
        echo json_encode(['error' => 'Database error: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Failed to move uploaded file. Error: ' . error_get_last()['message']]);
}
