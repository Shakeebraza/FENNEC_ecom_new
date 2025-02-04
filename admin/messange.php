<?php
require_once("../global.php");
include_once('header.php');

$userId = base64_decode($_SESSION['userid']);
$conversations = $dbFunctions->getDatanotenc('conversations', "user_one = '$userId' OR user_two = '$userId'");


?>
<style>
	.container {
		padding: 0;
		background-color: #ffffff;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
		height: 700px;
		border-radius: 12px;
		display: flex;
		flex-wrap: wrap;
	}

	.menu {
		height: 700px;
		width: 70px;
		background: #5b82db;
		background: linear-gradient(to bottom, #5b82db, #425ba7);
		border-radius: 12px 0 0 12px;
	}

	.menu .items {
		list-style: none;
		margin: 0;
		padding: 0;
	}

	.menu .items .item {
		height: 70px;
		display: flex;
		justify-content: center;
		align-items: center;
		color: #dbe3ff;
		font-size: 18pt;
		border-bottom: 1px solid rgba(255, 255, 255, 0.1);
	}

	.menu .items .item-active {
		background-color: #4868d2;
		color: #fff;
	}

	.menu .items .item:hover {
		cursor: pointer;
		background-color: #4a6bd9;
		color: #fff;
		transition: background-color 0.3s ease;
	}

	.discussions {
		width: 35%;
		height: 700px;
		overflow-y: scroll;
		background-color: #f5f6fa;
		border-radius: 0 12px 12px 0;
	}

	.discussions .discussion {
		width: 100%;
		height: 90px;
		padding: 10px;
		background-color: #fff;
		border-bottom: 1px solid #e0e0e0;
		display: flex;
		align-items: center;
		transition: background-color 0.3s ease;
	}

	.discussions .discussion:hover {
		background-color: #eef2ff;
	}

	.discussions .discussion .photo {
		margin-left: 20px;
		width: 50px;
		height: 50px;
		border-radius: 50%;
		background-color: #e6e7ed;
		background-size: cover;
		background-position: center;
	}

	.online {
		position: relative;
		width: 12px;
		height: 12px;
		background-color: #4caf50;
		border-radius: 50%;
		border: 2px solid #fff;
		position: absolute;
		top: 40px;
		left: 45px;
	}

	.desc-contact {
		margin-left: 15px;
		overflow: hidden;
	}

	.discussions .discussion .name {
		font-weight: 500;
		color: #4b4b4b;
		font-size: 13pt;
	}

	.discussions .discussion .message {
		margin-top: 5px;
		color: #909090;
		font-size: 10pt;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.timer {
		margin-left: auto;
		margin-right: 20px;
		font-size: 9pt;
		color: #bbb;
	}

	.chat {
		width: calc(65% - 85px);
		background-color: #fff;
		border-radius: 12px;
		overflow: hidden;
	}

	.header-chat {
		background-color: #fff;
		height: 90px;
		display: flex;
		align-items: center;
		padding: 0 20px;
		border-bottom: 1px solid #eee;
	}

	.header-chat .icon {
		color: #666;
		font-size: 18pt;
	}

	.header-chat .name {
		margin-left: 15px;
		font-size: 14pt;
		color: #333;
	}

	.messages-chat {
		padding: 20px 35px;
		overflow-y: auto;
		max-height: 554px;
		border: 1px solid #ccc;
	}

	.messages-chat .message {
		display: flex;
		align-items: flex-start;
		margin-bottom: 10px;
	}

	.messages-chat .message .photo {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		background-color: #e6e7ed;
		background-size: cover;
		margin-right: 10px;
	}

	.messages-chat .text {
		background-color: #f1f1f1;
		padding: 8px;
		border-radius: 5px;
	}

	.messages-chat .time {
		font-size: 0.8em;
		color: #666;
		margin-top: 2px;
	}

	.response .text {
		background-color: #e1effe;
	}

	.footer-chat {
		height: 80px;
		padding: 0 20px;
		display: flex;
		align-items: center;
		border-top: 1px solid #eee;
	}

	.footer-chat .write-message {
		border: none;
		width: 85%;
		height: 40px;
		border-radius: 20px;
		padding: 10px;
		font-size: 12pt;
	}

	.footer-chat .send {
		margin-left: 10px;
		background-color: #5b82db;
		color: #fff;
		padding: 12px;
		border-radius: 50%;
		cursor: pointer;
		font-size: 15pt;
	}

	.footer-chat .send:hover {
		background-color: #4a6bd9;
		transition: background-color 0.3s ease;
	}

	.message-input {
		display: flex;
		align-items: center;
		padding: 10px;
		border-top: 1px solid #eee;
		background-color: #f9f9f9;
	}

	.message-input input {
		flex: 1;
		border: 1px solid #ccc;
		border-radius: 20px;
		padding: 10px 20px;
		font-size: 12pt;
		margin-right: 10px;
	}

	.message-input button {
		background-color: #5b82db;
		color: #fff;
		padding: 10px 15px;
		border: none;
		border-radius: 20px;
		cursor: pointer;
		display: flex;
		align-items: center;
		font-size: 12pt;
		transition: background-color 0.3s ease;
	}

	.message-input button .icon {
		margin-right: 5px;
	}

	.message-input button:hover {
		background-color: #4a6bd9;
	}
	.conversationbtn{
		width: 100%;
	}

	/* Responsive adjustments */

	@media screen and (max-width: 768px) {
		.container {
			flex-direction: column;
			height: auto;
		}

		.menu {
			width: 100%;
			height: 60px;
			border-radius: 12px 12px 0 0;
			display: flex;
			justify-content: space-around;
			align-items: center;
		}

		.discussions {
			width: 100%;
			height: 300px;
		}

		.chat {
			width: 100%;
			height: auto;
		}

		.header-chat {
			height: 60px;
			padding: 0 10px;
		}

		.messages-chat {
			max-height: 300px;
		}

		.footer-chat .write-message {
			width: 80%;
			height: 40px;
		}

		.footer-chat .send {
			margin-left: 5px;
		}
	}

	@media screen and (max-width: 480px) {
		.menu {
			width: 100%;
			height: 50px;
		}

		.discussions {
			width: 100%;
			height: 250px;
		}

		.chat {
			width: 100%;
		}

		.header-chat {
			height: 50px;
		}

		.footer-chat .write-message {
			width: 75%;
		}
	}
</style>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="discussions">
                        <?php foreach ($conversations as $conversation):
                            $otherUserId = ($conversation['user_one'] == $userId) ? $conversation['user_two'] : $conversation['user_one'];
                            $userdata = $dbFunctions->getDatanotenc('users', "id = '$otherUserId'");
                            $image = !empty($userdata[0]['profile']) ? $urlval . $userdata[0]['profile'] : $urlval . 'images/profile.jpg';
                        ?>
                            <a href="#" class="conversationbtn open-conversation" data-conversation-id="<?= htmlspecialchars($conversation['id']) ?>">
                                <div class="discussion">
                                    <div class="photo" style="background-image: url('<?= htmlspecialchars($image) ?>');"></div>
                                    <div class="desc-contact">
                                        <p class="name"><?= htmlspecialchars($userdata[0]['username']) ?>: <?= htmlspecialchars($conversation['id']) ?></p>
                                        <p class="message">Click to see messages</p>
                                    </div>
                                    <div class="timer"><?= htmlspecialchars($conversation['created_at']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <section class="chat">
                        <div class="header-chat">
                            <p class="name">Chat Messages</p>
                        </div>
                        <div class="messages-chat" id="messagesChat">
                            <p>No messages to display.</p>
                        </div>
                        <div class="message-input">
                            <input type="text" id="newMessage" placeholder="Type your message here...">
                            <button id="sendMessage">
                                <i class="fa-solid fa-chevron-up">‌</i>
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>

<script>
    $(document).ready(function() {

        function loadMessages(conversationId) {
            $.ajax({
                url: '<?php echo $urlval ?>admin/ajax/fetch_messages.php',
                type: 'GET',
                data: {
                    conversation_id: conversationId
                },
                success: function(data) {
                    $('#messagesChat').html(data);
                },
                error: function() {
                    alert('Failed to load messages.');
                }
            });
        }

        $('.open-conversation').click(function(event) {
            event.preventDefault();
            const conversationId = $(this).data('conversation-id');
            loadMessages(conversationId);

            $('.open-conversation').removeClass('active');
            $(this).addClass('active');
        });

        $('#sendMessage').click(function() {
            const message = $('#newMessage').val();
            const conversationId = $('.open-conversation.active').data('conversation-id');

            if (message.trim() === '') {
                alert('Please enter a message.');
                return;
            }

            $.ajax({
                url: '<?php echo $urlval ?>admin/ajax/send_message.php',
                type: 'POST',
                data: {
                    message: message,
                    conversation_id: conversationId
                },
                success: function(response) {
                    $('#newMessage').val('');
                    loadMessages(conversationId);
                },
                error: function() {
                    alert('Failed to send message.');
                }
            });
        });
    });
</script>

</body>

</html>