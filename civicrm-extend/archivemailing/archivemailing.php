<?php

require_once 'archivemailing.civix.php';
// phpcs:disable
use CRM_Archivemailing_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function archivemailing_civicrm_config(&$config) {
  _archivemailing_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function archivemailing_civicrm_xmlMenu(&$files) {
  _archivemailing_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function archivemailing_civicrm_install() {
  _archivemailing_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function archivemailing_civicrm_postInstall() {
  _archivemailing_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function archivemailing_civicrm_uninstall() {
  _archivemailing_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function archivemailing_civicrm_enable() {
  _archivemailing_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function archivemailing_civicrm_disable() {
  _archivemailing_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function archivemailing_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _archivemailing_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function archivemailing_civicrm_managed(&$entities) {
  _archivemailing_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function archivemailing_civicrm_angularModules(&$angularModules) {
  _archivemailing_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function archivemailing_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _archivemailing_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function archivemailing_civicrm_entityTypes(&$entityTypes) {
  _archivemailing_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_pageRun().
 */
function archivemailing_civicrm_pageRun(&$page) {
  $pageName = get_class($page);

  if ($pageName == 'CRM_Mailing_Page_Report') {
    // Display the archived stats, otherwise all stats will be zero (because we deleted them)
    $smarty = CRM_Core_Smarty::singleton();
    $report = $smarty->_tpl_vars['report'];

    if (!empty($report['mailing']['is_archived'])) {
      // @todo Use api4? or dao?
      $dao = CRM_Core_DAO::executeQuery('SELECT * FROM civicrm_mailing_archive_stat WHERE mailing_id = %1', [
        1 => [$report['mailing']['id'], 'Positive'],
      ]);

      if ($dao->fetch()) {
        $report['event_totals']['queue'] = $dao->recipients;
        $report['event_totals']['delivered'] = $dao->deliveries;
        $report['event_totals']['forward'] = $dao->forwards;
        $report['event_totals']['reply'] = $dao->replies;
        $report['event_totals']['unsubscribe'] = $dao->unsubscribes;
        $report['event_totals']['optout'] = $dao->optouts;
        $report['event_totals']['bounce'] = $dao->bounces;
        $report['event_totals']['opened'] = $dao->opens;
        // url is a number
        $report['event_totals']['url'] = $dao->clicks;
        $smarty->assign('report', $report);
      }
    }
  }
}
