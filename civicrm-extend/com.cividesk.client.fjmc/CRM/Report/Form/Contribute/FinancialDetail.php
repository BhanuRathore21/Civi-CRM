<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
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
 * @copyright CiviCRM LLC (c) 2004-2014
 * $Id$
 *
 */
class CRM_Report_Form_Contribute_FinancialDetail extends CRM_Report_Form {
  protected $_addressField = FALSE;

  protected $_emailField = FALSE;

  protected $_summary = NULL;
  protected $_allBatches = NULL;

  protected $_softFrom = NULL;

  protected $_customGroupExtends = array(); #array( 'Contribution', 'Event', 'Contact', 'Individual', 'Household', 'Organization');

  //custom variables
  protected $_financialType = array();
  protected $_eventsList = array();  
  private $regdate_from = NULL;
  private $regdate_to = NULL;
  private $receipt_from = NULL;
  private $receipt_to = NULL;
  private $filter = NULL;
  private $receipt_relative = NULL;
  private $regdate_relative = NULL;
  private $index = NULL;

  /**
   *
   */
  /**
   *
   */
  function __construct() {

    // Check if CiviCampaign is a) enabled and b) has active campaigns
    $config = CRM_Core_Config::singleton();
    $this->_events = CRM_Event_PseudoConstant::event();
    $this->_financialType = CRM_Contribute_PseudoConstant::financialType();
    $this->_contributions = CRM_Contribute_PseudoConstant::contributionPage();

    $this->_columns = array(
      
      //Contact
      'civicrm_contact' =>
        array(
            'dao' => 'CRM_Contact_DAO_Contact',
            'fields' =>
            array(
                'sort_name' =>
                array(
                    'title' => ts('Name'),
                    'required' => TRUE,
                ),
                'first_name' => array('title' => ts('First Name'),),
                'last_name' => array('title' => ts('Last Name'),),
                'id' =>
                array(
                    'no_display' => TRUE,
                    'required' => TRUE,
                ),
                /*'created_date' => array(
                    'title' => 'Registration Date',
                    'type' => CRM_Utils_Type::T_DATE,
                    'no_display' => TRUE,
                ),*/
                'custom_value_paystatus' => array(
                    'title' => 'Payment', 
                    'type' => CRM_Utils_Type::T_INT,
                    'no_display' => TRUE,
                    'pseudofield' => TRUE,
                ),
                'custom_value_paymethod' => array(
                    'title' => 'Payment Mode', 
                    'type' => CRM_Utils_Type::T_INT,
                    'no_display' => TRUE,
                    'pseudofield' => TRUE,
                ),
            ),
            /*'filters' => array(
              'created_date' => array(
                    'operatorType' => CRM_Report_Form::OP_DATE,
                    'title' => 'Date',
                    'type' => CRM_Utils_Type::T_DATE,
                    'no_display' => TRUE,
                ),
            ),*/
            'order_bys' => array(
                'sort_name' => array(
                    'title' => ts('Name'),
                    'required' => TRUE,
                    ),
                /*'created_date' => array(
                    'title' => ts('Registration Date'),
                    'required' => TRUE,
                    'default' => false,
                    ),*/
            ),
            'grouping' => 'contact-fields',
        ),

      'civicrm_participant' =>
        array(
            'fields' => array(
                'register_date' => array(
                    'title' => 'Event Registration Date',
                    'operatorType' => CRM_Report_Form::OP_DATE,
                    'type' => CRM_Utils_Type::T_DATE,
                    'no_display' => true,
                    ),
                ),
            'filters' =>
            array(
                'custom_filter_by' =>
                array(
                    'pseudofield' => TRUE,
                    'title' => ts('Select By'),
                    'operatorType' => CRM_Report_Form::OP_SELECT,
                    'options' => array('' => 'Please Select Date', 'reg_date' => 'Transaction Date', 'pay_date' => 'Payment Date'),
                    'type' => CRM_Utils_Type::T_STRING,
                ),
                'register_date' =>
                array(
                    'operatorType' => CRM_Report_Form::OP_DATE,
                    'title' => 'Date',
                    'type' => CRM_Utils_Type::T_DATE,
                ),
                'custom_filter_by_type' =>
                array(
                    'pseudofield' => TRUE,
                    'title' => ts('Filter By'),
                    'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                    'options' => array('event' => 'Events', 'contribution' => 'Donation', 'pledge' => 'Pledges'),
                    'type' => CRM_Utils_Type::T_STRING,
                ),
            ),
            'grouping' => 'contact-fields',
        ),
      
      'civicrm_event' =>
        array(
            'dao' => 'CRM_Event_DAO_Event',
            'fields' => array(
                'event_id' => array(
                    'name' => 'id',
                    'no_display' => TRUE,
                ),
            ),
            'filters' => array(
                'event_id' =>
                array(
                    'name' => 'id',
                    'title' => ts('Events List'),
                    'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                    'options' => $this->_events,
                    'type' => CRM_Utils_Type::T_INT,
                    'no_display' => true,
                ),
            ),
        ),
        
      //Contact Contribution
      'civicrm_contribution' =>
        array(
            'dao' => 'CRM_Contribute_DAO_Contribution',
            'fields' =>
                array(
                    'contribution_id' => array(
                        'name' => 'id',
                        'title' => 'Con., ID',
                        'operatorType' => CRM_Report_Form::OP_INT,
                        'type' => CRM_Utils_Type::T_INT,
                        'required' => TRUE,
                        //'no_display' => TRUE, //while hideing need to remove css from tpl
                        //'required' => FALSE,
                    ),
                    'receive_date' => array(
                        'title' => 'Transaction Date',
                        'operatorType' => CRM_Report_Form::OP_DATE,
                        'type' => CRM_Utils_Type::T_DATE,
                        'no_display' => true,
                    ),
                    'receipt_date' => array(
                        'title' => 'Payment Date',
                        'operatorType' => CRM_Report_Form::OP_DATE,
                        'type' => CRM_Utils_Type::T_DATE,
                        'no_display' => true,
                    ),
                    'total_amount' => array(
                        'title' => 'Amount',
                        'type' => CRM_Utils_Type::T_MONEY,
                        'no_display' => TRUE,
                        'required' => TRUE,
                    ),
                    'financial_type_id' => array(
                        'title' => 'Activity',
                        'type' => CRM_Utils_Type::T_STRING,
                        'no_display' => TRUE,
                        'required' => TRUE,
                    ),
                    'contribution_status_id' => array(
                        'title' => 'Pay Status',
                        'type' => CRM_Utils_Type::T_STRING,
                        'no_display' => TRUE,
                        'required' => TRUE,
                    ),
                    'payment_instrument_id' => array(
                        'title' => 'Payment Mode',
                        'type' => CRM_Utils_Type::T_STRING,
                        'no_display' => TRUE,
                        'required' => TRUE,
                    ),
                    'source' => array(
                        'title' => 'Source',
                        'type' => CRM_Utils_Type::T_STRING,
                        'required' => TRUE,
                    )
                ),
            'filters' =>
                array(
                    'receive_date' => array(
                    'no_display' => true,
                    ),
                    'receipt_date' => array(
                    'no_display' => true,
                    ),
                    'donation_list' => array(
                        'name' => 'contribution_page_id',
                        'title' => ts('Donation List'),
                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                        'options' => $this->_contributions,
                        'type' => CRM_Utils_Type::T_INT,
                        'no_display' => true,
                    ),
                ),
            'order_bys' => 
                array(
                    'receive_date' => array(
                    'title' => ts('Transaction Date'),
                    'type' => CRM_Utils_Type::T_DATE,
                    'required' => TRUE,
                    ),
                    'receipt_date' => array(
                    'title' => ts('Payment Date'),
                    'type' => CRM_Utils_Type::T_DATE,
                    'required' => TRUE,
                    ),
                    'total_amount' => array(
                        'title' => ts('Amount'),
                        'type' => CRM_Utils_Type::T_MONEY,
                        'required' => TRUE,
                    ),
                    'contribution_status_id' => array(
                      'title' => ts('Pay Status'),
                      'type' => CRM_Utils_Type::T_INT,
                      'default' => false,
                    ),
                    'payment_instrument_id' => array(
                        'title' => ts('Payment Mode'),
                        'type' => CRM_Utils_Type::T_STRING,
                        'required' => TRUE,
                    )
                ),
        ), 
        
        'civicrm_pledge' => array(
            'dao' => 'CRM_Pledge_DAO_Pledge',
            'fields' =>
                array(
                    'pledge_id' => array(
                        'name' => 'id',
                        'no_display' => TRUE,
                        'required' => FALSE,
                    ),
                ),
            'filters' => array(
                'financial_type_id' => array(
                    'title' => ts('Financial Type'),
                    'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                    'options' => $this->_financialType,
                    'type' => CRM_Utils_Type::T_INT,
                    'no_display' => true,
                ),
            ),
        ),
        
        'civicrm_financial_type' => array(
            'dao' => 'CRM_Financial_DAO_FinancialType',
            'fields' =>
                array(
                    'financial_id' => array(
                        'name' => 'id',
                        'no_display' => TRUE,
                    ),
                    'name' => array(
                        'no_display' => TRUE,
                        'required' => TRUE,
                    )
                ),
            'order_bys' => 
                array(
                    'name' => array(
                    'title' => ts('Financial Type'),
                    'type' => CRM_Utils_Type::T_STRING,
                    'required' => TRUE,
                    ),
                ),
        ),
        
        
        /*'civicrm_financial_item' => array(
            'dao' => 'CRM_Financial_DAO_FinancialItem',
            'fields' =>
                array(
                    'financial_item' => array(
                        'name' => 'id',
                        'no_display' => TRUE,
                    ),
                    'transaction_date' => array(
                        'title' => 'Transaction Date',
                        'operatorType' => CRM_Report_Form::OP_DATE,
                        'type' => CRM_Utils_Type::T_DATE,
                        'no_display' => true,
                    ),
                    'created_date' => array(
                        'title' => 'Created Date',
                        'operatorType' => CRM_Report_Form::OP_DATE,
                        'type' => CRM_Utils_Type::T_DATE,
                        'no_display' => true,
                    )
                ),
            'filters' => array(
              'created_date' => array(
                    'operatorType' => CRM_Report_Form::OP_DATE,
                    'title' => 'Date',
                    'type' => CRM_Utils_Type::T_DATE,
                    'no_display' => TRUE,
                ),
            ),
            'order_bys' => array(
                'created_date' => array(
                    'title' => ts('Created Date'),
                    'required' => TRUE,
                    'default' => false,
                    )
            )
        ),*/
        
    );// + $this->addAddressFields(FALSE);
    
    $this->_aliases = array(
      'civicrm_contact' => 'contact',
      'civicrm_participant' => 'participant',
      'civicrm_event' => 'event',
      'civicrm_participant_payment' => 'particptpay',
      'civicrm_pledge' => 'pledge',
      'civicrm_contribution' => 'contribution',
      'civicrm_pledge' => 'pledge',
      'civicrm_pledge_payment' => 'pledgepayment',
      'civicrm_financial_type' => 'financialtype',
      'civicrm_financial_item' => 'financialItem',
      'civicrm_line_item' => 'lineItem',
    );
    
    $this->_groupFilter = FALSE;
    $this->_tagFilter = FALSE;
  
    $this->_currencyColumn = 'civicrm_contribution_currency';
    parent::__construct();
  }

