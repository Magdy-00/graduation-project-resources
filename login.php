<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid login credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - RefCollect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
  <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle dark mode" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <span class="sun-icon">☀️</span>
    <span class="moon-icon">🌙</span>
  </button>
  <div class="login-container">
    <h2>RefCollect Login</h2>
    <form method="post" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <p><a href="register.php">Don't have an account? Register here</a></p>
  </div>
  
  <script>
    // Theme toggle functionality for login page
    function toggleTheme() {
      const currentTheme = localStorage.getItem('theme') || 'light';
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      
      setTheme(newTheme);
      localStorage.setItem('theme', newTheme);
    }

    function setTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
    }

    function initializeTheme() {
      const savedTheme = localStorage.getItem('theme');
      
      let theme = savedTheme || 'light'; // Default to light mode
      
      setTheme(theme);
    }

    // Initialize theme on page load
    initializeTheme();
  </script>
</body>
</html>
