<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    
    // Cek apakah kategori sudah ada
    $check_query = "SELECT id FROM categories WHERE name = '$name' OR slug = '$slug'";
    $check_result = $conn->query($check_query);
    
    if($check_result->num_rows > 0) {
        $_SESSION['message'] = "Kategori dengan nama tersebut sudah ada!";
        $_SESSION['message_type'] = "danger";
    } else {
        $query = "INSERT INTO categories (name, slug) VALUES ('$name', '$slug')";
        
        if($conn->query($query)) {
            $_SESSION['message'] = "Kategori berhasil ditambahkan!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menambahkan kategori!";
            $_SESSION['message_type'] = "danger";
        }
    }
}

header('Location: index.php');
exit(); 