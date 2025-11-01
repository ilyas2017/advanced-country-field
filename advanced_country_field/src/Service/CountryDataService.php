<?php

namespace Drupal\advanced_country_field\Service;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Service for managing country data.
 */
class CountryDataService {

  use StringTranslationTrait;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Static cache for processed country lists.
   *
   * @var array
   */
  protected static $processedCountriesCache = [];

  /**
   * Static cache for individual country names.
   *
   * @var array
   */
  protected static $countryNameCache = [];

  /**
   * Static cache for custom countries.
   *
   * @var array
   */
  protected static $customCountriesCache = NULL;

  /**
   * ISO 3166-1 alpha-2 country codes with English names.
   *
   * @var array
   */
  protected $countries = [
    'AD' => 'Andorra',
    'AE' => 'United Arab Emirates',
    'AF' => 'Afghanistan',
    'AG' => 'Antigua and Barbuda',
    'AI' => 'Anguilla',
    'AL' => 'Albania',
    'AM' => 'Armenia',
    'AO' => 'Angola',
    'AQ' => 'Antarctica',
    'AR' => 'Argentina',
    'AS' => 'American Samoa',
    'AT' => 'Austria',
    'AU' => 'Australia',
    'AW' => 'Aruba',
    'AX' => 'Åland Islands',
    'AZ' => 'Azerbaijan',
    'BA' => 'Bosnia and Herzegovina',
    'BB' => 'Barbados',
    'BD' => 'Bangladesh',
    'BE' => 'Belgium',
    'BF' => 'Burkina Faso',
    'BG' => 'Bulgaria',
    'BH' => 'Bahrain',
    'BI' => 'Burundi',
    'BJ' => 'Benin',
    'BL' => 'Saint Barthélemy',
    'BM' => 'Bermuda',
    'BN' => 'Brunei',
    'BO' => 'Bolivia',
    'BQ' => 'Caribbean Netherlands',
    'BR' => 'Brazil',
    'BS' => 'Bahamas',
    'BT' => 'Bhutan',
    'BV' => 'Bouvet Island',
    'BW' => 'Botswana',
    'BY' => 'Belarus',
    'BZ' => 'Belize',
    'CA' => 'Canada',
    'CC' => 'Cocos Islands',
    'CD' => 'Congo (DRC)',
    'CF' => 'Central African Republic',
    'CG' => 'Congo',
    'CH' => 'Switzerland',
    'CI' => 'Côte d\'Ivoire',
    'CK' => 'Cook Islands',
    'CL' => 'Chile',
    'CM' => 'Cameroon',
    'CN' => 'China',
    'CO' => 'Colombia',
    'CR' => 'Costa Rica',
    'CU' => 'Cuba',
    'CV' => 'Cape Verde',
    'CW' => 'Curaçao',
    'CX' => 'Christmas Island',
    'CY' => 'Cyprus',
    'CZ' => 'Czechia',
    'DE' => 'Germany',
    'DJ' => 'Djibouti',
    'DK' => 'Denmark',
    'DM' => 'Dominica',
    'DO' => 'Dominican Republic',
    'DZ' => 'Algeria',
    'EC' => 'Ecuador',
    'EE' => 'Estonia',
    'EG' => 'Egypt',
    'EH' => 'Western Sahara',
    'ER' => 'Eritrea',
    'ES' => 'Spain',
    'ET' => 'Ethiopia',
    'FI' => 'Finland',
    'FJ' => 'Fiji',
    'FK' => 'Falkland Islands',
    'FM' => 'Micronesia',
    'FO' => 'Faroe Islands',
    'FR' => 'France',
    'GA' => 'Gabon',
    'GB' => 'United Kingdom',
    'GD' => 'Grenada',
    'GE' => 'Georgia',
    'GF' => 'French Guiana',
    'GG' => 'Guernsey',
    'GH' => 'Ghana',
    'GI' => 'Gibraltar',
    'GL' => 'Greenland',
    'GM' => 'Gambia',
    'GN' => 'Guinea',
    'GP' => 'Guadeloupe',
    'GQ' => 'Equatorial Guinea',
    'GR' => 'Greece',
    'GS' => 'South Georgia',
    'GT' => 'Guatemala',
    'GU' => 'Guam',
    'GW' => 'Guinea-Bissau',
    'GY' => 'Guyana',
    'HK' => 'Hong Kong',
    'HM' => 'Heard Island',
    'HN' => 'Honduras',
    'HR' => 'Croatia',
    'HT' => 'Haiti',
    'HU' => 'Hungary',
    'ID' => 'Indonesia',
    'IE' => 'Ireland',
    'IL' => 'Israel',
    'IM' => 'Isle of Man',
    'IN' => 'India',
    'IO' => 'British Indian Ocean Territory',
    'IQ' => 'Iraq',
    'IR' => 'Iran',
    'IS' => 'Iceland',
    'IT' => 'Italy',
    'JE' => 'Jersey',
    'JM' => 'Jamaica',
    'JO' => 'Jordan',
    'JP' => 'Japan',
    'KE' => 'Kenya',
    'KG' => 'Kyrgyzstan',
    'KH' => 'Cambodia',
    'KI' => 'Kiribati',
    'KM' => 'Comoros',
    'KN' => 'Saint Kitts and Nevis',
    'KP' => 'North Korea',
    'KR' => 'South Korea',
    'KW' => 'Kuwait',
    'KY' => 'Cayman Islands',
    'KZ' => 'Kazakhstan',
    'LA' => 'Laos',
    'LB' => 'Lebanon',
    'LC' => 'Saint Lucia',
    'LI' => 'Liechtenstein',
    'LK' => 'Sri Lanka',
    'LR' => 'Liberia',
    'LS' => 'Lesotho',
    'LT' => 'Lithuania',
    'LU' => 'Luxembourg',
    'LV' => 'Latvia',
    'LY' => 'Libya',
    'MA' => 'Morocco',
    'MC' => 'Monaco',
    'MD' => 'Moldova',
    'ME' => 'Montenegro',
    'MF' => 'Saint Martin',
    'MG' => 'Madagascar',
    'MH' => 'Marshall Islands',
    'MK' => 'North Macedonia',
    'ML' => 'Mali',
    'MM' => 'Myanmar',
    'MN' => 'Mongolia',
    'MO' => 'Macao',
    'MP' => 'Northern Mariana Islands',
    'MQ' => 'Martinique',
    'MR' => 'Mauritania',
    'MS' => 'Montserrat',
    'MT' => 'Malta',
    'MU' => 'Mauritius',
    'MV' => 'Maldives',
    'MW' => 'Malawi',
    'MX' => 'Mexico',
    'MY' => 'Malaysia',
    'MZ' => 'Mozambique',
    'NA' => 'Namibia',
    'NC' => 'New Caledonia',
    'NE' => 'Niger',
    'NF' => 'Norfolk Island',
    'NG' => 'Nigeria',
    'NI' => 'Nicaragua',
    'NL' => 'Netherlands',
    'NO' => 'Norway',
    'NP' => 'Nepal',
    'NR' => 'Nauru',
    'NU' => 'Niue',
    'NZ' => 'New Zealand',
    'OM' => 'Oman',
    'PA' => 'Panama',
    'PE' => 'Peru',
    'PF' => 'French Polynesia',
    'PG' => 'Papua New Guinea',
    'PH' => 'Philippines',
    'PK' => 'Pakistan',
    'PL' => 'Poland',
    'PM' => 'Saint Pierre and Miquelon',
    'PN' => 'Pitcairn',
    'PR' => 'Puerto Rico',
    'PS' => 'Palestine',
    'PT' => 'Portugal',
    'PW' => 'Palau',
    'PY' => 'Paraguay',
    'QA' => 'Qatar',
    'RE' => 'Réunion',
    'RO' => 'Romania',
    'RS' => 'Serbia',
    'RU' => 'Russia',
    'RW' => 'Rwanda',
    'SA' => 'Saudi Arabia',
    'SB' => 'Solomon Islands',
    'SC' => 'Seychelles',
    'SD' => 'Sudan',
    'SE' => 'Sweden',
    'SG' => 'Singapore',
    'SH' => 'Saint Helena',
    'SI' => 'Slovenia',
    'SJ' => 'Svalbard and Jan Mayen',
    'SK' => 'Slovakia',
    'SL' => 'Sierra Leone',
    'SM' => 'San Marino',
    'SN' => 'Senegal',
    'SO' => 'Somalia',
    'SR' => 'Suriname',
    'SS' => 'South Sudan',
    'ST' => 'São Tomé and Príncipe',
    'SV' => 'El Salvador',
    'SX' => 'Sint Maarten',
    'SY' => 'Syria',
    'SZ' => 'Eswatini',
    'TC' => 'Turks and Caicos Islands',
    'TD' => 'Chad',
    'TF' => 'French Southern Territories',
    'TG' => 'Togo',
    'TH' => 'Thailand',
    'TJ' => 'Tajikistan',
    'TK' => 'Tokelau',
    'TL' => 'Timor-Leste',
    'TM' => 'Turkmenistan',
    'TN' => 'Tunisia',
    'TO' => 'Tonga',
    'TR' => 'Turkey',
    'TT' => 'Trinidad and Tobago',
    'TV' => 'Tuvalu',
    'TW' => 'Taiwan',
    'TZ' => 'Tanzania',
    'UA' => 'Ukraine',
    'UG' => 'Uganda',
    'UM' => 'U.S. Outlying Islands',
    'US' => 'United States',
    'UY' => 'Uruguay',
    'UZ' => 'Uzbekistan',
    'VA' => 'Vatican City',
    'VC' => 'Saint Vincent and the Grenadines',
    'VE' => 'Venezuela',
    'VG' => 'British Virgin Islands',
    'VI' => 'U.S. Virgin Islands',
    'VN' => 'Vietnam',
    'VU' => 'Vanuatu',
    'WF' => 'Wallis and Futuna',
    'WS' => 'Samoa',
    'YE' => 'Yemen',
    'YT' => 'Mayotte',
    'ZA' => 'South Africa',
    'ZM' => 'Zambia',
    'ZW' => 'Zimbabwe',
  ];

