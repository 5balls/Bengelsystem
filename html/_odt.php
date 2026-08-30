<?php

require_once 'konfiguration.php';
require_once 'SQL.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;

function _getStyles()
{
    return [
        'title' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 12,
                'bold' => true,
                'italic' => true
            ]
        ],
        'dienst' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 10,
                'bold' => true
            ]
        ],
        'timerange' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 10
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ],
        'helfer_title' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ],
        'helfer_title_soll' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFFF00', // Hex code for yellow (do not include the # symbol)
                ]
            ]
        ],
        'telefon_title' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ],
        'telefon_title_soll' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFFF00', // Hex code for yellow (do not include the # symbol)
                ]
            ]
        ],
        'orga_title' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ],
        'orga_title_soll' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ],

        'infos' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'wrapText' => true
            ]
        ],
        'place' => [
            'font' => [
                'name' => 'Liberation Sans',
                'size' => 8,
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]

    ];
}

function ExportBeschreibungDienste(&$spreadsheet)
{
    $db_link = ConnectDB();
    $db_erg = HelferListe($db_link);
    while ($zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
        $helferliste[$zeile['HelferID']] = $zeile['Name'];
    }
    for($helferlevel = 2; $helferlevel >= 1; $helferlevel--){
        $styles = _getStyles();
        $helfer = [
            1 => 'Orga',
            2 => 'Helfer'
        ];
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle("Beschreibung Dienste ".$helfer[$helferlevel]);
        $sheet->getColumnDimensionByColumn(1)->setWidth(50);
        $sheet->getColumnDimensionByColumn(2)->setWidth(10);
        $db_erg = GetDienste($db_link);
        $column = 1;
        $row = 1;
        $sheet->setCellValue([$column, $row], "Beschreibung Dienste ".$helfer[$helferlevel]);
        $sheet->getStyle([$column, $row])->applyFromArray($styles['title']);
        while ($dienst = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
            if($dienst['HelferLevel'] != $helferlevel) continue;
            $row += 2;
            $range = Coordinate::stringFromColumnIndex($column).$row.':'.Coordinate::stringFromColumnIndex($column+1).$row;
            $sheet->mergeCells($range);
            $sheet->setCellValue([$column, $row], $dienst['Was']);
            $sheet->getStyle([$column, $row])->applyFromArray($styles['dienst']);
            $row++;
            $sheet->setCellValue([$column, $row], $dienst['Wo']);
            $sheet->getStyle([$column, $row])->applyFromArray($styles['place']);
            $column++;
            $sheet->setCellValue([$column, $row], $helferliste[$dienst['Leiter']]);
            $sheet->getStyle([$column, $row])->applyFromArray($styles['orga_title']);
            $column=1;
            $row++;
            $range = Coordinate::stringFromColumnIndex($column).$row.':'.Coordinate::stringFromColumnIndex($column+1).$row;
            $sheet->mergeCells($range);
            $sheet->setCellValue([$column, $row], $dienst['Info']);
            $sheet->getStyle([$column, $row])->applyFromArray($styles['infos']);
        }
    }
}

