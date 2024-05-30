<?php
// Путь к SSL сертификатам
$ssl_cert_path = '/путь/к/ssl/сертификатам/';
$ssl_cert_fullchain = $ssl_cert_path . 'fullchain.pem';
$ssl_cert_privkey = $ssl_cert_path . 'privkey.pem';

// Параметры SSL контекста
$ssl_context = stream_context_create([
    'ssl' => [
        'local_cert' => $ssl_cert_fullchain,
        'local_pk' => $ssl_cert_privkey,
        'verify_peer' => false, // Отключаем проверку сертификата клиента
        'verify_peer_name' => false, // Отключаем проверку имени сервера
        'allow_self_signed' => true // Разрешаем использование самоподписанных сертификатов
    ]
]);

// Создание защищенного SSL сервера на порту 443
$server = stream_socket_server('ssl://0.0.0.0:443', $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $ssl_context);

if (!$server) {
    die("Ошибка при создании SSL сервера: $errstr ($errno)");
}

echo "SSL сервер запущен на порту 443...\n";

// Принимаем соединения
while ($client = stream_socket_accept($server, -1)) {
    // Обработка запросов клиента
    $request = fread($client, 8192);

    // Отправляем ответ клиенту (например, просто сообщение "Hello, World!")
    $response = "HTTP/1.1 200 OK\r\nContent-Type: text/plain\r\n\r\nHello, World!";
    fwrite($client, $response);

    // Закрываем соединение
    fclose($client);
}

// Закрываем сервер
fclose($server);
