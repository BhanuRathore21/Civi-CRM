<?php

require_once 'packages/CsvReader.php';
require_once 'packages/XlsReader.php';

// Global variables.
$cid = $display_name = $email_id = '';

function _civicrm_api3_roster_import_spec(&$spec)
{
  $spec['filename']['api.required'] = 1;
}

function civicrm_api3_roster_import(&$api_params)
{

  if (!file_exists($api_params['filename'])) {
    throw new API_Exception('File does not exist on server');
  }

  if (substr($api_params['filename'], -4) == '.csv') {
    $reader = new CsvReader($api_params['filename']);
  } else {
    $reader = new XlsReader($api_params['filename'], 'Club Roster');
  }
  if (!$reader) {
    return civicrm_api3_create_error('Invalid import file, make sure the submitted file is a valid Excel spreadsheet.');
  }
  // echo "<pre>";
  //print_r($_SERVER['HTTP_REFERER']);
  // die('check clubid');
  // TODO: get club_id from API params
  //preg_match("/\d+/",$_SERVER['HTTP_REFERER'],$id);
  // Get club id from current URL.
  preg_match("/[?&]id=(\d+)/", $_SERVER['HTTP_REFERER'], $id);

  $club_id = $id[1];
  //$club_id = $id[0];
  // Today's Date
  $today_date = date('Y-m-d');

  global $cid, $display_name, $email_id;

  // TODO: have multiple headers per column
  // Mapping array
  $map = array(
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'street_address' => 'Street Address',
    'city' => 'City',
    'state_province' => 'State',
    'country' => 'Country',
    'postal_code' => 'Zip',
    'email' => 'Email',
    'landline' => 'Landline',
    'mobile' => 'Mobile',
    'phone_type_id' => 'Primary Phone',
    'prefix' => 'Prefix',
    'suffix' => 'Suffix',
    'is_active' => 'Active',
  );

  // Build Prefixes & Suffixes
  $prefixes = array_flip(CRM_Contact_BAO_Contact::buildOptions('prefix_id', 'get'));
  $suffixes = array_flip(CRM_Contact_BAO_Contact::buildOptions('suffix_id', 'get'));

  // Make column names case-insensitive
  $cols = $reader->getColumns();
  // watchdog('verbatim', print_r($cols, TRUE));
  foreach ($cols as $key => $val) {
    $cols[$key] = strtolower($val);
  }
  $reader->setColumns($cols);
  // watchdog('lowercase', print_r($cols, TRUE));

  // Check that we have the required columns
  // DO NOT change ANYTHING below as 'before' and 'after' will not be the same!
  //   This is a PHP bug, and $cols_copy is the only work-around.
  //   Trust me, any change below will trigger this bug. Anything.
  $cols_copy = array_flip(array_flip($cols));
  $columns = 0;
  foreach ($cols_copy as $col) {
    if (in_array($col, ['first name', 'last name', 'email'])) {
      $columns++;
    }
  }
  if ($columns < 3) {
    return civicrm_api3_create_error("One or more mandatory column(s) are missing.");
  }
  // watchdog('afterwards', print_r($cols, TRUE));

  // Write message for columns missing
/*
  $message = '';
  foreach ($map as $key => $col) {
    if (!in_array($col, $cols))
    $message .= "Column $col - Not Present<br />";
  }

  if (empty($message))
 $messages[] = ['display_name' => 'File Format', 'result' => 'All fields present'];
  else
 $messages[] = ['display_name' => 'File Format', 'result' => $message];
*/

  // Verify permissions
  $club_contact = civicrm_api3('Contact', 'getSingle', array('id' => $club_id));
  if (!empty($club_id)) {
    $club_ids[] = $club_id;
    CRM_Contact_BAO_Contact_Permission::relationshipList($club_ids, CRM_Core_Permission::EDIT);
    if (!in_array($club_id, $club_ids)) {
      return civicrm_api3_create_error("You do not have the required permission to import this club roster.");
    }
  }

  // Iterate over rows of excel sheet
  while ($data = $reader->getNextRow()) {
    $message['result'] .= _roster_add_msg('original', print_r($cols, TRUE));

    // Add values as per mapping, creating empty values if field not present
    $row = [];
    foreach ($map as $key => $col) {
      $col = strtolower($col);
      $row[$key] = !empty($data[$col]) ? $data[$col] : '';
    }
    // watchdog('import read', print_r($reader->getColumns(), TRUE));

    if (empty($row['first_name']) && empty($row['last_name']) && empty($row['email'])) {
      continue;
    }

    // Prepare display of results
    $display_name = $row['first_name'] . ' ' . $row['last_name'];
    $email_id = $row['email'];
    $message = array(
      'Contact' => $display_name,
      'result' => '',
    );

    // If the imported contact is missing first_name or last_name, then skip
    if (empty($row['first_name']) || empty($row['last_name'])) {
      $message['result'] .= _roster_add_msg('', 'Skip', '');
    }

    // Contact-matching algorithm
    $sql = "SELECT DISTINCT 
    ccb.id as contact_id, 
    ccb.first_name,
    ccb.last_name, 
    club.id as club_contact_id, 
    ccb.is_deceased, 
    ccb.display_name, 
    ccb.id, 
    eprim.email as email, 
    cpl.phone AS landline, 
    cpl.is_primary AS landline_is_primary, 
    cpm.phone AS mobile, 
    cr.is_active,
    ccb.sort_name  FROM civicrm_relationship cr
    LEFT JOIN civicrm_contact ccb ON ccb.id = cr.contact_id_a AND cr.end_date IS NULL
    LEFT JOIN civicrm_contact club ON club.id = cr.contact_id_b AND club.contact_type = 'Organization' AND club.contact_sub_type = 'Club'
    LEFT JOIN civicrm_address ca ON ca.contact_id = cr.contact_id_a AND ca.is_primary = 1
    LEFT JOIN civicrm_email eprim ON eprim.contact_id = cr.contact_id_a AND eprim.is_primary = 1 AND eprim.location_type_id = 3  
    LEFT JOIN (SELECT p.id, p.contact_id, p.phone, p.is_primary, p.phone_type_id FROM civicrm_phone p 
    JOIN (SELECT contact_id, min(id) as id FROM civicrm_phone WHERE phone_type_id = 1 AND location_type_id = 3  GROUP BY contact_id) pn ON p.id = pn.id) cpl ON cpl.contact_id = cr.contact_id_a AND cpl.phone_type_id = 1
    LEFT JOIN (SELECT p.id, p.contact_id, p.phone, p.phone_type_id FROM civicrm_phone p 
    JOIN (SELECT contact_id, max(id) as id FROM civicrm_phone WHERE phone_type_id = 2 AND location_type_id = 3  GROUP BY contact_id) pn ON p.id = pn.id) cpm ON cpm.contact_id = cr.contact_id_a AND cpm.phone_type_id = 2
    LEFT JOIN civicrm_state_province sp ON sp.id = ca.state_province_id
    LEFT JOIN civicrm_country cnty ON cnty.id = ca.country_id WHERE cr.contact_id_b = $club_id
    AND ccb.is_deleted <> 1
    AND cr.relationship_type_id = 10
    AND cr.end_date IS NULL
    AND IFNULL(ccb.is_deceased, 0) = 0
    ORDER BY ccb.sort_name;";

    $dao = CRM_Core_DAO::executeQuery($sql, array());

    // Iterate over the potential matches
    $fetched_id = NULL;
    while ($dao->fetch() && empty($fetched_id)) {
      if (!empty($dao->first_name) && !empty($dao->last_name) && !empty($dao->email)) {
        if ($dao->first_name == $row['first_name'] && $dao->last_name == $row['last_name'] && $dao->email == $row['email']) {
          //Contact is a club member, has same last_name, first_name and email address
          $fetched_id = $dao->contact_id;
        } elseif ($dao->email == $row['email']) {
          //Contact is a club member, has same email address (if not empty)
          $fetched_id = $dao->contact_id;
        } elseif ($dao->first_name == $row['first_name'] && $dao->last_name == $row['last_name']) { // Contact is a club member, has same last_name and first_name
          $fetched_id = $dao->contact_id;
        }
      } elseif (!empty($dao->first_name) && !empty($dao->last_name) && empty($dao->email)) {
        if ($dao->first_name == $row['first_name'] && $dao->last_name == $row['last_name']) { // Contact is a club member, has same last_name and first_name
          $fetched_id = $dao->contact_id;
        }
      }
    }

    if (!empty($fetched_id)) {
      // Look for existing contact ID
      $params = array(
        'id' => $fetched_id,
        'contact_type' => 'Individual',
      );
    } else {
      $params = array(
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'contact_type' => 'Individual',
      );
    }

    // Update Contact
    $update = array(
      'first_name' => $row['first_name'],
      'last_name' => $row['last_name'],
      'prefix_id' => CRM_Utils_Array::value($row['prefix'], $prefixes, ''),
      'suffix_id' => CRM_Utils_Array::value($row['suffix'], $suffixes, ''),
      'is_deceased' => ($row['is_active'] == 'Deceased' ? 1 : 0),
      // 'is_deleted' => 0,   DO NOT do this since CiviCRM returns 'contact_is_deleted' rather than 'is_deleted' ... ????
    );
    // Check values for drop-down fields
    if (!empty($row['prefix']) && empty($update['prefix_id']))
      $message['result'] .= _roster_add_msg('Contact', 'Value', 'Prefix');
    if (!empty($row['suffix']) && empty($update['suffix_id']))
      $message['result'] .= _roster_add_msg('Contact', 'Value', 'Suffix');

    // Create or Update Contact
    list($obj, $msg, $fld) = _roster_update('Contact', $params, $update);
    $cid = $obj['id'];
    if (empty($cid)) {
      $message[]['result'] = 'Unable to locate or create Contact';
      continue;
    } else {
      $message['result'] .= _roster_add_msg('Contact', $msg, (CRM_Utils_Array::value('is_deceased', $update) ? 'marked deceased' : ''));
    }


    if ($row['is_active'] != 'Deceased') {
      // Update Primary Email, forcing location_type
      $email_params = array(
        'contact_id' => $cid,
        'is_primary' => 1,
      );
      $update = array(
        'location_type_id' => 3,
        'email' => $row['email'],
      );
      if (empty($row['email']) || filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
        list($obj, $msg, $fld) = _roster_update('Email', $email_params, $update);
      } else {
        $msg = 'Invalid (' . $row['email'] . ')';
        $fld = '';
      }
      $message['result'] .= _roster_add_msg('Email', $msg, $fld);
    }

    // Deal with Deceased members
    if ($row['is_active'] == 'Deceased') {
      $current_date = date("Y-m-d");

      // Update Relationships end date if exists.
      $ids = [62 => 'Club Officer', 63 => 'Regional Officer', 61 => 'International Officer'];

      foreach ($ids as $rel_id => $rel_name) {
        $par = array(
          'contact_id_a' => $cid,
          'contact_id_b' => $club_id,
          'relationship_type_id' => $rel_id,
        );

        $update = array(
          'do_not_create' => TRUE,
          'end_date' => $current_date,
        );

        list($obj, $msg, $fld) = _roster_update('Relationship', $par, $update);
        if ($msg != 'Inexistent') {
          $message['result'] .= _roster_add_msg("$ids[$rel_id]", $msg, $fld);
        }
      }
    }

    // Set messages
    $message['display_name'] = $display_name;
    $message['Contact'] = "$msg ($cid)";

    // Get Address id's from pseudoconstants
    $raw = array(
      'state_province' => $row['state_province'],
      'country' => $row['country'],
      'skip_geocode' => TRUE,
    );
    CRM_Core_BAO_Address::fixAddress($raw);
    // For address, phone and email, force location_type to Main
    // Per Barry Balik's request, Jan 2022

    // Update Primary Address, forcing location_type
    $params = array(
      'contact_id' => $cid,
      'is_primary' => 1,
    );
    $update = array(
      'location_type_id' => 3,
      'street_address' => $row['street_address'],
      'city' => $row['city'],
      'postal_code' => $row['postal_code'],
      'state_province_id' => CRM_Utils_Array::value('state_province_id', $raw),
      'country_id' => CRM_Utils_Array::value('country_id', $raw),
    );
    /* print_r($update);
    die(); */
    list($obj, $msg, $fld) = _roster_update('Address', $params, $update);
    $message['result'] .= _roster_add_msg('Address', $msg, $fld);

    // Update Landline (only consider location_type = Main)
    $params = array(
      'contact_id' => $cid,
      'phone_type_id' => 1,    // Phone (ie. Landline)
      'location_type_id' => 3, // Main location type only
    );
    $update = array(
      'phone' => $row['landline'],
    );
    list($obj, $msg, $fld) = _roster_update('Phone', $params, $update);
    $message['result'] .= _roster_add_msg('Landline', $msg, $fld); // Aligns to Chaverot terminology 

    // Update Mobile Phone (only consider location_type = Main)
    $params = array(
      'contact_id' => $cid,
      'phone_type_id' => 2,    // Mobile
      'location_type_id' => 3, // Main location type only
    );
    $update = array(
      'phone' => $row['mobile'],
    );
    list($obj, $msg, $fld) = _roster_update('Phone', $params, $update);
    $message['result'] .= _roster_add_msg('Mobile', $msg, $fld); // Aligns to Chaverot terminology

    // Update Primary phone
    $params = array(
      'contact_id' => $cid,
      'phone_type_id' => 2,
      'location_type_id' => 3,
    );
    $update = array(
      'do_not_create' => TRUE,
      'is_primary' => 1,
    );
    list($obj, $msg, $fld) = _roster_update('Phone', $params, $update);
    $message['result'] .= _roster_add_msg('Primary Phone', $msg, $fld);

    // Update Relationship
    $params = array(
      'contact_id_a' => $cid,
      'contact_id_b' => $club_id,
      'relationship_type_id' => 10,
    );
    $update = array(
      'is_active' => (in_array($row['is_active'], ['No', 'Deceased']) ? 0 : 1),
    );
    list($obj, $msg, $fld) = _roster_update('Relationship', $params, $update);
    $message['result'] .= _roster_add_msg('Membership', $msg, $fld);

    // Cleanup returned array
    $message['result'] = trim($message['result']);
    $messages[] = $message;
  }

  // Send the audit email
  if ($api_params['email_notify']) {
    // Construct the email text
    $html = "<p>A club roster import was done in the Chaverot portal for
<a href=\"https://www.fjmc.org/civicrm/contact/view?reset=1&cid=$club_contact[id]\">$club_contact[display_name]</a>.<br>
The results are below:</p>
<table><tr><td>Display Name</td><td>Result</td></tr>";
    foreach ($messages as $line => $message) {
      $html .= "<tr><td valign='top'>$message[display_name]</td><td>$message[result]</td></tr>";
    }
    $html .= '</table>';

    foreach (explode(',', $api_params['email_notify']) as $recipient) {
      $mailParams = array(
        'from' => '"FJMC" <international@fjmc.org>',
        'toEmail' => $recipient,
        'subject' => 'Chaverot import results',
        'html' => $html,
        /* NG, #6791 API change leads to error, no need to attach file as was just uploaded by the same user
                'attachments' => array(
                  'fullPath' => $api_params['filename'],
                  'mime_type' => (substr($api_params['filename'], -3) == 'xls' ?
                    'application/vnd.ms-excel' :
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
                  'cleanName' => basename($api_params['filename']),
                ), */
      );
      CRM_Utils_Mail::send($mailParams);
    }
  }

  return civicrm_api3_create_success($messages, $api_params, 'Roster', 'import');
}

