function getAds($position) {
    global $conn;
    $today = date('Y-m-d');
    
    $stmt = $conn->prepare("SELECT * FROM ads 
                           WHERE position = ? 
                           AND status = 'active'
                           AND start_date <= ?
                           AND end_date >= ?
                           ORDER BY RAND()
                           LIMIT 1");
    
    $stmt->bind_param("sss", $position, $today, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $ad = $result->fetch_assoc();
        return '<div class="advertisement mb-4">
                    <a href="'.$ad['link'].'" target="_blank">
                        <img src="uploads/ads/'.$ad['image'].'" 
                             alt="'.$ad['title'].'"
                             class="img-fluid">
                    </a>
                </div>';
    }
    return '';
} 