<?php
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'header.php';

// Start the session
session_start();

// If the user is already logged in, redirect them to the home page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
}

// Initialize variables
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = sanitize_input($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = $_POST["password"];
    }

    // Check input errors before querying the database
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";
        $stmt = execute_query($sql, [$username]);

        if ($stmt) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $user_id, $username, $hashed_password, $role);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        start_session();

                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["user_id"] = $user_id;
                        $_SESSION["username"] = $username;
                        $_SESSION["role"] = $role;

                        // Redirect user to home.php
                        header("location: home.php");
                        exit();
                    } else {
                        // Display an error message if password is not valid
                        $login_err = "Invalid username or password.";
                    }
                }
            } else {
                // Display an error message if username doesn't exist
                $login_err = "Invalid username or password.";
            }
            mysqli_stmt_close($stmt);
        } else {
            log_error("Error: " . mysqli_error($link));
        }
    }
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const passwordInput = document.getElementById("password");
            const togglePassword = document.getElementById("togglePassword");

            function togglePasswordVisibility(input, icon) {
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }

            function showIconOnInput(input, icon) {
                input.addEventListener("input", function() {
                    if (input.value.length > 0) {
                        icon.style.display = "inline";
                    } else {
                        icon.style.display = "none";
                    }
                });
            }

            togglePassword.addEventListener("click", function() {
                togglePasswordVisibility(passwordInput, togglePassword);
            });

            showIconOnInput(passwordInput, togglePassword);
        });
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-content">
            <div class="login-form">
                <h2>Welcome!</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <input type="text" name="username" id="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>">
                        <span class="error"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <div class="input-container">
                            <input type="password" name="password" id="password" placeholder="Password">
                            <i class="fas fa-eye" id="togglePassword" style="display: none;"></i>
                        </div>
                        <span class="error"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Login">
                    </div>
                    <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
                    <p class="error"><?php echo $login_err; ?></p>
                </form>
            </div>
            <div class="login-image">
                <img src="image/login-bg.png" alt="Login Image">
            </div>
        </div>
    </div>
</body>
</html>
