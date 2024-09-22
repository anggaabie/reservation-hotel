<?php
include 'koneksi.php';

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil data dari formulir
$nama = $_POST['nama'];
$email = $_POST['email'];
$pesan = $_POST['pesan'];

// Menyiapkan dan mengeksekusi pernyataan SQL
$stmt = $conn->prepare("INSERT INTO help (nama, email, pesan) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $email, $pesan);

if ($stmt->execute()) {
    echo "Pesan berhasil dikirim!";
} else {
    echo "Error: " . $stmt->error;
}

// Menutup koneksi
$stmt->close();
$conn->close();
?>
