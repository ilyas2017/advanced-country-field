# Advanced Country Field Tests

This directory contains the test suite for the Advanced Country Field module.

## Test Structure

### Unit Tests (`src/Unit/`)

Unit tests test individual classes in isolation using mocks:

- **CountryDataServiceTest**: Tests the `CountryDataService` for country data retrieval, filtering, caching, and flag path generation.

### Kernel Tests (`src/Kernel/`)

Kernel tests test functionality with a minimal Drupal installation:

- **AdvancedCountryFieldItemTest**: Tests the field type plugin, including property definitions, schema, and `isEmpty()` logic.
- **SettingsFormTest**: Tests the settings configuration form, including form building and submission.
- **FieldStorageTest**: Tests field storage and retrieval with actual entities.
- **ConfigCacheInvalidatorTest**: Tests the event subscriber that invalidates caches on configuration changes.

### Functional Tests (`src/Functional/`)

Functional tests test the module with a full browser environment:

- **ModuleInstallationTest**: Tests module installation, settings page access, and permissions.

## Running Tests

### Running all tests for the module

```bash
# Using Drush (recommended)
drush test advanced_country_field

# Using PHPUnit directly
vendor/bin/phpunit modules/contrib/advanced_country_field/tests
```

### Running specific test groups

```bash
# Unit tests only
vendor/bin/phpunit modules/contrib/advanced_country_field/tests/src/Unit

# Kernel tests only
vendor/bin/phpunit modules/contrib/advanced_country_field/tests/src/Kernel

# Functional tests only
vendor/bin/phpunit modules/contrib/advanced_country_field/tests/src/Functional
```

### Running a specific test class

```bash
vendor/bin/phpunit modules/contrib/advanced_country_field/tests/src/Unit/CountryDataServiceTest.php
```

### Running tests with coverage

```bash
vendor/bin/phpunit --coverage-html coverage modules/contrib/advanced_country_field/tests
```

## Writing New Tests

When adding new functionality to the module, please add corresponding tests:

1. **Unit tests** for services and utility classes with complex logic.
2. **Kernel tests** for field types, widgets, formatters, and configuration.
3. **Functional tests** for forms, routes, and user-facing features.

### Test Naming Conventions

- Test classes should end with `Test`.
- Test methods should start with `test`.
- Use descriptive names for both classes and methods.

### Example Test

```php
<?php

namespace Drupal\Tests\advanced_country_field\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * @group advanced_country_field
 */
class MyServiceTest extends UnitTestCase {

  public function testMyMethod() {
    // Arrange: Set up test data.
    $input = 'test';

    // Act: Call the method under test.
    $result = my_function($input);

    // Assert: Verify the result.
    $this->assertEquals('expected', $result);
  }

}
```

## Continuous Integration

Tests are automatically run on:

- Pull requests to the main branch
- Pushes to the main branch
- Manual CI trigger

See `.github/workflows/` for CI configuration.

## Coverage Goals

- Aim for at least 80% code coverage
- Focus on testing critical functionality
- Test edge cases and error conditions

## Additional Resources

- [Drupal Testing Documentation](https://www.drupal.org/docs/develop/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

