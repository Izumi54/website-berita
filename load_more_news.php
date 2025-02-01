<?php
require_once 'includes/config.php';
session_start();

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 8;
$displayed_news_ids = isset($_SESSION['displayed_news_ids']) ? $_SESSION['displayed_news_ids'] : array();

// Query untuk berita acak berikutnya, exclude berita yang sudah ditampilkan
$excluded_ids = implode(',', $displayed_news_ids);
$random_news_query = "SELECT n.*, 
                           c.name as category_name,
                           c.slug as category_slug,
                           u.username as author
                    FROM news n
                    LEFT JOIN categories c ON n.category_id = c.id
                    LEFT JOIN users u ON n.author_id = u.id
                    WHERE n.status = 'published'
                    AND n.id NOT IN ($excluded_ids)
                    ORDER BY RAND()
                    LIMIT 8";

$random_news = $conn->query($random_news_query);

while($news = $random_news->fetch_assoc()):
    $_SESSION['displayed_news_ids'][] = $news['id']; // Update session dengan ID baru
?>
<div class="col-md-3 mb-4">
    <div class="card h-100 news-card">
        <img src="assets/images/news/<?php echo $news['image']; ?>" 
             class="card-img-top" 
             alt="<?php echo $news['title']; ?>"
             style="height: 180px; object-fit: cover;">
        <div class="card-body">
            <span class="badge badge-danger mb-2">
                <?php echo $news['category_name']; ?>
            </span>
            <h5 class="card-title">
                <a href="berita.php?slug=<?php echo $news['slug']; ?>" 
                   class="text-dark">
                    <?php echo strlen($news['title']) > 70 ? 
                          substr($news['title'], 0, 70) . '...' : 
                          $news['title']; ?>
                </a>
            </h5>
            <p class="card-text small text-muted mb-0">
                <i class="far fa-user"></i> <?php echo $news['author']; ?> |
                <i class="far fa-clock"></i> <?php echo date('d M Y', strtotime($news['created_at'])); ?>
            </p>
        </div>
    </div>
</div>
<?php endwhile; ?> 