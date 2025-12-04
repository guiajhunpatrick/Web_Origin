<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

session_start();

require_once 'db_config.php';

$response = ['success' => false, 'message' => 'Unknown error'];

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Please login to manage cart',
            'error_type' => 'not_logged_in'
        ]);
        exit;
    }

    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $user_id = $_SESSION['user_id'];

    $conn = getDBConnection();

    switch ($action) {
        case 'add':
            $product_name = $_POST['product_name'] ?? '';
            $product_price = $_POST['product_price'] ?? 0;

            if (empty($product_name) || $product_price <= 0) {
                throw new Exception('Invalid product data');
            }

            $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_name = ?");
            $stmt->bind_param("is", $user_id, $product_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $row = $result->fetch_assoc();
                $new_quantity = $row['quantity'] + 1;
                $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $update_stmt->bind_param("ii", $new_quantity, $row['id']);
                $update_stmt->execute();
                $update_stmt->close();
            } else {

                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_name, product_price, quantity) VALUES (?, ?, ?, 1)");
                $stmt->bind_param("isd", $user_id, $product_name, $product_price);
                $stmt->execute();
            }

            $response = ['success' => true, 'message' => 'Item added to cart'];
            $stmt->close();
            break;

        case 'get':
            $stmt = $conn->prepare("SELECT id, product_name, product_price, quantity FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $cart_items = [];
            $total = 0;

            while ($row = $result->fetch_assoc()) {
                $cart_items[] = $row;
                $total += $row['product_price'] * $row['quantity'];
            }

            $response = [
                'success' => true,
                'cart' => $cart_items,
                'total' => number_format($total, 2)
            ];

            $stmt->close();
            break;

        case 'update_quantity':
            $cart_id = $_POST['cart_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;

            if ($quantity < 1) {
                throw new Exception('Invalid quantity');
            }

            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Cart updated'];
            } else {
                throw new Exception('Update failed');
            }
            $stmt->close();
            break;

        case 'remove':
            $cart_id = $_POST['cart_id'] ?? 0;

            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cart_id, $user_id);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Item removed'];
            } else {
                throw new Exception('Remove failed');
            }
            $stmt->close();
            break;

        case 'checkout':
            $stmt = $conn->prepare("SELECT product_name, product_price, quantity FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception('Cart is empty');
            }

            $total = 0;
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
                $total += $row['product_price'] * $row['quantity'];
            }

            $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
            $order_stmt->bind_param("id", $user_id, $total);
            $order_stmt->execute();
            $order_id = $order_stmt->insert_id;

            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $item_stmt->bind_param("isdi", $order_id, $item['product_name'], $item['product_price'], $item['quantity']);
                $item_stmt->execute();
            }

            $clear_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $clear_stmt->bind_param("i", $user_id);
            $clear_stmt->execute();

            $response = [
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order_id,
                'total' => number_format($total, 2)
            ];

            $stmt->close();
            $order_stmt->close();
            $item_stmt->close();
            $clear_stmt->close();
            break;

        default:
            $response = ['success' => false, 'message' => 'Invalid action'];
    }

    $conn->close();
} catch (Exception $e) {

    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
