<?php
session_start();
include 'server.php'; // Database connection

// Check if user is logged in before outputting anything
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT username, email, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();

include 'header.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $target_dir = "uploads/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);

    
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $sql_update_pic = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_pic);
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
        
        
        header("Location: profile.php");
        exit();
    } else {
        echo "<script>alert('File upload failed! Check folder permissions.');</script>";
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancel_booking"])) {
    $booking_id = $_POST["cancel_booking"];
    $sql_cancel = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql_cancel);
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    header("Location: profile.php");
    exit();
}


$sql_bookings = "SELECT id, booking_date, booking_time, court, payment_status FROM bookings WHERE user_id = ? ORDER BY booking_date DESC";
$stmt = $conn->prepare($sql_bookings);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<div class="container mt-5">
    <div class="card p-4 text-center">
        <h3><?php echo htmlspecialchars($user['username']); ?>'s Profile</h3>
        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" class="profile-img" alt="Profile Picture">
        
        
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="profile_pic" class="form-control mt-2 mb-2" required>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <p class="mt-3"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    
    <div class="table-container mt-4">
        <h4 class="text-center">Booking History</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Court</th>
                    <th>Payment</th>
                    <th>Cancel</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['booking_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['court']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        <td>
                            <?php if ($row['booking_date'] > date("Y-m-d")): ?>
                                <form action="profile.php" method="POST">
                                    <input type="hidden" name="cancel_booking" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="cancel-btn btn btn-danger btn-sm">Cancel</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
