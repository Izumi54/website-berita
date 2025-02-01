<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';

// Ambil informasi kategori
$category_query = "SELECT * FROM categories WHERE slug = '$slug'";
$category_result = $conn->query($category_query);

if($category_result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$category = $category_result->fetch_assoc();

// Pagination
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Ambil berita berdasarkan kategori
$news_query = "SELECT n.*, u.username as author 
               FROM news n 
               LEFT JOIN users u ON n.author_id = u.id 
               WHERE n.category_id = {$category['id']} 
               AND n.status = 'published' 
               ORDER BY n.created_at DESC 
               LIMIT $start, $limit";
$news_result = $conn->query($news_query);

// Total pages
$total_query = "SELECT COUNT(*) as total FROM news 
                WHERE category_id = {$category['id']} 
                AND status = 'published'";
$total_result = $conn->query($total_query)->fetch_assoc();
$total_pages = ceil($total_result['total'] / $limit);
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active"><?php echo $category['name']; ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Konten Utama -->
        <div class="col-md-8">
            <h2 class="mb-4">Kategori: <?php echo $category['name']; ?></h2>

            <div class="row">
                <?php while($news = $news_result->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <img src="assets/images/news/<?php echo $news['image']; ?>" 
                             class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
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
                        <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Kategori Lainnya -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Kategori Lainnya</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php
                        $other_categories = $conn->query("SELECT * FROM categories WHERE id != {$category['id']} ORDER BY name");
                        while($other = $other_categories->fetch_assoc()):
                        ?>
                        <li class="mb-2">
                            <a href="kategori.php?slug=<?php echo $other['slug']; ?>" class="text-dark">
                                <?php echo $other['name']; ?>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Berita Populer -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Berita Populer</h5>
                </div>
                <div class="card-body p-0">
                    <?php
                    $popular_query = "SELECT * FROM news 
                                    WHERE status = 'published' 
                                    AND category_id = {$category['id']} 
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