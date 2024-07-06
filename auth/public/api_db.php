<?php
header("Content-Type: application/json");

// Database configuration
$servername = "localhost";
$username = "root";  // Change this to your phpMyAdmin username
$password = "";  // Change this to your phpMyAdmin password
$dbname = "api_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}

$conn->close();

function handleGet($conn) {
    $user_id = isset($_GET['User_ID']) ? intval($_GET['User_ID']) : 0;
    
    if ($user_id > 0) {
        // Fetch a specific user
        $sql = "SELECT * FROM users WHERE User_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
    } else {
        // Fetch all users
        $sql = "SELECT * FROM users";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
}

function handlePost($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_name = $data['User_Name'];
    $user_email = $data['User_Email'];
    $user_password = $data['User_Password'];

    $sql = "INSERT INTO users (User_Name, User_Email, User_Password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user_name, $user_email, $user_password);

    if ($stmt->execute()) {
        echo json_encode(["message" => "New user created successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
}

function handlePut($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['User_ID'];
    $user_name = $data['User_Name'];
    $user_email = $data['User_Email'];
    $user_password = $data['User_Password'];

    $sql = "UPDATE users SET User_Name = ?, User_Email = ?, User_Password = ? WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $user_name, $user_email, $user_password, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
}

function handleDelete($conn) {
    $user_id = isset($_GET['User_ID']) ? intval($_GET['User_ID']) : 0;

    $sql = "DELETE FROM users WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
}
?>
