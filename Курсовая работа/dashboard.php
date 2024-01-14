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
// Получение информации о пользователе
$userId = $_SESSION['user_ID'];
$userInfo = getUserInfo($pdo, $userId);
$roleName = getRoleName($pdo, $userInfo['role_ID']);  
function getAllUsers($pdo) {
    $query = "SELECT user_ID, username FROM users";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
// Функция для удаления пользователя
function deleteUser($pdo, $userId) {
    $query = "DELETE FROM users WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    if ($statement->execute()) {
        echo "Пользователь успешно удален";
    } else {
        echo "Ошибка при удалении пользователя";
    }
}
function cancelRecording($pdo, $recordingId) {
    $query = "DELETE FROM records WHERE records_ID = :records_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':records_ID', $recordingId, PDO::PARAM_INT);
    $statement->execute();
}
function changeUsername($pdo, $userId, $newUsername) {
    $query = "UPDATE users SET username = :username WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':username', $newUsername, PDO::PARAM_STR);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
}
function getUserInfoAndRecordings($pdo, $userId) {
    $userInfo = getUserInfo($pdo, $userId);
    $userRecordings = getUserRecordings($pdo, $userId);
    return [
        'user_info' => $userInfo,
        'recordings' => $userRecordings,
    ];
}
function getRoleName($pdo, $roleId) {
    $query = "SELECT role_name FROM Roles WHERE role_ID = :role_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':role_ID', $roleId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['role_name'] : 'user'; // Возвращаем 'user', если роль не найдена
}
function getUserRecordings($pdo, $userId) {
    $query = "SELECT * FROM records WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
function getUserInfo($pdo, $userId) {
    $query = "SELECT * FROM users WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}
// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Ваш код для обработки действия
    if ($_POST['action'] === 'updateUsername') {
        $newUsername = isset($_POST['newUsername']) ? $_POST['newUsername'] : '';
        updateUsername($pdo, $userId, $newUsername);
    } elseif ($_POST['action'] === 'getUserInfoAndRecordings') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $userInfo = getUserInfoAndRecordings($pdo, $userId);
        echo json_encode($userInfo);
        exit();
    } elseif ($_POST['action'] === 'updateUserInfo') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $newEmail = isset($_POST['newEmail']) ? $_POST['newEmail'] : '';
        $newRole = isset($_POST['newRole']) ? $_POST['newRole'] : '';
        updateUserInfo($pdo, $userId, $newEmail, $newRole);
    }elseif ($_POST['action'] === 'deleteUser') {
        $deleteUserId = isset($_POST['deleteUserId']) ? $_POST['deleteUserId'] : 0;
        deleteUser($pdo, $deleteUserId);
    }elseif ($_POST['action'] === 'changeUserRole') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $newRole = isset($_POST['newRole']) ? $_POST['newRole'] : '';
        changeUserRole($pdo, $userId, $newRole);
    }elseif ($_POST['action'] === 'changeUsername') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $newUsername = isset($_POST['newUsername']) ? $_POST['newUsername'] : '';
        changeUsername($pdo, $userId, $newUsername);
    }elseif ($_POST['action'] === 'cancelRecording') {
        $recordingId = isset($_POST['recordingId']) ? $_POST['recordingId'] : 0;
        cancelRecording($pdo, $recordingId);
    }elseif ($_POST['action'] === 'updateRecording') {
        $recordsId = isset($_POST['recordsId']) ? $_POST['recordsId'] : 0;
        $newDate = isset($_POST['newDate']) ? $_POST['newDate'] : '';
        $newTime = isset($_POST['newTime']) ? $_POST['newTime'] : '';
        $newElectronics = isset($_POST['newElectronics']) ? $_POST['newElectronics'] : '';
        updateRecording($pdo, $recordsId, $newDate, $newTime, $newElectronics);
    }
}
function updateRecording($pdo, $recordsId, $newDate, $newTime, $newElectronics) {
    $query = "UPDATE records SET records_date = :newDate, records_time = :newTime, electronics = :newElectronics WHERE records_ID = :recordsId";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':newDate', $newDate, PDO::PARAM_STR);
    $statement->bindParam(':newTime', $newTime, PDO::PARAM_STR);
    $statement->bindParam(':newElectronics', $newElectronics, PDO::PARAM_INT);
    $statement->bindParam(':recordsId', $recordsId, PDO::PARAM_INT);
    $statement->execute();
}
function changeUserRole($pdo, $userId, $newRole) {
    $query = "UPDATE users SET role_ID = :role_ID WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':role_ID', $newRole, PDO::PARAM_STR);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
}
// Функция для обновления информации о пользователе
function updateUserInfo($pdo, $userId, $newEmail, $newRole) {
    $query = "UPDATE users SET email = :email, role_ID = :role_ID WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':email', $newEmail, PDO::PARAM_STR);
    $statement->bindParam(':role_ID', $newRole, PDO::PARAM_STR);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
}
function updateUsername($pdo, $userId, $newUsername) {
    $query = "UPDATE users SET username = :username WHERE user_ID = :user_ID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':username', $newUsername, PDO::PARAM_STR);
    $statement->bindParam(':user_ID', $userId, PDO::PARAM_INT);
    $statement->execute();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard.css">
    <title>Панель пользователя</title>
</head>
<body>
<style>
    label{
        color: white;
    }
    #userInfoDetails{
        color: #2e2e2e;
    }
