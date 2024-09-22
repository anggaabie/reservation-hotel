<?php
// Include file koneksi untuk menghubungkan dengan database
include 'koneksi.php';

// Query untuk mengambil nama dari tabel `user`
$query = "SELECT nama FROM user";

// Eksekusi query
$result = mysqli_query($conn, $query);

// Cek apakah ada data yang diambil
if (mysqli_num_rows($result) > 0) {
    // Loop untuk menampilkan semua nama
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<p>Nama: " . $row['nama'] . "</p>";
    }
} else {
    echo "Tidak ada data ditemukan";
}

// Menutup koneksi
mysqli_close($koneksi);
?>
