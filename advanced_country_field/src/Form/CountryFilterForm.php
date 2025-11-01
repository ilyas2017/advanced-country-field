<?php

namespace Drupal\advanced_country_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\advanced_country_field\Service\CountryDataService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for filtering available countries.
 */
class CountryFilterForm extends ConfigFormBase {

  /**
   * The country data service.
   *
   * @var \Drupal\advanced_country_field\Service\CountryDataService
   */
  protected $countryDataService;

  /**
   * Constructs a CountryFilterForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typed_config_manager
   *   The typed config manager.
   * @param \Drupal\advanced_country_field\Service\CountryDataService $country_data_service
   *   The country data service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TypedConfigManagerInterface $typed_config_manager, CountryDataService $country_data_service) {
    parent::__construct($config_factory, $typed_config_manager);
    $this->countryDataService = $country_data_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('advanced_country_field.country_data')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advanced_country_field.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advanced_country_field_country_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('advanced_country_field.settings');
    $enabled_countries = $config->get('enabled_countries') ?? [];
    $all_countries = $this->countryDataService->getCountries(FALSE);

    $form['description'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Select which countries should be available in Advanced Country Fields. Leave all unchecked to allow all countries.') . '</p>',
    ];

    // Attach admin CSS and JavaScript.
    $form['#attached']['library'][] = 'advanced_country_field/admin';

    // Search bar with client-side filtering (like modules page).
    $form['filters'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['table-filter', 'js-show'],
      ],
    ];

    $form['filters']['text'] = [
      '#type' => 'search',
      '#title' => $this->t('Search countries'),
      '#title_display' => 'invisible',
      '#size' => 30,
      '#placeholder' => $this->t('Search countries...'),
      '#attributes' => [
        'class' => ['table-filter-text', 'country-filter-text'],
        'autocomplete' => 'off',
      ],
    ];

    $form['buttons'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['country-filter-actions']],
    ];

    $form['buttons']['select_all'] = [
      '#type' => 'button',
      '#value' => $this->t('Select All'),
      '#attributes' => [
        'onclick' => "jQuery('input.country-checkbox').prop('checked', true); return false;",
      ],
    ];

    $form['buttons']['deselect_all'] = [
      '#type' => 'button',
      '#value' => $this->t('Deselect All'),
      '#attributes' => [
        'onclick' => "jQuery('input.country-checkbox').prop('checked', false); return false;",
      ],
    ];

    // Wrapper for countries list.
    $form['countries_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['country-list-container']],
      '#tree' => TRUE,
    ];

    // Build country checkboxes (show all, filtering happens on client-side).
    foreach ($all_countries as $code => $name) {
      $checked = empty($enabled_countries) || in_array($code, $enabled_countries);
      
      $form['countries_wrapper']['country_' . $code] = [
        '#type' => 'checkbox',
        '#title' => $name . ' (' . $code . ')',
        '#default_value' => $checked,
        '#attributes' => [
          'class' => ['country-checkbox'],
          'data-country-code' => $code,
        ],
        '#wrapper_attributes' => [
          'class' => ['country-item', 'table-filter-text-source'],
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $enabled = [];
    
    // Get values from countries_wrapper
    $countries_values = $form_state->getValue('countries_wrapper', []);
    
    // Process checked countries
    foreach ($countries_values as $key => $value) {
      if (is_string($key) && strpos($key, 'country_') === 0 && $value) {
        // Extract country code from key like "country_US"
        $code = str_replace('country_', '', $key);
        $enabled[] = $code;
      }
    }

    $this->config('advanced_country_field.settings')
      ->set('enabled_countries', $enabled)
      ->save();

    parent::submitForm($form, $form_state);
  }

}


