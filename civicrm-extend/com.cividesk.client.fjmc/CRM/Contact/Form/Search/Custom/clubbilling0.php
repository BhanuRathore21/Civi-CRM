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
class CRM_Contact_Form_Search_Custom_clubbilling implements CRM_Contact_Form_Search_Interface {

  protected $_formValues; 
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

    /**
     * You can define a custom title for the search form
     */
    $this->setTitle('Find clubs to bill');

    /**
     * Define the search form fields here
     */
    $form->add('text',
      'Select club',
     ts('Aggregate xx Total Between $')
    );
    
    /**
     * If you are using the sample template, this array tells the template fields to render
     * for the search form.
     */
    $form->assign('elements', array('min_amount', 'max_amount', 'start_date', 'end_date'));
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
  function all($offset = 0, $rowcount = 0, $sort = NULL,
    $includeContactIDs = FALSE, $onlyIDs = FALSE
  ) {

    // SELECT clause must include contact_id as an alias for civicrm_contact.id
   
      $select = "club_id_7, cc.id , cc.addressee_display as club_name, club_title_40 as officer_position, ccb.last_name, ccb.addressee_display as officer_name , ccb.id as contact_id ";
    
    $from ="civicrm_contact cc,civicrm_relationship cr,civicrm_contact ccb,
`civicrm_value_extra_contact_info_1` ce
,civicrm_value_club_position_5 rcv";

    $where = 'cc.contact_sub_type="club" and cc.id=ce.entity_id and fjmcaffil_19=1 
and cr.id=rcv.entity_id and club_title_40 IN ("Club President","Club Treasurer","Club Co-President")  
and cr.contact_id_b=cc.id and cr.relationship_type_id=62 and  cr.end_date is NULL and cr.is_active=1 and
cr.contact_id_a=ccb.id ';

    $Orderby = "club_id_7,last_name";
   

    $sql = "
SELECT $select
FROM   $from
WHERE  $where
ORDER BY $Orderby 

";
    

    if ($rowcount > 0 && $offset >= 0) {
      $sql .= " LIMIT $offset, $rowcount ";
    }
    return $sql;
  }

  function from() {
    return ""
;
  }

  /*
      * WHERE clause is an array built from any required JOINS plus conditional filters based on search criteria field values
      *
      */
  function where($includeContactIDs = FALSE) {
    $clauses = array();

    $clauses[] = "contrib.contact_id = contact_a.id";
    $clauses[] = "contrib.is_test = 0";

    $startDate = CRM_Utils_Date::processDate($this->_formValues['start_date']);
    if ($startDate) {
      $clauses[] = "contrib.receive_date >= $startDate";
    }

    $endDate = CRM_Utils_Date::processDate($this->_formValues['end_date']);
    if ($endDate) {
      $clauses[] = "contrib.receive_date <= $endDate";
    }

    if ($includeContactIDs) {
      $contactIDs = array();
      foreach ($this->_formValues as $id => $value) {
        if ($value &&
          substr($id, 0, CRM_Core_Form::CB_PREFIX_LEN) == CRM_Core_Form::CB_PREFIX
        ) {
          $contactIDs[] = substr($id, CRM_Core_Form::CB_PREFIX_LEN);
        }
      }

      if (!empty($contactIDs)) {
        $contactIDs = implode(', ', $contactIDs);
        $clauses[] = "contact_a.id IN ( $contactIDs )";
      }
    }

    return implode(' AND ', $clauses);
  }

  function having($includeContactIDs = FALSE) {
    $clauses = array();
    $min = CRM_Utils_Array::value('min_amount', $this->_formValues);
    if ($min) {
      $min = CRM_Utils_Rule::cleanMoney($min);
      $clauses[] = "sum(contrib.total_amount) >= $min";
    }

    $max = CRM_Utils_Array::value('max_amount', $this->_formValues);
    if ($max) {
      $max = CRM_Utils_Rule::cleanMoney($max);
      $clauses[] = "sum(contrib.total_amount) <= $max";
    }

    return implode(' AND ', $clauses);
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

  function contactIDs($offset = 0, $rowcount = 0, $sort = NULL) {
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