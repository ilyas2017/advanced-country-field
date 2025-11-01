# Configuration Guide

Complete configuration guide for Advanced Country Field module.

## Overview

Advanced Country Field provides extensive configuration options for customizing the country selection experience. This guide covers all configuration settings and their use cases.

## Accessing Configuration

Navigate to the main configuration page:

```
/admin/config/regional/advanced-country-field
```

## General Settings

### Default Value Format

**Path**: Configuration > Regional and language > Advanced Country Field Settings > General Settings

**Options**:
- **Country code only** (e.g., "US")
- **Country name only** (e.g., "United States")
- **Both code and name** (e.g., "US - United States")

**Default**: Code only

**Use Case**: Determines what value is stored in the database by default when creating new fields.

### Language Handling

**Path**: Configuration > Regional and language > Advanced Country Field Settings > General Settings

**Options**:
- **Use site language**: Country names translated to the site's current language
- **Use native country name**: Display country names in their official language
- **Fallback to English**: Use English names if translation unavailable

**Default**: Site language

**Use Case**: Controls how country names are localized across the site.

**Examples**:

**Site Language (French)**:
- United States → États-Unis
- Germany → Allemagne

**Native Names**:
- United States → United States (English)
- Germany → Deutschland (German)
- Japan → 日本 (Japanese)

## Country Filtering

### Enable Country Filtering

**Path**: Configuration > Regional and language > Advanced Country Field Settings > Country Filtering

**Purpose**: Allow administrators to restrict which countries appear in fields.

**Use Cases**:
- Business-only countries for shipping
- Regional restrictions
- Compliance requirements

### Configure Countries

**Path**: Configuration > Regional and language > Advanced Country Field Settings > Configure Countries

URL: `/admin/config/regional/advanced-country-field/countries`

**Features**:
- Search/filter countries
- Select all / Deselect all
- Visual selection interface

**Steps**:
1. Check "Enable country filtering" in general settings
2. Navigate to "Configure Countries"
3. Use search to find countries
4. Select desired countries
5. Click "Save configuration"

**Example**: E-commerce site only shipping to:
- United States
- Canada
- Mexico

## Custom Countries

**Path**: Configuration > Regional and language > Advanced Country Field Settings > Custom Countries

URL: `/admin/config/regional/advanced-country-field/custom`

**Purpose**: Add custom countries or regions not in the standard ISO 3166-1 list.

**Use Cases**:
- Historical countries
- Special territories
- Custom regions

**Adding Custom Countries**:
1. Navigate to Custom Countries page
2. Enter country code (2-5 characters, uppercase)
3. Enter country name
4. Click "Add Custom Country" or "Save configuration"

**Example**:
- Code: `EU`
- Name: `European Union`

**Limitations**:
- Maximum 10 custom countries
- Codes must be unique
- Flags must be added manually to flag library

## Flag Settings

### Flag Library Path

**Path**: Configuration > Regional and language > Advanced Country Field Settings > Flag Settings

**Format**: `/path/to/library/`

**Defaults**:
- `country-flag-icons` 3x2: `/libraries/country-flag-icons/3x2/`
- `country-flag-icons` 1x1: `/libraries/country-flag-icons/1x1/`
- `flag-icons`: `/libraries/flag-icons/`

**Custom Paths**:
You can use any directory accessible via web:
- `/sites/default/files/flags/svg/`
- `/custom/flags/`

**Important**: 
- Path must end with `/`
- Files must be named `{lowercase_code}.svg` (e.g., `us.svg`, `gb.svg`)
- Files must be accessible via HTTP/HTTPS

## Field Configuration

### Widget Configuration

When adding a field to a content type, configure the widget:

**Access**: Structure > Content types > [Your Type] > Manage fields > Add field > Advanced Country > Widget settings

#### Widget Type

**Options**:
- **Dropdown (Select)**: Standard HTML select
  - Single value only
  - Minimal space
  - Good for long lists

- **Multi-select Dropdown**: Multiple selection
  - Multiple values (cardinality > 1)
  - List display
  - Good for many selections

- **Radio Buttons**: Visual selection
  - Single value only
  - Better UX for few options
  - Mobile-friendly

- **Checkboxes**: Visual multi-selection
  - Multiple values
  - Better UX
  - Mobile-friendly

**Cardinality Considerations**:

| Cardinality | Available Widgets |
|-------------|-------------------|
| 1 (Single) | Select, Radios |
| 2-10 (Limited) | Multi-select, Checkboxes |
| Unlimited | All widgets |

#### Show Country Flags

**Default**: Disabled

**Effects**:
- Displays SVG flags next to country names
- Requires flag library installed
- Adds visual appeal

#### Flag Position

**Options**:
- **Before country name**: 🇺🇸 United States
- **After country name**: United States 🇺🇸
- **Flag only**: 🇺🇸

**Best Practices**:
- Use "Before" for consistent alignment
- Use "After" for right-to-left languages
- Use "Only" with sufficient flag size

#### Enable Search

**Default**: Disabled

**Effects**:
- Adds search input to dropdown
- Filters countries in real-time
- Improves UX for long lists

**Recommended**: Enable for 20+ countries

#### Placeholder Text

**Default**: "Select a country..."

**Customization**: Can include plain text

**Examples**:
- "Choose your country"
- "Where are you located?"
- "Country of residence"

