<?php
session_start();
require('fpdf/fpdf.php');

// Get data from session
$name = $_SESSION['name'] ?? 'Student Name';
$level = $_SESSION['level'] == 'jr' ? "Junior" : "Senior"; // Get the level of the student
$score = $_SESSION['score'] ?? 0;
$totalQuestions = 20;
$scorePercentage = ($score / $totalQuestions) * 100; // Calculate score in percentage

$timeTaken = $_SESSION['time_taken_formatted'] ?? '00:00:00';
$date = $_SESSION['date'] ?? date('Y-m-d');
$formattedDate = date('d-m-Y', strtotime($date)); // Format date as day-month-year

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set background image (ensure the path to 'certificate.png' is correct)
$pdf->Image('logo4.png', 0, 0, 210, 297); // A4 size dimensions

// Set font and title for the certificate
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(0, 51, 102); // Dark blue color
$pdf->Cell(0, 100, '', 0, 1, 'C'); // Space above title for layout consistency

// Set font for name and position it (position already customized)
$pdf->SetFont('Arial', 'B', 32);
$pdf->SetY($pdf->GetY() + 35);
$pdf->Cell(0, 10, $name, 0, 1, 'C'); // Centered name

// Pull the level text down by 100 units
$pdf->SetY($pdf->GetY() + 80); // Move down by 100 units for level positioning
$pdf->SetFont('Arial', 'B', 20); // Set font for level
$pdf->Cell(0, 10, "DEMO $level", 0, 1, 'C'); // Centered level

// Position and display the score as a percentage in bold, moved right by 23
$pdf->SetFont('Arial', 'B', 20);
$pdf->SetY($pdf->GetY() + 1); // Move down by 50 for score positioning
$pdf->SetX(130); // Move right by 23 units
$pdf->Cell(0, 10, "" . round($scorePercentage, 2) . "%", 0, 1, 'L'); // Left-align to keep formatting consistent

// Display time taken in bold, positioned further down
$pdf->SetY($pdf->GetY() + -20); // Move down by 15 for time taken
$pdf->SetX(23); // Move right by 23 units
$pdf->Cell(0, 10, "", 0, 1, 'L'); // Display time taken

// Display the formatted date in bold, moved right by 23
$pdf->SetY($pdf->GetY() + 13); // Move down by another 15 for date
$pdf->SetX(85); // Move right by 23 units
$pdf->Cell(0, 10, "$formattedDate", 0, 1, 'L');

// Output the PDF for download with the user's name in the filename
$pdf->Output('D', "Certificate_$name.pdf");
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

// Destroy the session
session_destroy();
?>
