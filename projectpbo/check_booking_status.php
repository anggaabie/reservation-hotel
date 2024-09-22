<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi ke database ada

if (isset($_SESSION['booking_id'])) {
    $booking_id = $_SESSION['booking_id'];
    error_log("Booking ID: " . $booking_id); // Log untuk debug

    // Ambil status booking dari database berdasarkan id
    $stmt = $conn->prepare("SELECT status FROM booking WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            $stmt->bind_result($status);
            $stmt->fetch();
            error_log("Status: " . $status); // Log status untuk debug
            
            echo json_encode(['status' => $status]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else {
    echo json_encode(['status' => 'Menunggu']);
}

$conn->close();
?>


