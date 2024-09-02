<?php
session_start();
require 'PHP/Connection.php';

// Pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Execute the SQL query to fetch audit logs with pagination
$sql = "SELECT userlogs.LogID, CONCAT(users.FirstName, ' ', users.LastName) AS FullName, userlogs.LogDesc, userlogs.LogTimestamp
        FROM userlogs
        INNER JOIN users ON userlogs.UserID = users.UserID
        ORDER BY userlogs.LogTimestamp DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/AdminLog.css">
    <title>Admin Page</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="AdminPage.php">Reports</a></li>
            <li><a href="AdminLog.php">Logs</a></li>
            <li><a href="AdminOrders.php">Orders</a></li>
            <li><a href="ManageItems.php">Manage Items</a></li>
        </ul>
        <button onclick="location.href='PHP/Logout.php'">Logout</button>
    </nav>
    <main>
        <h1>Audit Logs</h1>
        <div class="main-container">
            <table>
                <thead>
                    <tr>
                        <th>LogID</th>
                        <th>Name</th>
                        <th>Login Desc</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are rows returned
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["LogID"] . "</td>";
                            echo "<td>" . $row["FullName"] . "</td>";
                            echo "<td>" . $row["LogDesc"] . "</td>";
                            echo "<td>" . $row["LogTimestamp"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No audit logs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <?php
            // Pagination controls
            $sql = "SELECT COUNT(*) AS total FROM userlogs";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $total_pages = ceil($row["total"] / $limit);

            // Define the range of pages to display
            $start_page = max(1, $page - 5);
            $end_page = min($total_pages, $page + 4);
            if ($end_page - $start_page < 9) {
                if ($start_page > 1) {
                    $start_page = max(1, $end_page - 9);
                } else {
                    $end_page = min($total_pages, $start_page + 9);
                }
            }
            ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?>">Previous</a>
                <?php endif; ?>
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo "class='active'"; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo ($page + 1); ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
