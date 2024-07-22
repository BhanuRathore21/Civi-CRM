<?php

/**
* Custom Contact Relationships Tab hooks.
*
* This extension modifies Contact Relationships tab view.
*/


/**
* Implemets CiviCRM 'alterTemplateFile' hook.
*
* @param String $formName Name of current form.
* @param CRM_Core_Form $form Current form.
* @param CRM_Core_Form $context Page or form.
* @param String $tplName The file name of the tpl - alter this to alter the file in use.
*/
function customContactRelationshipsTab_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName) {
  //Contact summary main page
  if($form instanceof CRM_Contact_Page_View_Summary) {
    /*
    * In Contact summary page CRM_Contact_Page_View_Relationship is loaded with AJAX so CRM_Core_Resources does not work 
    * directy. We need to inject JavaScript to main Summary page and listen tab change to init our own logic.
    */
    CRM_Core_Resources::singleton()->addScriptFile('com.github.anttikekki.customContactRelationshipsTab', 'customContactRelationshipsTab.js');
    
    //Add CMS neutral ajax callback URL
    $contactId = (int) $form->getTemplate()->get_template_vars("contactId");
    $ajaxURL = CRM_Utils_System::url('civicrm/customContactRelationshipsTab/ajax', 'contactId='.$contactId);
    CRM_Core_Resources::singleton()->addSetting(array('customContactRelationshipsTab' => array('ajaxURL' => $ajaxURL)));
  }
  //Contact Relationship tab and Relationship edit & view
  else if($form instanceof CRM_Contact_Page_View_Relationship) {
    /*
    * CRM_Contact_Page_View_Relationship is displayed in full page mode when viewin and editing relationship. CRM_Core_Resources 
    * works so JavaScript can be injected directly.
    */
    $action = $form->getTemplate()->get_template_vars("action");
    if($action == CRM_Core_Action::VIEW || $action == CRM_Core_Action::UPDATE) {
      CRM_Core_Resources::singleton()->addScriptFile('com.github.anttikekki.customContactRelationshipsTab', 'customContactRelationshipsTab.js');
    }
  }
}

/**
* Implemets CiviCRM 'config' hook.
*
* @param object $config the config object
*/
function customContactRelationshipsTab_civicrm_config(&$config) {
  $template =& CRM_Core_Smarty::singleton();
  $extensionDir = dirname(__FILE__);
 
  // Add extension template directory to the Smarty templates path
  if (is_array($template->template_dir)) {
    array_unshift($template->template_dir, $extensionDir);
  }
  else {
    $template->template_dir = array($extensionDir, $template->template_dir);
  }

  //Add extension folder to included folders list so that AJAX.php is found whe accessin it from URL
  $include_path = $extensionDir . DIRECTORY_SEPARATOR . PATH_SEPARATOR . get_include_path();
  set_include_path($include_path);
}

/**
* Implemets CiviCRM 'xmlMenu' hook.
*
* @param array $files the array for files used to build the menu. You can append or delete entries from this file. 
* You can also override menu items defined by CiviCRM Core.
*/
function customContactRelationshipsTab_civicrm_xmlMenu( &$files ) {
  //Add Ajax and Admin page URLs to civicrm_menu table so that they work
  $files[] = dirname(__FILE__)."/menu.xml";
}

/**
* Implemets CiviCRM 'navigationMenu' hook.
*
* @param array $params the navigation menu array
*/
function customContactRelationshipsTab_civicrm_navigationMenu(&$params) {
    //Find last index of Administer menu children
    $maxKey = max(array_keys($params[108]['child']));
    
    //Add extension menu as Admin menu last children
    $params[108]['child'][$maxKey+1] = array(
       'attributes' => array (
          'label'      => 'CustomContactRelationshipsTab',
          'name'       => 'CustomContactRelationshipsTab',
          'url'        => null,
          'permission' => null,
          'operator'   => null,
          'separator'  => null,
          'parentID'   => null,
          'navID'      => $maxKey+1,
          'active'     => 1
        ),
    );
}