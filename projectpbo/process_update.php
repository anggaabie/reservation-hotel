<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $check_in = isset($_POST['check_in']) ? $_POST['check_in'] : '';
    $check_out = isset($_POST['check_out']) ? $_POST['check_out'] : '';
    $fasilitas = isset($_POST['fasilitas']) ? $_POST['fasilitas'] : '';
    $total_price = isset($_POST['total_price']) ? $_POST['total_price'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Generate unique code for the booking
    // You can use a more sophisticated method for generating unique codes if needed
    $unique_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));

    // Update data booking
    $stmt = $conn->prepare("UPDATE booking SET username = ?, room = ?, check_in = ?, check_out = ?, fasilitas = ?, total_price = ?, unique_code = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssssssi", $username, $room, $check_in, $check_out, $fasilitas, $total_price, $unique_code, $status, $id);

    if ($stmt->execute()) {
        header("Location: admin.php?update=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>