  /**
   * Native country names in their official/native languages.
   *
   * @var array
   */
  protected $nativeCountries = [
    'AD' => 'Andorra',
    'AE' => 'الإمارات العربية المتحدة', // United Arab Emirates
    'AF' => 'افغانستان', // Afghanistan
    'AG' => 'Antigua and Barbuda',
    'AI' => 'Anguilla',
    'AL' => 'Shqipëria', // Albania
    'AM' => 'Հայաստան', // Armenia
    'AO' => 'Angola',
    'AQ' => 'Antarctica',
    'AR' => 'Argentina',
    'AS' => 'American Samoa',
    'AT' => 'Österreich', // Austria
    'AU' => 'Australia',
    'AW' => 'Aruba',
    'AX' => 'Åland',
    'AZ' => 'Azərbaycan', // Azerbaijan
    'BA' => 'Bosna i Hercegovina', // Bosnia and Herzegovina
    'BB' => 'Barbados',
    'BD' => 'বাংলাদেশ', // Bangladesh
    'BE' => 'België / Belgique', // Belgium (dual language)
    'BF' => 'Burkina Faso',
    'BG' => 'България', // Bulgaria
    'BH' => 'البحرين', // Bahrain
    'BI' => 'Burundi',
    'BJ' => 'Bénin',
    'BL' => 'Saint-Barthélemy',
    'BM' => 'Bermuda',
    'BN' => 'Brunei',
    'BO' => 'Bolivia',
    'BQ' => 'Caribbean Netherlands',
    'BR' => 'Brasil', // Brazil
    'BS' => 'Bahamas',
    'BT' => 'འབྲུག་ཡུལ', // Bhutan
    'BV' => 'Bouvet Island',
    'BW' => 'Botswana',
    'BY' => 'Беларусь', // Belarus
    'BZ' => 'Belize',
    'CA' => 'Canada',
    'CC' => 'Cocos Islands',
    'CD' => 'République Démocratique du Congo', // Congo (DRC)
    'CF' => 'République Centrafricaine', // Central African Republic
    'CG' => 'Congo',
    'CH' => 'Schweiz / Suisse / Svizzera', // Switzerland (triple language)
    'CI' => 'Côte d\'Ivoire',
    'CK' => 'Cook Islands',
    'CL' => 'Chile',
    'CM' => 'Cameroun', // Cameroon
    'CN' => '中国', // China
    'CO' => 'Colombia',
    'CR' => 'Costa Rica',
    'CU' => 'Cuba',
    'CV' => 'Cabo Verde',
    'CW' => 'Curaçao',
    'CX' => 'Christmas Island',
    'CY' => 'Κύπρος', // Cyprus
    'CZ' => 'Česká republika', // Czech Republic
    'DE' => 'Deutschland', // Germany
    'DJ' => 'Djibouti',
    'DK' => 'Danmark', // Denmark
    'DM' => 'Dominica',
    'DO' => 'República Dominicana', // Dominican Republic
    'DZ' => 'الجزائر', // Algeria
    'EC' => 'Ecuador',
    'EE' => 'Eesti', // Estonia
    'EG' => 'مصر', // Egypt
    'EH' => 'الصحراء الغربية', // Western Sahara
    'ER' => 'ኤርትራ', // Eritrea
    'ES' => 'España', // Spain
    'ET' => 'ኢትዮጵያ', // Ethiopia
    'FI' => 'Suomi', // Finland
    'FJ' => 'Fiji',
    'FK' => 'Falkland Islands',
    'FM' => 'Micronesia',
    'FO' => 'Føroyar', // Faroe Islands
    'FR' => 'France', // France
    'GA' => 'Gabon',
    'GB' => 'United Kingdom',
    'GD' => 'Grenada',
    'GE' => 'საქართველო', // Georgia
    'GF' => 'Guyane française', // French Guiana
    'GG' => 'Guernsey',
    'GH' => 'Ghana',
    'GI' => 'Gibraltar',
    'GL' => 'Grønland', // Greenland
    'GM' => 'Gambia',
    'GN' => 'Guinée', // Guinea
    'GP' => 'Guadeloupe',
    'GQ' => 'Guinea Ecuatorial', // Equatorial Guinea
    'GR' => 'Ελλάδα', // Greece
    'GS' => 'South Georgia and South Sandwich Islands',
    'GT' => 'Guatemala',
    'GU' => 'Guam',
    'GW' => 'Guiné-Bissau', // Guinea-Bissau
    'GY' => 'Guyana',
    'HK' => '香港', // Hong Kong
    'HM' => 'Heard Island and McDonald Islands',
    'HN' => 'Honduras',
    'HR' => 'Hrvatska', // Croatia
    'HT' => 'Haïti', // Haiti
    'HU' => 'Magyarország', // Hungary
    'ID' => 'Indonesia',
    'IE' => 'Éire', // Ireland
    'IL' => 'ישראל', // Israel
    'IM' => 'Isle of Man',
    'IN' => 'भारत', // India
    'IO' => 'British Indian Ocean Territory',
    'IQ' => 'العراق', // Iraq
    'IR' => 'ایران', // Iran
    'IS' => 'Ísland', // Iceland
    'IT' => 'Italia', // Italy
    'JE' => 'Jersey',
    'JM' => 'Jamaica',
    'JO' => 'الأردن', // Jordan
    'JP' => '日本', // Japan
    'KE' => 'Kenya',
    'KG' => 'Кыргызстан', // Kyrgyzstan
    'KH' => 'កម្ពុជា', // Cambodia
    'KI' => 'Kiribati',
    'KM' => 'Comores', // Comoros
    'KN' => 'Saint Kitts and Nevis',
    'KP' => '조선민주주의인민공화국', // North Korea
    'KR' => '대한민국', // South Korea
    'KW' => 'الكويت', // Kuwait
    'KY' => 'Cayman Islands',
    'KZ' => 'Қазақстан', // Kazakhstan
    'LA' => 'ລາວ', // Laos
    'LB' => 'لبنان', // Lebanon
    'LC' => 'Saint Lucia',
    'LI' => 'Liechtenstein',
    'LK' => 'ශ්‍රී ලංකාව', // Sri Lanka
    'LR' => 'Liberia',
    'LS' => 'Lesotho',
    'LT' => 'Lietuva', // Lithuania
    'LU' => 'Luxembourg / Lëtzebuerg',
    'LV' => 'Latvija', // Latvia
    'LY' => 'ليبيا', // Libya
    'MA' => 'المغرب', // Morocco
    'MC' => 'Monaco',
    'MD' => 'Moldova',
    'ME' => 'Crna Gora', // Montenegro
    'MF' => 'Saint-Martin',
    'MG' => 'Madagasikara', // Madagascar
    'MH' => 'Marshall Islands',
    'MK' => 'Северна Македонија', // North Macedonia
    'ML' => 'Mali',
    'MM' => 'မြန်မာ', // Myanmar
    'MN' => 'Монгол Улс', // Mongolia
    'MO' => '澳門', // Macao
    'MP' => 'Northern Mariana Islands',
    'MQ' => 'Martinique',
    'MR' => 'موريتانيا', // Mauritania
    'MS' => 'Montserrat',
    'MT' => 'Malta',
    'MU' => 'Mauritius',
    'MV' => 'ދިވެހިރާއްޖެ', // Maldives
    'MW' => 'Malawi',
    'MX' => 'México', // Mexico
    'MY' => 'Malaysia',
    'MZ' => 'Moçambique', // Mozambique
    'NA' => 'Namibia',
    'NC' => 'Nouvelle-Calédonie', // New Caledonia
    'NE' => 'Niger',
    'NF' => 'Norfolk Island',
    'NG' => 'Nigeria',
    'NI' => 'Nicaragua',
    'NL' => 'Nederland', // Netherlands
    'NO' => 'Norge', // Norway
    'NP' => 'नेपाल', // Nepal
    'NR' => 'Nauru',
    'NU' => 'Niue',
    'NZ' => 'New Zealand / Aotearoa',
    'OM' => 'عُمان', // Oman
    'PA' => 'Panamá', // Panama
    'PE' => 'Perú', // Peru
    'PF' => 'Polynésie française', // French Polynesia
    'PG' => 'Papua New Guinea',
    'PH' => 'Philippines',
    'PK' => 'پاکستان', // Pakistan
    'PL' => 'Polska', // Poland
    'PM' => 'Saint-Pierre-et-Miquelon',
    'PN' => 'Pitcairn',
    'PR' => 'Puerto Rico',
    'PS' => 'فلسطين', // Palestine
    'PT' => 'Portugal',
    'PW' => 'Palau',
    'PY' => 'Paraguay',
    'QA' => 'قطر', // Qatar
    'RE' => 'La Réunion',
    'RO' => 'România', // Romania
    'RS' => 'Србија', // Serbia
    'RU' => 'Россия', // Russia
    'RW' => 'Rwanda',
    'SA' => 'السعودية', // Saudi Arabia
    'SB' => 'Solomon Islands',
    'SC' => 'Seychelles',
    'SD' => 'السودان', // Sudan
    'SE' => 'Sverige', // Sweden
    'SG' => 'Singapore',
    'SH' => 'Saint Helena',
    'SI' => 'Slovenija', // Slovenia
    'SJ' => 'Svalbard og Jan Mayen', // Svalbard
    'SK' => 'Slovensko', // Slovakia
    'SL' => 'Sierra Leone',
    'SM' => 'San Marino',
    'SN' => 'Sénégal', // Senegal
    'SO' => 'Soomaaliya', // Somalia
    'SR' => 'Suriname',
    'SS' => 'South Sudan',
    'ST' => 'São Tomé e Príncipe',
    'SV' => 'El Salvador',
    'SX' => 'Sint Maarten',
    'SY' => 'سوريا', // Syria
    'SZ' => 'eSwatini',
    'TC' => 'Turks and Caicos Islands',
    'TD' => 'Tchad', // Chad
    'TF' => 'Terres australes françaises', // French Southern Territories
    'TG' => 'Togo',
    'TH' => 'ไทย', // Thailand
    'TJ' => 'Тоҷикистон', // Tajikistan
    'TK' => 'Tokelau',
    'TL' => 'Timor-Leste',
    'TM' => 'Türkmenistan', // Turkmenistan
    'TN' => 'تونس', // Tunisia
    'TO' => 'Tonga',
    'TR' => 'Türkiye', // Turkey
    'TT' => 'Trinidad and Tobago',
    'TV' => 'Tuvalu',
    'TW' => '台灣', // Taiwan
    'TZ' => 'Tanzania',
    'UA' => 'Україна', // Ukraine
    'UG' => 'Uganda',
    'UM' => 'U.S. Outlying Islands',
    'US' => 'United States',
    'UY' => 'Uruguay',
    'UZ' => 'Oʻzbekiston', // Uzbekistan
    'VA' => 'Città del Vaticano', // Vatican City
    'VC' => 'Saint Vincent and the Grenadines',
    'VE' => 'Venezuela',
    'VG' => 'British Virgin Islands',
    'VI' => 'U.S. Virgin Islands',
    'VN' => 'Việt Nam', // Vietnam
    'VU' => 'Vanuatu',
    'WF' => 'Wallis-et-Futuna',
    'WS' => 'Samoa',
    'YE' => 'اليمن', // Yemen
    'YT' => 'Mayotte',
    'ZA' => 'South Africa',
    'ZM' => 'Zambia',
    'ZW' => 'Zimbabwe',
  ];

