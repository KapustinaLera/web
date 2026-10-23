<?php
declare(strict_types=1);

namespace MyProject\Classes;

/**
 * Интерфейс SuperUserInterface
 * Определяет метод для получения информации о супер-пользователе в виде массива
 */
interface SuperUserInterface
{
    /**
     * Возвращает ассоциативный массив со свойствами объекта
     * @return array<string, mixed>
     */
    public function getInfo(): array;
}
