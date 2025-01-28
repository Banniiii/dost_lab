<?php
include_once 'side_bar.php';
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'auto_check.php';

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Fetch user details
$sql = "SELECT user_id, username, role, profile_image FROM users";
$result = mysqli_query($link, $sql);

if (!$result) {
    die("ERROR: Could not execute query. " . mysqli_error($link));
}

// Update user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, role = ? WHERE user_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $username, $role, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: users.php");
            exit();
        } else {
            echo "ERROR: Could not execute query. " . mysqli_error($link);
        }
    } else {
        echo "ERROR: Could not prepare query. " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
}

// Delete user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    
    $sql = "DELETE FROM users WHERE user_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: users.php");
            exit();
        } else {
            echo "ERROR: Could not execute query. " . mysqli_error($link);
        }
    } else {
        echo "ERROR: Could not prepare query. " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/users.css">
    <title>View Users</title>
</head>
<body>
    <div class="user-container">
        <h1>Users</h1>
        <table class="users-table">
            <thead>
                <tr>
                    <th class="profile-image">Profile Image</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($user = mysqli_fetch_assoc($result)) {
                    $user_id = $user['user_id'];
                    $username = htmlspecialchars($user['username']);
                    $role = htmlspecialchars($user['role']);
                    $profile_image = !empty($user['profile_image']) ? "uploads/" . htmlspecialchars($user['profile_image']) : "image/default-profile.png";
                ?>
                <tr>
                    <td class="profile-image"><img src="<?php echo $profile_image; ?>" alt="Profile Image"></td>
                    <td><?php echo $username; ?></td>
                    <td><?php echo $role; ?></td>
                    <td>
                        <button class="action-btn update-btn" onclick="openUpdateModal(<?php echo $user_id; ?>, '<?php echo $username; ?>', '<?php echo $role; ?>')">
                            <i class="ri-edit-box-line"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="openDeleteModal(<?php echo $user_id; ?>, '<?php echo $username; ?>')">
                            <i class="ri-delete-bin-2-line"></i>
                        </button>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Update User Modal -->
    <div id="updateUserModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h2 class="custom-modal-title">Update User</h2>
                <span class="close" onclick="closeModal('updateUserModal')">&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="updateUserForm" method="post" action="users.php">
                    <input type="hidden" name="user_id" id="updateUserId" autocomplete="off">
                    <div class="form-group">
                        <label for="updateUsername">Username:</label>
                        <input type="text" name="username" id="updateUsername" class="form-control" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label for="updateRole">Role:</label>
                        <select name="role" id="updateRole" class="form-control" required autocomplete="off">
                            <option value="Admin">Admin</option>
                            <option value="Microbiology Lab Manager">Microbiology Lab Manager</option>
                            <option value="Chemical Lab Manager">Chemical Lab Manager</option>
                            <option value="Metrology Lab Manager">Metrology Lab Manager</option>
                            <option value="Microbiology Analyst">Microbiology Analyst</option>
                            <option value="Chemical Analyst">Chemical Analyst</option>
                            <option value="Metrology Analyst">Metrology Analyst</option>
                            <option value="CRO">CRO</option>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btns confirms-btn">Update</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteUserModal" class="delete-custom-modal">
        <div class="delete-custom-modal-content">
            <div class="delete-custom-modal-header">
                <h3>Delete User</h3>
            </div>
            <div class="delete-custom-modal-body">
                <form id="deleteUserForm" method="post" action="users.php">
                    <p id="deleteUserMessage">Are you sure you want to delete this User: <span id="deleteUsername"></span>?</p>
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <button type="submit" name="delete" class="btns confirm-btn">Okay</button>
                    <button type="button" class="btns cancel-btn" onclick="closeModal('deleteUserModal')">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUpdateModal(user_id, username, role) {
            document.getElementById('updateUserId').value = user_id;
            document.getElementById('updateUsername').value = username;
            document.getElementById('updateRole').value = role;
            document.getElementById('updateUserModal').style.display = 'block';
        }

        function openDeleteModal(user_id, username) {
            document.getElementById('deleteUserId').value = user_id;
            document.getElementById('deleteUsername').textContent = username;
            document.getElementById('deleteUserModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
    <script src="scripts/search.js"></script>
</body>
</html>
