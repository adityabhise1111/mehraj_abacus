<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login2.php"); // Assuming 'login2.php' is the correct login page for students
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $level = $_POST['level']; // "jr" or "sr" from the form

    // Store user details in the session (username already in session from login2.php)
    $_SESSION['name'] = $name;
    $_SESSION['phone'] = $phone;
    $_SESSION['location'] = $location;
    $_SESSION['level'] = $level;

    // Redirect to exam2.php
    header("Location: exam2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up - ProSkill Abacus</title>
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
    <h2 class="text-3xl md:text-4xl font-extrabold text-accent-brown mb-6">Sign Up for Abacus Fun!</h2>
    <p class="text-text-light mb-8">Tell us a little about yourself to get started.</p>

    <form method="POST" action="" class="space-y-6">
        <div class="text-left">
            <label for="name" class="block text-text-dark text-sm font-bold mb-2">Your Name</label>
            <input 
                type="text" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="name" 
                name="name" 
                required
            >
        </div>
        <div class="text-left">
            <label for="phone" class="block text-text-dark text-sm font-bold mb-2">Phone Number</label>
            <input 
                type="number" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="phone" 
                name="phone" 
                required
            >
        </div>
        <div class="text-left">
            <label for="location" class="block text-text-dark text-sm font-bold mb-2">Your Location</label>
            <input 
                type="text" 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark" 
                id="location" 
                name="location" 
                required
            >
        </div>
        <div class="text-left">
            <label for="level" class="block text-text-dark text-sm font-bold mb-2">Choose Your Level</label>
            <select 
                class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-orange text-text-dark bg-white appearance-none" 
                id="level" 
                name="level" 
                required
            >
                <option value="jr">Junior </option>
                <option value="sr">Senior</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
            </div>
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-btn-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent-orange transition duration-300 shadow-md text-lg mt-6"
        >
            Sign Up
        </button>
    </form>
</div>

</body>
</html>