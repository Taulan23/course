<?php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No document specified']);
    exit();
}

$document_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "SELECT file_path FROM documents WHERE id = ? AND uploaded_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $document_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Document not found or you don\'t have permission to delete it']);
    exit();
}

$document = $result->fetch_assoc();
$file_path = $document['file_path'];

$sql = "DELETE FROM documents WHERE id = ? AND uploaded_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $document_id, $user_id);

if ($stmt->execute()) {
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    echo json_encode(['success' => 'Document deleted successfully']);
} else {
    echo json_encode(['error' => 'Failed to delete document']);
}
