<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from xml/schema/CRM/Core/County.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:3c3c1fed6b777fff9171e106c72678b3)
 */

/**
 * Database access object for the County entity.
 */
class CRM_Core_DAO_County extends CRM_Core_DAO {
  const EXT = 'civicrm';
  const TABLE_ADDED = '1.1';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_county';

  /**
   * Field to show when displaying a record.
   *
   * @var string
   */
  public static $_labelField = 'name';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = FALSE;

  /**
   * County ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * Name of County
   *
   * @var string|null
   *   (SQL type: varchar(64))
   *   Note that values will be retrieved from the database as a string.
   */
  public $name;

  /**
   * 2-4 Character Abbreviation of County
   *
   * @var string|null
   *   (SQL type: varchar(4))
   *   Note that values will be retrieved from the database as a string.
   */
  public $abbreviation;

  /**
   * ID of State/Province that County belongs
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $state_province_id;

  /**
   * Is this County active?
   *
   * @var bool|string
   *   (SQL type: tinyint)
   *   Note that values will be retrieved from the database as a string.
   */
  public $is_active;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_county';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? ts('Counties') : ts('County');
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
          'title' => ts('County ID'),
          'description' => ts('County ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_county.id',
          'table_name' => 'civicrm_county',
          'entity' => 'County',
          'bao' => 'CRM_Core_BAO_County',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => '1.1',
        ],
        'name' => [
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('County'),
          'description' => ts('Name of County'),
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
          'usage' => [
            'import' => TRUE,
            'export' => TRUE,
            'duplicate_matching' => TRUE,
            'token' => FALSE,
          ],
          'import' => TRUE,
          'where' => 'civicrm_county.name',
          'headerPattern' => '/county/i',
          'dataPattern' => '/[A-Z]{2}/',
          'export' => TRUE,
          'table_name' => 'civicrm_county',
          'entity' => 'County',
          'bao' => 'CRM_Core_BAO_County',
          'localizable' => 0,
          'add' => '1.1',
        ],
        'abbreviation' => [
          'name' => 'abbreviation',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('County Abbreviation'),
          'description' => ts('2-4 Character Abbreviation of County'),
          'maxlength' => 4,
          'size' => CRM_Utils_Type::FOUR,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_county.abbreviation',
          'table_name' => 'civicrm_county',
          'entity' => 'County',
          'bao' => 'CRM_Core_BAO_County',
          'localizable' => 0,
          'add' => '1.1',
        ],
        'state_province_id' => [
          'name' => 'state_province_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('State ID'),
          'description' => ts('ID of State/Province that County belongs'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_county.state_province_id',
          'table_name' => 'civicrm_county',
          'entity' => 'County',
          'bao' => 'CRM_Core_BAO_County',
          'localizable' => 0,
          'FKClassName' => 'CRM_Core_DAO_StateProvince',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("State"),
          ],
          'pseudoconstant' => [
            'table' => 'civicrm_state_province',
            'keyColumn' => 'id',
            'labelColumn' => 'name',
            'abbrColumn' => 'abbreviation',
          ],
          'add' => '1.1',
        ],
        'is_active' => [
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'title' => ts('County Is Active'),
          'description' => ts('Is this County active?'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_county.is_active',
          'default' => '1',
          'table_name' => 'civicrm_county',
          'entity' => 'County',
          'bao' => 'CRM_Core_BAO_County',
          'localizable' => 0,
          'html' => [
            'type' => 'CheckBox',
            'label' => ts("Enabled"),
          ],
          'add' => '5.35',
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'county', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'county', $prefix, []);
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
      'UI_name_state_id' => [
        'name' => 'UI_name_state_id',
        'field' => [
          0 => 'name',
          1 => 'state_province_id',
        ],
        'localizable' => FALSE,
        'unique' => TRUE,
        'sig' => 'civicrm_county::1::name::state_province_id',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}