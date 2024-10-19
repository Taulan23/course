<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
header("Location: login.php");
exit();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- ... -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- ... -->
    <script src="../js/script.js"></script>
</body>
</html>
