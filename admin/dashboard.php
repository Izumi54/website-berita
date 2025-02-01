<?php
require_once 'includes/auth.php';
require_once '../includes/config.php';
require_once 'includes/header.php';

// Mengambil statistik
$query_berita = "SELECT COUNT(*) as total FROM news";
$query_kategori = "SELECT COUNT(*) as total FROM categories";
$query_users = "SELECT COUNT(*) as total FROM users";

$berita = $conn->query($query_berita)->fetch_assoc();
$kategori = $conn->query($query_kategori)->fetch_assoc();
$users = $conn->query($query_users)->fetch_assoc();

// Query untuk mengambil detail berita
$query_detail_berita = "SELECT n.*, c.name as category_name, u.username as author 
                       FROM news n 
                       LEFT JOIN categories c ON n.category_id = c.id 
                       LEFT JOIN users u ON n.author_id = u.id 
                       ORDER BY n.created_at DESC";
$result_berita = $conn->query($query_detail_berita);

// Hitung total iklan aktif
$sql_iklan = "SELECT COUNT(*) as total FROM iklan WHERE status = 'aktif'";
$result_iklan = $conn->query($sql_iklan);
$total_iklan = $result_iklan->fetch_assoc()['total'];

// Hitung pendapatan iklan bulan ini
$sql_pendapatan = "SELECT SUM(harga) as total FROM iklan 
                   WHERE MONTH(tanggal_mulai) = MONTH(CURRENT_DATE())
                   AND YEAR(tanggal_mulai) = YEAR(CURRENT_DATE())";
$result_pendapatan = $conn->query($sql_pendapatan);
$pendapatan = $result_pendapatan->fetch_assoc()['total'];

// Hitung views berdasarkan periode
function getViews($period) {
    global $conn;
    $sql = "";
    switch($period) {
        case 'today':
            $sql = "SELECT SUM(views) as total FROM berita WHERE DATE(tanggal) = CURRENT_DATE()";
            break;
        case 'week':
            $sql = "SELECT SUM(views) as total FROM berita WHERE tanggal >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $sql = "SELECT SUM(views) as total FROM berita WHERE MONTH(tanggal) = MONTH(CURRENT_DATE())";
            break;
        case 'year':
            $sql = "SELECT SUM(views) as total FROM berita WHERE YEAR(tanggal) = YEAR(CURRENT_DATE())";
            break;
    }
    $result = $conn->query($sql);
    return $result->fetch_assoc()['total'] ?? 0;
}

// Get views per kategori
$sql_kategori_views = "SELECT k.nama_kategori, COUNT(b.id) as total_berita, SUM(b.views) as total_views 
                      FROM kategori k 
                      LEFT JOIN berita b ON k.id = b.id_kategori 
                      GROUP BY k.id 
                      ORDER BY total_views DESC";
$result_kategori = $conn->query($sql_kategori_views);

// Get data untuk grafik bulanan
$sql_monthly = "SELECT DATE_FORMAT(tanggal, '%Y-%m') as bulan, SUM(views) as total_views 
                FROM berita 
                WHERE tanggal >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
                ORDER BY bulan";
$result_monthly = $conn->query($sql_monthly);
$monthly_data = [];
while($row = $result_monthly->fetch_assoc()) {
    $monthly_data[] = $row;
}
?>

<div class="container mt-4">
    <h2>Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Berita</h5>
                    <h2><?php echo $berita['total']; ?></h2>
                    <a href="#detail-berita" class="text-white" data-toggle="collapse">Lihat Detail →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Kategori</h5>
                    <h2><?php echo $kategori['total']; ?></h2>
                    <a href="#detail-kategori" class="text-white" data-toggle="collapse">Lihat Detail →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2><?php echo $users['total']; ?></h2>
                    <a href="#detail-users" class="text-white" data-toggle="collapse">Lihat Detail →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Berita Collapse -->
    <div class="collapse mt-4" id="detail-berita">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Berita</h5>
                <button type="button" class="btn btn-sm btn-light" data-toggle="collapse" data-target="#detail-berita">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($berita = $result_berita->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $berita['title']; ?></td>
                                <td><?php echo $berita['category_name']; ?></td>
                                <td><?php echo $berita['author']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $berita['status'] == 'published' ? 'success' : 'warning'; ?>">
                                        <?php echo $berita['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($berita['views']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($berita['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Kategori Collapse -->
    <div class="collapse mt-4" id="detail-kategori">
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Kategori</h5>
                <button type="button" class="btn btn-sm btn-light" data-toggle="collapse" data-target="#detail-kategori">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Kategori</th>
                                <th>Jumlah Berita</th>
                                <th>Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query_detail_kategori = "SELECT c.*, 
                                                    (SELECT COUNT(*) FROM news WHERE category_id = c.id) as total_news 
                                                    FROM categories c 
                                                    ORDER BY c.name";
                            $result_kategori = $conn->query($query_detail_kategori);
                            while($kategori = $result_kategori->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $kategori['name']; ?></td>
                                <td><?php echo $kategori['total_news']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($kategori['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Users Collapse -->
    <div class="collapse mt-4" id="detail-users">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Users</h5>
                <button type="button" class="btn btn-sm btn-light" data-toggle="collapse" data-target="#detail-users">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Jumlah Berita</th>
                                <th>Tanggal Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query_detail_users = "SELECT u.*, 
                                                 (SELECT COUNT(*) FROM news WHERE author_id = u.id) as total_news 
                                                 FROM users u 
                                                 ORDER BY u.created_at DESC";
                            $result_users = $conn->query($query_detail_users);
                            while($user = $result_users->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?>">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td><?php echo $user['total_news']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 