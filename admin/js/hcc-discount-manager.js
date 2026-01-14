/**
 * Discount Manager JavaScript
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    const DiscountManager = {

        /**
         * Initialize discount manager
         */
        init: function() {
            this.handleAddDiscount();
            this.handleEditDiscount();
            this.handleDeleteDiscount();
            this.handleSaveDiscount();
            this.handleDiscountTypeChange();
            this.handleConditionsToggle();
            this.handleDatePickers();
        },

        /**
         * Handle add new discount
         */
        handleAddDiscount: function() {
            $(document).on('click', '#hcc-add-discount', function(e) {
                e.preventDefault();
                $('#hcc-discount-modal').fadeIn(300);
                DiscountManager.resetDiscountForm();
            });
        },

        /**
         * Handle edit discount
         */
        handleEditDiscount: function() {
            $(document).on('click', '.hcc-edit-discount', function(e) {
                e.preventDefault();
                
                const $row = $(this).closest('tr');
                const discountId = $row.data('id');
                
                // Get discount data via AJAX
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_get_discount_rule',
                        nonce: hccAdmin.nonce,
                        rule_id: discountId
                    },
                    success: function(response) {
                        if (response.success) {
                            DiscountManager.populateDiscountForm(response.data);
                            $('#hcc-discount-modal').fadeIn(300);
                        }
                    }
                });
            });
        },

        /**
         * Handle delete discount
         */
        handleDeleteDiscount: function() {
            $(document).on('click', '.hcc-delete-discount', function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to delete this discount rule?')) {
                    return;
                }
                
                const $row = $(this).closest('tr');
                const discountId = $row.data('id');
                
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_delete_discount_rule',
                        nonce: hccAdmin.nonce,
                        rule_id: discountId
                    },
                    beforeSend: function() {
                        HCCAdmin.showLoading();
                    },
                    success: function(response) {
                        if (response.success) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                            });
                            HCCAdmin.showNotice('success', response.data.message);
                        } else {
                            HCCAdmin.showNotice('error', response.data.message);
                        }
                    },
                    complete: function() {
                        HCCAdmin.hideLoading();
                    }
                });
            });
        },

        /**
         * Handle save discount
         */
        handleSaveDiscount: function() {
            $(document).on('submit', '#hcc-discount-form', function(e) {
                e.preventDefault();
                
                const $form = $(this);
                const formData = DiscountManager.getDiscountFormData();
                
                // Validate
                if (!DiscountManager.validateDiscountForm(formData)) {
                    return;
                }
                
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_save_discount_rule',
                        nonce: hccAdmin.nonce,
                        discount_data: JSON.stringify(formData)
                    },
                    beforeSend: function() {
                        $form.find('[type="submit"]').prop('disabled', true).html('Saving...');
                    },
                    success: function(response) {
                        if (response.success) {
                            HCCAdmin.showNotice('success', response.data.message);
                            $('#hcc-discount-modal').fadeOut(300);
                            location.reload(); // Reload to show updated list
                        } else {
                            HCCAdmin.showNotice('error', response.data.message);
                        }
                    },
                    complete: function() {
                        $form.find('[type="submit"]').prop('disabled', false).html('Save Discount Rule');
                    }
                });
            });
        },

        /**
         * Handle discount type change
         */
        handleDiscountTypeChange: function() {
            $(document).on('change', '#discount-type', function() {
                const type = $(this).val();
                const $valueLabel = $('label[for="discount-value"]');
                
                if (type === 'percentage') {
                    $valueLabel.text('Discount Percentage (%)');
                    $('#discount-value').attr('max', '100');
                } else {
                    $valueLabel.text('Discount Amount ($)');
                    $('#discount-value').removeAttr('max');
                }
            });
        },

        /**
         * Handle conditions toggle
         */
        handleConditionsToggle: function() {
            $(document).on('change', '.hcc-condition-toggle', function() {
                const $toggle = $(this);
                const target = $toggle.data('target');
                const $targetField = $(target);
                
                if ($toggle.is(':checked')) {
                    $targetField.prop('disabled', false).closest('.hcc-form-group').fadeIn(300);
                } else {
                    $targetField.prop('disabled', true).closest('.hcc-form-group').fadeOut(300);
                }
            });
        },

        /**
         * Handle date pickers
         */
        handleDatePickers: function() {
            if (typeof $.fn.datepicker !== 'undefined') {
                $('.hcc-datepicker').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });
            }
        },

        /**
         * Get discount form data
         */
        getDiscountFormData: function() {
            const conditions = {};
            
            // Collect conditions if enabled
            if ($('#enable-min-area').is(':checked')) {
                conditions.min_area = parseFloat($('#min-area').val());
            }
            if ($('#enable-min-rooms').is(':checked')) {
                conditions.min_rooms = parseInt($('#min-rooms').val());
            }
            if ($('#enable-min-subtotal').is(':checked')) {
                conditions.min_subtotal = parseFloat($('#min-subtotal').val());
            }
            if ($('#enable-room-types').is(':checked')) {
                conditions.room_types = $('#room-types').val();
            }
            
            // Collect days of week if enabled
            let daysOfWeek = [];
            if ($('#enable-days').is(':checked')) {
                $('.day-checkbox:checked').each(function() {
                    daysOfWeek.push($(this).val());
                });
            }
            
            return {
                id: $('#discount-id').val() || '',
                rule_name: $('#rule-name').val(),
                description: $('#rule-description').val(),
                discount_type: $('#discount-type').val(),
                discount_value: parseFloat($('#discount-value').val()),
                conditions: conditions,
                date_start: $('#date-start').val() || null,
                date_end: $('#date-end').val() || null,
                days_of_week: daysOfWeek,
                priority: parseInt($('#priority').val()),
                stackable: $('#stackable').is(':checked'),
                discount_code: $('#discount-code').val(),
                usage_limit: $('#usage-limit').val() ? parseInt($('#usage-limit').val()) : null,
                active: $('#discount-active').is(':checked')
            };
        },

        /**
         * Validate discount form
         */
        validateDiscountForm: function(data) {
            let isValid = true;
            
            // Clear previous errors
            $('.error').removeClass('error');
            
            // Validate rule name
            if (!data.rule_name || data.rule_name.trim() === '') {
                $('#rule-name').addClass('error');
                HCCAdmin.showNotice('error', 'Rule name is required');
                isValid = false;
            }
            
            // Validate discount value
            if (isNaN(data.discount_value) || data.discount_value <= 0) {
                $('#discount-value').addClass('error');
                HCCAdmin.showNotice('error', 'Please enter a valid discount value');
                isValid = false;
            }
            
            // Validate percentage range
            if (data.discount_type === 'percentage' && data.discount_value > 100) {
                $('#discount-value').addClass('error');
                HCCAdmin.showNotice('error', 'Percentage cannot exceed 100%');
                isValid = false;
            }
            
            // Validate date range
            if (data.date_start && data.date_end) {
                const startDate = new Date(data.date_start);
                const endDate = new Date(data.date_end);
                
                if (startDate > endDate) {
                    $('#date-end').addClass('error');
                    HCCAdmin.showNotice('error', 'End date must be after start date');
                    isValid = false;
                }
            }
            
            return isValid;
        },

        /**
         * Populate discount form with data
         */
        populateDiscountForm: function(data) {
            $('#discount-id').val(data.id);
            $('#rule-name').val(data.rule_name);
            $('#rule-description').val(data.description);
            $('#discount-type').val(data.discount_type).trigger('change');
            $('#discount-value').val(data.discount_value);
            $('#date-start').val(data.date_start);
            $('#date-end').val(data.date_end);
            $('#priority').val(data.priority);
            $('#stackable').prop('checked', data.stackable);
            $('#discount-code').val(data.discount_code);
            $('#usage-limit').val(data.usage_limit);
            $('#discount-active').prop('checked', data.active);
            
            // Populate conditions
            if (data.conditions) {
                const conditions = JSON.parse(data.conditions);
                
                if (conditions.min_area) {
                    $('#enable-min-area').prop('checked', true).trigger('change');
                    $('#min-area').val(conditions.min_area);
                }
                if (conditions.min_rooms) {
                    $('#enable-min-rooms').prop('checked', true).trigger('change');
                    $('#min-rooms').val(conditions.min_rooms);
                }
                if (conditions.min_subtotal) {
                    $('#enable-min-subtotal').prop('checked', true).trigger('change');
                    $('#min-subtotal').val(conditions.min_subtotal);
                }
            }
            
            // Populate days of week
            if (data.days_of_week) {
                const days = JSON.parse(data.days_of_week);
                $('#enable-days').prop('checked', true).trigger('change');
                
                days.forEach(function(day) {
                    $('.day-checkbox[value="' + day + '"]').prop('checked', true);
                });
            }
        },

        /**
         * Reset discount form
         */
        resetDiscountForm: function() {
            $('#hcc-discount-form')[0].reset();
            $('#discount-id').val('');
            $('.hcc-condition-toggle').prop('checked', false).trigger('change');
            $('.error').removeClass('error');
        },

        /**
         * Close discount modal
         */
        closeModal: function() {
            $('#hcc-discount-modal').fadeOut(300);
        }
    };

    /**
     * Close modal on background click or close button
     */
    $(document).on('click', '.hcc-modal-close, .hcc-modal-backdrop', function() {
        DiscountManager.closeModal();
    });

    /**
     * Prevent modal content click from closing
     */
    $(document).on('click', '.hcc-modal-content', function(e) {
        e.stopPropagation();
    });

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        if ($('#hcc-discount-form').length) {
            DiscountManager.init();
        }
    });

    /**
     * Make DiscountManager globally accessible
     */
    window.HCCDiscountManager = DiscountManager;

})(jQuery);