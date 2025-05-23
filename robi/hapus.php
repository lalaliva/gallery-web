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

if ($photo) {
    // Delete the image file
    if (file_exists("uploads/" . $photo['image'])) {
        unlink("uploads/" . $photo['image']);
    }
    
    // Delete the record from database
    $delete_query = "DELETE FROM gallery WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: index.php");
exit;
?>