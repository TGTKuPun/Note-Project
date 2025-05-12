<?php
session_start();

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_email'])) {
  // Lấy thông tin từ cookie
  $_SESSION['user_id'] = $_COOKIE['user_id'];
  $_SESSION['user_email'] = $_COOKIE['user_email'];
  $_SESSION['username'] = $_COOKIE['username'];

  header("Location: pages/dashboard.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- prettier-ignore -->

<head>
  <meta charset="UTF-8" />
  <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0" /> -->
  <!-- Favicon ICO-->
  <link rel="apple-touch-icon" sizes="57x57" href="./assets/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="./assets/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="./assets/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="./assets/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="./assets/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="./assets/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="./assets/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="./assets/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="./assets/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicon/favicon-16x16.png">
  <!-- <link rel="manifest" href="/manifest.json"> -->
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="./assets/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <!-- Font awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

  <!-- Icon -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./assets/css/reset.css">
  <link rel="stylesheet" href="./assets/css/index.css">

  <!-- Title -->
  <title>Note Project</title>
</head>

<body>
  <div class="wrapper">
    <!-- Form Box -->
    <div class="form-box">
      <!-- Login Form -->
      <!-- prettier-ignore -->
      <form class="login-container" id="login" method="POST">
        <div class="top">
          <span>Don't have an account? <a href="#" onclick="toRegister()">Sign Up</a></span>
          <header>Login</header>
          <div class="input-box">
            <input type="email" class="input-field" name="email" id="email" placeholder="Email" />
            <i class='bx bx-envelope'></i>
          </div>
          <div class="input-box">
            <input type="password" class="input-field" name="password" id="password" placeholder="Password" />
            <i class="bx bx-lock-alt"></i>
          </div>
          <div class="input-box">
            <input type="submit" class="submit" value="Sign In" />
          </div>
          <div class="two-col">
            <div class="one">
              <input type="checkbox" name="remember-me" id="login-check">
              <label for="login-check">Remember me</label>
            </div>
            <div class="two">
              <label for=""><a href="pages/forgot.php">Forgot password?</a></label>
            </div>
          </div>
        </div>
      </form>

      <!-- Register Form -->
      <!-- prettier-ignore -->
      <form class="register-container" id="register" method="POST">
        <div class="top">
          <span>Have an account? <a href="#" onclick="toLogin()">Sign In</a></span>
          <header>Sign Up</header>
          <div class="two-forms">
            <div class="input-box">
              <input type="text" class="input-field" name="firstname" id="firstname" placeholder="Firstname" />
              <i class="bx bx-user"></i>
            </div>
            <div class="input-box">
              <input type="text" class="input-field" name="lastname" id="lastname" placeholder="Lastname" />
              <i class="bx bx-user"></i>
            </div>
          </div>
          <div class="input-box">
            <input type="email" class="input-field" name="new-email" id="new-email" placeholder="Email" />
            <i class="bx bx-envelope"></i>
          </div>
          <div class="input-box">
            <input type="text" class="input-field" name="new-username" id="new-username" placeholder="Username" />
            <i class="bx bx-user"></i>
          </div>
          <div class="input-box">
            <input type="password" class="input-field" name="new-password" id="new-password" placeholder="Password" />
            <i class="bx bx-lock-alt"></i>
          </div>
          <div class="input-box">
            <input type="password" class="input-field" name="confirm-password" id="confirm-password" placeholder=" Confirm Password" />
            <i class="bx bx-lock-alt"></i>
          </div>
          <div class="input-box">
            <input type="submit" class="submit" value="Register" />
          </div>
          <div class="two-col">
            <div class="one">
            </div>
            <div class="two">
              <label for=""><a href="#">Ton Duc Thang University</a></label>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- SweetAlert2 JS -->
  <!-- prettier-ignore -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- JQuery JS -->
  <!-- prettier-ignore -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <!-- Bootstrap JS -->
  <!-- prettier-ignore -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  <!-- Custom JS for style -->
  <script src="./assets/js/index.js"></script>
</body>

</html>