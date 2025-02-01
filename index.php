<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="container mt-4">
    <!-- Breaking News -->
    <div class="breaking-news mb-4">
        <div class="row">
            <div class="col-md-8">
                <?php
                $breaking_query = "SELECT n.*, c.name as category_name 
                                 FROM news n 
                                 LEFT JOIN categories c ON n.category_id = c.id 
                                 WHERE n.status = 'published' 
                                 ORDER BY n.created_at DESC 
                                 LIMIT 1";
                $breaking = $conn->query($breaking_query)->fetch_assoc();
                ?>
                <div class="card">
                    <img src="assets/images/news/<?php echo $breaking['image']; ?>" class="card-img-top">
                    <div class="card-body">
                        <span class="badge badge-danger"><?php echo $breaking['category_name']; ?></span>
                        <h2 class="mt-2">
                            <a href="berita.php?slug=<?php echo $breaking['slug']; ?>" class="text-dark">
                                <?php echo $breaking['title']; ?>
                            </a>
                        </h2>
                        <p class="text-muted">
                            <i class="far fa-clock"></i> 
                            <?php echo date('d M Y H:i', strtotime($breaking['created_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Berita Populer -->
            <div class="col-md-4">
                <div class="card">
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

    <!-- Trending Topics Section -->
    <div class="trending-topics mb-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-fire"></i> Trending Topics</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap">
                    <?php
                    // Query untuk mengambil kategori yang memiliki berita saja
                    $trending_query = "SELECT 
                                        c.name, 
                                        c.slug, 
                                        COUNT(n.id) as total_news,
                                        SUM(n.views) as total_views 
                                     FROM categories c 
                                     INNER JOIN news n ON c.id = n.category_id 
                                     WHERE n.status = 'published' 
                                     AND n.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                     GROUP BY c.id 
                                     HAVING total_news > 0
                                     ORDER BY total_views DESC 
                                     LIMIT 8";
                    
                    $trending_result = $conn->query($trending_query);
                    
                    // Tampilkan hanya kategori yang memiliki berita
                    while($topic = $trending_result->fetch_assoc()):
                    ?>
                    <a href="kategori.php?slug=<?php echo $topic['slug']; ?>" 
                       class="btn btn-outline-danger btn-sm m-1">
                        #<?php echo $topic['name']; ?> 
                        <span class="badge badge-danger"><?php echo $topic['total_news']; ?></span>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita Terbaru dengan Layout Full Width (Populer) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <?php
                    // Simpan ID berita yang sudah ditampilkan
                    $displayed_news_ids = array();
                    
                    // Query untuk berita terbaru yang paling banyak dilihat
                    $top_news_query = "SELECT DISTINCT n.*, 
                                            c.name as category_name,
                                            c.slug as category_slug,
                                            u.username as author
                                     FROM news n
                                     LEFT JOIN categories c ON n.category_id = c.id
                                     LEFT JOIN users u ON n.author_id = u.id
                                     WHERE n.status = 'published'
                                     ORDER BY n.views DESC, n.created_at DESC
                                     LIMIT 5";
                    
                    $top_news = $conn->query($top_news_query);
                    while($news = $top_news->fetch_assoc()):
                        $displayed_news_ids[] = $news['id'];
                    ?>
                    <div class="media p-3 border-bottom">
                        <?php if($news['image']): ?>
                        <img src="assets/images/news/<?php echo $news['image']; ?>" 
                             class="mr-3 rounded" 
                             alt="<?php echo $news['title']; ?>"
                             style="width: 200px; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="media-body">
                            <span class="badge badge-danger mb-1">
                                <?php echo $news['category_name']; ?>
                            </span>
                            <h5 class="mt-0">
                                <a href="berita.php?slug=<?php echo $news['slug']; ?>" 
                                   class="text-dark">
                                    <?php echo $news['title']; ?>
                                </a>
                            </h5>
                            <p class="text-muted mb-2">
                                <?php echo substr(strip_tags($news['content']), 0, 200) . '...'; ?>
                            </p>
                            <small class="text-muted">
                                <i class="far fa-user"></i> <?php echo $news['author']; ?> |
                                <i class="far fa-clock"></i> <?php echo date('d M Y', strtotime($news['created_at'])); ?> |
                                <i class="far fa-eye"></i> <?php echo number_format($news['views']); ?> kali dibaca
                            </small>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita Acak Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Berita Pilihan</h5>
                    <!-- <button id="loadMoreBtn" class="btn btn-outline-light btn-sm">
                        Lihat Selengkapnya
                    </button> -->
                </div>
                <div class="card-body">
                    <div class="row" id="randomNewsContainer">
                        <?php
                        // Query untuk berita acak, exclude berita yang sudah ditampilkan
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
                            $displayed_news_ids[] = $news['id'];
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
                                        <i class="far fa-eye"></i> <?php echo number_format($news['views']); ?> kali dibaca |
                                        <i class="far fa-clock"></i> <?php echo date('d M Y', strtotime($news['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita Terbaru Lainnya dengan Layout Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <!-- <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Berita Terbaru Lainnya</h5>
                </div> -->
                <div class="card-body p-0">
                    <?php
                    // Query untuk berita terbaru yang belum ditampilkan
                    $excluded_ids = implode(',', $displayed_news_ids);
                    $latest_news_query = "SELECT DISTINCT n.*, 
                                               c.name as category_name,
                                               c.slug as category_slug,
                                               u.username as author
                                        FROM news n
                                        LEFT JOIN categories c ON n.category_id = c.id
                                        LEFT JOIN users u ON n.author_id = u.id
                                        WHERE n.status = 'published'
                                        AND n.id NOT IN ($excluded_ids)
                                        ORDER BY n.created_at DESC
                                        LIMIT 5";
                    
                    $latest_news = $conn->query($latest_news_query);
                    while($news = $latest_news->fetch_assoc()):
                        $displayed_news_ids[] = $news['id'];
                    ?>
                    <div class="media p-3 border-bottom">
                        <?php if($news['image']): ?>
                        <img src="assets/images/news/<?php echo $news['image']; ?>" 
                             class="mr-3 rounded" 
                             alt="<?php echo $news['title']; ?>"
                             style="width: 200px; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="media-body">
                            <span class="badge badge-danger mb-1">
                                <?php echo $news['category_name']; ?>
                            </span>
                            <h5 class="mt-0">
                                <a href="berita.php?slug=<?php echo $news['slug']; ?>" 
                                   class="text-dark">
                                    <?php echo $news['title']; ?>
                                </a>
                            </h5>
                            <p class="text-muted mb-2">
                                <?php echo substr(strip_tags($news['content']), 0, 200) . '...'; ?>
                            </p>
                            <small class="text-muted">
                                <i class="far fa-user"></i> <?php echo $news['author']; ?> |
                                <i class="far fa-clock"></i> <?php echo date('d M Y', strtotime($news['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan CSS -->
<style>
.news-slider {
    position: relative;
    overflow: hidden;
}

.news-slider-wrapper {
    display: flex;
    transition: transform 0.3s ease;
}

.news-item {
    flex: 0 0 25%;
    padding: 0 10px;
    min-width: 250px;
}

@media (max-width: 992px) {
    .news-item {
        flex: 0 0 33.333%;
    }
}

@media (max-width: 768px) {
    .news-item {
        flex: 0 0 50%;
    }
}

@media (max-width: 576px) {
    .news-item {
        flex: 0 0 100%;
    }
}

.news-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.news-card .card-title a {
    text-decoration: none;
}

.news-card .card-title a:hover {
    color: var(--primary-color) !important;
}
</style>

<!-- Tambahkan JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk slider
    function initSlider(sliderId) {
        const slider = document.getElementById(sliderId);
        const wrapper = slider.querySelector('.news-slider-wrapper');
        const prevBtn = document.querySelector(`button[data-target="${sliderId}"].slider-prev`);
        const nextBtn = document.querySelector(`button[data-target="${sliderId}"].slider-next`);
        let position = 0;

        nextBtn.addEventListener('click', () => {
            const itemWidth = slider.querySelector('.news-item').offsetWidth;
            const visibleItems = Math.floor(slider.offsetWidth / itemWidth);
            const maxPosition = wrapper.children.length - visibleItems;
            
            if (position < maxPosition) {
                position++;
                wrapper.style.transform = `translateX(-${position * itemWidth}px)`;
            }
        });

        prevBtn.addEventListener('click', () => {
            const itemWidth = slider.querySelector('.news-item').offsetWidth;
            
            if (position > 0) {
                position--;
                wrapper.style.transform = `translateX(-${position * itemWidth}px)`;
            }
        });
    }

    // Inisialisasi semua slider
    document.querySelectorAll('.news-slider').forEach(slider => {
        initSlider(slider.id);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    let offset = 8; // Mulai dari index 8 karena sudah menampilkan 8 berita pertama
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const container = document.getElementById('randomNewsContainer');

    loadMoreBtn.addEventListener('click', function() {
        // Gunakan AJAX untuk mengambil berita tambahan
        fetch(`load_more_news.php?offset=${offset}`)
            .then(response => response.text())
            .then(data => {
                container.insertAdjacentHTML('beforeend', data);
                offset += 8; // Tambah offset untuk load berikutnya
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>

<!-- Simpan displayed_news_ids dalam session untuk load_more_news.php -->
<?php $_SESSION['displayed_news_ids'] = $displayed_news_ids; ?>

<?php require_once 'includes/footer.php'; ?> 