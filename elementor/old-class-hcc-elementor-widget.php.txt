<?php
/**
 * Elementor Widget for Hotel Cleaning Calculator PRO
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/elementor
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Hotel Cleaning Calculator Widget
 *
 * @since 2.0.0
 */
class HCC_Elementor_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name
     *
     * @since 2.0.0
     * @return string Widget name
     */
    public function get_name() {
        return 'hotel_cleaning_calculator';
    }

    /**
     * Get widget title
     *
     * @since 2.0.0
     * @return string Widget title
     */
    public function get_title() {
        return __( 'Hotel Cleaning Calculator', 'hotel-cleaning-calculator-pro' );
    }

    /**
     * Get widget icon
     *
     * @since 2.0.0
     * @return string Widget icon
     */
    public function get_icon() {
        return 'eicon-calculator';
    }

    /**
     * Get widget categories
     *
     * @since 2.0.0
     * @return array Widget categories
     */
    public function get_categories() {
        return [ 'general' ];
    }

    /**
     * Get widget keywords
     *
     * @since 2.0.0
     * @return array Widget keywords
     */
    public function get_keywords() {
        return [ 'calculator', 'hotel', 'cleaning', 'price', 'quote', 'cost' ];
    }

    /**
     * Register widget controls
     *
     * @since 2.0.0
     */
    protected function register_controls() {
        
        // Include controls configuration
        require_once HCC_PLUGIN_DIR . 'elementor/elementor-controls.php';
        HCC_Elementor_Controls::register_controls( $this );
    }

    /**
     * Render widget output on the frontend
     *
     * @since 2.0.0
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Prepare shortcode attributes
        $atts = array(
            'theme'          => $settings['theme'] ?? 'default',
            'show_logo'      => $settings['show_logo'] ?? 'yes',
            'show_title'     => $settings['show_title'] ?? 'yes',
            'custom_class'   => $settings['custom_class'] ?? '',
        );
        
        // Add Elementor-specific class
        $atts['custom_class'] .= ' elementor-widget-calculator';
        
        // Render calculator
        $public = new HCC_Public();
        echo $public->render_calculator( $atts );
        
        // Add custom CSS if specified
        if ( ! empty( $settings['custom_css'] ) ) {
            echo '<style>' . esc_html( $settings['custom_css'] ) . '</style>';
        }
    }

    /**
     * Render widget output in the editor
     *
     * @since 2.0.0
     */
    protected function content_template() {
        ?>
        <#
        var themeClass = settings.theme !== 'default' ? 'hcc-theme-' + settings.theme : '';
        var customClass = settings.custom_class || '';
        #>
        
        <div class="hcc-calculator-wrapper elementor-widget-calculator {{ themeClass }} {{ customClass }}">
            <div class="hcc-calculator-header" style="text-align: center; padding: 20px; background: #f3f4f6; border-radius: 8px;">
                
                <# if ( settings.show_logo === 'yes' ) { #>
                    <div style="margin-bottom: 15px;">
                        <span class="dashicons dashicons-format-image" style="font-size: 48px; color: #d1d5db;"></span>
                    </div>
                <# } #>
                
                <# if ( settings.show_title === 'yes' ) { #>
                    <h2 style="margin: 0; color: #111827;">
                        <?php _e( 'Calculate Cleaning Cost', 'hotel-cleaning-calculator-pro' ); ?>
                    </h2>
                <# } #>
                
            </div>
            
            <div style="margin-top: 20px; padding: 40px; background: #fff; border: 2px dashed #e5e7eb; border-radius: 8px; text-align: center;">
                <span class="dashicons dashicons-calculator" style="font-size: 64px; color: #d1d5db; margin-bottom: 20px;"></span>
                <h3 style="margin: 0 0 10px 0; color: #6b7280;">
                    <?php _e( 'Calculator Preview', 'hotel-cleaning-calculator-pro' ); ?>
                </h3>
                <p style="margin: 0; color: #9ca3af; font-size: 14px;">
                    <?php _e( 'The calculator will be displayed here on the frontend', 'hotel-cleaning-calculator-pro' ); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Get style depends
     *
     * @since 2.0.0
     * @return array Style dependencies
     */
    public function get_style_depends() {
        return [ 'hcc-public-styles', 'hcc-responsive-styles' ];
    }

    /**
     * Get script depends
     *
     * @since 2.0.0
     * @return array Script dependencies
     */
    public function get_script_depends() {
        return [ 'hcc-public-scripts', 'hcc-calculator', 'hcc-room-repeater' ];
    }
}