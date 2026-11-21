<?php
session_start();
$message = '';

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_SESSION['captcha']) || $_SESSION['captcha'] === '') {
        $message = '<p style="color: red; font-weight: bold;">⚠️ Похоже, у вас отключен показ изображений в браузере. Пожалуйста, включите их в настройках и обновите страницу.</p>';
    } else {
        // 2. ПРОВЕРКА ВВЕДЕННОГО КОДА
        $answer = isset($_POST['answer']) ? strtoupper(trim($_POST['answer'])) : '';
        $expected = strtoupper($_SESSION['captcha']);

        if ($answer === $expected && $answer !== '') {
            $message = '<p style="color: green; font-weight: bold;">✅ Отлично! Код введен верно.</p>';
        } else {
            $message = '<p style="color: red; font-weight: bold;">❌ Неверный код. Попробуйте еще раз.</p>';
        }
    }
    
    // 3. Очищаем сессию после проверки, чтобы при обновлении страницы была новая капча
    unset($_SESSION['captcha']);
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Регистрация</title>
</head>
<body>
    <h1>Регистрация</h1>
    
    <!-- Вывод сообщения об ошибке или успехе -->
    <?php if ($message !== ''): ?>
        <div style="margin-bottom: 20px; padding: 10px; background: #f9f9f9; border: 1px solid #ccc;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        <div>
            <!-- ?v=<?= time() ?> нужен, чтобы браузер не показывал старую картинку из кэша -->
            <img src="noise-picture.php?v=<?= time() ?>" alt="Введите код с картинки">
        </div>
        <div>
            <label>Введите строку:</label>
            <input type="text" name="answer" size="6" required>
        </div>
        <br>
        <input type="submit" value="Подтвердить">
    </form>
</body>
</html>