  function preProcess() {
    parent::preProcess();
    
  }

  function select() {
    $this->_columnHeaders = array();
    parent::select();
    #$this->_columnHeaders = array_unique($this->_columnHeaders);
  }

  function orderBy() {
    parent::orderBy();
    // please note this will just add the order-by columns to select query, and not display in column-headers.
    // This is a solution to not throw fatal errors when there is a column in order-by, not present in select/display columns.
    foreach ($this->_orderByFields as $orderBy) {
        if ( ($this->filterBy == 'event' && $orderBy['tplField'] =='civicrm_contribution_receive_date')) {
            //this if condition only for making the users event registration date 
            //as created date
        } elseif(!array_key_exists($orderBy['tplField'], $this->_columnHeaders) && !array_key_exists($orderBy['name'], $this->_params['fields']) && empty($orderBy['section'])) {
            $this->_select .= ", {$orderBy['dbAlias']} as {$orderBy['tplField']}";
        }
      }
  }

  /**
   * @param bool $softcredit
   */
  function from() {
    $function = 'filter'.ucfirst($this->filterBy);
    $this->$function();
  }

  function groupBy() {
    $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_contact']}.id, {$this->_aliases['civicrm_contribution']}.id ";
  }

  /**
   * @param $rows
   *
   * @return array
   */
  function statistics(&$rows) {return;
    $statistics = parent::statistics($rows);

    $totalAmount = $average = array();
    $count = 0;
    $select = "
        SELECT COUNT({$this->_aliases['civicrm_contribution']}.total_amount ) as count,
               SUM( {$this->_aliases['civicrm_contribution']}.total_amount ) as amount,
               ROUND(AVG({$this->_aliases['civicrm_contribution']}.total_amount), 2) as avg,
               {$this->_aliases['civicrm_contribution']}.currency as currency
        ";
               
   $select = "SELECT * ";

    $group = "\nGROUP BY {$this->_aliases['civicrm_contribution']}.currency";
    $sql = "{$select} {$this->_from} {$this->_where} {$group}";
    $dao = CRM_Core_DAO::executeQuery($sql);

    while ($dao->fetch()) {
      $totalAmount[] = CRM_Utils_Money::format($dao->amount, $dao->currency)." (".$dao->count.")";
      $average[] =   CRM_Utils_Money::format($dao->avg, $dao->currency);
      $count += $dao->count;
    }
    $statistics['counts']['amount'] = array(
      'title' => ts('Total Amount (Donations)'),
      'value' => implode(',  ', $totalAmount),
      'type' => CRM_Utils_Type::T_STRING,
    );
    $statistics['counts']['count'] = array(
      'title' => ts('Total Donations'),
      'value' => $count,
    );
    $statistics['counts']['avg'] = array(
      'title' => ts('Average'),
      'value' => implode(',  ', $average),
      'type' => CRM_Utils_Type::T_STRING,
    );

    // Stats for soft credits
    if ($this->_softFrom && CRM_Utils_Array::value('contribution_or_soft_value', $this->_params) != 'contributions_only') {
      $totalAmount = $average = array();
      $count  = 0;
      $select = "
SELECT COUNT(contribution_soft_civireport.amount ) as count,
       SUM(contribution_soft_civireport.amount ) as amount,
       ROUND(AVG(contribution_soft_civireport.amount), 2) as avg,
       {$this->_aliases['civicrm_contribution']}.currency as currency";
      $sql = "
{$select}
{$this->_softFrom}
GROUP BY {$this->_aliases['civicrm_contribution']}.currency";
      $dao = CRM_Core_DAO::executeQuery($sql);
      while ($dao->fetch()) {
        $totalAmount[] = CRM_Utils_Money::format($dao->amount, $dao->currency)." (".$dao->count.")";
        $average[] =   CRM_Utils_Money::format($dao->avg, $dao->currency);
        $count += $dao->count;
      }
      $statistics['counts']['softamount'] = array(
        'title' => ts('Total Amount (Soft Credits)'),
        'value' => implode(',  ', $totalAmount),
        'type' => CRM_Utils_Type::T_STRING,
      );
      $statistics['counts']['softcount'] = array(
        'title' => ts('Total Soft Credits'),
        'value' => $count,
      );
      $statistics['counts']['softavg'] = array(
        'title' => ts('Average (Soft Credits)'),
        'value' => implode(',  ', $average),
        'type' => CRM_Utils_Type::T_STRING,
      );
    }

    return $statistics;
  }
  
