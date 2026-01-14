/**
 * Calculator Logic JavaScript
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/public/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    const HCCCalculator = {

        /**
         * Initialize calculator
         */
        init: function($calculator) {
            this.bindEvents($calculator);
            this.setupAutoCalculate($calculator);
        },

        /**
         * Bind events
         */
        bindEvents: function($calculator) {
            // Calculate button
            $calculator.on('click', '.hcc-calculate-btn', function(e) {
                e.preventDefault();
                HCCCalculator.calculate($calculator);
            });
            
            // Auto-calculate on change
            $calculator.on('change', '.hcc-room-type, .hcc-room-area', function() {
                HCCCalculator.calculateRoomSubtotal($(this).closest('.hcc-room-card'));
            });
            
            // Discount code
            $calculator.on('click', '.hcc-apply-discount', function(e) {
                e.preventDefault();
                HCCCalculator.applyDiscountCode($calculator);
            });
        },

        /**
         * Setup auto-calculate
         */
        setupAutoCalculate: function($calculator) {
            const debouncedCalculate = HCCPublic.debounce(function() {
                HCCCalculator.calculate($calculator, true);
            }, 500);
            
            $calculator.on('change input', '.hcc-room-type, .hcc-room-area', function() {
                debouncedCalculate();
            });
        },

        /**
         * Calculate room subtotal
         */
        calculateRoomSubtotal: function($room) {
            const roomType = $room.find('.hcc-room-type').val();
            const area = parseFloat($room.find('.hcc-room-area').val()) || 0;
            
            if (!roomType || area <= 0) {
                $room.find('.hcc-room-subtotal-value').text('-');
                return;
            }
            
            // Get room type data
            const roomTypeData = HCCCalculator.getRoomTypeData(roomType);
            
            if (roomTypeData) {
                const subtotal = area * roomTypeData.price_per_m2;
                $room.find('.hcc-room-subtotal-value').text(HCCPublic.formatCurrency(subtotal));
            }
        },

        /**
         * Get room type data
         */
        getRoomTypeData: function(roomTypeId) {
            if (typeof hccData === 'undefined' || !hccData.roomTypes) {
                return null;
            }
            
            for (let i = 0; i < hccData.roomTypes.length; i++) {
                if (hccData.roomTypes[i].id === roomTypeId) {
                    return hccData.roomTypes[i];
                }
            }
            
            return null;
        },

        /**
         * Calculate total
         */
        calculate: function($calculator, silent) {
            silent = silent || false;
            
            // Collect room data
            const rooms = HCCCalculator.collectRoomData($calculator);
            
            if (rooms.length === 0) {
                if (!silent) {
                    HCCPublic.showMessage($calculator, 'Please add at least one room', 'error');
                }
                return;
            }
            
            // Validate rooms
            const validation = HCCCalculator.validateRooms(rooms);
            if (!validation.valid) {
                if (!silent) {
                    HCCPublic.showMessage($calculator, validation.message, 'error');
                }
                return;
            }
            
            // Show loading
            if (!silent) {
                HCCPublic.showLoading($calculator);
            }
            
            // Get discount code if any
            const discountCode = $calculator.find('.hcc-discount-code-input').val() || '';
            
            // Send AJAX request
            $.ajax({
                url: hccData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hcc_calculate_price',
                    nonce: hccData.nonce,
                    rooms: JSON.stringify(rooms),
                    discount_code: discountCode
                },
                success: function(response) {
                    if (response.success) {
                        HCCCalculator.displayResults($calculator, response.data);
                        
                        // Update calculator data
                        HCCPublic.updateCalculatorData($calculator, {
                            rooms: rooms,
                            calculation: response.data
                        });
                        
                        if (!silent) {
                            HCCPublic.scrollToElement($calculator.find('.hcc-calculation-summary'));
                        }
                    } else {
                        HCCPublic.showMessage($calculator, response.data.message, 'error');
                    }
                },
                error: function(error) {
                    HCCPublic.handleError($calculator, error);
                },
                complete: function() {
                    if (!silent) {
                        HCCPublic.hideLoading($calculator);
                    }
                }
            });
        },

        /**
         * Collect room data
         */
        collectRoomData: function($calculator) {
            const rooms = [];
            
            $calculator.find('.hcc-room-card').each(function() {
                const $room = $(this);
                const roomType = $room.find('.hcc-room-type').val();
                const area = parseFloat($room.find('.hcc-room-area').val());
                
                if (roomType && area > 0) {
                    rooms.push({
                        type_id: roomType,
                        area: area
                    });
                }
            });
            
            return rooms;
        },

        /**
         * Validate rooms
         */
        validateRooms: function(rooms) {
            if (rooms.length === 0) {
                return {
                    valid: false,
                    message: 'Please add at least one room with valid data'
                };
            }
            
            for (let i = 0; i < rooms.length; i++) {
                if (!rooms[i].type_id) {
                    return {
                        valid: false,
                        message: 'Please select a room type for all rooms'
                    };
                }
                
                if (!rooms[i].area || rooms[i].area <= 0) {
                    return {
                        valid: false,
                        message: 'Please enter a valid area for all rooms'
                    };
                }
            }
            
            return { valid: true };
        },

        /**
         * Display results
         */
        displayResults: function($calculator, data) {
            const $summary = $calculator.find('.hcc-calculation-summary');
            
            if ($summary.length === 0) {
                // Create summary if doesn't exist
                const summaryHTML = HCCCalculator.buildSummaryHTML(data);
                $calculator.find('.hcc-rooms-container').after(summaryHTML);
            } else {
                // Update existing summary
                HCCCalculator.updateSummary($summary, data);
            }
            
            $summary.fadeIn(300);
        },

        /**
         * Build summary HTML
         */
        buildSummaryHTML: function(data) {
            let html = '<div class="hcc-calculation-summary" style="display: none;">';
            
            html += '<div class="hcc-summary-row">';
            html += '<span class="hcc-summary-label">' + HCCPublic.getString('subtotal') + ':</span>';
            html += '<span class="hcc-summary-value">' + data.subtotal + '</span>';
            html += '</div>';
            
            if (data.discount_raw > 0) {
                html += '<div class="hcc-summary-row hcc-discount-row">';
                html += '<span class="hcc-summary-label">' + HCCPublic.getString('discountApplied') + ':</span>';
                html += '<span class="hcc-summary-value">-' + data.discount_amount + '</span>';
                html += '</div>';
            }
            
            html += '<div class="hcc-summary-row hcc-total-row">';
            html += '<span class="hcc-summary-label"><strong>' + HCCPublic.getString('total') + ':</strong></span>';
            html += '<span class="hcc-summary-value"><strong>' + data.total_price + '</strong></span>';
            html += '</div>';
            
            html += '</div>';
            
            return html;
        },

        /**
         * Update summary
         */
        updateSummary: function($summary, data) {
            $summary.find('.hcc-summary-row:not(.hcc-discount-row):not(.hcc-total-row) .hcc-summary-value').text(data.subtotal);
            $summary.find('.hcc-total-row .hcc-summary-value strong').text(data.total_price);
            
            // Handle discount row
            if (data.discount_raw > 0) {
                if ($summary.find('.hcc-discount-row').length === 0) {
                    const discountHTML = '<div class="hcc-summary-row hcc-discount-row">' +
                        '<span class="hcc-summary-label">' + HCCPublic.getString('discountApplied') + ':</span>' +
                        '<span class="hcc-summary-value">-' + data.discount_amount + '</span>' +
                        '</div>';
                    $summary.find('.hcc-total-row').before(discountHTML);
                } else {
                    $summary.find('.hcc-discount-row .hcc-summary-value').text('-' + data.discount_amount);
                }
            } else {
                $summary.find('.hcc-discount-row').remove();
            }
        },

        /**
         * Apply discount code
         */
        applyDiscountCode: function($calculator) {
            const code = $calculator.find('.hcc-discount-code-input').val();
            
            if (!code) {
                HCCPublic.showMessage($calculator, 'Please enter a discount code', 'error');
                return;
            }
            
            $.ajax({
                url: hccData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hcc_validate_discount_code',
                    nonce: hccData.nonce,
                    code: code
                },
                success: function(response) {
                    if (response.success) {
                        HCCPublic.showMessage($calculator, response.data.message, 'success');
                        HCCCalculator.calculate($calculator);
                    } else {
                        HCCPublic.showMessage($calculator, response.data.message, 'error');
                    }
                }
            });
        }
    };

    /**
     * Make HCCCalculator globally accessible
     */
    window.HCCCalculator = HCCCalculator;

})(jQuery);