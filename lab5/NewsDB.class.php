<?php
require_once __DIR__ . '/INewsDB.class.php';

class NewsDB implements INewsDB, IteratorAggregate
{
    const DB_NAME = 'news.db';
    
    const RSS_NAME = 'rss.xml';
    const RSS_TITLE = 'Последние новости';
    const RSS_LINK = 'https://f1326258.xsph.ru/lab5/news.php'; 

    private $_db;
    
    private array $items = [];

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

    protected function getDb()
    {
        return $this->_db;
    }

    private function getCategories(): void
    {
        $res = $this->_db->query('SELECT id, name FROM category');
        $this->items = [];
        
        if ($res) {
            while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                $this->items[$row['id']] = $row['name'];
            }
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
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

        if ($result) {
            $this->createRss();
        }

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

        $this->createRss();

        return $this->_db->changes() > 0;
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