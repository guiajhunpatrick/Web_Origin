<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'Origin - store2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password required']);
            exit();
        }

        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;


            session_regenerate_id(true);

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user_name' => $user['name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Login Failed: Please check your email or sign up.']);
        }
        break;

    case 'signup':
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $address = $_POST['address'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields required']);
            exit();
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit();
        }


        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $stmt = $pdo->prepare("INSERT INTO users (name, email, address, phone, password) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $address, $phone, $hashedPassword])) {
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        break;

    case 'check_session':
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            echo json_encode([
                'logged_in' => true,
                'user_name' => $_SESSION['user_name'],
                'cart' => $_SESSION['cart'] ?? []
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        break;

    case 'update_cart':
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }

        $cartData = json_decode($_POST['cart'] ?? '[]', true);
        $_SESSION['cart'] = $cartData;

        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart']
        ]);
        break;

    case 'get_cart':
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }

        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'] ?? []
        ]);
        break;

    case 'clear_cart':
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }

        $_SESSION['cart'] = [];
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
