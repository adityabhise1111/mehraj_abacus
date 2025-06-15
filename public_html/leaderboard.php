<?php
session_start();

// Database connection
require_once 'db_connect.php';

// Default sort column
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'scores';

// Validate sort options to prevent SQL injection
$allowed_sorts = ['username', 'scores', 'date', 'phone', 'location', 'level'];
if (!in_array($sort_column, $allowed_sorts)) {
    $sort_column = 'scores';
}

// Fetch leaderboard data sorted by selected column
$query = "SELECT username, scores, date, phone, location, level FROM scores1 ORDER BY $sort_column DESC";
$result = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Additional styles to allow horizontal scrolling */
        .table-responsive {
            overflow-x: auto; /* Allow horizontal scrolling */
        }
        table {
            width: 100%; /* Full width */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Leaderboard</h1>

        <form action="leaderboard.php" method="get" class="mb-4">
            <div class="form-group">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="username" <?php if ($sort_column == 'username') echo 'selected'; ?>>Username</option>
                    <option value="scores" <?php if ($sort_column == 'scores') echo 'selected'; ?>>Score</option>
                    <option value="date" <?php if ($sort_column == 'date') echo 'selected'; ?>>Date</option>
                    <option value="phone" <?php if ($sort_column == 'phone') echo 'selected'; ?>>Phone</option>
                    <option value="location" <?php if ($sort_column == 'location') echo 'selected'; ?>>Location</option>
                    <option value="level" <?php if ($sort_column == 'level') echo 'selected'; ?>>Level</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Sort</button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Username</th>
                        <th>Score</th>
                        <th>Date</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['scores']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['level']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
