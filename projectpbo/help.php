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

     
header, footer, .sidebar {
    background-color: #2c3e50; /* Pastikan semua menggunakan warna yang sama */
    color: #fff; /* Warna teks jika ingin seragam */
}

.content {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2980b9;
        }

        p {
            line-height: 1.6;
            text-align: center;
            color: #555;
        }

        /* Form */
        .help-form {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        .help-form label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .help-form input, 
        .help-form textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .help-form button {
            padding: 10px;
            background-color: #2980b9;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .help-form button:hover {
            background-color: #1f6397;
        }

        /* Info Umum */
        .info {
            margin-top: 40px;
            padding: 20px;
            background-color: #eaeaea;
            border-radius: 10px;
        }

        .info h2 {
            text-align: center;
            color: #333;
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
    <h1>Butuh Bantuan?</h1>
    <p>Isi formulir di bawah ini untuk mengajukan pertanyaan atau memberikan komentar.</p>

    <form class="help-form" action="process_help.php" method="POST">
    <label for="name">Nama:</label>
    <input type="text" id="nama" name="nama" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="message">Pesan:</label>
    <textarea id="message" name="pesan" rows="5" required></textarea>

    <button type="submit">Kirim Pesan</button>
</form>


    </main>

   
    <footer>
    <div class="footer-content">
        <p>
            <p style="color: #fff;">Untuk pertanyaan cepat, Anda dapat menghubungi kami melalui:</p>
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
