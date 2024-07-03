<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

function handleError($errno, $errstr, $errfile, $errline) {
    $errorLog = "Error [$errno] $errstr in $errfile on line $errline\n";
    file_put_contents('error_log.txt', $errorLog, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'An internal error occurred']);
    exit;
}

set_error_handler('handleError');

try {
    session_start();
    require_once '../database.php';

    // Log input data
    file_put_contents('debug_log.txt', "Session: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
    file_put_contents('debug_log.txt', "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

    if (!isset($_SESSION['User_id'])) {
        throw new Exception('User not logged in');
    }

    if (!isset($_POST['limit'])) {
        throw new Exception('Limit value not provided');
    }

    $userId = $_SESSION['User_id'];
    $newLimit = intval($_POST['limit']);

    // Log processed data
    file_put_contents('debug_log.txt', "UserID: $userId, NewLimit: $newLimit\n", FILE_APPEND);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Check if a limit already exists for this user
    $checkStmt = $conn->prepare("SELECT Limit_ID FROM dailycalorielimit WHERE User_ID = ?");
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing limit
        $stmt = $conn->prepare("UPDATE dailycalorielimit SET Limit_Value = ? WHERE User_ID = ?");
        $stmt->bind_param("ii", $newLimit, $userId);
    } else {
        // Insert new limit
        $stmt = $conn->prepare("INSERT INTO dailycalorielimit (User_ID, Limit_Value) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $newLimit);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Limit updated successfully']);
    } else {
        throw new Exception('Failed to update limit: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    file_put_contents('error_log.txt', $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}