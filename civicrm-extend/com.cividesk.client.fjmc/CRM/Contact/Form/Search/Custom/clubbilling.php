<?php
/*   
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */
class CRM_Contact_Form_Search_Custom_clubbilling extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {

  public $_formValues; 
  function __construct(&$formValues) {
    $this->_formValues = $formValues;

    /**
     * Define the columns for search result rows
     */
    $this->_columns = array(
      ts('Contact Id') => 'contact_id',
      ts('Club Name') => 'club_name',
      ts('Officer Name') => 'officer_name',
      ts('Officer Position') => 'officer_position',
    );
  }

  function buildForm(&$form) {

    $this->setTitle(ts('Select Officers and Regions'));
    $office = "SELECT id, value FROM `civicrm_option_value` where `option_group_id`=84 order by weight";
    $officea = CRM_Core_DAO::executeQuery($office,array());
    while ($officea->fetch()) {
        $customData[addslashes($officea->value)] = $officea->value;
    }
    $inOfficers = &$form->addElement('advmultiselect', 'includeGroup1',
      ts('Include<br>Positions') . ' ', $customData,
      array(
        'size' => 5,
        'style' => 'width:240px; height: 73px;',
        'class' => 'advmultiselect'
      ) 
    );
 
    $regions = "select cc.id,display_name,sort_name from civicrm_contact cc, civicrm_value_region_detail_25 cr 
                where contact_sub_type = 'Region' 
                and cc.id=cr.entity_id  
                and cr.active_region_126=1 
                order by sort_name";

    $results = CRM_Core_DAO::executeQuery($regions,array());
  
    while ($results->fetch()) {
        $customDatar[$results->id] = $results->display_name;
    }
        $customDatar['ALL'] = "ALL";  
   /*   $rega=  &$form->addElement('radio','regiona','Select Region','Hudson Valley','1'); */
    $inRegions = &$form->addElement('select', 'includeGroup2',
      ts('Include<br>Regions') . ' ', $customDatar,
      array(
        'size' => 5,
        'style' => 'width:240px; height: 73px;',
        'class' => 'advmultiselect',
      )
    );
    
    
    //No Balance
    $balance_settings = array();
    $balance_settings['0'] = 'Owes No balance';
    $balance_settings['1'] = 'Owes balance';
    $balance_settings['all'] = 'All';
    $noBalance = &$form->addElement('select', 'includeGroup3',
      ts('No Balance') . ' ', $balance_settings,
      array(
        'size' => 5,
        'style' => 'width:240px; height: 73px;',
      ) 
    );
    
    //Email
    $email_settings = array();
    $email_settings['is not null'] = 'Select only email';
    $email_settings['is null'] = 'Select only no email';
    $email_settings['all'] = 'All';
    $email = &$form->addElement('select', 'includeGroup4',
      ts('Email') . ' ', $email_settings,
      array(
        'size' => 5,
        'style' => 'width:240px; height: 73px;',
      ) 
    );
    
    //Minimum Due
    $minimum_due = array();
    $minimum_due['<='] = 'Select Minimum';
    $minimum_due['>'] = 'Select greater than minimum';
    $minimum_due['all'] = 'All';
    $min_due = &$form->addElement('select', 'includeGroup5',
      ts('Minimum dues') . ' ', $minimum_due,
      array(
        'size' => 5,
        'style' => 'width:240px; height: 73px;',
      ) 
    );
    
    /* $regionlist =&$form->addElement('radio','region','Region',$customDatar,"x","XX",
     array(
        'size' => 5,
        'style' => 'width:240px',
        'class' => 'radio',
     ));
     print_r ($regionlist);  */
    //add/remove buttons for groups
    $inOfficers->setButtonAttributes('add', array('value' => ts('Add >>')));
    //add/remove buttons for groups
  /*  $inRegions->setButtonAttributes('add', array('value' => ts('Add >>'))); 8?
     /*  print_r ($inRegions);  */
    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $form->assign('elements', array('includeGroup1', 'includeGroup2', 'includeGroup3', 'includeGroup4', 'includeGroup5', 'regiona'));
  }

