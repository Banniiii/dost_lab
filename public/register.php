<?php
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'header.php';

// Initialize variables
$username = $password = $confirm_password = $role = $profile_image = "";
$username_err = $password_err = $confirm_password_err = $role_err = $profile_image_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $role = trim($_POST["role"]);

    if ($role == 'Lab_Manager') {
        $role = $_POST['lab_manager_subrole'];
    } elseif ($role == 'Analyst') {
        $role = $_POST['analyst_subrole'];
    }    

    // Validate and upload profile image
    if (!empty($_FILES["profile_image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);

        if ($check !== false) {
            if ($_FILES["profile_image"]["size"] > 5000000) {
                $profile_image_err = "Sorry, your file is too large.";
            } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                $profile_image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    $profile_image = basename($_FILES["profile_image"]["name"]);
                } else {
                    $profile_image_err = "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $profile_image_err = "File is not an image.";
        }
    }

    // Check input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($role_err) && empty($profile_image_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, role, profile_image) VALUES (?, ?, ?, ?)";
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = execute_query($sql, [$username, $hashed_password, $role, $profile_image]);

        if ($stmt) {
            // Redirect to login page after successful registration
            header("location: login.php");
            exit;
        } else {
            // Log error if the query fails
            log_error("Database insert error: " . mysqli_error($link));
        }
        mysqli_stmt_close($stmt);
    } else {
        // Log errors for debugging
        if (!empty($username_err)) log_error("Username error: " . $username_err);
        if (!empty($password_err)) log_error("Password error: " . $password_err);
        if (!empty($confirm_password_err)) log_error("Confirm Password error: " . $confirm_password_err);
        if (!empty($role_err)) log_error("Role error: " . $role_err);
        if (!empty($profile_image_err)) log_error("Profile Image error: " . $profile_image_err);
    }
}

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles/register.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const passwordInput = document.getElementById("password");
            const confirmPasswordInput = document.getElementById("confirm_password");
            const togglePassword = document.getElementById("togglePassword");
            const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

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

            toggleConfirmPassword.addEventListener("click", function() {
                togglePasswordVisibility(confirmPasswordInput, toggleConfirmPassword);
            });

            showIconOnInput(passwordInput, togglePassword);
            showIconOnInput(confirmPasswordInput, toggleConfirmPassword);
        });
    </script>
</head>
<body>
    <div class="register-container">
        <div class="register-content">
            <div class="register-form">
                <h2>Register</h2>
                <form action="register.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_image"></label>
                        <input type="file" name="profile_image" id="profile_image" accept="image/*">
                        <span class="error"><?php echo $profile_image_err; ?></span>
                    </div>
                    <div class="profile-image-container hidden" id="profile-image-container">
                        <img id="profile-image-preview" src="#" alt="Profile Image">
                    </div>
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
                        <div class="input-container">
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                            <i class="fas fa-eye" id="toggleConfirmPassword" style="display: none;"></i>
                        </div>
                        <span class="error"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <select name="role" id="role" onchange="showSubRoles(this.value)">
                            <option value="" selected disabled>Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Lab_Manager">Lab Manager</option>
                            <option value="Analyst">Analyst</option>
                            <option value="CRO">CRO</option>
                        </select>
                        <span class="error"><?php echo $role_err; ?></span>
                    </div>

                    <!-- Sub-role dropdowns for Lab Manager -->
                    <div class="form-group sub-role hidden" id="lab-manager-roles">
                        <select name="role" id="lab-manager-subrole">
                            <option value="" selected disabled>Select Lab Manager Role</option>
                            <option value="Microbiology Lab Manager">Microbiology Lab Manager</option>
                            <option value="Chemical Lab Manager">Chemical Lab Manager</option>
                            <option value="Metrology Lab Manager">Metrology Lab Manager</option>
                        </select>
                    </div>

                    <!-- Sub-role dropdowns for Analyst -->
                    <div class="form-group sub-role hidden" id="analyst-roles">
                        <select name="role" id="analyst-subrole">
                            <option value="" selected disabled>Select Analyst Role</option>
                            <option value="Microbiology Analyst">Microbiology Analyst</option>
                            <option value="Chemical Analyst">Chemical Analyst</option>
                            <option value="Metrology Analyst">Metrology Analyst</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Register">
                    </div>
                    <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
                </form>
            </div>
            <div class="register-image">
                <img src="image/register-bg.png" alt="Register Image">
            </div>
        </div>
    </div>
    <script>
        document.getElementById("profile_image").addEventListener("change", function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById("profile-image-preview");
                    const container = document.getElementById("profile-image-container");
                    preview.src = e.target.result;
                    container.classList.remove("hidden");
                };
                reader.readAsDataURL(file);
            }
        });

        function showSubRoles(role) {
            document.getElementById('lab-manager-roles').style.display = 'none';
            document.getElementById('analyst-roles').style.display = 'none';

            if (role === 'Lab_Manager') {
                document.getElementById('lab-manager-roles').style.display = 'block';
            } else if (role === 'Analyst') {
                document.getElementById('analyst-roles').style.display = 'block';
            }
        }
    </script>
</body>
</html>
