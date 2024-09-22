<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
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
         /* Style untuk footer */
         
         .footer-content {
    display: flex;
    justify-content: center; /* Memusatkan isi secara horizontal */
    align-items: center; /* Memusatkan isi secara vertikal */
    padding: 10px; /* Menambahkan padding jika diperlukan */
    position: relative;
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

header, footer, .sidebar {
    background-color: #2c3e50; /* Pastikan semua menggunakan warna yang sama */
    color: #fff; /* Warna teks jika ingin seragam */
}

         
.content {
    background-color: #f9f9f9; /* Warna latar belakang yang lembut */
    padding: 20px;
    border-radius: 10px; /* Pembulatan sudut */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Bayangan */
    margin: 20px; /* Jarak antara konten dan tepi */
    max-width: 800px; /* Maksimal lebar untuk konten */
    margin: auto; /* Memusatkan konten */
}

h1 {
    font-size: 36px;
    color: #2c3e50; /* Warna gelap untuk judul */
    margin-bottom: 15px;
    text-align: center; /* Memusatkan judul */
}

.card {
    background: #ffffff; /* Latar belakang putih untuk kartu */
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan untuk kartu */
    padding: 20px;
    margin: 15px 0; /* Jarak antar kartu */
    transition: transform 0.3s; /* Efek transisi saat hover */
}

.card:hover {
    transform: translateY(-5px); /* Efek naik saat hover */
}

.card h2 {
    color: #2980b9; /* Warna judul dalam kartu */
}

.card p {
    color: #555; /* Warna teks deskripsi dalam kartu */
    line-height: 1.6; /* Jarak antar baris */
}

.card:hover {
    background-color: #ecf0f1; /* Latar belakang berubah saat hover */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Bayangan lebih gelap saat hover */
}

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .content {
            padding: 100px 20px 20px;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
            animation: fade-in 0.5s ease-in-out forwards;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .card {
            perspective: 1000px; /* Efek 3D */
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 300px;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .card:hover .card-inner {
            transform: rotateY(180deg);
        }

        .card-front, .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .card-front {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-back {
            background: #2c3e50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotateY(180deg);
        }

        .card img {
            max-width: 100%;
            border-radius: 10px;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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
   <div class="content">
    <h1>Tentang kami:</h1>
    <p>Selamat datang di Hotel Alexander, tempat kenyamanan dan kemewahan bertemu. Kami menawarkan pengalaman menginap yang tak terlupakan dengan pelayanan terbaik.</p>

    <div class="card">
        <h2>Visi Kami</h2>
        <p>Menyediakan tempat tinggal yang nyaman dengan pelayanan terbaik untuk semua tamu kami.</p>
    </div>

    <div class="card">
        <h2>Misi Kami</h2>
        <p>Memberikan pengalaman menginap yang menyenangkan dan memenuhi semua kebutuhan tamu.</p>
    </div>

    <div class="card">
        <h2>Nilai Kami</h2>
        <p>Kami menghargai kejujuran, integritas, dan komitmen terhadap kualitas layanan.</p>
    </div>
</div>

<br>
<br>
<br>
<br>

<div class="grid">
        <!-- Card 1 -->
        <div class="card">
            <div class="card-inner">
                <div class="card-front">
                    <img src="uploads/about1.jpeg" alt="Kamar 1">
                </div>
                <div class="card-back">
                   <img src="uploads/about_2_1.jpeg" alt="part2">
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="card">
            <div class="card-inner">
                <div class="card-front">
                    <img src="uploads/about2.jpeg" alt="Kamar 2">
                </div>
                <div class="card-back">
                   <img src="uploads/about_2_2.jpeg" alt="">
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="card">
            <div class="card-inner">
                <div class="card-front">
                    <img src="uploads/about3.jpeg" alt="Kamar 3">
                </div>
                <div class="card-back">
                 <img src="uploads/about_2_3.jpeg" alt="">
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="card">
            <div class="card-inner">
                <div class="card-front">
                    <img src="uploads/about4.jpeg" alt="Lobby">
                </div>
                <div class="card-back">
                    <img src="uploads/about_2_4.jpeg" alt="">
                </div>
            </div>
        </div>
    </div>
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
