<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

// Query untuk mengambil semua booking
$sql = "SELECT username, room, check_in, check_out, fasilitas, total_price, unique_code, status FROM booking";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Status Booking</h1>
    <table>
        <tr>
            <th>Nama Tamu</th>
            <th>Kamar</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Fasilitas</th>
            <th>Total Harga</th>
            <th>Kode Booking</th>
            <th>Status</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output setiap baris
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['room']) . "</td>
                        <td>" . date('d-m-Y', strtotime($row['check_in'])) . "</td>
                        <td>" . date('d-m-Y', strtotime($row['check_out'])) . "</td>
                        <td>" . htmlspecialchars($row['fasilitas']) . "</td>
                        <td>Rp " . number_format($row['total_price'], 2, ',', '.') . "</td>
                        <td>" . htmlspecialchars($row['unique_code']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>Belum ada booking.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
