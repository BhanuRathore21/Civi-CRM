<?php

function club_details_form_alterabc(&$form, &$form_state, $form_id) {
  if ($form_id == 'webform_client_form_5141') {
    $member_id =  $_REQUEST['cid1'];
    if ($member_id) {
	    $member = club_details_get_civi_member($member_id);
	    $form['submitted']['civicrm_1_contact_1_fieldset_fieldset']['email']['#value'] = $member->email;

	    //$form['#submit'][] = 'club_details_ajax_form_submit';

          $nid = $form['#node']->nid;
          $form['actions']['submit']['#ajax'] = array(
              'callback' => 'club_details_ajax_form_submit',
              //'wrapper' => 'webform-client-form-' . $nid,
              //'method' => 'replace',
              //'effect' => 'fade',
          );
    }


/*
display: inline-block;
    $form['submitted']['civicrm_1_contact_1_fieldset_fieldset']['civicrm_1_contact_1_contact_first_name']['#value'] = 'foobar1';
    $form['submitted']['civicrm_1_contact_1_fieldset_fieldset']['civicrm_1_contact_1_contact_last_name']['#value'] = 'foobar2';
    $form['submitted']['civicrm_1_contact_1_fieldset_fieldset']['civicrm_1_contact_1_address_street_address']['#value'] = 'foobar3';
    $form['submitted']['civicrm_1_contact_1_fieldset_fieldset']['civicrm_1_contact_1_address_supplemental_address_1']['#value'] = 'foobar4';
    $form['submitted']['civicrm_1_contact_1_fieldset_fieldset']['civicrm_1_contact_1_address_city']['#value'] = 'foobar5';
    $form['submitted']['civicrm_1_contact_1_fieldset_fieldset']['civicrm_1_contact_1_address_postal_code']['#value'] = 'foobar6';
*/
  }
}

function club_details_ajax_form_submit($form, &$form_state) {
drupal_set_message('adding commands1');

  // define the $sid variable (submission id from webform)
  $sid = $form_state['values']['details']['sid'];
  // if we have a sid then we know the form was properly submitted, otherwise, we'll just return the existing $form array
  if ($sid) {
	// TODO:save the data first at this block then show confirmation
	$commands = array();
	$commands[] = array('command', 'reload_parent');
	print ajax_render($commands);
	exit;	

  }
  else {
    // return the form
    return $form;
  }

}

function _custom_webform_submit($form, $form_state) {
  $webform_nid = $form['#node']->nid;
  $_SESSION['webform_client_form_'.$webform_nid] = 'submitted';
}


function club_details_member_update_form_validate1($form, &$form_state) {
    $addr1 = $form_state['values']['address1'];
    if (!strlen($addr1)) {
        form_set_error('addr1', 'Cannot be empty');
    }
}


