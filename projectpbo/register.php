<?php
include 'koneksi.php'; // Pastikan koneksi ke database ada

session_start();

$error_message = ''; // Variabel untuk menyimpan pesan kesalahan
$success_message = ''; // Variabel untuk menyimpan pesan sukses

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

    // Validasi
    if (strlen($password) < 6) {
        $error_message = "Password harus minimal 6 karakter.";
    } elseif (!preg_match('/^[A-Z]/', $username)) {
        $error_message = "Username harus diawali dengan huruf kapital.";
    } elseif (strlen($phone) > 13) {
        $error_message = "Nomor telepon maksimal 13 digit.";
    } else {
        // Memeriksa apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM registrasi WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            // Hash password sebelum disimpan ke database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Memproses data dan menyimpan ke database
            $stmt = $conn->prepare("INSERT INTO registrasi (username, password, email, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $hashed_password, $email, $phone);

            if ($stmt->execute()) {
                // Menampilkan pesan sukses dengan hitungan mundur
                $success_message = "
                    <p>Registrasi berhasil! Silahkan login ulang dalam <span id='countdown'>3</span> detik...</p>
                    <script>
                        var countdownElement = document.getElementById('countdown');
                        var countdown = 3;

                        var interval = setInterval(function() {
                            countdown--;
                            countdownElement.textContent = countdown;

                            if (countdown === 0) {
                                clearInterval(interval);
                                window.location.href = 'login.php'; // Redirect ke halaman login
                            }
                        }, 1000); // Menghitung mundur setiap 1 detik
                    </script>
                ";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('latar.jpeg'); /* Ganti dengan path ke gambar latar belakang Anda */
            background-size: cover;
            background-position: center;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8); 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 20px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #808588;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #87CEEB;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-top: 10px;
        }
        p {
            text-align: center;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function validateForm(event) {
            event.preventDefault(); // Mencegah pengiriman form

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const phone = document.getElementById('phone').value;
            let error_message = '';

            if (password.length < 6) {
                error_message = "Password harus minimal 6 karakter.";
            } else if (!/^[A-Z]/.test(username)) {
                error_message = "Username harus diawali dengan huruf kapital.";
            } else if (phone.length > 13) {
                error_message = "Nomor telepon maksimal 13 digit.";
            }

            // Menampilkan pesan kesalahan jika ada
            const errorMessageElement = document.querySelector('.error-message');
            if (error_message) {
                errorMessageElement.textContent = error_message;
            } else {
                document.querySelector('form').submit(); // Mengirim form jika tidak ada kesalahan
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form action="register.php" method="POST" onsubmit="validateForm(event)">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="phone">No Telepon:</label>
            <input type="text" id="phone" name="phone" required>
            <button type="submit">Register</button>
        </form>
        <p class="error-message"></p>
        <!-- Tampilkan pesan sukses jika registrasi berhasil -->
        <?php if ($success_message): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>