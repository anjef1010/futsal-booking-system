<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $court = $_POST['court'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $payment_method = $_POST['payment_method'];

    $stmt = $pdo->prepare("UPDATE bookings SET name = ?, email = ?, court = ?, date = ?, time = ?, payment_method = ? WHERE id = ?");
    $stmt->execute([$name, $email, $court, $date, $time, $payment_method, $id]);
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Booking</h1>
    <form method="POST" action="">
        <input type="text" name="name" value="<?php echo $booking['name']; ?>" required>
        <input type="email" name="email" value="<?php echo $booking['email']; ?>" required>
        <input type="text" name="court" value="<?php echo $booking['court']; ?>" required>
        <input type="date" name="date" value="<?php echo $booking['date']; ?>" required>
        <input type="time" name="time" value="<?php echo $booking['time']; ?>" required>
        <input type="text" name="payment_method" value="<?php echo $booking['payment_method']; ?>" required>
        <button type="submit">Update Booking</button>
    </form>
    <a href="dashboard.php">Cancel</a>
</body>
</html>