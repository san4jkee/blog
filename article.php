<?php
// article.php

// Преобразование обычных URL в активные ссылки
function make_links_clickable($text) {
    return preg_replace(
        '/(http[s]?:\/\/[^\s]+)/',
        '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">$1</a>',
        $text
    );
}

$postId = isset($_GET['id']) ? $_GET['id'] : '';
$jsonFile = __DIR__ . '/posts/posts.json';
if (file_exists($jsonFile)) {
    $posts = json_decode(file_get_contents($jsonFile), true);
    $post = null;
    foreach ($posts as $p) {
        if ($p['id'] == $postId) {
            $post = $p;
            break;
        }
    }
    if ($post) {
        $description = htmlspecialchars($post['description']);
        $punctuationMarks = ['.', '!', ':'];
        $positions = array_map(function($mark) use ($description) {
            return mb_strpos($description, $mark);
        }, $punctuationMarks);
        $validPositions = array_filter($positions, function($position) {
            return $position !== false;
        });
        if (!empty($validPositions)) {
            $firstPunctuation = min($validPositions);
            $title = mb_substr($description, 0, $firstPunctuation + 1);
            $content = mb_substr($description, $firstPunctuation + 1);
        } else {
            $title = $description;
            $content = '';
        }

        // Преобразуем обычные URL в активные ссылки
        $content = make_links_clickable($content);

        // SEO метатеги
        $pageTitle = $title . ' | TechPulse';
        $pageDescription = htmlspecialchars(mb_substr($post['description'], 0, 160));
?>
<!DOCTYPE html>
<html lang="ru" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDescription ?>">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDescription ?>">
    <?php if ($post['media_type'] === 'image'): ?>
    <meta property="og:image" content="<?= $post['image'] ?>">
    <?php endif; ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/posts/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/posts/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/posts/img/favicon-16x16.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .prose a {
            color: #1E90FF;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .prose a:hover {
            color: #4682B4;
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-900">
<header class="sticky top-0 bg-white dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75">
    <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
      <div class="flex lg:flex-1">
        <a href="/" class="-m-1.5 p-1.5">
          <span class="text-lg font-bold text-gray-900 dark:text-blue-600">TechPulse</span>
        </a>
      </div>
      <div class="hidden lg:flex lg:gap-x-12">
        <a href="/" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">Главная</a>
        <a href="https://san4jkee.ru" target="_blank" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">Обо мне</a>
      </div>
    </nav>
  </header>
    
    <main class="container mx-auto py-6 px-3">
        <article class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white"><?= $title ?></h1> <!-- Заголовок -->
            
            <?php if ($post['media_type'] === 'image'): ?>
            <img src="<?= $post['image'] ?>" class="rounded-lg mb-6 w-full" alt="Иллюстрация">
            <?php elseif ($post['media_type'] === 'video'): ?>
            <video controls class="w-full mb-6">
                <source src="<?= $post['media'] ?>" type="video/mp4">
            </video>
            <?php endif; ?>
            
            <div class="prose dark:prose-invert max-w-none text-gray-900 dark:text-white">
                <?= $content ?> <!-- Основной текст -->
            </div>
            
            <p class="mt-6 text-gray-500"><?= $post['date'] ?></p>
        </article>
    </main>
 
<footer>
    <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
      <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024-2025
        <a href="https://san4jkee.ru/" class="hover:underline">san4jkee™</a>.
        All Rights Reserved.
      </span>
      <ul class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
        <li>
          <a href="https://t.me/techpulse_it_ai" target="_blank" class="hover:underline me-4 md:me-6">Telegram</a>
        </li>
        <li>
          <a href="https://dzen.ru/techpulse_it_ai" target="_blank" class="hover:underline me-4 md:me-6">Dzen</a>
        </li>
      </ul>
    </div>
  </footer>

  <script src="/src/jquery-3.2.1.slim.min.js"></script>

  <!-- Yandex.RTB R-A-9014713-2 -->
<script>
window.yaContextCb.push(() => {
    Ya.Context.AdvManager.render({
        "blockId": "R-A-9014713-2",
        "type": "topAd"
    })
})
</script>

</body>
</html>
<?php
        exit;
    }
}
// Если статья не найдена
header("HTTP/1.1 404 Not Found");
readfile('404.html');
exit;
?>