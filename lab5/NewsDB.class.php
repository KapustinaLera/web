<?php
require_once __DIR__ . '/INewsDB.class.php';

class NewsDB implements INewsDB
{
    const DB_NAME = 'news.db';
    
    //  НОВЫЕ КОНСТАНТЫ ДЛЯ RSS 
    const RSS_NAME = 'rss.xml';
    const RSS_TITLE = 'Последние новости';
    const RSS_LINK = 'https://f1326258.xsph.ru/lab5/news.php'; 

    private $_db;

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

        // ВЫЗОВ МЕТОДА СОЗДАНИЯ RSS ПОСЛЕ УСПЕШНОГО ДОБАВЛЕНИЯ
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

        // ОБНОВЛЯЕМ RSS ПРИ УДАЛЕНИИ НОВОСТИ 
        $this->createRss();

        return $this->_db->changes() > 0;
    }

    // НОВЫЙ МЕТОД: Создание RSS-ленты с помощью DOMDocument
    public function createRss(): void
    {
        // 1. Создаем объект DOMDocument
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true; 
        $dom->preserveWhiteSpace = false;

        // 2. Создаем корневой элемент rss и привязываем к $dom
        $rss = $dom->createElement('rss');
        $dom->appendChild($rss);

        // 3. Создаем и добавляем атрибут version="2.0"
        $version = $dom->createAttribute('version');
        $version->value = '2.0';
        $rss->appendChild($version);

        // 4. Создаем элемент channel и привязываем к rss
        $channel = $dom->createElement('channel');
        $rss->appendChild($channel);

        // 5. Создаем элементы title и link для channel (из констант)
        $title = $dom->createElement('title', self::RSS_TITLE);
        $channel->appendChild($title);

        $link = $dom->createElement('link', self::RSS_LINK);
        $channel->appendChild($link);

        // 6. Получаем данные из БД
        $newsList = $this->getNews();

        if ($newsList) {
            foreach ($newsList as $item) {
                // Создаем элемент item
                $xmlItem = $dom->createElement('item');

                // title (экранируем спецсимволы для XML)
                $xmlTitle = $dom->createElement('title', htmlspecialchars($item['title'], ENT_XML1, 'UTF-8'));
                $xmlItem->appendChild($xmlTitle);

                // link (ссылка на конкретную новость)
                $itemLink = self::RSS_LINK . '?id=' . $item['id'];
                $xmlLink = $dom->createElement('link', $itemLink);
                $xmlItem->appendChild($xmlLink);

                // description (ОБЯЗАТЕЛЬНО с CDATA секцией!)
                $xmlDesc = $dom->createElement('description');
                $cdata = $dom->createCDATASection($item['description']);
                $xmlDesc->appendChild($cdata);
                $xmlItem->appendChild($xmlDesc);

                // pubDate (формат RFC 2822, требуемый стандартом RSS)
                $pubDateStr = date(DATE_RSS, (int)$item['datetime']);
                $xmlPubDate = $dom->createElement('pubDate', $pubDateStr);
                $xmlItem->appendChild($xmlPubDate);

                // category
                $xmlCategory = $dom->createElement('category', htmlspecialchars($item['category'], ENT_XML1, 'UTF-8'));
                $xmlItem->appendChild($xmlCategory);

                // Привязываем item к channel
                $channel->appendChild($xmlItem);
            }
        }

        // 7. Сохраняем файл на диск (имя из константы RSS_NAME)
        $dom->save(self::RSS_NAME);
    }
}
?>
