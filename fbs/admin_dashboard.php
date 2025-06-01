<?php
session_start();
include("server.php"); // Database connection

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Function to execute queries safely
function fetchSingleValue($conn, $query, $default = 0) {
    $result = $conn->query($query);
    if (!$result) {
        die("SQL Error: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    return $row ? array_values($row)[0] : $default;
}

// Fetch summary data with error handling
$total_users = fetchSingleValue($conn, "SELECT COUNT(*) as count FROM users");
$total_bookings = fetchSingleValue($conn, "SELECT COUNT(*) as count FROM bookings");
$total_payments = fetchSingleValue($conn, "SELECT COUNT(*) as count FROM bookings WHERE payment_status = 'Completed'");

// Fetch all users safely
$users = $conn->query("SELECT * FROM users") or die("SQL Error (Users): " . $conn->error);

// Fetch all bookings safely
$bookings = $conn->query("SELECT * FROM bookings ORDER BY booking_date DESC") or die("SQL Error (Bookings): " . $conn->error);

// Fetch recent activity log safely
$activity_log = $conn->query("SELECT * FROM activity_log ORDER BY timestamp DESC LIMIT 10") or die("SQL Error (Activity Log): " . $conn->error);

// AI-Based Booking Insights: Predict best booking hours based on past data
$result = $conn->query("SELECT booking_time, COUNT(*) as count FROM bookings GROUP BY booking_time ORDER BY count DESC LIMIT 1");
$best_booking_time = ($result && $row = $result->fetch_assoc()) ? $row['booking_time'] : 'Not enough data';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<header class="minimal-header">
    <h2>Admin Dashboard</h2>
    <span>Let's Play Futsal</span> 
    <nav>
        <a href="#">Home</a>
        <a href="#users">Users</a>
        <a href="#bookings">Bookings</a>
        <a href="#reports">Reports</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>



    
    <div class="container">
        <div class="stats">
            <div class="card">Total Users: <?php echo $total_users; ?></div>
            <div class="card">Total Bookings: <?php echo $total_bookings; ?></div>
            <div class="card">Total Completed Payments: <?php echo $total_payments; ?></div>
        </div>

        <h3>AI-Based Booking Insights</h3>
        <p>Recommended best booking time: <strong><?php echo $best_booking_time; ?></strong></p>
        
        <h3>Recent Admin Activity</h3>
        <ul>
            <?php while ($log = $activity_log->fetch_assoc()): ?>
                <li><?php echo "[" . htmlspecialchars($log['timestamp']) . "] " . htmlspecialchars($log['action']); ?></li>
            <?php endwhile; ?>
        </ul>

        <h3 id="users">Users Management</h3>
        
        <input type="checkbox" onclick="toggleAllCheckboxes(this)"> Select All
        
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for names..">
        <table>
            <tr>
                <th></th> 
                <th>ID</th><th>First Name</th><th>Last Name</th><th>Username</th><th>Role</th><th>Actions</th>
            </tr>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" class="bulk-checkbox" value="<?php echo $user['id']; ?>"></td>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3 id="bookings">Bookings Management</h3>
        
        <input type="checkbox" onclick="toggleAllCheckboxes(this)"> Select All
        
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for bookings..">
        <table>
            <tr>
                <th></th> 
                <th>ID</th><th>User ID</th><th>Booking Date</th><th>Booking Time</th><th>Court</th><th>Payment Status</th><th>Actions</th>
            </tr>
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" class="bulk-checkbox" value="<?php echo $booking['id']; ?>"></td>
                    <td><?php echo $booking['id']; ?></td>
                    <td><?php echo $booking['user_id']; ?></td>
                    <td><?php echo $booking['booking_date']; ?></td>
                    <td><?php echo $booking['booking_time']; ?></td>
                    <td><?php echo $booking['court']; ?></td>
                    <td><?php echo $booking['payment_status']; ?></td>
                    <td>
                        <a href="edit_booking.php?id=<?php echo $booking['id']; ?>">Edit</a> |
                        <a href="delete_booking.php?id=<?php echo $booking['id']; ?>" onclick="return confirm('Are you sure?')">Cancel</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3>User Activity Timeline</h3>
        <div class="timeline">
          
        </div>

        <h3>Admin Notifications</h3>
        <div id="notifications">
            <p>No new notifications</p>
        </div>

        
        <canvas id="userStatsChart" width="400" height="200"></canvas>
    </div>

    <script>
    var ctx = document.getElementById('userStatsChart').getContext('2d');
    var userStatsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Users', 'Bookings', 'Payments'],
            datasets: [{
                label: '# of Entries',
                data: [<?php echo $total_users; ?>, <?php echo $total_bookings; ?>, <?php echo $total_payments; ?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            tr[i].style.display = "none";
            td = tr[i].getElementsByTagName("td");
            for (var j = 0; j < td.length; j++) {
                if (td[j]) {
                    if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    }
                }
            }
        }
    }

    function fetchNotifications() {
        
        fetch('fetch_notifications.php')
            .then(response => response.json())
            .then(data => {
                let notificationsDiv = document.getElementById('notifications');
                notificationsDiv.innerHTML = '';
                data.forEach(notification => {
                    let p = document.createElement('p');
                    p.textContent = notification;
                    notificationsDiv.appendChild(p);
                });
            });
    }

    fetchNotifications();

    function toggleDarkMode() {
        var element = document.body;
        element.classList.toggle("dark-mode");
    }

    function fetchUserActivities() {
        
        fetch('fetch_user_activities.php')
            .then(response => response.json())
            .then(data => {
                let timelineDiv = document.querySelector('.timeline');
                timelineDiv.innerHTML = '';
                data.forEach(activity => {
                    let div = document.createElement('div');
                    div.className = 'timeline-item';
                    div.innerHTML = `<span>${activity.timestamp}</span>: ${activity.action}`;
                    timelineDiv.appendChild(div);
                });
            });
    }

    
    fetchUserActivities();
    function toggleAllCheckboxes(source) {
        checkboxes = document.getElementsByClassName('bulk-checkbox');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function performBulkAction() {
        var selected = [];
        checkboxes = document.getElementsByClassName('bulk-checkbox');
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selected.push(checkboxes[i].value); 
            }
        }
       
        console.log('Selected IDs for bulk action:', selected);
    }
    </script>
</body>
</html>
