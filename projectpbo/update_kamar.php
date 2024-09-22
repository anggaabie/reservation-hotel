<?php
// Koneksi ke database
include 'koneksi.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Memeriksa apakah form telah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $room_number = $_POST['room_number'];
    $price = $_POST['price'];
    $facilities = $_POST['facilities'];
    $location = $_POST['location'];
    
    // Proses upload gambar jika ada file baru
    if (!empty($_FILES['image']['name'])) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);

        // Update dengan gambar
        $sql = "UPDATE rooms SET room_number='$room_number', price='$price', facilities='$facilities', location='$location', image='$image' WHERE id='$id'";
    } else {
        // Update tanpa gambar
        $sql = "UPDATE rooms SET room_number='$room_number', price='$price', facilities='$facilities', location='$location' WHERE id='$id'";
    }

    if ($conn->query($sql) === TRUE) {
        // Redirect kembali ke halaman daftar_kamar.php dengan pesan sukses
        echo "<script>
            alert('Kamar berhasil diupdate!');
            window.location.href = 'daftar_kamar.php';
        </script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
