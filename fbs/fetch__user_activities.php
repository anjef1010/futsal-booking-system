<?php
include("server.php"); 


$query = "SELECT timestamp, action FROM user_activities ORDER BY timestamp DESC LIMIT 10";
$result = $conn->query($query);

$activities = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
}

echo json_encode($activities);
?>
