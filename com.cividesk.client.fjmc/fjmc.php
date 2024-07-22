<?php

require_once 'fjmc.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function fjmc_civicrm_config(&$config) {
  _fjmc_civix_civicrm_config($config);

  global $civicrm_settings;
  $civicrm_setting['eu.tttp.publicautocomplete']['params'] = array(
    'contact_type' => 'Organization',
    'return' => 'display_name, city',
  );
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function fjmc_civicrm_xmlMenu(&$files) {
  _fjmc_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function fjmc_civicrm_install() {
  _fjmc_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function fjmc_civicrm_uninstall() {
  _fjmc_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function fjmc_civicrm_enable() {
  _fjmc_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function fjmc_civicrm_disable() {
  _fjmc_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function fjmc_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _fjmc_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function fjmc_civicrm_managed(&$entities) {
  _fjmc_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function fjmc_civicrm_caseTypes(&$caseTypes) {
  _fjmc_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function fjmc_civicrm_angularModules(&$angularModules) {
_fjmc_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function fjmc_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _fjmc_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
 */
function fjmc_civicrm_preProcess($formName, &$form) {
    if ($formName == "CRM_Contribute_Form_Contribution_Main" && $form->_id == 44) {
        if (!is_user_logged_in()) {
            $login_url = wp_login_url($_SERVER['REQUEST_URI']);
            wp_redirect($login_url);
            exit;
        }
    }
}

/**
 * Implements hook_civicrm_alterContent().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterContent
 *
 */
function fjmc_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  // Replace contact tokens inside some contribution pages (#7463)
  if ($context == "form" && $tplName == "CRM/Contribute/Form/Contribution/Main.tpl") {
    if ($object->_id == 44) {
      $session = CRM_Core_Session::singleton();
      if ($user_id = $session->get('userID')) {
        // Get current contact attributes
        $session = CRM_Core_Session::singleton();
        $values = array(
          'contact_id' => $user_id,
          'version' => 3,
        );
        require_once 'api/api.php';
        $contact = civicrm_api('contact', 'get', $values);
        $contact = reset($contact['values']);

        // Replace contact tokens in form content
        $content = CRM_Utils_Token::replaceContactTokens($content, $contact, TRUE,
            // we need to explicitely list all tokens (or regexp ourselves to extract)
            ['contact' => ['first_name', 'last_name', 'display_name']]
        );
      }
    }
  }
}

/**
 * Implementation of hook_civicrm_postProcess()
 *
 */
function fjmc_civicrm_postProcess($formName, &$form) {
  //https://projects.cividesk.com/projects/12/tasks/4224
  if ($formName == 'CRM_Contribute_Form_Contribution_Confirm') {
    // Send an email when the custom In Honor of / In Memory of block is filled out
    // The custom block is defined with contribution-level custom fields
    //contribution page id {45 : FJMC Donations}
    if ($form->getVar('_id') == 45) {
      $params = $form->_params;
      $values = $form->_values;
      $notify = $params['custom_236'];
      //{custom_236 : Email or Paper Acknowledgement}
      //check for notification method as Email
      //In Honor/In Memory of has a value {custom_239 : In Honor/Memory Of}
      //and recepient has valid email {custom_229 : Email}
      if ($notify == 'Email' && $params['custom_239'] && $params['custom_229']) {
        $domainValues = CRM_Core_BAO_Domain::getNameAndEmail();
        $receiptFrom = "$domainValues[0] <$domainValues[1]>";

        //template in use for Honor email : 418
        //osticket 8523 - send cc to the donor's email
        $sendTemplateParams = array(
          //'tplParams' => $tplParams,
          'messageTemplateID' => 418,
          'from' => $receiptFrom,//$values['receipt_from_name'],
          'toEmail' => $params['custom_229'],
          'toName' => $params['custom_237'] . ' ' . $params['custom_238'],
          'cc' => $params['email'],
        );

        $honorTemplateParams = $sendTemplateParams;
        $session =& CRM_Core_Session::singleton();
        $session->set('honorParams', $params);
        $con_id = $form->_contributionID;
        $session->set('contrib_id', $con_id);

        //send email to honoree
        CRM_Core_BAO_MessageTemplate::sendTemplate($honorTemplateParams);
      }
    }
  }
}


/**
 * Implementation of hook_civicrm_buildForm
 */
function fjmc_civicrm_buildForm($formName, &$form) {
  CRM_Core_Region::instance('page-body')->add(array(
    'script' => "
      //osticket 11456 - hide error message
      cj('.error').hide()
    ",
  ));

  if ($formName == 'CRM_Event_Form_Registration_Register' && in_array($form->getVar('_eventId'), [96])) {
    //osticket 10930 - profiles for Organization Settings and Ritual Fields
    //render above above the payment section
    //event '2023 FJMC International Convention' (event id = 96)
    CRM_Core_Region::instance('page-body')->add(array(
      'script' => "
        cj().ready(function () {
          fjmc_toggle_text_charges();
	});
        cj('#pricevalue').on('change', function() {
          fjmc_toggle_text_charges();
	});

        cj('[name=payment_processor_id]').change(function(){
          fjmc_toggle_text_charges();
        });
        cj('.transaction_processing_fee-section').hide();
        cj('#pricesetTotal').insertAfter('.payment_processor-section');

        cj('#billing-payment-block').insertAfter('.custom_post-section');
        cj('.custom_post-section').insertAfter('.custom_pre-section');

        function calculateTotalFee() {
          var totalFee = 0;
          cj('#priceset [price]').each(function () {
            totalFee = totalFee + cj(this).data('line_raw_total');
          });
          return totalFee;
        }

	// Function to show/hide the checkbox depending on value of select list.
	function fjmc_toggle_text_charges() {
          if (cj('[name=payment_processor_id]:checked').val() == 1 && parseFloat(cj('#pricevalue').text().replace('$ ', '').replace(',', '')) > 500) {

	    cj(\"label[for*='CIVICRM_QFID_1_payment_processor_id']\").html('Credit Card <b>($25 fee)</b>');
	    cj('#CIVICRM_QFID_668_48').prop('checked', true);
	  }
	  else {

            cj(\"label[for*='CIVICRM_QFID_1_payment_processor_id']\").html('Credit Card');
            cj('#CIVICRM_QFID_669_50').prop('checked', true);
            cj('#CIVICRM_QFID_668_48').prop('checked', false);

            //don't calculate everytime as this causes recursion
           // var totalfee = display(calculateTotalFee() -25);
           // totalfee = Math.round(totalfee*100)/100;

          }
        }
      ",
    ));
  }
}

/**
 * Implementation of hook_civicrm_tokens
 */
function fjmc_civicrm_tokens(&$tokens) {
  $tokens['date'] = array(
    'date.date_short' => 'Today\'s Date: mm/dd/yyyy',
    'date.date_med'   => 'Today\'s Date: Mon d yyyy',
    'date.date_long'  => 'Today\'s Date: Month dth, yyyy',
  );

  $tokens['financialFJMC'] = array(
    'financialFJMC.club_summary'        => 'financialFJMC: club all open pledges',
    'financialFJMC.club_total_balance'  => 'financialFJMC: club total open balance',
    'financialFJMC.club_name'           => 'financialFJMC: club name',
    'financialFJMC.MemberBaseline'      => 'financialFJMC: baseline members',
    'financialFJMC.MemberBilled'        => 'financialFJMC: billed members',
    'financialFJMC.MemberCount'         => 'financialFJMC: records members',
    'financialFJMC.InternationalRate'   => 'financialFJMC: international rate',
    'financialFJMC.RegionalRate'        => 'financialFJMC: regional rate',
    'financialFJMC.LastRoster'          => 'financialFJMC: last roster date',
  );

  $tokens['regionalReport'] = array(
    'regionalReport.region_name'           => 'Region Name',
    'regionalReport.region_report_summary' => 'Region Summary',
    'regionalReport.region_report_link'    => 'Region Document Link',
  );
}


function getTokens_for_contact($contact_type, $contact_id) {
  if ('Individual' == $contact_type) {
    /*
     * tbl cc - refering the Individual
     * tbl cc_cd - refering Organization(Club)
     */
    $sql = "select club_id_7, cc_cd.id as ccid , cc_cd.addressee_display as club_name, club_title_40 as officer_position,
            cc.last_name, cc.addressee_display as officer_name , cc.id as contact_id_ccb , p.id, p.contact_id ,
            date(p.start_date) as start_date, p.amount, (p.amount- ifnull(sum(pp.actual_amount),0)) as pledge_balance,
            ct.name as 'contrib_type_name',club_members_2013_92, date_format(cd.laster_roster_recieved_93,'%m-%d-%Y') as roster,
            number_of_club_members_104, current_billing_international_105, current_billing_region_106,
            past_due_international_107, past_due_regional_108,number_of_members_billed_for_116,regional_dues_rate_99,dues_rate_98
            FROM civicrm_contact cc
            left join civicrm_relationship cr on cr.contact_id_a = cc.id
            left join civicrm_contact cc_cd on cc_cd.id = cr.contact_id_b
            left join civicrm_value_extra_contact_info_1 ce  on ce.entity_id = cr.contact_id_b
            left join civicrm_value_club_details_19 cd on cd.entity_id = cr.contact_id_b
            left join civicrm_value_club_position_5 rcv on rcv.entity_id = cr.id
            left join civicrm_pledge p on cc_cd.id=p.contact_id and (p.status_id <> 1 AND p.status_id <> 3)
            left join civicrm_pledge_payment pp ON p.id = pp.pledge_id AND pp.status_id = 1
            left join civicrm_financial_type ct ON p.financial_type_id = ct.id
            Where cr.relationship_type_id=62 and cc.id = '$contact_id' and cr.end_date is NULL
            and cr.is_active=1 and club_title_40 IN ('Club President','Club Treasurer','Club Co-President')
            and fjmcaffil_19=1 group by p.id order by p.start_date desc, ct.name, p.amount desc, club_id_7, last_name";
  } else {
    /*
     * tbl cc - refering the Organization(club)
     * tbl cc_cd - refering the Individual
     */
    $sql = "select club_id_7, cc.id as ccid , cc.addressee_display as club_name, club_title_40 as officer_position,
            cc_cd.last_name, cc_cd.addressee_display as officer_name , cc.id as contact_id_ccb , p.id,
            p.contact_id , date(p.start_date) as start_date, p.amount, (p.amount- ifnull(sum(pp.actual_amount),0)) as pledge_balance,
            ct.name as 'contrib_type_name',club_members_2013_92, date_format(cd.laster_roster_recieved_93,'%m-%d-%Y') as roster,
            number_of_club_members_104, current_billing_international_105, current_billing_region_106, past_due_international_107,
            past_due_regional_108,number_of_members_billed_for_116,regional_dues_rate_99,dues_rate_98
            FROM civicrm_contact cc
            left join civicrm_relationship cr on cr.contact_id_b = cc.id
            left join civicrm_contact cc_cd on cc_cd.id = cr.contact_id_a
            left join civicrm_value_extra_contact_info_1 ce on ce.entity_id = cc.id
            left join civicrm_value_club_details_19 cd on cd.entity_id = cc.id
            left join civicrm_value_club_position_5 rcv on rcv.entity_id = cr.id
            left join civicrm_pledge p on cc.id = p.contact_id and (p.status_id <> 1 AND p.status_id <> 3)
            left join civicrm_pledge_payment pp ON p.id = pp.pledge_id AND pp.status_id = 1
            left join civicrm_financial_type ct ON p.financial_type_id = ct.id
            Where cc.contact_sub_type='club'
            AND cr.relationship_type_id = 62
            and cc.id = '$contact_id'
            and cr.end_date is NULL
            and cr.is_active = 1
            and club_title_40 IN ('Club President','Club Treasurer','Club Co-President')
            and fjmcaffil_19 = 1
            group by p.id
            order by p.start_date desc, ct.name, p.amount desc, club_id_7, last_name";
  }
  $pdo = &CRM_Core_DAO::executeQuery($sql);
  return $pdo;
}

/**
 * Implementation of hook_civicrm_alterMailParams()
 *
 */
function fjmc_civicrm_alterMailParams(&$params, $context = NULL) {
  //template in use for Honor email : 418
  if (CRM_Utils_Array::value('messageTemplateID', $params) == 418) {
    $session = CRM_Core_Session::singleton();
    $honorParams = $session->get('honorParams');
    $params['tplParams'] = $honorParams ;
  }
}

function fjmc_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  if (sizeof($cids) == 0) {
    return;
  }

  // Date tokens
  if (!empty($tokens['date'])) {
    $date = array(
      'date.date_short' => date('m/d/Y'),
      'date.date_med'   => date('M j Y'),
      'date.date_long'  => date('F jS, Y'),
    );
    foreach ($cids as $cid) {
      $values[$cid] = empty($values[$cid]) ? $date : $values[$cid] + $date;
    }
  }

	// Statement-related tokens.
  if (!empty($tokens['financialFJMC'])) {
    $token_name            = 'financialFJMC.club_summary';
    $bal_token_name        = 'financialFJMC.club_total_balance';
    $club_token_name       = 'financialFJMC.club_name';
    $baseline_token_name   = 'financialFJMC.MemberBaseline';
    $membill_token_name    = 'financialFJMC.MemberBilled';
    $memcount_token_name   = 'financialFJMC.MemberCount' ;
    $intrate_token_name    = 'financialFJMC.InternationalRate';
    $regrate_token_name    = 'financialFJMC.RegionalRate' ;
    $lastroster_token_name = 'financialFJMC.LastRoster' ;

    $prev_cid = "";
    $table_header = "<table width='100%'><tr><th style='text-align: left;'>Date</th><th>Description</th><th style='text-align: right;'>Orig. Amount</th><th style='text-align: right;'>Balance</th></tr>";
    $table_footer = "</table>";
    $row_count = 0;

    $bal_output  = array();
    $html_output = array();
    $club_output = array();

    foreach($cids as $contact_id) {
      $query = 'select contact_type from civicrm_contact where id = "'.$contact_id.'" ';
      $contact_type = &CRM_Core_DAO::singleValueQuery($query);
      $dao = getTokens_for_contact($contact_type, $contact_id);

      while ($dao->fetch()) {
        $cid                      = $dao->contact_id_ccb;
        $pledge_start_date        = $dao->start_date;
        $pledge_amount            = $dao->amount;
        $pledge_contrib_type_name = $dao->contrib_type_name;
        $pledge_balance           = (is_numeric($dao->pledge_balance) && $dao->pledge_balance > 0) ? $dao->pledge_balance : 0;
        $club_id_7                = $dao->club_id_7;
        $club_name                = $dao->club_name;
        $baseline                 = $dao->club_members_2013_92;
        $membersbilled            = $dao->number_of_members_billed_for_116;
        $membercount              = $dao->number_of_club_members_104;
        $intrate                  = $dao->dues_rate_98;
        $regrate                  = $dao->regional_dues_rate_99;
        $ccid                     = $dao->ccid;
        $lastroster               = $dao->roster;

        if ($cid <> $prev_cid  && in_array($cid, $cids )) {
          // wrap up previous contact, and start new contact.
          if ($prev_cid <> "") {
            $html_output[$prev_cid][$token_name]  = $html_output[$prev_cid][$token_name]."</table>";
          }
          $tmp_output     = array($token_name  => $table_header);
          $tmp_bal_output = array($bal_token_name => 0);

          $html_output[$cid] =  $tmp_output;
          $bal_output[$cid]  = $tmp_bal_output;
        }

        if (in_array($cid, $cids)) {
          //if ($pledge_balance <> 0 && $pledge_balance !== null) {
          $row = "<tr><td style='text-align: ;'>".$pledge_start_date."</td><td style='text-align: left; width: 45%;'>".
          $pledge_contrib_type_name."</td><td style='text-align: right;'>".$pledge_amount."</td><td style='text-align: right;'>".$pledge_balance."</td></tr><tr><td>";

          $html_output[$cid][$token_name] = $html_output[$cid][$token_name].$row;
          //$bal_output[$cid][$bal_token_name] = number_format(($bal_output[$cid][$bal_token_name] + $pledge_balance), 2);
          $bal_output[$cid][$bal_token_name] += floatval($pledge_balance);
          //}
        }
        $club_output[$cid][$club_token_name]       = $club_name;
        $club_output[$cid][$baseline_token_name]   = $baseline;
        $club_output[$cid][$memcount_token_name]   = $membercount;
        $club_output[$cid][$membill_token_name]    = $membersbilled;
        $club_output[$cid][$intrate_token_name]    = $intrate;
        $club_output[$cid][$regrate_token_name]    = $regrate;
        $club_output[$cid][$lastroster_token_name] = $lastroster;
        $prev_cid = $cid;
        $row_count++;
      }
      $dao->free();
    }

    if ($row_count > 0) {
      $html_output[$cid][$token_name] = $html_output[$cid][$token_name]."</table>";
    }

    foreach ($cids as $cid) {
     /*
      * 1. checking all pledges are paid for this contact
      * 2. checking have any pledges for this contact
      * 3. 1&2 is failed, so the contact have some pending dues, then we are displaying.
      */
      if ('Individual' == $contact_type) {
        $sql = "select count(p.id) as num_of_pledges from civicrm_pledge p
           left join civicrm_pledge_payment pp ON p.id = pp.pledge_id AND pp.status_id = 1
           left join civicrm_financial_type ct ON p.financial_type_id = ct.id
           where p.contact_id IN (select contact_id_b from civicrm_relationship where contact_id_a = '$cid' and relationship_type_id = 62 and end_date is NULL and is_active=1)";
      } else {
        $sql = "select count(p.id) as num_of_pledges from civicrm_pledge p
             left join civicrm_pledge_payment pp ON p.id = pp.pledge_id AND pp.status_id = 1
             left join civicrm_financial_type ct ON p.financial_type_id = ct.id
             where p.contact_id = '$cid'";
      }
      $num_of_pledges = &CRM_Core_DAO::singleValueQuery($sql);

      /*************** setting default values for tokens ***************/
      $club_output[$cid][$baseline_token_name] = ((isset($club_output[$cid][$baseline_token_name]) && $club_output[$cid][$baseline_token_name] > 0) ? $club_output[$cid][$baseline_token_name] : '0');
      $club_output[$cid][$memcount_token_name] = ((isset($club_output[$cid][$memcount_token_name]) && $club_output[$cid][$memcount_token_name] > 0) ? $club_output[$cid][$memcount_token_name] : 0);
      $club_output[$cid][$membill_token_name]  = ((isset($club_output[$cid][$membill_token_name]) && $club_output[$cid][$membill_token_name] >0) ? number_format($club_output[$cid][$membill_token_name], 2) : '0.00');
      $club_output[$cid][$intrate_token_name]  = ((isset($club_output[$cid][$intrate_token_name]) && $club_output[$cid][$intrate_token_name] >0) ? number_format($club_output[$cid][$intrate_token_name], 2) : '0.00');
      $club_output[$cid][$regrate_token_name]  = ((isset($club_output[$cid][$regrate_token_name]) && $club_output[$cid][$regrate_token_name] > 0) ? number_format($club_output[$cid][$regrate_token_name], 2) : '0.00');
      /*************** setting default values for tokens ***************/

      $bal_output[$cid][$bal_token_name] = number_format($bal_output[$cid][$bal_token_name], 2);
      $club_output[$cid] = isset($club_output[$cid]) ? $club_output[$cid] : array();
      $html_output[$cid] = isset($html_output[$cid]) ? $html_output[$cid] : array();
      $bal_output[$cid]  = isset($bal_output[$cid])  ? $bal_output[$cid]  : array();

      if (isset($bal_output[$cid][$bal_token_name]) && $bal_output[$cid][$bal_token_name] == 0 && $num_of_pledges) {
        // show balance token values even it is zero, otherwise it will show blank
        $values[$cid] = $values[$cid] + array( $token_name => "All Membership Dues Paid.") + $bal_output[$cid] + $club_output[$cid];
      } elseif (!$num_of_pledges) { //we are displaying same info message for both the cases as per client requirement.
        // show balance token values even it is zero, otherwise it will show blank
        $values[$cid] = $values[$cid] + array( $token_name => "All Membership Dues Paid.") + $bal_output[$cid] + $club_output[$cid];
      } else {
        $values[$cid] =  $values[$cid] + $html_output[$cid] + $bal_output[$cid] + $club_output[$cid];
      }
    }
  }

  if (!empty($tokens['regionalReport'])) {   //check the message template has the regional report tokens.
    $_REQUEST['regionalReport'] = true;  //for pdf library we are setting flag here.
    $j = 1;
    //tokens
    $regreport_name     = 'regionalReport.region_name';
    $regreport_summary  = 'regionalReport.region_report_summary';
    $regreport_document = 'regionalReport.region_report_link';

    foreach($cids as $contact_id) {
      if ($j == 20) {
        sleep(10);
        $j = 1;
      } else {
        $j = $j + 1;
      }

      /********* fetch the contact type *********/
      try{
        $result = civicrm_api3('contact', 'get', array( 'id' => $contact_id, 'return.display_name' => 1, 'return.contact_type' => 1, 'return.contact_sub_type' => 1 ));
      }
      catch (CiviCRM_API3_Exception $e) {
        // handle error here
        #return array('error' => $e->getMessage(), 'error_code' => $e->getErrorCode(), 'error_data' => $e->getExtraParams());
        continue;
      }
      /********* fetch the contact type *********/

      if ($result['count'] > 0) {
        $tmpcid           = $result['id'];
        $contact_type     = $result['values'][$tmpcid]['contact_type'];
        $contact_sub_type = is_array($result['values'][$tmpcid]['contact_sub_type']) ? reset($result['values'][$tmpcid]['contact_sub_type']) : $result['values'][$tmpcid]['contact_sub_type'];
        $display_name     = $result['values'][$tmpcid]['display_name'];
        /*
         * 1) as per client requirement on july 23, 2015, this tokens should not work other than regions
         * 2) adding this if condition to skip for other contacts. 
         * 3) in future by commending this if condition, we can enable this token for other contacts as well.
         */
        if ($contact_type != 'Organization' ||  $contact_sub_type != 'Region') continue;
          /********* if it is individual, fetch region details from individual *********/
          if ($contact_type == 'Individual') {
            $query = "select region.display_name, region.id as region_id from civicrm_contact as region "
                    . "inner join civicrm_relationship as rel ON region.id = rel.contact_id_b "
                    . "inner join civicrm_value_regional_position_2 auth ON auth.entity_id = rel.id "
                    . "inner join civicrm_contact as contact ON contact.id = rel.contact_id_a "
                    . "where contact.id = {$contact_id} AND rel.relationship_type_id = 63 AND region_access_123 IN ('read', 'update')";
            $contactObj = &CRM_Core_DAO::executeQuery($query);

            while($contactObj->fetch()) {
              $display_name = $contactObj->display_name;
              $region_id    = $contactObj->region_id;
            }
            $contactObj->free();
          } else if ($contact_type == 'Organization' && $contact_sub_type == 'Region') {
            $region_id = $contact_id;
          }

          if ($region_id) {
            $values[$contact_id][$regreport_name]     = $display_name;
            $values[$contact_id][$regreport_summary]  = _generate_regional_summary_for_email_html($region_id);
            $values[$contact_id][$regreport_document] = $_SERVER['HTTP_HOST'].'/regional/reportonfly/'.$region_id;
          } else {
            $values[$contact_id][$regreport_name]     = '';
            $values[$contact_id][$regreport_summary]  = '';
            $values[$contact_id][$regreport_document] = '';
          }
        }
      }
  }
}

/**
 * Implementation of hook_civicrm_alterReportVar
 */
function fjmc_civicrm_alterReportVar($varType, &$var, &$object) {
  $instanceValue = $object->getVar('_instanceValues');
  if ($instanceValue['id'] == 152) {
    if ($varType == 'columnHeaders') {
      $new['civicrm_value_org_selection_64_custom_292'] = $var['civicrm_value_org_selection_64_custom_292'];
      $new['contact_custom_294_civireport'] = $var['contact_custom_294_civireport'];
      foreach ($var as $key => $val) {
        $new[$key] = $val;
      }
      $var = $new;
    }
  }
}

/*
 * This hook is used for summary.tpl file only for relationship subtab count.
 * sites/default/local/templates/CRM/Contact/Page/View/Summary.tpl
 */
function fjmc_civicrm_tabs(&$tabs, $contactID) {
  $url = null;
  foreach($tabs as $key=>$tab_details) {
    if ('rel' == $tab_details['id']) {
      $url = $tab_details['url'];
      break;
    }
  }

  if (null != $url) {
    //Club Member
    $cm_count = CRM_Contact_BAO_Relationship::getRelationship($contactID, CRM_Contact_BAO_Relationship::CURRENT, 0, 1, 0, NULL, NULL, FALSE, array('relationship_type_id' => 10));
    $tab = array(
      'id'     => 'club-member',
      'title'  => t('Club Member'),
      'count'  => $cm_count,
      'weight' => 54,
      'url'    => "$url&tid=10",
    );
    $tabs[] = $tab;

    //Club Officer
    $co_count = CRM_Contact_BAO_Relationship::getRelationship($contactID, CRM_Contact_BAO_Relationship::CURRENT, 0, 1, 0, NULL, NULL, FALSE, array('relationship_type_id' => 62));
    $tab = array(
      'id'     => 'club-officer',
      'title'  => t('Club Officer'),
      'count'  => $co_count,
      'weight' => 55,
      'url'    => "$url&tid=62",
    );
    $tabs[] = $tab;

    //Others
    $others_count = CRM_Contact_BAO_Relationship::getRelationship($contactID, CRM_Contact_BAO_Relationship::CURRENT, 0, 1, 0, NULL, NULL, FALSE, array('relationship_type_id' => array('NOT IN' => array(10, 62))));
    $tab = array(
      'id'     => 'others',
      'title'  => t('Others'),
      'count'  => $others_count,
      'weight' => 56,
      'url'    => "$url&tid=notin_10_62",
    );
    $tabs[] = $tab;
  }
}

/**
 * This hook retrieves links from other modules and injects it into
 * CiviCRM forms
 *
 * @param string $op         the type of operation being performed
 * @param string $objectName the name of the object
 * @param int    $objectId   the unique identifier for the object
 * @param array  $links (reference) an optional links array (used for action links)
 *
 * @return array|null        an array of arrays, each element is a tuple consisting of url, title (and other parameters depending on $op)
 *
 * @access public
 */
function fjmc_civicrm_links( $op, $objectName, $objectId, &$links ) {
  switch ($objectName) {
    case 'Contact':
      switch ($op) {
        case 'view.contact.activity':
          // Adds a link to the main tab.
          $links[] = array(
            'id'     => 'create-order',
            'name'   => 'Create Order',
            'url'    => $_SERVER['HTTP_HOST'].'/club_details/create/order/'.CRM_Core_BAO_UFMatch::getUFId($objectId).'/'.$objectId,
            'weight' => 10,
            'class' => 'no-popup', // this is unique identifier for this link
          );
        break;
      }
  }
}

function _getMenuKeyMax($menuArray) {
  $max = array(max(array_keys($menuArray)));
  foreach($menuArray as $v) {
    if (!empty($v['child'])) {
      $max[] = _getMenuKeyMax($v['child']); 
    }
  }
  return max($max);
}

function fjmc_civicrm_navigationMenu( &$params ) {
  //  Get the maximum key of $params
  $maxKey = _getMenuKeyMax($params);
  $params[$maxKey+1] = array (
    'attributes' => array (
      'label'       => ts('Financial Report'),
      'name'        => 'Financial Report',
      'url'         => 'civicrm/report/contribute/financialdetail',
      'permission'  => 'access CiviReport,administer CiviCRM',
      'operator'    => 'OR',
      'separator'   => 1,
      'active'      => 1
    )
  );
}
