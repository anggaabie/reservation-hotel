<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

// Menghapus booking
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM booking WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Booking deleted successfully!";
    } else {
        echo "Error deleting booking: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_GET['Diterima']) || isset($_GET['Ditolak'])) {
    $id = intval($_GET['Diterima'] ?? $_GET['Ditolak']);
    $status = isset($_GET['Diterima']) ? 'Diterima' : 'Ditolak';

    // Update status booking
    $stmt = $conn->prepare("UPDATE booking SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo "Booking status updated to $status!";
    } else {
        echo "Error updating status: " . $stmt->error;
    }
    $stmt->close();
}


if (isset($_POST['action']) && $_POST['action'] == 'Update') {
    $booking_id = intval($_POST['booking_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE booking SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    if ($stmt->execute()) {
        echo "<p>Booking status updated to $status!</p>";
    } else {
        echo "<p>Error updating status: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Menambahkan booking baru
if (isset($_POST['action']) && $_POST['action'] == 'Create') {
    $username = $_POST['username'];
    $room = $_POST['room'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $fasilitas = $_POST['fasilitas'];
    $total_price = $_POST['total_price'];
    $unique_code = uniqid('GABS_'); // Membuat kode booking unik
    $status = 'Menunggu';

    $stmt = $conn->prepare("INSERT INTO booking (username, room, check_in, check_out, fasilitas, total_price, unique_code, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssddss", $username, $room, $check_in, $check_out, $fasilitas, $total_price, $unique_code, $status);
    
    if ($stmt->execute()) {
        echo "<p>Booking added successfully!</p>";
    } else {
        echo "<p>Error adding booking: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
 
$search_query = "";
$order_clause = "";
if (isset($_GET['search_code']) && !empty($_GET['search_code'])) {
    $search_code = $conn->real_escape_string($_GET['search_code']);
    $search_query = " WHERE unique_code LIKE '%$search_code%'";
    $order_clause = " ORDER BY CASE WHEN unique_code LIKE '%$search_code%' THEN 0 ELSE 1 END, id ASC";
}

if (isset($_GET['Delete'])) {
    $id = $_GET['Delete'];
    $sql = "DELETE FROM booking WHERE id= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo "Record deleted successfully.";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
// Menampilkan data booking
$query = "SELECT * FROM booking" . $search_query . $order_clause;
$result = $conn->query($query);

$current_page = basename($_SERVER['PHP_SELF']);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tentukan level (admin atau resepsionis)
$level = 'resepsionis'; // Ubah jadi 'resepsionis' untuk menampilkan resepsionis
// Query untuk mengambil nama berdasarkan level
$query = "SELECT nama FROM user WHERE level='$level'";

// Eksekusi query
$result = mysqli_query($conn, $query);

// Tampilkan hasil


// Tutup koneksi
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Booking Management</title>
    <style>
        /* Style umum untuk reset */
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
            width: 250px;
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

/* Gaya untuk tombol Update */
.btn-update {
    background-color: #007bff; /* Warna biru */
}

/* Gaya untuk tombol Terima */
.btn-Terima {
    background-color: #28a745; /* Warna hijau */
}

/* Gaya untuk tombol Tolak */
.btn-Tolak {
    background-color: #dc3545; /* Warna merah */
}

/* Gaya untuk tombol Delete */
.btn-delete {
    background-color: #ffc107; /* Warna kuning */
    color: #000; /* Warna teks hitam */
}

/* Gaya hover untuk semua tombol */
a:hover {
    opacity: 0.8;
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
        
sidebar-header h2 {
    margin: 0;
    font-size: 20px;
    color: #fff;
}
.sidebar ul li a.active {
    background-color: #555; /* Warna latar belakang item aktif */
    font-weight: bold; /* Menebalkan teks item aktif */
    color: #fff; /* Warna teks item aktif */
}

.menu-icon {
    cursor: pointer;
}

/* Memastikan search-container menyesuaikan kontennya */
    </style>
    <script defer>
         function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('main');

        sidebar.classList.toggle('active');
        mainContent.classList.toggle('with-sidebar');
        
        function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('open');
}

// Menjaga sidebar tetap terbuka jika ada klik di luar sidebar
document.addEventListener('click', (event) => {
    const sidebar = document.querySelector('.sidebar');
    const menuIcon = document.querySelector('.menu-icon');

    if (!sidebar.contains(event.target) && !menuIcon.contains(event.target)) {
        sidebar.classList.remove('open');
    }
});
    }
    function formatCurrency(value) {
    // Format angka ke format IDR
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function openUpdateForm(id, username, room, check_in, check_out, fasilitas, total_price, unique_code, status) {
    document.getElementById('update_id').value = id;
    document.getElementById('update_username').value = username;
    document.getElementById('update_room').value = room;
    document.getElementById('update_checkin').value = check_in;
    document.getElementById('update_checkout').value = check_out;
    document.getElementById('update_fasilitas').value = fasilitas;
    document.getElementById('update_total_price').value = formatCurrency(total_price);
    document.getElementById('update_unique_code').value = unique_code;
    document.getElementById('update_status').value = status;

    var modal = document.getElementById("updateModal");
    modal.style.display = "block";
}

        // Function to close the update form modal
        function closeUpdateForm() {
            var modal = document.getElementById("updateModal");
            modal.style.display = "none";
        }

        // Close the modal when clicking outside the modal content
        window.onclick = function(event) {
            var modal = document.getElementById("updateModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
function confirmDeletion(event, id) {
    event.preventDefault(); // Mencegah perilaku default dari tautan
    var confirmation = confirm("Apakah Anda yakin ingin menghapus booking ini?");
    if (confirmation) {
        window.location.href = "?delete=" + id;
    }
}


function openCreateForm() {
    var modal = document.getElementById("createModal");
    modal.style.display = "block";
}

// Function to close the create form modal
function closeCreateForm() {
    var modal = document.getElementById("createModal");
    modal.style.display = "none";
}

// Close the modal when clicking outside the modal content
window.onclick = function(event) {
    var modal = document.getElementById("createModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
   
function confirmDeletion(event, id) {
    if (confirm('Apakah kamu yakin ingin menghapus ini?')) {
        window.location.href = 'admin.php?Delete=' + id;
    } else {
        event.preventDefault();
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const currentPage = window.location.pathname.split('/').pop(); // Ambil nama file dari URL
    const menuItems = document.querySelectorAll('.sidebar a');
    
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
        }
    });
});
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
    <div class="sidebar-header">
        <h2>Resepsionis</h2> <!-- Tambahkan teks di atas sidebar -->
    </div>
    <ul>
    <li><a href="resepsionis.php" class="<?php echo $current_page == 'resepsionis.php' ? 'active' : ''; ?>">Home</a></li>
    <li><a href="logout.php?logout=true" class="<?php echo $current_page == 'logout.php' ? 'active' : ''; ?>">Logout</a></li> 
    </ul>
</div>
    <main>
        <center><h1>Booking Management</h1></center>
        <br>
        <?php
        if (mysqli_num_rows($result) > 0) {
       while ($row = mysqli_fetch_assoc($result)) {
        echo "<strong>Nama Staf:</strong> " . $row['nama'] . "<br>";
       }
} else {
    echo "Tidak ada data untuk level $level.";
}
?>
        <br>

        <form method="GET" action="admin.php">
            <input type="text" name="search_code" placeholder="Search booking code" value="<?php echo isset($_GET['search_code']) ? htmlspecialchars($_GET['search_code']) : ''; ?>">
            <input type="Submit" value="Cari">
        </form>

           
          <div>
           <button onclick="openCreateForm()">Tambah Booking</button>
        </div> 

        <!-- Form Create Booking -->
        <div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCreateForm()">&times;</span>
        <h2>Tambah Booking</h2>
        <form id="createForm" method="POST" action="process_create.php">
            <label for="create_username">Nama:</label>
            <input type="text" id="create_username" name="username" required><br>

            <label for="create_room">Kamar:</label>
            <input type="text" id="create_room" name="room" required><br>

            <label for="create_checkin">Check-in:</label>
            <input type="date" id="create_checkin" name="check_in" required><br>

            <label for="create_checkout">Check-out:</label>
            <input type="date" id="create_checkout" name="check_out" required><br>

            <label for="create_fasilitas">Fasilitas:</label>
            <input type="text" id="create_fasilitas" name="fasilitas"><br>

            <label for="create_total_price">Total Harga:</label>
            <input type="number" id="create_total_price" name="total_price" step="0.01" required><br>

            <label for="update_unique_code">Code:</label>
                <input type="text" id="update_unique_code" name="unique_code">

            <input type="hidden" id="status" name="status" value="Menunggu">

            <button type="submit">Tambah</button>
        </form>
    </div>
</div>

        <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateForm()">&times;</span>
            <h2>Update Booking</h2>
            <form id="updateForm" method="POST" action="process_update.php">
                <input type="hidden" id="update_id" name="id">
                <label for="update_username">Username:</label>
                <input type="text" id="update_username" name="username" required>

                <label for="update_room">Room:</label>
                <input type="text" id="update_room" name="room" required>

                <label for="update_checkin">Check-in Date:</label>
                <input type="date" id="update_checkin" name="check_in" required>

                <label for="update_checkout">Check-out Date:</label>
                <input type="date" id="update_checkout" name="check_out" required>

                <label for="update_fasilitas">Fasilitas:</label>
                <input type="text" id="update_fasilitas" name="fasilitas" required>

                <label for="update_total_price">Total Price:</label>
                <input type="text" id="update_total_price" name="total_price" required>

                <label for="update_unique_code">Code:</label>
                <input type="text" id="update_unique_code" name="unique_code">


                <label for="update_status">Status:</label>
                <select id="update_status" name="status" required>
                    <option value="Diterima">Diterima</option>
                    <option value="Ditolak">Ditolak</option>
                </select>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
        

        <table>
            <thead>
                <tr>
                    <th>No</th> 
                    <th>Nama</th>
                    <th>Kamar</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Fasilitas</th>
                    <th>Total Harga</th>
                    <th>Kode Booking</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
               if ($result->num_rows > 0) {
                $row_number = 1;
                while ($row = $result->fetch_assoc()) {
                    $highlight_class = (isset($_GET['search_code']) && !empty($_GET['search_code']) && strpos($row['unique_code'], $_GET['search_code']) !== false) ? 'highlight' : '';
                    echo "<tr class=\"$highlight_class\">";
                    echo "<td>" . $row_number++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['room']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['check_in']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['check_out']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fasilitas']) . "</td>";
                    echo "<td>" . number_format($row['total_price'], 0, ',', '.') . "</td>";
                    echo "<td>" . htmlspecialchars($row['unique_code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td><a href='javascript:void(0)' onclick='openUpdateForm(" . $row['id'] . ", \"" . htmlspecialchars($row['username']) . "\", \"" . htmlspecialchars($row['room']) . "\", \"" . htmlspecialchars($row['check_in']) . "\", \"" . htmlspecialchars($row['check_out']) . "\", \"" . htmlspecialchars($row['fasilitas']) . "\", \"" . number_format($row['total_price'], 0, ',', '.') . "\", \"" . htmlspecialchars($row['unique_code']) . "\", \"" . htmlspecialchars($row['status']) . "\")'>Update</a> | <a href='admin.php?Diterima=" . $row['id'] . "'>Terima</a> | <a href='admin.php?Ditolak=" . $row['id'] . "'>Tolak</a> | <a href='javascript:void(0)' onclick=\"confirmDeletion(event, " . $row['id'] . ")\">Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No records found.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </main>

    <!-- <footer>
        <div class="footer-content">
            <p>Halaman Admin-</p>
        </div>
    </footer> -->
</body>
</html>