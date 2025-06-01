<?php 
session_start(); 

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futsal Booking</title>
    <link rel="stylesheet" href="header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <section id="hero">
            <h2>Book Your Time in Futsal Today!</h2>
            <p>Join us for an exciting futsal experience.</p>
            <a href="#booking" class="btn">Book Now </a>
        </section>

        <section id="booking">
            <h2>Book Futsal</h2>
            <div class="booking">
                <h3>-----AVAILABLE FUTSALS-----</h3>
                <p>These are the available futsal venues on our website. Book your time and ground as per your preference.</p>
            </div>
        </section>

        <section class="container">
            <div class="card">
                <div class="card-image futsal-1"></div>
                <h2>Matshya Narayan Recreational Center</h2>
                <p>One of the Best Futsal Venue in Kathmandu is now OPEN! ⚽ ☎️ 9851324215 - Call/Message us to confirm your booking now. Come & play to your hearts fullest</p>
                <a href="<?php echo isset($_SESSION['username']) ? 'matshyabooking.php' : 'login.php'; ?>">BOOK NOW</a>
            </div>
            <div class="card">
                <div class="card-image futsal-2"></div>
                <h2>Dreamers Futsal Arena</h2>
                <p>Open Arena futsal - Big size 5A side.
                Be Ready to show,develop and exercise your skills.
Open Arena futsal .....
Big size 5A side Dreamer's Futsal Arena 
We are Ready to welcome all futsal lovers..
                </p>
                <a href="<?php echo isset($_SESSION['username']) ? 'dreamers.php' : 'login.php'; ?>">BOOK NOW</a>
            </div>
            <div class="card">
                <div class="card-image futsal-3"></div>
                <h2>Kirtipur Futsal</h2>
                <p>Kirtipur Futsal. Location: Kirtipur, Kathmandu. Contact: 9818149835. </p> 
                <a href="kirtipur.php">BOOK NOW</a>
            </div>
            <div class="card">
                <div class="card-image futsal-4"></div>
                <h2>Elite Futsal (Sports zone)</h2>
                <p>⚽ Our state-of-the-art facilities and top-notch organization promise an unparalleled 5-A-side Futsal experience like no other
                For inquiry: 9840737438, 9842449352
                </p> 
                <a href="Elite.php">BOOK NOW</a>
            </div>
            <div class="card">
                <div class="card-image futsal-5"></div>
                <h2>Chandragiri Futsal</h2>
                <p>Play More: Get More! 🎉
                    Now each team gets a free ticket in a game! 🎁
                    Submission of 20 tickets will make a free game from now on! 😍
                    One of the Best Futsal Venue in Kathmandu is now OPEN! 👟 ⚽ 💓
                    ☎️ 9851324215, 9851336838 - Call/Message us to confirm your booking now.</p> 
                <a href="chandragiri.php">BOOK NOW</a>
            </div>
            <div class="card">
                <div class="card-image futsal-6"></div>
                <h2>DIG Futsal</h2>
                <p>Sports ground Dig Futsal at Bagmati, Kathmandu District, Chandragiri Municipality, ☎️ +977 9866038268
                    
                </p> 
                <a href="dig.php">BOOK NOW</a>

            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
