<?php
// Подключение к базе данных
$servername = "localhost";
$username = "username"; // замените на ваше имя пользователя
$password = "password"; // замените на ваш пароль
$dbname = "ip_codes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение IP-адреса пользователя
$ip_address = $_SERVER['REMOTE_ADDR'];

// Проверка, существует ли IP-адрес в базе данных
$sql = "SELECT * FROM users WHERE ip_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ip_address);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Если IP-адрес уже существует, перенаправляем на страницу с сообщением
    header("Location: already_received.php");
    exit();
} else {
    // Если IP-адрес не существует, добавляем его в базу данных
    $verification_code = rand(100000, 999999); // Генерация кода

    $sql = "INSERT INTO users (ip_address, verification_code) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $ip_address, $verification_code);
    $stmt->execute();

    // Перенаправление на сайт с кодом
    header("Location: http://example.com/your_code_page?code=$verification_code");
    exit();
}

$stmt->close();
$conn->close();
?>