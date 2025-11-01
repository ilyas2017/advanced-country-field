# Frequently Asked Questions (FAQ)

Common questions and answers about Advanced Country Field.

## General

### What is Advanced Country Field?

Advanced Country Field is a Drupal field module providing enhanced country selection with flags, filtering, search, multi-language support, and extensive customization options.

### What Drupal versions are supported?

- Drupal 10.x
- Drupal 11.x

### What PHP version is required?

PHP 8.1 or higher is required.

### Is it compatible with...?

**Views**: ✅ Yes, fully integrated  
**Webform**: ❌ Not yet (planned)  
**Address Field**: ⚠️ Separate module but can migrate data  
**Entity Reference**: ✅ Works with entity-based references  

## Installation

### Can I install without Composer?

Yes, you can download and install manually. See [Installation Guide](INSTALLATION.md).

### Do I need to install a flag library?

No, flag libraries are optional. The module works without them if you don't need flag display.

### Which flag library should I use?

We recommend `country-flag-icons` as it's actively maintained and works well with the module.

## Configuration

### How do I restrict which countries appear?

1. Enable "Enable country filtering" in settings
2. Go to Configure Countries
3. Select desired countries
4. Save

### Can I use custom country codes?

Yes! Navigate to Custom Countries to add countries not in the standard ISO list.

### What's the difference between "Site language" and "Native country names"?

- **Site language**: Translations in your site's language (e.g., "États-Unis" in French)
- **Native names**: Country names in their official language (e.g., "Deutschland" for Germany)
- **Fallback**: English names as default

### Can I change the flag size?

Yes! In formatter settings, configure `flag_width` and `flag_height` with any CSS unit.

## Usage

### Which widget should I use?

- **Dropdown**: Best for long lists
- **Radio**: Better UX for 10 or fewer options
- **Multi-select**: Multiple selections
- **Checkboxes**: Visual multiple selection

### How do I add search to my dropdown?

Enable "Enable search" in widget settings.

### Can I hide the country name and show only the flag?

Yes! Set "Flag Position" to "Flag only" in widget/display settings.

### How do I customize the field appearance?

You can:
1. Override CSS in your theme
2. Override Twig templates
3. Use CSS custom properties

See [Developer Guide - Customization](DEVELOPER.md#customization).

## Development

### How do I programmatically get country data?

```php
$service = \Drupal::service('advanced_country_field.country_data');
$countries = $service->getCountries();
$country_name = $service->getCountryName('US');
```

See [API Reference](API.md).

### What hooks are available?

- `hook_advanced_country_field_country_list_alter()` - Alter country list
- `hook_advanced_country_field_custom_country_info_alter()` - Alter custom countries
- Plus Drupal core field hooks

See [API Reference - Hooks](API.md#hook-api).

### Can I create custom widgets?

Yes! Extend `AdvancedCountryFieldWidget`. See [Developer Guide](DEVELOPER.md#custom-widget).

### How do I write tests?

See [Developer Guide - Testing](DEVELOPER.md#testing).

## Troubleshooting

### Flags are not displaying

Check:
1. Flag library installed
2. Path configured correctly
3. Files named `{code}.svg` (lowercase)
4. Files accessible via HTTP
5. "Show flags" enabled
6. Cache cleared

See [Installation Guide - Troubleshooting](INSTALLATION.md#troubleshooting).

### Search is not working

Verify:
1. JavaScript enabled
2. No console errors
3. Widget library loaded
4. Browser cache cleared

### Field not saving values

Check:
1. Field cardinality matches widget type
2. Required field permissions
3. Form validation
4. Check browser console for errors

### Language translations not appearing

Verify:
1. Language handling setting
2. Language modules enabled
3. Translations loaded
4. Cache cleared

## Migration

### Can I migrate from Address Field?

Yes! See [Migration Guide](MIGRATION.md) for detailed steps.

### How do I migrate custom country data?

See [Migration Guide - Custom Countries](MIGRATION.md#data-mapping).

### Will I lose my existing data?

No, if you follow the migration guide properly. Always backup first!

## Accessibility

### Is the module accessible?

Yes! The module is 100% WCAG 2.1 compliant.

### What accessibility features are included?

- Keyboard navigation
- Screen reader support
- ARIA labels
- High contrast mode
- Focus indicators
- Semantic HTML

### Can I use it with screen readers?

Yes, the module is designed for screen reader compatibility.

## Performance

### Does flag display impact performance?

Minimal impact with SVG flags. Flags are loaded on-demand.

### Can I disable flags to improve performance?

Yes, simply uncheck "Show flags" in settings.

### How many countries are included?

249 standard ISO 3166-1 alpha-2 countries plus custom countries you add.

## Customization

### Can I style the dropdown?

Yes! Override CSS in your theme. See [Developer Guide - CSS](DEVELOPER.md#custom-css).

### Can I change how countries are displayed?

Yes! Override Twig templates. See [Developer Guide - Twig](DEVELOPER.md#twig-template-override).

### Can I add my own countries?

Yes! Use the Custom Countries configuration form.

### Can I modify the country list programmatically?

Yes! Use `hook_advanced_country_field_country_list_alter()`. See [API Reference](API.md).

## Support

### Where can I get help?

- [GitHub Issues](https://github.com/ilyas2017/advanced-country-field/issues)
- [Documentation Index](INDEX.md)
- [Drupal.org Project Page](https://www.drupal.org/project/advanced_country_field)

### How do I report a bug?

Open an issue on GitHub with:
- Drupal version
- PHP version
- Module version
- Steps to reproduce
- Expected vs actual behavior

### Can I contribute?

Yes! See [Contributing Guide](../CONTRIBUTING.md).

## License

### What license does the module use?

GNU General Public License, version 2 or later (GPL-2.0-or-later).

### Can I use it commercially?

Yes! GPL allows commercial use.

## Roadmap

### What features are planned?

See [Roadmap](README.md#roadmap) in the main README.

### Will there be Webform integration?

Yes, it's planned for a future release.

## Still have questions?

- Check the [Documentation Index](INDEX.md)
- Search [GitHub Issues](https://github.com/ilyas2017/advanced-country-field/issues)
- Open a new issue for your question

