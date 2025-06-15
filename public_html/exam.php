<?php
session_start();

// Redirect to login if not logged in or if signup data is missing
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve session data
$username = $_SESSION['username'];
$name = $_SESSION['name'];
// Assuming 'phone' and 'location' might be added to session/fetched later,
// keeping them commented out as they weren't explicitly set in previous code snippets for exam.php
// $phone = $_SESSION['phone'];
// $location = $_SESSION['location'];
$level = $_SESSION['level'];

// Database connection
require 'db_connect.php';

// Define the number of questions you want for the exam
$numberOfQuestions = 50;

// Determine the question table based on the level
$questionTable = ($level == 'jr') ? "question_jr" : "question_sr";

// --- START: OPTIMIZED QUESTION FETCHING ---

// Step 1: Get all IDs (sr) for the selected level from the database
$idQuery = "SELECT sr FROM $questionTable";
$idResult = $conn->query($idQuery);

$allQuestionIds = [];
if ($idResult->num_rows > 0) {
    while ($row = $idResult->fetch_assoc()) {
        $allQuestionIds[] = $row['sr'];
    }
} else {
    // Handle case where no questions are found for the level
    die("No questions found for the " . htmlspecialchars($level) . " level. Please contact support.");
}

// Step 2: Shuffle the array of IDs in PHP and pick the required number of random IDs
shuffle($allQuestionIds);
$selectedQuestionIds = array_slice($allQuestionIds, 0, $numberOfQuestions);

// Handle case where there are fewer questions than requested
if (count($selectedQuestionIds) < $numberOfQuestions) {
    // You might log this or show a message, but the exam will proceed with fewer questions
    // if there aren't enough in the database.
    // For now, it will just use the available questions.
}

// Ensure there are actually IDs to query
if (empty($selectedQuestionIds)) {
    die("Not enough questions available to start the exam for the " . htmlspecialchars($level) . " level.");
}

// Step 3: Fetch the full question data for the selected IDs in a single query
// It's crucial to ensure the IDs are integers to prevent SQL injection in the IN clause
$idList = implode(',', array_map('intval', $selectedQuestionIds));
$questionsQuery = "SELECT * FROM $questionTable WHERE sr IN ($idList)";
$questionsResult = $conn->query($questionsQuery);

$questions = [];
if ($questionsResult->num_rows > 0) {
    while ($row = $questionsResult->fetch_assoc()) {
        $questions[] = $row;
    }
}

// IMPORTANT: Shuffle the fetched questions again to ensure random order if the database's IN clause reordered them
shuffle($questions);

// --- END: OPTIMIZED QUESTION FETCHING ---

// Start the timer (placed here to ensure questions are loaded before timer starts for user)
$_SESSION['start_time'] = time();

