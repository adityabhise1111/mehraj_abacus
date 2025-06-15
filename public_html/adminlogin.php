<?php
session_start();

// Admin credentials
define('ADMIN_USERNAME', 'mehrajshaikh'); 
define('ADMIN_PASSWORD_HASH', password_hash('mehraj2024shaikh2024', PASSWORD_DEFAULT)); 

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin1.php'); // Redirect to dashboard
        exit;
    } else {
        $login_error = "Invalid username or password.";
    }
}

// Logout handler
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: adminlogin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ProSkill Abacus</title>
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

<div class="bg-white p-8 md:p-12 rounded-lg shadow-xl max-w-md w-full text-center border-t-4 border-accent-orange">
    <h1 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-6">Admin Login</h1>
    <form method="POST" class="space-y-4">
        <input 
            type="text" 
            name="username" 
            placeholder="Enter Username" 
            required 
            class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark"
        >
        <input 
            type="password" 
            name="password" 
            placeholder="Enter Password" 
            required 
            class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark"
        >
        <button 
            type="submit" 
            name="login" 
            class="w-full bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg mt-4"
        >
            Login
        </button>
        <?php if (isset($login_error)) echo "<p class='text-red-600 text-sm mt-4'>$login_error</p>"; ?>
    </form>
</div>

</body>
</html>