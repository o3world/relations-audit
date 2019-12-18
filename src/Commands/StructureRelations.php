<?php

namespace Drupal\relations_audit\Commands;

use Drush\Commands\DrushCommands;

/**
 * Migrates the full width CTA from under content sections to the full width CTA fields
 */
class StructureRelations extends DrushCommands {


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
                  $manifest[] = [
                    'entity_type' => $field_definition->getTargetEntityTypeId(),
                    'bundle_name' => \Drupal::service('entity_type.bundle.info')->getBundleInfo($field_definition->getTargetEntityTypeId())[$field_definition->getTargetBundle()]['label'],
                    'bundle' => $field_definition->getTargetBundle(),
                    'field_name' => $field_definition->getName(),
                    'target_entity_type' => $settings['target_type'],
                    'target_bundle_label' => $all_bundle_info[$settings['target_type']][$target_bundle]['label'],
                    'target_bundle' => $target_bundle,
                  ];
                }
            }
          };
        }
      }
    }
    $file = fopen('php://output', 'w');
    // Write header.
    fputcsv($file, [
      'EntityType',
      'ComponentName',
      'Bundle',
      'FieldName',
      'TargetEntityType',
      'TargetName',
      'TargetBundle',
    ]);
    foreach ($manifest as $lines) {
      fputcsv($file, array_values($lines));
    }
    fclose($file);
  }

}
