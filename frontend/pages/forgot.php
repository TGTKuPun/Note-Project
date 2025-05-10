<?php
session_start();

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_email'])) {
  // Lấy thông tin từ cookie
  $_SESSION['user_id'] = $_COOKIE['user_id'];
  $_SESSION['user_email'] = $_COOKIE['user_email'];

  header("Location: pages/dashboard.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- prettier-ignore -->

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Favicon ICO-->
  <link rel="apple-touch-icon" sizes="57x57" href=".././assets/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href=".././assets/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href=".././assets/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href=".././assets/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href=".././assets/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href=".././assets/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href=".././assets/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href=".././assets/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href=".././assets/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href=".././assets/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href=".././assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href=".././assets/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href=".././assets/favicon/favicon-16x16.png">
  <!-- <link rel="manifest" href="/manifest.json"> -->
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content=".././assets/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <!-- Font awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

  <!-- Icon -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/index.css">

  <!-- Title -->
  <title>Note Project</title>
</head>

<body>
  <div class="wrapper">
    <!-- Form Box -->
    <div class="form-box">

      <!-- prettier-ignore -->
      <form class="email-container" id="verify" method="POST">
        <div class="top">
          <header>Reset Password</header>
          <span class="text-center fs-6">If your email exists, you will receive an email to reset your password</span>
          <div class="input-box">
            <input type="email" class="input-field" name="email" id="email" placeholder="Your Email" />
            <i class="bx bx-envelope"></i>
          </div>
          <div class="input-box">
            <input type="submit" class="submit" value="Reset Password" />
          </div>
          <div class="two-col">
            <div class="two">
              <label for=""><a href="#"> <i class='bx bx-arrow-back'></i> Go back</a></label>
            </div>
          </div>
        </div>
      </form>

      <!-- prettier-ignore -->
      <form class="reset-container" id="change_password" method="POST">
        <div class="top">
          <header>Reset Password</header>
          <input type="email" name="email" id="reset-email" hidden />
          <div class="input-box">
            <input type="password" class="input-field" name="password" id="new-password" placeholder="New Password" autocomplete="new-password" />
            <i class="bx bx-lock-alt"></i>
          </div>
          <div class="input-box">
            <input type="password" class="input-field" name="confirm-password" id="confirm-password" placeholder=" Confirm New Password" autocomplete="confirm-password" />
            <i class="bx bx-lock-alt"></i>
          </div>
          <div class="input-box">
            <input type="submit" class="submit" value="Change Password" />
          </div>
          <div class="two-col">
            <div class="two">
              <label for=""><a href="#"> <i class='bx bx-arrow-back'></i> Go back</a></label>
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
  <script src="../assets/js/forgot.js"></script>
</body>

</html>