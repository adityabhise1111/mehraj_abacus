<?php
session_start();
require('fpdf/fpdf.php');

// Database connection
$host = "127.0.0.1";
$user = "u804948088_abacus";
$pass = "#Abacus123";
$db = "u804948088_abacus";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student's full name and level directly from the session
$fullName = $_SESSION['name'];
$level = $_SESSION['level'];

// Convert level to a readable format
$levelDisplay = ($level === 'jr') ? 'Junior' : (($level === 'sr') ? 'Senior' : 'Unknown');

// Fetch the latest score, date, and time taken for the student from 'scores1' table
$scoreQuery = "SELECT scores, timetaken, date FROM scores1 WHERE username = '$fullName' ORDER BY date DESC, sr DESC LIMIT 1";
$scoreResult = $conn->query($scoreQuery);

if ($scoreResult && $scoreResult->num_rows > 0) {
    $studentScore = $scoreResult->fetch_assoc();
    $score = $studentScore['scores'];
    $date = date("d-m-Y", strtotime($studentScore['date'])); // Format date as day-month-year
    $timeTaken = $studentScore['timetaken'];

    // Calculate percentage (assuming the maximum score is 50)
    $percentageScore = ($score / 50) * 100;
} else {
    die("Error fetching score details: " . $conn->error);
}

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set background image (ensure the path to 'certificate.png' is correct)
$pdf->Image('logo4.png', 0, 0, 210, 297); // A4 size dimensions

// Set font and title for the certificate
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(0, 51, 102); // Dark blue color
$pdf->Cell(0, 100, '', 0, 1, 'C'); // Space above title for layout consistency

// Move down by 12 units for the title
$pdf->Cell(0, 34, '', 0, 1, 'C'); // Move down

// Move right by 12 units for name display
$pdf->SetX($pdf->GetX() + 12);

// Display the full name of the student
$pdf->SetFont('Arial', 'B', 32);
$pdf->Cell(0, 10, $fullName, 0, 1, 'C'); // Centered name

// Move down by 12 units for the level display
$pdf->Cell(0, 80, '', 0, 1, 'C'); // Move down

// Move right by 12 units for level display
$pdf->SetX($pdf->GetX() + 0);

// Display level
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(0, 10, " $levelDisplay", 0, 1, 'C');

// Move down by 12 units for the percentage score display
$pdf->Cell(0, 3, '', 0, 1, 'C'); // Move down

// Move right by 12 units for percentage score display
$pdf->SetX($pdf->GetX() + 75);

// Display the percentage score
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, " " . number_format($percentageScore, 2) . "%", 0, 1, 'C');

// Move down slightly to position the date below the score
$pdf->Cell(0, 10, '', 0, 1, 'C'); // Adjust spacing as needed

// Display the date below the score
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(0, 1, "Date: $date", 0, 1, 'C');

// Output the PDF for download with the user's name in the filename
$pdf->Output('D', "Certificate_$fullName.pdf");
?>

<?php
// Start the session (if not already started)
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie (if set)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

//Destroy the session
session_destroy();
?>
<?php
session_start(); // Always start the session

// Print out all session variables
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
?>
