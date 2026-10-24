<?php
namespace MVC\Models;

class Users
{
    public $collection;

    public function __construct(public ?array $users = null)
    {
        $users ??= [
            new User(
                 'dmitry.koterov@gmail.com',
                 'password',
                 'Дмитрий',
                 'Котеров'),
            new User(
                 'igorsimdyanov@gmail.com',
                 'password',
                 'Игорь',
                 'Симдянов'),
            new User(
                'ivan.petrov@mail.ru',
                'pass123',
                'Иван',
                'Петров'
            ),
            new User(
                'maria.sidorova@yandex.ru',
                'qwerty',
                'Мария',
                'Сидорова'
            ),
            new User(
                'alexey.volkov@bk.ru',
                '12345',
                'Алексей',
                'Волков'
            )
        ];
        $this->collection = $users;
    }
}
