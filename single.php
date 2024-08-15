<?php
include 'includes/db.php';

// Mengambil pengaturan situs
$settings_result = $conn->query("SELECT * FROM settings");
if (!$settings_result) {
    die("Error retrieving settings: " . $conn->error);
}
$settings = $settings_result->fetch_assoc();

$site_title = isset($settings['site_title']) ? htmlspecialchars($settings['site_title']) : 'Default Site Title';
$site_description = isset($settings['site_description']) ? htmlspecialchars($settings['site_description']) : 'Default Site Description';

$slug = $_GET['slug'];
$stmt = $conn->prepare("SELECT * FROM articles WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

// Tambah jumlah tampilan
$update_views = $conn->prepare("UPDATE articles SET views = views + 1 WHERE slug = ?");
$update_views->bind_param("s", $slug);
$update_views->execute();

// Mengambil artikel populer (top 5 berdasarkan views)
$popular_articles_result = $conn->query("SELECT * FROM articles ORDER BY views DESC LIMIT 5");
if (!$popular_articles_result) {
    die("Error retrieving popular articles: " . $conn->error);
}
$popular_articles = $popular_articles_result->fetch_all(MYSQLI_ASSOC);

// Mengambil artikel lain untuk bagian "More Blog Posts"
$more_articles_result = $conn->query("SELECT * FROM articles WHERE slug != '$slug' ORDER BY created_at DESC LIMIT 4");
if (!$more_articles_result) {
    die("Error retrieving more articles: " . $conn->error);
}
$more_articles = $more_articles_result->fetch_all(MYSQLI_ASSOC);

// Mengambil data dari RSS
$rss_feed_url = 'https://example.com/rss'; // Ganti dengan URL RSS feed yang valid
$rss_feed = @simplexml_load_file($rss_feed_url);

include 'header.php';
?>

<div class="site-cover site-cover-sm same-height overlay single-page" style="background-image: url('<?php echo htmlspecialchars($article['image']); ?>');">
    <div class="container">
        <div class="row same-height justify-content-center">
            <div class="col-md-6">
                <div class="post-entry text-center">
                    <h1 class="mb-4"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="post-meta align-items-center text-center">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row blog-entries element-animate">
            <div class="col-md-12 col-lg-8 main-content">
                <div class="post-content-body article-content">
                    <?php echo htmlspecialchars_decode($article['content']); ?>
                </div>
                <!-- <div class="pt-5">
                    <p>Categories:  <a href="#">Food</a>, <a href="#">Travel</a>  Tags: <a href="#">#manila</a>, <a href="#">#asia</a></p>
                </div> -->
            </div>
            <!-- END main-content -->
            <div class="col-md-12 col-lg-4 sidebar">
                <div class="sidebar-box">
                    <h3 class="heading">Popular Posts</h3>
                    <div class="post-entry-sidebar">
                        <ul>
                            <?php foreach ($popular_articles as $row) : ?>
                                <li>
                                    <a href="single.php?slug=<?php echo htmlspecialchars($row['slug']); ?>">
                                        <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'images/default.jpg'; ?>" alt="Image placeholder" class="me-4 rounded popular-post-img">
                                        <div class="text popular-article-content">
                                            <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                                            <div class="post-meta">
                                                <span class="mr-2"><?php echo date('M. jS, Y', strtotime($row['created_at'])); ?> </span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <!-- END sidebar-box -->
                <div class="sidebar-box">
                    <h3 class="heading">Categories</h3>
                    <ul class="categories">
                        <?php if ($rss_feed && isset($rss_feed->channel->item)) : ?>
                            <?php foreach ($rss_feed->channel->item as $item) : ?>
                                <li><a href="<?php echo htmlspecialchars($item->link); ?>"><?php echo htmlspecialchars($item->title); ?></a></li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li>Failed to load RSS feed.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- END sidebar-box -->
            </div>
            <!-- END sidebar -->
        </div>
    </div>
</section>

<!-- Start posts-entry -->
<section class="section posts-entry posts-entry-sm bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-uppercase text-black">Berita Lainnya</div>
        </div>
        <div class="row">
            <?php foreach ($more_articles as $article) : ?>
                <div class="col-md-6 col-lg-3">
                    <div class="blog-entry">
                        <a href="single.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="img-link">
                            <div class="featured-img">
                                <img src="<?php echo !empty($article['image']) ? htmlspecialchars($article['image']) : 'images/default.jpg'; ?>" alt="Image" class="img-fluid more-blog-post-img">
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

<?php include 'footer.php'; ?>
