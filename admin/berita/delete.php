<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil informasi gambar sebelum menghapus
$query = "SELECT image FROM news WHERE id = $id";
$result = $conn->query($query);
$news = $result->fetch_assoc();

// Hapus gambar jika ada
if($news['image'] && file_exists('../../assets/images/news/' . $news['image'])) {
    unlink('../../assets/images/news/' . $news['image']);
}

// Hapus berita dari database
$query = "DELETE FROM news WHERE id = $id";
if($conn->query($query)) {
    $_SESSION['message'] = "Berita berhasil dihapus!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Gagal menghapus berita!";
    $_SESSION['message_type'] = "danger";
}

header('Location: index.php');
exit(); 