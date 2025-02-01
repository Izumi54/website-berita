<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$keyword = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Pagination
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query dasar
$base_query = "FROM news n 
               LEFT JOIN categories c ON n.category_id = c.id 
               LEFT JOIN users u ON n.author_id = u.id 
               WHERE n.status = 'published'";

// Tambahkan filter pencarian
if(!empty($keyword)) {
    $base_query .= " AND (n.title LIKE '%$keyword%' OR n.content LIKE '%$keyword%')";
}
if($category > 0) {
    $base_query .= " AND n.category_id = $category";
}

// Query untuk hasil pencarian
$news_query = "SELECT n.*, c.name as category_name, c.slug as category_slug, u.username as author " 
            . $base_query 
            . " ORDER BY n.created_at DESC LIMIT $start, $limit";
$news_result = $conn->query($news_query);

// Total hasil pencarian
$total_query = "SELECT COUNT(*) as total " . $base_query;
$total_result = $conn->query($total_query)->fetch_assoc();
$total_pages = ceil($total_result['total'] / $limit);
?>

<div class="container mt-4">
    <!-- Form Pencarian -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kata Kunci</label>
                        <input type="text" name="q" class="form-control" value="<?php echo htmlspecialchars($keyword); ?>" 
                               placeholder="Cari berita...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php
                            $cat_query = "SELECT * FROM categories ORDER BY name";
                            $cat_result = $conn->query($cat_query);
                            while($cat = $cat_result->fetch_assoc()):
                            ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hasil Pencarian -->
    <div class="row">
        <div class="col-md-8">
            <h4 class="mb-4">
                Hasil Pencarian<?php echo !empty($keyword) ? ': "' . htmlspecialchars($keyword) . '"' : ''; ?>
                <small class="text-muted">(<?php echo number_format($total_result['total']); ?> hasil)</small>
            </h4>

            <?php if($news_result->num_rows > 0): ?>
                <div class="row">
                    <?php while($news = $news_result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <img src="assets/images/news/<?php echo $news['image']; ?>" 
                                 class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge badge-danger mb-2">
                                    <?php echo $news['category_name']; ?>
                                </span>
                                <h5 class="card-title">
                                    <a href="berita.php?slug=<?php echo $news['slug']; ?>" class="text-dark">
                                        <?php echo $news['title']; ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    <?php echo substr(strip_tags($news['content']), 0, 150) . '...'; ?>
                                </p>
                                <div class="meta-info small text-muted">
                                    <i class="far fa-user"></i> <?php echo $news['author']; ?> |
                                    <i class="far fa-clock"></i> <?php echo date('d M Y', strtotime($news['created_at'])); ?> |
                                    <i class="far fa-eye"></i> <?php echo number_format($news['views']); ?> views
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?q=<?php echo urlencode($keyword); ?>&category=<?php echo $category; ?>&page=<?php echo $page - 1; ?>">Previous</a>
                        </li>
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?q=<?php echo urlencode($keyword); ?>&category=<?php echo $category; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?q=<?php echo urlencode($keyword); ?>&category=<?php echo $category; ?>&page=<?php echo $page + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info">
                    Tidak ada hasil yang ditemukan untuk pencarian Anda.
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Berita Terpopuler -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Berita Terpopuler</h5>
                </div>
                <div class="card-body p-0">
                    <?php
                    $popular_query = "SELECT * FROM news 
                                    WHERE status = 'published' 
                                    ORDER BY views DESC 
                                    LIMIT 5";
                    $popular_result = $conn->query($popular_query);
                    while($popular = $popular_result->fetch_assoc()):
                    ?>
                    <div class="media p-3 border-bottom">
                        <img src="assets/images/news/<?php echo $popular['image']; ?>" 
                             class="mr-3" style="width: 64px; height: 64px; object-fit: cover;">
                        <div class="media-body">
                            <h6 class="mt-0">
                                <a href="berita.php?slug=<?php echo $popular['slug']; ?>" class="text-dark">
                                    <?php echo $popular['title']; ?>
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="far fa-eye"></i> <?php echo number_format($popular['views']); ?> views
                            </small>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 