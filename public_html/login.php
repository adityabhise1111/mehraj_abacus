<?php
session_start();

// Database connection
require_once 'db_connect.php';
     
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute statement to fetch user
    $stmt = $conn->prepare("SELECT password FROM students WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Start session and redirect
            $_SESSION['username'] = $username; // Store username in session
            header("Location: studentinfo.php"); // Redirect to studentinfo.php
            exit();
        } else {
            $error = "Oops! Wrong username or password. Try again!";
        }
    } else {
        // User not found
        $error = "You are not eligible to give exam.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ProSkill Abacus</title>
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

<div class="bg-white p-8 md:p-12 rounded-lg shadow-xl max-w-md w-full text-center border-t-4 border-accent-orange mx-auto">
    <img src="logo1.jpg" alt="Abacus Icon" class="h-24 w-24 rounded-full mx-auto mb-6 shadow-md">

    <h2 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-6">Welcome to Abacus Fun!</h2>
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="" class="space-y-4">
        <div class="mb-4"> <label for="username" class="block text-left text-lg font-medium text-accent-brown mb-2">Your Username</label>
            <input 
                type="text" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="username" 
                name="username" 
                required
            >
        </div>
        <div class="mb-4"> <label for="password" class="block text-left text-lg font-medium text-accent-brown mb-2">Your Password</label>
            <input 
                type="password" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="password" 
                name="password" 
                required
            >
        </div>
        <button 
            type="submit" 
            class="w-full bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg mt-4"
        >
            Next
        </button>
    </form>
</div>

</body>
</html>