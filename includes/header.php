<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News LENS - LENSA MEDIA</title>
    <link rel="icon" href="assets/images/lens-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #DE2A68;
            --primary-dark: #C4235C;
            --primary-light: #FF3C7D;
            --secondary-color: #2A2A2A;
        }

        /* Header Styles */
        .site-header {
            padding: 20px 0;
            border-bottom: 2px solid var(--primary-color);
            background-color: #fff;
        }
        
        .site-logo {
            height: 60px;
            width: auto;
        }
        
        .site-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: var(--primary-color);
        }
        
        .site-tagline {
            font-size: 14px;
            color: var(--secondary-color);
            margin: 0;
        }
        
        /* Category Navigation */
        .category-nav {
            background-color: var(--primary-color);
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .category-nav .nav-link {
            color: #fff;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .category-nav .nav-link:hover {
            background-color: var(--primary-dark);
            color: #fff;
        }
        
        /* Search Form */
        .search-form {
            position: relative;
        }
        
        .search-form .form-control {
            border-radius: 25px;
            padding: 10px 20px;
            border: 2px solid var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .search-form .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(222, 42, 104, 0.25);
            border-color: var(--primary-color);
        }
        
        .search-form .btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            border-radius: 0 25px 25px 0;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0 25px;
        }
        
        .search-form .btn:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Breaking News */
        .breaking-news-ticker {
            background-color: var(--secondary-color);
        }
        
        .breaking-news-ticker .badge {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 500;
            padding: 5px 10px;
        }
        
        /* Custom Button Style */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Links */
        a {
            color: var(--primary-color);
        }
        
        a:hover {
            color: var(--primary-dark);
            text-decoration: none;
        }

        /* Active Navigation */
        .category-nav .nav-link.active {
            background-color: var(--primary-dark);
            color: #fff;
        }

        /* Hover Effects */
        .news-card:hover {
            border-color: var(--primary-color);
        }

        /* Custom Badge */
        .badge-primary {
            background-color: var(--primary-color);
        }

        /* Breaking News Links */
        .breaking-news-ticker a:hover {
            color: var(--primary-light) !important;
        }

        /* Dropdown Kategori Mobile */
        @media (max-width: 991px) {
            .category-nav .navbar-nav {
                width: 100%;
            }

            .category-dropdown {
                position: relative;
            }

            .category-dropdown-btn {
                color: #fff;
                padding: 12px 20px;
                background-color: var(--primary-color);
                border: none;
                width: 100%;
                text-align: left;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .category-dropdown-btn:hover,
            .category-dropdown-btn:focus {
                background-color: var(--primary-dark);
                color: #fff;
            }

            .category-dropdown-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: #fff;
                border: 1px solid var(--primary-color);
                border-radius: 0 0 4px 4px;
                z-index: 1000;
            }

            .category-dropdown-menu.show {
                display: block;
            }

            .category-dropdown-menu .nav-link {
                color: var(--secondary-color);
                padding: 10px 20px;
                border-bottom: 1px solid #eee;
            }

            .category-dropdown-menu .nav-link:hover {
                background-color: #f8f9fa;
                color: var(--primary-color);
            }
        }

        /* Penyesuaian warna untuk elemen lain */
        .btn-danger,
        .badge-danger,
        .bg-danger {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .text-danger {
            color: var(--primary-color) !important;
        }

        .btn-outline-danger {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .btn-outline-danger:hover {
            background-color: var(--primary-color) !important;
            color: #fff !important;
        }

        /* Dark mode styles */
        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --card-bg: #2d2d2d;
            --border-color: #404040;
            --link-color: #6ea8fe;
            --heading-color: #ffffff;
        }

        [data-theme="light"] {
            --bg-color: #ffffff;
            --text-color: #333333;
            --card-bg: #ffffff;
            --border-color: #dee2e6;
            --link-color: #0d6efd;
            --heading-color: #333333;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }

        [data-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-color);
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: var(--text-color);
        }

        [data-theme="dark"] h1, 
        [data-theme="dark"] h2, 
        [data-theme="dark"] h3, 
        [data-theme="dark"] h4, 
        [data-theme="dark"] h5, 
        [data-theme="dark"] h6 {
            color: var(--heading-color);
        }

        [data-theme="dark"] a {
            color: var(--link-color);
        }

        /* Reader Tools Styles */
        .reader-tools .btn {
            padding: 0.25rem 0.5rem;
        }

        .reader-tools .btn:hover {
            background-color: rgba(255,255,255,0.2);
        }

        /* Progress Bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: #fff;
            z-index: 1000;
            transition: width 0.2s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .reader-tools {
                margin-top: 0.5rem;
            }
            
            .form-inline {
                margin-bottom: 0.5rem;
            }
        }

        /* Fix button visibility in dark mode */
        [data-theme="dark"] .btn-outline-light {
            color: #ffffff;
            border-color: #ffffff;
        }

        [data-theme="dark"] .btn-outline-light:hover {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
        }

        /* Font size control */
        .content-wrapper {
            font-size: var(--font-size, 16px);
        }

        /* Reading Progress Bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: #dc3545;
            z-index: 1000;
            transition: width 0.2s ease;
        }

        /* Reading Time Estimate */
        .reading-time {
            font-style: italic;
            color: #6c757d;
        }

        /* Text-to-Speech controls */
        .tts-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: var(--card-bg);
            padding: 10px;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Reader Tools Styles */
        .reader-tools .btn {
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
        }

        .reader-tools .btn:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .reader-tools .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
        }

        /* Progress Bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: #fff;
            z-index: 1000;
            transition: width 0.2s ease;
        }

        /* Dark mode dropdown adjustments */
        [data-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-color);
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: rgba(255,255,255,0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .reader-tools {
                margin-top: 1rem;
                justify-content: center;
                width: 100%;
            }
        }

        /* Tooltip customization */
        .tooltip {
            font-size: 0.875rem;
        }

        .reader-tools .btn {
            white-space: nowrap;
        }
        
        .reader-tools .btn i {
            width: 16px;
            text-align: center;
        }

        @media (max-width: 991.98px) {
            .reader-tools {
                margin: 1rem 0;
                justify-content: center;
                width: 100%;
            }
            
            .form-inline {
                width: 100%;
                justify-content: center;
                margin-top: 1rem;
            }
        }

        /* Logo styles - tidak berubah di mode dark */
        .navbar-brand {
            color: #ffffff !important; /* Memastikan logo tetap putih */
        }

        /* Card styles */
        .card {
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .card-title {
            color: #ffffff;
        }

        [data-theme="dark"] .card-text {
            color: #ffffff;
        }

        [data-theme="dark"] .card-footer {
            background-color: rgba(0,0,0,0.2);
            border-top-color: var(--border-color);
        }

        [data-theme="dark"] .text-muted {
            color: #b0b0b0 !important;
        }

        /* General styles */
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        /* Dropdown styles */
        [data-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-color);
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: var(--text-color);
        }

        /* Heading styles */
        [data-theme="dark"] h1:not(.navbar-brand), 
        [data-theme="dark"] h2:not(.navbar-brand), 
        [data-theme="dark"] h3:not(.navbar-brand), 
        [data-theme="dark"] h4:not(.navbar-brand), 
        [data-theme="dark"] h5:not(.navbar-brand), 
        [data-theme="dark"] h6:not(.navbar-brand) {
            color: var(--heading-color);
        }

        /* Link styles */
        [data-theme="dark"] a:not(.navbar-brand) {
            color: var(--link-color);
        }

        /* Reader Tools Styles */
        .reader-tools .btn {
            padding: 0.25rem 0.5rem;
        }

        .reader-tools .btn:hover {
            background-color: rgba(255,255,255,0.2);
        }

        /* Progress Bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: #fff;
            z-index: 1000;
            transition: width 0.2s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .reader-tools {
                margin-top: 0.5rem;
            }
            
            .form-inline {
                margin-bottom: 0.5rem;
            }
        }

        /* Fix button visibility in dark mode */
        [data-theme="dark"] .btn-outline-light {
            color: #ffffff;
            border-color: #ffffff;
        }

        [data-theme="dark"] .btn-outline-light:hover {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
        }

        /* Card hover effect */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Progress Bar -->
    <div class="progress-bar" id="readingProgress"></div>

    <!-- Theme Toggle Button in Navbar -->
    <div class="navbar-nav ml-auto">
                        <div class="nav-item dropdown">
                            <button class="btn btn-link nav-link" id="themeToggle">
                                <i class="fas fa-moon"></i>
                            </button>
                        </div>
                        <div class="nav-item dropdown">
                            <button class="btn btn-link nav-link" id="fontSizeToggle">
                                <i class="fas fa-text-height"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" data-size="14">Kecil</a>
                                <a class="dropdown-item" href="#" data-size="16">Normal</a>
                                <a class="dropdown-item" href="#" data-size="18">Besar</a>
                                <a class="dropdown-item" href="#" data-size="20">Sangat Besar</a>
                            </div>
                        </div>
                    </div>

    <!-- Header dengan Logo -->
    <header class="site-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <?php
                        // Tentukan base path untuk logo
                        $current_path = $_SERVER['SCRIPT_NAME'];
                        $is_admin = strpos($current_path, '/admin/') !== false;
                        $logo_path = $is_admin ? '../../assets/images/lens-logo.png' : 'assets/images/lens-logo.png';
                        ?>
                        <img src="<?php echo $logo_path; ?>" alt="LENS News" class="site-logo mr-3" onerror="this.src='assets/images/lens-logo.png'">
                        <div>
                            <h1 class="site-title">News LENS</h1>
                            <p class="site-tagline">LENSA MEDIA</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 offset-md-4">
                    <form class="search-form" action="/learning/website/search.php" method="get">
                        <input class="form-control" type="search" name="q" placeholder="Cari berita...">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    
                </div>
            </div>
        </div>
    </header>

    <!-- Navigasi Kategori dengan Dropdown Mobile -->
    <nav class="category-nav">
        <div class="container">
            <ul class="nav justify-content-center d-none d-lg-flex">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" 
                       href="/learning/website/">Beranda</a>
                </li>
                <?php
                $categories_query = "SELECT * FROM categories ORDER BY name";
                $categories_result = $conn->query($categories_query);
                while($category = $categories_result->fetch_assoc()):
                    $isActive = (isset($_GET['slug']) && $_GET['slug'] == $category['slug']) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $isActive; ?>" 
                       href="/learning/website/kategori.php?slug=<?php echo $category['slug']; ?>">
                        <?php echo $category['name']; ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>

            <!-- Dropdown Kategori untuk Mobile -->
            <div class="d-lg-none category-dropdown">
                <button class="category-dropdown-btn" onclick="toggleCategoryMenu()">
                    <span>Kategori</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="category-dropdown-menu" id="categoryMenu">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" 
                       href="/learning/website/">Beranda</a>
                    <?php
                    // Reset pointer hasil query
                    $categories_result->data_seek(0);
                    while($category = $categories_result->fetch_assoc()):
                        $isActive = (isset($_GET['slug']) && $_GET['slug'] == $category['slug']) ? 'active' : '';
                    ?>
                    <a class="nav-link <?php echo $isActive; ?>" 
                       href="/learning/website/kategori.php?slug=<?php echo $category['slug']; ?>">
                        <?php echo $category['name']; ?>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </nav>
    
    


    <!-- Tambahkan script untuk dropdown -->
    <script>
        function toggleCategoryMenu() {
            const menu = document.getElementById('categoryMenu');
            menu.classList.toggle('show');
        }

        // Menutup dropdown saat mengklik di luar
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.category-dropdown');
            const menu = document.getElementById('categoryMenu');
            
            if (!dropdown.contains(event.target) && menu.classList.contains('show')) {
                menu.classList.remove('show');
            }
        });
    </script>

    <?php if(basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
    <div class="breaking-news-ticker py-2">
        <div class="container">
            <div class="d-flex align-items-center">
                <span class="badge mr-2">BREAKING</span>
                <marquee>
                    <?php
                    $breaking_query = "SELECT title, slug FROM news 
                                     WHERE status = 'published' 
                                     ORDER BY created_at DESC 
                                     LIMIT 5";
                    $breaking_result = $conn->query($breaking_query);
                    $breaking_news = [];
                    while($breaking = $breaking_result->fetch_assoc()) {
                        $breaking_news[] = '<a href="/learning/website/berita.php?slug=' . $breaking['slug'] . '" class="text-white">' . $breaking['title'] . '</a>';
                    }
                    echo implode(' &bull; ', $breaking_news);
                    ?>
                </marquee>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Initialize tooltips -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>
    <!-- Progress Bar -->
    <div class="progress-bar" id="readingProgress"></div>

    <!-- Include necessary scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/reader.js"></script>
    <?php if(basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
    <script src="assets/js/load-more.js"></script>
    <?php endif; ?>
</body>
</html> 