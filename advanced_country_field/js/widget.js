/**
 * @file
 * Widget JavaScript for Advanced Country Field.
 */

(function (Drupal, once) {
  'use strict';

  /**
   * Enhances country field widgets with search functionality.
   */
  Drupal.behaviors.advancedCountryFieldWidget = {
    attach: function (context, settings) {
      once('advanced-country-field-widget', '.advanced-country-field-widget[data-search-enabled="1"]', context).forEach(function (element) {
        if (element.tagName === 'SELECT') {
          // Add search functionality for select elements.
          const searchInput = document.createElement('input');
          searchInput.type = 'text';
          searchInput.className = 'country-search-input';
          searchInput.placeholder = Drupal.t('Search countries...');
          searchInput.setAttribute('aria-label', Drupal.t('Search countries'));
          
          // Insert before the select.
          element.parentNode.insertBefore(searchInput, element);
          
          // Filter options on input.
          searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const options = element.options;
            
            for (let i = 0; i < options.length; i++) {
              const option = options[i];
              const text = option.text.toLowerCase();
              option.style.display = text.includes(searchTerm) ? '' : 'none';
            }
            
            // Update aria-live region for screen readers.
            if (!element.nextElementSibling || !element.nextElementSibling.classList.contains('aria-live-region')) {
              const ariaLive = document.createElement('div');
              ariaLive.className = 'aria-live-region';
              ariaLive.setAttribute('aria-live', 'polite');
              ariaLive.setAttribute('aria-atomic', 'true');
              ariaLive.style.position = 'absolute';
              ariaLive.style.left = '-9999px';
              element.parentNode.appendChild(ariaLive);
            }
            
            const visibleCount = Array.from(options).filter(opt => opt.style.display !== 'none').length;
            element.nextElementSibling.textContent = Drupal.t('@count countries found', {'@count': visibleCount});
          });
        }
      });
    }
  };

  /**
   * Creates a custom dropdown with flags to replace native select.
   */
  Drupal.behaviors.advancedCountryFieldFlags = {
    attach: function (context, settings) {
      // Find all select elements with the widget class and flags enabled.
      once('advanced-country-field-flags', 'select.advanced-country-field-widget[data-show-flags="1"]', context).forEach(function (selectElement) {
        // Skip if already enhanced.
        if (selectElement.matches('.advanced-country-field-enhanced')) {
          return;
        }
        
        // Handle multi-select differently if needed, but for now apply same logic.
        const isMultiple = selectElement.multiple;

        // Get configuration from element's data attributes.
        const flagPosition = selectElement.getAttribute('data-flag-position') || 'before';
        const enableSearch = selectElement.getAttribute('data-enable-search') === '1';
        let flagBasePath = selectElement.getAttribute('data-flag-base-path');
        
        // Fallback: try to get from settings if data attribute not available.
        if (!flagBasePath && settings.advancedCountryField && settings.advancedCountryField.flags) {
          const fieldSelector = selectElement.getAttribute('data-drupal-selector') || selectElement.getAttribute('name') || '';
          Object.keys(settings.advancedCountryField.flags).forEach(function (fieldName) {
            if (fieldSelector.indexOf(fieldName) !== -1) {
              const flagConfig = settings.advancedCountryField.flags[fieldName];
              flagBasePath = flagConfig.flag_base_path;
            }
          });
        }
        
        // Final fallback to default path.
        if (!flagBasePath) {
          flagBasePath = '/libraries/country-flag-icons/3x2/';
        }
        
        // Ensure path ends with /
        if (flagBasePath.charAt(flagBasePath.length - 1) !== '/') {
          flagBasePath += '/';
        }

        // Create custom dropdown.
        const wrapper = document.createElement('div');
        wrapper.className = 'advanced-country-field-dropdown-wrapper';
        wrapper.setAttribute('role', 'combobox');
        wrapper.setAttribute('aria-expanded', 'false');
        wrapper.setAttribute('aria-haspopup', 'listbox');
        wrapper.setAttribute('tabindex', selectElement.hasAttribute('tabindex') ? selectElement.getAttribute('tabindex') : '0');

        const display = document.createElement('div');
        display.className = 'advanced-country-field-display';
        display.setAttribute('role', 'button');
        display.setAttribute('aria-label', selectElement.getAttribute('aria-label') || Drupal.t('Select country'));

        const dropdown = document.createElement('ul');
        dropdown.className = 'advanced-country-field-dropdown-list';
        dropdown.setAttribute('role', 'listbox');
        dropdown.style.display = 'none';

        // Add search input if enabled.
        let searchInput = null;
        if (enableSearch) {
          searchInput = document.createElement('input');
          searchInput.type = 'text';
          searchInput.className = 'advanced-country-field-search';
          searchInput.placeholder = Drupal.t('Search countries...');
          searchInput.setAttribute('aria-label', Drupal.t('Search countries'));
          searchInput.setAttribute('autocomplete', 'off');
        }

        // Build options and find selected value(s).
        let selectedValues = [];
        if (isMultiple) {
          Array.from(selectElement.selectedOptions).forEach(function(opt) {
            if (opt.value) {
              selectedValues.push(opt.value);
            }
          });
        } else {
          if (selectElement.value) {
            selectedValues.push(selectElement.value);
          }
        }
        const placeholder = selectElement.getAttribute('data-placeholder') || Drupal.t('Select a country...');

        // Build dropdown options.
        Array.from(selectElement.options).forEach(function (option, index) {
          if (option.value === '') {
            return; // Skip empty option.
          }

          const li = document.createElement('li');
          li.className = 'advanced-country-field-option';
          li.setAttribute('role', 'option');
          li.setAttribute('data-value', option.value);
          li.setAttribute('aria-selected', option.selected ? 'true' : 'false');
          li.setAttribute('tabindex', '-1');
          if (option.selected) {
            li.classList.add('selected');
          }

          const countryCode = option.value.toLowerCase();
          const flagPath = flagBasePath + countryCode + '.svg';

          // Build option content based on flag position.
          let content = '';
          if (flagPosition === 'before') {
            content = '<img src="' + flagPath + '" alt="" class="country-flag" style="width: 20px; height: 15px; vertical-align: middle; margin-right: 6px;" /> ' + option.text;
          }
          else if (flagPosition === 'after') {
            content = option.text + ' <img src="' + flagPath + '" alt="" class="country-flag" style="width: 20px; height: 15px; vertical-align: middle; margin-left: 6px;" />';
          }
          else {
            // For "only", show flag only (country name in alt/title for accessibility)
            content = '<img src="' + flagPath + '" alt="' + option.text + '" class="country-flag" style="width: 20px; height: 15px; vertical-align: middle;" title="' + option.text + '" />';
          }

          li.innerHTML = content;
          dropdown.appendChild(li);
        });

        // Update display text.
        function updateDisplay() {
          if (selectedValues.length > 0) {
            let displayContent = '';
            
            if (isMultiple) {
              // For multi-select: always show tags/chips with X buttons for each selection.
              selectedValues.forEach(function(val) {
                const opt = Array.from(selectElement.options).find(function(o) {
                  return o.value === val;
                });
                if (opt) {
                  const code = val.toLowerCase();
                  const path = flagBasePath + code + '.svg';
                  const countryName = opt.text;
                  
                  // Build tag/chip HTML with flag, name, and remove button.
                  let flagHtml = '';
                  if (flagPosition === 'before') {
                    flagHtml = '<img src="' + path + '" alt="" class="country-flag" style="width: 16px; height: 12px; vertical-align: middle; margin-right: 4px;" />';
                  } else if (flagPosition === 'after') {
                    flagHtml = '<img src="' + path + '" alt="" class="country-flag" style="width: 16px; height: 12px; vertical-align: middle; margin-left: 4px;" />';
                  } else if (flagPosition === 'only') {
                    flagHtml = '<img src="' + path + '" alt="' + countryName + '" class="country-flag" style="width: 16px; height: 12px; vertical-align: middle;" title="' + countryName + '" />';
                  }
                  
                  displayContent += '<span class="advanced-country-field-tag" data-value="' + htmlspecialchars(val) + '">';
                  if (flagPosition === 'before' || flagPosition === 'only') {
                    displayContent += flagHtml + '<span class="tag-name">' + (flagPosition === 'only' ? '' : countryName) + '</span>';
                  } else if (flagPosition === 'after') {
                    displayContent += '<span class="tag-name">' + countryName + '</span>' + flagHtml;
                  }
                  displayContent += '<button type="button" class="tag-remove" aria-label="' + Drupal.t('Remove @country', {'@country': countryName}) + '" tabindex="0">×</button></span>';
                }
              });
            }
            else {
              // Single select: show flag and text.
              const valueToShow = selectedValues[0];
              const selectedOption = Array.from(selectElement.options).find(function(opt) {
                return opt.value === valueToShow;
              });
              
              if (selectedOption) {
                const countryCode = valueToShow.toLowerCase();
                const flagPath = flagBasePath + countryCode + '.svg';
                
                if (flagPosition === 'before') {
                  displayContent = '<img src="' + flagPath + '" alt="" class="country-flag" style="width: 20px; height: 15px; vertical-align: middle; margin-right: 6px;" /> ' + selectedOption.text;
                }
                else if (flagPosition === 'after') {
                  displayContent = selectedOption.text + ' <img src="' + flagPath + '" alt="" class="country-flag" style="width: 20px; height: 15px; vertical-align: middle; margin-left: 6px;" />';
                }
                else {
                  // For "only", show flag only (country name in alt/title for accessibility)
                  displayContent = '<img src="' + flagPath + '" alt="' + selectedOption.text + '" class="country-flag" style="width: 20px; height: 15px; vertical-align: middle;" title="' + selectedOption.text + '" />';
                }
              }
            }
            
            display.innerHTML = displayContent || placeholder;
          }
          else {
            display.textContent = placeholder;
          }
        }
        
        // HTML entity encoding helper (for XSS prevention).
        function htmlspecialchars(text) {
          const div = document.createElement('div');
          div.textContent = text;
          return div.innerHTML;
        }
        updateDisplay();

        // Add search input to dropdown if enabled.
        if (searchInput) {
          const searchWrapper = document.createElement('li');
          searchWrapper.className = 'advanced-country-field-search-wrapper';
          searchWrapper.style.listStyle = 'none';
          searchWrapper.style.margin = '0';
          searchWrapper.style.padding = '8px';
          searchWrapper.style.borderBottom = '1px solid #ccc';
          searchWrapper.appendChild(searchInput);
          dropdown.insertBefore(searchWrapper, dropdown.firstChild);

          // Add search functionality.
          searchInput.addEventListener('input', function(e) {
            e.stopPropagation();
            const searchTerm = this.value.toLowerCase();
            
            dropdown.querySelectorAll('li[role="option"]').forEach(function(li) {
              const text = li.textContent.toLowerCase();
              if (text.includes(searchTerm)) {
                li.style.display = '';
              } else {
                li.style.display = 'none';
              }
            });
          });
        }

        wrapper.appendChild(display);
        wrapper.appendChild(dropdown);

        // Insert wrapper before select and hide select.
        selectElement.parentNode.insertBefore(wrapper, selectElement);
        selectElement.style.position = 'absolute';
        selectElement.style.left = '-9999px';
        selectElement.style.width = '1px';
        selectElement.style.height = '1px';
        selectElement.style.opacity = '0';
        selectElement.setAttribute('aria-hidden', 'true');
        selectElement.classList.add('advanced-country-field-enhanced');

        // Toggle dropdown.
        function toggleDropdown(open) {
          const isOpen = dropdown.style.display !== 'none';
          if (open === undefined) {
            open = !isOpen;
          }

          if (open) {
            dropdown.style.display = 'block';
            wrapper.setAttribute('aria-expanded', 'true');
            wrapper.classList.add('is-open');
            
            // Focus search input if enabled, otherwise focus first option.
            if (searchInput) {
              searchInput.focus();
            } else {
              const firstOption = dropdown.querySelector('li[role="option"]');
              if (firstOption) {
                firstOption.focus();
              }
            }
          }
          else {
            dropdown.style.display = 'none';
            wrapper.setAttribute('aria-expanded', 'false');
            wrapper.classList.remove('is-open');
            
            // Clear search input when closing.
            if (searchInput) {
              searchInput.value = '';
              // Restore all options visibility.
              dropdown.querySelectorAll('li[role="option"]').forEach(function(li) {
                li.style.display = '';
              });
            }
          }
        }

        // Display click handler.
        display.addEventListener('click', function(e) {
          e.preventDefault();
          toggleDropdown();
        });

        // Keyboard navigation.
        wrapper.addEventListener('keydown', function(e) {
          const options = Array.from(dropdown.querySelectorAll('li[role="option"]'));
          const focused = dropdown.querySelector('li[role="option"]:focus');
          let focusedIndex = focused ? options.indexOf(focused) : -1;

          switch (e.key) {
            case 'Enter':
            case ' ':
              e.preventDefault();
              if (focused) {
                focused.click();
              }
              else if (dropdown.style.display === 'none') {
                toggleDropdown(true);
              }
              break;
            case 'Escape':
              e.preventDefault();
              toggleDropdown(false);
              display.focus();
              break;
            case 'ArrowDown':
              e.preventDefault();
              if (dropdown.style.display === 'none') {
                toggleDropdown(true);
              }
              focusedIndex = (focusedIndex + 1) % options.length;
              options[focusedIndex].focus();
              break;
            case 'ArrowUp':
              e.preventDefault();
              focusedIndex = focusedIndex <= 0 ? options.length - 1 : focusedIndex - 1;
              options[focusedIndex].focus();
              break;
          }
        });

        // Prevent click on search wrapper from closing dropdown.
        if (searchInput) {
          searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
          });
          
          // Allow Enter key to select first visible option in search.
          searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
              e.preventDefault();
              const firstVisible = dropdown.querySelector('li[role="option"][style*="display: "]') || 
                                   dropdown.querySelector('li[role="option"]:not([style*="display: none"])');
              if (firstVisible) {
                firstVisible.click();
              }
            } else if (e.key === 'Escape') {
              e.preventDefault();
              toggleDropdown(false);
              display.focus();
            }
          });
        }

        // Option click handler.
        dropdown.querySelectorAll('li[role="option"]').forEach(function(li) {
          li.addEventListener('click', function(e) {
            e.preventDefault();
            const value = li.getAttribute('data-value');
            const optionElement = Array.from(selectElement.options).find(function(opt) {
              return opt.value === value;
            });
            
            if (!optionElement) {
              return;
            }

            if (isMultiple) {
              // Toggle selection for multi-select.
              optionElement.selected = !optionElement.selected;
              
              // Update selectedValues array.
              if (optionElement.selected) {
                if (selectedValues.indexOf(value) === -1) {
                  selectedValues.push(value);
                }
              } else {
                selectedValues = selectedValues.filter(function(v) {
                  return v !== value;
                });
              }
              
              // Update li selected state.
              li.classList.toggle('selected', optionElement.selected);
              li.setAttribute('aria-selected', optionElement.selected ? 'true' : 'false');
            } else {
              // Single select behavior.
              selectedValues = [value];
              
              // Clear previous selections.
              Array.from(selectElement.options).forEach(function(opt) {
                opt.selected = opt.value === value;
              });
              
              // Update all li elements.
              dropdown.querySelectorAll('li[role="option"]').forEach(function(item) {
                const isSelected = item === li;
                item.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                item.classList.toggle('selected', isSelected);
              });
              
              // Close dropdown for single select.
              toggleDropdown(false);
              display.focus();
            }

            // Update select element and trigger change event.
            selectElement.dispatchEvent(new Event('change', { bubbles: true }));

            // Update display.
            updateDisplay();
          });
        });

        // Close dropdown when clicking outside.
        document.addEventListener('click', function(e) {
          if (!wrapper.contains(e.target)) {
            toggleDropdown(false);
          }
        });
        
        // Handle remove button clicks on tags (event delegation for dynamically created tags).
        display.addEventListener('click', function(e) {
          if (e.target.classList.contains('tag-remove') || e.target.closest('.tag-remove')) {
            e.preventDefault();
            e.stopPropagation();
            
            const removeBtn = e.target.classList.contains('tag-remove') ? e.target : e.target.closest('.tag-remove');
            const tag = removeBtn.closest('.advanced-country-field-tag');
            
            if (tag) {
              const valueToRemove = tag.getAttribute('data-value');
              
              // Remove from selectedValues array.
              selectedValues = selectedValues.filter(function(v) {
                return v !== valueToRemove;
              });
              
              // Update the select element.
              const optionElement = Array.from(selectElement.options).find(function(opt) {
                return opt.value === valueToRemove;
              });
              
              if (optionElement) {
                optionElement.selected = false;
                
                // Update the corresponding dropdown option UI.
                const li = dropdown.querySelector('li[data-value="' + valueToRemove + '"]');
                if (li) {
                  li.classList.remove('selected');
                  li.setAttribute('aria-selected', 'false');
                }
              }
              
              // Trigger change event.
              selectElement.dispatchEvent(new Event('change', { bubbles: true }));
              
              // Update display.
              updateDisplay();
            }
          }
        });
      });
    }
  };

  /**
   * Adds flags to radio buttons and checkboxes.
   */
  Drupal.behaviors.advancedCountryFieldRadiosCheckboxes = {
    attach: function (context, settings) {
      // Find containers with radios/checkboxes that have flags enabled.
      const containers = context.querySelectorAll('.form-radios, .form-checkboxes');
      
      containers.forEach(function (container) {
        // Check if this container is part of an advanced country field widget with flags enabled.
        let widgetElement = container.closest('[data-show-flags="1"]');
        if (!widgetElement) {
          // Try parent elements
          widgetElement = container.parentElement?.closest('[data-show-flags="1"]');
        }
        if (!widgetElement) {
          // Try finding by advanced-country-field-widget class
          widgetElement = container.closest('.advanced-country-field-widget');
          if (widgetElement && widgetElement.getAttribute('data-show-flags') !== '1') {
            widgetElement = null;
          }
        }
        
        if (!widgetElement || widgetElement.getAttribute('data-show-flags') !== '1') {
          return;
        }
        
        // Use once() to ensure we only process each container once.
        once('advanced-country-field-radios-checkboxes-flags', container).forEach(function (processedContainer) {
        
          // Get flag configuration from widget element's data attributes.
          const flagPosition = widgetElement.getAttribute('data-flag-position') || 'before';
          let flagBasePath = widgetElement.getAttribute('data-flag-base-path');
        
          // Fallback: try to get from settings if data attribute not available.
          if (!flagBasePath && settings.advancedCountryField && settings.advancedCountryField.flags) {
            const fieldSelector = widgetElement.getAttribute('data-drupal-selector') || 
                                 processedContainer.querySelector('input[type="radio"], input[type="checkbox"]')?.getAttribute('name') || '';
            Object.keys(settings.advancedCountryField.flags).forEach(function (fieldName) {
              if (fieldSelector.indexOf(fieldName) !== -1) {
                const flagConfig = settings.advancedCountryField.flags[fieldName];
                flagBasePath = flagConfig.flag_base_path;
              }
            });
          }
          
          // Final fallback to default path.
          if (!flagBasePath) {
            flagBasePath = '/libraries/country-flag-icons/3x2/';
          }
          
          // Ensure path ends with /
          if (flagBasePath.charAt(flagBasePath.length - 1) !== '/') {
            flagBasePath += '/';
          }

          // Find all form items within the container.
          const formItems = processedContainer.querySelectorAll('.form-item');
          
          formItems.forEach(function (formItem) {
            const input = formItem.querySelector('input[type="radio"], input[type="checkbox"]');
            if (!input) {
              return;
            }
            
            const value = input.value;
            if (!value) {
              return;
            }
            
            // Find label - could be with for attribute or nested.
            let label = formItem.querySelector('label[for="' + input.id + '"]');
            if (!label && input.id) {
              label = formItem.querySelector('label');
            }
            // If still no label, skip.
            if (!label) {
              return;
            }
            
            // Skip if already has flag image.
            if (label.querySelector('.country-flag')) {
              return;
            }
            
            const countryCode = value.toLowerCase();
            const flagPath = flagBasePath + countryCode + '.svg';
            
            // Get original label text before modifying.
            let labelText = label.textContent.trim();
            
            // Create flag image.
            const flagImg = document.createElement('img');
            flagImg.src = flagPath;
            flagImg.alt = '';
            flagImg.className = 'country-flag';
            flagImg.style.cssText = 'width: 20px; height: 15px; vertical-align: middle; display: inline-block;';
            
            // Build label content based on flag position.
            if (flagPosition === 'before') {
              flagImg.style.marginRight = '6px';
              // Insert flag at the beginning of label.
              label.insertBefore(flagImg, label.firstChild);
              // Add space after flag if needed.
              if (!label.firstChild.nextSibling || label.firstChild.nextSibling.nodeType !== Node.TEXT_NODE) {
                label.insertBefore(document.createTextNode(' '), label.firstChild.nextSibling || null);
              }
            }
            else if (flagPosition === 'after') {
              flagImg.style.marginLeft = '6px';
              // Append flag at the end of label.
              if (!label.lastChild || label.lastChild.nodeType !== Node.TEXT_NODE || !label.lastChild.textContent.trim().endsWith(' ')) {
                label.appendChild(document.createTextNode(' '));
              }
              label.appendChild(flagImg);
            }
            else {
              // For "only", show flag only with title for accessibility.
              flagImg.alt = labelText;
              flagImg.title = labelText;
              // Clear label and add flag.
              label.innerHTML = '';
              label.appendChild(flagImg);
              // Also add visually hidden text for screen readers.
              const hiddenText = document.createElement('span');
              hiddenText.className = 'visually-hidden';
              hiddenText.textContent = labelText;
              label.appendChild(hiddenText);
            }
          });
        });
      });
    }
  };

  /**
   * Accessibility enhancements for keyboard navigation.
   */
  Drupal.behaviors.advancedCountryFieldAccessibility = {
    attach: function (context, settings) {
      once('advanced-country-field-a11y', '.advanced-country-field-widget', context).forEach(function (element) {
        // Skip enhanced selects - they already have full accessibility support via custom dropdown.
        if (element.matches('.advanced-country-field-enhanced')) {
          return;
        }
        
        // Ensure proper ARIA attributes for native selects.
        if (element.tagName === 'SELECT') {
          element.setAttribute('role', 'combobox');
          element.setAttribute('aria-haspopup', 'listbox');
          
          if (element.getAttribute('data-search-enabled') === '1') {
            element.setAttribute('aria-autocomplete', 'list');
          }
        }
        
        // Add keyboard navigation hints.
        if (!element.getAttribute('aria-describedby')) {
          const hint = document.createElement('div');
          hint.id = element.id + '-hint';
          hint.className = 'visually-hidden';
          hint.textContent = Drupal.t('Use arrow keys to navigate, Enter to select');
          element.parentNode.insertBefore(hint, element);
          element.setAttribute('aria-describedby', hint.id);
        }
      });
    }
  };

})(Drupal, once);

