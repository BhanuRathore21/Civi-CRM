<?php

class CsvReader {
  protected $handle, $row, $columns = [];
  protected $length;

  function __construct( $filename ) {
    $this->handle = fopen($filename, 'r');
    if ($this->handle == FALSE) {
      die("Unable to read file $filename");
    }
    $this->length = 5000;
    $this->columns = fgetcsv($this->handle, $this->length);
    $this->row = 1;
  }

  function getColumns() {
    return $this->columns;
  }

  // Dangerous - know what you're doing if using this function with an argument!
  function setColumns($columns) {
    $this->columns = $columns;
  }

  function getRowNumber() {
    return $this->row;
  }

  function getNextRow() {
    $data = fgetcsv($this->handle, $this->length);
    if ($data == FALSE) {
      return NULL;
    }
    foreach ($this->columns as $col => $name) {
      if ($name) {
        $data[$name] = $data[$col];
      } else { // column label was empty
        $c1 = (int)($col / 26);
        $c2 = $col % 26;
        $cn = ($c1 ? chr(ord('A')+$c1-1) : '').chr(ord('A')+$c2);
        $data[$cn] = $data[$col];
      }
    }
    $this->row++;
    return $data;
  }

  function close() {
    fclose($this->handle);
  }
}
