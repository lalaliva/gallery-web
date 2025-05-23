<?php
include 'koneksi.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_email);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $error_message = "Email sudah digunakan. Silakan gunakan email lain.";
    } else {
        if (strlen($_POST['password']) < 6) {
            $error_message = "Password harus minimal 6 karakter.";
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error_message = "Register gagal: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register - GaleriKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-md">
    <h3 class="text-2xl font-bold text-center mb-6 text-blue-500">Register GaleriKu</h3>
    
    <?php if ($error_message): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?= $error_message ?>
      </div>
    <?php endif; ?>
    
    <?php if ($success_message): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?= $success_message ?>
      </div>
      <div class="text-center mt-4">
        <a href="login.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition inline-block">
          Login Sekarang
        </a>
      </div>
    <?php else: ?>
    
    <form method="post">
      <div class="mb-4">
        <label for="username" class="block text-gray-700 mb-2">Username</label>
        <input type="text" id="username" name="username" required
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="mb-4">
        <label for="email" class="block text-gray-700 mb-2">Email</label>
        <input type="email" id="email" name="email" required
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="mb-4">
        <label for="password" class="block text-gray-700 mb-2">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p class="text-gray-500 text-sm mt-1">Password minimal 6 karakter</p>
      </div>
      
      <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition">
        Register
      </button>
      
      <div class="mt-4 text-center text-gray-600">
        Sudah punya akun? <a href="login.php" class="text-blue-500 hover:underline">Login</a>
      </div>
    </form>
    
    <?php endif; ?>
  </div>
</body>
</html>