<?php
require_once '../auth.php';
require_once '../../includes/config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil info gambar
    $query = "SELECT image FROM ads WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ad = $result->fetch_assoc();
    
    // Hapus gambar
    if($ad['image']) {
        unlink("../../uploads/ads/" . $ad['image']);
    }
    
    // Hapus data
    $query = "DELETE FROM ads WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Iklan berhasil dihapus!";
    }
}

header("Location: index.php");
exit();