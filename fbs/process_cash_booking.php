<?php
session_start();
include 'db_connection.php';


$name = $_POST['name'];
$email = $_POST['email'];
$court = $_POST['court'];
$date = $_POST['date'];
$time = $_POST['time'];
$payment_method = $_POST['payment_method'];


$sql = "INSERT INTO bookings (name, email, court, date, time, payment_method) 
        VALUES ('$name', '$email', '$court', '$date', '$time', '$payment_method')";
mysqli_query($conn, $sql);


$booking_id = mysqli_insert_id($conn);


if ($payment_method === "Online Pay") {
    header("Location: payment.php?booking_id=$booking_id"); 
    exit;
} else {
    
    $subject = "Booking Confirmation - Futsal Court";
    $message = "Dear $name,\n\nYour booking for $court on $date at $time has been confirmed. Please pay at the counter.\n\nThank you!";
    $headers = "From: noreply@futsalbooking.com";

    mail($email, $subject, $message, $headers);


    echo "<script>alert('Booking confirmed! A confirmation email has been sent. Please pay at the venue.'); window.location.href='index.php';</script>";
}
?>
