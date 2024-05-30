<?php
// Определите базовый URL вашего сайта
$baseUrl = 'http://blog.san4jkee.ru/';

// Массив с URL ваших страниц
$pages = [
    '',
    'about',
    'contact',
    // Добавьте сюда все страницы вашего сайта
];

// Получите текущую дату в формате W3C для <lastmod>
$lastMod = date('Y-m-d');

// Начните вывод XML
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach ($pages as $page) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($baseUrl . $page) . '</loc>';
    echo '<lastmod>' . $lastMod . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

echo '</urlset>';
?>
