<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if photo belongs to the user
$query = "SELECT * FROM gallery WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$photo = mysqli_fetch_assoc($result);

if (!$photo) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    
    // If new image is uploaded
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $error = "File bukan gambar.";
        } elseif ($_FILES["image"]["size"] > 5000000) {
            $error = "Ukuran file terlalu besar (maks 5MB).";
        } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $error = "Hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
        } else {
            // Generate unique filename
            $new_filename = uniqid() . '.' . $imageFileType;
            $target_path = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
                // Delete old image
                if (file_exists("uploads/" . $photo['image'])) {
                    unlink("uploads/" . $photo['image']);
                }
                
                $update_query = "UPDATE gallery SET title = ?, description = ?, image = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $new_filename, $id);
            } else {
                $error = "Maaf, terjadi kesalahan saat mengunggah file.";
            }
        }
    } else {
        // Update without changing image
        $update_query = "UPDATE gallery SET title = ?, description = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $id);
    }
    
    if (empty($error)) {
        if (mysqli_stmt_execute($stmt)) {
            $success = "Foto berhasil diperbarui!";
            header("Refresh: 2; URL=index.php");
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Foto - GaleriKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="bg-white shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
      <a href="index.php" class="text-blue-500 font-bold text-xl">GaleriKu</a>
      <div class="flex items-center space-x-4">
        <span class="text-gray-700">Hai, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">Logout</a>
      </div>
    </div>
  </nav>

  <div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-bold mb-6">Edit Foto</h2>
      
      <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?= $error ?>
        </div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          <?= $success ?>
        </div>
      <?php endif; ?>
      
      <form method="post" enctype="multipart/form-data">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2" for="title">Judul</label>
          <input type="text" id="title" name="title" required 
                 value="<?= htmlspecialchars($photo['title']) ?>"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="mb-4">
          <label class="block text-gray-700 mb-2" for="description">Deskripsi</label>
          <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($photo['description']) ?></textarea>
        </div>
        
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Foto Saat Ini</label>
          <img src="uploads/<?= htmlspecialchars($photo['image']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>" class="w-full h-48 object-cover mb-2 rounded">
          <label class="block text-gray-700 mb-2" for="image">Ganti Foto (opsional)</label>
          <input type="file" id="image" name="image" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <p class="text-gray-500 text-sm mt-1">Format: JPG, JPEG, PNG, GIF (maks 5MB)</p>
        </div>
        
        <div class="flex justify-end space-x-3">
          <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md transition">Batal</a>
          <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>