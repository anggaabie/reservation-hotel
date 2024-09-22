<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Memperbarui status reservasi
    $stmt = $conn->prepare("UPDATE booking SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        // Redirect ke halaman manajemen reservasi setelah berhasil
        header("Location: manage_bookings.php?message=Status%20successfully%20updated");
        exit(); // Pastikan script berhenti setelah redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM booking WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Reservasi</title>
</head>
<body>
    <h1>Update Status Reservasi</h1>
    <?php if (isset($booking)): ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $booking['username']; ?>" readonly><br>
        <label for="room">Room:</label>
        <input type="text" id="room" name="room" value="<?php echo $booking['room']; ?>" readonly><br>
        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Pending" <?php echo $booking['status'] == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
            <option value="Diterima" <?php echo $booking['status'] == 'Diterima' ? 'selected' : ''; ?>>Diterima</option>
            <option value="Ditolak" <?php echo $booking['status'] == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
        </select><br>
        <input type="submit" value="Update Status">
    </form>
    <?php else: ?>
    <p>Reservasi tidak ditemukan.</p>
    <?php endif; ?>
</body>
</html>


