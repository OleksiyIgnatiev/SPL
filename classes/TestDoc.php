<?php
namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    
class TestDoc extends Page {

    public function displayBodyContent(): void {
        $folderPath = __DIR__ . '/../classes';

        // Отримуємо список усіх файлів у вказаній папці
        $allFiles = scandir($folderPath);

        // Фільтруємо файли .doc та .html
        $docFiles = preg_grep('/\.doc$/', $allFiles);
        $htmlFiles = preg_grep('/\.html$/', $allFiles);

        // Перетворення .doc файлів у .html
        if (count($docFiles) > 0) {
            echo '<h3>Содержимое папки с файлами .doc:</h3>';
            
            foreach ($docFiles as $docFile) {
                $filePath = $folderPath . '/' . $docFile;
                $htmlFilePath = preg_replace('/\.doc$/', '.html', $filePath);
                
                // Читаємо вміст .doc файлу
                $docContent = file_get_contents($filePath);

                // Перетворюємо вміст у HTML
                $htmlContent = nl2br(htmlspecialchars($docContent));
                
                // Зберігаємо перетворений вміст у новий HTML файл
                file_put_contents($htmlFilePath, $htmlContent);
                
                echo $docFile . " преобразован в " . basename($htmlFilePath) . "<br>";
            }
        } else {
            echo '<p>В указанной папке нет файлов .doc.</p>';
        }

        // Перетворення .html файлів у .doc
        if (count($htmlFiles) > 0) {
            echo '<h3>Содержимое папки с файлами .html:</h3>';
            
            foreach ($htmlFiles as $htmlFile) {
                $filePath = $folderPath . '/' . $htmlFile;
                $docFilePath = preg_replace('/\.html$/', '.doc', $filePath);
                
                // Читаємо вміст .html файлу
                $htmlContent = file_get_contents($filePath);

                // Перетворюємо HTML вміст у текст для .doc файлу
                $docContent = strip_tags($htmlContent);
                
                // Зберігаємо перетворений вміст у новий .doc файл
                file_put_contents($docFilePath, $docContent);
                
                echo $htmlFile . " преобразован в " . basename($docFilePath) . "<br>";
            }
        } else {
            echo '<p>В указанной папке нет файлов .html.</p>';
        }
    }
}

}
?>
