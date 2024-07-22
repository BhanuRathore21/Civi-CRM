<?php
function club_details_club_officers_form($form, &$form_state) {

    // this form should always be on page /club_details/club/$club_id
    $club_id = arg(2);   
    $officers = club_details_get_club_officers($club_id);
    $form['officers'] = array(
        '#type' => 'fieldset', 
        '#title' => t('Club Officers'), 
        '#collapsible' => TRUE, 
        '#collapsed' => TRUE,
    );

    $form['officers']['help'] = array(
        '#type' => 'fieldset', 
        '#title' => t('[+] Help'), 
        '#collapsible' => TRUE, 
        '#collapsed' => TRUE,
    );

    $help = 'Each of your current officers is listed in one row.<br>
            <ul>
                <li>To add a more specific description such as "Activities VP", 
                    enter the description in the respective<br>"Additional Description" field for that officer and click "Update" on the right-most column.</li>
                <li>To remove a member from an office, click "Remove" on the right-most column.</li>
                <li>To update the officer\'s permissions on this page, under Authorization, select:<br>
                    "View" to allow the officer to view this club management page<br>
                    "Update" to allow the officer to edit the members and officers on this page<br>
                    "None" to prevent the officer from viewing this page
                </li>
            </ul>
    ';

    $form['officers']['help']['info'] = array(
        '#type' => 'item',
        '#markup' => $help,
    );


    $titles = array();
    $vps = isset($officers['Club Vice President']) ? count($officers['Club Vice President']) : 0;
    $max_vps = 6;

    $officersTable = '
        <div class="fjmc_officeContainer" style="width:100%;padding-bottom:12px;">
        <table>
            <thead>
                <tr>
                    <th>Office</td>
                    <th>Member</td>
                    <th>Additional Description</td>
                    <th>Authorization</td>
                    <th>Action</td>
                </tr>
            </thead>
            <tbody>
                #ROWS#
            </tbody>
        </table></div>';
    
    $officerRow = '
        <tr id="ofc_#oid#|#cid#">
            <td class="ofc_title">#officeName#</td>
            <td>#holder#</td>
            <td><input size="20" type="text" maxlength="40" id="desc-#oid#-#cid#" value="#desc#" /></td>
            <td><select style="display:inline;" id="sel-#oid#-#cid#">
                    #options#
                </select>
            </td>
            <td><a href="#" onclick="updateOfficer(\'#oid#\', #cid#, \'remove\');">Remove</a> | <a href="#" onclick="updateOfficer(\'#oid#\', #cid#, \'update\');">Update</a></td> 
        </tr>';


    ## show held offices
    $rows = '';
    $selArray = array('none' => 'None', 'read' => 'View', 'update' => 'Update');
    foreach ($officers as $office => $holders) {
        $title = str_replace(' ', '_', $office);
        $title = str_replace("'", "$", $title);
        foreach ($holders as $cid => $params) {
            $html = $officerRow;
            $html = str_replace('#club_id#', $club_id, $html);
            $html = str_replace('#oid#', $title, $html);
            $html = str_replace('#officeName#', $office, $html);
            $html = str_replace('#cid#', $cid, $html);
            $html = str_replace('#holder#', $params['name'], $html);
            $html = str_replace('#desc#', $params['desc'], $html);

            ## handle options for auth field
            $options = '';
            foreach ($selArray as $key => $val) {
                $option = '<option value="' . $key . '"';
                if ($key == $params['auth']) { 
                    $option .= ' selected="selected"';
                }
                $option .= ">$val</option>";
                $options .= $option;
            }
            $html = str_replace('#options#', $options, $html);

            $rows .= $html;
        }
    }
    $markup = str_replace('#ROWS#', $rows, $officersTable);
    $form['officers']['list'] = array(
        '#type' => 'item',
        '#markup' => $markup,
        '#suffix' => '<div style="clear:both;"></div>'
    );

    $members = club_details_get_club_members($club_id);
    $available_titles = club_details_get_club_titles();
    $titles = array();
    foreach ($available_titles as $title) {
        $new_title = str_replace(' ', '_', $title);
        $titles[$new_title] = $title;
    }

    $form['officers']['new_assignment'] = array(
        '#type' => 'fieldset', 
        '#title' => t('[+] Add Another Officer'), 
        '#collapsible' => TRUE, 
        '#collapsed' => TRUE,
    );

    $form['officers']['new_assignment']['office'] = array(
        '#title' => 'Office',
        '#type' => 'select',
        '#options' => $titles,
    );

    $form['officers']['new_assignment']['member'] = array(
        '#title' => 'Member',
        '#type' => 'select',
        '#options' => $members,
    );
    $form['officers']['new_assignment']['desc'] = array(
        '#title' => 'Additional Description',
        '#type' => 'textfield',
        '#attributes' => array('size' => '40', 'maxlength' => '40'),
    );
    $form['officers']['new_assignment']['auth'] = array(
        '#title' => 'Authorization',
        '#type' => 'select',
        '#options' => $selArray,
    );
    $form['officers']['new_assignment']['submit'] = array(
        '#type' => 'submit', 
        '#value' => t('Add Officer'),
        //## '#attributes' => array('disabled' => array('disabled'))
    );

    $form['club_id'] = array(
        '#type' => 'hidden',
        '#value' => $club_id,
    );
    
    return $form;
}
function club_details_club_officers_form_submit($form, &$form_state) {

    $club_id = $form_state['values']['club_id'];
    
    $member = $form_state['values']['member'];
    $office = $form_state['values']['office'];
    $desc = $form_state['values']['desc'];
    $auth = $form_state['values']['auth'];
    $now = date('Y-m-d');

    $sql = "INSERT INTO civicrm_relationship (contact_id_a, contact_id_b, relationship_type_id, start_date, is_active) values ($member, $club_id, 62, '$now', 1)";
	$dao = CRM_Core_DAO::executeQuery( $sql, array() );

    $sql = "SELECT max(id) as entity_id from civicrm_relationship where contact_id_a = $member and contact_id_b = $club_id and relationship_type_id = 62";
	$dao = CRM_Core_DAO::executeQuery( $sql, array() );
    $dao->fetch();
    $entity_id = $dao->entity_id;

    $office = str_replace("'", "\'", $office);
    $office = str_replace("_", " ", $office);
    $sql = "INSERT INTO civicrm_value_club_position_5 (entity_id, club_title_40, club_addtional_title_41, club_access_125) values ($entity_id, '$office', '$desc', '$auth')";
    $dao = CRM_Core_DAO::executeQuery( $sql, array() );
    $dao->free();

    drupal_set_message("Officer Added", 'status');
}

?>
