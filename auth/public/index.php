<?php
session_start(); // Start session

// Prevent caching
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache"); // HTTP/1.0

// Check if user is logged in
$userName = isset($_SESSION['User_name']) ? $_SESSION['User_name'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content -->
</head>
<body>
    <!-- Your existing HTML content -->

    
</body>
</html>



</body>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website with Login & Signup Form</title>
    <!-- Google Fonts Link For Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="style1.css">
    <script src="script.js" defer></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        #video-background {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
        }

        .middle-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 600px;
            height: auto;
            margin-bottom: 200px;
            padding: 20px;
            background-color: rgba(231, 203, 181, 0.9); /* Set the background color with reduced opacity */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .search-form {
            margin-top: 20px;
        }

        .search-form input[type="text"] {
            width: calc(100% - 120px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            height: 40px;
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100px;
            height: 40px;
        }

        .search-results {
            display: none;
            margin-top: 20px;
        }

        .footer {
            background-color: #1a2939;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .footer .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer p {
            margin: 0;
            font-size: 14px;
        }

        .footer .footer-links {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .footer .footer-links li {
            display: inline;
        }

        .footer .footer-links a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .footer .footer-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .middle-box {
                width: 90%;
                padding: 10px;
            }

            .search-form input[type="text"] {
                width: calc(100% - 100px);
            }

            .search-form button {
                width: 80px;
                height: 36px;
            }
        }

        @media (max-width: 480px) {
            .middle-box {
                width: 95%;
                padding: 5px;
            }

            .search-form input[type="text"] {
                width: calc(100% - 70px);
            }

            .search-form button {
                width: 60px;
                height: 34px;
            }
        }
    </style>
</head>
<body>
    <video autoplay muted loop id="video-background">
        <source src="backgroundvideo.mp4" type="video/mp4">
        <!-- Add additional video sources if needed -->
        Your browser does not support the video tag.
    </video>

    <div class="middle-box">
        <!-- Content for the middle box -->
        <h2>Welcome to CalorieTracker</h2>
        <p>Track your calories and stay healthy!</p>

        <!-- Search bar -->
        <form class="search-form" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Enter a food">
            <button type="submit">Search</button>
        </form>

        <!-- Display box for search results -->
        <div id="searchResults" class="search-results" style="display: none;">
            <h3>Search Results</h3>
            <div id="searchResultsContent"></div>
        </div>
    </div>

    <script>
        // Function to fetch data from the API
        function fetchDataFromAPI(query) {
            // API endpoint and API key
            const apiUrl = `https://api.calorieninjas.com/v1/nutrition?query=${encodeURIComponent(query)}`;
            const apiKey = 'rpRUKhLZs24UEajq6D8eLA==pz6nVJb8Il6e7bog';

            // Fetch data from the API
            fetch(apiUrl, {
                headers: {
                    'X-Api-Key': apiKey
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
                // Display error message in the search results box
                document.getElementById('searchResultsContent').innerHTML = 'An error occurred. Please try again later.';
                document.getElementById('searchResults').style.display = 'block';
            });
        }

        // Function to display search results in the middle box
        function displaySearchResults(data) {
            const searchResultsContent = document.getElementById('searchResultsContent');

            if (Array.isArray(data.items) && data.items.length > 0) {
                // Clear previous search results
                searchResultsContent.innerHTML = '';

                // Display search results
                data.items.forEach(item => {
                    const calories = item.calories;
                    const protein = item.protein_g;
                    const fat = item.fat_total_g;
                    const carbohydrates = item.carbohydrates_total_g;

                    // Create HTML elements for each search result
                    const resultItem = document.createElement('div');
                    resultItem.innerHTML = `
                        <p>Calories: ${calories}</p>
                        <p>Protein: ${protein}g</p>
                        <p>Fat: ${fat}g</p>
                        <p>Carbohydrates: ${carbohydrates}g</p>
                    `;
                    searchResultsContent.appendChild(resultItem);
                });

                // Show the search results box
                document.getElementById('searchResults').style.display = 'block';
            } else {
                // Display message if no search results found
                searchResultsContent.innerHTML = 'No search results found.';
                document.getElementById('searchResults').style.display = 'block';
            }
        }

        // Event listener for the search form submission
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission

            const query = document.getElementById('search').value.trim(); // Get the search query

            if (query === '') {
                alert('Please enter a search query.'); // Notify the user if the search query is empty
                return;
            }

            // Fetch data from the API
            fetchDataFromAPI(query);
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const signupForm = document.getElementById('signupForm');

            signupForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission

                // Fetch form data
                const formData = new FormData(signupForm);

                // Send form data via fetch to signup.php
                fetch('signup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Success message
                        // Optionally redirect to another page on success
                        // window.location.href = 'index.php';
                    } else {
                        alert(data.message); // Error message
                    }
                
                
            });
        });
    </script>

    <header>
        <nav class="navbar">
            <span class="hamburger-btn material-symbols-rounded">menu</span>
            <a href="#" class="logo">
