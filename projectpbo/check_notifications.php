<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi ke database

$response = ['hasNotification' => false]; // Default tidak ada notifikasi

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Cek apakah ada booking dengan status diterima untuk pengguna ini
    $stmt = $conn->prepare("SELECT COUNT(*) FROM booking WHERE username = ? AND status = 'Diterima' AND notification_seen = 0");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($notifCount);
    $stmt->fetch();
    
    if ($notifCount > 0) {
        $response['hasNotification'] = true; // Ada notifikasi
    }

    // Update notifikasi agar dianggap sudah dilihat (optional, jika ingin notifikasi hilang setelah ditampilkan)
    $updateStmt = $conn->prepare("UPDATE booking SET notification_seen = 1 WHERE username = ? AND status = 'Diterima'");
    $updateStmt->bind_param("s", $username);
    $updateStmt->execute();
    
    $stmt->close();
    $updateStmt->close();
}

$conn->close();

echo json_encode($response);
?>
