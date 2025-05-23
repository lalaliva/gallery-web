<?php
session_start();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <title>GaleriKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#6366f1',
            secondary: '#10b981',
            danger: '#ef4444',
            dark: '#1e293b',
            light: '#f8fafc'
          }
        }
      }
    }
  </script>
  <script>
    function confirmHapus() {
      return confirm("📸 Apakah kamu yakin ingin menghapus foto ini?\nTindakan ini tidak bisa dibatalkan.");
    }
  </script>
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
          <a href="index.php" class="flex items-center space-x-3 p-2 rounded-lg bg-primary text-white">
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
          <a href="upload.php" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Upload Foto</span>
          </a>
        </li>
      </ul>
    </nav>
    
    <div class="p-4 border-t border-gray-200">
      <?php if (isset($_SESSION['username'])): ?>
        <a href="logout.php" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 text-red-500">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      <?php else: ?>
        <a href="login.php" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
          <i class="fas fa-sign-in-alt"></i>
          <span>Login</span>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Main Content -->
  <div class="flex-1 ml-64 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Galeri Saya</h1>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="upload.php" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
          <i class="fas fa-plus"></i>
          <span>Tambah Foto</span>
        </a>
      <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
          $user_id = $_SESSION['user_id'];
          $result = mysqli_query($conn, "SELECT * FROM gallery WHERE user_id = $user_id ORDER BY id DESC");

          if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo '<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300">';
              echo '<div class="relative pb-[75%]">';
              echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" class="absolute h-full w-full object-cover">';
              echo '</div>';
              echo '<div class="p-4">';
              echo '<h3 class="font-semibold text-lg mb-1 truncate">' . htmlspecialchars($row['title']) . '</h3>';
              echo '<p class="text-gray-600 text-sm mb-3 line-clamp-2">' . htmlspecialchars($row['description']) . '</p>';
              echo '<div class="flex space-x-2">';
              echo '<a href="edit.php?id=' . $row['id'] . '" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1 transition">';
              echo '<i class="fas fa-edit text-xs"></i>';
              echo '<span>Edit</span>';
              echo '</a>';
              echo '<a href="hapus.php?id=' . $row['id'] . '" class="bg-danger hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1 transition" onclick="return confirmHapus()">';
              echo '<i class="fas fa-trash-alt text-xs"></i>';
              echo '<span>Hapus</span>';
              echo '</a>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
            }
          } else {
            echo '<div class="col-span-full text-center py-12">';
            echo '<div class="bg-white rounded-xl shadow-sm p-8 max-w-md mx-auto">';
            echo '<i class="fas fa-images text-5xl text-gray-300 mb-4"></i>';
            echo '<h3 class="text-xl font-medium text-gray-700 mb-2">Galeri Kosong</h3>';
            echo '<p class="text-gray-500 mb-4">Anda belum mengunggah foto apapun</p>';
            echo '<a href="upload.php" class="bg-primary hover:bg-indigo-700 text-white px-6 py-2 rounded-lg inline-flex items-center space-x-2 transition">';
            echo '<i class="fas fa-cloud-upload-alt"></i>';
            echo '<span>Upload Foto Pertama</span>';
            echo '</a>';
            echo '</div>';
            echo '</div>';
          }
        ?>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-xl shadow-sm p-8 max-w-md mx-auto text-center mt-12">
        <i class="fas fa-lock text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-medium text-gray-700 mb-2">Akses Terbatas</h3>
        <p class="text-gray-500 mb-4">Silakan login untuk melihat galeri Anda</p>
        <a href="login.php" class="bg-primary hover:bg-indigo-700 text-white px-6 py-2 rounded-lg inline-flex items-center space-x-2 transition">
          <i class="fas fa-sign-in-alt"></i>
          <span>Login Sekarang</span>
        </a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>