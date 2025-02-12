<?php
// Указываем заголовки для RSS ленты
header("Content-Type: application/rss+xml; charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';

// Путь к файлу с постами
$postsFile = __DIR__ . '/posts/posts.json';

// Функция для нормализации даты
function formatDateForRSS($dateString) {
    // Попробуем первый формат: Y-m-d H:i:s
    if ($dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateString)) {
        return $dateTime->format('r'); // Возвращает дату в формате RFC 2822
    }

    // Попробуем второй формат: H:i - d.m.Y
    if ($dateTime = DateTime::createFromFormat('H:i - d.m.Y', $dateString)) {
        return $dateTime->format('r'); // Возвращает дату в формате RFC 2822
    }

    // Если ни один формат не подходит, используем текущую дату
    return date('r');
}

// Функция для безопасной обработки описания
function sanitizeDescription($description) {
    // Экранируем специальные символы
    $sanitized = htmlspecialchars($description, ENT_XML1, 'UTF-8');

    // Дополнительно удаляем любые нежелательные символы
    $sanitized = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $sanitized);

    return $sanitized;
}

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
        echo '<link>https://blog.san4jkee.ru</link>';
        echo '<description>Последние новости сайта</description>';
        echo '<language>ru</language>';

        // Перебираем посты
        foreach ($posts as $post) {
            echo '<item>';
            // Безопасно выводим заголовок
            echo '<title>' . sanitizeDescription($post['description']) . '</title>';
            echo '<link>https://blog.san4jkee.ru/article.php?id=' . urlencode($post['id']) . '</link>';
            echo '<guid>https://blog.san4jkee.ru/article.php?id=' . urlencode($post['id']) . '</guid>';

            // Безопасно выводим описание
            echo '<description>' . sanitizeDescription($post['description']) . '</description>';

            // Форматируем дату для RSS
            $pubDate = formatDateForRSS($post['date']);
            echo '<pubDate>' . $pubDate . '</pubDate>';

            // Добавляем enclosure для изображений или видео
            if ($post['media_type'] === 'image' && !empty($post['image'])) {
                echo '<enclosure url="https://blog.san4jkee.ru/' . ltrim($post['image'], '/') . '" type="image/jpeg" />';
            } elseif ($post['media_type'] === 'video' && !empty($post['media'])) {
                echo '<enclosure url="https://blog.san4jkee.ru/' . ltrim($post['media'], '/') . '" type="video/mp4" />';
            }
            echo '</item>';
        }

        echo '</channel>';
        echo '</rss>';
    }
}
?>