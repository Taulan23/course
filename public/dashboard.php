<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = checkUserRole($user_id) === 'admin';
$user_info = getUserInfo($user_id);

// Получаем документы, загруженные пользователем
$sql = "SELECT * FROM documents WHERE uploaded_by = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_documents = $stmt->get_result();

// Получаем документы, которыми поделились с пользователем
$shared_sql = "SELECT d.*, u.username as shared_by_username 
               FROM documents d 
               JOIN document_shares ds ON d.id = ds.document_id 
               JOIN users u ON ds.shared_by = u.id
               WHERE ds.shared_with = ? 
               ORDER BY ds.created_at DESC";
$shared_stmt = $conn->prepare($shared_sql);
$shared_stmt->bind_param("i", $user_id);
$shared_stmt->execute();
$shared_documents = $shared_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Платформа обмена документами - Панель управления</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Платформа обмена документами - Панель управления</h1>
        <div class="user-info">
            <p>Имя: <?= htmlspecialchars($user_info['username']) ?></p>
            <p>Email: <?= htmlspecialchars($user_info['email']) ?></p>
        </div>
        <nav>
            <button onclick="showUploadForm()">Загрузить документ</button>
            <button onclick="посмотреть()">Посмотреть лимиты</button>
            <?php if ($is_admin): ?>
                <button onclick="location.href='manage_users.php'">Управление пользователями</button>
            <?php endif; ?>
            <button onclick="location.href='logout.php'">Выйти</button>
        </nav>
        <div id="uploadForm" style="display:none;">
            <form id="documentUploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="document" id="document" required>
                <input type="text" name="title" placeholder="Название документа" required>
                <input type="submit" value="Загрузить документ">
            </form>
        </div>
        <h2>Ваши документы</h2>
        <div id="documentList">
            <?php while ($row = $user_documents->fetch_assoc()): ?>
                <div class='document'>
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <button onclick='viewDocument(<?= intval($row['id']) ?>)'>Просмотреть</button>
                    <button onclick='downloadDocument(<?= intval($row['id']) ?>)'>Скачать</button>
                    <button onclick='addComment(<?= intval($row['id']) ?>)'>Комментировать</button>
                    <button onclick='shareDocument(<?= intval($row['id']) ?>)'>Поделиться</button>
                    <button onclick='deleteDocument(<?= intval($row['id']) ?>)'>Удалить</button>
                </div>
            <?php endwhile; ?>
        </div>
        
        <h2>Документы, которыми поделились с вами</h2>
        <div id="sharedDocumentList">
            <?php while ($row = $shared_documents->fetch_assoc()): ?>
                <div class='document'>
                    <h3><?= htmlspecialchars($row['title']) ?> (от <?= htmlspecialchars($row['shared_by_username']) ?>)</h3>
                    <button onclick='viewDocument(<?= intval($row['id']) ?>)'>Просмотреть</button>
                    <button onclick='downloadDocument(<?= intval($row['id']) ?>)'>Скачать</button>
                    <button onclick='addComment(<?= intval($row['id']) ?>)'>Комментировать</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>
