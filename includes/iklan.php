<?php
// Fungsi untuk mendapatkan iklan
function getAds($position) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM iklan 
                WHERE posisi = ? 
                AND status = 'aktif'
                AND tanggal_mulai <= CURDATE()
                AND tanggal_selesai >= CURDATE()
                ORDER BY RAND()
                LIMIT 1";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $position);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($row = $result->fetch_assoc()) {
            if($row['tipe'] == 'gambar' && !empty($row['gambar'])) {
                return '<div class="iklan-container">
                    <img src="assets/images/iklan/' . $row['gambar'] . '" class="img-fluid">
                </div>';
            } elseif($row['tipe'] == 'html' && !empty($row['konten'])) {
                return $row['konten'];
            }
        }
    } catch (Exception $e) {
        error_log("Error in getAds: " . $e->getMessage());
    }
    return '';
}

// Fungsi untuk menampilkan iklan (backward compatibility)
function tampilkan_iklan($posisi) {
    echo getAds($posisi);
}
?>