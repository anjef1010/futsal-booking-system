<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("db_connect.php");


$user_photo = "uploads/default_profile.png"; 
$username = "Guest";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];


    $stmt = $conn->prepare("SELECT profile_pic, username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_pic, $username);
    $stmt->fetch();
    $stmt->close();

   
    if ($profile_pic && !empty($profile_pic)) {
        
        if (strpos($profile_pic, 'uploads/') === false) {
            $image_path = "uploads/" . htmlspecialchars($profile_pic); 
        } else {
            $image_path = htmlspecialchars($profile_pic); 
        }

        
        if (file_exists($image_path)) {
            $user_photo = $image_path;  
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let's Play Futsal</title>
    <link rel="stylesheet" href="header.css?v=1.1">
</head>
<body>


<div id="sidebar" class="sidebar">
    <button class="close-btn" onclick="closeNav()">×</button>
    <a href="index.php#hero">Home</a>
    <a href="index.php#booking">Book Now</a>
    <a href="index.php#contact">Contact Us</a>
    <a href="index.php#about">About Us</a>
</div>


<div class="navbar">
    <button class="menu-btn" onclick="openNav()">☰</button>
    <a href="index.php" class="logo">Let's Play Futsal</a>

    <div class="dropdown">
        <button class="dropbtn" onclick="toggleDropdown(event)">
            <img src="<?php echo $user_photo; ?>" alt="User" class="user-img" style="width: 40px; height: 40px; border-radius: 50%;">
            <?php echo htmlspecialchars($username); ?>
        </button>
        <div class="dropdown-content" id="userDropdown">
            <a href="account.php">Account</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.querySelector(".menu-btn");
    const sidebar = document.getElementById("sidebar");
    const closeBtn = document.querySelector(".close-btn");

    menuBtn.addEventListener("click", function () {
        sidebar.classList.toggle("active");
    });

    closeBtn.addEventListener("click", function () {
        sidebar.classList.remove("active");
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const dropBtn = document.querySelector(".dropbtn");
    const dropdownContent = document.querySelector(".dropdown-content");

    dropBtn.addEventListener("click", function (event) {
        event.stopPropagation();
        dropdownContent.classList.toggle("active");
    });

 
    document.addEventListener("click", function (event) {
        if (!dropBtn.contains(event.target) && !dropdownContent.contains(event.target)) {
            dropdownContent.classList.remove("active");
        }
    });
});

</script>

</body>
</html>