function ExportSchichtenSheets(&$spreadsheet, $start_date)
{
    $helfer = [
        1 => 'Orga',
        2 => 'Helfer'
    ];
    $title = [
        1 => "Orgaverantwortlichkeiten ",
        2 => "Helferschichten ",
    ];
    $weekDays = [
        1 => 'Montag',
        2 => 'Dienstag',
        3 => 'Mittwoch',
        4 => 'Donnerstag',
        5 => 'Freitag',
        6 => 'Samstag',
        7 => 'Sonntag'
    ];
    $styles = _getStyles();
    $db_link = ConnectDB();
    for($helferlevel = 2; $helferlevel >= 1; $helferlevel--){
        $date = $start_date;
        $found_dienst = true;
        while($found_dienst){
            $weekDay = $weekDays[$date->format('N')];
            $db_erg = GetDiensteForDay($db_link, $helferlevel, $date->format('Y-m-d'));
            // Create sheet (if we don't fill it we can delete it later):
            $numberOfSheets = $spreadsheet->getSheetCount();
            if($numberOfSheets>10){
                break;
            }
            if($numberOfSheets == 1)
            {
                $sheet = $spreadsheet->getActiveSheet();
                if($sheet->getTitle() != "Worksheet")
                {
                    $sheet = $spreadsheet->createSheet();
                }
            }
            else
            {
                $sheet = $spreadsheet->createSheet();
            }
            $row=1;
            $column=1;
            $sheet->setTitle($helfer[$helferlevel]." ".$weekDay);
            error_log("ODS Export: Sheet: ".$numberOfSheets." Titel: ".$helfer[$helferlevel]." ".$weekDay);
            $sheet->setCellValue([$column, $row], $title[$helferlevel].$weekDay);
            $sheet->getStyle([$column, $row])->applyFromArray($styles['title']);
            $column = 1;
            $row = 1;
            $found_dienst = false;
            while ($dienst = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
                $found_dienst = true;
                $db_erg2 = GetSchichtenMaxSollDienstDay($db_link, $dienst['DienstId'], $date->format('Y-m-d'));
                $maxSoll = 0;
                $maxMuss = 0;
                if($zeile = mysqli_fetch_array($db_erg2, MYSQLI_ASSOC))
                    $maxSoll = (int)$zeile['MaxSoll'];
                    $maxMuss = (int)$zeile['MaxMuss'];
                // Mitigate some SQL query bug
                if($maxMuss == 0){
                    error_log("maxMuss 0");
                    continue;
                }
                $row += 2;
                $sheet->setCellValue([$column, $row], $dienst['Was']);
                $sheet->getStyle([$column, $row])->applyFromArray($styles['dienst']);
                error_log("Dienst: ".$dienst['DienstId']." ".$dienst['Was']);
                for($column = 2; $column <= $maxSoll*$helferlevel + 1; $column += $helferlevel)
                {
                    if($helferlevel == 2)
                    {
                        if($column <= $maxMuss*$helferlevel + 1)
                        {
                            $sheet->setCellValue([$column, $row], "Helfer*in");
                            $sheet->getStyle([$column, $row])->applyFromArray($styles['helfer_title']);
                            $sheet->setCellValue([$column+1, $row], "Telefon");
                            $sheet->getStyle([$column+1, $row])->applyFromArray($styles['telefon_title']);
                        }
                        else
                        {
                            $sheet->setCellValue([$column, $row], "Optional Helfer*in");
                            $sheet->getStyle([$column, $row])->applyFromArray($styles['helfer_title_soll']);
                            $sheet->setCellValue([$column+1, $row], "Telefon");
                            $sheet->getStyle([$column+1, $row])->applyFromArray($styles['telefon_title_soll']);
                        }
                    }
                    else
                    {
                        $sheet->setCellValue([$column, $row], "Wer?");
                        $sheet->getStyle([$column, $row])->applyFromArray($styles['orga_title']);
                    }
                }
                $column = 1;

                $db_erg3 = GetSchichtenForDienstForDay($db_link, $dienst['DienstId'], $date->format('Y-m-d'));
                $einzelschicht = 0;
                $oldzeitvon = null;
                $helfername = array(array());
                $telefon = array(array());
                while($helferdata = mysqli_fetch_array($db_erg3, MYSQLI_ASSOC)){
                    $zeitvon = $helferdata['ZeitVon'];
                    if($oldzeitvon != $zeitvon)
                        $einzelschicht = 0;
                    $helfername[$zeitvon][$einzelschicht] = $helferdata['Name'];
                    $telefon[$zeitvon][$einzelschicht] = $helferdata['Handy'];
                    $einzelschicht++;
                    $oldzeitvon = $zeitvon;
                }

                $db_erg2 = GetSchichtenRangeDienstDay($db_link, $dienst['DienstId'], $date->format('Y-m-d'));
                while ($schicht = mysqli_fetch_array($db_erg2, MYSQLI_ASSOC)) {
                    error_log("Schicht ".$schicht['ZeitVon']."-".$schicht['ZeitBis']." Uhr");
                    $row++;
                    for($column = 2; $column <= $maxSoll*$helferlevel + 1; $column += $helferlevel)
                    {
                        if($helferlevel == 2)
                        {
                            if(isset($helfername[$schicht['ZeitVon']][$column/2-1]) ? $helfername[$schicht['ZeitVon']][$column/2-1] : null){
                                $sheet->setCellValue([$column, $row], $helfername[$schicht['ZeitVon']][$column/2-1]);
                            }
                            if(isset($telefon[$schicht['ZeitVon']][$column/2-1]) ? $telefon[$schicht['ZeitVon']][$column/2-1] : null){
                                $sheet->setCellValue([$column+1, $row], $telefon[$schicht['ZeitVon']][$column/2-1]);
                            }
                            if($column <= $maxMuss*$helferlevel + 1)
                            {
                                $sheet->getStyle([$column, $row])->applyFromArray($styles['helfer_title']);
                                $sheet->getStyle([$column+1, $row])->applyFromArray($styles['telefon_title']);
                            }
                            else
                            {
                                $sheet->getStyle([$column, $row])->applyFromArray($styles['helfer_title_soll']);
                                $sheet->getStyle([$column+1, $row])->applyFromArray($styles['telefon_title_soll']);
                            }
                        }
                        else
                        {
                            if(isset($helfername[$schicht['ZeitVon']][$column-2]) ? $helfername[$schicht['ZeitVon']][$column-2] : null){
                                $sheet->setCellValue([$column, $row], $helfername[$schicht['ZeitVon']][$column-2]);
                            }
                            $sheet->getStyle([$column, $row])->applyFromArray($styles['orga_title']);
                        }
                    }
                    $column = 1;
                    $sheet->setCellValue([$column, $row], $schicht['ZeitVon']."-".$schicht['ZeitBis']." Uhr");
                    $sheet->getStyle([$column, $row])->applyFromArray($styles['timerange']);
                }
            }
            $date = $date->modify("+1 day");
        }
        // The last sheet never has any Dienst on it so delete it:
        $spreadsheet = $sheet->getParent();
        $sheetIndex = $spreadsheet->getIndex($sheet);
        $spreadsheet->removeSheetByIndex($sheetIndex);
    }
}

