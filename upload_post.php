<?php
// upload_post.php

// Путь к JSON файлу
$jsonFile = __DIR__ . '/posts/posts.json';
$imageDir = __DIR__ . '/posts/img/';
$mediaDir = __DIR__ . '/posts/media/';

// Создаем директории, если они не существуют
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
}
if (!file_exists($mediaDir)) {
    mkdir($mediaDir, 0777, true);
}

// Проверяем, что это POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из запроса
    $data = json_decode(file_get_contents('php://input'), true);

    // Проверяем наличие необходимых данных
    if (isset($data['description']) && isset($data['date'])) {
        // Загружаем существующие посты
        $posts = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

        // Определяем тип медиа и сохраняем соответствующий параметр
        $mediaPath = null;
        if ($data['media_type'] === 'image') {
            // Сохраняем изображение
            $imageData = base64_decode($data['image']);
            $imageName = uniqid() . '.jpg';
            $mediaPath = '/posts/img/' . $imageName;
            if (!file_put_contents($imageDir . $imageName, $imageData)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save image']);
                exit;
            }
        } elseif ($data['media_type'] === 'video') {
            // Сохраняем видео
            $videoData = base64_decode($data['media']);
            $videoName = uniqid() . '.mp4';
            $mediaPath = '/posts/media/' . $videoName;
            if (!file_put_contents($mediaDir . $videoName, $videoData)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save video']);
                exit;
            }
        } elseif ($data['media_type'] === 'url') {
            // Сохраняем URL-медиа
            $mediaPath = $data['media_url'];
        }

        // Добавляем новый пост
        $posts[] = [
            'id' => count($posts) + 1,
            'description' => $data['description'],
            ($data['media_type'] === 'image') ? 'image' : 'media' => $mediaPath,
            'media_type' => $data['media_type'],
            'date' => $data['date'],
        ];

        // Сохраняем обновленные данные в JSON файл
        file_put_contents($jsonFile, json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