</style>
<h1>Панель пользователя</h1>
<h2>Здраствуй, <?php echo $userInfo['username']; ?>!</h2>
<!-- Информация о пользователе -->
<h2>Ваш профиль:</h2>
<p>ID: <?php echo $userInfo['user_ID']; ?></p>
<p>Email: <?php echo $userInfo['email']; ?></p>
<p>Имя: <?php echo $userInfo['username']; ?>
<!-- Добавляем кнопку "Изменить" и форму для изменения имени -->
    <button onclick="showUpdateUsernameForm()">Изменить</button>
<form id="updateUsernameForm" style="display: none;">
    <label for="newUsername">Новое имя пользователя:</label>
    <input type="text" name="newUsername" required>
    <button type="button" onclick="updateUsername()">Сохранить</button>
</form>
</p>
<p>Роль: <?php echo ucfirst($roleName); ?></p>
<!-- Список записей пользователя -->
<h2>Ваши записи:</h2>
<?php
// Получаем записи пользователя из базы данных
$userRecordings = getUserRecordings($pdo, $_SESSION['user_ID']);
if ($userRecordings) {
    foreach ($userRecordings as $recording) {
        ?>
        <div>
            <p>Запись ID: <?php echo $recording['records_ID']; ?></p>
            <p>Кол-во техники: <?php echo $recording['electronics']?></p>
            <p>Дата и время: <?php echo $recording['records_date'] . ' ' . $recording['records_time']; ?></p>
<!-- Добавляем кнопку для отмены записи -->
            <form action="cancel_recording.php" method="post">
                <input type="hidden" name="recordingId" value="<?php echo $recording['records_ID']; ?>">
                <button type="submit" style = "margin-left: 1400px">Отменить запись</button>
            </form>
        </div>
        <?php
    }
} else {
    echo "<p>У вас нет активных записей.</p>";
}
?>
<?php if ($roleName === 'admin'): ?>
    <h2>Список всех пользователей:</h2>
    <input type="text" ID="userSearch" style = "margin-left: 1350px" placeholder="Поиск по именам" oninput="searchUsers()">
    <ul ID="userList">
        <?php
// Получаем список всех пользователей из базы данных
        $allUsers = getAllUsers($pdo);
        foreach ($allUsers as $user) {
            echo '<li class="userListItem" onclick="showUserInfo(' . $user['user_ID'] . ')">' . $user['username'] . '</li>';
        }
        ?>
    </ul>
