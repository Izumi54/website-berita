<?php
require_once '../includes/auth.php';
require_once '../../includes/config.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM news WHERE id = $id";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$news = $result->fetch_assoc();

if(isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $slug = strtolower(str_replace(' ', '-', $title));
    $content = $conn->real_escape_string($_POST['content']);
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    
    // Upload gambar baru jika ada
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $new_filename = time() . '.' . $filetype;
            $upload_path = '../../assets/images/news/' . $new_filename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Hapus gambar lama jika ada
                if($news['image'] && file_exists('../../assets/images/news/' . $news['image'])) {
                    unlink('../../assets/images/news/' . $news['image']);
                }
                $image_query = ", image = '$new_filename'";
            }
        }
    } else {
        $image_query = "";
    }
    
    $query = "UPDATE news SET 
              title = '$title', 
              slug = '$slug', 
              content = '$content', 
              category_id = $category_id, 
              status = '$status'
              $image_query 
              WHERE id = $id";
    
    if($conn->query($query)) {
        $_SESSION['message'] = "Berita berhasil diupdate!";
        $_SESSION['message_type'] = "success";
        header('Location: index.php');
        exit();
    } else {
        $error = "Terjadi kesalahan: " . $conn->error;
    }
}

// Ambil daftar kategori
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Berita</h3>
        </div>
        <div class="card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Berita</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $news['title']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php while($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($category['id'] == $news['category_id']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Konten</label>
                    <textarea name="content" id="editor" class="form-control" rows="10" required>
                        <?php echo $news['content']; ?>
                    </textarea>
                </div>

                <div class="form-group">
                    <label>Gambar</label>
                    <?php if($news['image']): ?>
                        <div class="mb-2">
                            <img src="../../assets/images/news/<?php echo $news['image']; ?>" 
                                 style="max-width: 200px;" class="img-thumbnail">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control-file" accept="image/*">
                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="draft" <?php echo ($news['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo ($news['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" name="submit" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>

<?php require_once '../includes/footer.php'; ?> 