<?php

define('CIVICRM_MOSAICO_CUSTOM_PLUGINS', 'link hr paste lists textcolor code civicrmtoken');
define('CIVICRM_MOSAICO_CUSTOM_TOOLBAR', 'bold italic forecolor backcolor hr styleselect removeformat | civicrmtoken | link unlink | pastetext code');

require_once 'moscustom.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function moscustom_civicrm_config(&$config) {
  _moscustom_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function moscustom_civicrm_xmlMenu(&$files) {
  _moscustom_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function moscustom_civicrm_install() {
  _moscustom_civix_civicrm_install();
  // Check dependencies and display error messages
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function moscustom_civicrm_uninstall() {
  return _moscustom_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function moscustom_civicrm_enable() {
  return _moscustom_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function moscustom_civicrm_disable() {
  return _moscustom_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function moscustom_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _moscustom_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function moscustom_civicrm_managed(&$entities) {
  return _moscustom_civix_civicrm_managed($entities);
}


function moscustom_civicrm_mosaicoBaseTemplates(&$templates) {
  // Delete unused templates
  unset($templates['tedc15']);
  unset($templates['tutorial']);

  // And add our own ...
  $path = CRM_Core_Resources::singleton()->getUrl('com.cividesk.email.mosaico.custom', "/templates/");
  $template = 'versafix-cividesk';
  $templates[$template] = array(
    'name' => $template,
    'title' => $template,
    'path' => $path . "$template/template-$template.html",
    'thumbnail' => $path . "$template/edres/_full.png",
  );
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function moscustom_civicrm_navigationMenu(&$params) {

  $domain = getenv('DOMAIN');

  // Skip menu changes for customers that had Mosaico before
  if (in_array($domain, array('dcsi', 'rutacivica', 'thefirsttee', 'wfsb', 'cpf'))) return;

  // Finds the 'Mailings' submenu
  $id_mailing = _menu_find($params, 'Mailings');
  $menu = $params[$id_mailing]['child'];

  // Re-key menu as will ease re-ordering
  $menu = array_values($menu);

  $id_mosaico = _menu_find($menu, 'New Mailing');
  $id_classic = _menu_find($menu, 'traditional_mailing');
  $id_mosatpl = _menu_find($menu, 'mosaico_templates');
  if (is_null($id_mosaico) || is_null($id_classic) || is_null($id_mosatpl)) {
    return;
  }

  // Change the labels
  $menu[$id_mosaico]['attributes']['label'] = 'New Mailing (Mosaico)';
  $menu[$id_classic]['attributes']['label'] = 'New Mailing';

  // Re-order the menu
  $tmp = $menu[$id_mosaico];
  $menu[$id_mosaico] = $menu[$id_classic];
  $menu[$id_classic] = $menu[$id_mosatpl];
  $menu[$id_mosatpl] = $tmp;

  // Re-calculate the NavIDs
  foreach ($menu as $key => &$item) {
    $item['attributes']['navID'] = $key;
  }

  // And re-assign modified menu to parent
  $params[$id_mailing]['child'] = $menu;
}

function _menu_find($menu, $name) {
  foreach ($menu as $key => $value) {
    if ($value['attributes']['name'] == $name) {
      return $key;
    }
  }
  return NULL;
}

function moscustom_civicrm_mosaicoConfig(&$config) {
    $mosaico_plugins = CRM_Core_BAO_Setting::getItem('Mosaico Preferences', 'mosaico_custom_plugins', NULL, CIVICRM_MOSAICO_CUSTOM_PLUGINS);
    $mosaico_toolbar = CRM_Core_BAO_Setting::getItem('Mosaico Preferences', 'mosaico_custom_toolbar', NULL, CIVICRM_MOSAICO_CUSTOM_TOOLBAR);

    if ($mosaico_plugins) {
      $config['tinymceConfigFull']['plugins'] = array($mosaico_plugins);
      $config['tinymceConfig']['plugins'] = array($mosaico_plugins);
    }
    if ($mosaico_toolbar) {
      $config['tinymceConfigFull']['toolbar1'] = $mosaico_toolbar;
      $config['tinymceConfig']['toolbar1'] = $mosaico_toolbar;
    }
}


/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function moscustom_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _moscustom_civix_civicrm_alterSettingsFolders($metaDataFolders);
}


function moscustom_civicrm_preProcess($formName, &$form) {
  if (in_array($formName, array('CRM_Mosaico_Form_MosaicoAdmin'))) {
    $settings = array(
      'mosaico_layout' => 'Mosaico Preferences',
      'mosaico_custom_templates_dir' => 'Mosaico Custom Templates Directory',
      'mosaico_custom_templates_url' => 'Mosaico Custom Templates URL',
      'mosaico_custom_plugins' => 'Mosaico Plugin List',
      'mosaico_custom_toolbar' => 'Mosaico Toolbar'
    );
    $form->setVar('_settings', $settings);
  }
}
