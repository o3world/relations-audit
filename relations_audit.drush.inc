<?php

/**
 * @file
 * Drush (< 9) integration.
 */

/**
 * Implements hook_drush_command().
 */
function relations_audit_drush_command() {
  $items['export-relations-structure'] = [
    'description' => 'Migrate the full width CTA from under content sections to the full width CTA field.',
    'callback' => 'relations_audit_export_relations_structure',
    'options' => [
      'base_entity_type' => 'The base entity type.',
      'target_entity_type' => 'The target entity type.',
      'base_bundle' => 'The base entity bundle.',
      'target_bundle' => 'The target entity bundle.'
    ],
    'drupal dependencies' => ['relations_audit'],
    'aliases' => ['ers', 'export-rs'],
  ];

  return $items;
}

/**
 * Callback function for hook_drush_command().
 */
function relations_audit_export_relations_structure() {
  $options = [
    'base_entity_type' => drush_get_option('base_entity_type', FALSE),
    'target_entity_type' => drush_get_option('target_entity_type', FALSE),
    'base_bundle' => drush_get_option('base_bundle', FALSE),
    'target_bundle' => drush_get_option('target_bundle', FALSE)
  ];
  \Drupal::service('relations_audit.structure')->export($options);
}
