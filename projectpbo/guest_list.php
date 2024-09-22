<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

// Query untuk mengambil nama, email, dan nomor telepon dari pengguna yang sudah melakukan booking dengan status diterima
$sql = "SELECT DISTINCT r.id, r.username, r.email, r.phone
        FROM registrasi r
        JOIN booking b ON r.id = b.id
        WHERE b.status = 'Diterima'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tamu</title>
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
    <h1>Daftar Tamu</h1>
    <table>
        <tr>
            <th>Nama Pengguna</th>
            <th>Email</th>
            <th>Nomor Telepon</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output setiap baris
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['phone']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Belum ada tamu yang terdaftar.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>


