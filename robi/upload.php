<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables to avoid undefined variable errors
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    // File upload handling
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $error = "File bukan gambar.";
    }
    
    // Check file size (max 5MB)
    elseif ($_FILES["image"]["size"] > 5000000) {
        $error = "Ukuran file terlalu besar (maks 5MB).";
    }
    
    // Allow certain file formats
    elseif (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        $error = "Hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
    }
    
    // If everything is ok, try to upload file
    else {
        // Generate unique filename
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_path = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
            $query = "INSERT INTO gallery (user_id, title, description, image) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "isss", $user_id, $title, $description, $new_filename);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Foto berhasil diunggah!";
                header("Refresh: 2; URL=index.php");
            } else {
                $error = "Gagal menyimpan data: " . mysqli_error($conn);
            }
        } else {
            $error = "Maaf, terjadi kesalahan saat mengunggah file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <title>Upload Foto - GaleriKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-white shadow-lg fixed h-full flex flex-col">
    <div class="p-4 border-b border-gray-200">
      <h1 class="text-xl font-bold text-primary flex items-center">
        <i class="fas fa-camera-retro mr-2"></i> GaleriKu
      </h1>
    </div>
    
    <div class="p-4 border-b border-gray-200">
      <?php if (isset($_SESSION['username'])): ?>
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white">
            <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
          </div>
          <div>
            <p class="font-medium"><?= htmlspecialchars($_SESSION['username']) ?></p>
            <p class="text-xs text-gray-500">Member</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <nav class="flex-1 overflow-y-auto p-4">
      <ul class="space-y-2">
        <li>
          <a href="index.php" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
            <i class="fas fa-home"></i>
            <span>Beranda</span>
          </a>
        </li>
        <li>
          <a href="galeri.php" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
            <i class="fas fa-images"></i>
            <span>Galeri Publik</span>
          </a>
        </li>
        <li>
          <a href="upload.php" class="flex items-center space-x-3 p-2 rounded-lg bg-primary text-white">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Upload Foto</span>
          </a>
        </li>
      </ul>
    </nav>
    
    <div class="p-4 border-t border-gray-200">
      <a href="logout.php" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 text-red-500">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="flex-1 ml-64 p-8">
    <div class="max-w-3xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Upload Foto Baru</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg flex items-center space-x-2 transition">
          <i class="fas fa-arrow-left"></i>
          <span>Kembali</span>
        </a>
      </div>

      <div class="bg-white rounded-xl shadow-md p-6">
        <?php if (!empty($error)): ?>
          <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
            <div class="flex items-center">
              <i class="fas fa-exclamation-circle mr-2"></i>
              <p><?= $error ?></p>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
          <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
            <div class="flex items-center">
              <i class="fas fa-check-circle mr-2"></i>
              <p><?= $success ?></p>
            </div>
          </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="space-y-6">
          <div>
            <label class="block text-gray-700 font-medium mb-2" for="title">Judul Foto</label>
            <input type="text" id="title" name="title" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
          </div>
          
          <div>
            <label class="block text-gray-700 font-medium mb-2" for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
          </div>
          
          <div>
            <label class="block text-gray-700 font-medium mb-2" for="image">Pilih Foto</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
              <div class="flex flex-col items-center justify-center">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-500 mb-2">Seret & lepas file atau klik untuk memilih</p>
                <input type="file" id="image" name="image" required 
                       class="hidden" onchange="previewImage(this)">
                <label for="image" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg inline-block cursor-pointer transition">
                  Pilih File
                </label>
                <p class="text-gray-500 text-sm mt-2">Format: JPG, JPEG, PNG, GIF (maks 5MB)</p>
              </div>
              <div id="imagePreview" class="mt-4 hidden">
                <img id="preview" class="max-h-48 mx-auto rounded-lg">
              </div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3">
            <button type="reset" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg transition">
              Reset
            </button>
            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition">
              <i class="fas fa-cloud-upload-alt"></i>
              <span>Upload Foto</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function previewImage(input) {
      const preview = document.getElementById('preview');
      const previewContainer = document.getElementById('imagePreview');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.src = e.target.result;
          previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</body>
</html>