<?php
session_start(); // Start the session to access session variables

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require 'db_connect.php';

// Get the username, name, and phone from the session
$username = $_SESSION['username'];
$name = $_SESSION['name'];
$phone = $_SESSION['phone'];
$location = $_SESSION['address']; // Assuming 'address' is used for location

// Calculate time taken for the exam
$end_time = time();
$start_time = $_SESSION['start_time'] ?? $end_time; // Fallback if start_time not set
$time_taken = $end_time - $start_time;
$time_taken_formatted = gmdate("H:i:s", $time_taken);

// Fetch student's level
// Using prepared statement for safety
$query = "SELECT level FROM students WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$level = $student['level'] ?? 'jr'; // Default to 'jr' if level not found or student not found
$stmt->close();


// Determine the question table based on the level
$questionTable = ($level == 'jr') ? "question_jr" : "question_sr";

// Initialize score and set total number of questions
$score = 0;
$total_questions = 50; // Ensure this matches the number of questions in exam.php

// --- START: OPTIMIZED ANSWER CHECKING ---

$submittedQuestionIds = [];
$submittedAnswersMap = []; // Store submitted answers by question ID

// First, collect all submitted question IDs and their answers from $_POST
foreach ($_POST as $key => $selected_answer) {
    if (strpos($key, 'q') === 0) { // Only process inputs starting with 'q'
        $question_id = (int) substr($key, 1); // Extract ID and cast to integer for safety
        $submittedQuestionIds[] = $question_id;
        $submittedAnswersMap[$question_id] = $selected_answer;
    }
}

// If no questions were submitted, something went wrong, handle gracefully
if (empty($submittedQuestionIds)) {
    // This could happen if a student refreshes after submission or form is empty
    die("No answers submitted or invalid exam session.");
}

// Fetch ALL correct answers for the submitted questions in ONE single query
// Ensure IDs are integers for the IN clause to prevent SQL injection
$idList = implode(',', array_map('intval', $submittedQuestionIds));
$correctAnswersQuery = "SELECT sr, ans FROM $questionTable WHERE sr IN ($idList)";
$correctAnswersResult = $conn->query($correctAnswersQuery);

$correctAnswersMap = [];
if ($correctAnswersResult && $correctAnswersResult->num_rows > 0) {
    while ($row = $correctAnswersResult->fetch_assoc()) {
        $correctAnswersMap[$row['sr']] = $row['ans'];
    }
}

// Now, compare submitted answers against the fetched correct answers in PHP memory
foreach ($submittedAnswersMap as $question_id => $selected_answer) {
    // Check if we actually found a correct answer for this question ID
    if (isset($correctAnswersMap[$question_id])) {
        $correct_answer = $correctAnswersMap[$question_id];
        if ($selected_answer == $correct_answer) {
            $score++;
        }
    }
}

// --- END: OPTIMIZED ANSWER CHECKING ---


// Calculate incorrect answers
$incorrect = $total_questions - $score;

// Data for pie chart
$chartData = [
    ['Result', 'Number of Questions'],
    ['Correct', $score],
    ['Incorrect', $incorrect]
];

// Save score to the database
$date = date('Y-m-d');
// Using prepared statement for safety
$insertScoreQuery = "INSERT INTO scores1 (username, scores, timetaken, date, phone, level, location) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertScoreQuery);
// Bind phone to phone and location to location as per your original code's variable usage
$stmt->bind_param("sisssss", $name, $score, $time_taken, $date, $phone, $level, $location); // 's' for string, 'i' for integer
if ($stmt->execute() !== TRUE) {
    echo "Error inserting score: " . $stmt->error;
}
$stmt->close();


