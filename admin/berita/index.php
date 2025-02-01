<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';
require_once '../includes/header.php';

// Filter parameters
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Build query with filters
$where_conditions = ["n.status != ''"];  // Base condition
$params = [];
$types = '';

if ($category > 0) {
    $where_conditions[] = "n.category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if ($date_from) {
    $where_conditions[] = "DATE(n.created_at) >= ?";
    $params[] = $date_from;
    $types .= 's';
}

if ($date_to) {
    $where_conditions[] = "DATE(n.created_at) <= ?";
    $params[] = $date_to;
    $types .= 's';
}

if ($status) {
    $where_conditions[] = "n.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($search) {
    $where_conditions[] = "(n.title LIKE ? OR n.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$where_clause = implode(' AND ', $where_conditions);

// Prepare the main query
$query = "SELECT n.*, c.name as category_name, u.username as author 
          FROM news n 
          LEFT JOIN categories c ON n.category_id = c.id 
          LEFT JOIN users u ON n.author_id = u.id 
          WHERE $where_clause
          ORDER BY n.created_at DESC 
          LIMIT ?, ?";

// Add pagination parameters
$params[] = $start;
$params[] = $limit;
$types .= 'ii';

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM news n WHERE $where_clause";
$stmt_total = $conn->prepare($total_query);
if ($params) {
    // Remove the last two parameters (LIMIT parameters)
    array_pop($params);
    array_pop($params);
    $types = substr($types, 0, -2);
    if ($params) {
        $stmt_total->bind_param($types, ...$params);
    }
}
$stmt_total->execute();
$total_result = $stmt_total->get_result()->fetch_assoc();
$total_pages = ceil($total_result['total'] / $limit);

// Get categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Berita</h2>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Berita
        </a>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Berita</h5>
        </div>
        <div class="card-body">
            <form method="get" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Kategori</label>
                        <select name="category" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control" 
                               value="<?php echo $date_from; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control" 
                               value="<?php echo $date_to; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="published" <?php echo $status == 'published' ? 'selected' : ''; ?>>
                                Published
                            </option>
                            <option value="draft" <?php echo $status == 'draft' ? 'selected' : ''; ?>>
                                Draft
                            </option>
                        </select>
                    </div>
                    <div class="col-md-9 mb-3">
                        <label>Cari</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari judul atau konten berita..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    <div class="card">
        <div class="card-body">
            <?php if($result->num_rows > 0): ?>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo $row['author']; ?></td>
                            <td>
                                <span class="badge badge-<?php echo $row['status'] == 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo number_format($row['views']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <?php else: ?>
            <div class="alert alert-info">
                Tidak ada berita yang ditemukan.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 