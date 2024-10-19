<?php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || checkUserRole($_SESSION['user_id']) !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$admin_info = getUserInfo($admin_id);
$users = getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $full_name = $_POST['full_name'] ?? '';
                addUser($_POST['username'], $_POST['password'], $_POST['email'], $full_name, $_POST['role']);
                break;
            case 'edit':
                editUser($_POST['user_id'], $_POST['username'], $_POST['email'], $_POST['role']);
                break;
            case 'delete':
                deleteUser($_POST['user_id']);
                break;
        }
    }
    header("Location: manage_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Платформа обмена документами - Управление пользователями</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Управление пользователями</h1>
        <div class="admin-info">
            <h2>Информация об администраторе</h2>
            <p>Имя: <?= htmlspecialchars($admin_info['username']) ?></p>
            <p>Email: <?= htmlspecialchars($admin_info['email']) ?></p>
        </div>
        
        <h2>Список пользователей</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Имя пользователя</th>
                <th>Email</th>
                <th>Полное имя</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['full_name'] ?? '') ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <button onclick="editUser(<?= $user['id'] ?>)">Редакти��овать</button>
                    <form action="manage_users.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">Удалить</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>Добавить нового пользователя</h2>
        <form action="manage_users.php" method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="full_name" placeholder="Полное имя" required>
            <select name="role">
                <option value="user">Пользователь</option>
                <option value="admin">Адм��нистратор</option>
            </select>
            <button type="submit">Добавить пользователя</button>
        </form>
        
        <a href="dashboard.php">Назад к панели управления</a>
    </div>
    <script src="../js/manage_users.js"></script>
</body>
</html>
