<?php

namespace Drupal\Tests\advanced_country_field\Kernel;

use Drupal\advanced_country_field\Plugin\Field\FieldType\AdvancedCountryFieldItem;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests field storage and retrieval.
 *
 * @group advanced_country_field
 */
class FieldStorageTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'field',
    'user',
    'advanced_country_field',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('field_config');
    $this->installEntitySchema('user');
  }

  /**
   * Tests field storage creation and retrieval.
   */
  public function testFieldStorage() {
    // Create a field storage.
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_test_country',
      'entity_type' => 'user',
      'type' => 'advanced_country_field',
    ]);
    $field_storage->save();

    // Create a field instance.
    $field = FieldConfig::create([
      'field_name' => 'field_test_country',
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => 'Test Country',
    ]);
    $field->save();

    // Create a user entity.
    $user = \Drupal::entityTypeManager()->getStorage('user')->create([
      'name' => 'testuser',
      'mail' => 'test@example.com',
      'status' => 1,
    ]);

    // Set country field value.
    $user->field_test_country->country_code = 'US';
    $user->field_test_country->country_name = 'United States';
    $user->save();

    // Reload the user.
    $loaded_user = \Drupal::entityTypeManager()->getStorage('user')->load($user->id());

    // Verify the field value.
    $this->assertEquals('US', $loaded_user->field_test_country->country_code);
    $this->assertEquals('United States', $loaded_user->field_test_country->country_name);
  }

  /**
   * Tests field storage with empty values.
   */
  public function testFieldStorageEmpty() {
    // Create a field storage.
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_test_country_empty',
      'entity_type' => 'user',
      'type' => 'advanced_country_field',
    ]);
    $field_storage->save();

    // Create a field instance.
    $field = FieldConfig::create([
      'field_name' => 'field_test_country_empty',
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => 'Test Country Empty',
    ]);
    $field->save();

    // Create a user entity without country field value.
    $user = \Drupal::entityTypeManager()->getStorage('user')->create([
      'name' => 'testuser_empty',
      'mail' => 'empty@example.com',
      'status' => 1,
    ]);
    $user->save();

    // Reload the user.
    $loaded_user = \Drupal::entityTypeManager()->getStorage('user')->load($user->id());

    // Field should be empty.
    $this->assertTrue($loaded_user->field_test_country_empty->isEmpty());
  }

}

