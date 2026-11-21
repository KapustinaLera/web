<?php
declare(strict_types=1);

// Включаем показ всех ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
echo "=== Проверка окружения ===\n";

// 1. Проверка GD
echo "GD установлен: " . (extension_loaded('gd') ? 'ДА' : 'НЕТ') . "\n";
echo "GD версия: " . (function_exists('gd_info') ? gd_info()['GD Version'] : 'неизвестно') . "\n";

// 2. Проверка файла шума
$noisePath = __DIR__ . '/noise.jpg';
echo "Файл noise.jpg существует: " . (file_exists($noisePath) ? 'ДА' : 'НЕТ') . "\n";
if (file_exists($noisePath)) {
    echo "Размер noise.jpg: " . filesize($noisePath) . " байт\n";
    $img = @imagecreatefromjpeg($noisePath);
    echo "noise.jpg читается как JPEG: " . ($img !== false ? 'ДА' : 'НЕТ') . "\n";
    if ($img !== false) imagedestroy($img);
}

// 3. Проверка шрифтов
$font1 = __DIR__ . '/fonts/bellb.ttf';
$font2 = __DIR__ . '/fonts/georgia.ttf';
echo "Шрифт bellb.ttf существует: " . (file_exists($font1) ? 'ДА' : 'НЕТ') . "\n";
echo "Шрифт georgia.ttf существует: " . (file_exists($font2) ? 'ДА' : 'НЕТ') . "\n";

// 4. Проверка прав на запись (для сессий)
echo "Папка доступна для записи: " . (is_writable(__DIR__) ? 'ДА' : 'НЕТ') . "\n";
echo "session.save_path: " . session_save_path() . "\n";
echo "session.save_path доступен для записи: " . (is_writable(session_save_path()) ? 'ДА' : 'НЕТ') . "\n";

echo "\n=== Попытка сгенерировать картинку ===\n";

// Пробуем создать тестовую картинку БЕЗ header, чтобы увидеть ошибки
$image = imagecreatefromjpeg($noisePath);
$color = imagecolorallocate($image, 255, 0, 0);
imagettftext($image, 24, 0, 20, 40, $color, $font1, 'TEST');

// Сохраняем в файл вместо вывода в браузер
$testFile = __DIR__ . '/test_output.jpg';
$result = imagejpeg($image, $testFile, 50);
imagedestroy($image);

echo "imagejpeg() вернул: " . var_export($result, true) . "\n";
echo "Файл test_output.jpg создан: " . (file_exists($testFile) ? 'ДА' : 'НЕТ') . "\n";
echo "Размер test_output.jpg: " . (file_exists($testFile) ? filesize($testFile) : 0) . " байт\n";

echo "</pre>";
echo '<img src="test_output.jpg" alt="Тестовая картинка">';