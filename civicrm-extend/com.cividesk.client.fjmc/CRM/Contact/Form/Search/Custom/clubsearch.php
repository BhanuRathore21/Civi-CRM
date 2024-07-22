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
class CRM_Contact_Form_Search_Custom_clubsearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {

  public $_formValues; 
  function __construct(&$formValues) {
    $this->_formValues = $formValues;

    /**
     * Define the columns for search result rows
     */ 
    $this->_columns = array(
      ts('Contact Id') => 'contact_id',
      ts('Club Name') => 'club_name',
      ts('Member Name') => 'officer_name',
      
    );
/**
 *  print_r( $this);
 */
 
  }  

  function buildForm(&$form) {

    $this->setTitle(ts('Select Regions Members'));
    
 
    $regions = "select contact_a.id,display_name,sort_name from civicrm_contact contact_a, civicrm_value_region_detail_25 cr 
                where contact_sub_type = 'Region' 
                and contact_a.id=cr.entity_id  
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
        'style' => 'width:240px; height: 75px;',
        'class' => 'advmultiselect',
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
   /**
 *  $inOfficers->setButtonAttributes('add', array('value' => ts('Add >>')));
 */
    //add/remove buttons for groups
  /*  $inRegions->setButtonAttributes('add', array('value' => ts('Add >>'))); 8?
     /*  print_r ($inRegions);  */
    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $form->assign('elements', array('includeGroup2'));
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
    
        $regionnum = $this->_formValues['includeGroup2'];
      
    // SELECT clause must include contact_id as an alias for civicrm_contact.id
    if($onlyIDs) {
        $select = 'contact_a.id as contact_id';
    } else {
        $select = ' club_id_7, ccb.id , contact_a.last_name as ln, ccb.addressee_display as club_name, contact_a.addressee_display as officer_name , contact_a.id as contact_id ';
    }
    
    $from =' civicrm_contact ccb,civicrm_relationship cr,civicrm_contact contact_a,
             civicrm_value_extra_contact_info_1 ce, civicrm_contact creg, civicrm_relationship cregr ';
   
    $where = ' ccb.contact_sub_type like "%club%" and ccb.id=ce.entity_id and fjmcaffil_19 = 1 
               and cr.contact_id_b=ccb.id and cr.relationship_type_id=10  and cr.end_date is NULL 
               and cr.is_active=1 and cr.contact_id_a=contact_a.id and ccb.id= cregr.contact_id_a 
               and cregr.relationship_type_id=11 and  cregr.contact_id_b=creg.id ';
        
    if ($regionnum != '' && $regionnum <> 'ALL' ) {
        $where = $where.'and creg.id = '.$regionnum;
  }
    
    #$Orderby = " club_id_7, ln ";
    //$Orderby = " club_id_7 ";

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
            $element = 'ccb.addressee_display';
          } else if ($element == 'officer_name') {
            $element = 'contact_a.addressee_display';
          }
        }
        $sort = implode(', ', $elements);
        $sql .= " ORDER BY '1', $sort ";
      } else {
        $sort->_vars['1']['name'] = 'contact_a.id';
        $sort->_vars['2']['name'] = 'ccb.addressee_display';
        $sort->_vars['3']['name'] = 'contact_a.addressee_display';
        $sql .= " ORDER BY '2', " . trim( $sort->orderBy() );
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
    $dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray );
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
