/**
 * Lytta Filter Frontend JS
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        // Initialize for each widget instance
        $('.lytta-filter-wrapper').each(function () {
            var $wrapper = $(this);
            var $form = $wrapper.find('.lytta-filter-form');
            var $resultsContainer = $wrapper.find('.lytta-results-container');
            var $loader = $wrapper.find('.lytta-filter-loader');
            var autoSubmit = $wrapper.data('auto-submit') === 'yes';

            // Handle Input Changes
            var typingTimer;
            var doneTypingInterval = 500; // Delay for text inputs

            $form.find('input, select').on('change', function () {
                // Prevent double trigger on text inputs
                if ($(this).attr('type') !== 'text' && $(this).attr('type') !== 'number') {
                    if (autoSubmit) {
                        updateAndReload();
                    }
                }
            });

            $form.find('input[type="text"], input[type="number"]').on('keyup', function () {
                if (autoSubmit) {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(updateAndReload, doneTypingInterval);
                }
            });

            $form.on('submit', function (e) {
                e.preventDefault();
                updateAndReload();
            });

            function updateAndReload() {
                var formData = $form.serializeArray();
                var params = new URLSearchParams(window.location.search);

                // Clear old lytta params
                for (const key of params.keys()) {
                    if (key.startsWith('lytta_')) {
                        params.delete(key);
                    }
                }

                // Add new active params
                $.each(formData, function (i, field) {
                    if (field.value !== "") {
                        params.set(field.name, field.value);
                    }
                });

                var newUrl = window.location.pathname;
                if (params.toString()) {
                    newUrl += '?' + params.toString();
                }

                // Reload page with new URL parameters.
                // Elementor Pro Loop Grid will detect the params via our PHP hook and filter the grid.
                window.location.href = newUrl;
            }

            // Handle Reset Button
            $wrapper.on('click', '.lytta-btn-reset', function (e) {
                e.preventDefault();
                var params = new URLSearchParams(window.location.search);
                var hasChanges = false;
                for (const key of Array.from(params.keys())) {
                    if (key.startsWith('lytta_')) {
                        params.delete(key);
                        hasChanges = true;
                    }
                }

                if (hasChanges) {
                    var newUrl = window.location.pathname;
                    if (params.toString()) {
                        newUrl += '?' + params.toString();
                    }
                    window.location.href = newUrl;
                }
            });

            // Handle Active Chip Removal
            $wrapper.on('click', '.lytta-chip-remove', function (e) {
                e.preventDefault();
                var $chip = $(this).closest('.lytta-chip');
                var param = $chip.data('param');
                var minParam = $chip.data('min-param');
                var maxParam = $chip.data('max-param');

                var params = new URLSearchParams(window.location.search);
                var hasChanges = false;

                // If it's a range chip, it might have two params
                if (minParam || maxParam) {
                    if (params.has(minParam)) { params.delete(minParam); hasChanges = true; }
                    if (params.has(maxParam)) { params.delete(maxParam); hasChanges = true; }
                } else if (param && params.has(param)) {
                    params.delete(param);
                    hasChanges = true;
                }

                if (hasChanges) {
                    var newUrl = window.location.pathname;
                    if (params.toString()) {
                        newUrl += '?' + params.toString();
                    }
                    window.location.href = newUrl;
                }
            });

            // Initial state from URL (if page is loaded with query params)
            function restoreStateFromURL() {
                var params = new URLSearchParams(window.location.search);
                var hasFilters = false;

                params.forEach(function (value, key) {
                    if (key.startsWith('lytta_')) {
                        var $input = $form.find('[name="' + key + '"]');
                        if ($input.length) {
                            $input.val(value);
                            hasFilters = true;
                        }
                    }
                });
            }

            restoreStateFromURL();
        });

    });

})(jQuery);
