<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'db_connect.php';

// Establishing connection (assuming db_connect.php provides $servername, $db_username, $db_password, $dbname)
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['sirname']; // Changed variable name to 'phone' based on label 'Your Phone'
    $address = $_POST['adress'];
    $level = $_POST['level'];

    // Prepare and bind for update (assuming 'phone' corresponds to 'sirname' in your form/logic)
    // NOTE: The database field is 'adress', not 'address' based on your query.
    // The query is "UPDATE students SET name = ?, adress = ?, level = ? WHERE username = ?"
    // The sirname field from the form is being mapped to 'phone' here, but it's not
    // being updated in the 'students' table with this query. Only name, adress, level are updated.
    // Make sure your database schema aligns with your PHP logic for 'phone' or 'sirname'.
    $stmt = $conn->prepare("UPDATE students SET name = ?, adress = ?, level = ? WHERE username = ?");
    $stmt->bind_param("ssss", $name, $address, $level, $username);

    // Execute the statement
    if ($stmt->execute()) {
        // Store details in session, including sirname (now mapped to phone)
        $_SESSION['name'] = $name;
        $_SESSION['phone'] = $phone; // Storing phone in session
        $_SESSION['address'] = $address;
        $_SESSION['level'] = $level;

        // Redirect to exam.php after successful submission
        header("Location: exam.php");
        exit();
    } else {
        $error = "Oops! Something went wrong. Please try again.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection (only if form was not submitted or in case of error, otherwise it might close too early)
// Best practice is to close it once all database operations are done for the page.
// In this case, if POST is successful, it redirects and close is not reached.
// If GET or POST fails, it's reached. So this is generally fine.
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Info - ProSkill Abacus</title>
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
</head>
<body class="font-sans text-text-dark bg-primary-bg antialiased flex items-center justify-center min-h-screen p-4">
<div class="bg-white p-8 md:p-12 rounded-lg shadow-xl max-w-2xl w-full text-center border-t-4 border-accent-orange mx-auto">
    <h2 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-6">Student Information Form</h2>
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="" class="space-y-4">
        <div class="mb-4">
            <label for="name" class="block text-left text-lg font-medium text-accent-brown mb-2">Your Name</label>
            <input 
                type="text" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="name" 
                name="name" 
                required
            >
        </div>
        <div class="mb-4">
            <label for="sirname" class="block text-left text-lg font-medium text-accent-brown mb-2">Your Phone</label>
            <input 
                type="text" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="sirname" 
                name="sirname" 
                required
            >
        </div>
        <div class="mb-4">
            <label for="adress" class="block text-left text-lg font-medium text-accent-brown mb-2">Your Address</label>
            <input 
                type="text" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="adress" 
                name="adress" 
                required
            >
        </div>

        <div class="mb-4">
            <label for="level" class="block text-left text-lg font-medium text-accent-brown mb-2">Level</label>
            <select 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark bg-white appearance-none" 
                id="level" 
                name="level" 
                required
            >
                <option value="jr">Junior</option>
                <option value="sr">Senior</option>
            </select>
            </div>
        <button 
            type="submit" 
            class="w-full bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg mt-4"
        >
            Submit & Go to Exam
        </button>
    </form>
</div>

</body>
</html>