<img src="http://localhost/CalorieCounterApp/applogo5.webp" alt="logo">
                <h2>CalorieTracker</h2>
            </a>
            <ul class="links">
                <span class="close-btn material-symbols-rounded">close</span>
                <li><a href="foodAPI.html">Food</a></li>
    <li><a href="recipeAPI.html">Recipes</a></li>
    <li><a href="caloriecounter.html">Calorie Calculator</a></li>
    <li><a href="aboutus.html">About us</a></li>
                <li class="<?= !$userName ? 'disabled' : '' ?>"><a href="index2.php">Track</a></li>
            </ul> 
            <button class="login-btn"><?= $userName ? 'LOG OUT' : 'LOG IN' ?></button>
        </nav>
        <?php if ($userName): ?>
            <div class="welcome-message">Welcome, <?php echo htmlspecialchars($userName); ?></div>
        <?php endif; ?>
    </header>

    <div class="blur-bg-overlay"></div>
    <div class="form-popup">
        <span class="close-btn material-symbols-rounded">close</span>
        <div class="form-box login">
            <div class="form-details"></div>
            <div class="form-content">
                <h2>LOGIN</h2>
<form action="login.php" method="post">
                    <div class="input-field">
        <input type="text" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-field">
        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <button type="submit">Log In</button>
                </form>
                <div class="bottom-link">
                    Don't have an account?
                    <a href="#" id="signup-link">Signup</a>
                </div>
            </div>
        </div>
        <div class="form-box signup">
            <div class="form-details"></div>
            <div class="form-content">
                <h2>SIGNUP</h2>
            <form id="signupForm" action="signup.php" method="post">
    <div class="input-field">
                    <input type="email" name="email" required>
        <label>Enter your email</label>
    </div>
    <div class="input-field">
                    <input type="text" name="name" required>
        <label>Enter your name</label> <!-- Adjusted label to reflect entering name -->
    </div>
    <div class="input-field">
                    <input type="password" name="password" required>
        <label>Create password</label>
    </div>
    <div class="policy-text">
        <input type="checkbox" id="policy" required> <!-- Added 'required' attribute for checkbox -->
        <label for="policy">
            I agree to the
            <a href="#" class="option">Terms & Conditions</a>
        </label>
    </div>
    <button type="submit">Sign Up</button>
</form>

                <div class="bottom-link">
                    Already have an account? 
                    <a href="#" id="login-link">Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 CalorieTracker. All rights reserved.</p>
            <ul class="footer-links">
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="http://Wa.me/+601111209906" target="_blank">Contact Us</a></li>
            </ul>
        </div>
    </footer>
</body>
</html>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const signupForm = document.getElementById('signupForm');

            signupForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission

                // Fetch form data
                const formData = new FormData(signupForm);

                // Perform client-side validation
                if (!validateForm(formData)) {
                    return;
                }

                // Send form data via fetch to signup.php
                fetch('signup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Success message
                        // Optionally redirect to another page on success
                        // window.location.href = 'index.php';
                    } else {
                        alert(data.message); // Error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again later.'); // Generic error message
                });
            });

            function validateForm(formData) {
                const email = formData.get('email');
                const name = formData.get('name');
                const password = formData.get('password');

                // Email validation (Gmail format)
                const emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
                if (!emailRegex.test(email)) {
                    alert('Please enter a valid Gmail address.');
                    return false;
                }

                // Username validation (letters only, up to 20 characters)
                const nameRegex = /^[a-zA-Z]{1,20}$/;
                if (!nameRegex.test(name)) {
                    alert('Username must be letters only and up to 20 characters.');
                    return false;
                }

                // Password validation (8-15 characters, at least 1 uppercase, 1 lowercase)
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z]).{8,15}$/;
                if (!passwordRegex.test(password)) {
                    alert('Password must be 8-15 characters long and include at least one uppercase and one lowercase letter.');
                    return false;
                }

                return true; // All validations passed
            }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[action="login.php"]');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission

        // Fetch form data
        const formData = new FormData(loginForm);

        // Send form data via fetch to login.php
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Success message
                // Optionally redirect to another page on success
                window.location.href = 'index2.php';
            } else {
                alert(data.message); // Error message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.'); // Generic error message
        });
    });
});
</script>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.querySelector('.login-btn');
            const userLoggedIn = <?= json_encode($userName !== null) ?>;

            if (!userLoggedIn) {
                document.querySelectorAll('.navbar .links li.disabled a').forEach(link => {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        alert('Please log in to access this page.');
                    });
                });

                loginBtn.addEventListener('click', function() {
                    // Redirect to login page or show login form
                    
                });
            } else {
                loginBtn.addEventListener('click', function() {
                    // Log out user by clearing session and redirect to home page
                    <?php session_destroy(); ?>
                    window.location.href = 'index.php';
                });
            }
        });
    </script>
</body>
</html>
