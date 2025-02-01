<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';

// Tambah iklan baru
if(isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $posisi = $_POST['posisi'];
    $tipe = $_POST['tipe'];
    $status = $_POST['status'];
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $harga = $_POST['harga'];
    $pengiklan = $_POST['pengiklan'];
    $kontak = $_POST['kontak'];
    
    // Handle konten berdasarkan tipe
    if($tipe == 'html') {
        $konten = $_POST['konten_html'];
        $gambar = '';
    } else {
        $konten = '';
        // Upload gambar
        if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $target_dir = "../../assets/images/iklan/";
            $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if(move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $new_filename;
            }
        }
    }

    $sql = "INSERT INTO iklan (judul, posisi, tipe, konten, gambar, status, tanggal_mulai, tanggal_selesai, harga, pengiklan, kontak) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssdss", $judul, $posisi, $tipe, $konten, $gambar, $status, $mulai, $selesai, $harga, $pengiklan, $kontak);
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Iklan berhasil ditambahkan!";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan iklan!";
    }
}

$title = "Manajemen Iklan";
include '../includes/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Iklan</h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#tambahIklanModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Iklan
        </button>
    </div>

    <?php 
    if(isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if(isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
    ?>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Iklan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Pengiklan</th>
                            <th>Tipe</th>
                            <th>Posisi</th>
                            <th>Status</th>
                            <th>Periode</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM iklan ORDER BY id DESC";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= htmlspecialchars($row['pengiklan']) ?></td>
                            <td><?= ucfirst($row['tipe']) ?></td>
                            <td><?= htmlspecialchars($row['posisi']) ?></td>
                            <td>
                                <span class="badge badge-<?= $row['status'] == 'aktif' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($row['tanggal_mulai'])) ?> - 
                                <?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?>
                            </td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm mb-1" data-toggle="modal" data-target="#previewModal<?= $row['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm mb-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm mb-1" 
                                   onclick="return confirm('Yakin ingin menghapus iklan ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Preview Modal -->
                        <div class="modal fade" id="previewModal<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Preview Iklan</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?php if($row['tipe'] == 'gambar' && $row['gambar']): ?>
                                            <img src="../../assets/images/iklan/<?= $row['gambar'] ?>" class="img-fluid">
                                        <?php else: ?>
                                            <?= $row['konten'] ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Iklan -->
<div class="modal fade" id="tambahIklanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Iklan Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul Iklan</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipe Iklan</label>
                        <select class="form-control" name="tipe" id="tipeIklan" required>
                            <option value="gambar">Gambar</option>
                            <option value="html">HTML</option>
                        </select>
                    </div>
                    
                    <div id="kontenGambar">
                        <div class="form-group">
                            <label>Upload Gambar</label>
                            <input type="file" class="form-control" name="gambar" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                        </div>
                    </div>
                    
                    <div id="kontenHTML" style="display:none;">
                        <div class="form-group">
                            <label>Konten HTML</label>
                            <textarea class="form-control" name="konten_html" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pengiklan</label>
                                <input type="text" class="form-control" name="pengiklan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kontak</label>
                                <input type="text" class="form-control" name="kontak" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tanggal_selesai" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Posisi</label>
                                <select class="form-control" name="posisi" required>
                                    <option value="top">Atas Konten</option>
                                    <option value="sidebar">Sidebar</option>
                                    <option value="content">Dalam Konten</option>
                                    <option value="bottom">Bawah Konten</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" class="form-control" name="harga" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#dataTable').DataTable();

    // Toggle konten berdasarkan tipe iklan
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