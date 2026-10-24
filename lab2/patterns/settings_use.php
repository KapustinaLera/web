<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'Singleton\\';
    $baseDir = __DIR__ . '/singleton/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    
    $file = $baseDir . strtolower(str_replace('\\', '/', $relativeClass)) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use Singleton\Settings;

// Получаем единственный экземпляр класса Settings
$settings = Settings::get();

// Сохраняем значения разных типов (используем __set)
$settings->app_name = 'Моя лабораторная';    // строковое
$settings->max_users = 100;                   // числовое
$settings->debug_mode = true;                 // логическое

// Проверяем, что это действительно один и тот же объект
$settings2 = Settings::get();

echo "<pre>";
echo "Это один и тот же объект? " . ($settings === $settings2 ? 'ДА' : 'НЕТ') . "\n\n";

// Выводим сохранённые значения (используем __get)
echo "Настройки приложения:\n";
echo "Имя приложения: " . $settings->app_name . "\n";
echo "Максимум пользователей: " . $settings->max_users . "\n";
echo "Режим отладки: " . ($settings->debug_mode ? 'включён' : 'выключен') . "\n";
echo "</pre>";
