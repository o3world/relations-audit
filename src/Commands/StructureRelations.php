<?php

namespace Drupal\relations_audit\Commands;

use Drush\Commands\DrushCommands;

/**
 * Export relations structure
 */
class StructureRelations extends DrushCommands {

  const COLUMN_HEADERS = [
    'EntityType',
    'BundleName',
    'Bundle',
    'FieldName',
    'TargetEntityType',
    'TargetBundleName',
    'TargetBundle',
  ];


  /**
   * Export relations structure
   *
   * @command relations_audit:export-relations-structure
   * @options base_entity_type The base entity type
   * @options target_entity_type The target entity type
   * @options base_bundle The base entity bundle
   * @options target_bundle The target entity bundle
   * @aliases export-relations-structure,ers,export-rs
   * @usage drush export-rs
   */
  public function export($options) {
    $manifest = $this->getDataArray($options);
    $manifest = array_merge([
      self::COLUMN_HEADERS
    ], $manifest);
    $file = fopen('php://output', 'w');
    foreach ($manifest as $lines) {
      fputcsv($file, array_values($lines));
    }
    fclose($file);
  }

  /**
   * Get data array
   *
   * @param array $options
   * @return array $manifest
   */
  public function getDataArray($options) {
    $manifest = [];
    // Get all bundle info for reference.
    $all_bundle_info = \Drupal::service("entity_type.bundle.info")->getAllBundleInfo();
    // Loop through all entity types.
    foreach (\Drupal::entityTypeManager()->getDefinitions() as $entity_definition) {
      // Check for option: base_bundle.
      if ($options['base_entity_type'] && $entity_definition->id() !== $options['base_entity_type']) {
        continue;
      }
      // Loop through bundles of the entity types
      foreach (\Drupal::service('entity_type.bundle.info')->getBundleInfo($entity_definition->id()) as $bundle => $bundle_data) {
        // Check for option: base_bundle.
        if ($options['base_bundle'] && $bundle !== $options['base_bundle']) {
          continue;
        }
        // Only select entities that are fieldable.
        if (in_array('Drupal\Core\Entity\FieldableEntityInterface', class_implements($entity_definition->getOriginalClass()))) {
          $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_definition->id(), $bundle);
          foreach ($field_definitions as $field_definition) {
            $settings = $field_definition->getSettings();
            if (in_array($field_definition->getType(), ['entity_reference' , 'entity_reference_revisions']) &&
              !empty($settings['handler_settings']['target_bundles'])) {
                foreach ($settings['handler_settings']['target_bundles'] as $target_bundle) {
                  // Check for option: target_type & target_bundle.
                  if (
                    ($options['target_entity_type'] && $settings['target_type'] !== $options['target_entity_type']) ||
                    ($options['target_bundle'] && $target_bundle !== $options['target_bundle'])
                  ) {
                    continue;
                  }
                  $manifest[] = array_combine(self::COLUMN_HEADERS, [
                    $field_definition->getTargetEntityTypeId(),
                    \Drupal::service('entity_type.bundle.info')->getBundleInfo($field_definition->getTargetEntityTypeId())[$field_definition->getTargetBundle()]['label'],
                    $field_definition->getTargetBundle(),
                    $field_definition->getName(),
                    $settings['target_type'],
                    $all_bundle_info[$settings['target_type']][$target_bundle]['label'],
                    $target_bundle,
                  ]);
                }
            }
          };
        }
      }
    }
    // Sort if necessary.
    if ($options['sort_by'] && in_array($options['sort_by'], self::COLUMN_HEADERS)) {
      $sort_by = array_column($manifest, $options['sort_by']);
      array_multisort($sort_by, SORT_ASC, $manifest);
    }
    return $manifest;
  }

}
