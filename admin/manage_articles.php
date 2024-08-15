<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Konstanta untuk jumlah artikel per halaman
define('ARTICLES_PER_PAGE', 20);

// Menentukan halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page < 1 ? 1 : $page;

// Menghitung offset untuk query
$offset = ($page - 1) * ARTICLES_PER_PAGE;

// Mengambil total jumlah artikel
$total_articles_result = $conn->query("SELECT COUNT(*) AS count FROM articles");
$total_articles_row = $total_articles_result->fetch_assoc();
$total_articles = $total_articles_row['count'];

// Mengambil daftar artikel untuk halaman saat ini
$articles = $conn->query("SELECT * FROM articles LIMIT $offset, " . ARTICLES_PER_PAGE);

// Menentukan total halaman
$total_pages = ceil($total_articles / ARTICLES_PER_PAGE);

// Menghapus artikel
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM articles WHERE id = $id");
        header("Location: manage_articles.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Manage Articles</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        /* Tambahkan CSS untuk mempercantik tabel dan pagination */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <header>
        <h1>Manage Articles</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="site_settings.php">Site Settings</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="add_source.php">Add RSS Source</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Articles List</h2>
            <?php if ($articles && $articles->num_rows > 0) : ?>
                <table>
                    <tr>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $articles->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else : ?>
                <p>No articles found.</p>
            <?php endif; ?>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="manage_articles.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);

                if ($start > 1) {
                    echo '<a href="manage_articles.php?page=1">1</a>';
                    if ($start > 2) {
                        echo '<span>...</span>';
                    }
                }

                for ($i = $start; $i <= $end; $i++) {
                    echo '<a href="manage_articles.php?page=' . $i . '" ' . ($i == $page ? 'class="active"' : '') . '>' . $i . '</a>';
                }

                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) {
                        echo '<span>...</span>';
                    }
                    echo '<a href="manage_articles.php?page=' . $total_pages . '">' . $total_pages . '</a>';
                }
                ?>

                <?php if ($page < $total_pages) : ?>
                    <a href="manage_articles.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>

</html>
