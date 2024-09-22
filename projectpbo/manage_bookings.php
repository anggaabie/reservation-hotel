<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

// Query untuk mengambil semua reservasi
$sql = "SELECT * FROM booking";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Reservasi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    
</head>
<body>
    <h1>Manajemen Reservasi</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['room']; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['check_in'])); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['check_out'])); ?></td>
                    <td><?php echo number_format($row['total_price'], 2); ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <a href="update_resepsionis.php?id=<?php echo $row['id']; ?>">Update</a> | 
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
