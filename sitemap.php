<?php
// Определите базовый URL вашего сайта
$baseUrl = 'https://blog.san4jkee.ru/';

// Массив с URL статических страниц
$staticPages = [
    '',
    'about',
    'contact',
    // Добавьте сюда все статические страницы вашего сайта
];

// Путь к файлу с постами
$jsonFile = __DIR__ . '/posts/posts.json';

// Получаем массив постов из JSON-файла
if (file_exists($jsonFile)) {
    $posts = json_decode(file_get_contents($jsonFile), true);
} else {
    $posts = [];
}

// Начните вывод XML
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Функция для нормализации даты
function formatDate($dateString) {
    // Попробуем первый формат: Y-m-d H:i:s
    if ($dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateString)) {
        return $dateTime->format('Y-m-d');
    }

    // Попробуем второй формат: H:i - d.m.Y
    if ($dateTime = DateTime::createFromFormat('H:i - d.m.Y', $dateString)) {
        return $dateTime->format('Y-m-d');
    }

    // Если ни один формат не подходит, используем текущую дату
    return date('Y-m-d');
}

// Добавляем статические страницы
foreach ($staticPages as $page) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($baseUrl . $page) . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>'; // Текущая дата для статических страниц
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// Добавляем посты
foreach ($posts as $post) {
    // Создаем URL для поста
    $postUrl = $baseUrl . 'article.php?id=' . urlencode($post['id']);

    // Нормализуем дату публикации
    $lastMod = formatDate($post['date']);

    echo '<url>';
    echo '<loc>' . htmlspecialchars($postUrl) . '</loc>';
    echo '<lastmod>' . $lastMod . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.5</priority>';
    echo '</url>';
}

// Завершаем XML
echo '</urlset>';
?>