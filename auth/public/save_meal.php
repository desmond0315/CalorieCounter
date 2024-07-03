<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['User_id'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['User_id'];
    $mealName = $_POST['meal_name'];
    $mealCalories = $_POST['meal_calories'];
    $mealDate = $_POST['meal_date'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
    }

    $stmt = $conn->prepare("INSERT INTO meal (User_ID, Meal_Name, Meal_Calories, Meal_Date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $userId, $mealName, $mealCalories, $mealDate);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Meal saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving meal: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}