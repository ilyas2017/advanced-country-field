<?php

namespace Drupal\advanced_country_field\EventSubscriber;

use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\advanced_country_field\Service\CountryDataService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber to invalidate caches when configuration changes.
 */
class ConfigCacheInvalidator implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events[ConfigEvents::SAVE][] = ['onConfigSave'];
    $events[ConfigEvents::DELETE][] = ['onConfigDelete'];
    return $events;
  }

  /**
   * Invalidate caches when configuration is saved.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The config event.
   */
  public function onConfigSave(ConfigCrudEvent $event) {
    $config = $event->getConfig();
    if ($config->getName() === 'advanced_country_field.settings') {
      // Clear static caches when module configuration changes.
      CountryDataService::clearCache();
    }
  }

  /**
   * Invalidate caches when configuration is deleted.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The config event.
   */
  public function onConfigDelete(ConfigCrudEvent $event) {
    $config = $event->getConfig();
    if ($config->getName() === 'advanced_country_field.settings') {
      // Clear static caches when module configuration is deleted.
      CountryDataService::clearCache();
    }
  }

}

