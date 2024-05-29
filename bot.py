import os
import json
import requests
import base64
from uuid import uuid4
from telegram import Update, InputFile
from telegram.ext import Updater, CommandHandler, MessageHandler, Filters, ConversationHandler, CallbackContext
from datetime import datetime

# Пути к директориям для сохранения медиа временно
TEMP_IMG_DIR = 'posts/img'
TEMP_MEDIA_DIR = 'posts/media'

# Создаем директории, если они не существуют
os.makedirs(TEMP_IMG_DIR, exist_ok=True)
os.makedirs(TEMP_MEDIA_DIR, exist_ok=True)

# URL API вашего сайта
API_URL = 'http://blog.san4jkee.ru/upload_post.php'

# Определение состояний для ConversationHandler
ASKING_FOR_INPUT = range(1)

# Команда /start
def start(update: Update, context: CallbackContext) -> None:
    update.message.reply_text('Привет! Используйте команду /post для создания нового поста.')

# Команда /post
def post(update: Update, context: CallbackContext) -> int:
    update.message.reply_text('Пожалуйста, загрузите изображение или видео для поста с описанием или отправьте ссылку на медиа с описанием.')
    return ASKING_FOR_INPUT

# Обработчик ввода медиа или ссылки
def handle_input(update: Update, context: CallbackContext) -> int:
    description = None
    media_base64 = None
    media_url = None
    media_type = None

    if update.message.photo:
        photo = update.message.photo[-1]  # Получаем изображение с наивысшим разрешением
        media_file = photo.get_file()

        # Создаем уникальное имя файла
        media_id = str(uuid4())
        media_path = os.path.join(TEMP_IMG_DIR, f'{media_id}.jpg')

        # Сохраняем файл временно
        media_file.download(media_path)

        with open(media_path, 'rb') as file:
            media_data = file.read()

        # Конвертируем медиа в base64
        media_base64 = base64.b64encode(media_data).decode('utf-8')
        media_type = 'image'

        # Удаляем временный файл медиа
        os.remove(media_path)

        # Получаем описание из подписи к фото
        description = update.message.caption

    elif update.message.video:
        video = update.message.video
        media_file = video.get_file()

        # Создаем уникальное имя файла
        media_id = str(uuid4())
        media_path = os.path.join(TEMP_MEDIA_DIR, f'{media_id}.mp4')

        # Сохраняем файл временно
        media_file.download(media_path)

        with open(media_path, 'rb') as file:
            media_data = file.read()

        # Конвертируем медиа в base64
        media_base64 = base64.b64encode(media_data).decode('utf-8')
        media_type = 'video'

        # Удаляем временный файл медиа
        os.remove(media_path)

        # Получаем описание из подписи к видео
        description = update.message.caption

    elif update.message.text and (update.message.text.startswith('http://') or update.message.text.startswith('https://')):
        # Сохраняем URL медиа и описание в контексте
        media_url = update.message.text.split()[0]
        description = ' '.join(update.message.text.split()[1:])
        media_type = 'url'

    if description:
        # Форматируем дату
        formatted_date = datetime.now().strftime('%H:%M - %d.%m.%Y')

        post_data = {
            'description': description,
            'media': media_base64,
            'image': media_base64 if media_type == 'image' else None,  # Добавляем параметр image для изображений
            'media_url': media_url,
            'media_type': media_type,
            'date': formatted_date
        }

        try:
            # Отправляем пост на сайт
            response = requests.post(API_URL, json=post_data)
            response.raise_for_status()  # Поднимаем исключение для HTTP ошибок

            try:
                response_json = response.json()
                if response.status_code == 200 and response_json.get('status') == 'success':
                    update.message.reply_text('Пост был успешно создан!')
                else:
                    update.message.reply_text('Произошла ошибка при создании поста. Попробуйте снова.')
            except json.JSONDecodeError:
                update.message.reply_text('Произошла ошибка при декодировании ответа сервера. Ответ не является корректным JSON.')

        except requests.RequestException as e:
            update.message.reply_text(f'Произошла ошибка при соединении с сервером: {e}')

        return ConversationHandler.END
    else:
        update.message.reply_text('Пожалуйста, предоставьте описание вместе с медиа или ссылкой.')
        return ASKING_FOR_INPUT

# Команда /cancel для завершения разговора
def cancel(update: Update, context: CallbackContext) -> int:
    update.message.reply_text('Создание поста отменено.')
    return ConversationHandler.END

def main() -> None:
    updater = Updater('TELEGRAM_BOT_API')
    
    dispatcher = updater.dispatcher
    
    conv_handler = ConversationHandler(
        entry_points=[CommandHandler('post', post)],
        states={
            ASKING_FOR_INPUT: [MessageHandler(Filters.photo | Filters.video | (Filters.text & ~Filters.command), handle_input)],
        },
        fallbacks=[CommandHandler('cancel', cancel)],
    )
    
    dispatcher.add_handler(CommandHandler('start', start))
    dispatcher.add_handler(conv_handler)
    
    updater.start_polling()
    updater.idle()

if __name__ == '__main__':
    main()
