<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_GET['date']) || !isset($_GET['court'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

$date = $_GET['date'];
$court = $_GET['court'];

function getAvailableTimeSlots($date, $court, $conn) {
    $bookedSlots = [];
    $query = "SELECT booking_time FROM bookings WHERE booking_date = ? AND court = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $date, $court);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        
        $bookedStartTime = explode(" - ", $row['booking_time'])[0];
        $bookedSlots[] = $bookedStartTime;
    }
    $stmt->close();

    $availableSlots = [];
    $startTime = new DateTime('07:00');
    $endTime = new DateTime('22:00');

    while ($startTime < $endTime) {
        $start = $startTime->format('H:i');
        $end = $startTime->modify('+1 hour')->format('H:i');
        $timeSlot = "$start - $end";

        if (!in_array($start, $bookedSlots)) {
            $availableSlots[] = $timeSlot;
        }
    }

    return $availableSlots;
}

$availableSlots = getAvailableTimeSlots($date, $court, $conn);
echo json_encode($availableSlots);

$conn->close();
?>
