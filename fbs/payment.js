document.addEventListener("DOMContentLoaded", function () {
    function selectPayment(method) {
        document.querySelectorAll(".payment-card").forEach(card => {
            card.classList.remove("selected");
        });

        let selectedCard = method === "Online Payment" ? document.getElementById("online-payment") : document.getElementById("cash-payment");
        selectedCard.classList.add("selected");

        let btn = document.getElementById("submit-btn");
        btn.innerText = method === "Online Payment" ? "Continue" : "Book";

        if (method === "Online Payment") {
            document.getElementById("bookingForm").action = "payment.php";
        } else {
            document.getElementById("bookingForm").action = "booking.php"; 
        }
    }

    document.querySelectorAll(".payment-card").forEach(card => {
        card.addEventListener("click", function () {
            let method = this.querySelector("input").value;
            selectPayment(method);
        });
    });
});
