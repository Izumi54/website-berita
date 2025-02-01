<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';
require_once '../includes/header.php';

// Cek apakah user adalah admin
if($_SESSION['admin_role'] !== 'admin') {
    header('Location: ../dashboard.php');
    exit();
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM news WHERE author_id = u.id) as total_news 
          FROM users u 
          ORDER BY u.created_at DESC 
          LIMIT $start, $limit";
$result = $conn->query($query);

// Total pages
$total_query = "SELECT COUNT(*) as total FROM users";
$total_result = $conn->query($total_query)->fetch_assoc();
$total_pages = ceil($total_result['total'] / $limit);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Users</h2>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-plus"></i> Tambah User
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
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Total Berita</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $row['role'] == 'admin' ? 'danger' : 
                                        ($row['role'] == 'editor' ? 'success' : 'info'); 
                                ?>">
                                    <?php echo $row['role']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['total_news']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-user" 
                                        data-id="<?php echo $row['id']; ?>"
                                        data-username="<?php echo $row['username']; ?>"
                                        data-email="<?php echo $row['email']; ?>"
                                        data-role="<?php echo $row['role']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if($row['id'] != $_SESSION['admin_id']): ?>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
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

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="create.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
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

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="update.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_user_id">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control">
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
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
    $('.edit-user').click(function() {
        var id = $(this).data('id');
        var username = $(this).data('username');
        var email = $(this).data('email');
        var role = $(this).data('role');
        
        $('#edit_user_id').val(id);
        $('#edit_username').val(username);
        $('#edit_email').val(email);
        $('#edit_role').val(role);
        $('#editUserModal').modal('show');
    });
});
</script>

<?php require_once '../includes/footer.php'; ?> 