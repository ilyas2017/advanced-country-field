<?php

namespace Drupal\advanced_country_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\advanced_country_field\Service\CountryDataService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Plugin implementation of the 'advanced_country_field_widget' widget.
 *
 * @FieldWidget(
 *   id = "advanced_country_field_widget",
 *   label = @Translation("Advanced Country Field"),
 *   field_types = {
 *     "advanced_country_field"
 *   },
 *   multiple_values = TRUE
 * )
 */
class AdvancedCountryFieldWidget extends WidgetBase {

  /**
   * The country data service.
   *
   * @var \Drupal\advanced_country_field\Service\CountryDataService
   */
  protected $countryDataService;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->countryDataService = $container->get('advanced_country_field.country_data');
    $instance->configFactory = $container->get('config.factory');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'widget_type' => 'select', // Will be adjusted based on cardinality in settingsForm()
      'show_flags' => FALSE,
      'flag_position' => 'before',
      'placeholder' => 'Select a country...',
      'enable_search' => FALSE,
      'allow_custom' => FALSE,
      'value_format' => 'code',
    ] + parent::defaultSettings();
  }
  
  /**
   * {@inheritdoc}
   */
  public function getSettings() {
    $settings = parent::getSettings();
    
    // Validate widget type based on current field cardinality.
    // Only validate if field definition is available (not during construction).
    if ($this->fieldDefinition) {
      try {
        $field_storage = $this->fieldDefinition->getFieldStorageDefinition();
        $cardinality = $field_storage->getCardinality();
        $available_widgets = array_keys($this->getAvailableWidgetTypes($cardinality));
        
        if (!in_array($settings['widget_type'], $available_widgets)) {
          // Reset to default if incompatible (but don't persist - let settingsForm handle it).
          $settings['widget_type'] = $this->getDefaultWidgetType($cardinality);
        }
      }
      catch (\Exception $e) {
        // If field storage is not available yet, skip validation.
        // This can happen during field creation.
      }
    }
    
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    // Get field storage cardinality.
    $field_storage = $this->fieldDefinition->getFieldStorageDefinition();
    $cardinality = $field_storage->getCardinality();

    // Get available widget types based on cardinality.
    $available_widgets = $this->getAvailableWidgetTypes($cardinality);
    $current_widget_type = $this->getSetting('widget_type');

    // Validate current widget type against cardinality.
    if (!isset($available_widgets[$current_widget_type])) {
      // Reset to default if current widget is incompatible.
      $default_widget = $this->getDefaultWidgetType($cardinality);
      $current_widget_type = $default_widget;
      $this->setSetting('widget_type', $default_widget);
    }

    // Build description based on cardinality.
    $description = $this->t('Choose how the country field should be displayed.');
    if ($cardinality == 1) {
      $description .= ' ' . $this->t('Single value field: only single-selection widgets are available.');
    }
    elseif ($cardinality > 1) {
      $description .= ' ' . $this->t('Multiple values field (max @count): only multi-selection widgets are available.', ['@count' => $cardinality]);
    }
    else {
      $description .= ' ' . $this->t('Unlimited values field: all widget types are available.');
    }

    $element['widget_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Widget Type'),
      '#description' => $description,
      '#options' => $available_widgets,
      '#default_value' => $current_widget_type,
      '#required' => TRUE,
    ];

    $element['show_flags'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show country flags'),
      '#description' => $this->t('Display SVG flags next to country names.'),
      '#default_value' => $this->getSetting('show_flags'),
    ];

    $element['flag_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Flag Position'),
      '#options' => [
        'before' => $this->t('Before country name'),
        'after' => $this->t('After country name'),
        'only' => $this->t('Flag only (hide country name)'),
      ],
      '#default_value' => $this->getSetting('flag_position'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][show_flags]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder text'),
      '#description' => $this->t('Text to display when no country is selected.'),
      '#default_value' => $this->getSetting('placeholder'),
    ];

    $element['enable_search'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable search'),
      '#description' => $this->t('Add a search input at the top of the dropdown to filter countries.'),
      '#default_value' => $this->getSetting('enable_search'),
    ];

    $element['value_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Value Format'),
      '#description' => $this->t('Choose what value should be stored.'),
      '#options' => [
        'code' => $this->t('Country code only (e.g., "US")'),
        'name' => $this->t('Country name only (e.g., "United States")'),
        'both' => $this->t('Both code and name'),
      ],
      '#default_value' => $this->getSetting('value_format'),
      '#required' => TRUE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    
    // Get field storage cardinality.
    $field_storage = $this->fieldDefinition->getFieldStorageDefinition();
    $cardinality = $field_storage->getCardinality();
    
    // Add cardinality info.
    if ($cardinality == 1) {
      $summary[] = $this->t('Cardinality: Single value');
    }
    elseif ($cardinality > 1) {
      $summary[] = $this->t('Cardinality: Multiple values (max @count)', ['@count' => $cardinality]);
    }
    else {
      $summary[] = $this->t('Cardinality: Unlimited');
    }
    
    $widget_type = $this->getSetting('widget_type');
    $widget_types = [
      'select' => $this->t('Dropdown'),
      'select_multiple' => $this->t('Multi-select'),
      'radios' => $this->t('Radio Buttons'),
      'checkboxes' => $this->t('Checkboxes'),
    ];
    $summary[] = $this->t('Widget type: @type', ['@type' => $widget_types[$widget_type] ?? $widget_type]);

    if ($this->getSetting('show_flags')) {
      $flag_positions = [
        'before' => $this->t('Before'),
        'after' => $this->t('After'),
        'only' => $this->t('Only'),
      ];
      $summary[] = $this->t('Flags: @position', ['@position' => $flag_positions[$this->getSetting('flag_position')] ?? '']);
    }

    if ($this->getSetting('enable_search')) {
      $summary[] = $this->t('Search: Enabled');
    }

    $value_formats = [
      'code' => $this->t('Code'),
      'name' => $this->t('Name'),
      'both' => $this->t('Both'),
    ];
    $summary[] = $this->t('Value format: @format', ['@format' => $value_formats[$this->getSetting('value_format')] ?? '']);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $countries = $this->countryDataService->getCountries();
    $widget_type = $this->getSetting('widget_type');
    $is_multiple = in_array($widget_type, ['select_multiple', 'checkboxes']);

    // For multi-select widgets, get all selected values from all items.
    // For single-select widgets, get the value for the current delta.
    if ($is_multiple) {
      $default_value = [];
      foreach ($items as $item) {
        if (!empty($item->country_code)) {
          $default_value[] = $item->country_code;
        }
      }
      // For multi-select, create element only at delta 0 to avoid duplication.
      if ($delta > 0) {
        return [];
      }
    }
    else {
      $default_value = $items[$delta]->country_code ?? '';
    }

    $element += [
      '#type' => $this->getWidgetType(),
      '#title' => $element['#title'],
      '#description' => $element['#description'],
      '#default_value' => $default_value,
      '#options' => $this->buildCountryOptions($countries),
      '#empty_option' => $this->getSetting('placeholder') ?: t('Select a country...'),
      '#attributes' => [
        'class' => ['advanced-country-field-widget'],
        'data-show-flags' => $this->getSetting('show_flags') ? '1' : '0',
        'data-flag-position' => $this->getSetting('flag_position'),
        'data-widget-type' => $widget_type,
        'data-placeholder' => $this->getSetting('placeholder') ?: t('Select a country...'),
        'data-enable-search' => $this->getSetting('enable_search') ? '1' : '0',
      ],
    ];

    // Add multiple selection for multi-select widgets.
    if ($is_multiple) {
      $element['#multiple'] = TRUE;
    }

    // Add flags library if enabled.
    if ($this->getSetting('show_flags')) {
      $element['#attached']['library'][] = 'advanced_country_field/flags';
      $element['#attached']['library'][] = 'advanced_country_field/widget';
      
      // Get flag base path and add it as a data attribute.
      $config = $this->configFactory->get('advanced_country_field.settings');
      $flag_base_path = $config->get('flag_library_path') ?? '/libraries/country-flag-icons/3x2/';
      $element['#attributes']['data-flag-base-path'] = $flag_base_path;
      
      // Also pass to drupalSettings for backward compatibility.
      if (!isset($element['#attached']['drupalSettings']['advancedCountryField'])) {
        $element['#attached']['drupalSettings']['advancedCountryField'] = [];
      }
      
      $field_id = $this->fieldDefinition->getName();
      $element['#attached']['drupalSettings']['advancedCountryField']['flags'][$field_id] = [
        'flag_base_path' => $flag_base_path,
        'flag_position' => $this->getSetting('flag_position'),
      ];
    }

    return $element;
  }

  /**
   * Gets available widget types based on field cardinality.
   *
   * @param int $cardinality
   *   The field storage cardinality (1 for single, >1 for multiple, -1 for unlimited).
   *
   * @return array
   *   Array of widget type options keyed by widget type ID.
   */
  protected function getAvailableWidgetTypes($cardinality) {
    $all_widgets = [
      'select' => $this->t('Dropdown (Select)'),
      'select_multiple' => $this->t('Multi-select Dropdown'),
      'radios' => $this->t('Radio Buttons'),
      'checkboxes' => $this->t('Checkboxes'),
    ];

    // Single value field: only single-selection widgets.
    if ($cardinality == 1) {
      return [
        'select' => $all_widgets['select'],
        'radios' => $all_widgets['radios'],
      ];
    }

    // Multiple values field (limited): only multi-selection widgets.
    if ($cardinality > 1) {
      return [
        'select_multiple' => $all_widgets['select_multiple'],
        'checkboxes' => $all_widgets['checkboxes'],
      ];
    }

    // Unlimited cardinality: all widgets available.
    return $all_widgets;
  }

  /**
   * Gets the default widget type based on field cardinality.
   *
   * @param int $cardinality
   *   The field storage cardinality (1 for single, >1 for multiple, -1 for unlimited).
   *
   * @return string
   *   The default widget type ID.
   */
  protected function getDefaultWidgetType($cardinality) {
    if ($cardinality == 1) {
      return 'select';
    }
    elseif ($cardinality > 1) {
      return 'select_multiple';
    }
    else {
      // Unlimited: default to select.
      return 'select';
    }
  }

  /**
   * Gets the widget type element.
   *
   * @return string
   *   The form element type.
   */
  protected function getWidgetType() {
    $widget_type = $this->getSetting('widget_type');
    
    switch ($widget_type) {
      case 'select_multiple':
      case 'select':
        return 'select';

      case 'radios':
        return 'radios';

      case 'checkboxes':
        return 'checkboxes';

      default:
        return 'select';
    }
  }

  /**
   * Builds country options with flags if enabled.
   *
   * @param array $countries
   *   Array of country codes => names.
   *
   * @return array
   *   Array of formatted options.
   */
  protected function buildCountryOptions(array $countries) {
    $options = [];
    
    // Always use plain text in options (HTML doesn't render in <option> tags).
    foreach ($countries as $code => $name) {
      $options[$code] = $name;
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues($values, array $form, FormStateInterface $form_state) {
    $widget_type = $this->getSetting('widget_type');
    $is_multiple = in_array($widget_type, ['select_multiple', 'checkboxes']);
    
    // Handle empty values.
    if (empty($values) || (is_array($values) && empty(array_filter($values)))) {
      return [];
    }
    
    // For multi-select widgets, Drupal returns values as a flat array of country codes.
    // For single-select widgets, values come per delta.
    if ($is_multiple) {
      // Multi-select: values should be a flat array of country codes like ['US', 'CA', 'GB'].
      $processed_values = [];
      
      // Normalize: ensure we have an array.
      if (!is_array($values)) {
        $values = $values ? [$values] : [];
      }
      
      // Flatten the array if needed and process each country code.
      foreach ($values as $value) {
        // Skip empty values.
        if (empty($value)) {
          continue;
        }
        
        // Handle different possible structures.
        if (is_array($value)) {
          // If value is an array, it might be nested - extract codes.
          foreach ($value as $code) {
            if (!empty($code) && is_string($code) && strlen($code) <= 3) {
              // Valid country code (2-3 characters).
              $processed_values[] = [
                'country_code' => strtoupper($code),
                'country_name' => $this->countryDataService->getCountryName(strtoupper($code)),
              ];
            }
          }
        }
        elseif (is_string($value) && strlen($value) <= 3) {
          // Direct country code string.
          $processed_values[] = [
            'country_code' => strtoupper($value),
            'country_name' => $this->countryDataService->getCountryName(strtoupper($value)),
          ];
        }
      }
      
      return $processed_values;
    }
    else {
      // Single-select: normalize and process per delta.
      if (!is_array($values)) {
        $values = $values ? [$values] : [];
      }
      
      $processed = [];
      foreach ($values as $delta => $value) {
        if (empty($value)) {
          continue;
        }
        
        // Extract country code from value.
        $code = is_array($value) ? (isset($value['country_code']) ? $value['country_code'] : reset($value)) : $value;
        
        if (!empty($code) && is_string($code)) {
          $processed[$delta] = [
            'country_code' => strtoupper($code),
            'country_name' => $this->countryDataService->getCountryName(strtoupper($code)),
          ];
        }
      }
      
      return $processed;
    }
  }

}

