<?php
// process_create.php

// Koneksi ke database
include 'koneksi.php';

// Ambil data dari form
$username = $_POST['username'];
$room = $_POST['room'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$fasilitas = $_POST['fasilitas'];
$total_price = $_POST['total_price'];
$status = $_POST['status']; // Status default adalah 'Menunggu'

// Validasi dan sanitasi input
$username = htmlspecialchars($username);
$room = htmlspecialchars($room);
$check_in = htmlspecialchars($check_in);
$check_out = htmlspecialchars($check_out);
$fasilitas = htmlspecialchars($fasilitas);
$total_price = htmlspecialchars($total_price);

// Menghasilkan kode booking unik
$unique_code = strtoupper(substr(uniqid('ALex', true), 0, 10));

// Query untuk menyimpan data booking
$sql = "INSERT INTO booking (username, room, check_in, check_out, fasilitas, total_price, status, unique_code) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $username, $room, $check_in, $check_out, $fasilitas, $total_price, $status, $unique_code);

if ($stmt->execute()) {
    echo "Booking berhasil ditambahkan dengan kode booking: $unique_code.";
    header("Location: admin.php"); // Redirect ke halaman admin setelah berhasil
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
