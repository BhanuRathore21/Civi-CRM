<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from xml/schema/CRM/Contribute/PremiumsProduct.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:e0fd905bafc0c4946615101587f696a8)
 */

/**
 * Database access object for the PremiumsProduct entity.
 */
class CRM_Contribute_DAO_PremiumsProduct extends CRM_Core_DAO {
  const EXT = 'civicrm';
  const TABLE_ADDED = '1.4';
  const COMPONENT = 'CiviContribute';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_premiums_product';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Contribution ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * Foreign key to premiums settings record.
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $premiums_id;

  /**
   * Foreign key to each product object.
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $product_id;

  /**
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $weight;

  /**
   * FK to Financial Type.
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $financial_type_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_premiums_product';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? ts('Product Premiums') : ts('Product Premium');
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
          'title' => ts('Premium Product ID'),
          'description' => ts('Contribution ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_premiums_product.id',
          'table_name' => 'civicrm_premiums_product',
          'entity' => 'PremiumsProduct',
          'bao' => 'CRM_Contribute_DAO_PremiumsProduct',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => '1.4',
        ],
        'premiums_id' => [
          'name' => 'premiums_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Premium ID'),
          'description' => ts('Foreign key to premiums settings record.'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_premiums_product.premiums_id',
          'table_name' => 'civicrm_premiums_product',
          'entity' => 'PremiumsProduct',
          'bao' => 'CRM_Contribute_DAO_PremiumsProduct',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contribute_DAO_Premium',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("Premium"),
          ],
          'add' => '1.4',
        ],
        'product_id' => [
          'name' => 'product_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Product ID'),
          'description' => ts('Foreign key to each product object.'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_premiums_product.product_id',
          'table_name' => 'civicrm_premiums_product',
          'entity' => 'PremiumsProduct',
          'bao' => 'CRM_Contribute_DAO_PremiumsProduct',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contribute_DAO_Product',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("Product"),
          ],
          'add' => '1.4',
        ],
        'weight' => [
          'name' => 'weight',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Order'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_premiums_product.weight',
          'table_name' => 'civicrm_premiums_product',
          'entity' => 'PremiumsProduct',
          'bao' => 'CRM_Contribute_DAO_PremiumsProduct',
          'localizable' => 0,
          'add' => '2.0',
        ],
        'financial_type_id' => [
          'name' => 'financial_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Financial Type ID'),
          'description' => ts('FK to Financial Type.'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_premiums_product.financial_type_id',
          'default' => NULL,
          'table_name' => 'civicrm_premiums_product',
          'entity' => 'PremiumsProduct',
          'bao' => 'CRM_Contribute_DAO_PremiumsProduct',
          'localizable' => 0,
          'FKClassName' => 'CRM_Financial_DAO_FinancialType',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("Financial Type"),
          ],
          'pseudoconstant' => [
            'table' => 'civicrm_financial_type',
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'premiums_product', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'premiums_product', $prefix, []);
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
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}