<?php
require_once '../database.php'; // Include the database configuration file

// Create a new connection to the MySQL database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response array
$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
    } 
    // Validate username (no numbers or symbols, max 20 characters)
    elseif (!preg_match("/^[a-zA-Z ]{1,20}$/", $name)) {
        $response['message'] = 'Username must be up to 20 letters and contain no numbers or symbols.';
    } 
    // Validate password (8-15 characters, 1 uppercase, 1 lowercase)
    elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z]).{8,15}$/", $password)) {
        $response['message'] = 'Password must be 8-15 characters long with at least one uppercase and one lowercase letter.';
    } 
    else {
        // Check if the email already exists in the database
        $check_stmt = $conn->prepare("SELECT User_ID FROM users WHERE User_Email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Email already exists, send error response
            $response['message'] = 'Email already exists. Please use a different email.';
        } else {
            // Email does not exist, proceed with signup
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare a statement to insert a new user into the users table
            $insert_stmt = $conn->prepare("INSERT INTO users (User_Name, User_Email, User_Password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

            // Execute the statement
            if ($insert_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Signup successful!';
            } else {
                // Error in executing insert statement
                $response['message'] = 'Error: ' . $conn->error;
            }

            // Close the insert statement
            $insert_stmt->close();
        }

        // Close the check statement
        $check_stmt->close();
    }
}

// Close the database connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
