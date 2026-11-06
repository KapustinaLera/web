<?php
$newsList = $news->getNews();

if ($newsList === false) {
    $errMsg = 'Произошла ошибка при выводе новостной ленты';
} else {
    $count = count($newsList);

    echo '<p>Всего новостей: ' . $count . '</p>';

    if ($count === 0) {
        echo '<p>Новостей пока нет.</p>';
    }

    foreach ($newsList as $item) {
        $id = (int)$item['id'];
        $title = htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8');
        $description = nl2br(htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'));
        $source = htmlspecialchars($item['source'], ENT_QUOTES, 'UTF-8');
        $datetime = date('d.m.Y H:i', (int)$item['datetime']);

        echo '<article>';
        // echo '<h2>' . $title . '</h2>';
        echo "<h3><a href='?id=" . $item['id'] . "'>" . htmlspecialchars($item['title']) . "</a></h3>";
        echo '<p><em>Категория: ' . $category . ' | Дата: ' . $datetime . '</em></p>';
        echo '<p>' . $description . '</p>';
        echo '<p>Источник: ' . $source . '</p>';
        echo '<p>';
        echo '<a href="?del=' . $id . '" onclick="return confirm(\'Удалить новость?\');">Удалить</a>';
        echo '</p>';
        echo '</article><hr>';
    }
}
?>