import os
import json
import requests
import base64
from uuid import uuid4
from telegram import Update, InputFile
from telegram.ext import Updater, CommandHandler, MessageHandler, Filters, ConversationHandler, CallbackContext
from datetime import datetime

# Путь к директории для сохранения изображений временно
TEMP_IMG_DIR = 'posts/img'

# Создаем директорию, если она не существует
os.makedirs(TEMP_IMG_DIR, exist_ok=True)

# URL API вашего сайта
API_URL = 'http://blog.san4jkee.ru/upload_post.php'

# Определение состояний для ConversationHandler
ASKING_FOR_IMAGE, ASKING_FOR_DESCRIPTION = range(2)

# Команда /start
def start(update: Update, context: CallbackContext) -> None:
    update.message.reply_text('Привет! Используйте команду /post для создания нового поста.')

# Команда /post
def post(update: Update, context: CallbackContext) -> int:
    update.message.reply_text('Пожалуйста, загрузите изображение для поста.')
    return ASKING_FOR_IMAGE

# Обработчик изображений
def handle_image(update: Update, context: CallbackContext) -> int:
    if update.message.photo:
        photo = update.message.photo[-1]  # Получаем изображение с наивысшим разрешением
        photo_file = context.bot.get_file(photo.file_id)
        
        # Создаем уникальное имя файла
        image_id = str(uuid4())
        image_path = os.path.join(TEMP_IMG_DIR, f'{image_id}.jpg')
        
        # Сохраняем файл временно
        photo_file.download(image_path)
        
        # Сохраняем путь к изображению в контексте
        context.user_data['image_path'] = image_path
        
        update.message.reply_text('Теперь, пожалуйста, введите описание для поста.')
        return ASKING_FOR_DESCRIPTION
    else:
        update.message.reply_text('Пожалуйста, загрузите изображение.')
        return ASKING_FOR_IMAGE

# Обработчик описания
def handle_description(update: Update, context: CallbackContext) -> int:
    description = update.message.text
    image_path = context.user_data.get('image_path')
    
    if image_path:
        with open(image_path, 'rb') as img_file:
            image_data = img_file.read()

        # Конвертируем изображение в base64
        image_base64 = base64.b64encode(image_data).decode('utf-8')

        # Форматируем дату
        formatted_date = update.message.date.strftime('%H:%M - %d.%m.%Y')

        post_data = {
            'description': description,
            'image': image_base64,
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
        
        # Удаляем временный файл изображения
        os.remove(image_path)

        return ConversationHandler.END
    else:
        update.message.reply_text('Произошла ошибка при сохранении изображения. Попробуйте снова.')
        return ConversationHandler.END

# Команда /cancel для завершения разговора
def cancel(update: Update, context: CallbackContext) -> int:
    update.message.reply_text('Создание поста отменено.')
    return ConversationHandler.END

def main() -> None:
    updater = Updater('7153931285:AAGYrEOVTTLgXQfxLMrcLm3V1nF0zNVLz2U')
    
    dispatcher = updater.dispatcher
    
    conv_handler = ConversationHandler(
        entry_points=[CommandHandler('post', post)],
        states={
            ASKING_FOR_IMAGE: [MessageHandler(Filters.photo, handle_image)],
            ASKING_FOR_DESCRIPTION: [MessageHandler(Filters.text & ~Filters.command, handle_description)],
        },
        fallbacks=[CommandHandler('cancel', cancel)],
    )
    
    dispatcher.add_handler(CommandHandler('start', start))
    dispatcher.add_handler(conv_handler)
    
    updater.start_polling()
    updater.idle()

if __name__ == '__main__':
    main()
