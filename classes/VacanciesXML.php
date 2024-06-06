<?php
namespace pages {

    use Exception;
    use DOMDocument;
    use SQLite3;
    use SimpleXMLElement;


    require 'page.php';

    class VacanciesXML extends Page
    {

        public function displayBodyContent(): void
        {
            // Перевірка наявності і не порожність XML-файлу
            $xmlFilePath = 'vacancies.xml';
            if (!file_exists($xmlFilePath) || filesize($xmlFilePath) <= 0) {
                $this->createXMLFromDatabase();
            }

            $xmlContent = file_get_contents($xmlFilePath);
            $xml = new SimpleXMLElement($xmlContent);


            // Виведення даних про вакансії
            foreach ($xml->vacancy as $vacancy) {
                echo "
                    <div class='vacancie_wraper'>
                        <a href='/vacancie/{$vacancy->vacancy_id}' class='vacancie'>
                            <div class='vacancie__title'> {$vacancy->description}</div>
                            <div class='vacancie__title'> {$vacancy->monthly_salary} ₴</div>
                            <div class='vacancie__title'>   {$vacancy->company_name}</div>
                        </a>
                        <button class='deleteVacancyBtn' data-id='{$vacancy->vacancy_id}'>Видалити</button>
                    </div>
                ";
            }
        }

        private function createXMLFromDatabase(): void
        {
            // Підключення до бази даних SQLite
            $dbPath = 'lw1.db';
            $conn = new SQLite3($dbPath);
        
            // Перевірка з'єднання
            if (!$conn) {
                die("Connection failed: Unable to open database.");
            }
        
            // Запит до бази даних для вибору даних з таблиці "vacancy"
            $sql = "SELECT * FROM vacancy";
            $result = $conn->query($sql);
        
            // Перевірка наявності даних
            if ($result) {
                // Створення об'єкта DOM
                $dom = new DOMDocument();
                $dom->formatOutput = true; // Встановлення форматування
        
                $vacancies = $dom->createElement('vacancies');
                $dom->appendChild($vacancies);
        
                // Витягнення рядків даних та створення елементів XML
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $vacancy = $dom->createElement('vacancy');
                    foreach ($row as $key => $value) {
                        $child = $dom->createElement($key, htmlspecialchars($value));
                        $vacancy->appendChild($child);
                    }
                    $vacancies->appendChild($vacancy);
                }
        
                // Збереження XML-документа в файл
                $dom->save('vacancies.xml');
                echo 'XML document created successfully!';
            } else {
                echo 'No vacancies found.';
            }
        
            // Закриття підключення до бази даних
            $conn->close();
        }
        
    }
}