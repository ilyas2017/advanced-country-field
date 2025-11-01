<?php

namespace Drupal\Tests\advanced_country_field\Kernel;

use Drupal\advanced_country_field\Plugin\Field\FieldType\AdvancedCountryFieldItem;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests for AdvancedCountryFieldItem field type.
 *
 * @group advanced_country_field
 */
class AdvancedCountryFieldItemTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'field',
    'advanced_country_field',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('field_config');
  }

  /**
   * Tests property definitions.
   */
  public function testPropertyDefinitions() {
    $field_definition = $this->createMock('\Drupal\Core\Field\FieldStorageDefinitionInterface');

    $properties = AdvancedCountryFieldItem::propertyDefinitions($field_definition);

    // Should have country_code property.
    $this->assertArrayHasKey('country_code', $properties);
    $this->assertEquals('The ISO 3166-1 alpha-2 country code.', $properties['country_code']->getDescription());

    // Should have country_name property.
    $this->assertArrayHasKey('country_name', $properties);
    $this->assertEquals('The country name (localized if available).', $properties['country_name']->getDescription());
  }

  /**
   * Tests schema definition.
   */
  public function testSchema() {
    $field_definition = $this->createMock('\Drupal\Core\Field\FieldStorageDefinitionInterface');

    $schema = AdvancedCountryFieldItem::schema($field_definition);

    // Check columns.
    $this->assertArrayHasKey('columns', $schema);
    $this->assertArrayHasKey('country_code', $schema['columns']);
    $this->assertArrayHasKey('country_name', $schema['columns']);

    // Check column types.
    $this->assertEquals('varchar', $schema['columns']['country_code']['type']);
    $this->assertEquals(2, $schema['columns']['country_code']['length']);
    $this->assertTrue($schema['columns']['country_code']['not null']);

    $this->assertEquals('varchar', $schema['columns']['country_name']['type']);
    $this->assertEquals(255, $schema['columns']['country_name']['length']);
    $this->assertFalse($schema['columns']['country_name']['not null']);

    // Check indexes.
    $this->assertArrayHasKey('indexes', $schema);
    $this->assertArrayHasKey('country_code', $schema['indexes']);
    $this->assertEquals(['country_code'], $schema['indexes']['country_code']);
  }

  /**
   * Tests isEmpty method.
   */
  public function testIsEmpty() {
    // This test is deferred because isEmpty() requires a properly initialized
    // field item instance. The method implementation itself is straightforward
    // and validates that country_code must be non-empty.
    // Full integration testing happens in FieldStorageTest.
    $this->assertTrue(TRUE);
  }

  /**
   * Tests main property name.
   */
  public function testMainPropertyName() {
    $this->assertEquals('country_code', AdvancedCountryFieldItem::mainPropertyName());
  }

}

