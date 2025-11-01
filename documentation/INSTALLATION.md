# Installation Guide

Complete installation guide for Advanced Country Field module.

## Prerequisites

Before installing the module, ensure you have:

- **Drupal 10.x** or **11.x** installed
- **PHP 8.1** or higher
- **Composer** installed (recommended)
- **Drush** installed (optional but recommended)

## Installation Methods

### Method 1: Composer (Recommended)

Composer is the recommended method for installing Drupal modules as it manages dependencies automatically.

#### Step 1: Install via Composer

Navigate to your Drupal root directory and run:

```bash
composer require drupal/advanced_country_field
```

This command will:
- Download the module
- Place it in `web/modules/contrib/advanced_country_field`
- Handle all dependencies automatically

#### Step 2: Enable the Module

Using Drush:
```bash
drush en advanced_country_field -y
```

Or via the admin interface:
1. Navigate to `/admin/modules`
2. Search for "Advanced Country Field"
3. Check the box
4. Click "Install"

#### Step 3: Configure the Module

Navigate to the configuration page:
```
/admin/config/regional/advanced-country-field
```

### Method 2: Manual Installation

For environments without Composer access or for development purposes.

#### Step 1: Download the Module

Clone the repository:
```bash
cd web/modules/contrib
git clone https://github.com/ilyas2017/advanced-country-field.git advanced_country_field
```

Or download the latest release:
```bash
cd web/modules/contrib
wget https://github.com/ilyas2017/advanced-country-field/archive/refs/tags/1.0.0.zip
unzip 1.0.0.zip
mv advanced-country-field-1.0.0 advanced_country_field
rm 1.0.0.zip
```

#### Step 2: Set Permissions

Ensure proper file permissions:
```bash
chmod -R 755 web/modules/contrib/advanced_country_field
```

#### Step 3: Enable the Module

Using Drush:
```bash
drush en advanced_country_field -y
drush cr
```

Or via the admin interface at `/admin/modules`

### Method 3: Drupal.org (When Available)

Once published on Drupal.org:

```bash
drush dl advanced_country_field
drush en advanced_country_field -y
```

Or via Composer:
```bash
composer require drupal/advanced_country_field:^1.0
```

## Post-Installation

### Verify Installation

1. Navigate to `/admin/modules`
2. Confirm "Advanced Country Field" is listed and enabled
3. Check the status shows "Installed"

### Clear Cache

After installation, always clear Drupal cache:

Using Drush:
```bash
drush cr
```

Or via admin interface:
1. Navigate to `/admin/config/development/performance`
2. Click "Clear all caches"

### Initial Configuration

Navigate to the configuration page to complete setup:

```
/admin/config/regional/advanced-country-field
```

Configure:
- Default value format
- Language handling
- Country filtering
- Flag library path

## Installing Flag Libraries

Flag libraries are optional but recommended for flag display functionality.

### Via Composer

```bash
composer require npm-asset/country-flag-icons
```

Or add to your `composer.json`:
```json
{
  "require": {
    "npm-asset/country-flag-icons": "^1.5"
  }
}
```

### Manual Installation

1. Create libraries directory (if it doesn't exist):
   ```bash
   mkdir -p web/libraries
   ```

2. Download the library:
   ```bash
   cd web/libraries
   curl -L https://github.com/hampusborgos/country-flags/archive/refs/heads/main.zip -o flags.zip
   unzip flags.zip
   mv country-flags-main country-flag-icons
   rm flags.zip
   ```

3. Set permissions:
   ```bash
   chmod -R 755 web/libraries/country-flag-icons
   ```

### Configure Library Path

In module configuration:
1. Go to `/admin/config/regional/advanced-country-field`
2. Find "Flag SVG Library Path"
3. Enter: `/libraries/country-flag-icons/3x2/`
4. Save configuration

## Troubleshooting

### Module Not Appearing

If the module doesn't appear in `/admin/modules`:

1. **Check file permissions**:
   ```bash
   ls -la web/modules/contrib/advanced_country_field
   ```

2. **Verify directory structure**:
   ```bash
   ls web/modules/contrib/advanced_country_field/
   # Should show: advanced_country_field.info.yml
   ```

3. **Clear cache**:
   ```bash
   drush cr
   ```

### Dependency Errors

If you see dependency errors:

```bash
# Update Composer dependencies
composer update

# Reinstall module
drush pmu advanced_country_field -y
drush en advanced_country_field -y
```

### Permissions Issues

If you encounter permission errors:

```bash
# Fix ownership (adjust USER:GROUP as needed)
sudo chown -R www-data:www-data web/modules/contrib/advanced_country_field

# Fix permissions
find web/modules/contrib/advanced_country_field -type d -exec chmod 755 {} \;
find web/modules/contrib/advanced_country_field -type f -exec chmod 644 {} \;
```

### Flag Library Not Loading

If flags don't display:

1. **Verify library path**: Check `/admin/config/regional/advanced-country-field`
2. **Check file existence**:
   ```bash
   ls -la web/libraries/country-flag-icons/3x2/us.svg
   ```
3. **Verify file permissions**:
   ```bash
   chmod -R 755 web/libraries/country-flag-icons
   ```
4. **Clear cache**: `drush cr`

### JavaScript Errors

If you see JavaScript errors in browser console:

1. Clear browser cache
2. Clear Drupal cache: `drush cr`
3. Check for conflicts with other modules
4. Verify JavaScript files are loaded:
   ```bash
   ls -la web/modules/contrib/advanced_country_field/js/
   ```

## Updating the Module

### Via Composer

```bash
composer update drupal/advanced_country_field
drush cr
```

### Manual Update

```bash
cd web/modules/contrib/advanced_country_field
git pull origin 1.x
drush cr
```

## Uninstalling

To uninstall the module:

```bash
drush pmu advanced_country_field -y
```

Note: This will remove module data. Back up your database first if needed.

## Next Steps

After installation:

1. Review the [Configuration Guide](CONFIGURATION.md)
2. Read the [Developer Guide](DEVELOPER.md) for customization
3. Check [Troubleshooting](README.md#troubleshooting) for common issues

## Getting Help

- [Issue Tracker](https://github.com/ilyas2017/advanced-country-field/issues)
- [Drupal.org Support](https://www.drupal.org/project/advanced_country_field)
- [Documentation](README.md)

