<?php

/**
 * @file
 * This file declares a managed database record of type "Job".
 */

return [
  [
    'name' => 'ArchiveMailing Job',
    'entity' => 'Job',
    'params' => [
      'version' => 3,
      'name' => 'ArchiveMailing Job',
      'description' => "Automatically archive mailings after a certain number of days.",
      'run_frequency' => 'Daily',
      'api_entity' => 'Job',
      'api_action' => 'archivemailing',
      'parameters' => 'days=365',
      'is_active' => 0,
    ],
    'update' => 'never',
  ],
];