<h2>Удаление пользователя:</h2>
   <div ID="userInfoModal" class="modal">
   <div class="modal-content">
     <span class="close" onclick="closeUserInfoModal()">&times;</span>
<h2 ID="userInfoTitle"></h2>
   <div ID="userInfoDetails"></div>
   <form ID="updateRecordingForm" style="display: none;">
    <label for="newDate">Новая дата:</label>
    <input type="date" name="newDate" ID="newDate" required>
    <label for="newTime">Новое время:</label>
    <input type="time" name="newTime" ID="newTime" required>
    <label for="newGuests">Новое количество техники:</label>
    <input type="number" name="newElectronics" ID="newElectronics" required>
    <button type="button" ID="updateRecordingButton" onclick="updateRecording()">Сохранить изменения</button>
    </form>
    <form ID="changeRoleForm">
    <label for="newRole">Новая роль:</label>
    <input type="text" name="newRole" ID="newRole" required>
    <button type="button" onclick="changeUserRole(<?php echo $userId; ?>)">Изменить роль</button>
    </form>
 <h3>Изменение имени пользователя:</h3>
    <form ID="changeUsernameForm">
       <label for="newUsername">Новое имя пользователя:</label>
    <input type="text" name="newUsername" ID="newUsername" required>
    <button type="button" onclick="changeUsername()">Изменить имя</button>
    </form>
   </div>
 </div>
<?php endif; ?>
<a href="logout.php" class="logout-button">Выход</a>
<a href="recording.php" class="recording-link" style="font-size: 22px;">Записаться к мастеру</a>
<script>
    var currentUserId;
// Функция для отображения формы для изменения имени
    function showUpdateUsernameForm() {
        var updateUsernameForm = document.getElementById('updateUsernameForm');
        updateUsernameForm.style.display = 'block';
    }
// Функция для отправки AJAX-запроса на обновление имени
    function updateUsername() {
        var newUsername = document.getElementById('updateUsernameForm').elements.newUsername.value;
// Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
// Обновляем страницу после успешного обновления имени
                location.reload();
            }
        };
        xhr.send('action=updateUsername&newUsername=' + newUsername);
    }
    function changeUsername() {
        var newUsername = document.getElementById('newUsername').value;
// Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
// Обновляем информацию в модальном окне после успешного изменения имени
                showUserInfo(currentUserId);
            }
        };
        xhr.send('action=changeUsername&userId=' + currentUserId + '&newUsername=' + newUsername);
    }
// Функция для удаления пользователя
    function deleteUser() {
        // Подтверждение удаления с помощью встроенной функции confirm
        if (confirm("Вы уверены, что хотите удалить этого пользователя?")) {
// Отправка AJAX-запроса на удаление пользователя
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'dashboard.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
// Обновляем страницу после успешного удаления пользователя
                    location.reload();
                }
            };
            xhr.send('action=deleteUser&deleteUserId=' + currentUserId);
        }
    }
// Функция для фильтрации списка пользователей при вводе в поисковую строку
    function searchUsers() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById('userSearch');
        filter = input.value.toUpperCase();
        ul = document.getElementById('userList');
        li = ul.getElementsByTagName('li');
// Проходим по каждому элементу списка и скрываем тех, кто не соответствует поисковому запросу
        for (i = 0; i < li.length; i++) {
            a = li[i];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = '';
            } else {
                li[i].style.display = 'none';
            }
        }
    }
// Функция для отображения подробной информации о пользователе
    function updateUserInfo(userId) {
        var newEmail = document.getElementById('newEmail').value;
        var newRole = document.getElementById('newRole').value;
// Отправка AJAX-запроса на обновление информации о пользователе
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
// Обновляем страницу после успешного обновления информации
                location.reload();
            }
        };
        xhr.send('action=updateUserInfo&userId=' + userId + '&newEmail=' + newEmail + '&newRole=' + newRole);
    }
