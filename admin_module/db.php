<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "complaint_portal";

// Create connection
$conn = new mysqli('localhost', 'root', '', 'complaint_portal', 3307); 


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

}
?>
