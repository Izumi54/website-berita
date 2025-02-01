<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Update iklan
if(isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $posisi = $_POST['posisi'];
    $tipe = $_POST['tipe'];
    $status = $_POST['status'];
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $harga = $_POST['harga'];
    $pengiklan = $_POST['pengiklan'];
    $kontak = $_POST['kontak'];
    
    if($tipe == 'html') {
        $konten = $_POST['konten_html'];
        $sql = "UPDATE iklan SET judul=?, posisi=?, tipe=?, konten=?, status=?, 
                tanggal_mulai=?, tanggal_selesai=?, harga=?, pengiklan=?, kontak=? 
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssdssi", $judul, $posisi, $tipe, $konten, $status, 
                         $mulai, $selesai, $harga, $pengiklan, $kontak, $id);
    } else {
        // Handle gambar baru jika diupload
        if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $target_dir = "../../assets/images/iklan/";
            $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if(move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                // Hapus gambar lama jika ada
                $sql = "SELECT gambar FROM iklan WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                if($row['gambar'] && file_exists($target_dir . $row['gambar'])) {
                    unlink($target_dir . $row['gambar']);
                }
                
                $sql = "UPDATE iklan SET judul=?, posisi=?, tipe=?, gambar=?, status=?, 
                        tanggal_mulai=?, tanggal_selesai=?, harga=?, pengiklan=?, kontak=? 
                        WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssdssi", $judul, $posisi, $tipe, $new_filename, 
                                $status, $mulai, $selesai, $harga, $pengiklan, $kontak, $id);
            }
        } else {
            $sql = "UPDATE iklan SET judul=?, posisi=?, tipe=?, status=?, 
                    tanggal_mulai=?, tanggal_selesai=?, harga=?, pengiklan=?, kontak=? 
                    WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssdssi", $judul, $posisi, $tipe, $status, 
                            $mulai, $selesai, $harga, $pengiklan, $kontak, $id);
        }
    }
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Iklan berhasil diupdate!";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengupdate iklan!";
    }
}

// Get iklan data
$sql = "SELECT * FROM iklan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$iklan = $result->fetch_assoc();

if(!$iklan) {
    header("Location: index.php");
    exit();
}

$title = "Edit Iklan";
include '../includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Iklan</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Iklan</h6>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Iklan</label>
                    <input type="text" class="form-control" name="judul" value="<?= htmlspecialchars($iklan['judul']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Tipe Iklan</label>
                    <select class="form-control" name="tipe" id="tipeIklan" required>
                        <option value="gambar" <?= $iklan['tipe'] == 'gambar' ? 'selected' : '' ?>>Gambar</option>
                        <option value="html" <?= $iklan['tipe'] == 'html' ? 'selected' : '' ?>>HTML</option>
                    </select>
                </div>
                
                <div id="kontenGambar" <?= $iklan['tipe'] == 'html' ? 'style="display:none;"' : '' ?>>
                    <div class="form-group">
                        <label>Upload Gambar</label>
                        <?php if($iklan['gambar']): ?>
                            <div class="mb-2">
                                <img src="../../assets/images/iklan/<?= $iklan['gambar'] ?>" class="img-fluid" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="gambar" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                    </div>
                </div>
                
                <div id="kontenHTML" <?= $iklan['tipe'] == 'gambar' ? 'style="display:none;"' : '' ?>>
                    <div class="form-group">
                        <label>Konten HTML</label>
                        <textarea class="form-control" name="konten_html" rows="5"><?= htmlspecialchars($iklan['konten']) ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pengiklan</label>
                            <input type="text" class="form-control" name="pengiklan" value="<?= htmlspecialchars($iklan['pengiklan']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kontak</label>
                            <input type="text" class="form-control" name="kontak" value="<?= htmlspecialchars($iklan['kontak']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" value="<?= $iklan['tanggal_mulai'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai" value="<?= $iklan['tanggal_selesai'] ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Posisi</label>
                            <select class="form-control" name="posisi" required>
                                <option value="top" <?= $iklan['posisi'] == 'top' ? 'selected' : '' ?>>Atas Konten</option>
                                <option value="sidebar" <?= $iklan['posisi'] == 'sidebar' ? 'selected' : '' ?>>Sidebar</option>
                                <option value="content" <?= $iklan['posisi'] == 'content' ? 'selected' : '' ?>>Dalam Konten</option>
                                <option value="bottom" <?= $iklan['posisi'] == 'bottom' ? 'selected' : '' ?>>Bawah Konten</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="aktif" <?= $iklan['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $iklan['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" class="form-control" name="harga" value="<?= $iklan['harga'] ?>" required>
                </div>

                <a href="index.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" name="update" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tipeIklan').change(function() {
        if($(this).val() == 'gambar') {
            $('#kontenGambar').show();
            $('#kontenHTML').hide();
        } else {
            $('#kontenGambar').hide();
            $('#kontenHTML').show();
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>