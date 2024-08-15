<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/login_style.css">
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Login</h1>
            <?php if (isset($error)) : ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" id="username" name="username" required placeholder="Username">
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" required placeholder="Password">
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
