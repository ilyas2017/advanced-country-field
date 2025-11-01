<?php

namespace Drupal\Tests\advanced_country_field\Unit;

use Drupal\advanced_country_field\Service\CountryDataService;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\advanced_country_field\Service\CountryDataService
 * @group advanced_country_field
 */
class CountryDataServiceTest extends UnitTestCase {

  /**
   * The country data service under test.
   *
   * @var \Drupal\advanced_country_field\Service\CountryDataService
   */
  protected $countryDataService;

  /**
   * The config factory mock.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $configFactory;

  /**
   * The language manager mock.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $languageManager;

  /**
   * The string translation mock.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $stringTranslation;

  /**
   * The module handler mock.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create mock config.
    $config = $this->createMock(ImmutableConfig::class);
    $config->expects($this->any())
      ->method('get')
      ->willReturnCallback(function ($key) {
        $defaults = [
          'default_value_format' => 'code',
          'language_handling' => 'site',
          'filter_enabled' => FALSE,
          'enabled_countries' => [],
          'custom_countries' => [],
          'flag_library_path' => '/libraries/country-flag-icons/3x2/',
        ];
        return $defaults[$key] ?? NULL;
      });

    // Create config factory mock.
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->configFactory->expects($this->any())
      ->method('get')
      ->with('advanced_country_field.settings')
      ->willReturn($config);

    // Create language manager mock.
    $language = new Language(['id' => 'en']);
    $this->languageManager = $this->createMock(LanguageManagerInterface::class);
    $this->languageManager->expects($this->any())
      ->method('getCurrentLanguage')
      ->willReturn($language);

    // Create string translation mock.
    $this->stringTranslation = $this->createMock(TranslationInterface::class);
    $this->stringTranslation->expects($this->any())
      ->method('translateString')
      ->willReturnCallback(function ($translatable) {
        // Return the untranslated string from the TranslatableMarkup object.
        if (method_exists($translatable, 'getUntranslatedString')) {
          return $translatable->getUntranslatedString();
        }
        return is_string($translatable) ? $translatable : (string) $translatable;
      });

    // Create module handler mock.
    $this->moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $this->moduleHandler->expects($this->any())
      ->method('alter')
      ->willReturnCallback(function ($hook_name, &$data, &$context = NULL) {
        // Default behavior: don't alter anything unless specifically tested.
        // This allows hooks to be invoked but not change data by default.
      });

    // Create the service under test.
    $this->countryDataService = new CountryDataService(
      $this->languageManager,
      $this->configFactory,
      $this->stringTranslation,
      $this->moduleHandler
    );
  }

  /**
   * Tests getting countries with default settings.
   *
   * @covers ::getCountries
   */
  public function testGetCountriesDefault() {
    $countries = $this->countryDataService->getCountries();

    // Should return an array.
    $this->assertIsArray($countries);

    // Should contain some expected countries.
    $this->assertArrayHasKey('US', $countries);
    $this->assertArrayHasKey('CA', $countries);
    $this->assertArrayHasKey('GB', $countries);

    // Should have proper format: code => name (may be TranslatableMarkup objects).
    $this->assertEquals('United States', (string) $countries['US']);
    $this->assertEquals('Canada', (string) $countries['CA']);
  }

  /**
   * Tests getting unfiltered countries.
   *
   * @covers ::getCountries
   */
  public function testGetCountriesUnfiltered() {
    $countries_unfiltered = $this->countryDataService->getCountries(FALSE);
    $countries_filtered = $this->countryDataService->getCountries(TRUE);

    // With filter_enabled = FALSE, both should return the same result.
    $this->assertCount(count($countries_unfiltered), $countries_filtered);
    $this->assertEquals(array_keys($countries_unfiltered), array_keys($countries_filtered));
  }

  /**
   * Tests getting a specific country name.
   *
   * @covers ::getCountryName
   */
  public function testGetCountryName() {
    $name = $this->countryDataService->getCountryName('US');
    $this->assertEquals('United States', (string) $name);

    $name = $this->countryDataService->getCountryName('CA');
    $this->assertEquals('Canada', (string) $name);

    $name = $this->countryDataService->getCountryName('INVALID');
    $this->assertNull($name);
  }

  /**
   * Tests getting custom countries.
   *
   * @covers ::getCustomCountries
   */
  public function testGetCustomCountries() {
    $custom_countries = $this->countryDataService->getCustomCountries();

    // Should return an array.
    $this->assertIsArray($custom_countries);

    // With no custom countries configured, should be empty.
    $this->assertEmpty($custom_countries);
  }

  /**
   * Tests getting flag path.
   *
   * @covers ::getFlagPath
   */
  public function testGetFlagPath() {
    $path = $this->countryDataService->getFlagPath('US');
    $this->assertEquals('/libraries/country-flag-icons/3x2/us.svg', $path);

    // Test with uppercase code (should be lowercased).
    $path = $this->countryDataService->getFlagPath('CA');
    $this->assertEquals('/libraries/country-flag-icons/3x2/ca.svg', $path);

    // Test with lowercase code.
    $path = $this->countryDataService->getFlagPath('gb');
    $this->assertEquals('/libraries/country-flag-icons/3x2/gb.svg', $path);
  }

