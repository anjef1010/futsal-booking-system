<?php
session_start();
require 'PHPMailer/PHPMailerAutoload.php'; 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $court = $_POST['court'];
    $payment_status = "Paid (Cash)";

    
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, booking_date, booking_time, payment_status, court) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $date, $time, $payment_status, $court);
    if ($stmt->execute()) {
        sendConfirmationEmail($email, $name, $date, $time, $court);
        echo "<script>alert('Booking confirmed! Check your email.'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
$conn->close();


function sendConfirmationEmail($email, $name, $date, $time, $court) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com'; 
    $mail->Password = 'your_email_password'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('futsal@gmail.com', 'Futsal Booking');
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Futsal Booking Confirmation';
    $mail->Body    = "<h3>Hello $name,</h3>
                      <p>Your booking is confirmed.</p>
                      <p><b>Date:</b> $date</p>
                      <p><b>Time:</b> $time</p>
                      <p><b>Court:</b> $court</p>
                      <p>Please pay at the futsal Counter.Thank you for choosing us!</p>";

    if (!$mail->send()) {
        echo "<script>alert('Email could not be sent.');</script>";
    }
}
?>
