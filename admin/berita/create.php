<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';
require_once '../includes/header.php';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // Handle image upload
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            // Generate unique filename
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = '../../assets/images/news/' . $new_filename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_filename;
            } else {
                $error = "Gagal mengupload gambar";
            }
        } else {
            $error = "Format file tidak diizinkan. Gunakan: " . implode(', ', $allowed);
        }
    }
    
    if(!isset($error)) {
        $query = "INSERT INTO news (title, slug, content, image, category_id, author_id, status, created_at) 
                  VALUES ('$title', '$slug', '$content', '$image', $category_id, {$_SESSION['admin_id']}, '$status', NOW())";
        
        if($conn->query($query)) {
            $_SESSION['message'] = "Berita berhasil ditambahkan!";
            $_SESSION['message_type'] = "success";
            header('Location: index.php');
            exit();
        } else {
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    }
}

// Ambil daftar kategori
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Berita Baru</h3>
        </div>
        <div class="card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Berita</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php while($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Konten</label>
                    <textarea name="content" id="editor" class="form-control" rows="10" required></textarea>
                </div>

                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="image" class="form-control-file" required>
                    <small class="text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF</small>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include CKEditor -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');
</script>

<?php require_once '../includes/footer.php'; ?> 