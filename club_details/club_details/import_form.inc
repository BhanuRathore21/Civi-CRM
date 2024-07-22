<?php

/**
* Implements hook_form
*/
function club_data_import_form($form, &$form_state) {
  global $base_url;

  $form['intro']['#markup'] = <<<EOT
<p>Chaverot provides the capability to import your own spreadsheet.  It will import the following columns.  Those with an asterisk are required.  Any other columns will be ignored.</p>
<ul>
<li>Prefix [allowed values:  Mr., Ms., Mrs., Dr., Prof., Hon., Rabbi, Cantor or Hazzan - NOTE: periods are required]</li>
<li>First Name*</li>
<li>Last Name*</li>
<li>Suffix [allowed values: Jr., Sr., Esq., II, III, IV, V, VI or VII - NOTE: periods are required]</li>
<li>Email* [WARNING: a blank value in this field will remove an existing email in Chaverot]<br /></li>
<li>Street Address</li>
<li>City</li>
<li>State</li>
<li>Country [allowed values: United States or Canada]</li>
<li>Zip<br /></li>
<li>Landline</li>
<li>Mobile</li>
<li>Primary Phone [allowed values: Landline or Mobile]</li>
<li>Active [allowed values: Yes, No or Deceased, defaults to Yes]</li>
</ul>
<p>* Required columns.</p>

<p>Columns can appear in any order.  The column titles are not case sensitive but the textual name must be exactly as it appears in the above list.</p>

<p>Please capitalize the first letter of the first and last names and then use lower case letters for the balance.</p>

<p>The import only affects names in your list.  If a name is not in the list, it will not deactivate it.</p>

<p>Note that the name is made up of multiple columns.  If your current spreadsheet has the name in a single column, you can use the Data->Text to Column in Excel to split the name into multiple columns.  If you need help with this and you will agree to use the updated spreadsheet in the future, depending on the number of requests we receive, we may be able to do this for you.  Email update@fjmc.org to make the request.</p>

<p>Click the Import Roster button and navigate to your spreadsheet.</p>
EOT;

  $form['myimport']['file'] = array(
    "#type" => 'file',
    "#description" => t('Upload a file, allowed extensions: .xls .xlsx csv'),
    "#upload_validators"  => array('file_validate_extensions' => array('xls xlsx csv')),
  );

  $cid = arg(1);
  $back_url = $base_url. "/club_details/club/". $cid;
  $form['myimport']['back'] = array(
    '#type' => 'button',
    '#value' => t('Cancel'),
    '#attributes' => array('onclick' => "window.location = '$back_url'; return false;"),
  );

  $form['myimport']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Import'),
  );

  // Set the Header
  $header = array(t('Display Name'), t('Result'));
  $rows = Null;

  // Set the Rows
  if(isset($form_state['storage']['import_data'])) {
    foreach ($form_state['storage']['import_data']['values'] as $import_data) {
      $rows[] = array($import_data['display_name'], $import_data['result']);
    }

    // Populate Table
    $form['myimport']['table'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No Records Found.')
    );
    $form['pager'] = array('#markup' => theme('pager'));
  }

  return $form;
}


/**
 * Implements hook_form_submit
 */
function club_data_import_form_submit($form, &$form_state) {
  $upload_dir    = file_directory_temp();
  $file_details  = file_save_upload('file', array(
                     'file_validate_extensions' => array('xls xlsx csv'),
                   ), FALSE, FILE_EXISTS_REPLACE);


  if(!empty($file_details)) {
    $upload_file_path = $upload_dir ."/". $file_details->filename;

    $error = '';
    try {
      $result = civicrm_api3('Roster', 'import', array(
        'sequential' => 1,
        'filename' => $upload_file_path,
        'email_notify' => 'nkatz@fjmc.org',
      ));
    } catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
    }
 
    if (empty($error)) {
      drupal_set_message("Club Data Imported Successfully", 'status');
      $form_state['rebuild'] = TRUE;
      $form_state['storage']['import_data'] =  $result;
    } else {
      drupal_set_message("Club Data Import Failed: $error", 'error');
    }

    file_delete($file_details);
  }
}

