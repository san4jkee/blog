<?php
// Указываем заголовки для RSS ленты
header("Content-Type: application/rss+xml; charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';

// Подключаемся к файлу с постами
$postsFile = __DIR__ . '/posts/posts.json';

// Проверяем наличие файла с постами
if (file_exists($postsFile)) {
    // Читаем содержимое файла
    $postsData = file_get_contents($postsFile);
    $posts = json_decode($postsData, true);

    // Проверяем, есть ли посты
    if (!empty($posts)) {
        // Начинаем формировать RSS ленту
        echo '<rss version="2.0">';
        echo '<channel>';
        echo '<title>TechPulse: IT & AI Innovations. Блог</title>';
        echo '<link>http://blog.san4jkee.ru</link>';
        echo '<description>Последние новости сайта</description>';
        echo '<language>ru</language>';

        // Перебираем посты
        foreach ($posts as $post) {
            echo '<item>';
            echo '<title>' . htmlspecialchars($post['description']) . '</title>';
            echo '<link>http://blog.san4jkee.ru/post.php?id=' . $post['id'] . '</link>'; 
            echo '<description>' . htmlspecialchars($post['description']) . '</description>';
            echo '<pubDate>' . date('r', strtotime($post['date'])) . '</pubDate>';
            if ($post['media_type'] === 'image') {
                echo '<enclosure url="http://blog.san4jkee.ru/' . ltrim($post['image'], '/') . '" type="image/jpeg" />';
            } elseif ($post['media_type'] === 'video') {
                echo '<enclosure url="http://blog.san4jkee.ru/' . ltrim($post['media'], '/') . '" type="video/mp4" />'; 
            }
            echo '</item>';
        }

        echo '</channel>';
        echo '</rss>';
    }
}
?>
