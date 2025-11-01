<?php

namespace Drupal\advanced_country_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\advanced_country_field\Service\CountryDataService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'advanced_country_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "advanced_country_field_formatter",
 *   label = @Translation("Advanced Country Field"),
 *   field_types = {
 *     "advanced_country_field"
 *   }
 * )
 */
class AdvancedCountryFieldFormatter extends FormatterBase {

  /**
   * The country data service.
   *
   * @var \Drupal\advanced_country_field\Service\CountryDataService
   */
  protected $countryDataService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->countryDataService = $container->get('advanced_country_field.country_data');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'display_format' => 'name',
      'show_flag' => FALSE,
      'flag_position' => 'before',
      'flag_width' => '20px',
      'flag_height' => '15px',
      'link_to_country' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['display_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Display Format'),
      '#options' => [
        'code' => $this->t('Country code (e.g., "US")'),
        'name' => $this->t('Country name (e.g., "United States")'),
        'both' => $this->t('Both code and name (e.g., "US - United States")'),
      ],
      '#default_value' => $this->getSetting('display_format'),
    ];

    $element['show_flag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show country flag'),
      '#default_value' => $this->getSetting('show_flag'),
    ];

    $element['flag_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Flag Position'),
      '#options' => [
        'before' => $this->t('Before country name'),
        'after' => $this->t('After country name'),
        'only' => $this->t('Flag only'),
      ],
      '#default_value' => $this->getSetting('flag_position'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][show_flag]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Get default values with backward compatibility for flag_size.
    $flag_width_default = trim($this->getSetting('flag_width') ?? '');
    $flag_height_default = trim($this->getSetting('flag_height') ?? '');
    
    // Migrate from old flag_size if needed.
    if ($flag_width_default === '' || $flag_height_default === '') {
      $legacy_size = trim($this->getSetting('flag_size') ?? '');
      if ($legacy_size !== '') {
        if ($flag_width_default === '') {
          $flag_width_default = $legacy_size;
        }
        if ($flag_height_default === '') {
          $flag_height_default = $legacy_size;
        }
      }
    }
    
    // Final defaults.
    if ($flag_width_default === '') {
      $flag_width_default = '20px';
    }
    if ($flag_height_default === '') {
      $flag_height_default = '15px';
    }

    $element['flag_width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flag Width'),
      '#description' => $this->t('CSS width value (e.g., "20px", "1.5rem", "100%", "5vh"). Accepts all CSS units: px, rem, em, %, vh, vw, etc.'),
      '#default_value' => $flag_width_default,
      '#size' => 12,
      '#maxlength' => 20,
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][show_flag]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $element['flag_height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flag Height'),
      '#description' => $this->t('CSS height value (e.g., "15px", "1rem", "auto", "3vh"). Accepts all CSS units: px, rem, em, %, vh, vw, auto, etc.'),
      '#default_value' => $flag_height_default,
      '#size' => 12,
      '#maxlength' => 20,
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][show_flag]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $display_formats = [
      'code' => $this->t('Code'),
      'name' => $this->t('Name'),
      'both' => $this->t('Both'),
    ];
    $summary[] = $this->t('Display: @format', ['@format' => $display_formats[$this->getSetting('display_format')] ?? '']);

    if ($this->getSetting('show_flag')) {
      $flag_positions = [
        'before' => $this->t('Before'),
        'after' => $this->t('After'),
        'only' => $this->t('Only'),
      ];
      
      // Get width and height values with fallbacks.
      $flag_width = trim($this->getSetting('flag_width') ?? '');
      $flag_height = trim($this->getSetting('flag_height') ?? '');
      
      // Check legacy flag_size if needed.
      if ($flag_width === '' || $flag_height === '') {
        $legacy_size = trim($this->getSetting('flag_size') ?? '');
        if ($legacy_size !== '') {
          if ($flag_width === '') {
            $flag_width = $legacy_size;
          }
          if ($flag_height === '') {
            $flag_height = $legacy_size;
          }
        }
      }
      
      // Final defaults for display.
      if ($flag_width === '') {
        $flag_width = '20px';
      }
      if ($flag_height === '') {
        $flag_height = '15px';
      }
      
      $summary[] = $this->t('Flag: @position (W: @width, H: @height)', [
        '@position' => $flag_positions[$this->getSetting('flag_position')] ?? '',
        '@width' => $flag_width,
        '@height' => $flag_height,
      ]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $show_flag = $this->getSetting('show_flag');
    $display_format = $this->getSetting('display_format');
    $flag_position = $this->getSetting('flag_position');
    
    // Get width and height, with backward compatibility for old flag_size setting.
    $flag_width = trim($this->getSetting('flag_width') ?? '');
    $flag_height = trim($this->getSetting('flag_height') ?? '');
    
    // Backward compatibility: if flag_size exists but width/height don't, use flag_size for both.
    if ($flag_width === '' || $flag_height === '') {
      $legacy_size = trim($this->getSetting('flag_size') ?? '');
      if ($legacy_size !== '') {
        if ($flag_width === '') {
          $flag_width = $legacy_size;
        }
        if ($flag_height === '') {
          $flag_height = $legacy_size;
        }
      }
    }
    
    // Ensure we have defaults if still empty.
    if ($flag_width === '') {
      $flag_width = '20px';
    }
    if ($flag_height === '') {
      $flag_height = '15px';
    }

    foreach ($items as $delta => $item) {
      $code = $item->country_code ?? '';
      if (empty($code)) {
        continue;
      }

      $name = $item->country_name ?? $this->countryDataService->getCountryName($code);
      
      // Build the display value based on format.
      $display_value = '';
      switch ($display_format) {
        case 'code':
          $display_value = $code;
          break;
        case 'name':
          $display_value = $name;
          break;
        case 'both':
          $display_value = $code . ' - ' . $name;
          break;
      }

      // Build flag HTML if enabled.
      $flag_html = '';
      if ($show_flag) {
        $flag_path = $this->countryDataService->getFlagPath($code);
        // Sanitize width and height values to prevent XSS while allowing CSS units.
        // Ensure values are not empty and trim whitespace.
        $safe_width = trim($flag_width);
        $safe_height = trim($flag_height);
        
        // Fallback to defaults if somehow still empty after processing.
        if ($safe_width === '') {
          $safe_width = '20px';
        }
        if ($safe_height === '') {
          $safe_height = '15px';
        }
        
        // Escape for HTML output.
        $safe_width = htmlspecialchars($safe_width, ENT_QUOTES, 'UTF-8');
        $safe_height = htmlspecialchars($safe_height, ENT_QUOTES, 'UTF-8');
        
        $flag_html = '<img src="' . htmlspecialchars($flag_path, ENT_QUOTES) . '" alt="' . htmlspecialchars($name, ENT_QUOTES) . '" class="country-flag" style="width: ' . $safe_width . '; height: ' . $safe_height . '; vertical-align: middle;" />';
      }

      // Combine flag and value based on position.
      $content = '';
      if ($show_flag) {
        switch ($flag_position) {
          case 'before':
            $content = $flag_html . ' ' . $display_value;
            break;
          case 'after':
            $content = $display_value . ' ' . $flag_html;
            break;
          case 'only':
            $content = $flag_html;
            break;
        }
      }
      else {
        $content = $display_value;
      }

      $elements[$delta] = [
        '#type' => 'markup',
        '#markup' => $content,
        '#attributes' => [
          'class' => ['advanced-country-field-display', 'country-' . strtolower($code)],
        ],
      ];

      // Attach flags library if enabled.
      if ($show_flag) {
        $elements[$delta]['#attached']['library'][] = 'advanced_country_field/flags';
      }
    }

    // Add a single style block for all flags in this field with the configured dimensions.
    // This ensures the width/height settings are applied even if inline styles are filtered.
    if ($show_flag && !empty($elements)) {
      $safe_width_css = htmlspecialchars(trim($flag_width), ENT_QUOTES, 'UTF-8');
      $safe_height_css = htmlspecialchars(trim($flag_height), ENT_QUOTES, 'UTF-8');
      
      // Use a unique key based on field name to avoid duplicates.
      $field_name = $this->fieldDefinition->getName();
      $style_key = 'advanced_country_field_flag_size_' . $field_name;
      
      // Add CSS rule to set flag dimensions from formatter settings.
      $elements['#attached']['html_head'][] = [
        [
          '#type' => 'html_tag',
          '#tag' => 'style',
          '#value' => '.field--name-' . str_replace('_', '-', $field_name) . ' .country-flag, .advanced-country-field-display .country-flag { width: ' . $safe_width_css . '; height: ' . $safe_height_css . '; }',
        ],
        $style_key,
      ];
    }

    return $elements;
  }

}

