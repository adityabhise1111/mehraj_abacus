<?php
session_start();

// Admin credentials
define('ADMIN_USERNAME', 'mehraj');
define('ADMIN_PASSWORD_HASH', password_hash('12345', PASSWORD_DEFAULT));

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['username'] = true;
        header('Location: signup2.php'); // Redirect to dashboard
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
    <title>Student Login - ProSkill Abacus</title>
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
    <style>
        /* No custom CSS needed here as Tailwind handles everything,
           but if you wanted a very specific hover effect not possible with Tailwind alone, it'd go here. */
    </style>
</head>

<body class="font-sans text-text-dark bg-primary-bg antialiased flex items-center justify-center min-h-screen p-4">

    <header id="header" class="fixed top-0 left-0 w-full bg-primary-bg bg-opacity-90 z-50 transition-all duration-300">
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
                <li><a href="/index.html"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Home</a>
                </li>
                <li><a href="admin1.php"
                        class="text-lg font-medium text-text-light hover:text-accent-orange transition duration-300">Admin
                        Panel</a></li>
                
                
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
               
                
            </ul>
        </div>
    </header>

    <div class="bg-white p-8 md:p-12 rounded-lg shadow-xl max-w-md w-full text-center border-t-4 border-accent-orange">
        <h1 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-6">Student Login</h1>
        <p class="text-text-light mb-8">Please enter your credentials.</p>

        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="sr-only">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required
                    class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark">
            </div>
            <div>
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required
                    class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark">
            </div>

            <?php if (isset($login_error))
                echo "<p class='text-red-600 text-sm font-medium'>$login_error</p>"; ?>

            <button type="submit" name="login"
                class="w-full bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg">
                Login
            </button>
        </form>
    </div>

</body>

</html>