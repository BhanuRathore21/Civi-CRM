<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from xml/schema/CRM/Mailing/Event/MailingEventQueue.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:953dd56586ae02b7dde48a338f0827ba)
 */

/**
 * Database access object for the MailingEventQueue entity.
 */
class CRM_Mailing_Event_DAO_MailingEventQueue extends CRM_Core_DAO {
  const EXT = 'civicrm';
  const TABLE_ADDED = '';
  const COMPONENT = 'CiviMail';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_mailing_event_queue';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = FALSE;

  /**
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * Mailing Job
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $job_id;

  /**
   * Related mailing. Used for reporting on mailing success, if present.
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $mailing_id;

  /**
   * @var bool|string
   *   (SQL type: tinyint)
   *   Note that values will be retrieved from the database as a string.
   */
  public $is_test;

  /**
   * FK to Email
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $email_id;

  /**
   * FK to Contact
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $contact_id;

  /**
   * Security hash
   *
   * @var string
   *   (SQL type: varchar(255))
   *   Note that values will be retrieved from the database as a string.
   */
  public $hash;

  /**
   * FK to Phone
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $phone_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_mailing_event_queue';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? ts('Mailing Recipients') : ts('Mailing Recipient');
  }

  /**
   * Returns user-friendly description of this entity.
   *
   * @return string
   */
  public static function getEntityDescription() {
    return ts('Intended recipients of a mailing.');
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
          'title' => ts('Mailing Event Queue ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.id',
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
            'label' => ts("ID"),
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'job_id' => [
          'name' => 'job_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Job ID'),
          'description' => ts('Mailing Job'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.job_id',
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'FKClassName' => 'CRM_Mailing_DAO_MailingJob',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("Outbound Mailing"),
          ],
          'add' => NULL,
        ],
        'mailing_id' => [
          'name' => 'mailing_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Mailing ID'),
          'description' => ts('Related mailing. Used for reporting on mailing success, if present.'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.mailing_id',
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'FKClassName' => 'CRM_Mailing_DAO_Mailing',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("Mailing"),
          ],
          'add' => '5.67',
        ],
        'is_test' => [
          'name' => 'is_test',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'title' => ts('Test'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.is_test',
          'default' => '0',
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'html' => [
            'type' => 'CheckBox',
          ],
          'readonly' => TRUE,
          'add' => '5.67',
        ],
        'email_id' => [
          'name' => 'email_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Email ID'),
          'description' => ts('FK to Email'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.email_id',
          'default' => NULL,
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'FKClassName' => 'CRM_Core_DAO_Email',
          'FKColumnName' => 'id',
          'html' => [
            'type' => 'EntityRef',
            'label' => ts("Email"),
          ],
          'add' => NULL,
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Contact ID'),
          'description' => ts('FK to Contact'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.contact_id',
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("Contact"),
          ],
          'add' => NULL,
        ],
        'hash' => [
          'name' => 'hash',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Security Hash'),
          'description' => ts('Security hash'),
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.hash',
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'add' => NULL,
        ],
        'phone_id' => [
          'name' => 'phone_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Phone ID'),
          'description' => ts('FK to Phone'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_mailing_event_queue.phone_id',
          'default' => NULL,
          'table_name' => 'civicrm_mailing_event_queue',
          'entity' => 'MailingEventQueue',
          'bao' => 'CRM_Mailing_Event_BAO_MailingEventQueue',
          'localizable' => 0,
          'FKClassName' => 'CRM_Core_DAO_Phone',
          'FKColumnName' => 'id',
          'html' => [
            'label' => ts("Phone"),
          ],
          'add' => NULL,
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'mailing_event_queue', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'mailing_event_queue', $prefix, []);
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
      'index_hash' => [
        'name' => 'index_hash',
        'field' => [
          0 => 'hash',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_mailing_event_queue::0::hash',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}