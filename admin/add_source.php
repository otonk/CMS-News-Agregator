<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
include '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rss_url'])) {
        $url = $_POST['rss_url'];
        $stmt = $conn->prepare("INSERT INTO rss_sources (url) VALUES (?)");
        $stmt->bind_param("s", $url);
        $stmt->execute();
        $message = "RSS source added successfully!";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        deleteRSSSource($id);
        $message = "RSS source and related articles deleted successfully!";
    } elseif (isset($_POST['fetch'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("SELECT url FROM rss_sources WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($url);
        $stmt->fetch();
        $stmt->close();
        fetchRSS($url, $id);
        $message = "Articles fetched successfully!";
    }
}

// Mengambil daftar sumber RSS
$sources = $conn->query("SELECT * FROM rss_sources");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Add RSS Source</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <header>
        <h1>Add RSS Source</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="site_settings.php">Site Settings</a></li>
                <li><a href="manage_articles.php">Manage Articles</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Add a New RSS Source</h2>
            <?php if (isset($message)) : ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="rss_url">RSS URL:</label>
                    <input type="url" id="rss_url" name="rss_url" required>
                </div>
                <button type="submit">Add RSS Source</button>
            </form>
        </section>
        <section>
            <h2>Current RSS Sources</h2>
            <ul>
                <?php while ($row = $sources->fetch_assoc()) : ?>
                    <li>
                        <?php echo $row['url']; ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="fetch">Fetch</button>
                            <button type="submit" name="delete">Delete</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>
</body>

</html>
