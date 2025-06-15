<?php
// Database connection
$host = "localhost"; // Your host
$user = "u804948088_abacus"; // Database username
$pass = "#Abacus123"; // Database password
$db = "u804948088_abacus"; // Database name

// Create a new connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hardcoded values for testing
$name = "Aditya"; // Hardcoded name
$score = 40; // Hardcoded score
$time_taken = 3600; // Hardcoded time taken in seconds (example: 1 hour)
$date = date('Y-m-d'); // Current date
$phone = "9503504298"; // Hardcoded phone number
$location = "Pune"; // Hardcoded location
$level = "sr"; // Hardcoded level

// Insert hardcoded values into scores1 table
$insertScoreQuery = "INSERT INTO scores1 (username, scores, timetaken, date, phone, location, level) 
VALUES ('$name', '$score', '$time_taken', '$date', '$phone', '$location', '$level')";

if ($conn->query($insertScoreQuery) === TRUE) {
    echo "Data inserted successfully with hardcoded values.";
} else {
    echo "Error: " . $insertScoreQuery . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>
