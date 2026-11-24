<?php
declare(strict_types=1);
session_start();

$resultMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['captcha']) || $_SESSION['captcha'] === '') {
        $resultMessage = '<p style="color: red; font-weight: bold;">Сервер не получил запрос на картинку. Включите отображение изображений в браузере.</p>';
    } else {
        $answer = isset($_POST['answer']) ? strtoupper(trim($_POST['answer'])) : '';
        $expected = strtoupper($_SESSION['captcha']);

        if ($answer === $expected && $answer !== '') {
            $resultMessage = '<p style="color: green; font-weight: bold;">Отлично! Код введён верно.</p>';
        } else {
            $resultMessage = '<p style="color: red; font-weight: bold;">Неверный код. Попробуйте ещё раз.</p>';
        }
    }
    unset($_SESSION['captcha']);
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Регистрация</title>
    <style>
        #img-warning {
            display: none;
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-weight: bold;
        }
        .form-container {
            display: block;
        }
    </style>
</head>
<body>
    <h1>Регистрация</h1>
    
    <?php if ($resultMessage !== ''): ?>
        <div style="margin-bottom: 15px; padding: 10px; background: #fff3f3; border: 1px solid red;">
            <?= $resultMessage ?>
        </div>
    <?php endif; ?>

    <div id="img-warning">
         Внимание: Изображение капчи не загрузилось. Пожалуйста, включите отображение картинок в настройках вашего браузера и обновите страницу.
    </div>

    <div class="form-container" id="reg-form">
        <form action="" method="post">
            <div>
                <img id="captcha-img" 
                     src="noise-picture.php?v=<?= time() ?>" 
                     alt="Введите код с картинки"
                     style="display: block;">
            </div>
            <div>
                <label>Введите строку:</label>
                <input type="text" name="answer" size="6" required autofocus>
            </div>
            <br>
            <input type="submit" value="Подтвердить">
        </form>
    </div>

    <script>
        window.addEventListener('load', function() {
            var img = document.getElementById('captcha-img');
            var warning = document.getElementById('img-warning');
            var form = document.getElementById('reg-form');
            
            if (!img.complete || img.naturalWidth === 0) {
                warning.style.display = 'block';
                form.style.display = 'none';
            }
        });
        
        setTimeout(function() {
            var img = document.getElementById('captcha-img');
            var warning = document.getElementById('img-warning');
            var form = document.getElementById('reg-form');
            
            if (!img.complete || img.naturalWidth === 0) {
                warning.style.display = 'block';
                form.style.display = 'none';
            }
        }, 2000);
    </script>
</body>
</html>
