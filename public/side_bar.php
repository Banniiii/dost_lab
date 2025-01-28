<?php
session_start();
require_once "../config/database.php"; 
require_once "../includes/functions.php";

$user_id = $_SESSION['user_id'];

// Fetch user details using a prepared statement
$sql = "SELECT username, role, profile_image FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$username = $user['username'];
$role = $user['role'];
$profile_image = $user['profile_image'];

// Set a default image if the user has no profile image
$profile_image_path = !empty($profile_image) ? "uploads/$profile_image" : "image/default-profile.png";

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/side_bar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <title>DOST LIMS</title>
</head>
<body>
    <header class="header">
        <div class="header__container container">
           <div class="header__logo-title">
              <a href="home.php" class="header__logo">
              <img src="image/DOST.png" alt="">
              </a>
              <div class="logo-title">
              <p>DOST LIMS</p>
              </div>
           </div>

           <div class="header__search">
              <input type="text" id="searchInput" placeholder="🔍Search...">
           </div>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <nav class="sidebar__container">
        <div class="sidebar__content">
            <div class="sidebar__list">

            <div class="sidebar__profile">
                <img src="<?php echo htmlspecialchars($profile_image_path); ?>" alt="User Profile Image" class="sidebar__profile-image">
                <span class="sidebar__username"><?php echo htmlspecialchars($username); ?></span>
                <span class="sidebar__role"><?php echo htmlspecialchars($role); ?></span>
            </div>

               <a href="home.php" class="sidebar__link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active-link' : ''; ?>">
                   <i class="ri-home-5-line"></i>
                   <span class="sidebar__link-name">Home</span>
               </a>
   
               <a href="lab.php" class="sidebar__link <?php echo basename($_SERVER['PHP_SELF']) == 'lab.php' ? 'active-link' : ''; ?>">
                  <i class="ri-flask-line"></i>
                  <span class="sidebar__link-name">Laboratory</span>
               </a>
   
               <a href="item.php" class="sidebar__link <?php echo basename($_SERVER['PHP_SELF']) == 'item.php' ? 'active-link' : ''; ?>">
                  <i class="ri-box-3-line"></i>
                  <span class="sidebar__link-name">Items</span>
               </a>
   
               <a href="inventory.php" class="sidebar__link <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active-link' : ''; ?>">
                  <i class="ri-archive-drawer-line"></i>
                  <span class="sidebar__link-name">Inventory</span>
               </a>
   
               <a href="report.php" class="sidebar__link <?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active-link' : ''; ?>">
                  <i class="ri-booklet-line"></i>
                  <span class="sidebar__link-name">Reports</span>
               </a>
   
               <a href="transaction.php" class="sidebar__link <?php 
                   $currentPage = basename($_SERVER['PHP_SELF']);
                   $activePages = ['transaction.php', 'item_history.php', 'inventory_history.php'];
                   echo in_array($currentPage, $activePages) ? 'active-link' : ''; 
               ?>">
                   <i class="ri-history-line"></i>
                   <span class="sidebar__link-name">History</span>
               </a>
                
               <?php if (is_admin()): ?>
               <a href="users.php" class="sidebar__link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active-link' : ''; ?>">
                   <i class="ri-user-3-line"></i>
                   <span class="sidebar__link-name">Users</span>
               </a>
               <?php endif; ?>

               <a href="logout.php" class="sidebar__link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active-link' : ''; ?>">
                  <i class="ri-logout-box-r-line"></i>
                  <span class="sidebar__link-name">Logout</span>
               </a>
            </div>
        </nav>
    </div>
</body>
</html>