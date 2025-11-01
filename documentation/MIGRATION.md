# Migration Guide

Guide for migrating data from other country field modules to Advanced Country Field.

## Overview

Advanced Country Field is designed as a successor to various country field implementations in Drupal. This guide helps you migrate data from other modules.

## From Other Country Field Modules

### Address Field Module

If you're using the **Address** module with country fields:

#### Option 1: Direct Migration (Recommended)

```php
use Drupal\Core\Database\Database;

// Get old country values
$connection = Database::getConnection();
$old_fields = $connection->select('address__country_code', 'a')
  ->fields('a', ['entity_id', 'country_code_value'])
  ->execute()
  ->fetchAll();

// Migrate to new field
foreach ($old_fields as $row) {
  // Update new field with old data
  // Implementation depends on your specific setup
}
```

#### Option 2: Field Replacement

1. Create new Advanced Country Field
2. Export old data to temporary format
3. Import to new field
4. Remove old field

### Country Field Module

From the generic **Country** module:

```php
// Both use ISO 3166-1 alpha-2 codes
// Migration should be straightforward
$country_codes = $connection->select('field_country', 'f')
  ->fields('f', ['country_value'])
  ->execute()
  ->fetchCol();

// Map to new structure
// Country codes remain the same
```

### Custom Country Implementations

For custom country fields:

1. **Export existing data**:
   ```bash
   drush sql-query "SELECT * FROM your_table_field_country"
   ```

2. **Map to ISO codes**: Ensure your codes match ISO 3166-1 alpha-2

3. **Import to new field**: Use Drupal's field update mechanisms

## Migration Script Template

```php
<?php

use Drupal\Core\Database\Database;

/**
 * Migration from old country field to Advanced Country Field.
 */
function migrate_country_fields() {
  $connection = Database::getConnection();
  
  // Get all nodes with old country field
  $old_countries = $connection->select('node__field_old_country', 'oc')
    ->fields('oc', ['entity_id', 'field_old_country_value'])
    ->execute()
    ->fetchAllKeyed();
  
  // Get all nodes of target type
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  
  $batch = [
    'title' => t('Migrating country fields'),
    'operations' => [],
    'finished' => 'migration_finished',
  ];
  
  foreach ($old_countries as $nid => $country_code) {
    $batch['operations'][] = [
      'migrate_single_node',
      [$nid, $country_code],
    ];
  }
  
  batch_set($batch);
  batch_process();
}

/**
 * Migrate single node.
 */
function migrate_single_node($nid, $country_code) {
  try {
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $node_storage->load($nid);
    
    if (!$node || !$node->hasField('field_country')) {
      return;
    }
    
    // Set country value
    $node->field_country->country_code = $country_code;
    
    // Optionally get country name
    $country_service = \Drupal::service('advanced_country_field.country_data');
    $country_name = $country_service->getCountryName($country_code);
    if ($country_name) {
      $node->field_country->country_name = $country_name;
    }
    
    $node->save();
  }
  catch (\Exception $e) {
    \Drupal::logger('advanced_country_field')->error('Migration error: @error', [
      '@error' => $e->getMessage(),
    ]);
  }
}

/**
 * Batch finished callback.
 */
function migration_finished($success, $results, $operations) {
  if ($success) {
    \Drupal::messenger()->addMessage(t('Migration completed successfully.'));
  }
  else {
    \Drupal::messenger()->addError(t('Migration encountered errors.'));
  }
}
```

## Data Mapping

### ISO 3166-1 Alpha-2 Codes

Advanced Country Field uses ISO 3166-1 alpha-2 codes. Common mappings:

| Old Format | ISO Code | Handling |
|------------|----------|----------|
| "United States" | US | Map to ISO code |
| "USA" | US | Map to ISO code |
| "GB" | GB | Direct match |
| "UK" | GB | Map to GB |
| Numeric codes | Convert to alpha-2 | Use lookup table |

### Country Name Storage

The module supports three storage formats:

1. **Code only**: Most efficient
2. **Code + Name**: Provides redundancy
3. **Name only**: Not recommended

## Field Configuration Migration

### Widget Settings

Map old widget settings to new:

| Old Widget | New Widget | Notes |
|------------|-----------|-------|
| select | Dropdown (Select) | Direct match |
| checkboxes | Checkboxes | Direct match |
| radios | Radio Buttons | Direct match |
| select_multi | Multi-select | Map to multi-select |