// Make sure the number of questions sent to JavaScript matches what we actually fetched
// This helps keep JS counter consistent.
$actualQuestionsCount = count($questions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Page - ProSkill Abacus</title>
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
</head>
<body class="font-sans text-text-dark bg-primary-bg antialiased flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 md:p-12 rounded-lg shadow-xl max-w-2xl w-full text-center border-t-4 border-accent-orange mx-auto">
        <h1 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-4">Fun Quiz Time!</h1>
        <p id="timer" class="text-xl text-accent-orange font-bold mb-4">600 seconds remaining</p>

        <p id="questionCounter" class="text-lg text-text-light mb-6 font-semibold">Question 1 of <?php echo $actualQuestionsCount; ?></p>

        <form id="examForm" action="display4.php" method="POST">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="hidden" name="level" value="<?php echo htmlspecialchars($level); ?>">

            <input type="hidden" id="questionsData" value='<?php echo json_encode($questions); ?>'>

            <div id="questionContainer" class="min-h-[150px] flex flex-col justify-center items-center mb-8"></div>
            <input type="submit" id="submitButton" class="bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg mt-8" value="Submit Answers" style="display: none;">
        </form>
    </div>

    <script>
        var totalSeconds = 600; // 10 minutes
        var countdown = setInterval(function () {
            if (totalSeconds <= 0) {
                clearInterval(countdown);
                document.getElementById('examForm').submit();
            }
            document.getElementById('timer').innerHTML = totalSeconds + " seconds remaining";
            totalSeconds--;
        }, 1000);

        // Parse the questions fetched from PHP
        const questions = JSON.parse(document.getElementById('questionsData').value);
        let currentQuestionIndex = 0;

        // Use the actual count of questions passed from PHP for the counter
        const actualQuestions = questions; // No need for slice(0,50) here, PHP already handles it

        function displayQuestion(index) {
            const questionContainer = document.getElementById("questionContainer");
            questionContainer.innerHTML = "";

            // Update question counter based on actual number of questions
            document.getElementById("questionCounter").innerText = `Question ${index + 1} of ${actualQuestions.length}`;

            const question = actualQuestions[index];
            if (!question) return;

            const questionText = document.createElement("p");
            questionText.classList.add("text-2xl", "font-semibold", "text-text-dark", "mb-6");
            questionText.innerText = question.question;
            questionContainer.appendChild(questionText);

            const optionsContainer = document.createElement("div");
            optionsContainer.classList.add("grid", "grid-cols-1", "md:grid-cols-2", "gap-4", "text-left", "w-full");

            ["o1", "o2", "o3", "o4"].forEach(option => {
                const label = document.createElement("label");
                label.classList.add(
                    "block", "p-4", "border", "border-gray-300", "rounded-lg",
                    "cursor-pointer", "transition-all", "duration-200", "text-text-dark",
                    "hover:bg-secondary-yellow", "hover:text-text-dark", "shadow-sm"
                );
                label.innerHTML = `<input type="radio" name="q${question.sr}" value="${question[option]}" required class="mr-3 transform scale-125 accent-accent-orange"> ${question[option]}`;

                label.querySelector("input").addEventListener("change", function() {
                    const currentLabels = questionContainer.querySelectorAll('label');
                    currentLabels.forEach(lbl => {
                        lbl.style.backgroundColor = "";
                        lbl.style.color = "";
                        lbl.classList.remove("bg-accent-orange", "text-white");
                    });

                    label.style.backgroundColor = "#FF8A00";
                    label.style.color = "white";

                    setTimeout(function() {
                        // Ensure the hidden input for the current question's answer is updated
                        // before moving to the next question.
                        document.getElementById(`input_q${question.sr}`).value = label.querySelector("input").value;

                        currentQuestionIndex++;
                        if (currentQuestionIndex < actualQuestions.length) {
                            displayQuestion(currentQuestionIndex);
                        } else {
                            document.getElementById("submitButton").style.display = "block";
                            clearInterval(countdown);
                            // Set a small delay before submitting to allow user to see "Submit Answers" button briefly
                            // Or remove this delay if immediate submission on last question answer is desired
                            // setTimeout(() => { document.getElementById('examForm').submit(); }, 1000);
                        }
                    }, 250);
                });

                // Original JS hover effects (will override Tailwind hover classes)
                label.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#FFD166";
                    this.style.color = "#3C3C3C";
                });
                label.addEventListener("mouseout", function() {
                    if (!this.querySelector("input").checked) {
                        this.style.backgroundColor = "";
                        this.style.color = "";
                    } else {
                        this.style.backgroundColor = "#FF8A00";
                        this.style.color = "white";
                    }
                });

                optionsContainer.appendChild(label);
            });

            questionContainer.appendChild(optionsContainer);
        }

        // Create hidden inputs for all questions at once
        actualQuestions.forEach(q => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = `q${q.sr}`;
            input.id = `input_q${q.sr}`; // Assign an ID to the hidden input for easy access
            document.getElementById("examForm").appendChild(input);
        });

        displayQuestion(currentQuestionIndex);
    </script>
</body>
</html>