<?php

namespace Drupal\advanced_country_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an item list class for advanced country field items.
 */
class AdvancedCountryFieldItemList extends FieldItemList {

  /**
   * {@inheritdoc}
   */
  public function defaultValuesForm(array &$form, FormStateInterface $form_state) {
    // Default values are handled by the widget.
    return [];
  }

}

