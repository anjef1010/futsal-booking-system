<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $court = $_POST['court'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $payment_method = $_POST['payment_method'];

    $stmt = $conn->prepare("INSERT INTO bookings (name, email, court, date, time, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $court, $date, $time, $payment_method);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error adding booking: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Booking</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add New Booking</h1>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="court" placeholder="Court" required>
        <input type="date" name="date" required>
        <input type="time" name="time" required>
        <input type="text" name="payment_method" placeholder="Payment Method" required>
        <button type="submit">Add Booking</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>