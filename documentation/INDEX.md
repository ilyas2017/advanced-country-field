# Documentation Index

Complete index of all documentation for Advanced Country Field module.

## 📚 Documentation Structure

### Getting Started

- **[Quick Start Guide](QUICKSTART.md)** - Get up and running in 5 minutes
  - Minimal setup
  - Basic usage
  - First steps

### User Documentation

#### Installation & Setup

- **[Installation Guide](INSTALLATION.md)** - Complete installation
  - Composer installation
  - Manual installation
  - Flag library setup
  - Troubleshooting

#### Configuration

- **[Configuration Guide](CONFIGURATION.md)** - All settings explained
  - General settings
  - Country filtering
  - Custom countries
  - Widget configuration
  - Formatter configuration
  - Best practices

#### Migration

- **[Migration Guide](MIGRATION.md)** - Migrate from other modules
  - From Address field
  - From Country field
  - Data mapping
  - Migration scripts
  - Testing procedures

### Developer Documentation

#### API Reference

- **[API Reference](API.md)** - Complete API documentation
  - Service API
  - Hook API
  - Field Plugin API
  - Configuration API
  - JavaScript API
  - Data structures

#### Customization

- **[Developer Guide](DEVELOPER.md)** - Customization and examples
  - Architecture overview
  - Custom widget development
  - Custom formatter development
  - JavaScript customization
  - CSS customization
  - Twig template override
  - Testing guide
  - Debugging tips

### Additional Resources

- **[FAQ](FAQ.md)** - Frequently asked questions and answers
- **[Module README](../advanced_country_field/README.md)** - Complete module documentation
- **[Changelog](CHANGELOG.md)** - Version history
- **[Contributing Guide](CONTRIBUTING.md)** - Contribution guidelines

## 🎯 Documentation by Role

### End Users

**Getting Started:**
1. [Quick Start](QUICKSTART.md)
2. [Installation](INSTALLATION.md)
3. [Configuration](CONFIGURATION.md)

**Common Tasks:**
- Add country field to content type
- Configure widget settings
- Set up flags
- Filter countries
- Troubleshoot issues

### Site Builders

**Configuration:**
- [Configuration Guide](CONFIGURATION.md) - Full guide
- [Installation Guide](INSTALLATION.md) - Setup steps

**Advanced:**
- [Migration Guide](MIGRATION.md) - Data migration
- [Module README](../advanced_country_field/README.md) - Full documentation

### Developers

**API:**
- [API Reference](API.md) - Complete API
- [Developer Guide](DEVELOPER.md) - Examples

**Customization:**
- [Developer Guide - Custom Widget](DEVELOPER.md#custom-widget)
- [Developer Guide - Custom Formatter](DEVELOPER.md#custom-formatter)
- [Developer Guide - Testing](DEVELOPER.md#testing)

## 📖 Documentation by Topic

### Installation
- [Quick Start](QUICKSTART.md) - Fast setup
- [Installation Guide](INSTALLATION.md) - Detailed steps
- [Configuration Guide](CONFIGURATION.md) - Post-installation

### Configuration
- [Configuration Guide](CONFIGURATION.md) - Complete guide
- [General Settings](CONFIGURATION.md#general-settings)
- [Country Filtering](CONFIGURATION.md#country-filtering)
- [Field Configuration](CONFIGURATION.md#field-configuration)

### Flags
- [Installation - Flag Library Setup](INSTALLATION.md#installing-flag-libraries)
- [Configuration - Flag Settings](CONFIGURATION.md#flag-settings)
- [Troubleshooting](INSTALLATION.md#troubleshooting)

### Development
- [API Reference](API.md) - Complete API
- [Developer Guide](DEVELOPER.md) - Customization
- [Contributing](CONTRIBUTING.md) - Guidelines (in this directory)
- [Testing](DEVELOPER.md#testing)

### Migration
- [Migration Guide](MIGRATION.md) - Complete guide
- [Data Mapping](MIGRATION.md#data-mapping)
- [Migration Scripts](MIGRATION.md#migration-script-template)

## 🔍 Quick Reference

### Configuration Files

- **Module Info**: `../advanced_country_field/advanced_country_field.info.yml`
- **Services**: `../advanced_country_field/advanced_country_field.services.yml`
- **Routing**: `../advanced_country_field/advanced_country_field.routing.yml`
- **Permissions**: `../advanced_country_field/advanced_country_field.permissions.yml`
- **Libraries**: `../advanced_country_field/advanced_country_field.libraries.yml`
- **Schema**: `../advanced_country_field/config/schema/advanced_country_field.schema.yml`
- **Install**: `../advanced_country_field/config/install/advanced_country_field.settings.yml`

### Important Classes

- **Field Type**: `AdvancedCountryFieldItem`
- **Widget**: `AdvancedCountryFieldWidget`
- **Formatter**: `AdvancedCountryFieldFormatter`
- **Service**: `CountryDataService`

### Key URLs

- Settings: `/admin/config/regional/advanced-country-field`
- Filter Countries: `/admin/config/regional/advanced-country-field/countries`
- Custom Countries: `/admin/config/regional/advanced-country-field/custom`

### Important Commands

```bash
# Enable module
drush en advanced_country_field -y

# Clear cache
drush cr

# Install flags
composer require npm-asset/country-flag-icons

# Run tests
drush test-run advanced_country_field
```

## 📊 Documentation Statistics

- **Total Documents**: 10
- **Pages**: 7 guides + 3 project files
- **Total Lines**: 2000+
- **Code Examples**: 50+
- **API Methods**: 10+
- **Hooks**: 5+

## 🔄 Updates

Last updated: 2024-12-16

Documentation is actively maintained. For latest updates, check the [module repository](https://github.com/ilyas2017/advanced-country-field).

## 🤝 Contributing to Docs

Found an error or want to improve documentation?

1. Open an issue on GitHub
2. Submit a pull request
3. See [Contributing Guide](CONTRIBUTING.md)

---

Happy coding! 🎉

