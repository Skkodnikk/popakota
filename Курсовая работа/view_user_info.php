<?php
session_start();
$host = 'localhost';
$db_name = 'electronic';
$username = 'kot';
$password = 'owo';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
// Проверка существования сессии пользователя
if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}
// Определение роли пользователя
$userId = $_SESSION['user_ID'];
$userInfo = getUserInfo($pdo, $userId);
$roleName = getRoleName($pdo, $userInfo['role_ID']);
function getRoleName($pdo, $roleId) {
    $query = "SELECT role_name FROM Roles WHERE role_ID = :role_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':role_ID', $roleId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['role_name'] : 'user';
}
function getUserInfo($pdo, $userId) {
    $query = "SELECT * FROM users WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}
// Проверка роли администратора
if ($roleName !== 'admin') {
    header("Location: dashboard.php");
    exit();
}
// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedUsername'])) {
    $selectedUsername = $_POST['selectedUsername'];
    $selectedUserInfo = getSelectedUserInfo($pdo, $selectedUsername);
// Ваш код для отображения информации о выбранном пользователе
// Например, выведем имя и email:
    echo "<h2>Информация о выбранном пользователе:</h2>";
    echo "<p>Имя: " . htmlspecialchars($selectedUserInfo['username']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($selectedUserInfo['email']) . "</p>";
// Здесь можете добавить дополнительные сведения о пользователе
} else {
// Если пользователь не выбран, перенаправляем на страницу с выбором пользователя
    header("Location: dashboard.php");
    exit();
}
// Функция для получения информации о выбранном пользователе
function getSelectedUserInfo($pdo, $selectedUsername) {
    $query = "SELECT * FROM users WHERE username = :username";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':username', $selectedUsername, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}
?>