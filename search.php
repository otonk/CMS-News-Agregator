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

$query = isset($_GET['query']) ? $_GET['query'] : '';
$search_query = "%" . $conn->real_escape_string($query) . "%";

// Mengambil artikel berdasarkan pencarian
$limit = 8; // Jumlah artikel per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_result = $conn->query("SELECT * FROM articles WHERE title LIKE '$search_query' OR content LIKE '$search_query' LIMIT $limit OFFSET $offset");
if (!$search_result) {
    die("Error retrieving search results: " . $conn->error);
}
$articles = $search_result->fetch_all(MYSQLI_ASSOC);

// Menghitung total hasil pencarian
$total_search_result = $conn->query("SELECT COUNT(*) as count FROM articles WHERE title LIKE '$search_query' OR content LIKE '$search_query'");
if (!$total_search_result) {
    die("Error counting search results: " . $conn->error);
}
$total_articles = $total_search_result->fetch_assoc()['count'];
$total_pages = ceil($total_articles / $limit);

include 'header.php'; 
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Untree.co">
    <link rel="shortcut icon" href="favicon.png">

    <meta name="description" content="<?php echo htmlspecialchars($site_description); ?>" />
    <meta name="keywords" content="bootstrap, bootstrap5" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/tiny-slider.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/glightbox.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/flatpickr.min.css">

    <title><?php echo htmlspecialchars($site_title); ?></title>
    <style>
        .featured-img {
            width: 100%;
            height: 200px; /* Sesuaikan tinggi sesuai kebutuhan */
            overflow: hidden;
            position: relative;
        }
        .featured-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .more-blog-post-img, .popular-post-img {
            width: 100%;
            height: 150px; /* Sesuaikan tinggi sesuai kebutuhan */
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .popular-article-content h4 {
            font-size: 14px; /* Ukuran font lebih kecil */
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .search-result-wrap .blog-entry-search-item img {
            width: 150px; /* Lebar proporsional */
            height: 100px; /* Tinggi otomatis mengikuti rasio */
            object-fit: cover;
            object-position: center;
        }
    </style>
</head>
<body>

<div class="section search-result-wrap">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="heading">Search: '<?php echo htmlspecialchars($query); ?>'</div>
            </div>
        </div>
        <div class="row posts-entry">
            <div class="col-lg-8">
                <?php if (empty($articles)): ?>
                    <p>No articles found.</p>
                <?php else: ?>
                    <?php foreach ($articles as $article) : ?>
                        <div class="blog-entry d-flex blog-entry-search-item">
                            <a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="img-link me-4">
                                <img src="<?php echo !empty($article['image']) ? htmlspecialchars($article['image']) : 'images/default.jpg'; ?>" alt="Image" class="img-fluid">
                            </a>
                            <div>
                                <span class="date"><?php echo date('Apr. jS, Y', strtotime($article['created_at'])); ?></span>
                                <h2><a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>"><?php echo htmlspecialchars($article['title']); ?></a></h2>
                                <p><?php echo htmlspecialchars(substr(strip_tags($article['content']), 0, 150)) . '...'; ?></p>
                                <p><a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="btn btn-sm btn-outline-primary">Read More</a></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Paginasi -->
                <div class="row text-start pt-5 border-top">
                    <div class="col-md-12">
                        <div class="custom-pagination">
                            <?php if ($page > 1): ?>
                                <a href="?query=<?php echo htmlspecialchars($query); ?>&page=<?php echo $page - 1; ?>">&laquo;</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?query=<?php echo htmlspecialchars($query); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?query=<?php echo htmlspecialchars($query); ?>&page=<?php echo $page + 1; ?>">&raquo;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- End Paginasi -->
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
