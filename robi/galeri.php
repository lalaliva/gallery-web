<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT gallery.*, users.username FROM gallery JOIN users ON gallery.user_id = users.id ORDER BY uploaded_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <title>Galeri Publik - GaleriKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen flex">
  <!-- Sidebar (same as index.php) -->
  <div class="w-64 bg-white shadow-lg fixed h-full flex flex-col">
    <!-- ... same sidebar content as index.php ... -->
  </div>

  <!-- Main Content -->
  <div class="flex-1 ml-64 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Galeri Publik</h1>
      <div class="flex space-x-4">
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg flex items-center space-x-2 transition">
          <i class="fas fa-arrow-left"></i>
          <span>Kembali</span>
        </a>
        <a href="upload.php" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
          <i class="fas fa-plus"></i>
          <span>Tambah Foto</span>
        </a>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300">
          <div class="relative pb-[75%]">
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="absolute h-full w-full object-cover" alt="<?= htmlspecialchars($row['title']) ?>">
          </div>
          <div class="p-4">
            <div class="flex justify-between items-start mb-2">
              <h5 class="font-semibold text-lg truncate"><?= htmlspecialchars($row['title']) ?></h5>
              <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">@<?= htmlspecialchars($row['username']) ?></span>
            </div>
            <p class="text-gray-600 text-sm line-clamp-2"><?= htmlspecialchars($row['description']) ?></p>
            <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
              <span class="text-xs text-gray-500">
                <i class="far fa-clock mr-1"></i>
                <?= date('d M Y', strtotime($row['uploaded_at'])) ?>
              </span>
              <div class="flex space-x-2">
                <button class="text-gray-400 hover:text-primary transition">
                  <i class="far fa-heart"></i>
                </button>
                <button class="text-gray-400 hover:text-primary transition">
                  <i class="far fa-comment"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>