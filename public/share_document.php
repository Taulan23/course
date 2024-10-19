<?php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['document_id']) || !isset($_POST['username'])) {
    echo json_encode(['success' => false, 'error' => 'Недостаточно данных']);
    exit();
}

$document_id = intval($_POST['document_id']);
$user_id = $_SESSION['user_id'];
$shared_username = $_POST['username'];

// Проверяем, существует ли пользователь, с которым хотим поделиться
$user_check_sql = "SELECT id FROM users WHERE username = ?";
$user_check_stmt = $conn->prepare($user_check_sql);
$user_check_stmt->bind_param("s", $shared_username);
$user_check_stmt->execute();
$user_result = $user_check_stmt->get_result();

if ($user_result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не найден']);
    exit();
}

$shared_user_id = $user_result->fetch_assoc()['id'];

// Проверяем, имеет ли текущий пользователь право делиться документом
$document_check_sql = "SELECT id FROM documents WHERE id = ? AND uploaded_by = ?";
$document_check_stmt = $conn->prepare($document_check_sql);
$document_check_stmt->bind_param("ii", $document_id, $user_id);
$document_check_stmt->execute();
$document_result = $document_check_stmt->get_result();

if ($document_result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'У вас нет прав для совместного использования этого документа']);
    exit();
}

// Добавляем запись о совместном использовании
$share_sql = "INSERT INTO document_shares (document_id, shared_by, shared_with) VALUES (?, ?, ?)";
$share_stmt = $conn->prepare($share_sql);
$share_stmt->bind_param("iii", $document_id, $user_id, $shared_user_id);

if ($share_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
