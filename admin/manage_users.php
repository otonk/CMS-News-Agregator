<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM users WHERE id = $id");
        header("Location: manage_users.php");
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$password' WHERE id = $id");
        header("Location: manage_users.php");
    }
}

// Mengambil daftar pengguna
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        /* Tambahkan CSS untuk mempercantik tabel dan modal */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        .actions button {
            margin-right: 5px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal h2 {
            margin-top: 0;
        }

        .modal label {
            display: block;
            margin-top: 10px;
        }

        .modal input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .modal button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header>
        <h1>Manage Users</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="site_settings.php">Site Settings</a></li>
                <li><a href="manage_articles.php">Manage Articles</a></li>
                <li><a href="add_source.php">Add RSS Source</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Users List</h2>
            <?php if ($users && $users->num_rows > 0) : ?>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $users->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="actions">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                                <button onclick="document.getElementById('updateModal-<?php echo $row['id']; ?>').style.display='block'">Update Password</button>
                            </td>
                        </tr>
                        <div id="updateModal-<?php echo $row['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="document.getElementById('updateModal-<?php echo $row['id']; ?>').style.display='none'">&times;</span>
                                <form method="post">
                                    <h2>Update Password for <?php echo htmlspecialchars($row['username']); ?></h2>
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <label for="password">New Password:</label>
                                    <input type="password" name="password" required>
                                    <button type="submit" name="update">Update</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </table>
            <?php else : ?>
                <p>No users found.</p>
            <?php endif; ?>
        </section>
    </main>
    <script>
        window.onclick = function(event) {
            var modals = document.getElementsByClassName('modal');
            for (var i = 0; i < modals.length; i++) {
                var modal = modals[i];
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    </script>
</body>

</html>