  /**
   * Define the smarty template used to layout the search form and results listings.
   */
  function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * Construct the search query
   */
  function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $onlyIDs = FALSE) { 

    $list = null;
    if (isset($this->_formValues['includeGroup1']) && !empty($this->_formValues['includeGroup1'])){
        $list = "'" . implode("','" , array_values($this->_formValues['includeGroup1'])) . "'"; 
    }
    
    $regionnum = null;
    if (isset($this->_formValues['includeGroup2']) && !empty($this->_formValues['includeGroup2'])){
        $regionnum = $this->_formValues['includeGroup2'];
    }
   
    // SELECT clause must include contact_id as an alias for civicrm_contact.id
    $select = " contact_a.id, club_id_7, cc.id as clubid , cc.addressee_display as club_name, club_title_40 as officer_position, 
                contact_a.last_name, contact_a.addressee_display as officer_name , contact_a.id as contact_id ";
    
    $from =" civicrm_contact cc,civicrm_relationship cr,civicrm_contact contact_a,
            civicrm_value_extra_contact_info_1 ce, civicrm_value_club_position_5 rcv ";
   
    $where = " cc.contact_sub_type='club' and cc.id=ce.entity_id and fjmcaffil_19=1 
            and cr.id=rcv.entity_id 
            and cr.contact_id_b=cc.id and cr.relationship_type_id=62 
            and cr.end_date is NULL and cr.is_active=1 and
            cr.contact_id_a=contact_a.id ";

    if ($list != '')   {
        $where .= " AND club_title_40 IN (".$list.") ";
    }

    if ('' != $regionnum && 'ALL' != $regionnum)   {

        $from .=", civicrm_contact creg, civicrm_relationship cregr";

        $where .= " and cc.id= cregr.contact_id_a and cregr.relationship_type_id=11 and  cregr.contact_id_b=creg.id and creg.id=$regionnum ";
    }

    $tblflag = false;
    $nobalance = (isset($this->_formValues['includeGroup3']) ? $this->_formValues['includeGroup3'] : null);
    if ('0' == $nobalance || '1' == $nobalance ){
        if(1 == $nobalance) {
            $where .= " and cc.id IN (SELECT cd19.entity_id FROM civicrm_value_club_details_19 cd19 WHERE cd19.entity_id = cc.id and IF(cd19.total_balance_114 is not null, cd19.total_balance_114, 0) > 0) ";
        } elseif(0 == $nobalance){
            $where .= " and cc.id IN (SELECT cd19.entity_id FROM civicrm_value_club_details_19 cd19 WHERE cd19.entity_id = cc.id and IF(cd19.total_balance_114 is not null, cd19.total_balance_114, 0) <= 0) ";
        }
    }

    $minimumdue = (isset($this->_formValues['includeGroup5']) ? $this->_formValues['includeGroup5'] : null);
    if ('>' == $minimumdue || '<=' == $minimumdue ){
        $where .= " and cc.id IN (SELECT cd19.entity_id FROM civicrm_value_club_details_19 cd19 WHERE cd19.entity_id=cc.id and IF(cd19.current_billing_international_105 is not null, cd19.current_billing_international_105, 0) $minimumdue 275) ";
    }

    $email = (isset($this->_formValues['includeGroup4']) ? $this->_formValues['includeGroup4'] : null);
    if ('' != $email && 'all' != $email ){   
        if('is not null' == $email) {
            $where .= " and contact_a.id IN (select civicrm_email.contact_id from civicrm_email where contact_a.id = civicrm_email.contact_id and civicrm_email.email is not null and on_hold = 0 ) ";
        } else {
            $where .= " and contact_a.id NOT IN (SELECT contact_id FROM civicrm_email where on_hold = 0 and contact_id is not null) ";
        }
    }

    //$Orderby = " club_id_7, contact_a.last_name ";
   
    if($onlyIDs) {
        $select = 'contact_a.id as contact_id';
    }
   
    $sql = "
        SELECT $select
        FROM   $from
        WHERE  $where
    "; 
 
     // Define ORDER BY for query in $sort, with default value
    if ( ! empty( $sort ) ) {
      if ( is_string( $sort ) ) {
        // First prefix sort fields with the correct table
        $elements = explode(',', $sort);
        foreach ($elements as &$element) {
          $element = trim($element);
          if ($element == 'contact_id') {
            $element = 'contact_a.id';
          } else if ($element == 'club_name') {
            $element = 'cc.addressee_display';
          } else if ($element == 'officer_name') {
            $element = 'contact_a.addressee_display';
          } else if ($element == 'officer_position') {
            $element = 'club_title_40';
          }
        }
        $sort = implode(', ', $elements);
        $sql .= " ORDER BY '1', $sort ";
      } else {
        $sort->_vars['1']['name'] = 'contact_a.id';
        $sort->_vars['2']['name'] = 'cc.addressee_display';
        $sort->_vars['3']['name'] = 'contact_a.addressee_display';
        $sort->_vars['4']['name'] = 'club_title_40';
        $sql .= " ORDER BY club_id_7, " . trim( $sort->orderBy() );
      }
    } else {
      $sql .= " ORDER BY contact_a.id ASC";
    }

    if ($rowcount > 0 && $offset >= 0) {
      $sql .= " LIMIT $offset, $rowcount ";
    }
    return $sql;
  }

  function from() {
    return "";
  }


  function where($includeContactIDs = FALSE) {
    return '';
  }


  function having($includeContactIDs = FALSE) {
    return '';
  }

  /* 
     * Functions below generally don't need to be modified
     */
  function count() {
    $sql = $this->all();
    $dao = CRM_Core_DAO::executeQuery($sql,
      CRM_Core_DAO::$_nullArray
    );
    return $dao->N;
  }

  function contactIDs($offset = 0, $rowcount = 0, $sort = NULL, $returnSQL = FALSE) {
    return $this->all($offset, $rowcount, $sort, FALSE, TRUE);
  }

  function &columns() {
    return $this->_columns;
  }

  function setTitle($title) {
    if ($title) {
      CRM_Utils_System::setTitle($title);
    }
    else {
      CRM_Utils_System::setTitle(ts('Search'));
    }
  }

  function summary() {
    return NULL;
  }
}
