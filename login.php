<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(file_get_contents('php://input'))) {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields!']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format!']);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            echo json_encode([
                'success' => true,
                'message' => 'Login successful!',
                'user_name' => $user['name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No account found with this email!']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit();
}

header('Location: index.php?action=login');
exit();
