# Advanced Country Field Drupal Module

[![Latest Stable Version](https://img.shields.io/badge/stable-1.0.0-blue.svg)](https://www.drupal.org/project/advanced_country_field)
[![License](https://img.shields.io/badge/license-GPL%202.0%2B-blue.svg)](LICENSE.txt)

A comprehensive Drupal field module providing enhanced country selection with flags, filtering, search, multi-language support, and extensive customization options.

## Features

### Core Features
- **Filterable country list**: Administrators can restrict which countries appear in dropdowns based on geographic regions or business needs
- **Multiple widget types**: Dropdown, multi-select, radio buttons, checkboxes
- **SVG flag support**: Display country flags with customizable positions (before, after, or flag-only)
- **Search and filtering**: Real-time search/filter functionality for quick country lookup
- **Multi-language support**: Localized country names with fallback options
- **Value format options**: Store country code, name, or both
- **Custom country entries**: Add custom countries or regions not in standard ISO list
- **Responsive/mobile support**: Touch-optimized for mobile and tablet devices
- **Full accessibility**: WCAG 2.1 compliant with keyboard navigation and screen reader support
- **Integration ready**: Works with Content Types, User profiles, and Views

### Widget Types
- **Select/Dropdown**: Standard HTML select dropdown
- **Multi-select**: Multiple country selection
- **Radio Buttons**: Single selection with radio buttons
- **Checkboxes**: Multiple selection with checkboxes

### Appearance Options
- Show/hide country flags
- Flag position: before name, after name, or flag-only
- Customizable placeholder text
- Search box overlay for long lists
- Stylized UI options

### Configuration Options
- Country filtering (enable/disable specific countries)
- Custom country management
- Default value format (code, name, or both)
- Language handling (site language, native, or fallback)
- Flag library path configuration

## Requirements

- Drupal 10 or 11
- PHP 8.1 or higher
- (Optional) SVG flag library for flag display

## Installation

### Using Composer (Recommended)

```bash
# Install the module
composer require drupal/advanced_country_field

# Optional: Install flag SVG library
composer require npm-asset/country-flag-icons:^1.5
```

### Manual Installation

1. Download or clone this repository:
   ```bash
   git clone https://github.com/ilyas2017/advanced-country-field.git modules/contrib/advanced_country_field
   ```

2. Enable the module:
   ```bash
   drush en advanced_country_field -y
   ```
   Or via admin UI at `/admin/modules`

4. Configure the module at `/admin/config/regional/advanced-country-field`

## Configuration

### Module Settings

1. Navigate to **Configuration > Regional and language > Advanced Country Field Settings** (`/admin/config/regional/advanced-country-field`)

2. Configure:
   - Default value format
   - Language handling
   - Country filtering
   - Flag library path

### Country Filtering

1. Navigate to **Configure Countries** (`/admin/config/regional/advanced-country-field/countries`)
2. Use the search bar to quickly find specific countries in the list
3. Select which countries should be available by checking the checkboxes
4. Use "Select All" or "Deselect All" buttons for quick bulk selection
5. Countries are displayed in an alphabetical list for easy browsing

### Custom Countries

1. Navigate to **Custom Countries** (`/admin/config/regional/advanced-country-field/custom`)
2. Add custom country codes and names for edge cases

## Usage

### Adding to Content Types

1. Go to **Structure > Content types > [Your Type] > Manage fields**
2. Add a new field
3. Select **Field type**: "Advanced Country"
4. Configure widget settings:
   - Choose widget type
   - Enable/disable flags
   - Configure search
   - Set placeholder
5. Configure formatter settings for display

### Field Widget Settings

- **Widget Type**: Select, Multi-select, Radio buttons, Checkboxes
  - **Note**: Widget type availability depends on field cardinality:
    - Single value fields: Only "Dropdown" and "Radio Buttons" are available
    - Multiple/unlimited value fields: Only "Multi-select" and "Checkboxes" are available
    - The widget automatically adjusts based on field cardinality
- **Show Flags**: Enable/disable flag display in widget options
- **Flag Position**: Before name, after name, or flag only
  - When "flag only" is selected, country names are hidden and only flags are displayed
- **Enable Search**: Add real-time search/filter functionality to select/multi-select dropdowns
  - Adds a search input above the dropdown
  - Filters options as you type
  - Fully accessible with ARIA labels
- **Placeholder**: Custom placeholder text for select elements
- **Value Format**: Code, name, or both
  - **Code**: Stores ISO 3166-1 alpha-2 code (e.g., "US") - Recommended
  - **Name**: Stores country name (e.g., "United States")
  - **Both**: Stores both code and name (typically displayed as one or the other)

### Field Formatter Settings

- **Display Format**: Code, name, or both
- **Show Flag**: Enable flag display in output
- **Flag Position**: Control flag placement (before, after, or flag only)
- **Flag Width**: CSS width value (accepts all valid CSS units)
  - Examples: `20px`, `1.5rem`, `2em`, `3vw`, `100%`, `auto`
  - Default: `20px`
- **Flag Height**: CSS height value (accepts all valid CSS units)
  - Examples: `15px`, `1rem`, `1.2em`, `2vh`, `auto`
  - Default: `15px`

## Flag Library Setup

The module uses SVG flags for displaying country flags. The recommended library is `country-flag-icons`.

### Via Composer (Recommended)

Install the flag library using Composer:

```bash
composer require npm-asset/country-flag-icons:^1.5
```

This will install the library into your `vendor/npm-asset/country-flag-icons` directory. The library will typically be available at `/libraries/country-flag-icons/` if you have the `oomphinc/composer-installers-extender` package configured, or you may need to create a symlink.

**After installation:**
1. Navigate to **Configuration > Regional and language > Advanced Country Field Settings** (`/admin/config/regional/advanced-country-field`)
2. Set the **Flag SVG Library Path** to the correct path:
   - For 3x2 aspect ratio flags: `/libraries/country-flag-icons/3x2/`
   - For 1x1 aspect ratio flags: `/libraries/country-flag-icons/1x1/`
   - Or if installed via npm-asset: `/vendor/npm-asset/country-flag-icons/3x2/` (you may need to create a symlink)

### Manual Installation

If you cannot use Composer, you can download the library manually:

```bash
# Create libraries directory
mkdir -p libraries

# Download country-flag-icons
cd libraries
curl -L https://github.com/hampusborgos/country-flags/archive/refs/heads/main.zip -o flags.zip
unzip flags.zip
mv country-flags-main country-flag-icons

# Configure in module settings: /admin/config/regional/advanced-country-field
# Set Flag SVG Library Path to: /libraries/country-flag-icons/3x2/
```

### Alternative Libraries

You can also use other flag libraries:
- `flag-icons` (formerly country-flags): `composer require npm-asset/flag-icons`

## Accessibility

This module is 100% WCAG 2.1 compliant:

- ✅ Keyboard navigation support
- ✅ Screen reader compatibility (ARIA labels)
- ✅ High contrast mode support
- ✅ Focus indicators
- ✅ Semantic HTML
- ✅ Proper form labels and descriptions
- ✅ Skip links where appropriate

## API and Hooks

### Available Hooks

The Advanced Country Field module provides several hooks that allow other modules to extend and customize its functionality.

#### hook_advanced_country_field_countries_alter()

Allows modules to alter the list of available countries before they are used in widgets or forms.

**Parameters:**
- `&$countries` (array): An associative array of country codes (ISO 3166-1 alpha-2) to country names. Passed by reference.
- `$context` (array): Context array with additional information, such as:
  - `langcode`: The language code being used
  - `field_definition`: The field definition object (if applicable)

**Example:**
```php
/**
 * Implements hook_advanced_country_field_countries_alter().
 */
function mymodule_advanced_country_field_countries_alter(array &$countries, array $context) {
  // Remove a country from the list
  unset($countries['XX']);
  
  // Add a custom country
  $countries['XX'] = 'Custom Country';
  
  // Modify an existing country name
  if (isset($countries['CA'])) {
    $countries['CA'] = 'Canada (Modified)';
  }
}
```

**When to use:** Customize the available country list based on business logic, regional restrictions, or other requirements.

---

#### hook_advanced_country_field_widget_options_alter()

Allows modules to alter the widget option display formatting before options are rendered in forms.

**Parameters:**
- `&$options` (array): An associative array of widget options (country_code => formatted_option). Passed by reference.
- `$context` (array): Context array with widget settings, including:
  - `widget_type`: The widget type (select, radios, checkboxes, etc.)
  - `show_flags`: Whether flags are enabled
  - `flag_position`: Flag position setting

**Example:**
```php
/**
 * Implements hook_advanced_country_field_widget_options_alter().
 */
function mymodule_advanced_country_field_widget_options_alter(array &$options, array $context) {
  // Customize option formatting based on widget type
  if ($context['widget_type'] === 'select') {
    foreach ($options as $code => &$option) {
      // Add prefix to all options
      $option = '🌍 ' . $option;
    }
  }
}
```

**When to use:** Customize how country options are displayed in the widget (formatting, prefixes, suffixes, etc.).

---

#### hook_advanced_country_field_validate()

Allows modules to add custom validation for country field values.

**Parameters:**
- `$country_code` (string): The country code to validate (ISO 3166-1 alpha-2).
- `$context` (array): Context array with field and entity information, including:
  - `field_definition`: The field definition
  - `entity`: The entity object (if available)
  - `delta`: The field delta value

**Returns:**
- `bool`: `TRUE` if the country code is valid, `FALSE` otherwise.

**Example:**
```php
/**
 * Implements hook_advanced_country_field_validate().
 */
function mymodule_advanced_country_field_validate($country_code, array $context) {
  // Only allow specific countries for certain entity types
  $allowed_countries = ['US', 'CA', 'MX'];
  
  if ($context['entity']->bundle() === 'restricted_content') {
    return in_array($country_code, $allowed_countries);
  }
  
  return TRUE;
}
```

**When to use:** Add custom validation rules based on business logic, entity type, or other contextual factors.

---

#### hook_advanced_country_field_selected()

Reacts to country selection events. This hook is called when a country is selected in a field.

**Parameters:**
- `$country_code` (string): The selected country code (ISO 3166-1 alpha-2).
- `$context` (array): Context array with field and entity information, including:
  - `field_definition`: The field definition
  - `entity`: The entity object
  - `field_name`: The field machine name
  - `delta`: The field delta value

**Example:**
```php
/**
 * Implements hook_advanced_country_field_selected().
 */
function mymodule_advanced_country_field_selected($country_code, array $context) {
  // Perform actions when a country is selected
  if ($country_code === 'US') {
    // Auto-fill related fields or trigger custom logic
    $context['entity']->set('field_state', 'default_state');
  }
  
  // Log country selections for analytics
  \Drupal::logger('mymodule')->info('Country selected: @code', ['@code' => $country_code]);
}
```

**When to use:** Trigger side effects when countries are selected (auto-fill fields, analytics, notifications, etc.).

---

### Drupal Core Hooks

The following Drupal core hooks can also be used with Advanced Country Field:

#### hook_field_widget_form_alter()

Allows modules to alter forms for field widgets. Can be used to modify the Advanced Country Field widget form.

**Example:**
```php
/**
 * Implements hook_field_widget_form_alter().
 */
function mymodule_field_widget_form_alter(&$element, &$form_state, $context) {
  if ($context['widget']->getPluginId() === 'advanced_country_field_widget') {
    // Add custom validation or modify the form element
    $element['#attributes']['data-custom-attribute'] = 'value';
  }
}
```

**When to use:** Modify the widget form element structure, add custom attributes, or add additional form elements.

---

#### hook_field_formatter_settings_summary_alter()

Allows modules to alter the formatter settings summary. Can be used to customize the display summary shown in the field's "Manage display" configuration.

**Example:**
```php
/**
 * Implements hook_field_formatter_settings_summary_alter().
 */
function mymodule_field_formatter_settings_summary_alter(&$summary, $context) {
  if ($context['formatter']->getPluginId() === 'advanced_country_field_formatter') {
    // Add custom information to the summary
    $summary[] = t('Custom formatting applied');
  }
}
```

**When to use:** Add custom information to the formatter settings summary displayed in the field configuration UI.

## Twig Template Customization

The module provides a Twig template for custom display:

`templates/advanced-country-field-display.html.twig`

### Template Variables

- `country_code`: ISO 3166-1 alpha-2 country code
- `country_name`: Localized country name
- `show_flag`: Boolean indicating if flag should be displayed
- `flag_position`: Flag position ('before', 'after', or 'only')
- `format`: Display format ('code', 'name', or 'both')
- `flag_path`: Path to the flag SVG file

### Override Instructions

1. Copy the template to your theme's `templates` directory
2. Use Drupal's template naming conventions for more specific targeting:
   - `field--node--field-country--article.html.twig` (for article content type)
   - `field--node--field-country.html.twig` (for all node types)
   - `field--advanced-country-field--default.html.twig` (module default)

### Example Override

```twig
{# templates/field--node--field-country.html.twig #}
<div class="custom-country-display">
  {% if show_flag and flag_position == 'before' %}
    <img src="{{ flag_path }}" alt="{{ country_name }} flag" class="country-flag" />
  {% endif %}
  <span class="country-name">{{ country_name }}</span>
  {% if show_flag and flag_position == 'after' %}
    <img src="{{ flag_path }}" alt="{{ country_name }} flag" class="country-flag" />
  {% endif %}
</div>
```

## Service API

The module provides a `CountryDataService` service for programmatic access to country data.

### Accessing the Service

```php
$country_data_service = \Drupal::service('advanced_country_field.country_data');
```

### Available Methods

#### getCountries($filtered = TRUE, $langcode = NULL)

Retrieves a list of all available countries, optionally filtered and localized.

**Parameters:**
- `$filtered` (bool): If `TRUE`, applies country filtering from module configuration. Defaults to `TRUE`.
- `$langcode` (string|NULL): Language code for country names. If `NULL`, uses module's language handling setting.

**Returns:** Array of country codes (ISO 3166-1 alpha-2) => country names.

**Example:**
```php
// Get all countries, unfiltered
$all_countries = $country_data_service->getCountries(FALSE);

// Get filtered countries in French
$french_countries = $country_data_service->getCountries(TRUE, 'fr');

// Get native country names
$native_countries = $country_data_service->getCountries(FALSE, 'native');
```

#### getCountryName($code, $langcode = NULL)

Retrieves the localized name for a specific country code.

**Parameters:**
- `$code` (string): ISO 3166-1 alpha-2 country code (e.g., 'US', 'CA').
- `$langcode` (string|NULL): Language code. If `NULL`, uses module's language handling setting.

**Returns:** Country name string or `NULL` if code not found.

**Example:**
```php
$us_name = $country_data_service->getCountryName('US');
$de_native = $country_data_service->getCountryName('DE', 'native');
```

#### getCustomCountries()

Retrieves custom country entries defined in module configuration.

**Returns:** Array of custom country codes => names.

**Example:**
```php
$custom_countries = $country_data_service->getCustomCountries();
```

#### getFlagPath($code)

Constructs the full path to the SVG flag file for a country code.

**Parameters:**
- `$code` (string): ISO 3166-1 alpha-2 country code.

**Returns:** URL path to the flag SVG file.

**Example:**
```php
$flag_path = $country_data_service->getFlagPath('US');
// Returns: /libraries/country-flag-icons/3x2/us.svg
```

#### clearCache()

Clears all static caches in the service. Typically called automatically when configuration changes.

**Note:** This is a static method. Use `CountryDataService::clearCache()` to call it.

### Performance

The service implements static caching for:
- Processed country lists (filtered and localized)
- Individual country names
- Custom country lists

Caches are automatically cleared when module configuration changes via the `ConfigCacheInvalidator` event subscriber.

## Integration

### Views
The field integrates with Drupal Views for filtering and sorting by country.

### Custom Modules
Use the provided hooks and service (`advanced_country_field.country_data`) to extend functionality.

### Event Subscribers

The module includes a `ConfigCacheInvalidator` event subscriber that automatically clears static caches when module configuration is saved or deleted. This ensures country data remains up-to-date after configuration changes.

**Custom Event Subscribers:**
You can subscribe to `ConfigEvents::SAVE` and `ConfigEvents::DELETE` events to react to configuration changes:
```php
/**
 * Implements hook_event_dispatcher_subscriber().
 */
function mymodule_event_dispatcher_subscriber() {
  return [
    ConfigEvents::SAVE => ['onConfigSave'],
  ];
}

function mymodule_onConfigSave(ConfigCrudEvent $event) {
  if ($event->getConfig()->getName() === 'advanced_country_field.settings') {
    // React to Advanced Country Field configuration changes.
  }
}
```

## JavaScript API

The module provides JavaScript enhancements for better user experience.

### Custom Dropdown Implementation

For select widgets with flags enabled, the module uses a custom JavaScript dropdown implementation because browsers don't support HTML inside `<option>` tags. This allows flags to be displayed in dropdown options.

### Widget JavaScript (`js/widget.js`)

Enhances country field widgets with:
- Real-time search filtering for select/multi-select elements
- Custom dropdown for flag display in select options
- ARIA labels and live regions for accessibility
- Keyboard navigation support

### Admin JavaScript (`js/admin.js`)

Provides client-side filtering for the country filter administration form, similar to Drupal's modules page.

### JavaScript Behaviors

You can extend widget functionality using Drupal behaviors:

```javascript
(function (Drupal, once) {
  Drupal.behaviors.myCustomCountryField = {
    attach: function (context, settings) {
      once('myCustomCountryField', '.advanced-country-field-widget', context).forEach(function (element) {
        // Add custom functionality
        console.log('Custom behavior attached');
      });
    }
  };
})(Drupal, once);
```

## Development

### Code Structure

```
advanced_country_field/
├── src/
│   ├── Plugin/
│   │   ├── Field/
│   │   │   ├── FieldType/           # Field type plugin
│   │   │   ├── FieldWidget/         # Widget plugin
│   │   │   └── FieldFormatter/      # Formatter plugin
│   ├── Form/                        # Configuration forms
│   │   ├── AdvancedCountryFieldSettingsForm.php
│   │   ├── CountryFilterForm.php
│   │   └── CustomCountryForm.php
│   ├── Service/                      # Country data service
│   │   └── CountryDataService.php
│   ├── EventSubscriber/             # Event subscribers
│   │   └── ConfigCacheInvalidator.php
│   └── Controller/                   # Controllers (if needed)
├── css/                              # Stylesheets
│   ├── widget.css                    # Widget styles
│   ├── formatter.css                 # Formatter styles
│   ├── flags.css                     # Flag display styles
│   └── admin.css                     # Administration styles
├── js/                               # JavaScript
│   ├── widget.js                     # Widget enhancements
│   └── admin.js                      # Admin form enhancements
├── templates/                        # Twig templates
│   └── advanced-country-field-display.html.twig
├── config/                           # Configuration schemas
│   ├── install/                      # Default configuration
│   └── schema/                       # Configuration schemas
├── tests/                            # Test suite
│   ├── src/
│   │   ├── Unit/                     # Unit tests
│   │   ├── Kernel/                    # Kernel tests
│   │   └── Functional/                # Functional tests
│   └── README.md                      # Test documentation
└── advanced_country_field.module      # Hook implementations
```

### Key Implementation Details

#### Field Cardinality Handling

The widget automatically adjusts available widget types based on field cardinality:
- **Cardinality = 1**: Only single-value widgets (Dropdown, Radio Buttons)
- **Cardinality > 1**: Only multi-value widgets (Multi-select, Checkboxes)
- **Cardinality = -1 (Unlimited)**: Multi-value widgets available

#### Caching Strategy

The module uses static caching in `CountryDataService` for performance:
- Country lists are cached per language and filter combination
- Individual country names are cached
- Custom countries are cached
- Caches are automatically invalidated via event subscriber when configuration changes

#### Native Country Names

The module includes a comprehensive database of native country names in their official languages (e.g., "Deutschland" for Germany, "España" for Spain, "中国" for China). When "Use native country name" is selected in language handling, these native names are displayed instead of translated or English names.

## Troubleshooting

### Flags not displaying
- Verify flag library is installed at the configured path
- Check that flag files are named correctly (lowercase country code + .svg)
- Ensure flag_library_path setting is correct
- Check file permissions on the flag library directory
- Clear Drupal cache: `drush cr`

### Search not working
- Verify JavaScript is enabled
- Check for JavaScript conflicts
- Ensure widget library is properly loaded
- Check browser console for JavaScript errors

### Field not appearing in content type
- Ensure the module is enabled
- Clear cache: `drush cr`
- Check field permissions

### Language translations not appearing
- Verify the language handling setting matches your needs
- Check that language modules are enabled
- Clear cache after language setup changes

### Native country names not showing
- Ensure "Language Handling" is set to "Use native country name" in module settings
- Native names are available for most countries but may fallback to English for some
- Clear cache after changing language handling settings

### Widget type not available
- Widget type selection is restricted by field cardinality:
  - Single-value fields can only use Dropdown or Radio Buttons
  - Multiple-value fields can only use Multi-select or Checkboxes
- Change field cardinality in field storage settings if you need different widget types

### Custom dropdown not displaying flags
- Ensure JavaScript is enabled in the browser
- Check that the widget library is properly attached
- Verify flag library path is correct and flags exist
- Clear browser cache and Drupal cache

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow [Drupal coding standards](https://www.drupal.org/docs/develop/standards)
- Run PHPCS: `vendor/bin/phpcs --standard=Drupal,DrupalPractice modules/contrib/advanced_country_field`
- Document all functions with PHPDoc
- Write descriptive commit messages
- Ensure all tests pass before submitting

### Testing

The module includes a comprehensive test suite with Unit, Kernel, and Functional tests. See `tests/README.md` for detailed testing documentation.

```bash
# Run all tests via Drush (requires fully installed Drupal site)
drush test-run advanced_country_field

# Run tests via PHPUnit in Docker container
docker exec my_drupal11_project_php ./vendor/bin/phpunit -c web/core/phpunit.xml.dist --group advanced_country_field

# Run specific test suites
docker exec my_drupal11_project_php ./vendor/bin/phpunit -c web/core/phpunit.xml.dist --group advanced_country_field --testsuite unit
docker exec my_drupal11_project_php ./vendor/bin/phpunit -c web/core/phpunit.xml.dist --group advanced_country_field --testsuite kernel

# Run coding standards check
composer install && vendor/bin/phpcs --standard=Drupal,DrupalPractice modules/contrib/advanced_country_field

# Run PHPStan static analysis
vendor/bin/phpstan analyze modules/contrib/advanced_country_field
```

**Test Coverage:**
- ✅ Unit tests: `CountryDataService` (10 tests, 61 assertions)
- ✅ Kernel tests: Field type, settings form, field storage, cache invalidation
- ✅ Functional tests: Module installation and basic configuration

## Module Uninstallation

When uninstalling the module, the `hook_uninstall()` implementation:
- Deletes module-specific configuration
- Clears all caches
- **Note**: Field instances and data are preserved for safety. Administrators should manually remove fields before uninstalling if needed to prevent accidental data loss.

## Changelog

### 1.0.0 (2024-12-16)
- Initial release
- Support for Drupal 10 and 11
- Multiple widget types (select, multi-select, radio, checkboxes)
- SVG flag support with customizable positioning
- Country filtering and custom country management
- Multi-language support with native country names
- Full WCAG 2.1 accessibility compliance
- Integration with Views and custom modules
- Comprehensive Service API (`CountryDataService`)
- Automatic cache invalidation via event subscriber
- Static caching for improved performance
- Custom JavaScript dropdown for flag display in select options
- Field cardinality-aware widget selection
- Unit, Kernel, and Functional test coverage

## Roadmap

- [ ] More flag library options
- [ ] Additional language packs
- [ ] Enhanced search with autocomplete
- [ ] Continent/region grouping
- [ ] Country data export/import
- [ ] Migration path from Addressfield module

## Support

- **Documentation**: See inline code documentation
- **Issue Tracker**: https://github.com/ilyas2017/advanced-country-field/issues
- **Drupal.org Project**: https://www.drupal.org/project/advanced_country_field

## Authors and Maintainers

- Developed by the Drupal community
- Maintained by contributors

## License

This project is licensed under the GNU General Public License, version 2 or later.

GPL-2.0-or-later

See [LICENSE.txt](LICENSE.txt) for the full license text.

## Credits

- [Drupal Community](https://www.drupal.org/community) for the fantastic CMS
- [Country Flags by Hampus Borgås](https://github.com/hampusborgos/country-flags) for the flag library
- All contributors who helped improve this module

## Donate

If you find this module useful, please consider:
- Contributing code improvements
- Reporting bugs
- Creating documentation
- Supporting Drupal development

