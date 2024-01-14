-- Создание базы данных
CREATE DATABASE IF NOT EXISTS electronic CHARACTER SET utf8 COLLATE utf8_general_ci;
-- Использование базы данных
USE electronic;
-- Таблица ролей
CREATE TABLE IF NOT EXISTS roles (
    role_ID INT AUTO_INCREMENT PRIMARY KEY,
    role_name ENUM('user', 'admin') NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;
-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    user_ID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(110) NOT NULL,
    role_ID INT,
    unique (email),
    FOREIGN KEY (role_ID) REFERENCES roles(role_ID)
) CHARACTER SET utf8 COLLATE utf8_general_ci;
-- Таблица мастеров
CREATE TABLE IF NOT EXISTS masters (
    master_ID INT AUTO_INCREMENT PRIMARY KEY,
    master_name VARCHAR(20) NOT NULL,
    ability INT NOT NULL,
    UNIQUE (master_name)
) CHARACTER SET utf8 COLLATE utf8_general_ci;
-- Таблица записей
CREATE TABLE IF NOT EXISTS records (
    records_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT,
    master_ID INT,
    electronics INT,
    records_date DATE NOT NULL,
    records_time TIME NOT NULL,
    FOREIGN KEY (user_ID) REFERENCES users(user_ID),
    FOREIGN KEY (master_ID) REFERENCES master(master_ID)
) CHARACTER SET utf8 COLLATE utf8_general_ci;