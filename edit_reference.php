<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log all POST data for debugging
    error_log("POST data received: " . print_r($_POST, true));
    
    $reference_id = filter_input(INPUT_POST, 'reference_id', FILTER_VALIDATE_INT);
    $title = trim($_POST['title']);
    $url = trim($_POST['url']);
    $comment = trim($_POST['comment']);
    $category = $_POST['category'];
    
    // Debug logging
    error_log("Edit reference attempt - ID: $reference_id, Title: '$title', Category: '$category', URL: '$url', Comment: '$comment'");
    
    // Validate inputs
    if (!$reference_id || empty($title) || empty($category)) {
        $_SESSION['error'] = "Reference ID, title, and category are required.";
        error_log("Edit reference validation failed - ID: $reference_id, Title: '$title', Category: '$category'");
        header("Location: index.php");
        exit;
    }
    
    // Validate category
    $valid_categories = ['document', 'link', 'paper', 'tool', 'person', 'other'];
    if (!in_array($category, $valid_categories)) {
        $_SESSION['error'] = "Invalid category selected.";
        header("Location: index.php");
        exit;
    }
    
    try {
        // First, verify that the reference exists and belongs to the current user
        $check_stmt = $pdo->prepare("SELECT created_by FROM references_entity WHERE id = ?");
        $check_stmt->execute([$reference_id]);
        $reference = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reference) {
            $_SESSION['error'] = "Reference not found.";
            header("Location: index.php");
            exit;
        }
        
        if ($reference['created_by'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "You can only edit your own references.";
            header("Location: index.php");
            exit;
        }
        
        // Update the reference
        $stmt = $pdo->prepare("
            UPDATE references_entity 
            SET title = ?, url = ?, comment = ?, category = ? 
            WHERE id = ? AND created_by = ?
        ");
        
        $stmt->execute([
            $title,
            $url ?: null,
            $comment ?: null,
            $category,
            $reference_id,
            $_SESSION['user_id']
        ]);
        
        $rowCount = $stmt->rowCount();
        error_log("Edit reference SQL executed - Rows affected: $rowCount");
        
        if ($rowCount > 0) {
            $_SESSION['success'] = "Reference updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update reference. No rows were affected.";
            error_log("Edit reference failed - No rows affected. Reference ID: $reference_id, User ID: " . $_SESSION['user_id']);
        }
        
    } catch (PDOException $e) {
        error_log("Edit reference error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while updating the reference.";
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
}

header("Location: index.php#ref-" . $reference_id);
exit;
?>
