<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';
require_once '../includes/header.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM news WHERE category_id = c.id) as total_news 
          FROM categories c 
          ORDER BY c.name 
          LIMIT $start, $limit";
$result = $conn->query($query);

// Total pages
$total_query = "SELECT COUNT(*) as total FROM categories";
$total_result = $conn->query($total_query)->fetch_assoc();
$total_pages = ceil($total_result['total'] / $limit);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Kategori</h2>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
            <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th>Jumlah Berita</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['slug']; ?></td>
                            <td><?php echo $row['total_news']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-category" 
                                        data-id="<?php echo $row['id']; ?>"
                                        data-name="<?php echo $row['name']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if($row['total_news'] == 0): ?>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="create.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="update.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_category_id">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" id="edit_category_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle edit button click
    $('.edit-category').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#edit_category_id').val(id);
        $('#edit_category_name').val(name);
        $('#editCategoryModal').modal('show');
    });
});
</script>

<?php require_once '../includes/footer.php'; ?> 