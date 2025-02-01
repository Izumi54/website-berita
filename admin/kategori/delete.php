<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cek apakah kategori masih memiliki berita
$check_query = "SELECT COUNT(*) as total FROM news WHERE category_id = $id";
$check_result = $conn->query($check_query)->fetch_assoc();

if($check_result['total'] > 0) {
    $_SESSION['message'] = "Tidak dapat menghapus kategori yang masih memiliki berita!";
    $_SESSION['message_type'] = "danger";
} else {
    $query = "DELETE FROM categories WHERE id = $id";
    
    if($conn->query($query)) {
        $_SESSION['message'] = "Kategori berhasil dihapus!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menghapus kategori!";
        $_SESSION['message_type'] = "danger";
    }
}

header('Location: index.php');
exit(); 