  function postProcess() {
    // get the acl clauses built before we assemble the query
    $this->buildACLClause($this->_aliases['civicrm_contact']);

    $this->beginPostProcess();
    
    $this->_paramsBackup = $this->_params;  //storing the params values into another variable
    
    $tmpArr = array('event' , 'contribution', 'pledge');
    if(CRM_Utils_Array::value('custom_filter_by_type_op', $this->_params) == 'notin') {
        $this->filter = !empty($this->_params['custom_filter_by_type_value']) ? array_diff($tmpArr, $this->_params['custom_filter_by_type_value']) : array('date');
    } else {
        $this->filter = !empty($this->_params['custom_filter_by_type_value']) ? $this->_params['custom_filter_by_type_value'] : array('date');
    }
    
    $filterby = array_shift($this->filter);
    // 1. use main contribution query to build temp table 1
    $sql = $this->buildQuery(TRUE, $filterby);
    #print "receipt date: "; print '<pre>'; print_r($this->_params); print '</pre>'; die;
    #die($sql);
    #print $filterby.'<br />';
    #print "<br />Query1 : {$sql}<br/>";
    $tempQuery = 'CREATE TEMPORARY TABLE civireport_contribution_detail_temp1 AS ' . $sql;
    #die($tempQuery);
    CRM_Core_DAO::executeQuery($tempQuery);
    $this->setPager();
    
    $index = 1;
    if(!empty($this->filter)) {
        $index = 2;
        $finalQuery = "(SELECT * FROM civireport_contribution_detail_temp1) UNION ";
        foreach($this->filter as $filterby) {#print $filterby.'<br />';
            
            $sql = $this->buildQuery(TRUE, $filterby);//"{$this->_select} {$this->$from} {$this->_where} {$this->_groupBy}";
            #print "<br />Query in loop(@{$index}) : {$sql}<br/>";
            $tempQuery = "CREATE TEMPORARY TABLE civireport_contribution_detail_temp{$index} AS {$sql}";
            CRM_Core_DAO::executeQuery($tempQuery);
            $this->setPager();
            
            $finalQuery .= " (SELECT * FROM civireport_contribution_detail_temp{$index}) UNION ";
            $index += 1;
        }
        
        $finalQuery = substr($finalQuery, 0, -6);
        
        // 4. build temp table 3
        $sql = "CREATE TEMPORARY TABLE civireport_contribution_detail_temp{$index} AS {$finalQuery}";
        #print "<br />=>After union all Query: {$sql}<=<br/>";
        CRM_Core_DAO::executeQuery($sql);
    }

    // 5. Re-construct order-by to make sense for final query on temp3 table
    $orderBy = '';
    if (!empty($this->_orderByArray)) {
      $aliases = array_flip($this->_aliases);
      $orderClause = array();
      foreach ($this->_orderByArray as $clause) {
        list($alias, $rest) = explode('.', $clause);
        $orderClause[] = $aliases[$alias] . "_" . $rest;
      }
      $orderBy = (!empty($orderClause)) ? "ORDER BY " . implode(', ', $orderClause) : '';
    }
    
    //pagination #start
    if (empty($this->_params['charts'])) {
        $this->limit();
    }
    CRM_Utils_Hook::alterReportVar('sql', $this, $this);
        
    $this->setPager();
    //pagination #end

    $this->index = $index;
    
    $this->_from = " FROM civireport_contribution_detail_temp{$this->index} ";
    
    // 6. show result set from temp table 3
    $rows = array();
    $sql  = "SELECT * {$this->_from} {$orderBy} {$this->_limit}";
    #print "<br />=>Query final : {$sql}<=<br/>";#die($sql);
    $this->buildRows($sql, $rows);

    // format result set.
    $this->formatDisplay($rows, FALSE);

    $sort_array = array(
            'civicrm_contact_created_date',
            'civicrm_participant_register_date',
            'civicrm_contribution_receive_date' ,
            'civicrm_contribution_receipt_date' ,
            'civicrm_contact_sort_name',
            'civicrm_contribution_financial_type_id',
            'civicrm_contribution_source',
            'civicrm_contribution_total_amount',
            'civicrm_contribution_contribution_status_id',
            'civicrm_contribution_payment_instrument_id',
           );
    
    
    $this->sortArrayByArray($this->_columnHeaders, $sort_array);
    
    // assign variables to templates
    $this->doTemplateAssignment($rows);

    // do print / pdf / instance stuff if needed
    $this->endPostProcess($rows);
  }

