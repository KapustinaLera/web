<?php
class NumbersSquared implements Iterator
{
    private $start;
    private $end;
    private $current;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function rewind(): void
    {
        $this->current = $this->start;
    }

    public function valid(): bool
    {
        return $this->current <= $this->end;
    }

    public function next(): void
    {
        $this->current++;
    }

    public function key(): mixed
    {
        return $this->current;
    }

    public function current(): mixed
    {
        return $this->current ** 2;
    }
}

// Проверка работы (можно вывести прямо здесь или в отдельном файле index.php)
$obj = new NumbersSquared(3, 7);
foreach ($obj as $num => $square) {
    echo "Квадрат числа $num = $square<br>";
}
?>