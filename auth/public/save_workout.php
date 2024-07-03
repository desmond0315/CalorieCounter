<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['User_id'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['User_id'];
    $workoutName = $_POST['workout_name'];
    $workoutCalories = $_POST['workout_calories'];
    $workoutDate = $_POST['workout_date'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
    }

    $stmt = $conn->prepare("INSERT INTO workout (User_ID, Workout_Name, Workout_Calories, Workout_Date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $userId, $workoutName, $workoutCalories, $workoutDate);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Workout saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving workout: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}