  /**
   * @param $rows
   */
  function alterDisplay(&$rows) {
    // custom code to alter rows
    $checkList          = array();
    $entryFound         = FALSE;
    $display_flag       = $prev_cid = $cid = 0;
    $contributionTypes  = CRM_Contribute_PseudoConstant::financialType();
    $contributionStatus = CRM_Contribute_PseudoConstant::contributionStatus();
    $paymentInstruments = CRM_Contribute_PseudoConstant::paymentInstrument();
    $contributionPages  = CRM_Contribute_PseudoConstant::contributionPage();

    foreach ($rows as $rowNum => $row) {
      if (!empty($this->_noRepeats) && $this->_outputMode != 'csv') {   
          
        /*$i=1;
        if( $i == 1) { $i=2;
          print '<pre>'; print_r($row); print '</pre>';
        }*/
        #$contribution_status_id = $row['civicrm_contribution_contribution_status_id'];
        #$payment_instrument_id = $row['civicrm_contribution_payment_instrument_id'];
          
        // don't repeat contact details if its same as the previous row
        if (array_key_exists('civicrm_contact_id', $row)) {
          if ($cid = $row['civicrm_contact_id']) {
             
            if ($rowNum == 0) {
              $prev_cid = $cid;
            }
            else {
              if ($prev_cid == $cid) {
                $display_flag = 1;
                $prev_cid = $cid;
              }
              else {
                $display_flag = 0;
                $prev_cid = $cid;
              }
            }

            if ($display_flag) {
              foreach ($row as $colName => $colVal) {
                // Hide repeats in no-repeat columns, but not if the field's a section header
                if (in_array($colName, $this->_noRepeats) && !array_key_exists($colName, $this->_sections)) {
                  unset($rows[$rowNum][$colName]);
                }
              }
            }
            $entryFound = TRUE;
          }
        }
      }

      if (CRM_Utils_Array::value('civicrm_contribution_contribution_or_soft', $rows[$rowNum]) == 'Contribution') {
        unset($rows[$rowNum]['civicrm_contribution_soft_soft_credit_type_id']);
      }

      // convert donor sort name to link
      if (array_key_exists('civicrm_contact_sort_name', $row) && !empty($rows[$rowNum]['civicrm_contact_sort_name']) &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Utils_System::url("civicrm/contact/view",
          'reset=1&cid=' . $row['civicrm_contact_id'],
          $this->_absoluteUrl
        );
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("View Contact Summary for this Contact.");
      }

      if ($value = CRM_Utils_Array::value('civicrm_contribution_financial_type_id', $row)) {
        $rows[$rowNum]['civicrm_contribution_financial_type_id'] = $contributionTypes[$value];
        $entryFound = TRUE;
      }
      if ($value = CRM_Utils_Array::value('civicrm_contribution_contribution_status_id', $row)) {
        $rows[$rowNum]['civicrm_contribution_contribution_status_id'] = $contributionStatus[$value];
        $entryFound = TRUE;
      }
      if ($value = CRM_Utils_Array::value('civicrm_contribution_contribution_page_id', $row)) {
        $rows[$rowNum]['civicrm_contribution_contribution_page_id'] = $contributionPages[$value];
        $entryFound = TRUE;
      }
      if ($value = CRM_Utils_Array::value('civicrm_contribution_payment_instrument_id', $row)) {
        $rows[$rowNum]['civicrm_contribution_payment_instrument_id'] = $paymentInstruments[$value];
        $entryFound = TRUE;
      }
      if (array_key_exists('civicrm_batch_batch_id', $row)) {
        if ($value = $row['civicrm_batch_batch_id']) {
          $rows[$rowNum]['civicrm_batch_batch_id'] = CRM_Core_DAO::getFieldValue('CRM_Batch_DAO_Batch', $value, 'title');
        }
        $entryFound = TRUE;
      }

      // Contribution amount links to viewing contribution
      if (($value = CRM_Utils_Array::value('civicrm_contribution_total_amount_sum', $row)) &&
        CRM_Core_Permission::check('access CiviContribute')
      ) {
        $url = CRM_Utils_System::url("civicrm/contact/view/contribution",
          "reset=1&id=" . $row['civicrm_contribution_contribution_id'] . "&cid=" . $row['civicrm_contact_id'] . "&action=view&context=contribution&selectedChild=contribute",
          $this->_absoluteUrl
        );
        $rows[$rowNum]['civicrm_contribution_total_amount_sum_link'] = $url;
        $rows[$rowNum]['civicrm_contribution_total_amount_sum_hover'] = ts("View Details of this Contribution.");
        $entryFound = TRUE;
      }

      // convert campaign_id to campaign title
      if (array_key_exists('civicrm_contribution_campaign_id', $row)) {
        if ($value = $row['civicrm_contribution_campaign_id']) {
          $rows[$rowNum]['civicrm_contribution_campaign_id'] = $this->activeCampaigns[$value];
          $entryFound = TRUE;
        }
      }

      // skip looking further in rows, if first row itself doesn't
      // have the column we need
      if (!$entryFound) {
        break;
      }
      $lastKey = $rowNum;
    }
  }

