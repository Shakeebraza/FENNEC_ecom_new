<?php
require_once("../../global.php");

if (isset($_GET['conversation_id'])) {
    $conversationId = $_GET['conversation_id'];

    $messages = $dbFunctions->getDatanotenc('messages', "conversation_id = '$conversationId'", '', 'created_at', 'ASC');

    if (!empty($messages)) {
        foreach ($messages as $message) {
            $senderId = $message['sender_id'];
            $userdata = $dbFunctions->getDatanotenc('users', "id = '$senderId'");

            $image = !empty($userdata) && !empty($userdata[0]['profile']) ? $urlval . $userdata[0]['profile'] : $urlval . 'images/profile.jpg';
            
            echo '<div class="message">';
            echo '<div class="photo" style="background-image: url(\'' . htmlspecialchars($image) . '\');"></div>';
            echo '<p class="text">' . htmlspecialchars($message['message']) . '</p>';
            echo '</div>';
            echo '<p class="time">' . htmlspecialchars($message['created_at']) . '</p>';
        }
    } else {
        echo '<p>No messages to display.</p>';
    }
} else {
    echo '<p>No messages to display.</p>';
}
?>
