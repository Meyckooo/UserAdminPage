<?php


// $host = "localhost";
// $user = "root";
// $password = "";
// $database = "users_db";
// $conn = new mysqli($host, $user, $password, $database);

$conn = mysqli_connect("localhost", "root", "", "users_db");

if ($conn->connect_error){
    die("Connection failed:". $conn->connect_error);
}

?>