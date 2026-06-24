Функционал плагина:
* Создание пост тайпа `employee` для сотрудников
* Создание ACF групп для этого пост тайпа
* Генерация PDF-файлов

После активации плагина он также потребует установку ACF или SCF. В меню появится раздел "Сотрудники". У сотрудников уже будут иметься все ACF поля, они создаются в коде плагина.

Список сотрудников будет по адресу `employees`.

---

Для генерации PDF используется библиотека dompdf.

---

Что можно улучшить:
- Добавить возможность выводить сотрудников шорткодом вместо архива.
- Добавить поддержку кастомных PHP шаблонов для PDF.

---

## Как все устроено

```
employee-manager/
├── employee-manager.php        # Главный файл, точка входа
├── README.md
├── composer.json               # Конфигурация Composer чтобы вытягивать DOMPDF
├── composer.lock               # Зависимости Composer
├── assets/                     # CSS и JS
├── includes/
│   ├── class-acf.php           # ACF группы
│   ├── class-assets.php        # Прикрепление CSS и JS файлов
│   ├── class-pdf-generator.php # Генератор PDF-файлов
│   └── class-post-type.php     # Регистрация пост тайпа "employee"
├── templates/                  # Шаблоны PDF и архива
│   ├── archive-employees.php
│   ├── pdf-template.php
│   └── pdf-single-template.php
└── vendor/                     # DOMPDF и его зависимости
```