  /**
   * Constructs a CountryDataService object.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(LanguageManagerInterface $language_manager, ConfigFactoryInterface $config_factory, TranslationInterface $string_translation, ModuleHandlerInterface $module_handler) {
    $this->languageManager = $language_manager;
    $this->configFactory = $config_factory;
    $this->stringTranslation = $string_translation;
    $this->moduleHandler = $module_handler;
  }

  /**
   * Gets all available countries.
   *
   * @param bool $filtered
   *   Whether to filter based on configuration.
   * @param string|null $langcode
   *   (optional) The language code to translate country names. Defaults to the
   *   current content language.
   *
   * @return array
   *   Array of country codes => country names.
   */
  public function getCountries($filtered = TRUE, $langcode = NULL) {
    // Get language handling configuration.
    $config = $this->configFactory->get('advanced_country_field.settings');
    $language_handling = $config->get('language_handling') ?? 'site';
    $filter_enabled = $config->get('filter_enabled') ?? FALSE;
    $enabled_countries = $config->get('enabled_countries') ?? [];
    
    // Determine which language to use.
    if ($langcode === NULL) {
      if ($language_handling === 'site') {
        $langcode = $this->languageManager->getCurrentLanguage()->getId();
      } elseif ($language_handling === 'native') {
        // Use native names (original language for each country).
        $langcode = 'native';
      } else {
        // Fallback: use English as default.
        $langcode = 'en';
      }
    }
    
    // Create cache key based on parameters.
    // Use a more efficient cache key generation without expensive serialize().
    $cache_key = implode(':', [
      $filtered ? '1' : '0',
      $langcode,
      $language_handling,
      $filter_enabled ? '1' : '0',
      md5(implode(',', $enabled_countries)),
    ]);
    
    // Return cached result if available.
    if (isset(static::$processedCountriesCache[$cache_key])) {
      return static::$processedCountriesCache[$cache_key];
    }
    
    $countries = $this->countries;
    
    // Add custom countries.
    $custom_countries = $this->getCustomCountries();
    $countries = array_merge($countries, $custom_countries);

    if ($filtered && $filter_enabled && !empty($enabled_countries)) {
      $countries = array_intersect_key($countries, array_flip($enabled_countries));
    }

    // Handle localization based on language_handling setting.
    if ($language_handling === 'site' && $langcode !== 'native') {
      // Translate country names using Drupal's translation system.
      foreach ($countries as $code => &$name) {
        $name = $this->t($name, [], ['langcode' => $langcode]);
      }
    } elseif ($language_handling === 'native') {
      // Use native country names.
      foreach ($countries as $code => &$name) {
        if (isset($this->nativeCountries[$code])) {
          $name = $this->nativeCountries[$code];
        }
      }
    }
    // For 'fallback', we keep original English names.

    // Sort alphabetically by translated names.
    asort($countries);

    // Allow other modules to alter the country list.
    $context = [
      'langcode' => $langcode,
      'language_handling' => $language_handling,
      'filtered' => $filtered,
    ];
    $this->moduleHandler->alter('advanced_country_field_countries', $countries, $context);

    // Cache the result.
    static::$processedCountriesCache[$cache_key] = $countries;

    return $countries;
  }