  /**
   * Tests getting flag path with trailing slash handling.
   *
   * @covers ::getFlagPath
   */
  public function testGetFlagPathTrailingSlash() {
    // Clear any cached data first.
    CountryDataService::clearCache();
    
    // Create a config that has a path without trailing slash.
    $config = $this->createMock(ImmutableConfig::class);
    $config->expects($this->any())
      ->method('get')
      ->willReturnCallback(function ($key) {
        if ($key === 'flag_library_path') {
          return '/custom/flags';
        }
        return NULL;
      });

    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->configFactory->expects($this->any())
      ->method('get')
      ->with('advanced_country_field.settings')
      ->willReturn($config);

    $service = new CountryDataService(
      $this->languageManager,
      $this->configFactory,
      $this->stringTranslation,
      $this->moduleHandler
    );

    $path = $service->getFlagPath('US');
    $this->assertEquals('/custom/flags/us.svg', $path);
  }

  /**
   * Tests cache clearing.
   *
   * @covers ::clearCache
   * @covers ::getCountries
   */
  public function testClearCache() {
    // Get countries to populate cache.
    $countries1 = $this->countryDataService->getCountries();

    // Clear cache.
    CountryDataService::clearCache();

    // Get countries again.
    $countries2 = $this->countryDataService->getCountries();

    // Results should still be the same, but cache was cleared in between.
    $this->assertCount(count($countries1), $countries2);
    $this->assertEquals(array_keys($countries1), array_keys($countries2));
  }

  /**
   * Tests that results are cached properly.
   *
   * @covers ::getCountries
   */
  public function testCaching() {
    // First call.
    $countries1 = $this->countryDataService->getCountries();

    // Second call with same parameters.
    $countries2 = $this->countryDataService->getCountries();

    // Should return same reference (cached).
    $this->assertCount(count($countries1), $countries2);
    $this->assertEquals(array_keys($countries1), array_keys($countries2));

    // Same parameters should hit cache.
    $countries3 = $this->countryDataService->getCountries(TRUE, 'en');
    $this->assertCount(count($countries1), $countries3);
    $this->assertEquals(array_keys($countries1), array_keys($countries3));
  }

  /**
   * Tests filtering with enabled countries.
   *
   * @covers ::getCountries
   */
  public function testFiltering() {
    // Clear any cached data first.
    CountryDataService::clearCache();
    
    // Create config with filter enabled and specific countries.
    $config = $this->createMock(ImmutableConfig::class);
    $config->expects($this->any())
      ->method('get')
      ->willReturnCallback(function ($key) {
        $defaults = [
          'default_value_format' => 'code',
          'language_handling' => 'site',
          'filter_enabled' => TRUE,
          'enabled_countries' => ['US', 'CA', 'GB'],
          'custom_countries' => [],
          'flag_library_path' => '/libraries/country-flag-icons/3x2/',
        ];
        return $defaults[$key] ?? NULL;
      });

    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->configFactory->expects($this->any())
      ->method('get')
      ->with('advanced_country_field.settings')
      ->willReturn($config);

    $service = new CountryDataService(
      $this->languageManager,
      $this->configFactory,
      $this->stringTranslation,
      $this->moduleHandler
    );

    $countries = $service->getCountries(TRUE);

    // Should only contain enabled countries.
    $this->assertArrayHasKey('US', $countries);
    $this->assertArrayHasKey('CA', $countries);
    $this->assertArrayHasKey('GB', $countries);
    $this->assertArrayNotHasKey('FR', $countries);
    $this->assertCount(3, $countries);
  }

  /**
   * Tests hook_advanced_country_field_countries_alter().
   *
   * @covers ::getCountries
   */
  public function testCountriesAlterHook() {
    // Clear any cached data first.
    CountryDataService::clearCache();

    // Create a module handler that will alter the countries list.
    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->expects($this->once())
      ->method('alter')
      ->willReturnCallback(function ($hook_name, &$countries, &$context = NULL) {
        // Verify hook name is correct.
        $this->assertEquals('advanced_country_field_countries', $hook_name);
        
        // Verify countries array is passed by reference and can be modified.
        $this->assertIsArray($countries);
        $this->assertArrayHasKey('US', $countries);
        
        // Modify the list: remove a country and add a custom one.
        unset($countries['FR']);
        $countries['XX'] = 'Test Country';
        
        // Verify context is passed correctly.
        $this->assertIsArray($context);
        $this->assertArrayHasKey('langcode', $context);
        $this->assertArrayHasKey('language_handling', $context);
        $this->assertArrayHasKey('filtered', $context);
      });

    $service = new CountryDataService(
      $this->languageManager,
      $this->configFactory,
      $this->stringTranslation,
      $moduleHandler
    );

    $countries = $service->getCountries();

    // Verify hook was invoked and changes were applied.
    $this->assertArrayNotHasKey('FR', $countries, 'Hook should have removed FR');
    $this->assertArrayHasKey('XX', $countries, 'Hook should have added XX');
    $this->assertEquals('Test Country', (string) $countries['XX']);
  }

}

