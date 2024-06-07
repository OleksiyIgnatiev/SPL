<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class testXML extends Page {
        function process_tags($tag, $content) {
            // Опрацювання тегів
            switch ($tag) {
                case 'b':
                    return "<strong>$content</strong>";
                case 'i':
                    return "<em>$content</em>";
                // Додайте інші обробники тегів за необхідності
                default:
                    return $content;
            }
        }
    
        function process_text_content($content) {
            // Обробник текстового вмісту
            // Наприклад, можна застосувати функцію екранування для безпечного відображення тексту у HTML
            return htmlspecialchars($content);
        }
    
        // Реєстрація функцій-обробників
        private $tag_handlers = [
            'b' => 'process_tags',
            'i' => 'process_tags',
            // Додайте інші теги за необхідності
        ];
    
        // Реєстрація функції обробки текстового вмісту
        private $text_content_handler = 'process_text_content';
    
        // Функція-обробник кінцевих тегів
        function process_end_tags($tag, $content) {
            // Опрацювання кінцевих тегів (у випадку потреби)
            return $content;
        }
    
        // Реєстрація функції-обробника для кінцевих тегів
        private $end_tag_handlers = [
            // Додайте обробники для кінцевих тегів за необхідності
        ];
    
        // Функція для парсингу XML і створення HTML-таблиці
        function parseXMLAndCreateTable() {
            // Завантаження XML-файлу
            $xml_file = "./classes/data.xml";
            $xml = simplexml_load_file($xml_file);
    
            // Створення HTML-таблиці
            $html_table = '<table>';
    
            foreach ($xml->children() as $child) {
                // Обробка кожного елементу XML
                $tag = $child->getName();
                $content = (string)$child;
    
                // Обробка тегів та текстового вмісту
                if (isset($GLOBALS['tag_handlers'][$tag])) {
                    $content = call_user_func($GLOBALS['tag_handlers'][$tag], $tag, $content);
                } elseif (isset($GLOBALS['end_tag_handlers'][$tag])) {
                    $content = call_user_func($GLOBALS['end_tag_handlers'][$tag], $tag, $content);
                } elseif ($this->text_content_handler) {
                    $content = $this->{$this->text_content_handler}($content);

                }
                
    
                // Додавання до HTML-таблиці
                $html_table .= "<tr><td>$tag</td><td>$content</td></tr>";
            }
    
            $html_table .= '</table>';
    
            // Виведення HTML-таблиці
            echo $html_table;
        }

        public function displayBodyContent(): void {
            $this->parseXMLAndCreateTable();
            
        }
    }

}
?>
