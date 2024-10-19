<?php
session_start();

// Уничтожаем все данные сессии
$_SESSION = array();

// Если используются сессионные cookie, то удаляем их
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на страницу входа
header("Location: index.php");
exit();
