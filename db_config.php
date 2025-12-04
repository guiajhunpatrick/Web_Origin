<?php
function getDBConnection()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Origin - store2";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function getCartCount($conn, $user_id)
{
    $sql = "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {

        return 0;
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total_items'] ? $row['total_items'] : 0;
}
