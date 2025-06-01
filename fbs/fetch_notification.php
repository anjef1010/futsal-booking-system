<?php
include("server.php"); 


$query = "SELECT message FROM notifications ORDER BY timestamp DESC LIMIT 10";
$result = $conn->query($query);

$notifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row['message'];
    }
}

echo json_encode($notifications);
?>
