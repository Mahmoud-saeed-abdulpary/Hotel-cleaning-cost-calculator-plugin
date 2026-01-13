<?php
/**
 * UI Customization functionality
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * UI Customization functionality.
 *
 * Handles all UI customization settings including colors, fonts, layout, etc.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Customizer {

    /**
     * Default color scheme
     *
     * @since    2.0.0
     * @access   private
     * @var      array    $default_colors    Default colors
     */
    private $default_colors = array(
        'primary'    => '#2563eb',
        'secondary'  => '#64748b',
        'accent'     => '#10b981',
        'background' => '#ffffff',
        'text'       => '#1e293b',
        'border'     => '#e2e8f0',
    );

    /**
     * Initialize customizer
     *
     * @since    2.0.0
     */
    public function init() {
        // Customizer functionality is called as needed
    }

    /**
     * Get current color scheme
     *
     * @since    2.0.0
     * @return   array    Color scheme array
     */
    public function get_color_scheme() {
        return array(
            'primary'    => get_option( 'hcc_primary_color', $this->default_colors['primary'] ),
            'secondary'  => get_option( 'hcc_secondary_color', $this->default_colors['secondary'] ),
            'accent'     => get_option( 'hcc_accent_color', $this->default_colors['accent'] ),
            'background' => get_option( 'hcc_background_color', $this->default_colors['background'] ),
            'text'       => get_option( 'hcc_text_color', $this->default_colors['text'] ),
            'border'     => get_option( 'hcc_border_color', $this->default_colors['border'] ),
        );
    }

    /**
     * Update color scheme
     *
     * @since    2.0.0
     * @param    array    $colors    Color values
     * @return   bool                True on success
     */
    public function update_color_scheme( $colors ) {
        
        $valid_keys = array( 'primary', 'secondary', 'accent', 'background', 'text', 'border' );
        
        foreach ( $colors as $key => $color ) {
            if ( in_array( $key, $valid_keys ) && $this->is_valid_color( $color ) ) {
                update_option( 'hcc_' . $key . '_color', sanitize_text_field( $color ) );
            }
        }
        
        // Log activity
        HCC_Database::log_activity( 'settings_updated', 'settings', 0, array(
            'setting' => 'color_scheme',
        ) );
        
        return true;
    }

    /**
     * Validate color hex code
     *
     * @since    2.0.0
     * @param    string    $color    Color hex code
     * @return   bool                True if valid
     */
    private function is_valid_color( $color ) {
        return preg_match( '/^#[a-f0-9]{6}$/i', $color );
    }

    /**
     * Reset color scheme to defaults
     *
     * @since    2.0.0
     * @return   bool    True on success
     */
    public function reset_color_scheme() {
        foreach ( $this->default_colors as $key => $color ) {
            update_option( 'hcc_' . $key . '_color', $color );
        }
        return true;
    }

    /**
     * Get typography settings
     *
     * @since    2.0.0
     * @return   array    Typography settings
     */
    public function get_typography_settings() {
        return array(
            'font_family'        => get_option( 'hcc_font_family', 'system-ui' ),
            'font_size'          => get_option( 'hcc_font_size', '16' ),
            'font_weight'        => get_option( 'hcc_font_weight', '400' ),
            'heading_font_size'  => get_option( 'hcc_heading_font_size', '24' ),
            'line_height'        => get_option( 'hcc_line_height', '1.5' ),
        );
    }

    /**
     * Update typography settings
     *
     * @since    2.0.0
     * @param    array    $settings    Typography settings
     * @return   bool                  True on success
     */
    public function update_typography_settings( $settings ) {
        
        $valid_keys = array( 'font_family', 'font_size', 'font_weight', 'heading_font_size', 'line_height' );
        
        foreach ( $settings as $key => $value ) {
            if ( in_array( $key, $valid_keys ) ) {
                update_option( 'hcc_' . $key, sanitize_text_field( $value ) );
            }
        }
        
        return true;
    }

    /**
     * Get layout settings
     *
     * @since    2.0.0
     * @return   array    Layout settings
     */
    public function get_layout_settings() {
        return array(
            'border_radius'  => get_option( 'hcc_border_radius', '8' ),
            'spacing'        => get_option( 'hcc_spacing', 'standard' ),
            'button_style'   => get_option( 'hcc_button_style', 'solid' ),
            'layout_preset'  => get_option( 'hcc_layout_preset', 'standard' ),
            'card_shadow'    => get_option( 'hcc_card_shadow', 'medium' ),
        );
    }

    /**
     * Update layout settings
     *
     * @since    2.0.0
     * @param    array    $settings    Layout settings
     * @return   bool                  True on success
     */
    public function update_layout_settings( $settings ) {
        
        $valid_keys = array( 'border_radius', 'spacing', 'button_style', 'layout_preset', 'card_shadow' );
        
        foreach ( $settings as $key => $value ) {
            if ( in_array( $key, $valid_keys ) ) {
                update_option( 'hcc_' . $key, sanitize_text_field( $value ) );
            }
        }
        
        return true;
    }

    /**
     * Get predefined theme presets
     *
     * @since    2.0.0
     * @return   array    Array of theme presets
     */
    public function get_theme_presets() {
        return array(
            'default' => array(
                'name'        => __( 'Default (Blue)', 'hotel-cleaning-calculator-pro' ),
                'colors'      => array(
                    'primary'    => '#2563eb',
                    'secondary'  => '#64748b',
                    'accent'     => '#10b981',
                    'background' => '#ffffff',
                    'text'       => '#1e293b',
                    'border'     => '#e2e8f0',
                ),
            ),
            'modern_dark' => array(
                'name'        => __( 'Modern Dark', 'hotel-cleaning-calculator-pro' ),
                'colors'      => array(
                    'primary'    => '#6366f1',
                    'secondary'  => '#8b5cf6',
                    'accent'     => '#ec4899',
                    'background' => '#1e293b',
                    'text'       => '#f1f5f9',
                    'border'     => '#334155',
                ),
            ),
            'professional' => array(
                'name'        => __( 'Professional', 'hotel-cleaning-calculator-pro' ),
                'colors'      => array(
                    'primary'    => '#0ea5e9',
                    'secondary'  => '#06b6d4',
                    'accent'     => '#14b8a6',
                    'background' => '#ffffff',
                    'text'       => '#0f172a',
                    'border'     => '#cbd5e1',
                ),
            ),
            'elegant' => array(
                'name'        => __( 'Elegant', 'hotel-cleaning-calculator-pro' ),
                'colors'      => array(
                    'primary'    => '#7c3aed',
                    'secondary'  => '#a855f7',
                    'accent'     => '#d946ef',
                    'background' => '#faf5ff',
                    'text'       => '#3b0764',
                    'border'     => '#e9d5ff',
                ),
            ),
        );
    }

    /**
     * Apply theme preset
     *
     * @since    2.0.0
     * @param    string    $preset_id    Preset ID
     * @return   bool                    True on success
     */
    public function apply_theme_preset( $preset_id ) {
        
        $presets = $this->get_theme_presets();
        
        if ( ! isset( $presets[ $preset_id ] ) ) {
            return false;
        }
        
        $preset = $presets[ $preset_id ];
        
        // Apply colors
        foreach ( $preset['colors'] as $key => $color ) {
            update_option( 'hcc_' . $key . '_color', $color );
        }
        
        // Store current preset
        update_option( 'hcc_current_preset', $preset_id );
        
        return true;
    }

    /**
     * Get custom CSS
     *
     * @since    2.0.0
     * @return   string    Custom CSS
     */
    public function get_custom_css() {
        return get_option( 'hcc_custom_css', '' );
    }

    /**
     * Update custom CSS
     *
     * @since    2.0.0
     * @param    string    $css    Custom CSS code
     * @return   bool              True on success
     */
    public function update_custom_css( $css ) {
        
        // Basic CSS sanitization (remove script tags, etc.)
        $css = wp_strip_all_tags( $css );
        
        update_option( 'hcc_custom_css', $css );
        
        // Log activity
        HCC_Database::log_activity( 'settings_updated', 'settings', 0, array(
            'setting' => 'custom_css',
        ) );
        
        return true;
    }

    /**
     * Get available Google Fonts
     *
     * @since    2.0.0
     * @return   array    Array of Google Fonts
     */
    public function get_google_fonts() {
        return array(
            'system-ui'       => 'System Default',
            'Roboto'          => 'Roboto',
            'Open Sans'       => 'Open Sans',
            'Lato'            => 'Lato',
            'Montserrat'      => 'Montserrat',
            'Poppins'         => 'Poppins',
            'Inter'           => 'Inter',
            'Raleway'         => 'Raleway',
            'Nunito'          => 'Nunito',
            'Playfair Display' => 'Playfair Display',
            'Merriweather'    => 'Merriweather',
            'Ubuntu'          => 'Ubuntu',
        );
    }

    /**
     * Get all customization settings
     *
     * @since    2.0.0
     * @return   array    All customization settings
     */
    public function get_all_settings() {
        return array(
            'colors'     => $this->get_color_scheme(),
            'typography' => $this->get_typography_settings(),
            'layout'     => $this->get_layout_settings(),
            'custom_css' => $this->get_custom_css(),
        );
    }

    /**
     * Export customization settings
     *
     * @since    2.0.0
     * @return   string    JSON string of settings
     */
    public function export_settings() {
        $settings = $this->get_all_settings();
        return json_encode( $settings, JSON_PRETTY_PRINT );
    }

    /**
     * Import customization settings
     *
     * @since    2.0.0
     * @param    string    $json    JSON string
     * @return   array              Result array
     */
    public function import_settings( $json ) {
        
        $settings = json_decode( $json, true );
        
        if ( empty( $settings ) || ! is_array( $settings ) ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid JSON data', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        // Import colors
        if ( isset( $settings['colors'] ) ) {
            $this->update_color_scheme( $settings['colors'] );
        }
        
        // Import typography
        if ( isset( $settings['typography'] ) ) {
            $this->update_typography_settings( $settings['typography'] );
        }
        
        // Import layout
        if ( isset( $settings['layout'] ) ) {
            $this->update_layout_settings( $settings['layout'] );
        }
        
        // Import custom CSS
        if ( isset( $settings['custom_css'] ) ) {
            $this->update_custom_css( $settings['custom_css'] );
        }
        
        return array(
            'success' => true,
            'message' => __( 'Settings imported successfully', 'hotel-cleaning-calculator-pro' ),
        );
    }

    /**
     * Generate CSS variables for frontend
     *
     * @since    2.0.0
     * @return   string    CSS code
     */
    public function generate_css_variables() {
        
        $colors = $this->get_color_scheme();
        $typography = $this->get_typography_settings();
        $layout = $this->get_layout_settings();
        
        $css = ":root {\n";
        
        // Colors
        foreach ( $colors as $key => $value ) {
            $css .= "    --hcc-{$key}-color: {$value};\n";
        }
        
        // Typography
        $css .= "    --hcc-font-family: {$typography['font_family']};\n";
        $css .= "    --hcc-font-size: {$typography['font_size']}px;\n";
        $css .= "    --hcc-font-weight: {$typography['font_weight']};\n";
        $css .= "    --hcc-heading-font-size: {$typography['heading_font_size']}px;\n";
        $css .= "    --hcc-line-height: {$typography['line_height']};\n";
        
        // Layout
        $css .= "    --hcc-border-radius: {$layout['border_radius']}px;\n";
        
        $css .= "}\n";
        
        return $css;
    }

    /**
     * Get spacing values based on preset
     *
     * @since    2.0.0
     * @param    string    $preset    Spacing preset (compact, standard, spacious)
     * @return   array                Spacing values
     */
    public function get_spacing_values( $preset = 'standard' ) {
        
        $spacing = array(
            'compact' => array(
                'xs' => '4px',
                'sm' => '8px',
                'md' => '12px',
                'lg' => '16px',
                'xl' => '20px',
            ),
            'standard' => array(
                'xs' => '8px',
                'sm' => '12px',
                'md' => '16px',
                'lg' => '24px',
                'xl' => '32px',
            ),
            'spacious' => array(
                'xs' => '12px',
                'sm' => '16px',
                'md' => '24px',
                'lg' => '32px',
                'xl' => '48px',
            ),
        );
        
        return isset( $spacing[ $preset ] ) ? $spacing[ $preset ] : $spacing['standard'];
    }

    /**
     * Reset all customization to defaults
     *
     * @since    2.0.0
     * @return   bool    True on success
     */
    public function reset_to_defaults() {
        
        // Reset colors
        $this->reset_color_scheme();
        
        // Reset typography
        delete_option( 'hcc_font_family' );
        delete_option( 'hcc_font_size' );
        delete_option( 'hcc_font_weight' );
        delete_option( 'hcc_heading_font_size' );
        delete_option( 'hcc_line_height' );
        
        // Reset layout
        delete_option( 'hcc_border_radius' );
        delete_option( 'hcc_spacing' );
        delete_option( 'hcc_button_style' );
        delete_option( 'hcc_layout_preset' );
        delete_option( 'hcc_card_shadow' );
        
        // Clear custom CSS
        delete_option( 'hcc_custom_css' );
        
        // Clear preset
        delete_option( 'hcc_current_preset' );
        
        return true;
    }
}