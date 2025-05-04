<?php

$host = 'mysql-server';
$user = 'user';
$pass = 'user';
$db = 'db_note';

$con = new mysqli($host, $user, $pass, $db);

if ($con->connect_error) {
    die("Kết nối thất bại: " . $con->connect_error);
}
