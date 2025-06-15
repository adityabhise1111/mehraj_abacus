<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: adminlogin.php');
    exit();
}

// Database connection
$host = "127.0.0.1";
$user = "u804948088_abacus";
$pass = "#Abacus123";
$db = "u804948088_abacus";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new student with only username and password
$message = "";
if (isset($_POST['insertStudent'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $insertQuery = "INSERT INTO students (username, password, name , phone,adress,level) VALUES  ('$username', '$password', '', 0, '', '')";     
    if ($conn->query($insertQuery)) {
        $message = "Student has been added successfully!";
    } else {
        $message = "Failed to add student." . $conn->error;
    }
}

// Delete a student
if (isset($_POST['deleteStudent'])) {
    $sr = $_POST['sr'];
    $deleteQuery = "DELETE FROM students WHERE sr = $sr";
    $conn->query($deleteQuery);
}

// Fetch all students
$result = $conn->query("SELECT sr, username FROM students");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            margin-right:60px;
            color: #333;
        }
        .button-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .logout-btn, .leaderboard-btn {
            background-color: #dc3545;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-left: 10px;
        }
        .leaderboard-btn {
            background-color: #007bff;
        }
        .message {
            text-align: center;
            color: green;
            margin: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .delete-btn, .insert-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
        }
        .insert-btn {
            background-color: #28a745;
        }
        .form-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }
        .form-container h2 {
            margin-bottom: 15px;
        }
        .form-container input {
            margin-bottom: 10px;
            padding: 8px;
            width: calc(100% - 20px);
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    

<div class="button-container">
    <a href="leaderboard.php" class="leaderboard-btn">Leaderboard</a>
    <a href="adminlogin.php?logout=true" class="logout-btn">Logout</a>
</div>

<div class="container">
    <h1>Dashboard</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Insert New Student (Only Username and Password) -->
    <div class="form-container">
        <h2>Insert New Student</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" name="insertStudent" class="insert-btn" value="Insert Student">
        </form>
    </div>

    <!-- All Students -->
    <h2>All Students</h2>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row['sr']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="sr" value="<?php echo $row['sr']; ?>">
                            <input type="submit" class="delete-btn" name="deleteStudent" value="Delete">
                        </form>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='3'>No students found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
