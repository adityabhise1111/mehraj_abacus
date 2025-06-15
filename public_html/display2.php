<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login2.php");
    exit();
}

// Database connection
// Ensure db_connect.php properly establishes $conn
require_once 'db_connect.php';

// Get data from session
$username = $_SESSION['username'];
$name = $_SESSION['name'];
$phone = $_SESSION['phone'];
$location = $_SESSION['location'];
$level = $_SESSION['level'];

// Calculate time taken for the exam
$end_time = time();
$start_time = $_SESSION['start_time'];
$time_taken = $end_time - $start_time;
$time_taken_formatted = gmdate("H:i:s", $time_taken);

// Determine question table based on level
$questionTable = ($level == 'jr') ? "demo_jr" : "demo_sr";

// Initialize score and set total number of questions
$score = 0;
$total_questions = 20;

// Process each submitted answer
foreach ($_POST as $key => $selected_answer) {
    if (strpos($key, 'q') === 0) {
        $question_id = substr($key, 1);
        $query = "SELECT ans FROM $questionTable WHERE sr = $question_id";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $correct_answer = $result->fetch_assoc()['ans'];
            if ($selected_answer == $correct_answer) {
                $score++;
            }
        }
    }
}

// Calculate incorrect answers
$incorrect = $total_questions - $score;

// Data for pie chart (keeping original colors as per instruction not to touch logic/data)
$chartData = [
    ['Result', 'Number of Questions'],
    ['Correct', $score],
    ['Incorrect', $incorrect]
];

// Save score to the database
$date = date('Y-m-d');
$insertScoreQuery = "INSERT INTO scores (username, scores, timetaken, date) VALUES ('$name', '$score', '$time_taken', '$date')";
if ($conn->query($insertScoreQuery) !== TRUE) {
    echo "Error: " . $insertScoreQuery . "<br>" . $conn->error;
}

// Store values in session for certificate generation
$_SESSION['score'] = $score;
$_SESSION['time_taken_formatted'] = $time_taken_formatted;
$_SESSION['exam_date'] = $date;

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProSkill Abacus - Exam Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Based on the image's warm/childish theme from your main site
                        'primary-bg': '#FFFBF5', // Soft, warm cream background
                        'accent-orange': '#FF8A00', // Bright, friendly orange
                        'accent-brown': '#7B4A2F',  // Deeper, earthy brown for text/elements
                        'secondary-yellow': '#FFD166', // Lighter, warm yellow
                        'text-dark': '#3C3C3C', // Very dark gray/off-black for body text
                        'text-light': '#6E6E6E', // Lighter gray for secondary text
                        'btn-primary': '#E67E22', // A more vibrant, inviting orange for buttons
                        'btn-secondary': '#F39C12', // A slightly different orange/yellow for secondary buttons
                        'hero-gradient-start': '#F8C471', // Lighter yellow for gradient
                        'hero-gradient-end': '#FF8A00',   // Deeper orange for gradient
                        'footer-bg': '#2C3E50', // Dark but warm navy/charcoal for footer (contrasting but still soft)
                        'footer-text': '#BDC3C7', // Light gray for footer text
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($chartData); ?>);

            var options = {
                title: 'Exam Results',
                is3D: true,
                // These colors are from your original code. You could change them here
                // to match your Tailwind palette (e.g., '#accent-orange', '#secondary-yellow')
                // if you want the chart colors to be themed.
                colors: ['#66ff99', '#ff6666'], 
                pieSliceText: 'label',
                titleTextStyle: {
                    color: '#3C3C3C', // text-dark from your palette
                    fontSize: 18,
                    bold: true
                },
                legend: {
                    textStyle: {
                        color: '#6E6E6E' // text-light from your palette
                    }
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body class="font-sans text-text-dark bg-primary-bg antialiased flex flex-col min-h-screen">
    <header id="header" class="fixed top-0 left-0 w-full bg-primary-bg bg-opacity-90 z-50 transition-all duration-300 shadow-md">
        <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-2">
                <img src="logo1.jpg" alt="ProSkill Abacus Logo" class="h-10 w-10 rounded-full bg-secondary-yellow">
                <span class="text-2xl font-extrabold text-accent-brown">Math Mastery Abacus</span>
            </a>
            
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-text-light focus:outline-none focus:text-text-dark">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <ul id="main-nav" class="hidden md:flex space-x-8 items-center">
                <li><a href="#hero" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Home</a></li>
                <li><a href="admin1.php" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Admin Panel</a></li>
                <li><a href="#courses" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Courses</a></li>
                <li><a href="#testimonials" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Testimonials</a></li>
                <li><a href="#faq" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">FAQ</a></li>
                <li><a href="#contact" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Contact</a></li>
                <li>
                    <a href="login.php" class="px-6 py-3 bg-btn-primary text-white rounded-full font-semibold hover:bg-accent-orange transition duration-300 shadow-md">
                        Log In
                    </a>
                </li>
            </ul>
        </nav>

        <div id="mobile-menu" class="hidden md:hidden bg-primary-bg py-4 shadow-lg">
            <ul class="flex flex-col items-center space-y-4">
                <li><a href="#hero" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Home</a></li>
                <li><a href="admin1.php" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2"> Admin Panel</a></li>
                <li><a href="#courses" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Courses</a></li>
                <li><a href="#testimonials" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Testimonials</a></li>
                <li><a href="#faq" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">FAQ</a></li>
                <li><a href="#contact" class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300 block py-2">Contact</a></li>
                <li>
                    <a href="login.php" class="px-6 py-3 bg-btn-primary text-white rounded-full font-semibold hover:bg-accent-orange transition duration-300 shadow-md block text-center">
                        Log In
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <div class="flex-grow flex items-center justify-center p-4 pt-24 md:pt-32">
        <div class="max-w-3xl w-full mx-auto">
            <div class="bg-white p-8 md:p-12 rounded-lg shadow-xl text-center border-t-4 border-accent-orange">
                <h1 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-4">Great Job!</h1>
                <h2 class="text-2xl md:text-3xl font-bold text-text-dark mb-4">Your Score: <strong class="text-accent-orange"><?php echo $score; ?></strong> / <strong class="text-accent-orange"><?php echo $total_questions; ?></strong></h2>
                <p class="text-lg text-text-light mb-2">Time Taken: <span class="font-semibold text-text-dark"><?php echo $time_taken_formatted; ?></span></p>
                <p class="text-lg text-text-light mb-6">Date: <span class="font-semibold text-text-dark"><?php echo $date; ?></span></p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left mt-6 mb-8">
                    <div>
                        <p class="mb-2"><strong class="text-accent-brown">Name:</strong> <span class="text-text-dark"><?php echo htmlspecialchars($name); ?></span></p>
                        <p class="mb-2"><strong class="text-accent-brown">Phone:</strong> <span class="text-text-dark"><?php echo htmlspecialchars($phone); ?></span></p>
                    </div>
                    <div>
                        <p class="mb-2"><strong class="text-accent-brown">Location:</strong> <span class="text-text-dark"><?php echo htmlspecialchars($location); ?></span></p>
                        <p class="mb-2"><strong class="text-accent-brown">Level:</strong> <span class="text-text-dark"><?php echo htmlspecialchars($level); ?></span></p>
                    </div>
                </div>

                <div class="flex justify-center mb-8">
                    <div id="piechart" class="w-full max-w-sm h-72"></div>
                </div>

                <form action="generate_certificate2.php" method="post" class="mt-4">
                    <button type="submit" class="bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg">
                        Download Certificate
                    </button>
                </form>
            </div>
        </div>
    </div>
    </body>
</html>