/*
 * Checks if an Entity exists or should be updated, and does what is necessary
 *
 * $params: query for locating the Entity
 * $update: fields that need to be checked for update
 */
function _roster_update($entity, $params, $update)
{

  // Pre-convert fields that CiviCRM will return differently than the saved value
  if (isset($update['email']))
    $update['email'] = strtolower($update['email']);

  // First try to locate an existing record matching the search criteria
  $current = array();
  $result = civicrm_api3($entity, 'get', $params);
  if ($result['is_error']) {
    return array([], 'Error', '');
  } else {
    if ($result['count'] == 0) {
      // Check if the no-create flag is set
      if (CRM_Utils_Array::value('do_not_create', $update)) {
        return array([], 'Inexistent', '');
      }
      // We will be creating a new entity, so fill out mandatory fields
      $current = $params;
      if (in_array($entity, array('Address', 'Email', 'Phone')) && !array_key_exists('location_type_id', $current)) {
        $current['location_type_id'] = 3; // Main
        // Sort order to have predictability for duplicates created by moving all to Main location type
        $current['options'] = ['sort' => "is_primary, id"];
      }
    } else {
      // Moving all phone and emails to the Main location type created many duplicates, so ignore
      // } elseif ($result['count'] == 1) {
      $current = reset($result['values']);
      $current = (array) $current; // convert to array
      // } else { // We MUST only have one result
      //  return array(array(), 'Duplicate');
    }
  }
  unset($update['do_not_create']);

  // If updating an EXISTING phone or email, and the updated value is EMPTY, then delete the record
  // Per Barry Balik's request dated Jan 2022
  if (
    in_array($entity, array('Phone', 'Email')) && empty($update['email']) && empty($update['phone'])
    && !array_key_exists('is_primary', $update)
  ) {    // This is NOT a primary phone/email update
    if (!empty($current['id'])) {
      $result = civicrm_api3($entity, 'delete', ['id' => $current['id']]);
      return array($current, 'Deleted', '');
    } else {
      return array(array(), 'Empty', '');
    }
  }

  // If updating a Primary Phone flag, check that the phone number exists
  if (in_array($entity, array('Phone')) && array_key_exists('is_primary', $update)) {
    if (empty($current['id'])) {
      return array(array(), 'Inexistent', ''); // This will be ignored in the output
    }
  }

  // Then see if we need to change any information in his record
  $changes = 0;
  $details = '';
  foreach ($update as $key => $value) {
    if (($value !== '') && ($value !== NULL) && ($value !== 'null')) {
      // We have a valid target value, now let's test the source
      if ((!array_key_exists($key, $current)) || (trim($current[$key]) != trim($value))) {
        if (empty($current[$key]))
          $details .= "$key: '$value', ";
        else
          $details .= "$key: '$current[$key]' to '$value', ";
        $current[$key] = $value;
        $changes++;
      }
    }
  }
  $details = substr($details, 0, -2);

  // And finally perform any changes that are needed (or create the record if non-existing)
  if ($changes) {
    // do not create an address with only the country
    if ($entity == 'Address') {
      $extra_fields = array_diff(array_keys($current), array('contact_id', 'location_type_id', 'country_id'));
      if (empty($extra_fields)) {
        $obj = array();
        $msg = 'Empty';
        return array($obj, $msg, '');
      }
    }
    // try to create/update the Entity
    try {
      $result = civicrm_api3($entity, 'create', $current);
    } catch (Exception $e) {
      $obj = array('error_message' => $e->getMessage());
      CRM_Core_Error::debug_var('Error:', $current);
      CRM_Core_Error::debug_var('Exception:', $e->getMessage());
      $msg = 'Error';
      return array($obj, $msg, '');
    }
    if ($result['is_error']) {
      $obj = $current;
      $msg = 'Error';
      CRM_Core_Error::debug_var('Error:', $current);
      CRM_Core_Error::debug_var('Result:', $result);
    } else {
      $obj = reset($result['values']);
      $msg = (empty($current['id']) ? 'Inserted' : 'Updated');
      return array($obj, $msg, $details);
    }
  } elseif (!empty($current['id'])) {
    $obj = $current;
    $msg = 'Unchanged';
  } else {
    $obj = array();
    $msg = 'Empty';
  }

  return array($obj, $msg, '');
}

function _roster_add_msg($entity, $msg, $fld = '')
{
  global $cid, $display_name, $email_id;

  switch ($msg) {
    case 'Inserted':
      $msg = 'Created' . ($fld ? " ($fld)" : '');
      return "$entity - $msg<br>";
    case 'Updated':
      $msg = 'Updated' . ($fld ? " ($fld)" : '');
      return "$entity - $msg<br>";
    case 'Deleted':
      return "$entity - $msg<br>";
    case 'Value':
      return "$entity - Invalid value for field $fld, ignored.<br>";
    case 'Unchanged':
    case 'Empty':
      return "$entity - Unchanged<br>";
    case 'Abort':
      return "Mandatory column missing, import aborted.<br>";
    case 'Skip':
      return "Contact - Missing first or last name, skipped<br>";
    case 'Duplicate':
      return "$entity - Duplicate records exist, skipped.<br>";
    case 'Error':
      return "$entity - Error reading or saving record, skipped.<br>";
    default:
      return "$entity - $msg<br>";
  }
}