// --- START: IMPROVED STUDENT STATUS UPDATE ---
// Instead of deleting, mark the student as having taken the exam
// Using prepared statement for safety
$updateStudentQuery = "UPDATE students SET exam_taken = TRUE WHERE username = ?";
$stmt = $conn->prepare($updateStudentQuery);
$stmt->bind_param("s", $username);
if ($stmt->execute() !== TRUE) {
    echo "Error updating student status: " . $stmt->error;
}
$stmt->close();
// --- END: IMPROVED STUDENT STATUS UPDATE ---

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results - Math Mastery Abacus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-bg': '#FFFBF5',
                        'accent-orange': '#FF8A00',
                        'accent-brown': '#7B4A2F',
                        'secondary-yellow': '#FFD166',
                        'text-dark': '#3C3C3C',
                        'text-light': '#6E6E6E',
                        'btn-primary': '#E67E22',
                        'btn-secondary': '#F39C12',
                        'hero-gradient-start': '#F8C471',
                        'hero-gradient-end': '#FF8A00',
                        'footer-bg': '#2C3E50',
                        'footer-text': '#BDC3C7',
                    }
                }
            }
        }
    </script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($chartData); ?>);

            var options = {
                title: 'Exam Results',
                is3D: true,
                colors: ['#66ff99', '#ff6666'],
                pieSliceText: 'label'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>

<body class="font-sans text-text-dark bg-primary-bg antialiased flex items-center justify-center min-h-screen p-4">
    <header id="header" class="fixed top-0 left-0 w-full bg-primary-bg bg-opacity-90 z-50 transition-all duration-300">
        <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-2">
                <img src="logo1.jpg" alt="Math Mastery Abacus Logo" class="h-10 w-10 rounded-full bg-secondary-yellow">
                <span class="text-2xl font-extrabold text-accent-brown">Math Mastery Abacus</span>
            </a>

            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-text-light focus:outline-none focus:text-text-dark">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <ul id="main-nav" class="hidden md:flex space-x-8 items-center">
                <li><a href="#hero"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Home</a>
                </li>
                <li><a href="admin1.php"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Admin
                        Panel</a></li>
                <li><a href="#courses"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Courses</a>
                </li>
                <li><a href="#testimonials"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Testimonials</a>
                </li>
                <li><a href="#faq"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">FAQ</a>
                </li>
                <li><a href="#contact"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Contact</a>
                </li>
                <li>
                    <a href="login.php"
                        class="px-6 py-3 bg-btn-primary text-white rounded-full font-semibold hover:bg-accent-orange transition duration-300 shadow-md">
                        Log In
                    </a>
                </li>
            </ul>
        </nav>

        <div id="mobile-menu" class="hidden md:hidden bg-primary-bg py-4 shadow-lg">
            <ul class="flex flex-col items-center space-y-4">
                <li><a href="#hero"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Home</a>
                </li>
                <li><a href="admin1.php"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">
                        Admin Panel</a></li>
                <li><a href="#courses"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Courses</a>
                </li>
                <li><a href="#testimonials"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Testimonials</a>
                </li>
                <li><a href="#faq"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">FAQ</a>
                </li>
                <li><a href="#contact"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Contact</a>
                </li>
                <li>
                    <a href="login.php"
                        class="px-6 py-3 bg-btn-primary text-white rounded-full font-semibold hover:bg-accent-orange transition duration-300 shadow-md block text-center">
                        Log In
                    </a>
                </li>
            </ul>
        </div>
    </header>


    <div
        class="bg-white p-8 md:p-12 rounded-lg shadow-xl max-w-xl w-full text-center border-t-4 border-accent-orange mx-auto">
        <h1 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-4">Great Job,
            <?php echo htmlspecialchars($name . ' ' . $phone); ?>!</h1>
        <h2 class="text-2xl font-bold text-accent-orange mb-3">Your Score: <strong
                class="text-accent-brown"><?php echo $score; ?></strong> / <strong
                class="text-accent-brown"><?php echo $total_questions; ?></strong></h2>
        <p class="text-lg text-text-light mb-2">Time Taken: <?php echo $time_taken_formatted; ?></p>
        <p class="text-lg text-text-light mb-6">Date: <?php echo $date; ?></p>

        <div id="piechart" class="w-full h-80 mx-auto my-6"></div>

        <form action="generate_certificate1.php" method="post" class="text-center mt-6">
            <button type="submit"
                class="bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg">Download
                Certificate</button>
        </form>
    </div>
</body>

</html>