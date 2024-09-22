<?php

include 'koneksi.php';

session_start(); // Pastikan session dimulai di awal file

// Cek apakah pengguna sudah login
$isLoggedIn = isset($_SESSION['username']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$username = $_SESSION['username'];

// Inisialisasi variabel
$latestBooking = null;
$showNotification = false;

// Mengambil booking terbaru untuk pengguna
$query = $conn->prepare("SELECT * FROM booking WHERE username = ? ORDER BY waktu_booking DESC LIMIT 1");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$latestBooking = $result->fetch_assoc(); // Simpan data booking terbaru

// Cek apakah booking terbaru ada dan statusnya 'Diterima'
if ($latestBooking && $latestBooking['status'] === 'Diterima') {
    // Jika notifikasi belum pernah ditampilkan, tampilkan sekarang
    if (!isset($_SESSION['notification_shown'])) {
        $_SESSION['notification_shown'] = true; // Tandai notifikasi telah ditampilkan
        $showNotification = true; // Aktifkan notifikasi
    }
} else {
    // Reset notifikasi jika status booking berubah atau tidak ada
    unset($_SESSION['notification_shown']);
}



// Mengambil data booking untuk pengguna yang sedang login
$query = $conn->prepare("SELECT * FROM booking WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();


$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Beranda</title>
    <style>
        /* Style umum untuk reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

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

        .sign-in-link,
.logout-link {
    color: white; /* Ubah warna teks agar kontras dan terlihat */
    text-decoration: none; /* Hilangkan garis bawah default */
    margin-left: 20px;
    align-self: center;
    font-weight: bold; /* Opsional: buat teks lebih tebal agar lebih terlihat seperti tombol */
    padding: 0; /* Hilangkan padding jika ingin benar-benar seperti teks biasa */
    background-color: transparent; /* Pastikan background tidak ada */
    border-radius: 0; /* Hilangkan radius untuk sudut */
}

.sign-in-link:hover,
.logout-link:hover {
    text-decoration: underline; /* Tambahkan underline saat hover untuk efek interaktif */
}

        .user-name {
            color: #fff;
            margin-left: 10px;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            padding-top: 60px;
            transition: left 0.3s ease;
            z-index: 999;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar ul li a {
            display: block;
            padding: 15px 20px;
            color: #fff;
            text-decoration: none;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
        }

        .sidebar ul li a:hover {
            background-color: #444;
        }

        main {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        main.sidebar-active {
            margin-left: 250px;
        }

        .room-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Jarak antara card */
            justify-content: center;
            /* Rata tengah */
        }

        .room-card {
            flex: 1 1 calc(33.333% - 20px);
            /* 3 card per baris dengan gap 20px */
            max-width: calc(33.333% - 20px);
            /* 3 card per baris dengan gap 20px */
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            position: relative;
            box-sizing: border-box;
        }

        .room-card img {
            width: 100%;
            /* Lebar gambar mengikuti lebar card */
            height: auto;
            /* Menjaga proporsi gambar */
            border-radius: 8px;
            /* Menjaga border-radius gambar */
            display: block;
            /* Menghilangkan ruang di bawah gambar */
            margin: 0 auto;
            /* Memastikan gambar berada di tengah */
        }

        .room-card h2 {
            margin: 10px 0;
        }

        .room-card p {
            margin: 5px 0;
        }

        .room-card button {
            background-color: #333;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        .room-card button:hover {
            background-color: #555;
        }

        footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
        }

       
        .footer-content a {
            color: #fff;
            text-decoration: none;
            margin: 0 5px;
        }

        .footer-content a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                left: -100%;
                transform: translateX(-100%);
            }

            .sidebar.active {
                left: 0;
                transform: translateX(0);
            }

            main {
                margin-left: 0;
            }

            .room-card {
                flex: 1 1 calc(50% - 20px);
                /* 2 card per baris pada layar kecil */
                max-width: calc(50% - 20px);
                /* 2 card per baris pada layar kecil */
            }
        }

        @media (max-width: 480px) {
            .room-card {
                flex: 1 1 100%;
                /* 1 card per baris pada layar sangat kecil */
                max-width: 100%;
                /* 1 card per baris pada layar sangat kecil */
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
            max-width: 500px;
            height: auto;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-img {
            width: 100%;
            height: auto;
        }

        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
        }

        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .dots {
            text-align: center;
            padding: 20px;
        }

        .dot {
            cursor: pointer;
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
        }

        .active,
        .dot:hover {
            background-color: #717171;
        }

        .notification {
            display: none;
            padding: 15px;
            background-color: #4caf50;
            color: white;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            border-radius: 5px;
        }
        .notification a, .notification button {
            color: white;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }
        .notification button {
            background-color: #f44336;
            padding: 5px 10px;
            border-radius: 3px;
        }
        /* Animasi Fade In */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .notif-icon {
            width: 24px;
            height: 24px;
            cursor: pointer;
            position: relative;
        }

        .notif-badge {
            background-color: red;
            color: white;
            border-radius: 50%;
            position: absolute;
            top: -5px;
            right: -5px;
            width: 10px;
            height: 10px;
            display: inline-block;
        }

        header, footer, .sidebar {
    background-color: #2c3e50; /* Pastikan semua menggunakan warna yang sama */
    color: #fff; /* Warna teks jika ingin seragam */
}

    </style>
   <script defer>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('main');
        const menuIcon = document.querySelector('.menu-icon');

        menuIcon.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('sidebar-active');
        });

        let slideIndex = 0;

        function showSlides(n, modalId) {
            let i;
            let slides = document.querySelectorAll(`#${modalId} .mySlides`);
            let dots = document.querySelectorAll(`#${modalId} .dot`);
            if (n > slides.length) {
                slideIndex = 1;
            }
            if (n < 1) {
                slideIndex = slides.length;
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " active";
        }

        function plusSlides(n, modalId) {
            showSlides(slideIndex += n, modalId);
        }

        function currentSlide(n, modalId) {
            showSlides(slideIndex = n, modalId);
        }

        function openModal(modalId) {
            slideIndex = 1; // Reset slideIndex ke 1 setiap kali modal dibuka
            document.getElementById(modalId).style.display = "block";
            showSlides(slideIndex, modalId);
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal(event.target.id);
            }
        }

        // Exposing functions to global scope to be used in HTML
        window.plusSlides = plusSlides;
        window.currentSlide = currentSlide;
        window.openModal = openModal;
        window.closeModal = closeModal;

        // Notifikasi
        function showNotification(message, link) {
            const notification = document.querySelector('.notification');
            notification.innerHTML = `
                ${message} 
                <a href="${link}" class="close-notification">Click here</a> 
                <button class="close-notification">Close</button>
            `;
            notification.style.display = 'block';

            const closeElements = document.querySelectorAll('.close-notification');
            closeElements.forEach(function(element) {
                element.addEventListener('click', function() {
                    closeNotification();
                });
            });

            setTimeout(function() {
                closeNotification();
            }, 10000);
        }

        function closeNotification() {
            const notification = document.querySelector('.notification');
            notification.style.display = 'none';
        }

        // Mengecek apakah booking baru sudah disimpan
        // Hapus flag setelah notifikasi muncul
        localStorage.removeItem('newBooking');

        <?php if ($showNotification): ?>
            showNotification('Booking Anda telah diterima.', 'history.php');
        <?php endif; ?>
    });
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
            <?php if (isset($_SESSION['username'])): ?>
                <div class="header-right">
                    <!-- Ikon Notifikasi -->
                    <div class="notification-icon" onclick="showNotifications()">
                        <img src="uploads/bell.png" alt="Notifikasi" class="notif-icon">
                        <span id="notif-badge" class="notif-badge" style="display: none;"></span> <!-- Titik merah -->
                    </div>
                    <!-- Nama Pengguna -->
                    <a>Pengguna:</a>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            <?php else: ?>
                <a href="login.php" class="sign-in-link">Sign In</a>
            <?php endif; ?>
        </div>
    </header>


    <div class="sidebar">
        <ul>
            <li><a href="index.php">Beranda</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="history.php">History</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="help.php">Help</a></li>
                <li><a href="logout.php?logout=true">Logout</a></li>
            <?php else: ?>
                <li><a href="about.php">About</a></li>
                <li><a href="help.html">Help</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <main>
    <div class="notification"></div>

        <br><br><br>
        <div class="room-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="room-card">';
                    echo '<img src="' . $row['image'] . '" alt="Room ' . $row['room_number'] . '">';
                    echo '<h2>Room ' . $row['room_number'] . '</h2>';
                    echo '<p>Harga: Rp ' . number_format($row['price'], 0, ',', '.') . '/malam</p>';
                    echo '<p>Fasilitas: ' . $row['facilities'] . '</p>';
                    echo '<hr>';
                    echo '<p>LOKASI 📌: ' . $row['location'] . '</p>';
                    echo '<button onclick="window.location.href=\'booking.php?room=' . $row['room_number'] . '&price=' . $row['price'] . '&fasilitas=' . urlencode($row['facilities']) . '\'">Booking</button>';
                    echo '<button onclick="openModal(\'modal' . $row['room_number'] . '\')">Lihat Detail Foto</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>Tidak ada kamar tersedia</p>';
            }
            ?>
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
    <div id="modal1" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal1')">&times;</span>
            <div class="slideshow-container">
                <div class="mySlides">
                    <img class="modal-img" src="foto2.jpeg" alt="Room 1 Photo 1">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto2.jpeg" alt="Room 1 Photo 2">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto2.jpeg" alt="Room 1 Photo 3">
                </div>

                <a class="prev" onclick="plusSlides(-1, 'modal1')">&#10094;</a>
                <a class="next" onclick="plusSlides(1, 'modal1')">&#10095;</a>
            </div>
            <div class="dots">
                <span class="dot" onclick="currentSlide(1, 'modal1')"></span>
                <span class="dot" onclick="currentSlide(2, 'modal1')"></span>
                <span class="dot" onclick="currentSlide(3, 'modal1')"></span>
            </div>
        </div>
    </div>
    <div id="modal2" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal2')">&times;</span>
            <div class="slideshow-container">
                <div class="mySlides">
                    <img class="modal-img" src="foto1.jpg" alt="Room 1 Photo 1">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto1.jpg" alt="Room 1 Photo 2">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto1.jpg" alt="Room 1 Photo 3">
                </div>

                <a class="prev" onclick="plusSlides(-1, 'modal2')">&#10094;</a>
                <a class="next" onclick="plusSlides(1, 'modal2')">&#10095;</a>
            </div>
            <div class="dots">
                <span class="dot" onclick="currentSlide(1, 'modal2')"></span>
                <span class="dot" onclick="currentSlide(2, 'modal2')"></span>
                <span class="dot" onclick="currentSlide(3, 'modal2')"></span>
            </div>
        </div>
    </div>
    <div id="modal3" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal3')">&times;</span>
            <div class="slideshow-container">
                <div class="mySlides">
                    <img class="modal-img" src="foto4.jpeg" alt="Room 1 Photo 1">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto4.jpeg" alt="Room 1 Photo 2">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto4.jpeg" alt="Room 1 Photo 3">
                </div>

                <a class="prev" onclick="plusSlides(-1, 'modal3')">&#10094;</a>
                <a class="next" onclick="plusSlides(1, 'modal3')">&#10095;</a>
            </div>
            <div class="dots">
                <span class="dot" onclick="currentSlide(1, 'modal3')"></span>
                <span class="dot" onclick="currentSlide(2, 'modal3')"></span>
                <span class="dot" onclick="currentSlide(3, 'modal3')"></span>
            </div>
        </div>
    </div>
    <div id="modal4" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal4')">&times;</span>
            <div class="slideshow-container">
                <div class="mySlides">
                    <img class="modal-img" src="foto3.jpeg" alt="Room 1 Photo 1">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto3.jpeg" alt="Room 1 Photo 2">
                </div>
                <div class="mySlides">
                    <img class="modal-img" src="foto101.jpeg" alt="Room 1 Photo 3">
                </div>

                <a class="prev" onclick="plusSlides(-1, 'modal4')">&#10094;</a>
                <a class="next" onclick="plusSlides(1, 'modal4')">&#10095;</a>
            </div>
            <div class="dots">
                <span class="dot" onclick="currentSlide(1, 'modal4')"></span>
                <span class="dot" onclick="currentSlide(2, 'modal4')"></span>
                <span class="dot" onclick="currentSlide(3, 'modal4')"></span>
            </div>
        </div>
    </div>



</body>

</html>