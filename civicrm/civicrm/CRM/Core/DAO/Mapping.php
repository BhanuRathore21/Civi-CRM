<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from xml/schema/CRM/Core/Mapping.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:bd700a2b140ebe3a72cd122bbe04efbd)
 */

/**
 * Database access object for the Mapping entity.
 */
class CRM_Core_DAO_Mapping extends CRM_Core_DAO {
  const EXT = 'civicrm';
  const TABLE_ADDED = '1.2';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_mapping';

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
   * Paths for accessing this entity in the UI.
   *
   * @var string[]
   */
  protected static $_paths = [
    'add' => 'civicrm/admin/mapping/edit?reset=1&action=add',
    'browse' => 'civicrm/admin/mapping?reset=1',
    'update' => 'civicrm/admin/mapping/edit?reset=1&action=update&id=[id]',
  ];

  /**
   * Mapping ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * Unique name of Mapping
   *
   * @var string
   *   (SQL type: varchar(64))
   *   Note that values will be retrieved from the database as a string.
   */
  public $name;

  /**
   * Description of Mapping.
   *
   * @var string|null
   *   (SQL type: varchar(255))
   *   Note that values will be retrieved from the database as a string.
   */
  public $description;

  /**
   * Mapping Type
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $mapping_type_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_mapping';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? ts('Field Mappings') : ts('Field Mapping');
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
          'title' => ts('Mapping ID'),
          'description' => ts('Mapping ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mapping.id',
          'table_name' => 'civicrm_mapping',
          'entity' => 'Mapping',
          'bao' => 'CRM_Core_BAO_Mapping',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => '1.2',
        ],
        'name' => [
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Mapping Name'),
          'description' => ts('Unique name of Mapping'),
          'required' => TRUE,
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mapping.name',
          'table_name' => 'civicrm_mapping',
          'entity' => 'Mapping',
          'bao' => 'CRM_Core_BAO_Mapping',
          'localizable' => 0,
          'add' => '1.2',
        ],
        'description' => [
          'name' => 'description',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Description'),
          'description' => ts('Description of Mapping.'),
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mapping.description',
          'table_name' => 'civicrm_mapping',
          'entity' => 'Mapping',
          'bao' => 'CRM_Core_BAO_Mapping',
          'localizable' => 0,
          'add' => '1.2',
        ],
        'mapping_type_id' => [
          'name' => 'mapping_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Mapping Type'),
          'description' => ts('Mapping Type'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mapping.mapping_type_id',
          'table_name' => 'civicrm_mapping',
          'entity' => 'Mapping',
          'bao' => 'CRM_Core_BAO_Mapping',
          'localizable' => 0,
          'html' => [
            'type' => 'Select',
          ],
          'pseudoconstant' => [
            'optionGroupName' => 'mapping_type',
            'optionEditPath' => 'civicrm/admin/options/mapping_type',
          ],
          'add' => '2.1',
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'mapping', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'mapping', $prefix, []);
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
      'UI_name' => [
        'name' => 'UI_name',
        'field' => [
          0 => 'name',
        ],
        'localizable' => FALSE,
        'unique' => TRUE,
        'sig' => 'civicrm_mapping::1::name',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}