<?php

session_start();

// Проверяем, установлен ли CSRF токен в сессии
if (!isset($_SESSION['csrf_token'])) {
    // Если нет, создаем новый и сохраняем в сессии
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Подключение к базе данных
$servername = getenv('MYSQL_SERVER');
$db_user = getenv('MYSQL_USER');
$db_pass = getenv('MYSQL_PASSWORD');
$db_name = getenv('MYSQL_DATABASE');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = mysqli_connect($servername, $db_user, $db_pass, $db_name);
    if ($db === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    // Обработка POST запросов с проверкой CSRF
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Проверяем наличие CSRF токена в запросе
        if (!isset($_POST['csrf_token'])) {
            $_SESSION['message'] = "Security error: CSRF token missing";
            header('Location: index.php');
            exit();
        }
        
        // Проверяем соответствие CSRF токена
        if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = "Security error: CSRF token validation failed";
            header('Location: index.php');
            exit();
        }
        
        // Остальной код обработки POST...
        // [остальная часть кода без изменений]
    }
    
    // Обработка GET запросов (удаление)
    if (isset($_GET['del'])) {
        // Код обработки удаления...
        // [остальная часть кода без изменений]
    }

} catch (Exception $e) {
    // Обработка ошибок...
    // [остальная часть кода без изменений]
}

// Если кто-то попытается напрямую открыть process.php
header('Location: index.php');
exit();
?>
