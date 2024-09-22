<?php
include 'koneksi.php'; // Pastikan koneksi MySQLi diinisialisasi di sini

// Query untuk mengambil data pengguna dari tabel registrasi
$sql = "SELECT id, username, email, phone FROM registrasi";

// Siapkan pernyataan SQL
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Ambil semua data sebagai array asosiatif
$users = $result->fetch_all(MYSQLI_ASSOC);

// Debugging output (opsional)
// echo '<pre>';
// print_r($users);
// echo '</pre>';
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM registrasi WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    header('Location: user_list.php'); // Redirect setelah penghapusan
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Gaya umum */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        form {
    margin: 20px; /* Jarak dari elemen lain */
    display: flex;
    justify-content: flex-start; /* Geser form ke kiri */
}

form input[type="text"] {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 300px; /* Atur lebar input */
    margin-right: 10px; /* Jarak antara input dan tombol */
}

form input[type="submit"] {
    padding: 8px 4px; /* Mengurangi padding */
    font-size: 14px; /* Mengurangi ukuran font */
    border: none;
    border-radius: 4px;
    background-color: #808588; /* Sesuaikan dengan warna tema */
    color: #fff;
    cursor: pointer;
}

form input[type="submit"]:hover {
    background-color: #666; /* Warna saat hover */
}
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            background-color: #808588;
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

        .title {
            font-size: 24px;
            text-align: center; /* Memastikan teks berada di tengah */
            flex-grow: 1; /* Mengambil ruang yang tersedia agar teks bisa berada di tengah */
        }

        .sidebar {
            width: 200px;
            background-color: #808588;
            position: fixed;
            top: 0;
            left: -250px; /* Mulai sidebar dari luar layar */
            height: 100%;
            padding-top: 60px; /* Untuk memberi ruang di bawah header */
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: left 0.3s ease; /* Gunakan left untuk pergerakan sidebar */
            z-index: 999;
        }

        .sidebar.active {
            left: 0; /* Menampilkan sidebar */
        }

        .sidebar ul {
            list-style-type: none; /* Menghilangkan bullet point */
            padding: 0;
            width: 100%; /* Pastikan lebar sidebar penuh */
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar ul li a {
            display: block; /* Membuat link memenuhi lebar sidebar */
            padding: 15px 20px; /* Memberikan ruang dalam link */
            color: #fff; /* Warna teks putih */
            text-decoration: none; /* Menghilangkan underline */
            width: 100%; /* Pastikan lebar penuh */
            box-sizing: border-box; /* Agar padding tidak melebihi lebar elemen */
            text-align: center; /* Teks di tengah */
        }

        .sidebar ul li a:hover {
            background-color: #444; /* Warna background saat di-hover */
        }

        main {
            padding: 20px;
            padding-top: 70px;
            padding-bottom: 20px; /* Memberi ruang di bawah untuk footer */
            flex: 1 0 auto; /* Agar konten utama dapat memperluas halaman */
            transition: margin-left 0.3s ease; /* Tambahkan transisi untuk efek mulus */
        }

        main.with-sidebar {
            margin-left: 250px; /* Memberi ruang untuk sidebar hanya jika aktif */
        }

        footer {
            background-color:#808588 ;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            width: 100%;
            position: relative;
            clear: both;
            margin-top: auto; /* Membuat footer berada di bawah */
        }

        .footer-content a {
            color: #fff;
            text-decoration: none;
            margin: 0 5px;
        }

        .footer-content a:hover {
            text-decoration: underline;
        }

        /* CSS untuk tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Menandai baris hasil pencarian */
        tr.highlight {
            background-color: #ffff99; /* Warna latar belakang untuk menandai baris */
        }

        /* Link di dalam tabel */
        table a {
            color: #0066cc;
            text-decoration: none;
        }

        table a:hover {
            text-decoration: underline;
        }

        /* Responsif */
        @media (max-width: 768px) {
            main {
                margin-left: 0;
                padding-top: 60px;
            }

            .sidebar {
                width: 100%;
                top: -100%; /* Mulai dari atas layar */
                left: 0; /* Atur ulang untuk tampilan ponsel */
                transform: translateY(-100%);
                height: auto;
            }

            .sidebar.active {
                transform: translateY(0); /* Pindahkan ke layar */
            }
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
            padding-top: 60px;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 5% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 90%; /* Default width */
            max-width: 600px; /* Max width */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow-y: auto; /* Allow vertical scrolling */
        }

        /* Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        label {
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="date"], input[type="number"], select {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%; /* Full width */
            box-sizing: border-box;
        }

        button {
            background-color: #333;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

 
        
a {
    text-decoration: none;
    padding: 5px 10px;
    margin: 0 5px;
    border-radius: 4px;
    color: #fff;
    font-weight: bold;
}

/* Mengatur gaya umum untuk tombol "Edit" dan "Hapus" */
.btn-update, .btn-delete {
    display: inline-block;
    padding: 8px 16px;
    margin: 4px;
    border: none;
    border-radius: 4px;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
    color: #fff;
    background-color: #6c757d; /* Warna abu-abu dasar */
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-update {
    /* Gaya khusus untuk tombol "Edit" */
}

.btn-update:hover, .btn-delete:hover {
    background-color: #5a6268; /* Warna abu-abu lebih gelap saat hover */
}

.btn-delete {
    /* Gaya khusus untuk tombol "Hapus" */
}

.btn-delete:hover {
    background-color: #495057; /* Warna abu-abu lebih gelap saat hover */
}

/* Gaya tambahan untuk tombol ketika diklik */
.btn-update:active, .btn-delete:active {
    background-color: #343a40; /* Warna abu-abu lebih gelap saat diklik */
}

/* Gaya hover untuk semua tombol */
a:hover {
    opacity: 0.7;
}
.search-form {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px; /* Adds some spacing below the form */
}

/* Style for the search input field */
.search-form input[type="text"] {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 200px; /* Sets a fixed width */
    transition: border-color 0.3s; /* Adds transition for border color */
}

/* Style for the submit button */
.search-form input[type="submit"] {
    padding: 10px 20px;
    font-size: 14px;
    color: white;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s; /* Adds transition for background color */
}

/* Hover effect for the submit button */
.search-form input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Focus effect for the input field */
.search-form input[type="text"]:focus {
    border-color: #007bff;
}
        
        /* Modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
            padding-top: 60px;
        }
        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 5% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 90%; /* Default width */
            max-width: 600px; /* Max width */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow-y: auto; /* Allow vertical scrolling */
        }
        /* Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        /* Form Styling */
        .sidebar ul li a.active {
    background-color: #555; /* Warna latar belakang item aktif */
    font-weight: bold; /* Menebalkan teks item aktif */
    color: #fff; /* Warna teks item aktif */
}
    </style>
    <script defer>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('main');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('with-sidebar'); // Tambahkan atau hapus kelas berdasarkan sidebar aktif
        }

        function openEditForm(id, username, email, phone) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditForm() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDeletion(event, id) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'user_list.php?delete_id=' + id;
            }
            event.preventDefault();
        }
    </script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="menu-icon" onclick="toggleSidebar()">
                &#9776; <!-- Simbol untuk tiga garis (ikon hamburger) -->
            </div>
            <div class="title">Hotel Alexander</div>
        </div>
    </header>

    <div class="sidebar">
        <ul>
        <li><a href="admin.php" class="<?php echo $current_page == 'admin.php' ? 'active' : ''; ?>">Home</a></li>
        <li><a href="user_list.php" class="<?php echo $current_page == 'user_list.php' ? 'active' : ''; ?>">User</a></li>
        <li><a href="daftar_kamar.php" class="<?php echo $current_page == 'daftar_kamar.php' ? 'active' : ''; ?>">Daftar kamar</a></li>
        <li><a href="komentar.php" class="<?php echo $current_page == 'komentar.php' ? 'active' : ''; ?>">Komentar user</a></li>
        <li><a href="logout.php?logout=true" class="<?php echo $current_page == 'logout.php' ? 'active' : ''; ?>">Logout</a></li>
        </ul>
    </div>
    <main>
    <h2>Daftar Pengguna</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td>
                        <a href="#" class="btn-update" onclick="openEditForm('<?php echo htmlspecialchars($user['id']); ?>', '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['phone']); ?>')">Edit</a>
                        <a href="#" class="btn-delete" onclick="confirmDeletion(event, '<?php echo htmlspecialchars($user['id']); ?>')">Hapus</a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    </main>

    <!-- Modal Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditForm()">&times;</span>
            <h2>Edit User</h2>
            <form action="update_user.php" method="post">
                <input type="hidden" id="edit_id" name="id">
                <label for="edit_username">Username:</label>
                <input type="text" id="edit_username" name="username" required>
                <label for="edit_email">Email:</label>
                <input type="text" id="edit_email" name="email" required>
                <label for="edit_phone">Phone:</label>
                <input type="text" id="edit_phone" name="phone" required>
                <button type="submit">Edit</button>
            </form>
        </div>
    </div>
</body>
</html>