  function sectionTotals( ) {

    // Reports using order_bys with sections must populate $this->_selectAliases in select() method.
    if (empty($this->_selectAliases)) {
      return;
    }

    if (!empty($this->_sections)) {
      // build the query with no LIMIT clause
      $select = str_ireplace( 'SELECT SQL_CALC_FOUND_ROWS ', 'SELECT ', $this->_select );
      $sql = "{$select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_having} {$this->_orderBy}";

      // pull section aliases out of $this->_sections
      $sectionAliases = array_keys($this->_sections);

      $ifnulls = array();
      foreach (array_merge($sectionAliases, $this->_selectAliases) as $alias) {
        if(strpos($alias, 'date') !== false) {
            $ifnulls[] = "ifnull(DATE_FORMAT({$alias}, '%Y-%m-%d'), '') as $alias";
        } else {
            $ifnulls[] = "ifnull($alias, '') as $alias";
        }
      }

      /* Group (un-limited) report by all aliases and get counts. This might
      * be done more efficiently when the contents of $sql are known, ie. by
      * overriding this method in the report class.
      */

      $addtotals = '';

      if (array_search("civicrm_contribution_total_amount_sum", $this->_selectAliases) !== FALSE) {
        $addtotals = ", sum(civicrm_contribution_total_amount_sum) as sumcontribs";
        $showsumcontribs = TRUE;
      }

      $query = "select "
        . implode(", ", $ifnulls)
        ."$addtotals, count(*) as ct from civireport_contribution_detail_temp{$this->index} group by ".  implode(", ", $sectionAliases);
      // initialize array of total counts
      $sumcontribs = $totals = array();
      $dao = CRM_Core_DAO::executeQuery($query);
      while ($dao->fetch()) {

        // let $this->_alterDisplay translate any integer ids to human-readable values.
        $rows[0] = $dao->toArray();
        $this->alterDisplay($rows);
        $row = $rows[0];

        // add totals for all permutations of section values
        $values = array();
        $i = 1;
        $aliasCount = count($sectionAliases);
        foreach ($sectionAliases as $alias) {
          $values[] = $row[$alias];
          $key = implode(CRM_Core_DAO::VALUE_SEPARATOR, $values);
          if ($i == $aliasCount) {
            // the last alias is the lowest-level section header; use count as-is
            $totals[$key] = $dao->ct;
            if ($showsumcontribs) { $sumcontribs[$key] = $dao->sumcontribs; }
          }
          else {
            // other aliases are higher level; roll count into their total
            $totals[$key] = (array_key_exists($key, $totals)) ? $totals[$key] + $dao->ct : $dao->ct;
            if ($showsumcontribs) {
              $sumcontribs[$key] = array_key_exists($key, $sumcontribs) ? $sumcontribs[$key] + $dao->sumcontribs : $dao->sumcontribs;
            }
          }
        }
      }
      if ($showsumcontribs) {
        $totalandsum = array();
        // ts exception to avoid having ts("%1 %2: %3")
        $title = '%1 contributions / soft-credits: %2';

        if (CRM_Utils_Array::value('contribution_or_soft_value', $this->_params) == 'contributions_only') {
          $title = '%1 contributions: %2';
        } else if (CRM_Utils_Array::value('contribution_or_soft_value', $this->_params) == 'soft_credits_only') {
          $title = '%1 soft-credits: %2';
        }
        foreach ($totals as $key => $total) {
          $totalandsum[$key] = ts($title, array(
            1 => $total,
            2 => CRM_Utils_Money::format($sumcontribs[$key])
          ));
        }
        $this->assign('sectionTotals', $totalandsum);
      }
      else {
        $this->assign('sectionTotals', $totals);
      }
    }
  }
  
  //Custom and Override Methods are below
  
