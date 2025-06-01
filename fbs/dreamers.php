<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    die("<script>alert('You need to login first! Redirecting to login page...'); window.location.href='login.php';</script>");
}


$name = $_SESSION['username'] ?? 'Guest';  
$email = $_SESSION['email'] ?? null;

if (!$email) {
    die("<script>alert('Error: Email not found in session! Please log in again.'); window.location.href='login.php';</script>");
}



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['court'], $_POST['date'], $_POST['time'], $_POST['payment_method'])) {
        die("<script>alert('All fields are required!'); window.location.href='index.php';</script>");
    }

    $court = $_POST['court'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, booking_date, booking_time, payment_status, court) VALUES (?, ?, ?, ?, ?)");
    $payment_status = ($payment_method === "Online Payment") ? "Pending" : "Paid";
    $stmt->bind_param("issss", $user_id, $date, $time, $payment_status, $court);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        if ($payment_method === "Online Payment") {
            header("Location: payment.php?booking_id=$booking_id");
            exit;
        } else {
            echo "<script>alert('Booking confirmed! Please pay at the venue.'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href='index.php';</script>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futsal Court Booking</title>
    <link rel="stylesheet" href="header.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="bookingstyles.css?v=<?php echo time(); ?>"> 

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dateInput = document.querySelector('input[name="date"]');
            const courtSelect = document.querySelector('select[name="court"]');
            const timeSelect = document.querySelector('select[name="time"]');
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            const bookingForm = document.querySelector('#bookingForm');

           
            let today = new Date().toISOString().split("T")[0];
            dateInput.setAttribute("min", today);

           
            function fetchTimeSlots() {
                const date = dateInput.value;
                const court = courtSelect.value;
                if (date && court) {
                    fetch(`fetch_times.php?date=${date}&court=${court}`)
                        .then(response => response.json())
                        .then(data => {
                            timeSelect.innerHTML = "<option value=''>Select a Time</option>";
                            data.forEach(slot => {
                                timeSelect.innerHTML += `<option value="${slot}">${slot}</option>`;
                            });
                        })
                        .catch(error => console.error("Error fetching time slots:", error));
                }
            }

            dateInput.addEventListener("change", fetchTimeSlots);
            courtSelect.addEventListener("change", fetchTimeSlots);

           
            paymentRadios.forEach(radio => {
                radio.addEventListener("change", function () {
                    document.getElementById("submit-btn").textContent = "Proceed to Payment";
                    if (this.value === "Cash") {
                        document.getElementById("submit-btn").textContent = "Confirm Booking";
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Dreamers Futsal </h1>
        <p>☎️Call us for the confirmation of booking.</p>
        <form id="bookingForm" action="booking.php" method="POST">
            <label>Name:</label>
            <input type="text" value="<?php echo htmlspecialchars($name); ?>" readonly>
            
            <label>Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            
            <label>Select Court:</label>
            <select name="court" required>
                <option value="">Choose Futsal Sides</option>
                <option value="Court 1">7A Side Futsal (Rs : 1500)</option>
                <option value="Court 2">5A Side Futsal (Rs : 1200)</option>
            </select>
            
            <label>Booking Date:</label>
            <input type="date" name="date" required>
            
            <label>Select Time:</label>
            <select name="time" required>
                <option value="">Select a Date & Court First</option>
            </select>

           
            <div class="payment-selection">
                <label class="payment-card">
                    <input type="radio" name="payment_method" value="Online Payment" required>
                    <div class="card-content">
                        <h3>Online Payment</h3>
                        <p>Pay via eSewa, Khalti, or IME Pay</p>
                    </div>
                </label>

                <label class="payment-card">
                    <input type="radio" name="payment_method" value="Cash" required>
                    <div class="card-content">
                        <h3>Cash</h3>
                        <p>Pay at the counter</p>
                    </div>
                </label>
            </div>

            <button type="submit" id="submit-btn">Select Payment</button>
        </form>
    </div>

    <script src="payment.js"></script>

    <?php include 'footer.php'; ?>
</body>
</html>
