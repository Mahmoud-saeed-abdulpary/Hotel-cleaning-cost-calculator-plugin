/**
 * UI Customizer JavaScript
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    const Customizer = {

        /**
         * Current settings
         */
        settings: {},

        /**
         * Initialize customizer
         */
        init: function() {
            this.loadCurrentSettings();
            this.handleSectionToggle();
            this.handleColorChanges();
            this.handleFontChanges();
            this.handleLayoutChanges();
            this.handleThemePresets();
            this.handleCustomCSS();
            this.handleSave();
            this.handleReset();
            this.handleDevicePreview();
            this.initPreview();
        },

        /**
         * Load current settings
         */
        loadCurrentSettings: function() {
            // Get settings from data attributes or defaults
            this.settings = {
                primary_color: $('#primary-color').val() || '#2563eb',
                secondary_color: $('#secondary-color').val() || '#64748b',
                accent_color: $('#accent-color').val() || '#10b981',
                background_color: $('#background-color').val() || '#ffffff',
                text_color: $('#text-color').val() || '#1e293b',
                border_color: $('#border-color').val() || '#e2e8f0',
                font_family: $('#font-family').val() || 'system-ui',
                font_size: $('#font-size').val() || '16',
                border_radius: $('#border-radius').val() || '8',
                spacing: $('#spacing').val() || 'standard'
            };
        },

        /**
         * Handle section toggle
         */
        handleSectionToggle: function() {
            $(document).on('click', '.hcc-control-section-header', function() {
                const $section = $(this).closest('.hcc-control-section');
                $section.toggleClass('expanded');
                
                if ($section.hasClass('expanded')) {
                    $section.find('.hcc-control-section-body').slideDown(300);
                } else {
                    $section.find('.hcc-control-section-body').slideUp(300);
                }
            });
            
            // Expand first section by default
            $('.hcc-control-section').first().addClass('expanded').find('.hcc-control-section-body').show();
        },

        /**
         * Handle color changes
         */
        handleColorChanges: function() {
            // Initialize color pickers
            if (typeof $.fn.wpColorPicker !== 'undefined') {
                $('.hcc-color-picker').wpColorPicker({
                    change: function(event, ui) {
                        const color = ui.color.toString();
                        const $input = $(event.target);
                        const colorName = $input.attr('id').replace('-color', '_color');
                        
                        Customizer.settings[colorName] = color;
                        Customizer.updatePreview();
                    }
                });
            } else {
                // Fallback for manual color input
                $('.hcc-color-picker').on('change', function() {
                    const $input = $(this);
                    const colorName = $input.attr('id').replace('-color', '_color');
                    Customizer.settings[colorName] = $input.val();
                    Customizer.updatePreview();
                });
            }
        },

        /**
         * Handle font changes
         */
        handleFontChanges: function() {
            // Font family change
            $(document).on('change', '#font-family', function() {
                Customizer.settings.font_family = $(this).val();
                Customizer.updatePreview();
            });
            
            // Font size slider
            $(document).on('input', '#font-size', function() {
                const size = $(this).val();
                $('#font-size-value').text(size + 'px');
                Customizer.settings.font_size = size;
                Customizer.updatePreview();
            });
        },

        /**
         * Handle layout changes
         */
        handleLayoutChanges: function() {
            // Border radius slider
            $(document).on('input', '#border-radius', function() {
                const radius = $(this).val();
                $('#border-radius-value').text(radius + 'px');
                Customizer.settings.border_radius = radius;
                Customizer.updatePreview();
            });
            
            // Spacing options
            $(document).on('click', '.hcc-spacing-option', function() {
                $('.hcc-spacing-option').removeClass('active');
                $(this).addClass('active');
                Customizer.settings.spacing = $(this).data('value');
                Customizer.updatePreview();
            });
            
            // Button style
            $(document).on('change', '#button-style', function() {
                Customizer.settings.button_style = $(this).val();
                Customizer.updatePreview();
            });
        },

        /**
         * Handle theme presets
         */
        handleThemePresets: function() {
            $(document).on('click', '.hcc-theme-preset-card', function() {
                const presetId = $(this).data('preset');
                
                $('.hcc-theme-preset-card').removeClass('active');
                $(this).addClass('active');
                
                // Apply preset via AJAX
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_apply_theme_preset',
                        nonce: hccAdmin.nonce,
                        preset_id: presetId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload page to apply preset
                            location.reload();
                        }
                    }
                });
            });
        },

        /**
         * Handle custom CSS
         */
        handleCustomCSS: function() {
            const $cssEditor = $('#custom-css');
            
            if ($cssEditor.length) {
                $cssEditor.on('input', HCCAdmin.debounce(function() {
                    Customizer.updatePreview();
                }, 500));
            }
        },

        /**
         * Update live preview
         */
        updatePreview: function() {
            const $preview = $('.hcc-preview-content');
            
            // Apply CSS variables
            $preview.css({
                '--hcc-primary-color': this.settings.primary_color,
                '--hcc-secondary-color': this.settings.secondary_color,
                '--hcc-accent-color': this.settings.accent_color,
                '--hcc-background-color': this.settings.background_color,
                '--hcc-text-color': this.settings.text_color,
                '--hcc-border-color': this.settings.border_color,
                '--hcc-font-family': this.settings.font_family,
                '--hcc-font-size': this.settings.font_size + 'px',
                '--hcc-border-radius': this.settings.border_radius + 'px'
            });
            
            // Apply font family
            $preview.css('font-family', this.settings.font_family);
            
            // Apply custom CSS
            const customCSS = $('#custom-css').val();
            if (customCSS) {
                $('#preview-custom-css').remove();
                $('<style id="preview-custom-css">' + customCSS + '</style>').appendTo($preview);
            }
            
            // Show save indicator
            Customizer.showSaveIndicator('unsaved');
        },

        /**
         * Handle save
         */
        handleSave: function() {
            $(document).on('click', '#save-customization', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const originalText = $button.html();
                
                // Collect all settings
                const settings = {
                    primary_color: $('#primary-color').val(),
                    secondary_color: $('#secondary-color').val(),
                    accent_color: $('#accent-color').val(),
                    background_color: $('#background-color').val(),
                    text_color: $('#text-color').val(),
                    border_color: $('#border-color').val(),
                    font_family: $('#font-family').val(),
                    font_size: $('#font-size').val(),
                    font_weight: $('#font-weight').val(),
                    heading_font_size: $('#heading-font-size').val(),
                    border_radius: $('#border-radius').val(),
                    spacing: $('.hcc-spacing-option.active').data('value'),
                    button_style: $('#button-style').val(),
                    custom_css: $('#custom-css').val()
                };
                
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_save_customization',
                        nonce: hccAdmin.nonce,
                        settings: JSON.stringify(settings)
                    },
                    beforeSend: function() {
                        $button.prop('disabled', true).html('<span class="hcc-loading"></span> Saving...');
                        Customizer.showSaveIndicator('saving');
                    },
                    success: function(response) {
                        if (response.success) {
                            Customizer.showSaveIndicator('saved');
                            HCCAdmin.showNotice('success', response.data.message);
                            
                            setTimeout(function() {
                                Customizer.showSaveIndicator('');
                            }, 3000);
                        } else {
                            Customizer.showSaveIndicator('error');
                            HCCAdmin.showNotice('error', response.data.message);
                        }
                    },
                    error: function() {
                        Customizer.showSaveIndicator('error');
                        HCCAdmin.showNotice('error', 'Failed to save customization');
                    },
                    complete: function() {
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            });
        },

        /**
         * Handle reset
         */
        handleReset: function() {
            $(document).on('click', '#reset-customization', function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to reset all customization to defaults? This cannot be undone.')) {
                    return;
                }
                
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_reset_customization',
                        nonce: hccAdmin.nonce
                    },
                    beforeSend: function() {
                        HCCAdmin.showLoading();
                    },
                    success: function(response) {
                        if (response.success) {
                            HCCAdmin.showNotice('success', 'Customization reset to defaults');
                            location.reload();
                        } else {
                            HCCAdmin.showNotice('error', 'Failed to reset customization');
                        }
                    },
                    complete: function() {
                        HCCAdmin.hideLoading();
                    }
                });
            });
        },

        /**
         * Handle device preview
         */
        handleDevicePreview: function() {
            $(document).on('click', '.hcc-device-btn', function() {
                $('.hcc-device-btn').removeClass('active');
                $(this).addClass('active');
                
                const device = $(this).data('device');
                const $preview = $('.hcc-preview-content');
                
                $preview.removeClass('mobile-view tablet-view desktop-view');
                
                if (device !== 'desktop') {
                    $preview.addClass(device + '-view');
                }
            });
        },

        /**
         * Initialize preview
         */
        initPreview: function() {
            // Load preview calculator HTML
            const previewHTML = `
                <div class="hcc-calculator-preview">
                    <h2>Cleaning Cost Calculator</h2>
                    <div class="hcc-room-card" style="border: 1px solid var(--hcc-border-color); border-radius: var(--hcc-border-radius); padding: 20px; margin-bottom: 15px;">
                        <div class="hcc-form-group">
                            <label>Room Type</label>
                            <select style="width: 100%; padding: 10px; border: 1px solid var(--hcc-border-color); border-radius: var(--hcc-border-radius);">
                                <option>Bedroom</option>
                                <option>Bathroom</option>
                                <option>Kitchen</option>
                            </select>
                        </div>
                        <div class="hcc-form-group">
                            <label>Area (m²)</label>
                            <input type="number" value="25" style="width: 100%; padding: 10px; border: 1px solid var(--hcc-border-color); border-radius: var(--hcc-border-radius);">
                        </div>
                    </div>
                    <button class="hcc-btn-primary" style="background: var(--hcc-primary-color); color: white; padding: 12px 24px; border: none; border-radius: var(--hcc-border-radius); font-weight: 500; cursor: pointer;">Calculate Price</button>
                    <div style="margin-top: 20px; padding: 20px; background: var(--hcc-background-color); border: 1px solid var(--hcc-border-color); border-radius: var(--hcc-border-radius);">
                        <p style="margin: 0 0 10px 0; color: var(--hcc-text-color);">Subtotal: <strong>$125.00</strong></p>
                        <p style="margin: 0; font-size: 20px; color: var(--hcc-primary-color); font-weight: 700;">Total: $125.00</p>
                    </div>
                </div>
            `;
            
            $('.hcc-preview-content').html(previewHTML);
        },

        /**
         * Show save indicator
         */
        showSaveIndicator: function(status) {
            const $indicator = $('.hcc-save-indicator');
            
            $indicator.removeClass('saving error');
            
            switch(status) {
                case 'saving':
                    $indicator.addClass('saving').html('<span class="dashicons dashicons-update"></span> Saving...');
                    break;
                case 'saved':
                    $indicator.html('<span class="dashicons dashicons-yes"></span> Saved');
                    break;
                case 'error':
                    $indicator.addClass('error').html('<span class="dashicons dashicons-warning"></span> Error');
                    break;
                case 'unsaved':
                    $indicator.html('<span class="dashicons dashicons-edit"></span> Unsaved changes');
                    break;
                default:
                    $indicator.html('');
            }
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        if ($('.hcc-customizer-container').length) {
            Customizer.init();
        }
    });

    /**
     * Make Customizer globally accessible
     */
    window.HCCCustomizer = Customizer;

})(jQuery);