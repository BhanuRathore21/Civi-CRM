<?php
use CRM_Archivemailing_ExtensionUtil as E;

class CRM_Archivemailing_BAO_MailingArchiveStat extends CRM_Archivemailing_DAO_MailingArchiveStat {

  /**
   * Create a new MailingArchiveStat based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Archivemailing_DAO_MailingArchiveStat|NULL
   */
  public static function create($params) {
    $className = 'CRM_Archivemailing_DAO_MailingArchiveStat';
    $entityName = 'MailingArchiveStat';
    $hook = empty($params['id']) ? 'create' : 'edit';

    if (empty($params['mailing_id'])) {
      throw new Exception('Missing required param: mailing_id');
    }

    $mailing_id = $params['mailing_id'];

    // I guess we could feed create() with the stats, but otherwise we will fetch them
    if (count($params) == 1) {
      $is_archived = CRM_Core_DAO::singleValueQuery('SELECT is_archived FROM civicrm_mailing WHERE id = %1', [
        1 => [$mailing_id, 'Positive'],
      ]);

      $report = CRM_Mailing_BAO_Mailing::report($mailing_id);
      if ($is_archived && empty($report['event_totals']['delivered'])) {
        Civi::log()->info("archivemailing: mailing $mailing_id is already archived and stats seem empty, skipping stats archival");
      }
      else {
        $params['recipients'] = $report['event_totals']['queue'];
        $params['deliveries'] = $report['event_totals']['delivered'];
        $params['forwards'] = $report['event_totals']['forward'];
        $params['replies'] = $report['event_totals']['reply'];
        $params['unsubscribes'] = $report['event_totals']['unsubscribe'];
        $params['optouts'] = $report['event_totals']['optout'];
        $params['bounces'] = $report['event_totals']['bounce'];
        // unique opens
        $params['opens'] = $report['event_totals']['opened'];
        // url is a number here
        $params['clicks'] = $report['event_totals']['url'];
      }
    }

    // Check if a stats entry already exists
    $stat_id = CRM_Core_DAO::singleValueQuery('SELECT id FROM civicrm_mailing_archive_stat WHERE mailing_id = %1', [
      1 => [$mailing_id, 'Positive'],
    ]);

    if ($stat_id) {
      $params['id'] = $stat_id;
    }

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    // Delete the stats from CiviMail queue tables, assuming the FKs cascade deletion
    $result = civicrm_api3('MailingJob', 'get', [
      'mailing_id' => $mailing_id,
    ]);

    if (!empty($result['values'])) {
      $job_ids = array_keys($result['values']);

      CRM_Core_DAO::executeQuery('DELETE FROM civicrm_mailing_event_queue where job_id IN (%1)', [
        1 => [implode(',', $job_ids), 'CommaSeparatedIntegers'],
      ]);
    }

    // Update the mailing as being archived
    // Somewhat using the DAO on purpose, in case eventually we add a hook to detect mailing archival
    // from the UI, and want to automatically trigger the archival of stats.
    CRM_Core_DAO::executeQuery('UPDATE civicrm_mailing SET is_archived = 1 WHERE id = %1', [
      1 => [$mailing_id, 'Positive'],
    ]);

    CRM_Core_DAO::executeQuery('DELETE FROM civicrm_mailing_recipients WHERE mailing_id = %1', [
      1 => [$mailing_id, 'Positive'],
    ]);

    return $instance;
  }

}
