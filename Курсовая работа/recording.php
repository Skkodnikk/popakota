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
// Получение списка мастеров для выбора
$stmtTables = $pdo->prepare("SELECT master_ID, ability FROM masters");
$stmtTables->execute();
$master = $stmtTables->fetchAll(PDO::FETCH_ASSOC);
// Обработка формы записи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_ID = $_SESSION['user_ID'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $electronics = $_POST['electronics'];
// Поиск подходящего мастера в зависимости от поломки электроники
    $stmtFindMaster = $pdo->prepare("SELECT master_ID FROM masters WHERE ability >= ? ORDER BY ability ASC LIMIT 1");
    $stmtFindMaster->execute([$electronics]);
    $master = $stmtFindMaster->fetch(PDO::FETCH_ASSOC);
    if (!$master) {
        die("Извините, нет подходящего мастера для указанного количества устройств.");
    }
    $master_ID = $master['master_ID'];
// Проверка доступности выбранного времени и мастера 
    $stmtCheckAvailAbility = $pdo->prepare("SELECT COUNT(*) FROM records WHERE records_date = ? AND records_time = ? AND master_ID = ?");
    $stmtCheckAvailAbility->execute([$date, $time, $master_ID]);
    $availAbilityCount = $stmtCheckAvailAbility->fetchColumn();
    if ($availAbilityCount > 0) {
        die("Выбранное время или мастер уже заняты. Пожалуйста, выберите другое время или мастера.");
    }
// Вставка записи о записи в базу данных
    $stmtInsertRecords = $pdo->prepare("INSERT INTO records (user_ID, master_ID, electronics, records_date, records_time) VALUES (?, ?, ?, ?, ?)");
    $stmtInsertRecords->execute([$user_ID, $master_ID, $electronics, $date, $time ]);
// Редирект после успешной записи
    header("Location: dashboard.php");
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="recording.css">
    <title>Запись к мастеру</title>
</head>
<body>
<div class="user-info">
    <?php
// Проверка существования сессии пользователя
    if (isset($_SESSION['username'])) {
        echo 'Здравствуй, ' .'<a href="dashboard.php">'.htmlspecialchars($_SESSION['username']).'</a>'. '!';
        echo '<br><a href="logout.php" class="logout-button">Выход</a>';
    }
    ?>
</div>
<div class="container">
    <h2>Запись к мастеру</h2>
    <form action="recording.php" method="post">
        <label for="date">Дата:</label>
        <input type="date" name="date" required><br>
        <label for="time">Время:</label>
        <input type="time" name="time" required><br>
        <label for="electronics">Количество техники:</label>
        <input type="number" name="electronics" min="1" required><br>
        <button type="submit">Записаться</button>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var recordingForm = document.getElementById("recordingForm");
        var recordingButton = document.getElementById("recordingButton");
// Проверка наличия авторизации
        var isUserAuthenticated = <?php echo isset($_SESSION['user_ID']) ? 'true' : 'false'; ?>;
// Установка состояния кнопки в зависимости от авторизации
        recordingButton.disabled = !isUserAuthenticated;
    });
</script>
</body>
</html>