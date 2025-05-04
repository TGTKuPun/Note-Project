<?php
session_start();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) {
  if (isset($_COOKIE['user_id']) && isset($_SESSION['user_email'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['user_email'] = $_COOKIE['user_email'];
  } else {
    header('Location: ../index.php');
    exit();
  }
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['user_email'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
$avatar = $_SESSION['user_avatar'];

require_once(__DIR__ . '/../api/note/user_preferences.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Favicon ICO-->
  <!-- prettier-ignore -->
  <link rel="apple-touch-icon" sizes="57x57" href="../assets/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="../assets/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="../assets/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="../assets/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="../assets/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="../assets/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="../assets/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="../assets/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="../assets/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="../assets/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../assets/favicon/favicon-16x16.png">
  <!-- <link rel="manifest" href="/manifest.json"> -->
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="../assets/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <!-- Font awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7"
    crossorigin="anonymous" />

  <!-- Custome CSS -->
  <link rel="stylesheet" href="../assets/css/reset.css" />
  <link rel="stylesheet" href="../assets/css/dashboard/note_section.css" />
  <link rel="stylesheet" href="../assets/css/dashboard/profile.css" />
  <link rel="stylesheet" href="../assets/css/dashboard/create_note.css" />
  <link rel="stylesheet" href="../assets/css/dashboard/search_bar.css">
  <link rel="stylesheet" href="../assets/css/dashboard/responsive.css" />
  <link rel="stylesheet" href="../assets/css/dashboard/scroll_to_top.css" />
  <link rel="stylesheet" href="../assets/css/dashboard/edit_profile.css" />

  <!-- Icon -->
  <link
    href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
    rel="stylesheet" />

  <!-- Title -->
  <title>Note Dashboard</title>
</head>

<body>
  <!-- Scroll to Top -->
  <i class='bx bxs-chevron-up fs-1'></i>

  <!-- Top of Navbar -->
  <div class="text-center">
    <div class="container d-flex justify-content-between align-items-center pt-3">
      <div>
        <i class="bx bx-search fs-3" onclick="openSearch()"></i>
      </div>
      <div>
        <span class="h1 m-0">NOTE DASHBOARD</span>
      </div>
      <div class="d-flex align-items-center justify-content-center">
        <img src="../assets/uploads/avatar/<?= isset($avatar) && $avatar ? $avatar : 'default.webp' ?>" alt="" class="rounded-circle" id="profile_1" style="width: 45px; height: 45px; object-fit: cover;" onclick="toggleProfile()">
        <i class='bx bx-chevron-down fs-4' onclick="toggleProfile()"></i>
        <div class="sub-menu-wrap" id="subMenu">
          <div class="sub-menu">
            <div class="user-info">
              <img src="../assets/uploads/avatar/<?= isset($avatar) && $avatar ? $avatar : 'default.webp' ?>" alt="" id="profile_2">
              <h2 class="h3"><?= $username ?></h2>
            </div>
            <div class="text-start" id="user_id">UID: <?= $user_id ?></div>
            <hr>
            <a href="#" class="sub-menu-link" id="btn_edit_profile">
              <i class='bx bx-user-circle fs-3'></i>
              <p>Edit Profile</p>
              <span><i class='bx bx-chevron-right'></i></span>
            </a>
            <a href="#" class="sub-menu-link">
              <i class='bx bxs-cog fs-3'></i>
              <p>Settings & Privacy</p>
              <span><i class='bx bx-chevron-right'></i></span>
            </a>
            <a href="#" class="sub-menu-link">
              <i class='bx bx-help-circle fs-3'></i>
              <p>Help & Support</p>
              <span><i class='bx bx-chevron-right'></i></span>
            </a>
            <a href="#" class="sub-menu-link" onclick="logout()">
              <i class='bx bx-power-off fs-3'></i>
              <p>Logout</p>
              <span><i class='bx bx-chevron-right'></i></span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bottom of Navbar-->
  <nav class="navbar navbar-expand-lg">
    <div class="container d-inline-flex justify-content-center">
      <!-- prettier-ignore -->
      <button class="navbar-toggler me-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div
        class="collapse navbar-collapse justify-content-start pt-2 m-0"
        id="navbarSupportedContent">
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#" id="all-labels">All</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" id="all-labels">Favorite</a>
          </li>
          <li class="nav-item dropdown">
            <!-- prettier-ignore -->
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Layout</a>
            <ul class="dropdown-menu" id="layout-menu">
              <li class="d-flex align-items-center justify-content-center ms-1" data-view="list">
                <i class='bx bx-list-ul'></i>
                <a class="dropdown-item ms-0 ps-2" href="#">Row</a>
              </li>
              <li>
                <hr class="dropdown-divider" />
              </li>
              <li class="d-flex align-items-center justify-content-center ms-1" data-view="grid">
                <i class='bx bxs-grid-alt'></i>
                <a class="dropdown-item ms-0 ps-2" href="#">Grid</a>
              </li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false">
              Labels
            </a>
            <ul class="dropdown-menu" id="label-menu">
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Trash</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Offcanvas Search Bar -->
  <div id="searchOffcanvas" class="offcanvas offcanvas-top" tabindex="-1">
    <div class="offcanvas-header justify-content-center">
      <h2 class="offcanvas-title m-0 h2">Search</h2>
      <!-- prettier-ignore -->
      <button type="button" class="btn-close position-absolute end-0 me-3" onclick="closeSearch()"></button>
    </div>
    <div class="offcanvas-body">
      <input
        type="text"
        class="form-control"
        id="input-field"
        placeholder="Type to search" />
    </div>
  </div>

  <!-- Popup Create Note Form -->
  <div class="popup-box">
    <div class="popup">
      <div class="content">
        <header>
          <p>Add a new note</p>
          <i class="bx bx-x"></i>
        </header>
        <!-- Main Content -->
        <form action="#">
          <!-- Title -->
          <div class="row title">
            <label for="note_title">Title</label>
            <input type="text" name="title" id="note_title">
          </div>
          <!-- Label -->
          <div class="row label">
            <label for="label">Label</label>
            <select name="label" id="label">
              <option value="" disabled hidden selected>-- Select a label --</option>
              <!-- fetch labels from Server -->
            </select>
          </div>
          <!-- Description -->
          <div class="row desc">
            <label for="">Description</label>
            <textarea name="description" id="note_desc"></textarea>
          </div>
          <button>Add Note</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Popup Edit Profile -->
  <div class="popup-box-edit-profile" id="popup-edit-profile">
    <div class="popup">
      <div class="content">
        <header>
          <p>Edit Profile</p>
          <i class="bx bx-x" id="close_profile_popup"></i>
        </header>
        <form action="../api/profile/update_profile.php" id="editProfileForm" method="POST" enctype="multipart/form-data">
          <!-- Avatar -->
          <div class="row avatar">
            <label for="edit_avatar">Profile images</label>
            <div class="avatar-preview-container">
              <div class="avatar-preview">
                <img loading="lazy" src="../assets/uploads/avatar/<?= isset($avatar) && $avatar ? $avatar : 'default.webp' ?>" id="avatar_preview" alt="Avatar Preview" />
              </div>
              <input type="file" name="avatar" id="edit_avatar" accept="image/*">
            </div>
          </div>
          <!-- Username -->
          <div class="row username">
            <label for="edit_username">Username</label>
            <input type="text" name="username" value="<?= $username ?>" id="edit_username" placeholder="Enter your username">
          </div>
          <!-- Email -->
          <div class="row email">
            <label for="edit_email">Email</label>
            <input type="email" name="email" value="<?= $email ?>" id="edit_email" placeholder="Enter your email" readonly>
          </div>
          <!-- Firstname -->
          <div class="row firstname">
            <label for="edit_firstname">First Name</label>
            <input type="text" name="firstname" value="<?= $firstname ?>" id="edit_firstname" placeholder="Enter your first name">
          </div>
          <!-- Lastname -->
          <div class="row lastname">
            <label for="edit_lastname">Last Name</label>
            <input type="text" name="lastname" value="<?= $lastname ?>" id="edit_lastname" placeholder="Enter your last name">
          </div>
          <button type="submit">Save Changes</button>
        </form>
      </div>
    </div>
  </div>


  <!-- Note Section -->
  <div class="wrapper list-view">
    <!-- Box_add -->
    <li class="add-note">
      <div class="icon"><i class="bx bx-plus"></i></div>
      <p>Add new note</p>
    </li>
    <!-- Area of notes -->
  </div>

  <!-- User Preferences -->
  <div id="user_preferences"
    data-theme="<?= $preferences['theme'] ?>"
    data-view="<?= $preferences['view'] ?>"
    hidden>
  </div>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- JQuery JS -->
  <script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custome JS -->
  <script src="../assets/js/dashboard.js"></script>
  <script>
    window.addEventListener("DOMContentLoaded", () => {
      fetch_label();
      loadNotesFromServer();

      const prefer_element = document.getElementById('user_preferences');

      if (prefer_element) {
        const theme = prefer_element.dataset.theme || "light";
        const view = prefer_element.dataset.view || "grid";

        // Apply theme
        // if (theme === "dark") {
        //   document.body.classList.add("dark");
        // } else {
        //   document.body.classList.remove("dark");
        // }

        // Apply layout view
        const note_section = document.querySelector(".wrapper");

        if (note_section) {
          note_section.classList.remove("list-view"); // reset class

          if (view === "list") {
            note_section.classList.add("list-view");
          }
        }
      }
    });
  </script>
</body>

</html>