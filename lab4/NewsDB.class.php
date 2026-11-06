<?php
require_once __DIR__ . '/INewsDB.class.php';

// class NewsDB implements INewsDB
class NewsDB implements INewsDB, IteratorAggregate
{
    const DB_NAME = 'news.db';

    private $_db;
    private $items = [];

    public function __construct()
    {
        $isNew = !file_exists(self::DB_NAME);

        $this->_db = new SQLite3(self::DB_NAME);

        if ($isNew) {
            $this->_db->exec("
                CREATE TABLE msgs(
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT,
                    category INTEGER,
                    description TEXT,
                    source TEXT,
                    datetime INTEGER
                )
            ");

            $this->_db->exec("
                CREATE TABLE category(
                    id INTEGER,
                    name TEXT
                )
            ");

            $this->_db->exec("
                INSERT INTO category(id, name)
                SELECT 1 as id, 'Политика' as name
                UNION SELECT 2 as id, 'Культура' as name
                UNION SELECT 3 as id, 'Спорт' as name
            ");
           
        }
        $this->getCategories();
    }

    public function __destruct()
    {
        if ($this->_db instanceof SQLite3) {
            $this->_db->close();
        }
    }

    // Доступ на чтение свойства $_db для классов-наследников
    protected function getDb()
    {
        return $this->_db;
    }

    public function saveNews($title, $category, $description, $source)
    {
        $dt = time();

        $stmt = $this->_db->prepare("
            INSERT INTO msgs(title, category, description, source, datetime)
            VALUES(:title, :category, :description, :source, :datetime)
        ");

        if ($stmt === false) {
            return false;
        }

        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':category', (int)$category, SQLITE3_INTEGER);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':source', $source, SQLITE3_TEXT);
        $stmt->bindValue(':datetime', $dt, SQLITE3_INTEGER);

        $result = $stmt->execute();
        $stmt->close();

        return (bool)$result;
    }

    public function getNews()
    {
        $sql = "
            SELECT msgs.id as id, title, category.name as category,
                   description, source, datetime
            FROM msgs, category
            WHERE category.id = msgs.category
            ORDER BY msgs.id DESC
        ";

        $res = $this->_db->query($sql);

        if ($res === false) {
            return false;
        }

        $news = [];

        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $news[] = $row;
        }

        $res->finalize();

        return $news;
    }

    public function deleteNews($id)
    {
        $stmt = $this->_db->prepare("DELETE FROM msgs WHERE id = :id");

        if ($stmt === false) {
            return false;
        }

        $stmt->bindValue(':id', (int)$id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $stmt->close();

        if (!$result) {
            return false;
        }

        // Если запись не найдена, changes() вернёт 0
        return $this->_db->changes() > 0;
    }
    
        // Новый метод для получения категорий
    private function getCategories()
    {
        $sql = "SELECT id, name FROM category";
        $res = $this->_db->query($sql);

        if ($res) {
            while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                // Ключ = id, Значение = name
                $this->items[$row['id']] = $row['name'];
            }
        }
    }

    // Новый метод для итератора
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
?>