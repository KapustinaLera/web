<?php
require_once __DIR__ . '/INewsDB.class.php';

class NewsDB implements INewsDB, IteratorAggregate
{
    const DB_NAME = 'news.db';
    const RSS_NAME = 'rss.xml';
    const RSS_TITLE = 'Последние новости';
    const RSS_LINK = 'https://f1326258.xsph.ru/lab6/news.php'; 

    /** @var PDO */
    private $_db;
    private array $items = [];

    public function __construct()
    {
        $isNew = !file_exists(self::DB_NAME) || filesize(self::DB_NAME) === 0;

        try {
            // Подключение через PDO
            $this->_db = new PDO('sqlite:' . self::DB_NAME);
            // Настраиваем выброс исключений при ошибках
            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($isNew) {
                $this->_db->beginTransaction();

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
                    SELECT 1, 'Политика'
                    UNION SELECT 2, 'Культура'
                    UNION SELECT 3, 'Спорт'
                ");

                $this->_db->commit();
            }
            $this->getCategories(); 
        } catch (PDOException $e) {
            // Откат транзакции при ошибке
            if (isset($this->_db) && $this->_db->inTransaction()) {
                $this->_db->rollBack();
            }
            die("Невозможно создать или инициализировать базу данных. Ошибка: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        // В PDO закрытие соединения происходит обнулением переменной
        $this->_db = null;
    }

    protected function getDb(): PDO
    {
        return $this->_db;
    }

    private function getCategories(): void
    {
        try {
            $stmt = $this->_db->query('SELECT id, name FROM category');
            // FETCH_KEY_PAIR автоматически создает массив вида [id => name]
            $this->items = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            $this->items = [];
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function saveNews($title, $category, $description, $source)
    {
        try {
            $dt = time();
            
            $qTitle = $this->_db->quote($title);
            $qDesc = $this->_db->quote($description);
            $qSource = $this->_db->quote($source);
            $qCat = (int)$category; 

            $sql = "INSERT INTO msgs (title, category, description, source, datetime) 
                    VALUES ($qTitle, $qCat, $qDesc, $qSource, $dt)";
            
            $this->_db->exec($sql);
            
            $this->createRss();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getNews()
    {
        try {
            $sql = "SELECT msgs.id as id, title, category.name as category,
                           description, source, datetime
                    FROM msgs, category
                    WHERE category.id = msgs.category
                    ORDER BY msgs.id DESC";
            
            $stmt = $this->_db->query($sql);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteNews($id)
    {
        try {
            $id = (int)$id;
            $sql = "DELETE FROM msgs WHERE id = $id";
            $this->_db->exec($sql);
            
            $this->createRss();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createRss(): void
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true; 
        $dom->preserveWhiteSpace = false;

        $rss = $dom->createElement('rss');
        $dom->appendChild($rss);

        $version = $dom->createAttribute('version');
        $version->value = '2.0';
        $rss->appendChild($version);

        $channel = $dom->createElement('channel');
        $rss->appendChild($channel);

        $title = $dom->createElement('title', self::RSS_TITLE);
        $channel->appendChild($title);

        $link = $dom->createElement('link', self::RSS_LINK);
        $channel->appendChild($link);

        $newsList = $this->getNews();

        if ($newsList) {
            foreach ($newsList as $item) {
                $xmlItem = $dom->createElement('item');

                $xmlTitle = $dom->createElement('title', htmlspecialchars($item['title'], ENT_XML1, 'UTF-8'));
                $xmlItem->appendChild($xmlTitle);

                $itemLink = self::RSS_LINK . '?id=' . $item['id'];
                $xmlLink = $dom->createElement('link', $itemLink);
                $xmlItem->appendChild($xmlLink);

                $xmlDesc = $dom->createElement('description');
                $cdata = $dom->createCDATASection($item['description']);
                $xmlDesc->appendChild($cdata);
                $xmlItem->appendChild($xmlDesc);

                $pubDateStr = date(DATE_RSS, (int)$item['datetime']);
                $xmlPubDate = $dom->createElement('pubDate', $pubDateStr);
                $xmlItem->appendChild($xmlPubDate);

                $xmlCategory = $dom->createElement('category', htmlspecialchars($item['category'], ENT_XML1, 'UTF-8'));
                $xmlItem->appendChild($xmlCategory);

                $channel->appendChild($xmlItem);
            }
        }

        $dom->save(__DIR__ . '/' . self::RSS_NAME);
    }
}
?>