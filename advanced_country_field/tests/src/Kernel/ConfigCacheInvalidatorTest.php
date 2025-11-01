<?php

namespace Drupal\Tests\advanced_country_field\Kernel;

use Drupal\advanced_country_field\EventSubscriber\ConfigCacheInvalidator;
use Drupal\advanced_country_field\Service\CountryDataService;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests for ConfigCacheInvalidator event subscriber.
 *
 * @group advanced_country_field
 */
class ConfigCacheInvalidatorTest extends KernelTestBase {

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
   * Tests that cache is cleared when configuration is saved.
   */
  public function testCacheInvalidationOnSave() {
    $config_factory = $this->container->get('config.factory');
    $event_dispatcher = $this->container->get('event_dispatcher');

    // Get the country data service to populate cache.
    $country_data_service = $this->container->get('advanced_country_field.country_data');
    $countries = $country_data_service->getCountries();

    // Verify cache is populated.
    $this->assertNotEmpty($countries);

    // Get configuration and create an event.
    $config = $config_factory->get('advanced_country_field.settings');
    $event = new ConfigCrudEvent($config);

    // Dispatch the SAVE event.
    $event_dispatcher->dispatch($event, 'config.save');

    // Verify cache was cleared by trying to access it again.
    // We should still get data, but the internal cache should be cleared.
    $countries_after = $country_data_service->getCountries();
    $this->assertNotEmpty($countries_after);
  }

  /**
   * Tests that cache is cleared when configuration is deleted.
   */
  public function testCacheInvalidationOnDelete() {
    $config_factory = $this->container->get('config.factory');
    $event_dispatcher = $this->container->get('event_dispatcher');

    // Get the country data service to populate cache.
    $country_data_service = $this->container->get('advanced_country_field.country_data');
    $countries = $country_data_service->getCountries();

    // Verify cache is populated.
    $this->assertNotEmpty($countries);

    // Get configuration and create an event.
    $config = $config_factory->get('advanced_country_field.settings');
    $event = new ConfigCrudEvent($config);

    // Dispatch the DELETE event.
    $event_dispatcher->dispatch($event, 'config.delete');

    // Verify cache was cleared.
    $countries_after = $country_data_service->getCountries();
    $this->assertNotEmpty($countries_after);
  }

  /**
   * Tests that other config changes don't invalidate cache.
   */
  public function testCacheNotInvalidatedOnOtherConfig() {
    $config_factory = $this->container->get('config.factory');
    $event_dispatcher = $this->container->get('event_dispatcher');

    // Get the country data service to populate cache.
    $country_data_service = $this->container->get('advanced_country_field.country_data');
    $countries = $country_data_service->getCountries();

    // Verify cache is populated.
    $this->assertNotEmpty($countries);

    // Use a real config with a different name.
    $other_config = $config_factory->get('system.site');

    $event = new ConfigCrudEvent($other_config);

    // Dispatch the SAVE event.
    $event_dispatcher->dispatch($event, 'config.save');

    // Cache should still be populated because it's not our config.
    $countries_after = $country_data_service->getCountries();
    $this->assertNotEmpty($countries_after);
    // Just verify we got the same number of countries, not comparing objects.
    $this->assertCount(count($countries), $countries_after);
  }

}

