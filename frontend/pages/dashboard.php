<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        if (isset($_COOKIE['user_id'])) {
            $_SESSION['user_id'] = $_COOKIE['user_id'];
        } else {
            header('Location: ../index.php');
            exit();
        }
    }

    $user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    <link rel="icon" type="image/png" sizes="192x192"  href=".././assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href=".././assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href=".././assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href=".././assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content=".././assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Custome CSS -->
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- Icon -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Note Dashboard</title>
</head>
<body>
    <!-- Top Content -->
    <div class="wrapper">
        <!-- Top of Navbar -->
        <div class="container text-center">
            <div class="d-flex justify-content-between align-items-center pt-3">
                <div>
                    <i class='bx bx-search fs-3' onclick="openSearch()"></i>
                </div>
                <div>
                    <span class="h1 m-0">NOTE DASHBOARD</span>
                </div>
                <div>
                    <i class='bx bx-log-out fs-3' onclick="logout()"></i>
                </div>
            </div>
        </div>

        <!-- Bottom of Navbar-->
        <nav class="navbar navbar-expand-lg">
            <div class="container d-inline-flex justify-content-center">
                <button class="navbar-toggler me-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse justify-content-center pt-2" id="navbarSupportedContent">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">All</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Layout
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Row</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Grid</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Labels
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Important</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Personal</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Business</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Trash</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Offcanvas Search -->
        <div id="searchOffcanvas" class="offcanvas offcanvas-top" tabindex="-1">
            <div class="offcanvas-header justify-content-center">
                <h2 class="offcanvas-title m-0 h2">Search</h2>
                <button type="button" class="btn-close position-absolute end-0 me-3" onclick="closeSearch()"></button>
            </div>
            <div class="offcanvas-body">
                <input type="text" class="form-control" id="input-field" placeholder="Type to search">
            </div>
        </div>
    </div>

    <!-- Note Section -->
    <div class="popup-box">
        <div class="popup">
            <div class="content">
                <header>
                    <p>Add a new note</p>
                    <i class="bx bx-x"></i>
                </header>
                <form action="#">
                    <!-- Title -->
                    <div class="row title">
                        <label for="">Title</label>
                        <input type="text" name="" id="">
                    </div>
                    <!-- Description -->
                    <div class="row desc">
                        <label for="">Description</label>
                        <textarea name="" id=""></textarea>
                    </div>
                    <!-- Label -->
                    <div class="row label">
                        <label for="label">Label</label>
                        <select name="label" id="label">
                            <option value="">-- Select a label --</option>
                            <option value="urgent">Urgent</option>
                            <option value="important">Important</option>
                            <option value="optional">Optional</option>
                        </select>
                    </div>
                    <button>Add Note</button>
                </form>
            </div>
        </div>
    </div>

    <div id="note-section">
        <li class="add-note">
            <div class="icon"><i class='bx bx-plus'></i></div>
            <p>Add new note</p>
        </li>
        <li class="note">
            <div class="details">
                <p>Title</p>
                <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur, itaque?</span>
                <div class="labels">
                    <span class="label">Personal</span>
                    <span class="label">Work</span>
                    <span class="label">Urgent</span>
                </div>
            </div>
            <div class="bottom-content">
                <span>April 3, 2022</span>
                <div class="settings">
                    <i class='bx bx-dots-horizontal-rounded' ></i>
                    <ul class="menu">
                        <li><i class='bx bx-edit-alt'></i><span>Edit</span></li>
                        <li><i class='bx bx-trash-alt'></i><span>Delete</span></li>
                    </ul>
                </div>
            </div>
        </li>
        <li class="note">
            <div class="details">
                <p>Title</p>
                <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur, itaque?</span>
                <div class="labels">
                    <span class="label">Personal</span>
                    <span class="label">Work</span>
                    <span class="label">Urgent</span>
                </div>
            </div>
            <div class="bottom-content">
                <span>April 3, 2022</span>
                <div class="settings">
                    <i class='bx bx-dots-horizontal-rounded' ></i>
                    <ul class="menu">
                        <li><i class='bx bx-edit-alt'></i><span>Edit</span></li>
                        <li><i class='bx bx-trash-alt'></i><span>Delete</span></li>
                    </ul>
                </div>
            </div>
        </li>
        <li class="note">
            <div class="details">
                <p>Title</p>
                <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur, itaque?</span>
                <div class="labels">
                    <span class="label">Personal</span>
                    <span class="label">Work</span>
                    <span class="label">Urgent</span>
                </div>
            </div>
            <div class="bottom-content">
                <span>April 3, 2022</span>
                <div class="settings">
                    <i class='bx bx-dots-horizontal-rounded' ></i>
                    <ul class="menu">
                        <li><i class='bx bx-edit-alt'></i><span>Edit</span></li>
                        <li><i class='bx bx-trash-alt'></i><span>Delete</span></li>
                    </ul>
                </div>
            </div>
        </li>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <!-- JQuery JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custome JS -->
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>