<!DOCTYPE html>
<html lang="ru" class="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="yandex-verification" content="f3c9162a571ff1e7" />
  <meta name="zen-verification" content="ppyrArNEEhpYdbXUuG6oFWlkbaS938O7UIsqMrsu0gIZjyR9WQv454ofnFSlGtHQ" />
  <title>TechPulse: IT & AI Innovations. Блог</title>
  <meta name="description" content="TechPulse - это канал для всех, кто хочет быть в курсе последних новостей и тенденций в области информационных технологий и искусственного интеллекта. Здесь вы найдете актуальные статьи, обзоры, интервью с экспертами и многое другое.">
  <meta name="keywords" content="IT новости, AI инновации, технологические тренды, информационные технологии, искусственный интеллект, технологические новинки, обзоры IT продуктов, IT эксперты, технологические статьи, AI разработки">
  <meta name="author" content="TechPulse: IT & AI Innovations">
  <meta name="robots" content="index, follow"> 
  <meta property="og:title" content="TechPulse: IT & AI Innovations">
  <meta property="og:description" content="TechPulse - это канал для всех, кто хочет быть в курсе последних новостей и тенденций в области информационных технологий и искусственного интеллекта. Здесь вы найдете актуальные статьи, обзоры, интервью с экспертами и многое другое.">
  <meta property="og:image" content="/posts/img/logo.jpg">
  <meta property="og:url" content="http://blog.san4jkee.ru">
  <link rel="apple-touch-icon" sizes="180x180" href="/posts/img/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/posts/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/posts/img/favicon-16x16.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Дополнительные стили */
    body {
      transition: background-color 0.5s, color 0.5s;
    }

    .freehosting-2domains-link {
      display: none;
    }
  </style>
  <!-- Yandex.RTB -->
<script>window.yaContextCb=window.yaContextCb||[]</script>
<script src="https://yandex.ru/ads/system/context.js" async></script>
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

  <!-- Вместо JavaScript-рендеринга -->
<section class="container mx-auto py-6 px-3">
    <div class="mx-auto grid lg:grid-cols-4 gap-4 md:grid-cols-2 sm:grid-cols-1 sm:justify-center">
        <?php
        $posts = json_decode(file_get_contents(__DIR__.'/posts/posts.json'), true);
        foreach (array_reverse($posts) as $post):
        ?>
        <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <a href="/article.php?id=<?= $post['id'] ?>">
                <?php if ($post['media_type'] === 'image'): ?>
                <img class="rounded-t-lg object-cover object-center h-48 w-96" 
                     src="<?= $post['image'] ?>" 
                     alt="<?= htmlspecialchars($post['description']) ?>">
                <?php endif; ?>
            </a>
            <div class="p-5">
                <a href="/article.php?id=<?= $post['id'] ?>" class="hover:underline">
                    <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white truncate">
                        <?= htmlspecialchars($post['description']) ?>
                    </h3>
                </a>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= $post['date'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
  
  

  <footer>
    <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
      <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024 - 2025
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