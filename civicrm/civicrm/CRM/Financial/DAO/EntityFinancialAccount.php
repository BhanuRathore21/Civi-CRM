<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from xml/schema/CRM/Financial/EntityFinancialAccount.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:e70f42c50c6b264f47371a93183e7010)
 */

/**
 * Database access object for the EntityFinancialAccount entity.
 */
class CRM_Financial_DAO_EntityFinancialAccount extends CRM_Core_DAO {
  const EXT = 'civicrm';
  const TABLE_ADDED = '4.3';
  const COMPONENT = 'CiviContribute';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_entity_financial_account';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Paths for accessing this entity in the UI.
   *
   * @var string[]
   */
  protected static $_paths = [
    'add' => 'civicrm/admin/financial/financialType/accounts?action=add&reset=1&aid=[entity_id]',
    'update' => 'civicrm/admin/financial/financialType/accounts?action=update&id=[id]&aid=[entity_id]&reset=1',
    'delete' => 'civicrm/admin/financial/financialType/accounts?action=delete&id=[id]&aid=[entity_id]&reset=1',
  ];

  /**
   * ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * Links to an entity_table like civicrm_financial_type
   *
   * @var string
   *   (SQL type: varchar(64))
   *   Note that values will be retrieved from the database as a string.
   */
  public $entity_table;

  /**
   * Links to an id in the entity_table, such as vid in civicrm_financial_type
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $entity_id;

  /**
   * FK to a new civicrm_option_value (account_relationship)
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $account_relationship;

  /**
   * FK to the financial_account_id
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $financial_account_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_entity_financial_account';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? ts('Entity Financial Accounts') : ts('Entity Financial Account');
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Entity Financial Account ID'),
          'description' => ts('ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_entity_financial_account.id',
          'table_name' => 'civicrm_entity_financial_account',
          'entity' => 'EntityFinancialAccount',
          'bao' => 'CRM_Financial_BAO_EntityFinancialAccount',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => '4.3',
        ],
        'entity_table' => [
          'name' => 'entity_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Entity Table'),
          'description' => ts('Links to an entity_table like civicrm_financial_type'),
          'required' => TRUE,
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
          'usage' => [
            'import' => TRUE,
            'export' => TRUE,
            'duplicate_matching' => TRUE,
            'token' => FALSE,
          ],
          'import' => TRUE,
          'where' => 'civicrm_entity_financial_account.entity_table',
          'export' => TRUE,
          'table_name' => 'civicrm_entity_financial_account',
          'entity' => 'EntityFinancialAccount',
          'bao' => 'CRM_Financial_BAO_EntityFinancialAccount',
          'localizable' => 0,
          'html' => [
            'type' => 'Select',
            'label' => ts("Entity Type"),
          ],
          'pseudoconstant' => [
            'callback' => 'CRM_Financial_BAO_EntityFinancialAccount::entityTables',
          ],
          'add' => '4.3',
        ],
        'entity_id' => [
          'name' => 'entity_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Entity ID'),
          'description' => ts('Links to an id in the entity_table, such as vid in civicrm_financial_type'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_entity_financial_account.entity_id',
          'table_name' => 'civicrm_entity_financial_account',
          'entity' => 'EntityFinancialAccount',
          'bao' => 'CRM_Financial_BAO_EntityFinancialAccount',
          'localizable' => 0,
          'DFKEntityColumn' => 'entity_table',
          'FKColumnName' => 'id',
          'html' => [
            'type' => 'EntityRef',
            'label' => ts("Entity"),
          ],
          'add' => '4.3',
        ],
        'account_relationship' => [
          'name' => 'account_relationship',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Account Relationship'),
          'description' => ts('FK to a new civicrm_option_value (account_relationship)'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_entity_financial_account.account_relationship',
          'table_name' => 'civicrm_entity_financial_account',
          'entity' => 'EntityFinancialAccount',
          'bao' => 'CRM_Financial_BAO_EntityFinancialAccount',
          'localizable' => 0,
          'html' => [
            'type' => 'Select',
          ],
          'pseudoconstant' => [
            'optionGroupName' => 'account_relationship',
            'optionEditPath' => 'civicrm/admin/options/account_relationship',
          ],
          'add' => '4.3',
        ],
        'financial_account_id' => [
          'name' => 'financial_account_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Financial Account ID'),
          'description' => ts('FK to the financial_account_id'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_entity_financial_account.financial_account_id',
          'table_name' => 'civicrm_entity_financial_account',
          'entity' => 'EntityFinancialAccount',
          'bao' => 'CRM_Financial_BAO_EntityFinancialAccount',
          'localizable' => 0,
          'FKClassName' => 'CRM_Financial_DAO_FinancialAccount',
          'FKColumnName' => 'id',
          'html' => [
            'type' => 'Select',
            'label' => ts("Financial Account"),
          ],
          'pseudoconstant' => [
            'table' => 'civicrm_financial_account',
            'keyColumn' => 'id',
            'labelColumn' => 'name',
          ],
          'add' => '4.3',
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'entity_financial_account', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'entity_financial_account', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [
      'index_entity_id_entity_table_account_relationship' => [
        'name' => 'index_entity_id_entity_table_account_relationship',
        'field' => [
          0 => 'entity_id',
          1 => 'entity_table',
          2 => 'account_relationship',
        ],
        'localizable' => FALSE,
        'unique' => TRUE,
        'sig' => 'civicrm_entity_financial_account::1::entity_id::entity_table::account_relationship',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}