function ParseDiensteBeschreibungenSheet($sheet, $helferlevel)
{
    $db_link = ConnectDB();
    $db_erg = GetDienste($db_link);
    $message = "<h2>Dienste Beschreibungen ".$helferlevel."</h2>";
    while ($zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
        if($zeile['HelferLevel'] == $helferlevel)
        {
            $dienste[$zeile['Was']] = $zeile['DienstID'];
        }
    }
    mysqli_free_result($db_erg);
    $highestRow = $sheet->getHighestDataRow();
    $highestColumn = $sheet->getHighestDataColumn();
    $allCellsArray = $sheet->rangeToArray('A1:' . $highestColumn . $highestRow, NULL, TRUE, FALSE);
    for ($row = 2; $row <= $highestRow; $row+=4){
        if ($allCellsArray[$row][0]) {
            $currentDienstName = $allCellsArray[$row][0];
            $message .= "<h3>".$currentDienstName."</h3>";
            $currentDienst = $dienste[$currentDienstName] ?? null;
            if($currentDienst == null)
                break;
            $dienstdata = GetEinzelDienst($db_link, $currentDienst);
            $info = $allCellsArray[$row+2][0];
            // Don't overwrite existing data with empty cells:
            if(!$info)
                $info = $dienstdata['Info'];
            $place = $allCellsArray[$row+1][0];
            if(!$place)
                $place = $dienstdata['Wo'];
            if($allCellsArray[$row+1][1]){
                $wer =  GetHelferIDByName($db_link, $allCellsArray[$row+1][1]);
            }
            else{
                $wer = $dienstdata['Leiter'];
            }
            ChangeDienst($db_link, $currentDienst, $currentDienstName, $place, $info, $wer, null, $helferlevel);
        }
    }
    return $message;
}

