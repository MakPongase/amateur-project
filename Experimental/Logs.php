<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Logs.css">
    <title>Document</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="">Reports</a></li>
            <li><a href="">Logs</a></li>
            <li><a href="">Orders</a></li>
            <li><a href="">Mange Items</a></li>
        </ul>
        <button>Logout</button>
    </nav>
    <main>
        <h1>Audit Logs</h1>
        <h3>This day: 5/14/2024</h3>
        <table>
            <tr>
                <th>USER ID</th>
                <th>NAME</th>
                <th>ACTION</th>
                <th>TIME</th>
                <th>DATE</th>
            </tr>
            <?php
            $servername = "localhost";
            $username = "your_username";
            $password = "your_password";
            $database = "your_database";

            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT user_id, name, action, time, date FROM audit WHERE date = '2024-05-14'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["action"] . "</td>";
                    echo "<td>" . $row["time"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No logs found for this day.</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>