    function selectClause($tableName, $fields = 'fields', $fieldName, $field) {
      if($fieldName == 'receive_date' || $fieldName == 'register_date' || $fieldName == 'receipt_date' 
              || $fieldName == 'total_amount' || $fieldName == 'contribution_status_id' || $fieldName == 'payment_instrument_id'
              || $fieldName == 'contribution_page_id' || $fieldName == 'financial_type_id' ) {

          if(!array_key_exists("{$tableName}_{$fieldName}", $this->_columnHeaders)) {
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = CRM_Utils_Array::value('title', $field);
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);

            $alias = "{$tableName}_{$fieldName}";
            $this->recursive_array_search($alias);  //removing current field from no display array
            
            //this if condition only for making the users event registration date 
            //as created date
            if($this->filterBy == 'event' && $fieldName == 'receive_date' && CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'reg_date') {
                #return "{$field['dbAlias']} as civicrm_contribution_receive_date";
                return "DATE_FORMAT(participant_civireport.register_date, '%Y-%m-%d') as civicrm_contribution_receive_date ";
            } else if($fieldName == 'receipt_date' ) {
                
                //For some transactions Payment status is Completed but no payment date, so we are assume transaction date as payment date
                
                return " IF("
                . "({$this->_aliases['civicrm_contribution']}.{$fieldName} IS NULL AND {$this->_aliases['civicrm_contribution']}.contribution_status_id = 1) ,"
                . " DATE_FORMAT({$this->_aliases['civicrm_contribution']}.receive_date, '%Y-%m-%d'), DATE_FORMAT({$field['dbAlias']}, '%Y-%m-%d'))  as $alias";
            } else {
                if(strpos($field['dbAlias'], 'date') !== false) {
                    return "DATE_FORMAT({$field['dbAlias']}, '%Y-%m-%d') as $alias";
                } else {
                    return "{$field['dbAlias']} as $alias";
                }
            }
          }
      }
    }

    function dateClause($fieldName, $relative, $from, $to, $type = NULL, $fromTime = NULL, $toTime = NULL, $isnullFlag = false) {
      $clauses = array();
      if (in_array($relative, array_keys($this->getOperationPair(CRM_Report_Form::OP_DATE)))) {
        $sqlOP = $this->getSQLOperator($relative);
        return "( {$fieldName} {$sqlOP} )";
      }

      list($from, $to) = $this->getFromTo($relative, $from, $to, $fromTime, $toTime);

      $nullTxt = '';
      if($isnullFlag == true) {
          $nullTxt = " OR {$fieldName} IS NULL ";
          $this->nullTxt[] = $nullTxt;
      }
      
      if ($from) {
        $from = ($type == CRM_Utils_Type::T_DATE) ? substr($from, 0, 8) : $from;
        $clauses[] = "( DATE_FORMAT({$fieldName},'%Y-%m-%d') >= '{$from}' $nullTxt )";   //changed the fromat  
      }

      if ($to) {
        $to = ($type == CRM_Utils_Type::T_DATE) ? substr($to, 0, 8) : $to;
        $clauses[] = "( DATE_FORMAT({$fieldName},'%Y-%m-%d') <= '{$to}' $nullTxt )";     //changed the fromat
      }

      if (!empty($clauses)) {
        return implode(' AND ', $clauses);
      }

      return NULL;
    }
  
    function getFromTo($relative, $from, $to, $fromtime = NULL, $totime = NULL) {
      if (empty($totime)) {
        $totime = '235959';
      }
      //FIX ME not working for relative
      if ($relative) {
        list($term, $unit) = CRM_Utils_System::explode('.', $relative, 2);
        $dateRange = CRM_Utils_Date::relativeToAbsolute($term, $unit);
        $from = substr($dateRange['from'], 0, 8);
        //Take only Date Part, Sometime Time part is also present in 'to'
        $to = substr($dateRange['to'], 0, 8);
      }
      $from = CRM_Utils_Date::processDate($from, $fromtime, FALSE, 'Y-m-d');  //changed the fromat
      $to = CRM_Utils_Date::processDate($to, $totime, FALSE, 'Y-m-d');        //changed the fromat
      return array($from, $to);
    }
  
  
    function filterEvent() {
        $from = '_from'.$this->filterBy;
        
        $this->$from = " FROM civicrm_event as {$this->_aliases['civicrm_event']} ";
        
        $this->$from .= "\n INNER JOIN civicrm_participant as {$this->_aliases['civicrm_participant']} ON  "
          . "{$this->_aliases['civicrm_participant']}.event_id = {$this->_aliases['civicrm_event']}.id ";
          
        $this->$from .= "\n INNER JOIN civicrm_contact as {$this->_aliases['civicrm_contact']} ON "
              . "{$this->_aliases['civicrm_participant']}.contact_id = {$this->_aliases['civicrm_contact']}.id ";
        
        $this->$from .= "\n LEFT JOIN civicrm_participant_payment as {$this->_aliases['civicrm_participant_payment']} ON "
              . " {$this->_aliases['civicrm_participant_payment']}.participant_id = {$this->_aliases['civicrm_participant']}.id ";
              //. " OR ({$this->_aliases['civicrm_participant']}.registered_by_id IS NOT NULL AND {$this->_aliases['civicrm_participant']}.registered_by_id = {$this->_aliases['civicrm_participant']}.id "
              //. " AND {$this->_aliases['civicrm_participant']}.registered_by_id = {$this->_aliases['civicrm_participant_payment']}.participant_id "
              //. " AND {$this->_aliases['civicrm_participant']}.event_id = {$this->_aliases['civicrm_event']}.id) ";
              
              
          
        /*$this->$from = " FROM  civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom}";
        
        $this->$from .= "\n INNER JOIN civicrm_participant as {$this->_aliases['civicrm_participant']} ON  "
          . "{$this->_aliases['civicrm_participant']}.contact_id = {$this->_aliases['civicrm_contact']}.id ";
          
        $this->$from .= "\n INNER JOIN civicrm_participant_payment as {$this->_aliases['civicrm_participant_payment']} ON "
              . "{$this->_aliases['civicrm_participant_payment']}.participant_id = {$this->_aliases['civicrm_participant']}.id";
          
        $this->$from .= "\n INNER JOIN civicrm_event as {$this->_aliases['civicrm_event']} ON "
          . "{$this->_aliases['civicrm_event']}.id = {$this->_aliases['civicrm_participant']}.event_id";*/
          
        
          
        if(CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'pay_date') {
                          

              $this->$from .= "\n LEFT JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
            . " {$this->_aliases['civicrm_contribution']}.id = {$this->_aliases['civicrm_participant_payment']}.contribution_id"
            . " INNER JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
            . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";
          } else {
            $this->$from .= "\n LEFT JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
              . "{$this->_aliases['civicrm_participant_payment']}.contribution_id = {$this->_aliases['civicrm_contribution']}.id "
              . " LEFT JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
              . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";
          }       
    }
    
    function filterPledge() {
        $from = '_from'.$this->filterBy;
        $this->$from = " FROM  civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom} ";

        $this->$from .= "\n INNER JOIN  civicrm_pledge as {$this->_aliases['civicrm_pledge']} ON "
        . " {$this->_aliases['civicrm_pledge']}.contact_id = {$this->_aliases['civicrm_contact']}.id ";
        
        $this->$from .= "\n INNER JOIN  civicrm_pledge_payment as {$this->_aliases['civicrm_pledge_payment']} ON "
        . " {$this->_aliases['civicrm_pledge_payment']}.pledge_id = {$this->_aliases['civicrm_pledge']}.id ";
        
        $this->$from .= "\n LEFT JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
            . "{$this->_aliases['civicrm_pledge_payment']}.contribution_id = {$this->_aliases['civicrm_contribution']}.id "
            . " LEFT JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
            . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";
    }
    
    function filterContribution() {
        $from = '_from'.$this->filterBy;
        $this->$from = " FROM  civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom}";
        
        
        if( CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'pay_date' ) {
            $this->$from .= "\n INNER JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
                             . "{$this->_aliases['civicrm_contribution']}.contact_id = {$this->_aliases['civicrm_contact']}.id AND {$this->_aliases['civicrm_contribution']}.contribution_status_id = 1 AND {$this->_aliases['civicrm_contribution']}.contribution_page_id IS NOT NULL "
                             . " LEFT JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
                             . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";
        } else {
            $this->$from .= "\n INNER JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
                             . "{$this->_aliases['civicrm_contribution']}.contact_id = {$this->_aliases['civicrm_contact']}.id AND {$this->_aliases['civicrm_contribution']}.contribution_page_id IS NOT NULL "
                             . " LEFT JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
                             . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";
                             
        }
        /*$this->$from .= "\n INNER JOIN civicrm_financial_item as {$this->_aliases['civicrm_financial_item']} ON "
                         . " {$this->_aliases['civicrm_financial_item']}.contact_id =  {$this->_aliases['civicrm_contact']}.id ";
        
        $this->$from .= "\n INNER JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
            . "{$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id "
            . " LEFT JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
            . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";*/
    }
    
    function filterDate() {
        $from = '_from'.$this->filterBy;
        $this->$from = " FROM  civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom}";

        if( CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'pay_date' ) {
            
            /*$this->$from .= "\n INNER JOIN civicrm_financial_item as {$this->_aliases['civicrm_financial_item']} ON "
                     . " {$this->_aliases['civicrm_financial_item']}.contact_id =  {$this->_aliases['civicrm_contact']}.id "
                     . " INNER JOIN civicrm_line_item  as {$this->_aliases['civicrm_line_item']} ON"
                     . " {$this->_aliases['civicrm_line_item']}.id =  {$this->_aliases['civicrm_financial_item']}.entity_id AND {$this->_aliases['civicrm_financial_item']}.entity_table = 'civicrm_line_item' ";
            */            
            
            $this->$from .= "\n INNER JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
                         . "{$this->_aliases['civicrm_contribution']}.contact_id = {$this->_aliases['civicrm_contact']}.id AND {$this->_aliases['civicrm_contribution']}.contribution_status_id = 1"
                         . " LEFT JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
                         . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";
        } else {
            
            $this->$from .= "\n INNER JOIN civicrm_contribution as {$this->_aliases['civicrm_contribution']} ON "
                         . "{$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id "
                         . " LEFT JOIN  civicrm_financial_type as {$this->_aliases['civicrm_financial_type']} ON  "
                         . " {$this->_aliases['civicrm_financial_type']}.id = {$this->_aliases['civicrm_contribution']}.financial_type_id ";
        }
        
        /*if ('' != CRM_Utils_Array::value('custom_filter_by_value', $this->_params) && (NULL != $this->regdate_relative || NULL != $this->receipt_relative) ) {
          $this->$from .= "\n RIGHT JOIN civicrm_participant as {$this->_aliases['civicrm_participant']} ON  "
          . "{$this->_aliases['civicrm_participant']}.contact_id = {$this->_aliases['civicrm_contact']}.id ";

          if(CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'pay_date') {
            $this->$from .= "\n LEFT JOIN civicrm_participant_payment as {$this->_aliases['civicrm_participant_payment']} ON "
              . "{$this->_aliases['civicrm_participant_payment']}.participant_id = {$this->_aliases['civicrm_participant']}.id"
              . " AND {$this->_aliases['civicrm_contribution']}.id = {$this->_aliases['civicrm_participant_payment']}.contribution_id";
          }
        }*/
    }
    
    
    function _setColumns() {
        $this->_params = $this->_paramsBackup;

        $this->_params['fields']['receive_date'] = 1;
        if(CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'pay_date') {
          $this->_params['receipt_date_relative'] = $this->_params['register_date_relative'];
          $this->_params['receipt_date_from'] = $this->_params['register_date_from'];
          $this->_params['receipt_date_to'] = $this->_params['register_date_to'];
          $this->_params['fields']['receipt_date'] = 1;

          unset($this->_params['register_date_relative'], $this->_params['register_date_from'], $this->_params['register_date_to']);
          unset($this->_params['fields']['register_date']);

          $this->receipt_from = $this->_params['receipt_date_from'];
          $this->receipt_to = $this->_params['receipt_date_to'];
          $this->receipt_relative = $this->_params['register_date_relative'];
          
          $this->_params['fields']['receive_date'] = 1;          
        } else if( CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'reg_date' ) {

            if($this->filterBy == 'event') {
                #$this->_params['fields']['register_date'] = 1;
                $this->regdate_from = $this->_params['register_date_from'];
                $this->regdate_to = $this->_params['register_date_to'];
                $this->regdate_relative = $this->_params['register_date_relative'];
                #unset($this->_params['fields']['receive_date']);
                #$this->_columns['civicrm_contact']['fields']['receive_date']['pseudofield'] = TRUE;
            } else {
                $this->_params['receive_date_relative'] = $this->_params['register_date_relative'];
                $this->_params['receive_date_from'] = $this->_params['register_date_from'];
                $this->_params['receive_date_to'] = $this->_params['register_date_to'];
                unset($this->_params['register_date_relative'], $this->_params['register_date_from'], $this->_params['register_date_to']);
                unset($this->_params['fields']['register_date']);
                $this->_params['fields']['receive_date'] = 1;
            }
        }
        
        $this->_params['fields']['receipt_date'] = 1;
    }
    
    function buildQuery($applyLimit = TRUE, $filterby = '') {//print $from;
        static $tempi = 1;
        
        $this->filterBy = $filterby;
        
        $this->_setColumns();
        
        $this->select();
        $this->from();
        $this->customDataFrom();
        $this->where();
        $this->groupBy();

        if($tempi ==1) {
            $this->orderBy();
            $tempi += 1;
        }
        
        // order_by columns not selected for display need to be included in SELECT
        $unselectedSectionColumns = $this->unselectedSectionColumns();
        foreach ($unselectedSectionColumns as $alias => $section) {
          $this->_select .= ", {$section['dbAlias']} as {$alias}";
        }

        /*if ($applyLimit && empty($this->_params['charts'])) {
          $this->limit();
        }
        CRM_Utils_Hook::alterReportVar('sql', $this, $this);*/

        $from = '_from'.$this->filterBy;
        $sql = "{$this->_select} {$this->$from} {$this->_where} {$this->_groupBy} {$this->_having} {$this->_orderBy} "; //{$this->_limit}";
        return $sql;
    }
    
    function where() {
        
        $tmpcol = $this->_columns;
        
        if($this->filterBy == 'pledge') {
            unset($tmpcol['civicrm_event'], $tmpcol['civicrm_contribution']['filters']['donation_list']);
            
        } elseif($this->filterBy == 'event') {
            unset($tmpcol['civicrm_pledge'], $tmpcol['civicrm_contribution']['filters']['donation_list']);
            
        } elseif($this->filterBy == 'contribution') {
            unset($tmpcol['civicrm_participant'], $tmpcol['civicrm_event'], $tmpcol['civicrm_pledge']);
        }
        
        $this->storeWhereHavingClauseArray($tmpcol, $reset = TRUE);

        if (empty($this->_whereClauses)) {
          $this->_where = "WHERE ( 1 ) ";
          $this->_having = "";
        }
        else {
          $this->_where = "WHERE " . implode(' AND ', $this->_whereClauses);
        }

        if ($this->_aclWhere) {
          $this->_where .= " AND {$this->_aclWhere} ";
        }

        if (!empty($this->_havingClauses)) {
          // use this clause to construct group by clause.
          $this->_having = "HAVING " . implode(' AND ', $this->_havingClauses);
        }
        
        
        //only for pledge payment
        if(count($this->nullTxt) > 0 && $this->filterBy == 'pledge' && CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'reg_date') {
          $from = '_from'.$this->filterBy;
          $whereUntil = str_replace($this->nullTxt, '', $this->_where);
          $this->_where .= " AND {$this->_aliases['civicrm_contact']}.id IN ( SELECT DISTINCT {$this->_aliases['civicrm_contact']}.id {$this->$from} {$whereUntil} )";
        }
    }
    
    //Array Search 
    function recursive_array_search($needle) {
        foreach($this->_noDisplay as $key=>$value) {
            $current_key=$key;
            if($needle===$value OR (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
                unset($this->_noDisplay[$current_key]);
            }
        }
    }
    
    //Sorting Array
    function sortArrayByArray(Array &$array, Array $orderArray) {
        $ordered = array();
        foreach($orderArray as $key) {
            if(array_key_exists($key,$array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        $array = $ordered + $array;
    }
    
    
   /**
   * Store Where clauses into an array - breaking out this step makes
   * over-riding more flexible as the clauses can be used in constructing a
   * temp table that may not be part of the final where clause or added
   * in other functions
   */
    function storeWhereHavingClauseArray($_columns, $reset = FALSE){

      if($reset == TRUE) {
          $this->_whereClauses = array();
      } 

      foreach ($_columns as $tableName => $table) {
        if (array_key_exists('filters', $table)) {
          foreach ($table['filters'] as $fieldName => $field) {
            // respect pseudofield to filter spec so fields can be marked as
            // not to be handled here
            if(!empty($field['pseudofield'])){
              continue;
            }

            $isnullFlag = false;
            //For some transactions Payment status is Completed but no payment date, so we are assume transaction date as payment date
            if($fieldName == 'receipt_date') {
                $field['dbAlias'] = " IF( ({$this->_aliases['civicrm_contribution']}.receipt_date IS NULL AND {$this->_aliases['civicrm_contribution']}.contribution_status_id = 1), {$this->_aliases['civicrm_contribution']}.receive_date, {$this->_aliases['civicrm_contribution']}.receipt_date) ";
            } elseif($this->filterBy == 'pledge' && $fieldName == 'receive_date' && CRM_Utils_Array::value('custom_filter_by_value', $this->_params) == 'reg_date') {
                #$isnullFlag = true; //only for pledge payment
            }
            
            $clause = NULL;
            if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
              if (CRM_Utils_Array::value('operatorType', $field) == CRM_Report_Form::OP_MONTH) {
                $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
                $value = CRM_Utils_Array::value("{$fieldName}_value", $this->_params);
                if (is_array($value) && !empty($value)) {
                  $clause = "(month({$field['dbAlias']}) $op (" . implode(', ', $value) . '))';
                }
              }
              else {
                $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
                $from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
                $to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);
                $fromTime = CRM_Utils_Array::value("{$fieldName}_from_time", $this->_params);
                $toTime   = CRM_Utils_Array::value("{$fieldName}_to_time", $this->_params);
                #$clause   = $this->dateClause($field['dbAlias'], $relative, $from, $to, $field['type'], $fromTime, $toTime);
                $clause   = $this->dateClause($field['dbAlias'], $relative, $from, $to, CRM_Utils_Type::T_TIME, $fromTime, $toTime, $isnullFlag);
              }
            }
            else {
              $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);

              if ($op) {
                $clause = $this->whereClause($field,
                  $op,
                  CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
                  CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
                  CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
                );
              }
            }

            if (!empty($clause)) {
              if (!empty($field['having'])) {
                $this->_havingClauses[] = $clause;
              }
              else {
                $this->_whereClauses[] = $clause;
              }
            }
          }
        }
      }

    }
}
