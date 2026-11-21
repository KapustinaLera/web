<?php
require_once 'NewsDB.class.php';
$news = new NewsDB();
$errMsg = '';

if (isset($_GET['del'])) {
    include 'delete_news.inc.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'save_news.inc.php';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Новостная лента</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Последние новости</h1>
    
    <?php if ($errMsg !== ''): ?>
        <p style="color: red;"><?= htmlspecialchars($errMsg) ?></p>
    <?php endif; ?>

    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
        Заголовок новости:<br>
        <input type="text" name="title" required><br>
        
        Выберите категорию:<br>
        <select name="category" required>
            <option value="" selected disabled>Выберите категорию</option>
            <?php foreach ($news as $id => $name): ?>
                <option value="<?= (int)$id ?>">
                    <?= htmlspecialchars($name) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br />
        
        Текст новости:<br>
        <textarea name="description" cols="50" rows="5" required></textarea><br>
        
        Источник:<br>
        <input type="text" name="source" required><br>
        <br>
        <input type="submit" value="Добавить!">
    </form>

    <?php include 'get_news.inc.php'; ?>
</body>
</html>