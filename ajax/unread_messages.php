<?php
require_once "../global.php";

if (isset($_SESSION['userid'])) {
    $userid = base64_decode($_SESSION['userid']);
    
    // Modify the query to exclude messages where sender_id is the same as userid
    $query = "
        SELECT COUNT(m.id) AS unread_count
        FROM messages m
        JOIN conversations c ON m.conversation_id = c.id
        WHERE m.is_read = 0 
          AND (c.user_one = :userid OR c.user_two = :userid)
          AND m.sender_id != :userid  -- Exclude messages where sender_id is the same as userid
    ";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    $unread_count = 0;

    // Fetch the result
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $unread_count = $row['unread_count'];
    }

    // Return the unread message count as JSON
    echo json_encode(['unread_count' => $unread_count]);
}
?>
