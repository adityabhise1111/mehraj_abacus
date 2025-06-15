<?php
session_start();
require('fpdf/fpdf.php');

$host = "localhost"; // Use localhost for local MySQL
$user = "u804948088_abacus"; // Default username for XAMPP
$pass = "#Abacus123"; // Default password is empty for XAMPP
$db = "u804948088_abacus"; // Your local database name  // Your local database name 


$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username from session
$username = $_SESSION['username'];

// Fetch student details including full name and level from 'students' table
$studentQuery = "SELECT name, level FROM students WHERE username = '$username'";
$studentResult = $conn->query($studentQuery);

if ($studentResult && $studentResult->num_rows > 0) {
    $student = $studentResult->fetch_assoc();
    $fullName = $student['name'];
    $level = $student['level'] == 'jr' ? "Junior" : "Senior";
} else {
    die("Error fetching student details: " . $conn->error);
}

// Fetch the latest score, date, and time taken for the student from 'scores' table
$scoreQuery = "SELECT scores, timetaken, date FROM scores WHERE username = '$username' ORDER BY date DESC, sr DESC LIMIT 1";
$scoreResult = $conn->query($scoreQuery);

if ($scoreResult && $scoreResult->num_rows > 0) {
    $studentScore = $scoreResult->fetch_assoc();
    $score = $studentScore['scores'];
    $date = $studentScore['date'];
    $timeTaken = $studentScore['timetaken'];
} else {
    die("Error fetching score details: " . $conn->error);
}

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set background image (ensure the path to 'certificate.png' is correct)
$pdf->Image('certificate.png', 0, 0, 210, 297); // A4 size dimensions

// Set font and title for the certificate
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(0, 51, 102); // Dark blue color
$pdf->Cell(0, 100, 'Certificate of Achievement', 0, 1, 'C');

// Display the full name of the student
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, $fullName, 0, 1, 'C');

// Display completion message, score, and level
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(0, 10, "has successfully completed the $level level exam with a score of", 0, 1, 'C');

// Display the score
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, $score, 0, 1, 'C');

// Display time taken
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(0, 10, "Time Taken: $timeTaken", 0, 1, 'C');

// Display the date
$pdf->Cell(0, 10, "Date: $date", 0, 1, 'C');

// Output the PDF for download with the user's name in the filename
$pdf->Output('D', "Certificate_$fullName.pdf");
?>
