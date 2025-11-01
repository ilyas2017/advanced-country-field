<?php

namespace Drupal\advanced_country_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for adding custom countries.
 */
class CustomCountryForm extends ConfigFormBase {

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
    return 'advanced_country_field_custom_country_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('advanced_country_field.settings');
    $custom_countries = $config->get('custom_countries') ?? [];

    $form['description'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Add custom countries or regions that are not in the standard ISO 3166-1 list.') . '</p>',
    ];

    $form['custom_countries'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Code'),
        $this->t('Name'),
        $this->t('Operations'),
      ],
      '#empty' => $this->t('No custom countries added yet.'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'custom-country-weight',
        ],
      ],
    ];

    foreach ($custom_countries as $delta => $country) {
      $form['custom_countries'][$delta]['#attributes']['class'][] = 'draggable';
      
      $form['custom_countries'][$delta]['code'] = [
        '#type' => 'textfield',
        '#size' => 5,
        '#maxlength' => 5,
        '#default_value' => $country['code'] ?? '',
        '#required' => TRUE,
      ];
      
      $form['custom_countries'][$delta]['name'] = [
        '#type' => 'textfield',
        '#size' => 30,
        '#default_value' => $country['name'] ?? '',
        '#required' => TRUE,
      ];
      
      $form['custom_countries'][$delta]['remove'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove'),
        '#name' => 'remove_' . $delta,
        '#submit' => [[$this, 'removeCustomCountry']],
        '#ajax' => [
          'callback' => '::ajaxRemoveCountry',
          'wrapper' => 'custom-countries-wrapper',
        ],
      ];
      
      $form['custom_countries'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#default_value' => $delta,
        '#delta' => 10,
        '#attributes' => ['class' => ['custom-country-weight']],
      ];
    }

    $form['add_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Custom Country'),
      '#submit' => [[$this, 'addCustomCountry']],
      '#ajax' => [
        'callback' => '::ajaxAddCountry',
        'wrapper' => 'custom-countries-wrapper',
      ],
    ];

    $form['#prefix'] = '<div id="custom-countries-wrapper">';
    $form['#suffix'] = '</div>';

    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit handler for adding a custom country.
   */
  public function addCustomCountry(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  /**
   * Submit handler for removing a custom country.
   */
  public function removeCustomCountry(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $delta = str_replace('remove_', '', $trigger['#name']);
    $form_state->setRebuild();
    
    $config = $this->config('advanced_country_field.settings');
    $custom_countries = $config->get('custom_countries') ?? [];
    unset($custom_countries[$delta]);
    $config->set('custom_countries', array_values($custom_countries))->save();
  }

  /**
   * Ajax callback for adding country.
   */
  public function ajaxAddCountry(array &$form, FormStateInterface $form_state) {
    return $form['custom_countries'];
  }

  /**
   * Ajax callback for removing country.
   */
  public function ajaxRemoveCountry(array &$form, FormStateInterface $form_state) {
    return $form['custom_countries'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $custom_countries = [];
    $values = $form_state->getValue('custom_countries', []);
    
    foreach ($values as $delta => $country) {
      if (!empty($country['code']) && !empty($country['name'])) {
        $custom_countries[] = [
          'code' => strtoupper($country['code']),
          'name' => $country['name'],
        ];
      }
    }

    $this->config('advanced_country_field.settings')
      ->set('custom_countries', $custom_countries)
      ->save();

    parent::submitForm($form, $form_state);
  }

}

