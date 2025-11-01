# Quick Start Guide

Get up and running with Advanced Country Field in 5 minutes.

## Prerequisites

- ✅ Drupal 10 or 11 installed
- ✅ PHP 8.1 or higher
- ✅ Composer installed (recommended)

## Installation

### Step 1: Install the Module

```bash
composer require drupal/advanced_country_field
drush en advanced_country_field -y
```

### Step 2: Clear Cache

```bash
drush cr
```

## Basic Usage

### Adding a Country Field to a Content Type

1. Go to **Structure > Content types**
2. Click "Manage fields" on your content type
3. Click "Add field"
4. Select **Field type**: "Advanced Country"
5. Enter field name (e.g., "Country")
6. Click "Save and continue"

### Configure Widget

On the widget settings page:

1. Choose **Widget Type**: Dropdown (Select)
2. (Optional) Check "Show country flags"
3. (Optional) Check "Enable search"
4. Click "Save settings"

### Configure Display

On the manage display page:

1. Find your field
2. Click settings gear
3. Choose **Display Format**: Name
4. (Optional) Check "Show flag"
5. Click "Update"
6. Click "Save"

## Test It Out

1. Create or edit content
2. Select a country from the dropdown
3. Save the content
4. View the page to see the result

## Optional: Install Flag Library

For flag display:

```bash
composer require npm-asset/country-flag-icons
```

Configure the path in module settings:
- Go to `/admin/config/regional/advanced-country-field`
- Set **Flag Library Path**: `/libraries/country-flag-icons/3x2/`
- Click "Save configuration"

## Next Steps

- [Full Installation Guide](INSTALLATION.md)
- [Configuration Guide](CONFIGURATION.md)
- [Developer Documentation](DEVELOPER.md)

## Getting Help

- [Documentation](../advanced_country_field/README.md)
- [Issue Tracker](https://github.com/ilyas2017/advanced-country-field/issues)

That's it! You're ready to use Advanced Country Field. 🎉

