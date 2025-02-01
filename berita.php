<?php
require_once 'includes/config.php';
require_once 'includes/iklan.php';
require_once 'includes/header.php';

$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';

// Ambil detail berita
$query = "SELECT n.*, c.name as category_name, c.slug as category_slug, u.username as author 
          FROM news n 
          LEFT JOIN categories c ON n.category_id = c.id 
          LEFT JOIN users u ON n.author_id = u.id 
          WHERE n.slug = '$slug' AND n.status = 'published'";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$news = $result->fetch_assoc();

// Update view count
$conn->query("UPDATE news SET views = views + 1 WHERE id = {$news['id']}");

// Ambil berita terkait
$related_query = "SELECT * FROM news 
                 WHERE category_id = {$news['category_id']} 
                 AND id != {$news['id']} 
                 AND status = 'published' 
                 ORDER BY created_at DESC 
                 LIMIT 4";
$related_result = $conn->query($related_query);

// Di atas konten
?>
<?php echo getAds('top'); ?>

<div class="container mt-4">
    <div class="row">
        <!-- Konten Utama -->
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">
                        <a href="kategori.php?slug=<?php echo $news['category_slug']; ?>">
                            <?php echo $news['category_name']; ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $news['title']; ?></li>
                </ol>
            </nav>

            <article class="news-detail">
                <div class="article-header mb-4">
                    <h1 class="article-title"><?php echo $news['title']; ?></h1>
                    <div class="article-meta">
                        <span class="reading-time"></span> | 
                        <span class="views"><i class="far fa-eye"></i> <?php echo number_format($news['views']); ?> kali dibaca</span>
                    </div>
                </div>

                <div class="content-wrapper">
                    <div class="article-content">
                        <?php echo $news['content']; ?>
                        <?php tampilkan_iklan('content'); ?>
                    </div>
                </div>

                <!-- Social Share Buttons -->
                <div class="share-buttons mt-4">
                    <h5>Bagikan Berita:</h5>
                    <div class="d-flex flex-wrap align-items-center">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" 
                           class="btn btn-facebook m-1" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f"></i>
                            <span class="d-none d-md-inline ml-2">Facebook</span>
                        </a>
                        
                        <!-- Twitter/X -->
                        <a href="https://twitter.com/intent/tweet?url=<?php echo $current_url; ?>&text=<?php echo urlencode($news['title']); ?>" 
                           class="btn btn-twitter m-1" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-twitter"></i>
                            <span class="d-none d-md-inline ml-2">Twitter</span>
                        </a>
                        
                        <!-- WhatsApp -->
                        <a href="https://wa.me/?text=<?php echo urlencode($news['title'] . ' ' . $current_url); ?>" 
                           class="btn btn-whatsapp m-1" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp"></i>
                            <span class="d-none d-md-inline ml-2">WhatsApp</span>
                        </a>
                        
                        <!-- Telegram -->
                        <a href="https://t.me/share/url?url=<?php echo $current_url; ?>&text=<?php echo urlencode($news['title']); ?>" 
                           class="btn btn-telegram m-1" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-telegram-plane"></i>
                            <span class="d-none d-md-inline ml-2">Telegram</span>
                        </a>
                        
                        <!-- LinkedIn -->
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $current_url; ?>&title=<?php echo urlencode($news['title']); ?>" 
                           class="btn btn-linkedin m-1" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-linkedin-in"></i>
                            <span class="d-none d-md-inline ml-2">LinkedIn</span>
                        </a>
                        
                        <!-- Copy Link -->
                        <button class="btn btn-secondary m-1" onclick="copyLink()">
                            <i class="fas fa-link"></i>
                            <span class="d-none d-md-inline ml-2">Copy Link</span>
                        </button>
                    </div>
                </div>

                <!-- Add JavaScript for Copy Link functionality -->
                <script>
                function copyLink() {
                    // Create temporary input
                    const temp = document.createElement('input');
                    temp.value = '<?php echo $current_url; ?>';
                    document.body.appendChild(temp);
                    
                    // Select and copy
                    temp.select();
                    document.execCommand('copy');
                    
                    // Remove temporary input
                    document.body.removeChild(temp);
                    
                    // Show feedback
                    alert('Link berhasil disalin!');
                }
                </script>
            </article>

            <!-- Berita Terkait -->
            <?php if($related_result->num_rows > 0): ?>
            <div class="related-news mt-5">
                <h4>Berita Terkait</h4>
                <div class="row">
                    <?php while($related = $related_result->fetch_assoc()): ?>
                    <div class="col-md-3">
                        <div class="card mb-3">
                            <img src="assets/images/news/<?php echo $related['image']; ?>" 
                                 class="card-img-top" style="height: 120px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="berita.php?slug=<?php echo $related['slug']; ?>" class="text-dark">
                                        <?php echo $related['title']; ?>
                                    </a>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <?php echo getAds('sidebar'); ?>
            <!-- Berita Populer -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Berita Populer</h5>
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

<?php tampilkan_iklan('footer'); ?>

<?php require_once 'includes/footer.php'; ?> 