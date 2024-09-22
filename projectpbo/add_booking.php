<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $room = $_POST['room'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $fasilitas = $_POST['fasilitas'];
    $total_price = $_POST['total_price'];
    $unique_code = uniqid('GABS_'); // Membuat kode booking unik
    $status = 'Menunggu';

    $stmt = $conn->prepare("INSERT INTO booking (username, room, check_in, check_out, fasilitas, total_price, unique_code, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssddss", $username, $room, $check_in, $check_out, $fasilitas, $total_price, $unique_code, $status);
    
    // Eksekusi query
    if ($stmt->execute()) {
        // Redirect ke halaman admin setelah sukses
        header("Location: admin.php");
        exit(); // Pastikan script berhenti setelah redirect
    } else {
        echo "Error adding booking: " . $stmt->error;
    }
    
    // Tutup statement
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Booking</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="date"],
        form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form input[type="submit"] {
            background-color: #808588;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #666;
        }
    </style>
</head>
<body>
    <header>
        <!-- Header sama seperti sebelumnya -->
    </header>
    <div class="sidebar">
        <!-- Sidebar sama seperti sebelumnya -->
    </div>
    <main>
        <h1>Tambah Booking</h1>
        <form method="POST" action="add_booking.php">
            <label for="username">Nama:</label>
            <input type="text" id="username" name="username" required><br>
            
            <label for="room">Kamar:</label>
            <input type="text" id="room" name="room" required><br>
            
            <label for="check_in">Check-in:</label>
            <input type="date" id="check_in" name="check_in" required><br>
            
            <label for="check_out">Check-out:</label>
            <input type="date" id="check_out" name="check_out" required><br>
            
            <label for="fasilitas">Fasilitas:</label>
            <input type="text" id="fasilitas" name="fasilitas"><br>
            
            <label for="total_price">Total Harga:</label>
            <input type="number" id="total_price" name="total_price" step="0.01" required><br>
            
            
            <input type="submit" value="Tambah Booking">
        </form>
    </main>
    <footer>
        <!-- Footer sama seperti sebelumnya -->
    </footer>
</body>
</html>
