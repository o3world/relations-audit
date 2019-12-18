<?php

namespace Drupal\relations_audit\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StructureRelations.
 */
class StructureRelations extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'structure_relations';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['download_node_structure_csv'] = [
      '#type' => 'submit',
      '#value' => $this->t('Download Relations Structure CSV'),
      '#weight' => '0',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="relations-structure.csv";');
    \Drupal::service('relations_audit.structure')->export([], FALSE);
    exit();
  }

}
