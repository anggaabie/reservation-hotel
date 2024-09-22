<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];



$query = $conn->prepare("SELECT * FROM booking WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Booking History</title>
    <style>
        /* Reset style umum */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Style untuk header */
         /* Header */
         header {
            background-color: #2c3e50;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-between;
        }

        .menu-icon {
            font-size: 24px;
            cursor: pointer;
            display: block;
        }

        .header-title-container {
    display: flex;
    align-items: center;
    justify-content: center; /* Menyatukan logo dan tulisan di tengah secara horizontal */
    width: 100%; /* Pastikan elemen ini mengambil seluruh lebar header */
}
.logo {
    width: 40px;
    height: auto;
    margin-right: 10px; /* Jarak antara logo dan teks */
}

.title {
    font-size: 24px;
    text-align: center;
    margin: 0; /* Menghapus margin yang tidak perlu */
}

        .header-right {
            display: flex;
            align-items: center;
        }
        /* Style untuk sidebar */
        .sidebar {
            width: 250px;
            background-color: #808588;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding-top: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease;
            transform: translateX(-100%);
            z-index: 999;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar ul {
            list-style-type: none;
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            color: #fff;
            padding: 15px;
            width: 100%;
            text-align: center;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        /* Style untuk konten utama */
        main {
            margin-left: 0; /* Mulai dengan margin 0 */
            padding: 20px;
            padding-top: 70px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.active + main {
            margin-left: 250px; /* Menambah margin ketika sidebar aktif */
        }

        /* Style untuk tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            text-align: left;
        }

        th, td {
            padding: 12px;
        }

        th {
            background-color: #D3D3D3;
            color: #000;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        td {
            color: #000;
            font-size: 14px;
        }

        /* Style untuk footer */
        footer {
    background-color: #808588;
    color: #fff;
    text-align: center;
    padding: 15px 0;
    position: relative;
    bottom: 0;
    left: 0;
    width: 100%;
}

        .footer-content a {
            color: #fff;
            text-decoration: none;
            margin: 0 5px;
        }

        .footer-content a:hover {
            text-decoration: underline;
        }

        /* Mengatur jarak main dari footer agar tidak tertutup */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1; /* Mengisi ruang yang tersisa agar footer tetap di bawah */
        }

        .map-container {
    margin-top: 20px; /* Ruang di atas peta */
    margin-bottom: 20px; /* Ruang di bawah peta */
    width: 100%;
    display: flex;
    justify-content: center;
}

.map-container iframe {
    border: 0;
}

header, footer, .sidebar {
    background-color: #2c3e50; /* Pastikan semua menggunakan warna yang sama */
    color: #fff; /* Warna teks jika ingin seragam */
}
    </style>
    <script defer>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
            console.log('Sidebar toggled', sidebar.classList);
        }
    </script>
</head>
<body>
<header>
        <div class="header-container">
            <div class="menu-icon" onclick="toggleSidebar()">
                &#9776; <!-- Simbol untuk tiga garis (ikon hamburger) -->
            </div>
            <div class="header-title-container">
                <img src="logoheader.png" alt="Logo Hotel Alexander" class="logo"> <!-- Tambahkan logo -->
                <div class="title">Hotel Alexander</div>
            </div>
        </div>
    </header>

    <div class="sidebar">
        <ul>
        <li><a href="index.php">Beranda</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="help.php">Help</a></li>
            <li><a href="logout.php?logout=true">Logout</a></li>
        </ul>
    </div>

    <main>
        <center><h1>Booking History</h1></center>
        <table>
        <thead>
    <tr>
        <th>Nama</th>
        <th>Room</th>
        <th>Check-in Date</th>
        <th>Check-out Date</th>
        <th>Fasilitas</th>
        <th>Total Price</th>
        <th>Code booking</th>
        <th>Status</th>
        <!-- <th>Opsi</th> Tambahkan kolom "Cancel" -->
    </tr>
</thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['room']; ?></td>
                            <td><?php echo $row['check_in']; ?></td>
                            <td><?php echo $row['check_out']; ?></td>
                            <td><?php echo $row['fasilitas']; ?></td>
                            <td><?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['unique_code']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <!-- <td>
                    
                    <form action="cancel_booking.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Cancel</button>
                    </form>
                </td> -->
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Belum ada pesanan):</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <p><strong>PEMBERITAHUAN!</strong></p>
        <br>
        <p><li>Silahkan gunakan code booking untuk diberitahu saat administrasi.</li></p>
        <p><li>Untuk checkin masuk pada pukul 11:00 WIB /checkout pukul 10:00 WIB</li></p>
        <p><li>Jika Sudah di acc,silahkan datang pada alamat dibawah.</li></p>
        <br>
        <br>
        <br>
        <p>SYARAT DAN KETENTUAN</p>
        <br>
        <p><li><strong>MEMBAWA FOTOCOPY KK & FOTOCOPY KTP</strong></li></p>
        


        <br>
       <center><p><strong>MAPS</strong></p></center>
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3950.287737631377!2d112.65878371508602!3d-7.930577394328188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f7903b93b6b%3A0x87b0a68692a5a1e8!2sJalan%20Lapangan%20Banteng%20Selatan%20No.%208%2C%20Jakarta%20Selatan!5e0!3m2!1sen!2sid!4v1632764187108!5m2!1sen!2sid"
                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>

    </main>

   
    <footer>
    <div class="footer-content">
        <p>
            Hubungi kami:
            <a href="https://instagram.com/alexanderhotel44" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            | 
            <a href="mailto:info@hotelalexander.com">
                <i class="fas fa-envelope"></i>
            </a>
            | 
            <a href="tel:+1234567890">
                <i class="fab fa-whatsapp"></i>
            </a>
        </p>
    </div>
</footer>
</body>
</html>

<?php
$query->close();
$conn->close();
?>
