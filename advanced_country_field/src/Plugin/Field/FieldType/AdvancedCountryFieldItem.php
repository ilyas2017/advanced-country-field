<?php

namespace Drupal\advanced_country_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'advanced_country_field' field type.
 *
 * @FieldType(
 *   id = "advanced_country_field",
 *   label = @Translation("Advanced Country"),
 *   description = @Translation("An advanced country field with flags, filtering, and custom options."),
 *   default_widget = "advanced_country_field_widget",
 *   default_formatter = "advanced_country_field_formatter",
 *   list_class = "\Drupal\advanced_country_field\Plugin\Field\FieldType\AdvancedCountryFieldItemList"
 * )
 */
class AdvancedCountryFieldItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['country_code'] = DataDefinition::create('string')
      ->setLabel(t('Country Code'))
      ->setDescription(t('The ISO 3166-1 alpha-2 country code.'))
      ->setRequired(TRUE);

    $properties['country_name'] = DataDefinition::create('string')
      ->setLabel(t('Country Name'))
      ->setDescription(t('The country name (localized if available).'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'country_code' => [
          'type' => 'varchar',
          'length' => 2,
          'not null' => TRUE,
        ],
        'country_name' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
      ],
      'indexes' => [
        'country_code' => ['country_code'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('country_code')->getValue();
    return empty($value);
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'country_code';
  }

}

