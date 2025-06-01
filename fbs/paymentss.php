<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("<script>alert('You need to login first!'); window.location.href='login.php';</script>");
}

$name = $_SESSION['username'];
$email = $_SESSION['email'];
$booking_id = $_GET['booking_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="payment.css">
    <script>
        function showPaymentDetails(method) {
            document.getElementById('card-details').style.display = (method === 'Card') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    
    <div class="container">
        <h2>Payment Options</h2>
        <p>Please choose your payment option below.</p>
        
        <form action="process_payment.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
            
            <label>Name:</label>
            <input type="text" value="<?php echo htmlspecialchars($name); ?>" readonly>
            
            <label>Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            
            <label>Payment Method:</label>
            <div class="payment-options">
                <label>
                    <input type="radio" name="payment_method" value="Esewa" required onclick="showPaymentDetails('Esewa')">
                    <img src="icon/esewa.png" alt="Esewa">
                </label>
                <label>
                    <input type="radio" name="payment_method" value="Khalti" required onclick="showPaymentDetails('Khalti')">
                    <img src="icon/khalti.png" alt="Khalti">
                </label>
                <label>
                    <input type="radio" name="payment_method" value="IME Pay" required onclick="showPaymentDetails('IME Pay')">
                    <img src="icon/imepay.png" alt="IME Pay">
                </label>
                <label>
                    <input type="radio" name="payment_method" value="Card" required onclick="showPaymentDetails('Card')">
                    <img src="icon/creditcard.png" alt="Credit/Debit Card">
                </label>
            </div>
            
            <div id="card-details" style="display: none;">
                <h3>Payment by Credit/Debit Card</h3>
                <label>Card Number:</label>
                <input type="text" name="card_number" maxlength="16" placeholder="1234 5678 9012 3456">
                
                <label>Expiration Date:</label>
                <select name="exp_month">
                    <option>Month</option>
                    <option>01</option>
                    <option>02</option>
                    <option>03</option>
                    <option>04</option>
                    <option>05</option>
                    <option>06</option>
                    <option>07</option>
                    <option>08</option>
                    <option>09</option>
                    <option>10</option>
                    <option>11</option>
                    <option>12</option>
                </select>
                <select name="exp_year">
                    <option>Year</option>
                    <?php for ($i = date('Y'); $i <= date('Y') + 10; $i++) { ?>
                        <option><?php echo $i; ?></option>
                    <?php } ?>
                </select>

                <label>Security Code:</label>
                <input type="text" name="cvv" maxlength="3" placeholder="CVV">
                
                <label>Cardholder Name:</label>
                <input type="text" name="cardholder_name" placeholder="Full Name">
            </div>
            
            <button type="submit">Proceed to Pay</button>
        </form>
    </div>
</body>
</html>
