<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference_id = filter_input(INPUT_POST, 'reference_id', FILTER_VALIDATE_INT);
    
    // Validate input
    if (!$reference_id) {
        $_SESSION['error'] = "Invalid reference ID.";
        header("Location: index.php");
        exit;
    }
    
    try {
        // First, verify that the reference exists and belongs to the current user
        $check_stmt = $pdo->prepare("SELECT created_by, title FROM references_entity WHERE id = ?");
        $check_stmt->execute([$reference_id]);
        $reference = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reference) {
            $_SESSION['error'] = "Reference not found.";
            header("Location: index.php");
            exit;
        }
        
        if ($reference['created_by'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "You can only delete your own references.";
            header("Location: index.php");
            exit;
        }
        
        // Start transaction to delete reference and its comments
        $pdo->beginTransaction();
        
        try {
            // Delete all comments for this reference first
            $delete_comments_stmt = $pdo->prepare("DELETE FROM comments WHERE reference_id = ?");
            $delete_comments_stmt->execute([$reference_id]);
            
            // Delete the reference
            $delete_ref_stmt = $pdo->prepare("DELETE FROM references_entity WHERE id = ? AND created_by = ?");
            $delete_ref_stmt->execute([$reference_id, $_SESSION['user_id']]);
            
            if ($delete_ref_stmt->rowCount() > 0) {
                $pdo->commit();
                $_SESSION['success'] = "Reference \"" . htmlspecialchars($reference['title']) . "\" deleted successfully!";
            } else {
                $pdo->rollback();
                $_SESSION['error'] = "Failed to delete reference.";
            }
            
        } catch (PDOException $e) {
            $pdo->rollback();
            throw $e;
        }
        
    } catch (PDOException $e) {
        error_log("Delete reference error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while deleting the reference.";
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
}

header("Location: index.php");
exit;
?>
