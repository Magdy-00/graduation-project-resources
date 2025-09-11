<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

// Fetch references with user information
$stmt = $pdo->query("
    SELECT r.*, u.display_name, u.username 
    FROM references_entity r 
    LEFT JOIN users u ON r.created_by = u.id 
    ORDER BY r.created_at DESC
");
$references = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch comments for all references
$comments_stmt = $pdo->query("
    SELECT c.*, u.display_name, u.username, c.reference_id
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    ORDER BY c.created_at ASC
");
$all_comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group comments by reference_id
$comments_by_ref = [];
foreach ($all_comments as $comment) {
    $comments_by_ref[$comment['reference_id']][] = $comment;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RefCollect</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/app.js" defer></script>
</head>
<body>
  <header>
    <div class="header-left">
      <h1><span class="brand">Ref</span>Collect</h1>
      <p>Collaborative reference management for our graduation project</p>
    </div>
    <div class="header-right">
      <input type="text" id="search" placeholder="Search references..." aria-label="Search references">
      <button onclick="openForm()" aria-label="Add new reference">+ Add Reference</button>
      <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle dark mode">
        <span class="sun-icon">☀️</span>
        <span class="moon-icon">🌙</span>
      </button>
      <a href="logout.php" class="logout-btn" aria-label="Logout">Logout</a>
    </div>
  </header>

  <nav class="categories">
    <button onclick="filterCategory('all')">All</button>
    <button onclick="filterCategory('document')">Documents</button>
    <button onclick="filterCategory('link')">Links</button>
    <button onclick="filterCategory('paper')">Papers</button>
    <button onclick="filterCategory('tool')">Tools</button>
    <button onclick="filterCategory('person')">People</button>
    <button onclick="filterCategory('other')">Other</button>
  </nav>

  <main>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-md);">
      <h2>All References</h2>
      <small id="results-count" style="color: var(--text-muted);"></small>
    </div>
    <div id="reference-list" class="card-container">
      <?php foreach ($references as $ref): ?>
        <div class="card" data-category="<?= htmlspecialchars($ref['category']) ?>" id="ref-<?= $ref['id'] ?>">
          <h3>
            <?= htmlspecialchars($ref['title']) ?>
            <?php if (!empty($ref['url'])): ?>
              <a href="<?= htmlspecialchars($ref['url']) ?>" target="_blank">🔗</a>
            <?php endif; ?>
          </h3>
          <p><strong>By:</strong> <?= htmlspecialchars($ref['display_name'] ?? $ref['username'] ?? 'Unknown') ?></p>
          <p><?= htmlspecialchars($ref['comment']) ?></p>
          <span class="tag"><?= htmlspecialchars($ref['category']) ?></span>
          
          <!-- Comments section -->
          <?php if (isset($comments_by_ref[$ref['id']])): ?>
            <div class="comments-section">
              <h4>Comments:</h4>
              <?php foreach ($comments_by_ref[$ref['id']] as $comment): ?>
                <div class="comment">
                  <strong><?= htmlspecialchars($comment['display_name'] ?? $comment['username'] ?? 'Anonymous') ?>:</strong>
                  <p><?= htmlspecialchars($comment['comment']) ?></p>
                  <small><?= $comment['created_at'] ?></small>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          
          <!-- Add comment form -->
          <div class="add-comment">
            <form action="add_comment.php" method="post">
              <input type="hidden" name="reference_id" value="<?= $ref['id'] ?>">
              <textarea name="comment" placeholder="Add a comment..." required></textarea>
              <button type="submit">Add Comment</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Add Reference Form -->
  <div id="form-popup" class="form-popup">
    <form action="add_reference.php" method="post" class="form-container">
      <h2>Add Reference</h2>
      <input type="text" name="title" placeholder="Title" required>
      <input type="url" name="url" placeholder="URL (optional)">
      <textarea name="comment" placeholder="Comment (optional)"></textarea>
      <select name="category" required>
        <option value="document">Document</option>
        <option value="link">Link</option>
        <option value="paper">Paper</option>
        <option value="tool">Tool</option>
        <option value="person">Person</option>
        <option value="other">Other</option>
      </select>
      <button type="submit">Add</button>
      <button type="button" onclick="closeForm()">Cancel</button>
    </form>
  </div>
</body>
</html>
