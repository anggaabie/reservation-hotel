<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: register.php");
    exit();
}

$username = $_SESSION['username']; // Ambil username dari session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <style>
        /* Style umum untuk reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Menambahkan flexbox pada main */
        main {
            display: flex;
            justify-content: center; /* Membuat form berada di tengah secara horizontal */
            align-items: center; /* Membuat form berada di tengah secara vertikal jika halaman lebih tinggi dari form */
            min-height: 100vh; /* Menggunakan seluruh tinggi viewport */
            padding: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            max-width: 600px;
            width: 100%; /* Menambah lebar penuh */
            margin: auto;
        }

        label {
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="date"], input[type="number"], select {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .button-group {
            display: flex;
            justify-content: space-between; /* Menyusun tombol secara horizontal */
            gap: 10px;
        }

        button {
            background-color: #333;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <main>
        <form id="bookingForm" action="process_booking.php" method="POST">
            <h1 style="text-align: center;">Booking</h1>
            <label for="name">Nama:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="room">Room:</label>
            <input type="text" id="room" name="room" readonly>

            <label for="checkin">Check-in Date:</label>
            <input type="date" id="checkin" name="check_in" required>

            <label for="checkout">Check-out Date:</label>
            <input type="date" id="checkout" name="check_out" required>

            <label for="fasilitas">Fasilitas:</label>
            <input type="text" id="fasilitas" name="fasilitas" readonly>

            <label for="total_price">Total Price:</label>
            <input type="text" id="total_price" name="total_price" readonly>

            <!-- Menambahkan div untuk kelompok tombol -->
            <div class="button-group">
                <button type="button" onclick="window.location.href='index.php';">Kembali ke Home</button>
                <button type="submit">Booking!</button>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const room = urlParams.get('room');
            const pricePerDay = parseInt(urlParams.get('price'), 10);
            const fasilitas = urlParams.get('fasilitas');

            if (room && pricePerDay) {
                document.getElementById('room').value = room;
                document.getElementById('fasilitas').value = fasilitas ? fasilitas : '';

                const checkinInput = document.getElementById('checkin');
                const checkoutInput = document.getElementById('checkout');

                function calculateTotalPrice() {
                    const checkinDate = new Date(checkinInput.value);
                    const checkoutDate = new Date(checkoutInput.value);
                    if (checkinDate && checkoutDate && checkinDate < checkoutDate) {
                        const days = (checkoutDate - checkinDate) / (1000 * 60 * 60 * 24);
                        const totalPrice = days * pricePerDay;
                        document.getElementById('total_price').value = Math.floor(totalPrice);
                    } else {
                        document.getElementById('total_price').value = '';
                    }
                }

                checkinInput.addEventListener('change', calculateTotalPrice);
                checkoutInput.addEventListener('change', calculateTotalPrice);
            }
        });
    </script>
</body>
</html>