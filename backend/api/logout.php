<?php
    session_start();

    session_unset();
    session_destroy();

    $cookies_to_clear = ['user_id', 'user_email', 'username', 'firstname', 'lastname', 'user_avatar'];

    foreach ($cookies_to_clear as $cookie_name) {
        if (isset($_COOKIE[$cookie_name])) {
            setcookie($cookie_name, '', time() - 3600, '/');
        }
    }

    header("Location: ../index.php");
    exit;
?>
