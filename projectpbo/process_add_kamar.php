<?php
// Koneksi ke database
include 'koneksi.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $price = $_POST['price'];
    $facilities = $_POST['facilities'];
    $location = $_POST['location'];

    // Upload gambar
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    // Query untuk menyimpan data ke tabel rooms
    $sql = "INSERT INTO rooms (room_number, price, facilities, image, location) 
            VALUES ('$room_number', '$price', '$facilities', '$target_file', '$location')";

    if ($conn->query($sql) === TRUE) {
        // Menampilkan alert JavaScript setelah data berhasil ditambahkan
        echo "<script>
                alert('Kamar berhasil ditambahkan!');
                window.location.href='daftar_kamar.php'; // Ganti dengan halaman yang diinginkan
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi
$conn->close();
?>

