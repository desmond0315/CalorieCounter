<?php
session_start();
//var_dump($_SESSION['User_id']); // Add this line

require_once '../database.php'; // Adjust the path relative to index2.php

// Check if user is logged in
if (!isset($_SESSION['User_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['User_id'];
$userName = $_SESSION['User_name'];

// Fetch the user's current calorie limit
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT Limit_Value FROM dailycalorielimit WHERE User_ID = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$currentLimit = 2000; // Default value
if ($row = $result->fetch_assoc()) {
    $currentLimit = $row['Limit_Value'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="trackstyle.css" />
    <!-- Add this before your app.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src="app.js" defer></script>
    <title>Tracalorie 2.0 | Track your Caloric Intake</title>

  </head>
  <body>
    <header class="bg-primary d-sm-flex justify-content-between align-items-center text-white text-center py-2 px-5">
      <h1>
        <img src="http://localhost/CalorieCounterApp/applogo5.webp" alt="App Logo" style="height: 1.2em; vertical-align: middle;"> <!-- Image -->
        CalorieTracker
      </h1>
      <div>
        <span class="me-3">Welcome, <?php echo htmlspecialchars($userName); ?>!</span>

        <button class="btn btn-outline-light mx-3" data-bs-toggle="modal" data-bs-target="#limit-modal">Set Daily Limit</button>
        <button id="reset" class="btn btn-outline-light">Reset Day</button>
        <a href="logout.php" class="btn btn-outline-light ms-3">Log Out</a>

      </div>
    </header>

    <!-- Stats -->
    <section class="stats my-5 px-5">
  <div class="row g-3 my-3">
    <div class="col-md-6">
      <div class="card text-center bg-dark text-white">
        <div class="card-body">
    <div id="calories-limit" class="fs-1 fw-bold"><?php echo $currentLimit; ?></div>
          <p class="fs-4">Daily Calorie Limit</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <div id="calories-total" class="fs-1 fw-bold">0</div>
              <p class="fs-4">Gain/Loss</p>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card bg-light">
            <div class="card-body">
              <div id="calories-consumed" class="fs-1 fw-bold">0</div>
              <p class="fs-4">Calories Consumed</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card bg-light">
            <div class="card-body">
              <div id="calories-burned" class="fs-1 fw-bold">0</div>
              <p class="fs-4">Calories Burned</p>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card bg-light">
            <div class="card-body" id="calories-remaining-card">
              <div id="calories-remaining" class="fs-1 fw-bold">2000</div>
              <p class="fs-4">Calories Remaining</p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>


    <section class="px-5">
      <div class="progress">
        <div
          id="calorie-progress"
          class="progress-bar"
          role="progressbar"
        ></div>
      </div>
    </section>

    <!-- Filter -->
    <section class="filter my-5 px-5">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="mt-3">
            <input
              type="text"
              id="filter-meals"
              class="form-control"
              placeholder="Search Meals..."
            />
          </div>
        </div>
        <div class="col-md-6">
          <div class="mt-3">
            <input
              type="text"
              id="filter-workouts"
              class="form-control"
              placeholder="Search Workouts..."
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Items -->
    <section class="items mx-5">
      <div class="row g-4">
        <!-- Meals -->
        <div class="col-md-6 order-md-1">
          <div class="d-flex align-items-center">
            <h2 class="border-start border-primary border-3 p-2">
              Meals / Food Items
            </h2>
          </div>

          <!-- Add Meal Form -->
          <div class="collapse" id="collapse-meal">
            <div class="card card-body bg-light">
              <form id="meal-form">
    <div class="mb-3">
        <label for="meal-name" class="form-label">Meal Name</label>
    <input type="text" class="form-control" id="meal-name" required>
    </div>
    <div class="mb-3">
        <label for="meal-calories" class="form-label">Calories</label>
    <input type="number" class="form-control" id="meal-calories" required>
    </div>
    <div class="mb-3">
        <label for="meal-date-time" class="form-label">Date and Time</label>
    <input type="datetime-local" class="form-control" id="meal-date-time" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Meal</button>
</form>

            </div>
          </div>

          <div id="meal-items"></div>
        </div>
        <!-- Workout -->
        <div class="col-md-6 order-md-2">
          <div class="d-flex align-items-center">
            <h2 class="border-start border-secondary border-3 p-2">Workouts</h2>
          </div>

          <!-- Add Workout Form -->
          <div class="collapse" id="collapse-workout">
            <div class="card card-body bg-light">
              <form id="workout-form">
  <div class="mb-3">
    <label for="workout-name" class="form-label">Workout Name</label>
    <input type="text" class="form-control" id="workout-name" required>
  </div>
  <div class="mb-3">
    <label for="workout-calories" class="form-label">Calories</label>
    <input type="number" class="form-control" id="workout-calories" required>
  </div>
  <div class="mb-3">
    <label for="workout-date-time" class="form-label">Date and Time</label>
    <input type="datetime-local" class="form-control" id="workout-date-time" required>
  </div>
  <button type="submit" class="btn btn-primary">Add Workout</button>
</form>
            </div>
          </div>

          <div id="workout-items"></div>
        </div>
      </div>
    </section>

    <!-- Calorie Limit Modal -->
    <div
      class="modal fade"
      id="limit-modal"
      tabindex="-1"
      aria-labelledby="limitModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="limitModalLabel">
              Set Daily Limit
            </h1>
            
          </div>
          <div class="modal-body">
            <form id="limit-form">
              <div class="mb-3">
                <label for="limit" class="form-label">Daily Calorie Limit</label>
                <input
                  type="number"
                  class="form-control"
                  id="limit"
                  placeholder="Enter a Calories..."
                  required
                />
              </div>

              <button type="submit" class="btn btn-primary text-white">
                Save
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script>
        // You can use the user data in JavaScript if needed
        const userId = <?php echo json_encode($userId); ?>;
        const userEmail = <?php echo json_encode($userEmail); ?>;
        const userName = <?php echo json_encode($userName); ?>;
    </script>
        <script>
           document.getElementById('limit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const newLimit = document.getElementById('limit').value;
    
    console.log('Sending limit:', newLimit);

    const formData = new FormData();
    formData.append('limit', newLimit);

    fetch('save_limit.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);
        if (data.success) {
            document.getElementById('calories-limit').textContent = newLimit;
            bootstrap.Modal.getInstance(document.getElementById('limit-modal')).hide();
            alert('Limit updated successfully');
        } else {
            alert('Failed to update limit: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the limit');
    });
});
    </script>
    <script>
document.getElementById('meal-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const mealName = document.getElementById('meal-name').value;
    const mealCalories = document.getElementById('meal-calories').value;
    const mealDateTime = document.getElementById('meal-date-time').value;

    const formData = new FormData();
    formData.append('meal_name', mealName);
    formData.append('meal_calories', mealCalories);
    formData.append('meal_date', mealDateTime);

    fetch('save_meal.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Meal saved successfully');
            // Clear the form
            document.getElementById('meal-form').reset();
            // You might want to update the UI here to show the new meal
        } else {
            alert('Failed to save meal: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the meal');
    });
});
</script>
<script>
// Existing meal form submission code...

document.getElementById('workout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const workoutName = document.getElementById('workout-name').value;
    const workoutCalories = document.getElementById('workout-calories').value;
    const workoutDateTime = document.getElementById('workout-date-time').value;

    const formData = new FormData();
    formData.append('workout_name', workoutName);
    formData.append('workout_calories', workoutCalories);
    formData.append('workout_date', workoutDateTime);

    fetch('save_workout.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Workout saved successfully');
            // Clear the form
            document.getElementById('workout-form').reset();
            // You might want to update the UI here to show the new workout
        } else {
            alert('Failed to save workout: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the workout');
    });
});
</script>

  </body>
</html>