function ParseDiensteSchichtenSheet($sheet, $helferlevel, $date, &$touchedSchicht)
{
    $db_link = ConnectDB();
    $message = "";
    $highestRow = $sheet->getHighestDataRow();
    $highestColumn = $sheet->getHighestDataColumn();
    $allCellsArray = $sheet->rangeToArray('A1:' . $highestColumn . $highestRow, NULL, TRUE, FALSE);
    $currentDienstRow = 0;
    $currentDienstName = "";
    $currentDienst = null;
    $dienste = null;

    $db_erg = GetDienste($db_link);
    while ($zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
        if($zeile['HelferLevel'] == $helferlevel)
        {
            $dienste[$zeile['Was']] = $zeile['DienstID'];
        }
    }
    mysqli_free_result($db_erg);

    for ($row = 1; $row <= $highestRow; $row++){
        if ($allCellsArray[$row][0]) {
            if (preg_match('/^([0-9][0-9])(?:-([0-9][0-9]))? Uhr$/i', $allCellsArray[$row][0], $matches))
            {
                // If only start time is set assume 2 hours:
                if(!isset($matches[2])){
                    $matches[2] = sprintf('%02d',(int)$matches[1]+2);
                }
                if($currentDienstRow != 0){

                    if($currentDienst == null)
                    {
                        $currentDienst = $dienste[$currentDienstName] ?? null;
                        if($currentDienst == null)
                        {
                            $currentDienst = NewDienst($db_link, $currentDienstName, "", "", 251, null, $helferlevel);
                            $dienste[$currentDienstName] = $currentDienst;
                        }
                    }
                    $countEinzelSchichten = 0;
                    $countEinzelMussSchichten = 0;
                    for($column = 1; $column <= $highestColumn; $column+=$helferlevel)
                    {
                        if($allCellsArray[$currentDienstRow][$column])
                        {
                            $countEinzelSchichten++;
                            if(preg_match('/^(?!Soll|Optional)/', $allCellsArray[$currentDienstRow][$column]))
                                $countEinzelMussSchichten++;
                        }
                        else
                            break;
                    }
                    $fromHour = (int)$matches[1];
                    $toHour = (int)$matches[2];
                    if($fromHour == 24)
                        $fromDateTime = $date->modify("+1 day");
                    else
                        $fromDateTime = $date->modify("+".$matches[1]." hours");
                    if($toHour < $fromHour)
                        $toDateTime = $date->modify("+1 day")->modify("+".$matches[2]." hours");
                    else
                        $toDateTime = $date->modify("+".$matches[2]." hours");
                    $duration = $fromDateTime->diff($toDateTime)->format("%h:%i");
                    $from = $fromDateTime->format('Y-m-d H:i:s');
                    $to = $toDateTime->format('Y-m-d H:i:s');
                    $db_erg = GetMatchingSchicht($db_link, $currentDienst, $from, $to);
                    $zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC);
                    
                    if($zeile == null)
                    {// Schicht does not exist -> create:
                        NewSchicht($db_link, $currentDienst, $from, $to, $countEinzelSchichten, $duration, "ODS Import", $countEinzelMussSchichten);
                        // Get SchichtID  so we always have it
                        $db_erg = GetMatchingSchicht($db_link, $currentDienst, $from, $to);
                        $zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC);
                        $touchedSchicht[] = $zeile['SchichtID'];
                    }
                    else
                    {// Schicht exists -> change:
                        ChangeSchicht($db_link, $zeile['SchichtID'], $from, $to, $countEinzelSchichten, $duration, $countEinzelMussSchichten);
                        $touchedSchicht[] = $zeile['SchichtID'];
                    }
                    // Check if there are hints for EinzelSchichten not in the Database:

                    $db_erg = GetEinzelSchichtenForSchicht($db_link, $zeile['SchichtID']);
                    $helferids = [];
                    while( $einzelschicht = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)){
                        $helferids[] = $einzelschicht['HelferID'];
                        error_log("Einzelschicht HelferID ".$einzelschicht['HelferID']);
                    }
                    for($column = 1; $column <= $highestColumn; $column+=$helferlevel)
                    {
                        if($allCellsArray[$row][$column]){
                            $helferid =  GetHelferIDByName($db_link, $allCellsArray[$row][$column]);
                            if($helferid){
                                // We found the name, let's check if he already has a shift here:
                                if(!in_array($helferid, $helferids)){
                                    error_log("Schicht ".$currentDienstName." zuweisen an ".$allCellsArray[$row][$column]." id ".$helferid." row ".$row." column ".$column);
                                    HelferSchichtZuweisen($db_link, $helferid, $zeile['SchichtID']);
                                }
                            }
                        }
                        else {
                            break;
                        }
                    }
//                    $message .= "SELECT SchichtID FROM Schicht WHERE DienstID=".$currentDienst." AND Von=".$from." AND Bis=".$to."<br>";
                    $message .= $countEinzelSchichten." Schichten ".$matches[1]." bis ".$matches[2]." Uhr<br>";
                }
                else {
                    $message .= "Schichten ohne Dienst! Können nicht importiert werden!<br>";
                }

            }
            else
            {
                $currentDienstName = $allCellsArray[$row][0];
                $currentDienst = null;
                $currentDienstRow = $row;
                $message .= "<h3>Dienst ".$allCellsArray[$row][0]."</h3>";
            }
        }
    }
    return $message;
}

