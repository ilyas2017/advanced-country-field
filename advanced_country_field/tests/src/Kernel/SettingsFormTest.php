<?php

namespace Drupal\Tests\advanced_country_field\Kernel;

use Drupal\advanced_country_field\Form\AdvancedCountryFieldSettingsForm;
use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests for AdvancedCountryFieldSettingsForm.
 *
 * @group advanced_country_field
 */
class SettingsFormTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'config',
    'advanced_country_field',
  ];

  /**
   * The form under test.
   *
   * @var \Drupal\advanced_country_field\Form\AdvancedCountryFieldSettingsForm
   */
  protected $form;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['advanced_country_field']);
    $this->form = $this->container->get('form_builder')->getForm(AdvancedCountryFieldSettingsForm::class);
  }

  /**
   * Tests form building.
   */
  public function testBuildForm() {
    // Form should have required fieldsets.
    $this->assertArrayHasKey('general', $this->form);
    $this->assertArrayHasKey('filtering', $this->form);
    $this->assertArrayHasKey('flags', $this->form);

    // General fieldset should have value format and language handling.
    $this->assertArrayHasKey('default_value_format', $this->form['general']);
    $this->assertArrayHasKey('language_handling', $this->form['general']);

    // Filtering fieldset should have filter_enabled.
    $this->assertArrayHasKey('filter_enabled', $this->form['filtering']);

    // Flags fieldset should have flag_library_path.
    $this->assertArrayHasKey('flag_library_path', $this->form['flags']);
  }

  /**
   * Tests form ID.
   */
  public function testGetFormId() {
    $form = $this->container->get('form_builder')->getForm(AdvancedCountryFieldSettingsForm::class);
    $this->assertEquals('advanced_country_field_settings_form', $form['#form_id']);
  }

  /**
   * Tests form submission.
   */
  public function testSubmitForm() {
    // Create form state with new values.
    $form_state = new FormState();
    $form_state->setValue('default_value_format', 'name');
    $form_state->setValue('language_handling', 'native');
    $form_state->setValue('filter_enabled', TRUE);
    $form_state->setValue('flag_library_path', '/custom/flags/');

    // Create and submit the form.
    $form = $this->container->get('form_builder')->getForm(AdvancedCountryFieldSettingsForm::class);
    $form_builder = $this->container->get('form_builder');
    $form_builder->submitForm(AdvancedCountryFieldSettingsForm::class, $form_state);

    // Check that values were saved.
    $config = $this->config('advanced_country_field.settings');
    $this->assertEquals('name', $config->get('default_value_format'));
    $this->assertEquals('native', $config->get('language_handling'));
    $this->assertTrue($config->get('filter_enabled'));
    $this->assertEquals('/custom/flags/', $config->get('flag_library_path'));
  }

}

