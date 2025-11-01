<?php

namespace Drupal\Tests\advanced_country_field\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests module installation and basic configuration.
 *
 * @group advanced_country_field
 */
class ModuleInstallationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['advanced_country_field'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests module installation.
   */
  public function testModuleInstallation() {
    // Module should be enabled.
    $this->assertTrue($this->container->get('module_handler')->moduleExists('advanced_country_field'));

    // Service should be available.
    $this->assertNotNull($this->container->get('advanced_country_field.country_data'));

    // Configuration should exist.
    $this->assertNotNull($this->config('advanced_country_field.settings'));
  }

  /**
   * Tests settings page access and form.
   */
  public function testSettingsPage() {
    // Create an admin user.
    $admin_user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($admin_user);

    // Access the settings page.
    $this->drupalGet('/admin/config/regional/advanced-country-field');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Advanced Country Field Settings');

    // Verify form elements are present.
    $this->assertSession()->fieldExists('Default Value Format');
    $this->assertSession()->fieldExists('Language Handling');
    $this->assertSession()->fieldExists('Enable country filtering');
    $this->assertSession()->fieldExists('Flag SVG Library Path');

    // Submit the form with different values.
    $edit = [
      'default_value_format' => 'name',
      'language_handling' => 'native',
      'filter_enabled' => 1,
      'flag_library_path' => '/custom/flags/',
    ];
    $this->drupalGet('/admin/config/regional/advanced-country-field');
    $this->submitForm($edit, 'Save configuration');

    // Verify values were saved.
    $this->assertSession()->responseContains('The configuration options have been saved.');
    $config = $this->config('advanced_country_field.settings');
    $this->assertEquals('name', $config->get('default_value_format'));
    $this->assertEquals('native', $config->get('language_handling'));
    $this->assertTrue($config->get('filter_enabled'));
    $this->assertEquals('/custom/flags/', $config->get('flag_library_path'));
  }

  /**
   * Tests country filter page access.
   */
  public function testCountryFilterPage() {
    // Create an admin user.
    $admin_user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($admin_user);

    // Enable country filtering first.
    $config = $this->config('advanced_country_field.settings');
    $config->set('filter_enabled', TRUE)->save();

    // Access the country filter page.
    $this->drupalGet('/admin/config/regional/advanced-country-field/countries');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Configure Countries');
  }

  /**
   * Tests custom country page access.
   */
  public function testCustomCountryPage() {
    // Create an admin user.
    $admin_user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($admin_user);

    // Access the custom country page.
    $this->drupalGet('/admin/config/regional/advanced-country-field/custom');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Custom Countries');
  }

  /**
   * Tests permissions.
   */
  public function testPermissions() {
    // Create a user without permissions.
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);

    // Should not be able to access settings.
    $this->drupalGet('/admin/config/regional/advanced-country-field');
    $this->assertSession()->statusCodeEquals(403);
  }

}

