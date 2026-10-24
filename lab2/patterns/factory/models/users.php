<?php
namespace Factory\Models;

class Users extends Collection
{
    public function __construct(public ?array $users = null)
    {
        $users ??= [
            new User(
                 'dmitry.koterov@gmail.com',
                 'password',
                 'Дмитрий',
                 'Котеров'),
            new User(
                 'igor.simdyanov@gmail.com',
                 'password',
                 'Игорь',
                 'Симдянов'),
            new User(
                 'ivan.petrov@mail.ru',
                 'pass123',
                 'Иван',
                 'Петров'),
            new User(
                 'maria.sidorova@yandex.ru',
                 'qwerty',
                 'Мария',
                 'Сидорова'),
            new User(
                 'alexey.volkov@bk.ru',
                 '12345',
                 'Алексей',
                 'Волков')
        ];
        parent::__construct($users);
    }
}