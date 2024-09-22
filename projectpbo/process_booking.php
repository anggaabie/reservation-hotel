<?php

include 'koneksi.php';

session_start();

// Fungsi untuk menghasilkan kode unik
function generateUniqueCode() {
    return strtoupper(bin2hex(random_bytes(6))); // Contoh: A1B2C3D4E5F6
}

// Memeriksa apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $check_in = isset($_POST['check_in']) ? $_POST['check_in'] : '';
    $check_out = isset($_POST['check_out']) ? $_POST['check_out'] : '';
    $fasilitas = isset($_POST['fasilitas']) ? $_POST['fasilitas'] : '';
    $total_price = isset($_POST['total_price']) ? $_POST['total_price'] : '';

    // Menghapus format harga dan memastikan hanya angka yang tersisa
    $total_price = filter_var($total_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Cek ketersediaan kamar
    $stmt = $conn->prepare("SELECT check_in, check_out FROM booking WHERE room = ? AND ((check_in BETWEEN ? AND ?) OR (check_out BETWEEN ? AND ?) OR (? BETWEEN check_in AND check_out) OR (? BETWEEN check_in AND check_out))");
    $stmt->bind_param("sssssss", $room, $check_in, $check_out, $check_in, $check_out, $check_in, $check_out);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Jika kamar tidak tersedia, ambil semua booking untuk kamar ini
        $stmt_all_bookings = $conn->prepare("SELECT username, check_in, check_out FROM booking WHERE room = ?");
        $stmt_all_bookings->bind_param("s", $room);
        $stmt_all_bookings->execute();
        $stmt_all_bookings->bind_result($booked_username, $booked_check_in, $booked_check_out);

        $all_bookings = [];
        while ($stmt_all_bookings->fetch()) {
            $all_bookings[] = [
                'username' => $booked_username,
                'check_in' => $booked_check_in,
                'check_out' => $booked_check_out
            ];
        }

        // Contoh query untuk mengambil status booking setelah proses booking
        $bookingQuery = "SELECT status FROM booking WHERE username = ? ORDER BY waktu_booking DESC LIMIT 1";
        $stmt = $conn->prepare($bookingQuery); // Ganti $pdo dengan $conn
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $latestBooking = $stmt->get_result()->fetch_assoc(); // Mengambil hasil menggunakan get_result()
        
        if ($latestBooking && $latestBooking['status'] === 'Diterima') {
            $_SESSION['booking_status'] = 'Diterima';
        }


        // Tampilkan pesan dengan semua booking yang sudah ada
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Booking Failed</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f0f0f0;
                    color: #333;
                    text-align: center;
                    padding: 50px;
                }
                .message-box {
            margin: 20px;
            padding: 20px;
            border: 1px solid red;
            background-color: #f8d7da;
            color: #721c24;
        }
                .message-box h1 {
                    font-size: 24px;
                    color: #FF0000;
                    margin-bottom: 20px;
                }
                .message-box p {
                    font-size: 16px;
                    margin-bottom: 20px;
                }
                .message-box ul {
                    list-style: none;
                    padding: 0;
                }
                .message-box li {
                    font-size: 14px;
                    margin-bottom: 5px;
                }
                .message-box a {
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #fff;
                    background-color: #FF0000;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .message-box a:hover {
                    background-color: #E60000;
                }
            </style>
            <script>
             // Simpan flag di localStorage untuk menampilkan notifikasi
            localStorage.setItem('newBooking', 'true');
            </script>
        </head>
        <body>
            <div class='message-box'>
                <h1>KAMAR SUDAH TER-BOOKING</h1>
                <p>Kamar ini sudah dipesan silahkan pilih tanggal lain. Berikut adalah detail tanggal yang Sudah dibooking:</p>
                <ul>";
        foreach ($all_bookings as $booking) {
            echo "<li><strong> - Check-in: " . date('d-m-Y', strtotime($booking['check_in'])) . ", Check-out: " . date('d-m-Y', strtotime($booking['check_out'])) . "</strong></li>";
        }
        echo "  </ul>
                <a href='index.php'>Back to Home</a>
            </div>
        </body>
        </html>";
    } else {
        // Generate unique code
        $unique_code = generateUniqueCode();

        // Memproses data dan menyimpan ke database jika kamar tersedia
        $stmt = $conn->prepare("INSERT INTO booking (username, room, check_in, check_out, fasilitas, total_price, unique_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $username, $room, $check_in, $check_out, $fasilitas, $total_price, $unique_code);

        if ($stmt->execute()) {
            // Menampilkan pesan sukses
            echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Booking Success</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f0f0f0;
                        color: #333;
                        text-align: center;
                        padding: 50px;
                    }
                    .message-box {
                        display: inline-block;
                        padding: 20px;
                        margin: 20px auto;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    .message-box h1 {
                        font-size: 24px;
                        color: #4CAF50;
                        margin-bottom: 20px;
                    }
                    .message-box p {
                        font-size: 16px;
                        margin-bottom: 20px;
                    }
                    .message-box a {
                        display: inline-block;
                        padding: 10px 20px;
                        font-size: 16px;
                        color: #fff;
                        background-color: #4CAF50;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .message-box a:hover {
                        background-color: #45a049;
                    }
                </style>
            </head>
            <body>
                <div class='message-box'>
                    <h1>Booking Successful!</h1>
                    <p>Silakan periksa history Anda untuk melihat prosesnya. Kode booking anda: <strong>$unique_code</strong></p>
                    <a href='index.php'>Back to Home</a>
                </div>
            </body>
            </html>";
        
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();

    // Pastikan $stmt_all_bookings hanya ditutup jika didefinisikan
    if (isset($stmt_all_bookings)) {
        $stmt_all_bookings->close();
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
?>

