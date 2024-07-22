<?php

//require dirname(__DIR__).'/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php';
require dirname(__DIR__).'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

class XlsReader {
  private $objPHPExcel, $filename;
  private $sheet, $rows, $rowNumber, $columns = [];
  private $changed = false; // whether we have changed the file, and need to save it back
  private $error;

  function __construct( $filename, $sheetname = '' ) {
// Remove warning in watchdog, as we don't really need any cache.
//    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
//    PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
    // Create the Excel_Reader object
    $this->filename = $filename;
    try {
      $this->objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->filename);
    } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
      $this->error = $e->getMessage();
      return $this->error;
    }
    if (empty($sheetname))
      $this->sheet = $this->objPHPExcel->getActiveSheet();
    else {
      $this->sheet = $this->objPHPExcel->getSheetByName($sheetname);
      // fallback to valid sheet if not found
      if (!$this->sheet) $this->sheet = $this->objPHPExcel->getActiveSheet();
    }
    $this->rows = $this->sheet->getRowIterator();
    $this->rowNumber = 0;
    // Get column headers from first line in file
    $this->columns = $this->getNextRow();
    $this->changed = false;
  }

  function getColumns() {
    return $this->columns; 
  }

  // Dangerous - know what you're doing if using this function with an argument!
  function setColumns($columns) {
    $this->columns = $columns; 
  }

  function getRowNumber() {
    return $this->rowNumber;
  }

  function getNextRow() {
    if (!$this->rows->valid()) return NULL;
    $row = $this->rows->current();
    $this->rowNumber++;

    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); // This loops all cells, even if it is not set.
    // Read values from spreadsheet
    $data = array();
    foreach ($cellIterator as $cell) {
      if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
        $data[] = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::toFormattedString($cell->getCalculatedValue(), 'yyyymmddhhmmss', null);
      } else {
        $data[] = trim($cell->getFormattedValue());
      }
    }
    // Add columns names as result keys if present
    foreach ($this->columns as $col => $name) {
      if ($name) {
        $data[$name] = $data[$col];
      }
    }
    $this->rows->next();

    return $data;
  }

  function setValue( $name, $value ) {
    // Find column
    foreach ($this->columns as $col => $colname) {
      if ($colname == $name) {
        $this->sheet->setCellValueByColumnAndRow($col, $this->rowNumber, $value);
        $this->changed = true;
      }
    }
  }

  function close() {
    if ($this->changed) {
      echo 'Saving Excel file ...';
      $pathinfo = pathinfo($this->filename);
      if ($pathinfo['extension'] == 'xls') {
        $class = 'Excel5';
      } else {
        $class = 'Excel2007';
      }
      $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->objPHPExcel, $class);
      $objWriter->save($this->filename);
    }
  }
}
