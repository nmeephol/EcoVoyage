<?php

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ecovoyagedb', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$user_id = intval($_SESSION['User_ID'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_favorite') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $category = $_POST['category'] ?? null;

    if (!$id || !$category) {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        exit;
    }

    // Determine table and column based on category
    if ($category === 'hotel') {
        $tableName = 'favoritehotel';
        $columnName = 'Hotel_ID';
    } elseif ($category === 'place') {
        $tableName = 'favoriteplace';
        $columnName = 'Place_ID';
    } elseif ($category === 'activity') {
        $tableName = 'favoriteact';
        $columnName = 'Act_ID';
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid category']);
        exit;
    }

    // Execute delete query
    try {
        $stmt = $pdo->prepare("DELETE FROM $tableName WHERE $columnName = :id AND User_ID = :user_id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No rows affected. Check your ID and User_ID.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to execute query.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

?>
