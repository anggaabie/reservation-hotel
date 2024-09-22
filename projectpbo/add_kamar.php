<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kamar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: #333;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 10px;
        }
        .form-container input[type="text"], .form-container input[type="number"], .form-container input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #808588;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #808588;
        }
        .button-group {
        display: flex;
        justify-content: flex-start;
        gap: 10px; 
    }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Tambah Kamar Baru</h2>
    <form action="process_add_kamar.php" method="POST" enctype="multipart/form-data">
    <label for="room_number">Nomor Kamar:</label>
    <input type="text" name="room_number" required><br>

    <label for="price">Harga (Rp):</label>
    <input type="number" name="price" required><br>

    <label for="facilities">Fasilitas:</label>
    <textarea name="facilities" required></textarea><br>

    <label for="image">Gambar Kamar:</label>
    <input type="file" name="image" required><br>

    <label for="location">Lokasi:</label>
    <input type="text" name="location" required><br>

    <input type="submit" value="Tambah Kamar">
</form>
</div>
    </form>
</div>

</body>
</html>
