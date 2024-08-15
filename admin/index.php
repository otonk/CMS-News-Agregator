<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../includes/db.php';

// Menghitung jumlah artikel
$article_count_result = $conn->query("SELECT COUNT(*) as count FROM articles");
$article_count = $article_count_result->fetch_assoc()['count'];

// Menghitung jumlah feed
$feed_count_result = $conn->query("SELECT COUNT(*) as count FROM rss_sources");
$feed_count = $feed_count_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>

<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="site_settings.php">Site Settings</a></li>
                <li><a href="manage_articles.php">Manage Articles</a></li>
                <li><a href="add_source.php">Add RSS Source</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="stat-card">
            <div>
                <h2><?php echo $article_count; ?></h2>
                <p>Total Articles</p>
            </div>
            <div>
                <h2><?php echo $feed_count; ?></h2>
                <p>Total Feeds</p>
            </div>
        </section>
        <section>
            <h2>Welcome to the Admin Dashboard</h2>
            <p>Use the navigation menu to manage users, articles, and site settings.</p>
        </section>
    </main>
</body>

</html>
