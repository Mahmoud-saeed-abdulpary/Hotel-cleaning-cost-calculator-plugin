/**
 * Admin JavaScript for Hotel Cleaning Calculator PRO
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    /**
     * Main Admin Object
     */
    const HCCAdmin = {

        /**
         * Initialize admin functionality
         */
        init: function() {
            this.handleTabNavigation();
            this.handleNotices();
            this.handleConfirmDialogs();
            this.handleToggles();
            this.handleFormValidation();
            this.initTooltips();
            this.handleAjaxForms();
        },

        /**
         * Handle tab navigation
         */
        handleTabNavigation: function() {
            $('.hcc-nav-tabs a').on('click', function(e) {
                e.preventDefault();
                
                const targetId = $(this).attr('href');
                
                // Update active tab
                $('.hcc-nav-tabs a').removeClass('active');
                $(this).addClass('active');
                
                // Show target content
                $('.hcc-tab-content').hide();
                $(targetId).fadeIn(300);
                
                // Update URL hash
                window.location.hash = targetId;
            });
            
            // Handle initial hash
            if (window.location.hash) {
                $('.hcc-nav-tabs a[href="' + window.location.hash + '"]').trigger('click');
            }
        },

        /**
         * Handle admin notices
         */
        handleNotices: function() {
            // Auto-dismiss success notices after 5 seconds
            setTimeout(function() {
                $('.hcc-notice.success').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Dismissible notices
            $(document).on('click', '.hcc-notice .notice-dismiss', function() {
                $(this).closest('.hcc-notice').fadeOut(300, function() {
                    $(this).remove();
                });
            });
        },

        /**
         * Handle confirmation dialogs
         */
        handleConfirmDialogs: function() {
            $(document).on('click', '[data-confirm]', function(e) {
                const message = $(this).data('confirm') || hccAdmin.strings.confirmDelete;
                
                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            });
        },

        /**
         * Handle toggle switches
         */
        handleToggles: function() {
            $(document).on('change', '.hcc-toggle input', function() {
                const $toggle = $(this);
                const isChecked = $toggle.is(':checked');
                const action = $toggle.data('action');
                
                if (action) {
                    HCCAdmin.handleToggleAction($toggle, isChecked, action);
                }
            });
        },

        /**
         * Handle toggle action via AJAX
         */
        handleToggleAction: function($toggle, isChecked, action) {
            const data = {
                action: action,
                nonce: hccAdmin.nonce,
                value: isChecked ? 1 : 0,
                id: $toggle.data('id')
            };
            
            $.ajax({
                url: hccAdmin.ajaxUrl,
                type: 'POST',
                data: data,
                beforeSend: function() {
                    $toggle.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        HCCAdmin.showNotice('success', response.data.message || hccAdmin.strings.saved);
                    } else {
                        HCCAdmin.showNotice('error', response.data.message || hccAdmin.strings.error);
                        // Revert toggle
                        $toggle.prop('checked', !isChecked);
                    }
                },
                error: function() {
                    HCCAdmin.showNotice('error', hccAdmin.strings.error);
                    // Revert toggle
                    $toggle.prop('checked', !isChecked);
                },
                complete: function() {
                    $toggle.prop('disabled', false);
                }
            });
        },

        /**
         * Handle form validation
         */
        handleFormValidation: function() {
            $('form[data-validate]').on('submit', function(e) {
                const $form = $(this);
                let isValid = true;
                
                // Clear previous errors
                $form.find('.error-message').remove();
                $form.find('.error').removeClass('error');
                
                // Validate required fields
                $form.find('[required]').each(function() {
                    const $field = $(this);
                    const value = $field.val().trim();
                    
                    if (value === '') {
                        isValid = false;
                        $field.addClass('error');
                        $field.after('<span class="error-message" style="color: #ef4444; font-size: 13px; margin-top: 5px; display: block;">This field is required</span>');
                    }
                });
                
                // Validate email fields
                $form.find('input[type="email"]').each(function() {
                    const $field = $(this);
                    const value = $field.val().trim();
                    
                    if (value !== '' && !HCCAdmin.isValidEmail(value)) {
                        isValid = false;
                        $field.addClass('error');
                        $field.after('<span class="error-message" style="color: #ef4444; font-size: 13px; margin-top: 5px; display: block;">Please enter a valid email address</span>');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    HCCAdmin.showNotice('error', 'Please fix the errors and try again.');
                    
                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $form.find('.error').first().offset().top - 100
                    }, 500);
                }
            });
        },

        /**
         * Validate email address
         */
        isValidEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                const $el = $(this);
                const text = $el.data('tooltip');
                
                $el.on('mouseenter', function() {
                    const $tooltip = $('<div class="hcc-tooltip">' + text + '</div>');
                    $('body').append($tooltip);
                    
                    const offset = $el.offset();
                    $tooltip.css({
                        top: offset.top - $tooltip.outerHeight() - 10,
                        left: offset.left + ($el.outerWidth() / 2) - ($tooltip.outerWidth() / 2)
                    });
                    
                    $tooltip.fadeIn(200);
                });
                
                $el.on('mouseleave', function() {
                    $('.hcc-tooltip').fadeOut(200, function() {
                        $(this).remove();
                    });
                });
            });
        },

        /**
         * Handle AJAX forms
         */
        handleAjaxForms: function() {
            $(document).on('submit', 'form[data-ajax]', function(e) {
                e.preventDefault();
                
                const $form = $(this);
                const $submitBtn = $form.find('[type="submit"]');
                const originalText = $submitBtn.html();
                const formData = new FormData(this);
                
                // Add nonce
                formData.append('nonce', hccAdmin.nonce);
                
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $submitBtn.prop('disabled', true).html('<span class="hcc-loading"></span> Saving...');
                    },
                    success: function(response) {
                        if (response.success) {
                            HCCAdmin.showNotice('success', response.data.message || hccAdmin.strings.saved);
                            
                            // Trigger custom event
                            $form.trigger('hcc-form-success', [response.data]);
                        } else {
                            HCCAdmin.showNotice('error', response.data.message || hccAdmin.strings.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        HCCAdmin.showNotice('error', hccAdmin.strings.error);
                        console.error('AJAX Error:', error);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        },

        /**
         * Show admin notice
         */
        showNotice: function(type, message) {
            const $notice = $('<div class="hcc-notice ' + type + '">' + message + '</div>');
            
            // Remove existing notices of same type
            $('.hcc-notice.' + type).remove();
            
            // Add to page
            if ($('.hcc-admin-content').length) {
                $('.hcc-admin-content').prepend($notice);
            } else {
                $('.wrap').prepend($notice);
            }
            
            // Scroll to notice
            $('html, body').animate({
                scrollTop: $notice.offset().top - 100
            }, 300);
            
            // Auto-dismiss success notices
            if (type === 'success') {
                setTimeout(function() {
                    $notice.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        },

        /**
         * Show loading overlay
         */
        showLoading: function() {
            if ($('.hcc-loading-overlay').length === 0) {
                $('body').append('<div class="hcc-loading-overlay"><div class="hcc-loading" style="width: 50px; height: 50px; border-width: 5px;"></div></div>');
            }
        },

        /**
         * Hide loading overlay
         */
        hideLoading: function() {
            $('.hcc-loading-overlay').fadeOut(300, function() {
                $(this).remove();
            });
        },

        /**
         * Format number as currency
         */
        formatCurrency: function(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
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
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        HCCAdmin.init();
    });

    /**
     * Make HCCAdmin globally accessible
     */
    window.HCCAdmin = HCCAdmin;

})(jQuery);