  /**
   * Gets a country name by code.
   *
   * @param string $code
   *   The country code.
   * @param string|null $langcode
   *   The language code for localized name.
   *
   * @return string|null
   *   The country name or NULL if not found.
   */
  public function getCountryName($code, $langcode = NULL) {
    // Get language handling configuration.
    $config = $this->configFactory->get('advanced_country_field.settings');
    $language_handling = $config->get('language_handling') ?? 'site';
    
    // Determine which language to use.
    if ($langcode === NULL) {
      if ($language_handling === 'site') {
        $langcode = $this->languageManager->getCurrentLanguage()->getId();
      } elseif ($language_handling === 'native') {
        $langcode = 'native';
      } else {
        $langcode = 'en';
      }
    }
    
    // Create cache key.
    $cache_key = md5($code . ':' . $langcode);
    
    // Return cached result if available.
    if (isset(static::$countryNameCache[$cache_key])) {
      return static::$countryNameCache[$cache_key];
    }
    
    // Get the base country name.
    $name = $this->countries[$code] ?? NULL;
    
    if ($name === NULL) {
      return NULL;
    }
    
    // Handle localization based on language_handling setting.
    if ($language_handling === 'site' && $langcode !== 'native') {
      // Translate country names using Drupal's translation system.
      $name = $this->t($name, [], ['langcode' => $langcode]);
    } elseif ($language_handling === 'native' && isset($this->nativeCountries[$code])) {
      // Use native country names.
      $name = $this->nativeCountries[$code];
    }
    
    // Cache the result.
    static::$countryNameCache[$cache_key] = $name;
    
    return $name;
  }

