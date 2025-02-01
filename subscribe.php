<?php
/**
 * Subscribe Handler
 * 
 * File ini menangani proses subscription newsletter
 * 
 * @package LENS News
 * @author Your Name
 * @version 1.0
 */

// Include database connection
require_once 'includes/config.php';

// Set header untuk response JSON
header('Content-Type: application/json');

// Terima data JSON
$data = json_decode(file_get_contents('php://input'), true);
$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);

// Validasi email
if (!$email) {
    echo json_encode([
        'success' => false,
        'message' => 'Email tidak valid'
    ]);
    exit;
}

try {
    // Cek apakah email sudah terdaftar
    $check_query = "SELECT id FROM subscribers WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email sudah terdaftar'
        ]);
        exit;
    }
    
    // Insert email baru
    $insert_query = "INSERT INTO subscribers (email, status, created_at) VALUES (?, 'active', NOW())";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('s', $email);
    
    if ($stmt->execute()) {
        // Kirim email konfirmasi
        $to = $email;
        $subject = "Selamat Datang di Newsletter LENS News";
        $message = "Terima kasih telah berlangganan newsletter LENS News.\n\n";
        $message .= "Anda akan mendapatkan update berita terkini dari kami.\n";
        $headers = "From: noreply@lensnews.com";
        
        mail($to, $subject, $message, $headers);
        
        echo json_encode([
            'success' => true,
            'message' => 'Berhasil berlangganan'
        ]);
    } else {
        throw new Exception('Gagal menyimpan data');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem'
    ]);
} 