# Changelog

All notable changes to the Advanced Country Field module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-12-16

### Added
- Initial release
- Support for Drupal 10 and 11
- Multiple widget types (select, multi-select, radio buttons, checkboxes)
- SVG flag support with customizable positioning (before, after, or flag-only)
- Country filtering functionality
- Custom country management
- Multi-language support with native country names
- Real-time search and filtering
- Full WCAG 2.1 accessibility compliance
- Integration with Views
- Comprehensive API with hooks for extensibility
- Configuration forms for module settings
- Field widget with multiple display options
- Field formatter with flag display support
- Responsive design for mobile and tablet devices
- Keyboard navigation and screen reader support
- Documentation and code examples

### Technical Details

**Field Type Plugin**
- `AdvancedCountryFieldItem`: Field type for storing country data
- ISO 3166-1 alpha-2 country codes
- Storage for country code and name
- Proper schema definitions

**Widget Plugin**
- `AdvancedCountryFieldWidget`: Multiple widget types
- Cardinality-based widget selection
- Flag display with custom JavaScript
- Search functionality
- Configurable settings

**Formatter Plugin**
- `AdvancedCountryFieldFormatter`: Display formatting
- Flag size customization (width/height)
- Multiple display formats
- CSS customization support

**Service**
- `CountryDataService`: Centralized country data management
- Native country names in multiple languages
- Configuration-based filtering
- Custom country support
- Flag path resolution

**Configuration Forms**
- Settings form for general configuration
- Country filter form with search
- Custom country management form

**Hooks**
- `hook_advanced_country_field_country_list_alter()`
- `hook_advanced_country_field_custom_country_info_alter()`
- Additional hooks for extensibility

**Libraries**
- Widget JavaScript for custom dropdown with flags
- Admin JavaScript for country filtering
- CSS for styling and flags
- Drupal core library dependencies

## [Unreleased]

### Planned Features

- Webform element integration
- More flag library options
- Additional language packs
- Enhanced search with autocomplete
- Continent/region grouping
- Country data export/import functionality
- Migration path from Addressfield module
- REST API support
- GraphQL integration

### Improvements

- Performance optimizations
- Additional unit tests
- Integration tests
- Browser compatibility enhancements
- Documentation improvements

---

[1.0.0]: https://github.com/ilyas2017/advanced-country-field/releases/tag/1.0.0

