# API Reference

Complete API reference for Advanced Country Field module.

## Service API

### CountryDataService

Service ID: `advanced_country_field.country_data`

**Namespace**: `Drupal\advanced_country_field\Service\CountryDataService`

#### Methods

##### getCountries()

Retrieve all available countries.

```php
public function getCountries($filtered = TRUE, $langcode = NULL): array
```

**Parameters**:
- `$filtered` (bool, default: TRUE): Whether to apply country filtering
- `$langcode` (string|null, default: NULL): Language code for translation

**Returns**: Associative array of country codes => country names

**Example**:
```php
use Drupal\advanced_country_field\Service\CountryDataService;

$service = \Drupal::service('advanced_country_field.country_data');

// Get all countries (filtered)
$countries = $service->getCountries();

// Get unfiltered list
$all = $service->getCountries(FALSE);

// Get French translations
$french = $service->getCountries(TRUE, 'fr');
```

##### getCountryName()

Get country name by ISO code.

```php
public function getCountryName($code, $langcode = NULL): ?string
```

**Parameters**:
- `$code` (string): ISO 3166-1 alpha-2 country code
- `$langcode` (string|null): Language code

**Returns**: Country name or NULL if not found

**Example**:
```php
$name = $service->getCountryName('US');
// Returns: "United States"

$name_fr = $service->getCountryName('US', 'fr');
// Returns: "États-Unis"
```

##### getCustomCountries()

Retrieve custom countries from configuration.

```php
public function getCustomCountries(): array
```

**Returns**: Associative array of custom country codes => names

**Example**:
```php
$custom = $service->getCustomCountries();
// Returns: ['EU' => 'European Union', ...]
```

##### getFlagPath()

Get SVG flag file path for country code.

```php
public function getFlagPath($code): string
```

**Parameters**:
- `$code` (string): ISO 3166-1 alpha-2 country code

**Returns**: Relative path to flag SVG file

**Example**:
```php
$path = $service->getFlagPath('US');
// Returns: "/libraries/country-flag-icons/3x2/us.svg"
```

## Hook API

### hook_advanced_country_field_country_list_alter()

Alter the list of available countries before display.

```php
function hook_advanced_country_field_country_list_alter(array &$countries, array $context)
```

**Parameters**:
- `$countries` (array, by reference): Country codes => names
- `$context` (array): Contextual information
  - `mode` (string): 'widget' or 'formatter'
  - `field_name` (string): Field name
  - `entity_type` (string): Entity type

**Example**:
```php
function mymodule_advanced_country_field_country_list_alter(array &$countries, array $context) {
  // Remove specific countries
  unset($countries['XX']);
  
  // Add custom country
  $countries['EU'] = 'European Union';
  
  // Modify existing
  $countries['US'] = 'United States of America';
}
```

### hook_advanced_country_field_custom_country_info_alter()

Alter custom country information.

```php
function hook_advanced_country_field_custom_country_info_alter(array &$custom_countries)
```

**Parameters**:
- `$custom_countries` (array, by reference): Custom country data

**Example**:
```php
function mymodule_advanced_country_field_custom_country_info_alter(array &$custom_countries) {
  $custom_countries['XX'] = [
    'code' => 'XX',
    'name' => 'Custom Country',
  ];
}
```

### hook_advanced_country_field_widget_options_alter()

Alter widget options before rendering.

```php
function hook_advanced_country_field_widget_options_alter(array &$options, array $context)
```

**Parameters**:
- `$options` (array, by reference): Formatted options
- `$context` (array): Context information

**Example**:
```php
function mymodule_advanced_country_field_widget_options_alter(array &$options, array $context) {
  // Sort alphabetically
  asort($options);
}
```

## Field Plugin API

### AdvancedCountryFieldItem

**Type**: Field Type Plugin

**Class**: `Drupal\advanced_country_field\Plugin\Field\FieldType\AdvancedCountryFieldItem`

**Extends**: `Drupal\Core\Field\FieldItemBase`

#### Properties

| Property | Type | Length | Required | Description |
|----------|------|--------|----------|-------------|
| `country_code` | string | 2 | Yes | ISO 3166-1 alpha-2 code |
| `country_name` | string | 255 | No | Country name |

#### Schema

```php
[
  'columns' => [
    'country_code' => [
      'type' => 'varchar',
      'length' => 2,
      'not null' => TRUE,
    ],
    'country_name' => [
      'type' => 'varchar',
      'length' => 255,
      'not null' => FALSE,
    ],
  ],
  'indexes' => [
    'country_code' => ['country_code'],
  ],
]
```

