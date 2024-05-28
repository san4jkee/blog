<?php
// upload_post.php

// Путь к JSON файлу
$jsonFile = __DIR__ . '/posts/posts.json';
$imageDir = __DIR__ . '/posts/img/';

// Проверяем, что это POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из запроса
    $data = json_decode(file_get_contents('php://input'), true);

    // Проверяем наличие необходимых данных
    if (isset($data['description']) && isset($data['image']) && isset($data['date'])) {
        // Декодируем изображение из base64
        $imageData = base64_decode($data['image']);
        $imageName = uniqid() . '.jpg';
        $imagePath = $imageDir . $imageName;

        // Сохраняем изображение
        if (file_put_contents($imagePath, $imageData)) {
            // Загружаем существующие посты
            $posts = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

            // Добавляем новый пост
            $posts[] = [
                'id' => count($posts) + 1,
                'description' => $data['description'],
                'image' => '/posts/img/' . $imageName,
                'date' => $data['date'],
            ];

            // Сохраняем обновленные данные в JSON файл
            file_put_contents($jsonFile, json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save image']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