// Функция для отмены записи
    function cancelRecording(recordsId) {
// Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'cancel_recording_for_user.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
// Обновляем информацию в модальном окне после успешной отмены записи
                showUserInfo(currentUserId);
            }
        };
        xhr.send('recordingId=' + recordsId);
    }
    function changeUserRole() {
        var newRole = document.getElementById('newRole').value;
// Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
// Обновляем информацию после успешного изменения роли
                showUserInfo(currentUserId);
            }
        };
        xhr.send('action=changeUserRole&userId=' + currentUserId + '&newRole=' + newRole);
    }
    function showUserInfo(userId) {
        currentUserId = userId;
// Отправка AJAX-запроса для получения информации о пользователе
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var userInfo = JSON.parse(xhr.responseText);
                displayUserInfoModal(userInfo);
            }
        };
        xhr.send('action=getUserInfoAndRecordings&userId=' + userId);
    }
// Функция для отображения информации о пользователе
    function displayUserInfoModal(userInfo) {
        var modal = document.getElementById('userInfoModal');
        var title = document.getElementById('userInfoTitle');
        var details = document.getElementById('userInfoDetails');
// Отобразить информацию в модальном окне
        title.innerHTML = 'Записи пользователя: ' + userInfo.user_info.username;
        details.innerHTML = '';
// Отобразить основную информацию о пользователе
        details.innerHTML += '<h3>Основная информация:</h3>';
        details.innerHTML += '<p>ID: ' + userInfo.user_info.user_ID + '</p>';
        details.innerHTML += '<p>Email: ' + userInfo.user_info.email + '</p>';
        details.innerHTML += '<p>Имя: ' + userInfo.user_info.username + '</p>';
        details.innerHTML += '<p>Роль: ' + userInfo.user_info.role_ID + '</p>';
        details.innerHTML += '<button onclick="deleteUser()">Удалить пользователя</button>';
        if (userInfo.recordings.length > 0) {
            details.innerHTML += '<h3>Информация о записях:</h3>';
            userInfo.recordings.forEach(function (recording) {
                details.innerHTML += '<p>Запись ID: ' + recording.records_ID + '</p>';
                details.innerHTML += '<p>Количество техники: ' + recording.electronics + '</p>';
                details.innerHTML += '<p>Дата и время: ' + recording.records_date + ' ' + recording.records_time + '</p>';
                details.innerHTML += '<button onclick="cancelRecording(' + recording.records_ID + ')">Отменить запись</button>';
// Добавляем кнопки для изменения даты и времени записи, а также количества техники
                details.innerHTML += '<button onclick="showUpdateRecordingForm(' + recording.records_ID + ')">Изменить запись</button>';
                details.innerHTML += '<hr>';
            });
        } else {
            details.innerHTML += '<p>Пользователь не делал записей.</p>';
        }
// Отобразить модальное окно
        modal.style.display = 'block';
    }
// Функция для отображения формы изменения записи
    function showUpdateRecordingForm(recordsId) {
        var updateRecordingForm = document.getElementById('updateRecordingForm');
        var updateRecordingButton = document.getElementById('updateRecordingButton');
        updateRecordingButton.setAttribute('data-records-ID', recordsId);  // Устанавливаем атрибут с ID бронирования
        updateRecordingForm.style.display = 'block';
    }
// Функция для отправки AJAX-запроса на изменение записи
    function updateRecording() {
        var recordsId = document.getElementById('updateRecordingButton').getAttribute('data-records-ID');
        var newDate = document.getElementById('newDate').value;
        var newTime = document.getElementById('newTime').value;
        var newElectronics = document.getElementById('newElectronics').value;
// Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
// Обновляем информацию в модальном окне после успешного изменения записи
                showUserInfo(currentUserId);
            }
        };
        xhr.send('action=updateRecording&recordsId=' + recordsId + '&newDate=' + newDate + '&newTime=' + newTime + '&newElectronics=' + newElectronics);
    }
// Функция для закрытия модального окна с информацией о пользователе
    function closeUserInfoModal() {
        var modal = document.getElementById('userInfoModal');
        modal.style.display = 'none';
    }
</script>
</body>
</html>