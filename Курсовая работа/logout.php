<?php
session_start();
// Удаление всех данных сессии
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
