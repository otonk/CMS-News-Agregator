<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_title = $_POST['site_title'];
    $site_description = $_POST['site_description'];

    $check_settings = $conn->query("SELECT * FROM settings");
    if ($check_settings->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE settings SET site_title = ?, site_description = ? WHERE id = 1");
    } else {
        $stmt = $conn->prepare("INSERT INTO settings (site_title, site_description) VALUES (?, ?)");
    }
    $stmt->bind_param("ss", $site_title, $site_description);

    if ($stmt->execute()) {
        $message = "Settings updated successfully!";
    } else {
        $message = "Failed to update settings.";
    }
}

$result = $conn->query("SELECT * FROM settings");
$settings = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Site Settings</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <header>
        <h1>Site Settings</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_articles.php">Manage Articles</a></li>
                <li><a href="add_source.php">Add RSS Source</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Update Site Settings</h2>
            <?php if (isset($message)) : ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="site_title">Site Title:</label>
                    <input type="text" id="site_title" name="site_title" value="<?php echo isset($settings['site_title']) ? $settings['site_title'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="site_description">Site Description:</label>
                    <textarea id="site_description" name="site_description" required><?php echo isset($settings['site_description']) ? $settings['site_description'] : ''; ?></textarea>
                </div>
                <button type="submit">Update Settings</button>
            </form>
        </section>
    </main>
</body>

</html>