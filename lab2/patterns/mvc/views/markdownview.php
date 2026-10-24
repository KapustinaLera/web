<?php
namespace MVC\Views;

/**
 * Представление для вывода данных в формате Markdown
 */
class MarkdownView extends ViewFactory
{
    protected object $decorator;

    public function __construct(object $decorator)
    {
        $this->decorator = $decorator;
    }

    public function render(): string
    {
        // Формируем заголовок и тело из методов декоратора
        $title = $this->decorator->title();
        $body = $this->decorator->body();

        return "# {$title}\n\n{$body}\n";
    }
}