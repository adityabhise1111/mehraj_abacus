<?php
session_start();

// Redirect to login if not logged in or if signup data is missing
if (!isset($_SESSION['username'], $_SESSION['name'], $_SESSION['phone'], $_SESSION['location'], $_SESSION['level'])) {
    header("Location: login2.php");
    exit();
}

// Retrieve session data
$username = $_SESSION['username'];
$name = $_SESSION['name'];
$phone = $_SESSION['phone'];
$location = $_SESSION['location'];
$level = $_SESSION['level'];

// Database connection
// Make sure 'db_connect.php' exists and establishes a valid $conn
require_once 'db_connect.php';

// Fetch questions based on the level
$questionTable = ($level == 'jr') ? "demo_jr" : "demo_sr";
$questionsQuery = "SELECT * FROM $questionTable ORDER BY RAND() LIMIT 20";
$questionsResult = $conn->query($questionsQuery);

// Store questions in an array to use with JavaScript
$questions = [];
if ($questionsResult->num_rows > 0) {
    while ($row = $questionsResult->fetch_assoc()) {
        $questions[] = $row;
    }
}

// Start the timer
$_SESSION['start_time'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProSkill Abacus - Fun Quiz Time!</title>
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
</head>
<body class="font-sans text-text-dark bg-primary-bg antialiased flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 md:p-12 rounded-lg shadow-xl max-w-2xl w-full text-center border-t-4 border-accent-orange">
        <h1 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-6">Fun Quiz Time!</h1>
        <p id="timer" class="text-xl font-semibold text-accent-orange mb-4">240 seconds remaining</p>
        
        <form id="examForm" action="display2.php" method="POST">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
            <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
            <input type="hidden" name="level" value="<?php echo htmlspecialchars($level); ?>">

            <input type="hidden" id="questionsData" value='<?php echo json_encode($questions); ?>'>

            <p id="questionCounter" class="text-lg font-medium text-text-light mb-6"></p> <div id="questionContainer" class="mb-8"></div>
            
            <input type="submit" id="submitButton" class="w-full bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg hidden" value="Submit Answers">
        </form>
    </div>

    <script>
        var totalSeconds = 240;
        var countdown = setInterval(function () {
            if (totalSeconds <= 0) {
                clearInterval(countdown);
                document.getElementById('examForm').submit();
            }
            document.getElementById('timer').innerHTML = totalSeconds + " seconds remaining";
            totalSeconds--;
        }, 1000);

        const questions = JSON.parse(document.getElementById('questionsData').value);
        let currentQuestionIndex = 0;

        function displayQuestion(index) {
            const questionContainer = document.getElementById("questionContainer");
            questionContainer.innerHTML = "";

            // Update question counter
            const questionCounter = document.getElementById("questionCounter");
            questionCounter.innerText = `Question ${index + 1} of ${questions.length}`;

            const question = questions[index];
            if (!question) return;

            const questionText = document.createElement("p");
            questionText.classList.add("question", "text-2xl", "font-bold", "text-accent-brown", "mb-8", "leading-relaxed"); // Tailwind classes for question text
            questionText.innerText = question.question;
            questionContainer.appendChild(questionText);

            const optionsContainer = document.createElement("div");
            optionsContainer.classList.add("options", "grid", "grid-cols-1", "md:grid-cols-2", "gap-4", "text-left"); // Tailwind classes for options grid

            ["o1", "o2", "o3", "o4"].forEach(option => {
                const label = document.createElement("label");
                // The tricky part: using `has-[:checked]` to style the label when its contained radio is checked.
                // This requires Tailwind's JIT mode, which is enabled by the CDN.
                label.classList.add(
                    "block", "p-4", "rounded-lg", "border", "border-gray-300", 
                    "cursor-pointer", "transition-all", "duration-200", "ease-in-out",
                    "hover:bg-secondary-yellow", "hover:border-accent-orange", "hover:text-accent-brown",
                    "has-[:checked]:bg-accent-orange", "has-[:checked]:text-white", "has-[:checked]:border-accent-brown",
                    "has-[:checked]:shadow-md" // Add a subtle shadow when checked
                );
                label.innerHTML = `
                    <input type="radio" name="q${question.sr}" value="${question[option]}" required class="sr-only"> 
                    <span class="font-medium">${question[option]}</span>
                `;
                optionsContainer.appendChild(label);

                // Modified event listener to ensure hidden input is updated and then advance question
                label.querySelector("input").addEventListener("change", function() {
                    document.getElementById(`input_q${question.sr}`).value = this.value;
                    setTimeout(() => {
                        currentQuestionIndex++;
                        if (currentQuestionIndex < questions.length) {
                            displayQuestion(currentQuestionIndex);
                        } else {
                            // Ensure the submit button becomes visible if it's the last question
                            document.getElementById("submitButton").classList.remove("hidden"); // Remove hidden class
                        }
                    }, 250);  // 250 ms delay
                });
            });

            questionContainer.appendChild(optionsContainer);
        }

        questions.forEach(q => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = `q${q.sr}`;
            input.id = `input_q${q.sr}`;
            document.getElementById("examForm").appendChild(input);
        });

        // Initial display of the first question
        displayQuestion(currentQuestionIndex);
        
        // Ensure submit button is hidden initially and only shown when all questions are answered
        document.getElementById("submitButton").classList.add("hidden"); 
    </script>
</body>
</html>