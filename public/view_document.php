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
$sql = "SELECT d.*, u.username as owner
        FROM documents d
        JOIN users u ON d.uploaded_by = u.id
        WHERE d.id = ? AND (d.uploaded_by = ? OR d.id IN (SELECT document_id FROM document_shares WHERE shared_with = ?))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $document_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("У вас нет доступа к этому документу.");
}

$document = $result->fetch_assoc();

// Получаем комментарии к документу
$comments_sql = "SELECT c.*, u.username
                 FROM comments c
                 JOIN users u ON c.user_id = u.id
                 WHERE c.document_id = ?
                 ORDER BY c.created_at DESC";
$comments_stmt = $conn->prepare($comments_sql);
$comments_stmt->bind_param("i", $document_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр документа - <?= htmlspecialchars($document['title']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($document['title']) ?></h1>
        <p>Загружен пользователем: <?= htmlspecialchars($document['owner']) ?></p>
        <p>Дата загрузки: <?= $document['created_at'] ?></p>
        
        <?php
        $file_path = $document['file_path'];
        $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        if (in_array($file_type, ['pdf', 'jpg', 'jpeg', 'png', 'gif'])) {
            echo "<embed src='../{$file_path}' type='application/{$file_type}' width='100%' height='600px' />";
        } elseif (in_array($file_type, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])) {
            $viewer_url = "https://view.officeapps.live.com/op/embed.aspx?src=" . urlencode('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../' . $file_path);
            echo "<iframe src='$viewer_url' width='100%' height='600px' frameborder='0'></iframe>";
        } elseif ($file_type === 'txt') {
            $content = file_get_contents('../' . $file_path);
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
        } else {
            echo "<p>Предпросмотр для данного типа файла недоступен. <a href='../download_document.php?id={$document_id}' target='_blank'>Скачать файл</a></p>";
        }
        ?>

        <h2>Комментарии</h2>
        <div id="comments">
            <?php while ($comment = $comments_result->fetch_assoc()): ?>
                <div class="comment">
                    <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['content']) ?></p>
                    <small><?= $comment['created_at'] ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <form id="commentForm">
            <textarea name="comment" placeholder="Оставьте свой комментарий" required></textarea>
            <button type="submit">Отправить комментарий</button>
        </form>

        <a href="dashboard.php">Вернуться к панели управления</a>
    </div>

    <script>
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const comment = this.comment.value;
            fetch('add_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `document_id=<?= $document_id ?>&comment=${encodeURIComponent(comment)}`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    alert('Ошибка при добавлении комментария: ' + result.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при добавлении комментария');
            });
        });
    </script>
</body>
</html>
