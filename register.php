<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $display_name = trim($_POST['display_name']);

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        
        if ($stmt->fetch()) {
            $error = "Username already exists!";
        } else {
            // Create new user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, display_name) VALUES (:username, :password_hash, :display_name)");
            
            if ($stmt->execute([
                'username' => $username,
                'password_hash' => $password_hash,
                'display_name' => $display_name ?: $username
            ])) {
                $success = "Account created successfully! You can now log in.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - RefCollect</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
  <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle dark mode" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <span class="sun-icon">☀️</span>
    <span class="moon-icon">🌙</span>
  </button>
  <div class="login-container">
    <h2>RefCollect Registration</h2>
    <form method="post" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="text" name="display_name" placeholder="Display Name (optional)">
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
      <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <p><a href="login.php">Already have an account? Login here</a></p>
  </div>
  
  <script>
    // Theme toggle functionality for register page
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
