# Developer Guide

Complete developer guide for Advanced Country Field module, including API documentation, hooks, and customization options.

## Table of Contents

1. [Architecture](#architecture)
2. [API Reference](#api-reference)
3. [Hooks](#hooks)
4. [Services](#services)
5. [Customization](#customization)
6. [Testing](#testing)
7. [Debugging](#debugging)

## Architecture

### Module Structure

```
advanced_country_field/
├── src/
│   ├── Plugin/
│   │   ├── Field/
│   │   │   ├── FieldType/
│   │   │   │   └── AdvancedCountryFieldItem.php        # Field type
│   │   │   │   └── AdvancedCountryFieldItemList.php    # Item list
│   │   │   ├── FieldWidget/
│   │   │   │   └── AdvancedCountryFieldWidget.php      # Widget
│   │   │   └── FieldFormatter/
│   │   │       └── AdvancedCountryFieldFormatter.php   # Formatter
│   │   └── ...
│   ├── Form/
│   │   ├── AdvancedCountryFieldSettingsForm.php        # Settings
│   │   ├── CountryFilterForm.php                       # Filter config
│   │   └── CustomCountryForm.php                       # Custom countries
│   ├── Service/
│   │   └── CountryDataService.php                      # Country data
│   └── Controller/
├── css/                                                 # Stylesheets
├── js/                                                  # JavaScript
├── templates/                                           # Twig
├── config/
│   ├── install/                                        # Defaults
│   └── schema/                                         # Schemas
└── advanced_country_field.module                       # Hooks
```

### Core Components

#### 1. Field Type

**Class**: `Drupal\advanced_country_field\Plugin\Field\FieldType\AdvancedCountryFieldItem`

**Purpose**: Defines the data structure and schema for the country field.

**Properties**:
- `country_code` (string, 2 chars, required): ISO 3166-1 alpha-2 code
- `country_name` (string, 255 chars, optional): Country name

#### 2. Widget

**Class**: `Drupal\advanced_country_field\Plugin\Field\FieldWidget\AdvancedCountryFieldWidget`

**Purpose**: Provides form elements for country selection.

**Features**:
- Multiple widget types
- Cardinality-based widget selection
- Flag display with JavaScript
- Search functionality

#### 3. Formatter

**Class**: `Drupal\advanced_country_field\Plugin\Field\FieldFormatter\AdvancedCountryFieldFormatter`

**Purpose**: Controls how the field is displayed.

**Features**:
- Multiple display formats
- Flag display with size control
- Customizable positioning

#### 4. Service

**Class**: `Drupal\advanced_country_field\Service\CountryDataService`

**Purpose**: Centralized country data management.

**Dependencies**:
- `language_manager`
- `config.factory`
- `string_translation`

## API Reference

### CountryDataService

The main service for managing country data.

#### Methods

##### getCountries()

Get all available countries.

**Signature**:
```php
public function getCountries($filtered = TRUE, $langcode = NULL): array
```

**Parameters**:
- `$filtered` (bool): Whether to filter based on configuration
- `$langcode` (string|null): Language code for translation

**Returns**: Array of country codes => names

**Example**:
```php
use Drupal\advanced_country_field\Service\CountryDataService;

// Get all countries
$service = \Drupal::service('advanced_country_field.country_data');
$countries = $service->getCountries();

// Get unfiltered list
$all_countries = $service->getCountries(FALSE);

// Get translated countries
$translated = $service->getCountries(TRUE, 'fr');
```

##### getCountryName()

Get country name by code.

**Signature**:
```php
public function getCountryName($code, $langcode = NULL): ?string
```

**Parameters**:
- `$code` (string): ISO 3166-1 alpha-2 code
- `$langcode` (string|null): Language code

**Returns**: Country name or NULL

**Example**:
```php
$name = $service->getCountryName('US'); // "United States"
$name_fr = $service->getCountryName('US', 'fr'); // "États-Unis"
```

##### getCustomCountries()

Get custom countries from configuration.

**Signature**:
```php
public function getCustomCountries(): array
```

**Returns**: Array of custom country codes => names

**Example**:
```php
$custom = $service->getCustomCountries();
```

##### getFlagPath()

Get flag SVG path for country code.

**Signature**:
```php
public function getFlagPath($code): string
```

**Parameters**:
- `$code` (string): ISO 3166-1 alpha-2 code

**Returns**: Flag file path

**Example**:
```php
$flag_path = $service->getFlagPath('US');
// Returns: /libraries/country-flag-icons/3x2/us.svg
```

## Hooks

### hook_advanced_country_field_country_list_alter()

Alter the list of available countries.

**Signature**:
```php
function hook_advanced_country_field_country_list_alter(array &$countries, array $context) {
}
```

**Parameters**:
- `$countries` (array): Array of country codes => names (by reference)
- `$context` (array): Contextual information

**Example**:
```php
/**
 * Implements hook_advanced_country_field_country_list_alter().
 */
function mymodule_advanced_country_field_country_list_alter(array &$countries, array $context) {
  // Remove specific countries
  unset($countries['XX']);
  
  // Add custom country
  $countries['XX'] = 'Custom Country';
  
  // Modify existing name
  $countries['US'] = 'United States of America';
}
```

### hook_advanced_country_field_custom_country_info_alter()

Alter custom country information.

**Signature**:
```php
function hook_advanced_country_field_custom_country_info_alter(array &$custom_countries, array $context) {
}
```

**Parameters**:
- `$custom_countries` (array): Array of custom country data (by reference)
- `$context` (array): Contextual information

**Example**:
```php
/**
 * Implements hook_advanced_country_field_custom_country_info_alter().
 */
function mymodule_advanced_country_field_custom_country_info_alter(array &$custom_countries, array $context) {
  // Add custom country data
  $custom_countries['XX'] = [
    'code' => 'XX',
    'name' => 'Custom Country',
    'flag' => '/sites/default/files/flags/xx.svg',
  ];
}
```

### hook_field_widget_info_alter()

Alter widget information (Drupal core hook).

**Signature**:
```php
function hook_field_widget_info_alter(array &$info) {
}
```

**Example**:
```php
/**
 * Implements hook_field_widget_info_alter().
 */
function mymodule_field_widget_info_alter(array &$info) {
  // Customize widget
  if (isset($info['advanced_country_field_widget'])) {
    $info['advanced_country_field_widget']['description'] = t('My custom description');
  }
}
```

### hook_field_formatter_info_alter()

Alter formatter information (Drupal core hook).

**Signature**:
```php
function hook_field_formatter_info_alter(array &$info) {
}
```

**Example**:
```php
/**
 * Implements hook_field_formatter_info_alter().
 */
function mymodule_field_formatter_info_alter(array &$info) {
  // Customize formatter
  if (isset($info['advanced_country_field_formatter'])) {
    $info['advanced_country_field_formatter']['label'] = t('Custom Formatter');
  }
}
```

### hook_theme()

Register theme templates (Drupal core hook).

**Example**:
```php
/**
 * Implements hook_theme().
 */
function mymodule_theme(array $existing, $type, $theme, $path) {
  return [
    'my_country_display' => [
      'variables' => [
        'country_code' => NULL,
        'country_name' => NULL,
      ],
      'template' => 'my-country-display',
    ],
  ];
}
```

## Customization

### Custom Widget

Create a custom widget by extending the base widget:

```php
<?php

namespace Drupal\mymodule\Plugin\Field\FieldWidget;

use Drupal\advanced_country_field\Plugin\Field\FieldWidget\AdvancedCountryFieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Custom country widget.
 *
 * @FieldWidget(
 *   id = "my_custom_country_widget",
 *   label = @Translation("Custom Country Widget"),
 *   field_types = {"advanced_country_field"}
 * )
 */
class MyCustomCountryWidget extends AdvancedCountryFieldWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    
    // Custom modifications
    $element['#attributes']['class'][] = 'my-custom-class';
    
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['my_custom_setting'] = 'default';
    return $settings;
  }

}
```

### Custom Formatter

Create a custom formatter:

```php
<?php

namespace Drupal\mymodule\Plugin\Field\FieldFormatter;

use Drupal\advanced_country_field\Plugin\Field\FieldFormatter\AdvancedCountryFieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Custom country formatter.
 *
 * @FieldFormatter(
 *   id = "my_custom_country_formatter",
 *   label = @Translation("Custom Country Formatter"),
 *   field_types = {"advanced_country_field"}
 * )
 */
class MyCustomCountryFormatter extends AdvancedCountryFieldFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    
    // Custom modifications
    foreach ($elements as $delta => $element) {
      $elements[$delta]['#attributes']['class'][] = 'my-custom-class';
    }
    
    return $elements;
  }

}
```

### Custom JavaScript

Extend the widget JavaScript:

```javascript
(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.myCustomCountryWidget = {
    attach: function (context, settings) {
      // Custom JavaScript logic
      once('myCustomCountryWidget', '.advanced-country-field-widget', context).forEach(function (element) {
        // Your custom code
      });
    }
  };

})(Drupal, once);
```

### Custom CSS

Add custom CSS via your theme or module:

```css
/* Custom country field styling */
.advanced-country-field-widget {
  border: 2px solid #0073aa;
}

.advanced-country-field-display {
  font-weight: bold;
}

.country-flag {
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}
```

### Twig Template Override

Override templates in your theme:

**Template**: `templates/advanced-country-field-display.html.twig`

```twig
{{ attach_library('advanced_country_field/flags') }}

<div class="custom-country-display">
  {% if show_flag %}
    <img src="{{ flag_path }}" alt="{{ country_name }}" class="country-flag" />
  {% endif %}
  <span class="country-name">{{ country_name }}</span>
</div>
```

## Testing

### Unit Tests

Create unit tests for custom functionality:

```php
<?php

namespace Drupal\Tests\mymodule\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\advanced_country_field\Service\CountryDataService;

/**
 * Tests for custom country functionality.
 *
 * @group mymodule
 */
class MyCountryTest extends UnitTestCase {

  /**
   * Test country name retrieval.
   */
  public function testCountryName() {
    $service = $this->prophesize(CountryDataService::class);
    $service->getCountryName('US')->willReturn('United States');
    
    // Your test logic
  }

}
```

### Functional Tests

Test form behavior:

```php
<?php

namespace Drupal\Tests\advanced_country_field\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Functional tests for Advanced Country Field.
 *
 * @group advanced_country_field
 */
class AdvancedCountryFieldTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Test field creation.
   */
  public function testFieldCreation() {
    // Your test logic
  }

}
```

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test class
vendor/bin/phpunit advanced_country_field/tests/src/Unit/

# Run with coverage
vendor/bin/phpunit --coverage-html coverage
```

## Debugging

### Enable Debug Mode

Add to `settings.local.php`:

```php
// Enable debug output
$config['system.logging']['error_level'] = 'verbose';

// Enable Twig debug
$settings['twig_debug'] = TRUE;
```

### View Country Data

```php
// Get all countries
$service = \Drupal::service('advanced_country_field.country_data');
$countries = $service->getCountries();
ksort($countries);
var_dump($countries);

// Check configuration
$config = \Drupal::config('advanced_country_field.settings');
var_dump($config->getRawData());
```

### Debug JavaScript

Add to browser console:

```javascript
// Check widget initialization
console.log(Drupal.behaviors.advancedCountryFieldWidget);

// Log settings
console.log(drupalSettings.advancedCountryField);

// Check flag paths
jQuery('.advanced-country-field-widget').each(function() {
  console.log(jQuery(this).data('flag-base-path'));
});
```

### Debug Configuration

```bash
# View configuration
drush config:get advanced_country_field.settings

# Edit configuration
drush config:edit advanced_country_field.settings

# Import configuration
drush config:import

# Export configuration
drush config:export
```

## Best Practices

### Performance

- Cache country data when possible
- Use efficient queries
- Minimize JavaScript execution
- Optimize CSS delivery

### Security

- Sanitize user input
- Validate country codes
- Use CSRF tokens
- Check permissions

### Accessibility

- Provide ARIA labels
- Support keyboard navigation
- Test with screen readers
- Use semantic HTML

### Code Quality

- Follow Drupal coding standards
- Write comprehensive tests
- Document your code
- Use dependency injection

## Additional Resources

- [Drupal API Documentation](https://api.drupal.org/api/drupal)
- [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards)
- [Field API Reference](https://www.drupal.org/docs/8/api/entity-api/field-api-field-api)
- [Plugin System](https://www.drupal.org/docs/drupal-apis/plugin-api)

## Getting Help

- [Issue Tracker](https://github.com/ilyas2017/advanced-country-field/issues)
- [Drupal Community](https://www.drupal.org/community)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/drupal)

