<?php
session_start();
// Проверка существования сессии пользователя
if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}
// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
// Получаем ID записи из POST-запроса
    $recordingId = isset($_POST['recordingId']) ? intval($_POST['recordingId']) : 0;
// Ваш код для проверки, принадлежит ли запись текущему пользователю
    $userId = $_SESSION['user_ID'];
    if (isRecordingBelongsToUser($pdo, $recordingId, $userId)) {
// Если запись принадлежит пользователю, удаляем ее
        cancelRecording($pdo, $recordingId);
        echo "Запись успешно отменена.";
    } else {
        echo "Ошибка: Запись не принадлежит текущему пользователю.";
    }
} else {
// Если запрос не является POST-запросом, перенаправляем пользователя
    header("Location: dashboard.php");
    exit();
}
// Функция для проверки, принадлежит ли запись пользователю
function isRecordingBelongsToUser($pdo, $recordingId, $userId) {
    $query = "SELECT COUNT(*) FROM records WHERE records_ID = :recording_ID AND user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':recording_ID', $recordingId, PDO::PARAM_INT);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn() > 0;
}
// Функция для отмены записи
function cancelRecording($pdo, $recordingId) {
    $query = "DELETE FROM records WHERE records_ID = :recording_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':recording_ID', $recordingId, PDO::PARAM_INT);
    $statement->execute();
}
?>