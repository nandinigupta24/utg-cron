<?php

//$servername = "localhost";
//$username = "root";
//$password = "4yqalw2Ibh%?";

$servername = "10.68.120.6";
$database = "UTGAPI";
$username = "root";
$password = "16theGeeksUK";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
mysqli_close($conn);
//try {
//    $conn = new PDO("mysql:host=$servername;dbname=myDB", $username, $password);
//    // set the PDO error mode to exception
//    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    echo "Connected successfully"; 
//    }
//catch(PDOException $e)
//    {
//    echo "Connection failed: " . $e->getMessage();
//    }
// Create connection
//$conn = new mysqli($servername, $username, $password);
//// Check connection
//if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
//} 
//
//// Create database
//$sql = "CREATE DATABASE myDB";
//if ($conn->query($sql) === TRUE) {
//    echo "Database created successfully";
//} else {
//    echo "Error creating database: " . $conn->error;
//}
//
//$conn->close();