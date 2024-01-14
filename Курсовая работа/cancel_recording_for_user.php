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
// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recordingId'])) {
    $recordingId = $_POST['recordingId'];
// Ваш код для отмены записи по $recordingId
    cancelRecording($pdo, $recordingId);
}
// Функция для отмены записи
function cancelRecording($pdo, $recordingId) {
    $query = "DELETE FROM records WHERE recods_ID = :recording_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':recording_ID', $recordingId, PDO::PARAM_INT);
    $statement->execute();
}
?>