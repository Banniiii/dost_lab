<!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
      <link rel="stylesheet" href="styles/side-nav.css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
      <title>DOST LIMS</title>
   </head>
   <body>
      <header class="header">
      <div class="header__container container">
         <div class="header__toggle" id="header-toggle">
            <i class="ri-menu-line"></i>
         </div>

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
            <div class="sidebar__account">
               <img src="image/Yvan.jpg" alt="sidebar image" class="sidebar__perfil">

               <div class="sidebar__names">
                  <h3 class="sidebar__name">YVAN</h3>
                  <span class="sidebar__email">ymm1218@gmail.com</span>
               </div>

               <i class="ri-arrow-right-s-line"></i>
            </div>

               <div class="sidebar__list">
                  <a href="home.php" class="sidebar__link active-link">
                     <i class="ri-home-5-line"></i>
                     <span class="sidebar__link-name">Home</span>
                     <span class="sidebar__link-floating">Home</span>
                  </a>
                  
                  <a href="lab.php" class="sidebar__link">
                     <i class="ri-flask-line"></i>
                     <span class="sidebar__link-name">Laboratory</span>
                     <span class="sidebar__link-floating">Laboratory</span>
                  </a>

                  <a href="item.php" class="sidebar__link">
                     <i class="ri-box-3-line"></i>
                     <span class="sidebar__link-name">Items</span>
                     <span class="sidebar__link-floating">Items</span>
                  </a>

                  <a href="inventory.php" class="sidebar__link">
                     <i class="ri-archive-drawer-line"></i>
                     <span class="sidebar__link-name">Inventory</span>
                     <span class="sidebar__link-floating">Inventory</span>
                  </a>

                  <a href="report.php" class="sidebar__link">
                     <i class="ri-booklet-line"></i>
                     <span class="sidebar__link-name">Reports</span>
                     <span class="sidebar__link-floating">Reports</span>
                  </a>

                  <a href="transaction.php" class="sidebar__link">
                     <i class="ri-history-line"></i>
                     <span class="sidebar__link-name">History</span>
                     <span class="sidebar__link-floating">History</span>
                  </a>

                  <a href="login.php" class="sidebar__link">
                     <i class="ri-logout-box-r-line"></i>
                     <span class="sidebar__link-name">Logout</span>
                     <span class="sidebar__link-floating">Logout</span>
                  </a>
               </div>
         </nav>
      </div>

      <main class="main container" id="main">
         
      </main>

      <script src="scripts/side-nav.js"></script>
   </body>
</html>