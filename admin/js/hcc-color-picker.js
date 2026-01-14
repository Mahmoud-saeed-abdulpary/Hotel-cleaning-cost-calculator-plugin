/**
 * Color Picker Component JavaScript
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    const ColorPicker = {

        /**
         * Initialize color picker
         */
        init: function() {
            this.setupColorInputs();
            this.handleColorChange();
            this.handlePresetColors();
        },

        /**
         * Setup color inputs with preview
         */
        setupColorInputs: function() {
            $('.hcc-color-control').each(function() {
                const $control = $(this);
                const $input = $control.find('input[type="text"]');
                const $preview = $control.find('.hcc-color-preview');
                
                // Set initial preview color
                if ($input.val()) {
                    $preview.css('background-color', $input.val());
                }
                
                // Handle preview click
                $preview.on('click', function() {
                    if (typeof $.fn.wpColorPicker !== 'undefined') {
                        $input.wpColorPicker('open');
                    } else {
                        $input.focus();
                    }
                });
            });
        },

        /**
         * Handle color change
         */
        handleColorChange: function() {
            $(document).on('input change', '.hcc-color-input', function() {
                const $input = $(this);
                const color = $input.val();
                const $preview = $input.closest('.hcc-color-picker-wrapper').find('.hcc-color-preview');
                
                if (ColorPicker.isValidHex(color)) {
                    $preview.css('background-color', color);
                    $input.removeClass('error');
                } else {
                    $input.addClass('error');
                }
            });
        },

        /**
         * Handle preset colors
         */
        handlePresetColors: function() {
            const presetColors = [
                '#2563eb', '#1d4ed8', '#1e40af', // Blues
                '#10b981', '#059669', '#047857', // Greens
                '#f59e0b', '#d97706', '#b45309', // Oranges
                '#ef4444', '#dc2626', '#b91c1c', // Reds
                '#8b5cf6', '#7c3aed', '#6d28d9', // Purples
                '#64748b', '#475569', '#334155', // Grays
                '#000000', '#ffffff', '#f3f4f6'  // Black, White, Light gray
            ];
            
            // Add preset color palette to color pickers
            $('.hcc-color-control').each(function() {
                const $control = $(this);
                
                if ($control.find('.hcc-color-presets').length === 0) {
                    const $presets = $('<div class="hcc-color-presets"></div>');
                    
                    presetColors.forEach(function(color) {
                        const $preset = $('<div class="hcc-color-preset" data-color="' + color + '" style="background-color: ' + color + '"></div>');
                        $presets.append($preset);
                    });
                    
                    $control.append($presets);
                }
            });
            
            // Handle preset click
            $(document).on('click', '.hcc-color-preset', function() {
                const color = $(this).data('color');
                const $input = $(this).closest('.hcc-color-control').find('.hcc-color-input');
                
                $input.val(color).trigger('change');
                
                // If WordPress color picker is active, update it
                if (typeof $.fn.wpColorPicker !== 'undefined') {
                    $input.wpColorPicker('color', color);
                }
            });
        },

        /**
         * Validate hex color
         */
        isValidHex: function(hex) {
            return /^#[0-9A-F]{6}$/i.test(hex);
        },

        /**
         * Convert hex to RGB
         */
        hexToRgb: function(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        },

        /**
         * Convert RGB to hex
         */
        rgbToHex: function(r, g, b) {
            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        },

        /**
         * Get contrast color (black or white)
         */
        getContrastColor: function(hex) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return '#000000';
            
            // Calculate luminance
            const luminance = (0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b) / 255;
            
            return luminance > 0.5 ? '#000000' : '#ffffff';
        },

        /**
         * Lighten color
         */
        lightenColor: function(hex, percent) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return hex;
            
            const factor = 1 + (percent / 100);
            
            const r = Math.min(255, Math.round(rgb.r * factor));
            const g = Math.min(255, Math.round(rgb.g * factor));
            const b = Math.min(255, Math.round(rgb.b * factor));
            
            return this.rgbToHex(r, g, b);
        },

        /**
         * Darken color
         */
        darkenColor: function(hex, percent) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return hex;
            
            const factor = 1 - (percent / 100);
            
            const r = Math.max(0, Math.round(rgb.r * factor));
            const g = Math.max(0, Math.round(rgb.g * factor));
            const b = Math.max(0, Math.round(rgb.b * factor));
            
            return this.rgbToHex(r, g, b);
        }
    };

    /**
     * Add CSS for color presets
     */
    const presetsCSS = `
        <style>
            .hcc-color-presets {
                display: grid;
                grid-template-columns: repeat(9, 1fr);
                gap: 5px;
                margin-top: 10px;
                padding: 10px;
                background: #f9fafb;
                border-radius: 6px;
            }
            
            .hcc-color-preset {
                width: 30px;
                height: 30px;
                border-radius: 4px;
                cursor: pointer;
                border: 2px solid transparent;
                transition: all 0.3s ease;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }
            
            .hcc-color-preset:hover {
                transform: scale(1.1);
                border-color: #2563eb;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            }
            
            .hcc-color-input.error {
                border-color: #ef4444;
                background: #fee2e2;
            }
        </style>
    `;
    
    $('head').append(presetsCSS);

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        ColorPicker.init();
    });

    /**
     * Make ColorPicker globally accessible
     */
    window.HCCColorPicker = ColorPicker;

})(jQuery);