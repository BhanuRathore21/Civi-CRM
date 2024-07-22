<?php
use CRM_Mosaico_ExtensionUtil as E;
return array(
  'mosaico_custom_plugins' => [
    'group_name' => 'Mosaico Preferences',
    'group' => 'mosaico',
    'name' => 'mosaico_custom_plugins',
    'type' => 'String',
    'html_type' => 'text',
    'html_attributes' => [
      'class' => 'huge40',
    ],
    'default' => CIVICRM_MOSAICO_CUSTOM_PLUGINS,
    'add' => '4.7',
    'title' => ts('Mosaico Plugin Lists'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Plugins name are separated by space.',
    'help_text' => NULL,
    'settings_pages' => ['mosaico' => ['weight' => 45]]
  ],
  'mosaico_custom_toolbar' => [
    'group_name' => 'Mosaico Preferences',
    'group' => 'mosaico',
    'name' => 'mosaico_custom_toolbar',
    'type' => 'String',
    'html_type' => 'text',
    'html_attributes' => [
      'class' => 'huge40',
    ],
    'default' => CIVICRM_MOSAICO_CUSTOM_TOOLBAR,
    'add' => '4.7',
    'title' => ts('Mosaico Toolbar Settings'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Tool sets name are separated by space, use | symbol for grouping of tool set.',
    'help_text' => NULL,
    'settings_pages' => ['mosaico' => ['weight' => 47]]
  ],
);

