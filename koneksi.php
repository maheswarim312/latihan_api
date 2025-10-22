<?php

$servername = "localhost";
$username = "root";
$password = "";
$database_name = "latihan_api";

$conn = mysqli_connect($servername, $username, $password, $database_name);

if ($conn->connect_error) {
    die("Koneksi tidak berhasil: " );
}