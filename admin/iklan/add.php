<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $link = $_POST['link'];
    $position = $_POST['position'];
    $status = $_POST['status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/ads/' . $image);
        
        $stmt = $conn->prepare("INSERT INTO ads (title, image, link, position, status, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $image, $link, $position, $status, $start_date, $end_date);
        
        if($stmt->execute()) {
            header('Location: index.php?success=1');
            exit;
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tambah Iklan</h1>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Judul Iklan</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label>Gambar Iklan</label>
                    <input type="file" name="image" class="form-control" required 
                           accept="image/*">
                </div>
                
                <div class="mb-3">
                    <label>Link (URL)</label>
                    <input type="url" name="link" class="form-control">
                </div>
                
                <div class="mb-3">
                    <label>Posisi</label>
                    <select name="position" class="form-control" required>
                        <option value="top">Atas Konten</option>
                        <option value="sidebar">Sidebar</option>
                        <option value="content">Dalam Konten</option>
                        <option value="bottom">Bawah Konten</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 