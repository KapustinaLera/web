<?php
$id = filter_input(
    INPUT_GET,
    'del',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($id === false || $id === null) {
    $errMsg = 'Некорректный идентификатор новости';
} else {
    $result = $news->deleteNews($id);

    if ($result) {
        header('Location: news.php');
        exit;
    } else {
        $errMsg = 'Произошла ошибка при удалении новости';
    }
}
?>