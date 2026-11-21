<?php
declare(strict_types=1);

session_start();

$resultMessage = '';

// 1. Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. Проверяем, не отключён ли показ картинок.
    if (!isset($_SESSION['captcha']) || $_SESSION['captcha'] === '') {
        $resultMessage = '<p style="color: red;">'
            . 'Похоже, у вас отключён показ изображений. '
            . 'Включите отображение картинок в браузере и обновите страницу.</p>';
    } else {
        // 3. Проверяем введённые данные на совпадение 
        $answer = isset($_POST['answer']) ? strtoupper(trim($_POST['answer'])) : '';
        $expected = strtoupper($_SESSION['captcha']);

        if ($answer === $expected) {
            $resultMessage = '<p style="color: green;"> Отлично! Код введён верно.</p>';
        } else {
            $resultMessage = '<p style="color: red;"> Неверный код. Попробуйте ещё раз.</p>';
        }
    }

    // 4. После проверки сбрасываем строку, чтобы капча обновилась
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
    <form action="" method="post">
        <div>
            <img src="noise-picture.php?v=<?= time() ?>" alt="Введите код с картинки">
        </div>
        <div>
            <label>Введите строку</label>
            <input type="text" name="answer" size="6">
        </div>
        <input type="submit" value="Подтвердить">
    </form>

    <!-- Сообщения для пользователя выводим внизу после формы -->
    <?php if ($resultMessage !== ''): ?>
        <div><?= $resultMessage ?></div>
    <?php endif; ?>
</body>
</html>