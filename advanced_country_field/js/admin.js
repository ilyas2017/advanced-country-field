/**
 * @file
 * Admin JavaScript for Advanced Country Field.
 */

(function ($, Drupal, debounce) {
  'use strict';

  /**
   * Client-side filtering for country list (like modules page).
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.countryFilterText = {
    attach: function (context, settings) {
      const input = once('country-filter-text', 'input.country-filter-text', context);
      if (!input.length) {
        return;
      }

      const $countries = $('.country-item');
      let searching = false;

      function filterCountryList(e) {
        const query = $(e.target).val().toLowerCase();
        // Case insensitive expression to find query at the beginning of a word.
        const re = new RegExp(`\\b${query}`, 'i');

        function showCountryRow(index, item) {
          const $item = $(item);
          // The wrapper (country-item) has the table-filter-text-source class, so get text directly.
          const text = $item.text();
          const textMatch = text.search(re) !== -1;
          $item.toggle(textMatch);
        }

        // Filter if the length of the query is at least 2 characters.
        if (query.length >= 2) {
          searching = true;
          $countries.each(showCountryRow);
        } else if (searching) {
          searching = false;
          $countries.show();
        }
      }

      function preventEnterKey(event) {
        if (event.which === 13) {
          event.preventDefault();
          event.stopPropagation();
        }
      }

      $(input[0]).on({
        input: debounce(filterCountryList, 200),
        keydown: preventEnterKey,
      });
    }
  };

})(jQuery, Drupal, Drupal.debounce);

