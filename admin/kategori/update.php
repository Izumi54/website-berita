<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    
    // Cek apakah kategori dengan nama yang sama sudah ada (kecuali kategori yang sedang diedit)
    $check_query = "SELECT id FROM categories WHERE (name = '$name' OR slug = '$slug') AND id != $id";
    $check_result = $conn->query($check_query);
    
    if($check_result->num_rows > 0) {
        $_SESSION['message'] = "Kategori dengan nama tersebut sudah ada!";
        $_SESSION['message_type'] = "danger";
    } else {
        $query = "UPDATE categories SET name = '$name', slug = '$slug' WHERE id = $id";
        
        if($conn->query($query)) {
            $_SESSION['message'] = "Kategori berhasil diupdate!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal mengupdate kategori!";
            $_SESSION['message_type'] = "danger";
        }
    }
}

header('Location: index.php');
exit(); 