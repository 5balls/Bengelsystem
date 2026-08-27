<?php
// Login und Admin Status testen. Wenn kein Admin-Status, Weiterleiten auf index.php und beenden
require_once 'konfiguration.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

SESSION_START();
require 'SQL.php';
require '_functions.php';
$db_link = ConnectDB();
$pagename  = "ODS-Import";  // name of this page
$backlink  = "index.php";         // back button in table header from table header
$header = PageHeader($pagename);
$tablehead = TableHeader($pagename,$backlink);
require '_login.php';
require '_odt.php';

if ($AdminStatus != 1) {
    //Seite nur fuer Admins. Weiter zu index.php und exit, wenn kein Admin
    echo '<!doctype html><head><meta http-equiv="Refresh" content="0; URL=index.php" /></head></html>';
    exit;
}
$message = "";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ods_file'])) {
    $file = $_FILES['ods_file'];
    
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['ods'];
    $message = "";

    // Map German day names to English for PHP's DateTime parser
    $dayOffsets = [
        'Montag'     => 0,
        'Dienstag'   => 1,
        'Mittwoch'   => 2,
        'Donnerstag' => 3,
        'Freitag'    => 4,
        'Samstag'    => 5,
        'Sonntag'    => 6
    ];
    $helferLevel = [
        'Orga'   => 1,
        'Helfer' => 2
    ];
    $mondayThisWeek = $start_date->modify('monday this week');
    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB limit
                $fileNameNew = uniqid('', true) . "." . $fileExt;
                $fileDestination = 'uploads/' . $fileNameNew;

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    try {
                        // Load the ODS file
                        $reader = IOFactory::createReader('Ods');
                        $reader->setReadDataOnly(true); // Ignore styles, fonts, and colors to save memory
                        $spreadsheet = $reader->load($fileDestination);

                        foreach ($spreadsheet->getSheetNames() as $sheetName) {
                            if (preg_match('/^(Helfer|Orga)\s+(Montag|Dienstag|Mittwoch|Donnerstag|Freitag|Samstag|Sonntag)$/i', $sheetName, $matches)) {
                                $type = $matches[1];      // "Helfer" or "Orga"
                                $germanDay = $matches[2]; // e.g., "Mittwoch"
                                $offsetDays = $dayOffsets[$germanDay];

                                // Calculate the concrete date relative to your base start date
                                // 'this [Day]' or finding the offset works well if your sheets represent a single week
                                $sheetDate = $mondayThisWeek->modify("+$offsetDays days");
                                
                                $formattedDate = $sheetDate->format('Y-m-d'); // Or 'Y-m-d'

                                $message .= "<h2>Typ: $helferLevel[$type], Tag: $germanDay, Date: $formattedDate</h2>";

                                // Now you can load the sheet data and tag each row with this $sheetDate!
                                $sheet = $spreadsheet->getSheetByName($sheetName);
                                $message .= ParseDiensteSchichtenSheet($sheet, $helferLevel[$type], $sheetDate);
                                // ... process rows here ...
                            }
                            if(preg_match('/^(Beschreibung Dienste)\s+(Helfer|Orga)$/i', $sheetName, $matches)) {
                                $type = $matches[2];      // "Helfer" or "Orga"
                                $sheet = $spreadsheet->getSheetByName($sheetName);
                                $message .= ParseDiensteBeschreibungenSheet($sheet, $helferLevel[$type]);
                            }
                        }
                        $message .= "File uploaded successfully!";
                        // Delete file again:
                        unlink($fileDestination);
                    } catch (Exception $e) {
                        $message = "Error when reading the file: " . $e->getMessage() . "<br>";
                    }
                    // TODO: Parse $fileDestination here
                } else {
                    $message = "There was an error moving your file.";
                }
            } else {
                $message = "Your file is too large! Maximum size is 5MB.";
            }
        } else {
            $message = "There was an error uploading your file.";
        }
    } else {
        $message = "Invalid file type. Please upload a valid .ods file.";
    }
}

echo $header; // muss nach redirect-headern fuer POST ausgegeben werden
echo $tablehead; // variablen aus _login.php
?>
<h2>Upload ODS Spreadsheet</h2>

    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Action is left empty to submit to the same file -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="ods_file">Choose ODS file:</label>
        <input type="file" name="ods_file" id="ods_file" accept=".ods" required>
        <br><br>
        <button type="submit">Upload and Import</button>
    </form>
</body>
</html>

