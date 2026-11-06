<?php
$title = isset($_POST['title']) ? trim(strip_tags($_POST['title'])) : '';
$description = isset($_POST['description']) ? trim(strip_tags($_POST['description'])) : '';
$source = isset($_POST['source']) ? trim(strip_tags($_POST['source'])) : '';

$category = filter_input(
    INPUT_POST,
    'category',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 3]]
);

if (
    $title === '' ||
    $description === '' ||
    $source === '' ||
    $category === false ||
    $category === null
) {
    $errMsg = 'Заполните все поля формы!';
} else {
    $result = $news->saveNews($title, $category, $description, $source);

    if ($result) {
        header('Location: news.php');
        exit;
    } else {
        $errMsg = 'Произошла ошибка при добавлении новости';
    }
}
?>