### Formatter Settings

Map display formats:

| Old Format | New Format | Notes |
|------------|-----------|-------|
| default | Name | Map default to name |
| code | Code | Direct match |
| name | Name | Direct match |

## Post-Migration Steps

### 1. Verify Data

```bash
# Check field population
drush sql-query "SELECT COUNT(*) FROM node__field_country WHERE country_code_value IS NOT NULL"

# Sample data
drush sql-query "SELECT country_code_value, country_name_value FROM node__field_country LIMIT 10"
```

### 2. Test Display

- View several content items
- Check flag display (if enabled)
- Verify language translations
- Test search functionality

### 3. Clean Up

```bash
# Remove old field definition (if safe)
drush entity:delete field_config old_country_field

# Clear cache
drush cr
```

### 4. Rebuild Cache

```bash
drush cr
```

## Rollback Plan

If migration fails:

1. **Disable new module**:
   ```bash
   drush pmu advanced_country_field -y
   ```

2. **Keep old data intact**:
   - Don't delete old field data
   - Keep backups

3. **Restore from backup** if needed

## Testing Migration

### Test Procedure

1. **Backup database**: Before migration
   ```bash
   drush sql-dump > backup.sql
   ```

2. **Run on development**: Test migration first

3. **Verify sample**:
   ```php
   // Get sample nodes
   $nodes = \Drupal::entityTypeManager()->getStorage('node')
     ->loadByProperties(['type' => 'your_type']);
   
   foreach ($nodes as $node) {
     if ($node->hasField('field_country')) {
       $code = $node->field_country->country_code;
       $name = $node->field_country->country_name;
       \Drupal::logger('migration_test')->info('Country: @code - @name', [
         '@code' => $code,
         '@name' => $name,
       ]);
     }
   }
   ```

4. **Validate counts**: Ensure all records migrated

5. **Check displays**: Verify frontend rendering

## Common Issues

### Missing Country Names

If country names are empty:

```php
// Bulk update country names
$connection = Database::getConnection();
$service = \Drupal::service('advanced_country_field.country_data');

$codes = $connection->select('node__field_country', 'f')
  ->fields('f', ['country_code_value'])
  ->isNull('country_name_value')
  ->distinct()
  ->execute()
  ->fetchCol();

foreach ($codes as $code) {
  $name = $service->getCountryName($code);
  if ($name) {
    $connection->update('node__field_country')
      ->fields(['country_name_value' => $name])
      ->condition('country_code_value', $code)
      ->execute();
  }
}
```

### Invalid Country Codes

Handle invalid codes:

```php
// Validate and clean country codes
$invalid = ['XX', 'ZZ', 'INVALID'];
$connection = Database::getConnection();

foreach ($invalid as $code) {
  // Either remove or map to valid code
  $connection->delete('node__field_country')
    ->condition('country_code_value', $code)
    ->execute();
}
```

### Case Sensitivity

Ensure consistent casing:

```php
// Normalize to uppercase
$connection->update('node__field_country')
  ->expression('country_code_value', 'UPPER(country_code_value)')
  ->execute();
```

## Best Practices

### Before Migration

- ✅ **Backup everything**: Database, files, configuration
- ✅ **Test on development**: Never test on production
- ✅ **Document process**: Keep migration script
- ✅ **Verify mappings**: Check all code mappings

### During Migration

- ✅ **Use batch API**: For large datasets
- ✅ **Log progress**: Track migration status
- ✅ **Handle errors**: Don't crash on single record
- ✅ **Monitor memory**: Use batch chunking

### After Migration

- ✅ **Verify data**: Spot check records
- ✅ **Test displays**: Check all view modes
- ✅ **Clear caches**: Rebuild everything
- ✅ **Update documentation**: Note migration date

## Getting Help

- [Issue Tracker](https://github.com/ilyas2017/advanced-country-field/issues)
- [Developer Guide](DEVELOPER.md)
- [Drupal Migrate API](https://www.drupal.org/docs/drupal-apis/migrate-api)

## Additional Resources

- [Drupal Migration Guide](https://www.drupal.org/docs/8/api/migrate-api)
- [Batch API](https://www.drupal.org/docs/drupal-apis/batch-api)
- [ISO 3166-1 Standard](https://www.iso.org/iso-3166-country-codes.html)

