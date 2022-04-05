<?php

/**
 * Implements hook_views_data().
 */
function lynx_client_views_data() {
  $data = [];
  // Base data.
  $data['lynx_client']['table']['group'] = t('Lynx server content');
  $data['lynx_client']['table']['base'] = [
    'title' => t('Lynx Client Connector'),
    'help' => t('Lynx server content provided by the Server API.'),
    'query_id' => 'lynxclient',
  ];

  // Fields.
  $data['lynx_client']['id'] = [
    'title' => t('ID'),
    'help' => t('Server content node ID.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];
  $data['lynx_client']['title'] = [
    'title' => t('Title'),
    'help' => t('Server content node title.'),
    'field' => [
      'id' => 'standard',
    ],
  ];
  $data['lynx_client']['changed'] = [
    'title' => t('Last Changed date'),
    'help' => t('Node last changed date.'),
    'field' => [
      'id' => 'date',
    ],
  ];

  /* @TODO: Could put other fields here like body, image, link, etc. */

  return $data;
}