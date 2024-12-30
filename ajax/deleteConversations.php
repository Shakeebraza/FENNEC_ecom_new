<?php

require_once("../global.php");

if (isset($_POST['delete_conversations'])) {
    $user_id = base64_decode($_SESSION['userid']) ?? null;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID not found in session.']);
        exit;
    }

    // Get the list of conversation IDs from the POST request
    $conversation_ids = json_decode($_POST['delete_conversations']);

    if (empty($conversation_ids)) {
        echo json_encode(['success' => false, 'message' => 'No conversations selected.']);
        exit;
    }

    // Prepare the SQL to update each conversation
    $updateSql = "UPDATE conversations SET user_one = NULL, user_two = NULL WHERE id IN (" . implode(',', array_fill(0, count($conversation_ids), '?')) . ") AND (user_one = ? OR user_two = ?)";
    $stmt = $pdo->prepare($updateSql);
    $stmt->execute(array_merge($conversation_ids, [$user_id, $user_id]));

    // Optionally, mark the messages as archived
    $archiveMessagesSql = "
        UPDATE messages 
        SET is_read = 1 
        WHERE conversation_id IN (" . implode(',', array_fill(0, count($conversation_ids), '?')) . ")";
    $archiveStmt = $pdo->prepare($archiveMessagesSql);
    $archiveStmt->execute($conversation_ids);

    echo json_encode(['success' => true, 'message' => 'Selected conversations have been deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No conversations data provided.']);
}