<?php

/**
 * Page for displaying the reports
 */
class CRM_XlsReport_Page_Runner extends CRM_Core_Page {
  public function run() {
    $report = CRM_Utils_Array::value('report', $_GET, 'memberships');

    // Generate report
    require_once "XlsReport.php";
    $runner = new XlsReport();
    if (method_exists($runner, $report)) {
      $params = array();
      $file = $runner->$report($params);
      $ext = strtolower(substr(basename($file), 0, 3)); // xls or csv
      $filename = ucfirst($report) . '_Report-' . date('YmdHi') . ".$ext";
    }

    if (empty($file)) {
      CRM_Core_Session::setStatus(ts('No record found.'), ts('None Found'), 'info');
      return parent::run();
    }

    // Output the report as a file download
    $fp = fopen($file, 'rb');
    $mime_type = ($ext == 'xls' ? 'application/vnd.ms-excel' : 'text/csv');
    header('Content-type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    fpassthru($fp);
    fclose($fp);
    unlink($file);
    exit;
  }
}
