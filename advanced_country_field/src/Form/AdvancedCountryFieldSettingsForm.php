<?php

namespace Drupal\advanced_country_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides settings form for Advanced Country Field.
 */
class AdvancedCountryFieldSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advanced_country_field.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advanced_country_field_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('advanced_country_field.settings');

    $form['general'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General Settings'),
    ];

    $form['general']['default_value_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Default Value Format'),
      '#description' => $this->t('Default format for storing country values.'),
      '#options' => [
        'code' => $this->t('Country code only'),
        'name' => $this->t('Country name only'),
        'both' => $this->t('Both code and name'),
      ],
      '#default_value' => $config->get('default_value_format') ?? 'code',
    ];

    $form['general']['language_handling'] = [
      '#type' => 'select',
      '#title' => $this->t('Language Handling'),
      '#description' => $this->t('How to handle country name localization.'),
      '#options' => [
        'site' => $this->t('Use site language'),
        'native' => $this->t('Use native country name'),
        'fallback' => $this->t('Fallback to English if translation unavailable'),
      ],
      '#default_value' => $config->get('language_handling') ?? 'site',
    ];

    $form['filtering'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Country Filtering'),
    ];

    $form['filtering']['filter_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable country filtering'),
      '#description' => $this->t('Allow administrators to restrict which countries appear in fields.'),
      '#default_value' => $config->get('filter_enabled') ?? FALSE,
    ];

    $form['filtering']['info'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Configure which countries are available in <a href="@url">country filter settings</a>.', ['@url' => '/admin/config/regional/advanced-country-field/countries']) . '</p>',
    ];

    $form['flags'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Flag Settings'),
    ];

    $form['flags']['flag_library_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flag SVG Library Path'),
      '#description' => $this->t('Path to the SVG flag library. Default uses country-flag-icons (3x2): e.g., /libraries/country-flag-icons/3x2/. You can also use /libraries/country-flag-icons/1x1/ or point to /sites/default/files/flags/svg/.'),
      '#default_value' => $config->get('flag_library_path') ?? '/libraries/country-flag-icons/3x2/',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('advanced_country_field.settings')
      ->set('default_value_format', $form_state->getValue('default_value_format'))
      ->set('language_handling', $form_state->getValue('language_handling'))
      ->set('filter_enabled', $form_state->getValue('filter_enabled'))
      ->set('flag_library_path', $form_state->getValue('flag_library_path'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}

