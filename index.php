<?php
include 'includes/db.php';

// Mengambil pengaturan situs
$result = $conn->query("SELECT * FROM settings");
if (!$result) {
    die("Error retrieving settings: " . $conn->error);
}
$settings = $result->fetch_assoc();

$site_title = isset($settings['site_title']) ? htmlspecialchars($settings['site_title']) : 'Default Site Title';
$site_description = isset($settings['site_description']) ? htmlspecialchars($settings['site_description']) : 'Default Site Description';

$limit = 8; // Jumlah artikel per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menghitung total artikel
$total_articles_result = $conn->query("SELECT COUNT(*) as count FROM articles");
if (!$total_articles_result) {
    die("Error counting articles: " . $conn->error);
}
$total_articles = $total_articles_result->fetch_assoc()['count'];
$total_pages = ceil($total_articles / $limit);

// Mengambil artikel untuk halaman saat ini, diurutkan dari yang terbaru
$articles_result = $conn->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
if (!$articles_result) {
    die("Error retrieving articles: " . $conn->error);
}
$articles = $articles_result->fetch_all(MYSQLI_ASSOC);

// Mengambil artikel populer (top 5 berdasarkan views)
$popular_articles_result = $conn->query("SELECT * FROM articles ORDER BY views DESC LIMIT 5");
if (!$popular_articles_result) {
    die("Error retrieving popular articles: " . $conn->error);
}
$popular_articles = $popular_articles_result->fetch_all(MYSQLI_ASSOC);

include 'header.php'; 
?>

<!-- Start retroy layout blog posts -->
<section class="section bg-light">
    <div class="container">
        <div class="row align-items-stretch retro-layout">
            <?php foreach ($popular_articles as $article) : ?>
                <div class="col-md-4">
                    <a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="h-entry mb-30 v-height gradient">
                        <div class="featured-img">
                            <img src="<?php echo !empty($article['image']) ? htmlspecialchars($article['image']) : 'images/default.jpg'; ?>" alt="Image">
                        </div>
                        <div class="text">
                            <span class="date"><?php echo date('M. jS, Y', strtotime($article['created_at'])); ?></span>
                            <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- End retroy layout blog posts -->

<!-- Start posts-entry -->
<section class="section posts-entry">
    <div class="container">
        <div class="row">
            <?php foreach ($articles as $article) : ?>
                <div class="col-md-6 col-lg-3">
                    <div class="blog-entry">
                        <a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="img-link">
                            <div class="featured-img">
                                <img src="<?php echo !empty($article['image']) ? htmlspecialchars($article['image']) : 'images/default.jpg'; ?>" alt="Image">
                            </div>
                        </a>
                        <span class="date"><?php echo date('M. jS, Y', strtotime($article['created_at'])); ?></span>
                        <h2><a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>"><?php echo htmlspecialchars($article['title']); ?></a></h2>
                        <p><?php echo htmlspecialchars(substr(strip_tags($article['content']), 0, 150)) . '...'; ?></p>
                        <p><a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="read-more">Continue Reading</a></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>    
    </div>
</section>
<!-- End posts-entry -->

<!-- Pagination -->
<div class="container">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($page > 3): ?>
            <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
            <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($page + 2, $total_pages); $i++): ?>
            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages - 2): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a></li>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
<!-- End Pagination -->

<?php include 'footer.php'; ?>