### AdvancedCountryFieldWidget

**Type**: Widget Plugin

**Class**: `Drupal\advanced_country_field\Plugin\Field\FieldWidget\AdvancedCountryFieldWidget`

**Extends**: `Drupal\Core\Field\WidgetBase`

#### Settings

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| `widget_type` | string | 'select' | Widget type |
| `show_flags` | bool | FALSE | Show country flags |
| `flag_position` | string | 'before' | Flag position |
| `placeholder` | string | 'Select...' | Placeholder text |
| `enable_search` | bool | FALSE | Enable search |
| `allow_custom` | bool | FALSE | Allow custom entries |
| `value_format` | string | 'code' | Value format |

#### Methods

##### formElement()

Build the form element.

```php
public function formElement(
  FieldItemListInterface $items, 
  $delta, 
  array $element, 
  array &$form, 
  FormStateInterface $form_state
): array
```

##### massageFormValues()

Process submitted values.

```php
public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array
```

### AdvancedCountryFieldFormatter

**Type**: Formatter Plugin

**Class**: `Drupal\advanced_country_field\Plugin\Field\FieldFormatter\AdvancedCountryFieldFormatter`

**Extends**: `Drupal\Core\Field\FormatterBase`

#### Settings

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| `display_format` | string | 'name' | Display format |
| `show_flag` | bool | FALSE | Show flag |
| `flag_position` | string | 'before' | Flag position |
| `flag_width` | string | '20px' | Flag width |
| `flag_height` | string | '15px' | Flag height |
| `link_to_country` | bool | FALSE | Link to country |

#### Methods

##### viewElements()

Build render array for display.

```php
public function viewElements(FieldItemListInterface $items, $langcode): array
```

## Configuration API

### Configuration Objects

**Config Name**: `advanced_country_field.settings`

#### Keys

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `default_value_format` | string | 'code' | Default value format |
| `language_handling` | string | 'site' | Language handling |
| `filter_enabled` | bool | FALSE | Filtering enabled |
| `enabled_countries` | array | [] | Enabled country codes |
| `custom_countries` | array | [] | Custom countries |
| `flag_library_path` | string | '/libraries/...' | Flag library path |

### Accessing Configuration

```php
// Read configuration
$config = \Drupal::config('advanced_country_field.settings');
$language_handling = $config->get('language_handling');

// Edit configuration
$config = \Drupal::configFactory()->getEditable('advanced_country_field.settings');
$config->set('language_handling', 'native')->save();

// Check existence
$config->get('custom_countries') ?? [];
```

## JavaScript API

### Widget Behavior

**Namespace**: `Drupal.behaviors.advancedCountryFieldWidget`

**Selector**: `.advanced-country-field-widget`

**Settings**: Available via `drupalSettings.advancedCountryField`

#### Settings Structure

```javascript
drupalSettings.advancedCountryField = {
  flags: {
    'field_name': {
      flag_base_path: '/libraries/country-flag-icons/3x2/',
      flag_position: 'before'
    }
  }
};
```

#### Methods

The widget JavaScript provides custom dropdown functionality for flag display in select options.

## Twig Variables

### Template: `advanced-country-field-display`

**File**: `templates/advanced-country-field-display.html.twig`

#### Variables

| Variable | Type | Description |
|----------|------|-------------|
| `country_code` | string | ISO country code |
| `country_name` | string | Country name |
| `display_format` | string | Display format |
| `show_flag` | bool | Show flag |
| `flag_path` | string | Flag file path |

## Data Structures

### Country Array

```php
[
  'AD' => 'Andorra',
  'AE' => 'United Arab Emirates',
  'AF' => 'Afghanistan',
  // ... 249 countries
]
```

### Custom Country Array

```php
[
  [
    'code' => 'XX',
    'name' => 'Custom Country',
  ],
  // ...
]
```

### Enabled Countries Array

```php
['US', 'CA', 'MX', 'GB', 'FR']
```

## Constants

None currently defined.

## Events

None currently defined.

## Examples

See [Developer Guide](DEVELOPER.md) for comprehensive examples.

## Additional Resources

- [Drupal Field API](https://www.drupal.org/docs/drupal-apis/entity-api/field-api-field-api)
- [Drupal Plugin API](https://www.drupal.org/docs/drupal-apis/plugin-api)
- [Drupal Service API](https://www.drupal.org/docs/drupal-apis/services-and-dependency-injection)