  /**
   * Gets custom countries from configuration.
   *
   * @return array
   *   Array of custom country codes => names.
   */
  public function getCustomCountries() {
    // Return cached result if available.
    if (static::$customCountriesCache !== NULL) {
      return static::$customCountriesCache;
    }
    
    $config = $this->configFactory->get('advanced_country_field.settings');
    $custom = $config->get('custom_countries') ?? [];
    $custom_countries = [];
    
    foreach ($custom as $item) {
      if (!empty($item['code']) && !empty($item['name'])) {
        $custom_countries[$item['code']] = $item['name'];
      }
    }
    
    // Cache the result.
    static::$customCountriesCache = $custom_countries;
    
    return $custom_countries;
  }

  /**
   * Gets the flag SVG path for a country code.
   *
   * @param string $code
   *   The country code.
   *
   * @return string
   *   The path to the flag SVG file.
   */
  public function getFlagPath($code) {
    $config = $this->configFactory->get('advanced_country_field.settings');
    $base_path = $config->get('flag_library_path') ?? '/libraries/country-flag-icons/3x2/';
    
    // Ensure path ends with /
    if (substr($base_path, -1) !== '/') {
      $base_path .= '/';
    }
    
    return $base_path . strtolower($code) . '.svg';
  }

  /**
   * Clears all static caches for country data.
   *
   * This method should be called when configuration changes to ensure
   * fresh data is loaded.
   */
  public static function clearCache() {
    static::$processedCountriesCache = [];
    static::$countryNameCache = [];
    static::$customCountriesCache = NULL;
  }

}

