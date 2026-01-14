/**
 * Main Public JavaScript for Hotel Cleaning Calculator PRO
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/public/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    /**
     * Main Public Object
     */
    const HCCPublic = {

        /**
         * Calculator instances
         */
        calculators: {},

        /**
         * Initialize all calculators on page
         */
        init: function() {
            $('.hcc-calculator-wrapper').each(function(index) {
                const $calculator = $(this);
                const calculatorId = 'hcc-calc-' + index;
                
                $calculator.attr('data-calculator-id', calculatorId);
                
                HCCPublic.calculators[calculatorId] = {
                    element: $calculator,
                    rooms: [],
                    calculation: null
                };
                
                HCCPublic.initCalculator(calculatorId);
            });
        },

        /**
         * Initialize single calculator
         */
        initCalculator: function(calculatorId) {
            const calc = HCCPublic.calculators[calculatorId];
            const $calc = calc.element;
            
            // Initialize room repeater
            if (typeof HCCRoomRepeater !== 'undefined') {
                HCCRoomRepeater.init($calc);
            }
            
            // Initialize calculator engine
            if (typeof HCCCalculator !== 'undefined') {
                HCCCalculator.init($calc);
            }
            
            // Initialize quote form if present
            if ($calc.find('.hcc-quote-form').length && typeof HCCQuoteForm !== 'undefined') {
                HCCQuoteForm.init($calc);
            }
            
            // Add initial room if none exist
            if ($calc.find('.hcc-room-card').length === 0) {
                HCCPublic.addInitialRoom($calc);
            }
        },

        /**
         * Add initial room
         */
        addInitialRoom: function($calc) {
            const $addButton = $calc.find('.hcc-add-room-btn');
            if ($addButton.length) {
                $addButton.trigger('click');
            }
        },

        /**
         * Show message
         */
        showMessage: function($calculator, message, type) {
            type = type || 'info';
            
            const $message = $('<div class="hcc-message ' + type + '">' + message + '</div>');
            
            // Remove existing messages
            $calculator.find('.hcc-message').remove();
            
            // Add new message
            $calculator.prepend($message);
            
            // Auto-remove success messages
            if (type === 'success') {
                setTimeout(function() {
                    $message.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
            
            // Scroll to message
            HCCPublic.scrollToElement($message);
        },

        /**
         * Scroll to element
         */
        scrollToElement: function($element, offset) {
            offset = offset || 100;
            
            $('html, body').animate({
                scrollTop: $element.offset().top - offset
            }, 300);
        },

        /**
         * Format currency
         */
        formatCurrency: function(amount) {
            const currency = hccData.currency || '$';
            const position = hccData.currencyPos || 'before';
            const decimals = parseInt(hccData.decimalPlaces) || 2;
            
            const formatted = parseFloat(amount).toFixed(decimals);
            
            if (position === 'before') {
                return currency + formatted;
            } else {
                return formatted + currency;
            }
        },

        /**
         * Get translated string
         */
        getString: function(key) {
            if (typeof hccData !== 'undefined' && hccData.strings && hccData.strings[key]) {
                return hccData.strings[key];
            }
            return key;
        },

        /**
         * Debounce function
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Validate email
         */
        isValidEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        /**
         * Show loading state
         */
        showLoading: function($element) {
            $element.addClass('hcc-calculating');
            $element.find('.hcc-btn').prop('disabled', true);
        },

        /**
         * Hide loading state
         */
        hideLoading: function($element) {
            $element.removeClass('hcc-calculating');
            $element.find('.hcc-btn').prop('disabled', false);
        },

        /**
         * Handle errors
         */
        handleError: function($calculator, error) {
            console.error('HCC Error:', error);
            
            let message = HCCPublic.getString('errorMessage');
            
            if (error.responseJSON && error.responseJSON.data && error.responseJSON.data.message) {
                message = error.responseJSON.data.message;
            }
            
            HCCPublic.showMessage($calculator, message, 'error');
            HCCPublic.hideLoading($calculator);
        },

        /**
         * Get calculator data
         */
        getCalculatorData: function($calculator) {
            const calculatorId = $calculator.attr('data-calculator-id');
            return HCCPublic.calculators[calculatorId];
        },

        /**
         * Update calculator data
         */
        updateCalculatorData: function($calculator, data) {
            const calculatorId = $calculator.attr('data-calculator-id');
            HCCPublic.calculators[calculatorId] = $.extend(
                HCCPublic.calculators[calculatorId],
                data
            );
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        HCCPublic.init();
    });

    /**
     * Make HCCPublic globally accessible
     */
    window.HCCPublic = HCCPublic;

})(jQuery);