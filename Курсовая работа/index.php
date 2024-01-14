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
    header("Location: registration.php");
    exit();
}
else{
    header("Location: dashboard.php");
    exit();
}