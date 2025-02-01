<?php
// Tentukan base URL untuk admin
$base_url = '/learning/website/admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Portal Berita</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_url; ?>/dashboard.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>/berita/index.php">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>/kategori/index.php">Kategori</a>
                    </li>
                    <?php if(isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>/users/index.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>/iklan/index.php">Iklan</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            <?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : ''; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo $base_url; ?>/profile.php">Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo $base_url; ?>/logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html> 