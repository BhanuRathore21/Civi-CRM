<?php
## insure that civi is initialized on ajax calls
if (!defined('CIVICRM_SETTINGS_PATH')) civicrm_initialize();

function club_details_get_regional_officers($region_id) {
    $sql = "SELECT DISTINCT m.id as member_id, concat(m.first_name, ' ', m.last_name) as name, regional_title_31 as office, regional_additional_title_28 as description, region_access_123 as auth
        FROM `civicrm_contact` m
        inner join civicrm_relationship relm
        on m.id = relm.contact_id_a AND relm.relationship_type_id = 63 AND relm.contact_id_b = $region_id AND relm.end_date is null and relm.is_active = 1
        inner join civicrm_value_regional_position_2 rpos
        on relm.id = rpos.entity_id
        left join civicrm_option_value o on rpos.regional_title_31 = o.value and o.option_group_id = 81
        WHERE m.is_deleted <> 1 AND m.is_deceased <> 1
        ORDER BY o.weight";
    $dao = CRM_Core_DAO::executeQuery( $sql, array() );

    $results = array();

    while ($dao->N && $dao->fetch()) {
        $results[$dao->office][$dao->member_id] = array('name'=>$dao->name, 'desc'=>$dao->description, 'auth'=>$dao->auth);
    }
    $dao->free();
    return $results;
}

function club_details_get_regional_titles() {
    $sql = "SELECT value from civicrm_option_value where option_group_id = 81 order by weight";
    $dao = CRM_Core_DAO::executeQuery( $sql, array() );
    $results = array();

    while ($dao->N && $dao->fetch()) {
        $results[] = $dao->value;
    }
    $dao->free();
    return $results;
}

function club_details_regional_officers_form($form, &$form_state) {
    // this form should always be on page /club_details/region/$region_id
    $region_id = arg(2);
    if (empty($region_id)) {
        ## on ajax calls
        $region_id =  $form_state['values']['region_id'];
    }

    $officers = club_details_get_regional_officers($region_id);
    $clubs = club_details_get_clubs_for_region($region_id);

    ## insure that this include file gets loaded before processing ajax callbacks
    form_load_include($form_state, 'inc', 'club_details', 'regional_officers');

    $form['officers'] = array(
        '#type' => 'fieldset',
        '#title' => t('Regional Officers'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );

    $form['officers']['help'] = array(
        '#type' => 'fieldset',
        '#title' => t('[+] Help'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );

    $help = 'Each of the current regional officers is listed in one row.<br>
            <ul>
                <li>To add a more specific description such as "Activities VP",
                    enter the description in the respective<br>"Additional Description" field for that officer and click "Update" on the right-most column.</li>
                <li>To remove a member from an office, click "Remove" on the right-most column.</li>
                <li>To update the officer\'s permissions on this page, under Authorization, select:<br>
                    "View" to allow the officer to view this region management page<br>
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
    $vps = isset($officers['Regional Vice President']) ? count($officers['Regional Vice President']) : 0;
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
            <td><a href="#" onclick="updateRegionalOfficer(\'#oid#\', #cid#, \'remove\');">Remove</a> | <a href="#" onclick="updateRegionalOfficer(\'#oid#\', #cid#, \'update\');">Update</a></td>
        </tr>';


    ## show held offices
    $rows = '';
    $selArray = array('none' => 'None', 'read' => 'View', 'update' => 'Update');
    foreach ($officers as $office => $holders) {
        $title = str_replace(' ', '_', $office);
        $title = str_replace("'", "$", $title);
        foreach ($holders as $cid => $params) {
            $html = $officerRow;
            $html = str_replace('#region_id#', $region_id, $html);
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

    $available_titles = club_details_get_regional_titles();
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

    $form['officers']['new_assignment']['clubs'] = array (
        '#type' => 'select',
        '#title' => t('Club where officer is a member'),
        '#options' => $clubs,
        '#ajax' => array
        (
            'event' => 'change',
            'wrapper' => 'mdiv_wrapper',
            'callback' => 'club_details_club_sel_callback',
        )
    );

    if (isset($form_state['values']['clubs'])) {
        $members = club_details_get_club_members($form_state['values']['clubs']);
    } else {
        $members = array();

        if (count($clubs) > 0) {
            $keys = array_keys($clubs);
            $first_club = $keys[0];
            $members = club_details_get_club_members($first_club);
        }                    
    }

    $form['officers']['new_assignment']['member'] = array(
        '#prefix' => '<div id="mdiv_wrapper">',
        '#suffix' => '</div>',
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

    $form['region_id'] = array(
        '#type' => 'hidden',
        '#value' => $region_id,
    );

    return $form;
}

function club_details_club_sel_callback($form, &$form_state) {
    watchdog('fjmc', 'in callback', array(), WATCHDOG_NOTICE);

    return render($form['officers']['new_assignment']['member']);

}

function club_details_regional_officers_form_submit($form, &$form_state) {

    $region_id = $form_state['values']['region_id'];

    $member = $form_state['values']['member'];
    $office = $form_state['values']['office'];
    $desc = $form_state['values']['desc'];
    $auth = $form_state['values']['auth'];
    $now = date('Y-m-d');

    $sql = "INSERT INTO civicrm_relationship (contact_id_a, contact_id_b, relationship_type_id, start_date, is_active) values ($member, $region_id, 63, '$now', 1)";
	$dao = CRM_Core_DAO::executeQuery( $sql, array() );

    $sql = "SELECT max(id) as entity_id from civicrm_relationship where contact_id_a = $member and contact_id_b = $region_id and relationship_type_id = 63";
	$dao = CRM_Core_DAO::executeQuery( $sql, array() );
    $dao->fetch();
    $entity_id = $dao->entity_id;

    $office = str_replace("'", "\'", $office);
    $office = str_replace("_", " ", $office);
    $sql = "INSERT INTO civicrm_value_regional_position_2 (entity_id, regional_title_31, regional_additional_title_28, region_access_123) values ($entity_id, '$office', '$desc', '$auth')";
    $dao = CRM_Core_DAO::executeQuery( $sql, array() );
    $dao->free();

    drupal_set_message("Regional Officer Added", 'status');
}

?>
