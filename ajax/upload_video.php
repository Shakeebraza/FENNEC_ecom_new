<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['videoFiles'])) {
    $productId = $_POST['product_id']; 
    $videoPaths = [];

    if (empty($productId) || !is_numeric($productId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

   
    try {
        $stmt = $pdo->prepare("SELECT video_paths FROM product_videos WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        $existingVideos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingVideos) {
           
            $existingVideoPaths = explode(',', $existingVideos['video_paths']);

           
            if (count($existingVideoPaths) >= 3) {
                echo json_encode(['success' => false, 'message' => 'You can only upload up to 3 videos per product.']);
                exit;
            }
        } else {
            $existingVideoPaths = [];
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }

    foreach ($_FILES['videoFiles']['tmp_name'] as $index => $tmpName) {
        $fileName = $_FILES['videoFiles']['name'][$index];
        $fileTmp = $_FILES['videoFiles']['tmp_name'][$index];
        $fileError = $_FILES['videoFiles']['error'][$index];
        $fileSize = $_FILES['videoFiles']['size'][$index];

        if ($fileError !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Error uploading video: ' . $fileError]);
            exit;
        }

        if ($fileSize > 30 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Each video file size should not exceed 30MB.']);
            exit;
        }

        $allowedMimeTypes = ['video/mp4', 'video/avi', 'video/mkv', 'video/webm'];
        $fileMimeType = mime_content_type($fileTmp);
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only video files are allowed.']);
            exit;
        }

      
        $uploadDir = '../upload/videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);  
        }

       
        $uniqueFileName = uniqid() . '-' . basename($fileName);
        $filePath = $uploadDir . $uniqueFileName;

     
        if (move_uploaded_file($fileTmp, $filePath)) {
        
            $videoPaths[] = 'upload/videos/' . $uniqueFileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading video file.']);
            exit;
        }
    }

  
    $updatedVideoPaths = array_merge($existingVideoPaths, $videoPaths);
    

    if (count($updatedVideoPaths) > 3) {
        echo json_encode(['success' => false, 'message' => 'You can only upload up to 3 videos.']);
        exit;
    }


    $videoPathsStr = implode(',', $updatedVideoPaths);


    try {
        if ($existingVideos) {
        
            $stmt = $pdo->prepare("UPDATE product_videos SET video_paths = :video_paths WHERE product_id = :product_id");
        } else {
           
            $stmt = $pdo->prepare("INSERT INTO product_videos (product_id, video_paths) VALUES (:product_id, :video_paths)");
        }
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':video_paths', $videoPathsStr);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'videoPaths' => $updatedVideoPaths]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save video information in the database.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
