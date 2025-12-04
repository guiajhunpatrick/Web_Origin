<?php
session_start();
require_once 'db_config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $address = trim($data['address'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields!']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format!']);
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered!']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, address, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $address, $phone, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['logged_in'] = true;

        echo json_encode([
            'success' => true,
            'message' => 'Registration successful!',
            'user_name' => $name
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed! Please try again.']);
    }

    $stmt->close();
    $conn->close();
}
