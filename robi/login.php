<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - GaleriKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-md">
    <h3 class="text-2xl font-bold text-center mb-6 text-blue-500">Login GaleriKu</h3>
    
    <?php if (isset($error_message)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?= $error_message ?>
      </div>
    <?php endif; ?>
    
    <form method="post">
      <div class="mb-4">
        <label for="email" class="block text-gray-700 mb-2">Email</label>
        <input type="email" id="email" name="email" required
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="mb-4">
        <label for="password" class="block text-gray-700 mb-2">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition">
        Login
      </button>
      
      <div class="mt-4 text-center text-gray-600">
        Belum punya akun? <a href="register.php" class="text-blue-500 hover:underline">Register</a>
      </div>
    </form>
  </div>
</body>
</html>