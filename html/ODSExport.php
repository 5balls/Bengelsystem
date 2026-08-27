<?php
// Login und Admin Status testen. Wenn kein Admin-Status, Weiterleiten auf index.php und beenden
require_once 'konfiguration.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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

// 1. Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
ExportSchichtenSheets($spreadsheet, $start_date);
ExportBeschreibungDienste($spreadsheet);

// 4. Set HTTP headers so the browser treats the output as an ODS download
header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
$currentdate = new DateTime();
header('Content-Disposition: attachment; filename="'.$start_date->format('Y').'_'.preg_replace('/[^a-zA-Z0-9]/', '_', EVENTNAME).'_helferdb_'.$currentdate->format('Ymd').'.ods"');
header('Cache-Control: max-age=0');

// 5. Create an ODS writer and save directly to PHP's output stream
$writer = IOFactory::createWriter($spreadsheet, 'Ods');
$writer->save('php://output');
exit();
?>