#### Value Format

**Options**:
- **Code**: Store ISO code only
- **Name**: Store full name
- **Both**: Store code and name

**Database Impact**:

| Format | `country_code` | `country_name` |
|--------|----------------|----------------|
| Code | US | NULL |
| Name | US | United States |
| Both | US | United States |

**Recommended**: Code (most efficient, translatable)

### Formatter Configuration

Configure how the field displays on the frontend:

**Access**: Structure > Content types > [Your Type] > Manage display > [Your Field] > Advanced Country > Settings

#### Display Format

**Options**:
- **Code**: US
- **Name**: United States
- **Both**: US - United States

**Examples**:

**Code**:
```
US
```

**Name**:
```
United States
```

**Both**:
```
US - United States
```

#### Show Flag

**Default**: Disabled

Enable flag display in rendered output.

#### Flag Position

**Options**:
- Before, After, Only

Match widget position for consistency.

#### Flag Width

**Default**: 20px

**Units Supported**:
- `px` - Pixels
- `em` - Relative to font size
- `rem` - Relative to root font size
- `%` - Percentage
- `vh` / `vw` - Viewport units
- `auto` - Automatic

**Examples**:
- `20px` - Fixed 20 pixels
- `1.5em` - 1.5 times font size
- `2rem` - 2 times root font size
- `100%` - Full width of container
- `3vh` - 3% of viewport height
- `auto` - Maintain aspect ratio

**Validation**: Any valid CSS dimension value

#### Flag Height

**Default**: 15px

**Options**: Same as width

**Best Practices**:
- Keep aspect ratio (typically 3:2 or 1:1)
- Use relative units for responsiveness
- Consider accessibility minimums

**Examples**:
- `16px` × `12px` (4:3 ratio)
- `20px` × `15px` (4:3 ratio)
- `24px` × `16px` (3:2 ratio)
- `1em` × `0.75em` (responsive)

## Advanced Configuration

### Multi-Language Sites

For sites with multiple languages:

1. **Configure Language Handling**: Use "Site language"
2. **Enable Translation**: Install and configure translation modules
3. **Translate Country Names**: Use Drupal's translation interface
4. **Test**: Switch languages to verify

### Views Integration

Add country field to views:

1. Create/edit view
2. Add field: Advanced Country Field
3. Configure formatter settings
4. Use filters for country-based filtering
5. Use sorting for alphabetical organization

**Example**: Filter by country in node views:
```
Filter: Advanced Country Field: Country Code = US
```

### Programmatic Configuration

Configure settings via code:

```php
use Drupal\Core\Config\ConfigFactoryInterface;

// Get configuration
$config = \Drupal::configFactory()->getEditable('advanced_country_field.settings');

// Set language handling
$config->set('language_handling', 'native')->save();

// Enable filtering
$config->set('filter_enabled', TRUE)->save();

// Set flag path
$config->set('flag_library_path', '/custom/flags/')->save();
```

### Bulk Country Configuration

Enable/disable countries programmatically:

```php
use Drupal\Core\Config\ConfigFactoryInterface;

$config = \Drupal::configFactory()->getEditable('advanced_country_field.settings');

// Enable specific countries
$config->set('enabled_countries', [
  'US', 'CA', 'MX', 'GB', 'FR', 'DE', 'IT', 'ES'
])->save();
```

## Configuration Best Practices

### Performance

- ✅ Disable flags if not needed
- ✅ Use country code format for storage
- ✅ Limit enabled countries when possible
- ✅ Enable caching
- ✅ Use search for long lists

### Accessibility

- ✅ Use clear placeholder text
- ✅ Ensure sufficient flag sizes
- ✅ Test with screen readers
- ✅ Provide text alternatives
- ✅ Maintain keyboard navigation

### User Experience

- ✅ Match widget to use case
- ✅ Use appropriate cardinality
- ✅ Provide helpful placeholder text
- ✅ Enable search for 20+ countries
- ✅ Test on mobile devices

### Security

- ✅ Validate custom country codes
- ✅ Sanitize user input
- ✅ Use HTTPS for flag libraries
- ✅ Review file permissions
- ✅ Keep module updated

## Troubleshooting

### Configuration Not Saving

**Symptom**: Settings not persisting after save

**Solutions**:
1. Check file permissions
2. Verify database connectivity
3. Check for JavaScript errors
4. Clear Drupal cache: `drush cr`

### Flags Not Displaying

**Symptom**: Flags not showing in fields

**Checklist**:
- [ ] Flag library installed
- [ ] Path configured correctly
- [ ] Files named correctly (lowercase.svg)
- [ ] Files accessible via HTTP
- [ ] "Show flags" enabled in widget
- [ ] Cache cleared

### Filtering Not Working

**Symptom**: Disabled countries still appear

**Solutions**:
1. Verify "Enable country filtering" is checked
2. Clear cache: `drush cr`
3. Re-configure enabled countries
4. Check configuration is saved

## Next Steps

- [Developer Guide](DEVELOPER.md) - API and customization
- [Full Documentation](../advanced_country_field/README.md)
- [Installation Guide](INSTALLATION.md)

## Getting Help

- [Issue Tracker](https://github.com/ilyas2017/advanced-country-field/issues)
- [Support Documentation](README.md#support)

