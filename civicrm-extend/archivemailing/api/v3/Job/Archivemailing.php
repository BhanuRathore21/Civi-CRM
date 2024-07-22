<?php

use CRM_Archivemailing_ExtensionUtil as E;

/**
 * Job.archivemailing API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_job_archivemailing($params) {
  $logs = [];

  // Archive a specific mailing
  if (!empty($params['mailing_id'])) {
    $stat = CRM_Archivemailing_BAO_MailingArchiveStat::create([
      'mailing_id' => $params['mailing_id'],
    ]);

    $logs[] = 'Archived Mailing: ' . $params['mailing_id'] . ' (' . $stat->id . ')';
  }
  elseif (!empty($params['days'])) {
    // Calculate the date for "now - X days"
    $date = new DateTime('-' . $params['days'] . ' days');
    $archive_date = $date->format('Y-m-d');

    $result = civicrm_api3('Mailing', 'get', [
      'sequential' => 1,
      'return' => ["id", "scheduled_date"],
      'scheduled_date' => ['<' => $archive_date],
      'is_archived' => 0,
      'options' => ['limit' => 0],
    ]);

    foreach ($result['values'] as $key => $val) {
      $stat = CRM_Archivemailing_BAO_MailingArchiveStat::create([
        'mailing_id' => $val['id'],
      ]);

      $logs[] = 'Archived Mailing: ' . $val['id'] . ' (' . $stat->id . ')';
    }
  }
  else {
    throw new Exception('Please provide either a number of days (days=123) or a specific mailing_id to archive');
  }

  // This may be a bit controversial: if we have done cleanup, MySQL will not liberate the space
  // unless we run "optimize" on it ('recreate + analyze' for InnoDB). This could be problematic
  // if the table is huge and the hosting cannot handle it.
  if (!empty($logs)) {
    try {
      CRM_Core_DAO::executeQuery('OPTIMIZE TABLE civicrm_mailing_event_delivered');
      CRM_Core_DAO::executeQuery('OPTIMIZE TABLE civicrm_mailing_event_opened');
      CRM_Core_DAO::executeQuery('OPTIMIZE TABLE civicrm_mailing_event_queue');
      CRM_Core_DAO::executeQuery('OPTIMIZE TABLE civicrm_mailing_event_trackable_url_open');
      CRM_Core_DAO::executeQuery('OPTIMIZE TABLE civicrm_mailing_recipients');
    }
    catch (Exception $e) {
      $logs[] = "WARNING: Failed to optimize database tables: " . $e->getMessage();
    }
  }

  // Generate log-friendly output
  $output = '';

  if (!empty($logs)) {
    $output = '<ul>';
  }

  foreach ($logs as $line) {
    $output .= '<li>' . $line . '</li>';
  }

  if (!empty($t)) {
    $output .= '</ul>';
  }

  if (empty($output)) {
    $output = E::ts("OK");
  }

  return civicrm_api3_create_success($output);
}
