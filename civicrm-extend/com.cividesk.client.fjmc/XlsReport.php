<?php

require_once 'packages/PHPExcel.php';
require_once 'packages/XlsReader.php';

class XlsReport
{

  protected $_phpExcel;
  protected $_styles;

  function __construct()
  {
    $this->_styles = array(
      'money' => array('numberformat' => array('code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE), 'alignment' => array('horizontal' => 'right')),
      'total' => array('borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font' => array('bold' => true)),
      'dollar' => array('numberformat' => array('code' => '$#,##0.00_);[Red]($#,##0.00)')),
      'percent' => array('numberformat' => array('code' => '0%;[Red](0%)')),
    );
  }

  function load($filename)
  {
    $realpath = dirname(__FILE__);
    $inputFileName = $realpath . DIRECTORY_SEPARATOR . $filename;
    $this->_phpExcel = PHPExcel_IOFactory::load($inputFileName);
  }

  function save()
  {
    $fileName = tempnam(sys_get_temp_dir(), 'XlsReport');
    $objWriter = PHPExcel_IOFactory::createWriter($this->_phpExcel, 'Excel5');
    $objWriter->save($fileName);
    return $fileName;
  }

  function saveCsv($dao)
  {
    $fileName = tempnam(sys_get_temp_dir(), 'CsvReport');
    $fp = fopen($fileName, 'w');
    // Write BOM for UFT-8
    fwrite($fp, "\xEF\xBB\xBF");
    $header = true;
    // write data rows
    while ($fields = $dao->fetch(PDO::FETCH_ASSOC)) {
      if ($header) {
        fputcsv($fp, array_keys($fields));
        $header = false;
      }
      fputcsv($fp, $fields);
    }
    fclose($fp);
    return $fileName;
  }

  // Excel Report for - "Club Members Details"
  function clubmembers($params)
  {
    // Load the spreadsheet template for the report
    $this->load('club_members.xls');
    $objWorksheet = $this->_phpExcel->getActiveSheet();

    $organizatioCID = $_GET['cid'];
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'id' => $organizatioCID,
      'return' => "display_name"
    );
    $organizatioName = '';
    $organizationData = civicrm_api('Contact', 'get', $params);
    if ( $organizationData['is_error'] == 0 && ! empty( $organizationData['values']) ) {
      $organizatioName = $organizationData['values'][0]['display_name'];
    }

    $sql = "select cca.id, cca.display_name, cca.first_name, cca.last_name,
            ce.email as Email, cph.phone as Phone,
            ca.street_address as Address, ca.city as City, cst.name as State, cy.name as Country
            FROM civicrm_contact cca
            LEFT JOIN civicrm_email ce ON (ce.contact_id = cca.id AND ce.is_primary = 1)
            LEFT JOIN civicrm_phone cph ON (cph.contact_id = cca.id AND cph.is_primary = 1)
            LEFT JOIN civicrm_address ca ON (ca.contact_id = cca.id AND ca.is_primary = 1)
            LEFT JOIN civicrm_state_province cst ON (cst.id = ca.state_province_id)
            LEFT JOIN civicrm_country cy ON (cy.id = ca.country_id)
            INNER JOIN civicrm_relationship cr on( cca.id = cr.contact_id_a)
            LEFT JOIN civicrm_contact ccb on ( ccb.id = cr.contact_id_b )
            WHERE cr.relationship_type_id = 10 and cr.is_active = 1 and cr.contact_id_b = $organizatioCID and cca.is_deleted = 0
            GROUP BY cca.id
            ORDER BY cca.id";

    $dao = CRM_Core_DAO::executeQuery($sql);

    $row = 4;

    //Set the Club Name
    $objWorksheet->setCellValue('B2', $organizatioName);
    $address = $city = $state = $country = '';

    while ($dao->fetch()) {
      $objWorksheet->setCellValue('A' . $row, $dao->id);
      $objWorksheet->setCellValue('B' . $row, $dao->first_name);
      $objWorksheet->setCellValue('C' . $row, $dao->last_name);
      $objWorksheet->setCellValue('D' . $row, $dao->Email);
      $objWorksheet->setCellValue('E' . $row, $dao->Phone);
      if(!empty($dao->Address)) { $dao->Address = $dao->Address . "," ; }
      if(!empty($dao->City)) { $dao->City = $dao->City . "," ; }
      if(!empty($dao->State)) { $dao->State = $dao->State . "," ; }
      if(!empty($dao->Country)) { $dao->Country = $dao->Country ; }

      $objWorksheet->setCellValue('F' . $row, $dao->Address . $dao->City . $dao->State . $dao->Country);
      $row++;
    }
    return $this->save();
  }
}




