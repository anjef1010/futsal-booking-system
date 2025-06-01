<?php
session_start();


$dsn = "mysql:host=localhost;dbname=registration;charset=utf8mb4";
$username = "root";
$password = "";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];
$conn = new PDO($dsn, $username, $password, $options);


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];


$query = $conn->prepare("SELECT first_name, last_name, email, profile_pic FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $password = $_POST['password'];

   
    $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?")
         ->execute([$first_name, $last_name, $user_id]);

    
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn->prepare("UPDATE users SET password = ? WHERE id = ?")
             ->execute([$hashed_password, $user_id]);
    }

   
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES['profile_pic']['name']);
        $target_file = $target_dir . uniqid() . "_" . $file_name; 

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?")
                 ->execute([$target_file, $user_id]);
        }
    }

    $_SESSION['message'] = "Profile updated successfully!";
    header('Location: account.php');
    exit();
}

$bookings = $conn->prepare("SELECT booking_date, booking_time, court, payment_status FROM bookings WHERE user_id = ? ORDER BY booking_date DESC, booking_time DESC");
$bookings->execute([$user_id]);


if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Settings</title>  
    <link rel="stylesheet" href="header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="account.css?v=<?php echo time(); ?>">
</head>
<body>
<?php include 'header.php'; ?>
    <div class="container">
        <h2>Account Settings</h2>

        <!-- Display profile picture -->
        <?php if (!empty($user['profile_pic'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="profile-pic">
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

            <label>New Password (optional):</label>
            <input type="password" name="password">

            <label>Profile Picture:</label>
            <input type="file" name="profile_pic">

            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <!-- Booking Dashboard Table -->
        <h3>Your Futsal Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Court</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookings->fetch()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                        <td><?php echo htmlspecialchars($booking['court']); ?></td>
                        <td><?php echo htmlspecialchars($booking['payment_status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="account.php?logout=true" class="logout">Logout</a>
    </div>
</body>
</html>
