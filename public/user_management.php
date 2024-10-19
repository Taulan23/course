<?php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || checkUserRole($_SESSION['user_id']) !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$users = getAllUsers();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>КСТ - Управление пользователями</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Управление пользователями</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Имя пользователя</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <button onclick="editUser(<?= $user['id'] ?>)">Редактировать</button>
                    <button onclick="deleteUser(<?= $user['id'] ?>)">Удалить</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <button onclick="addUser()">Добавить пользователя</button>
        <a href="manage_users.php">Назад</a>
    </div>
    <script src="../js/user_management.js"></script>
</body>
</html>
