<?php
/**
 * Public-facing functionality
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Public-facing functionality.
 *
 * Defines frontend assets, shortcodes, and public-facing functionality.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Public {

    /**
     * Initialize the public functionality
     *
     * @since    2.0.0
     */
    public function init() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
        add_action( 'wp_head', array( $this, 'add_custom_styles' ) );
    }

    /**
     * Enqueue public assets
     *
     * @since    2.0.0
     */
    public function enqueue_public_assets() {
        
        // Only enqueue on pages with calculator shortcode
        global $post;
        if ( ! is_a( $post, 'WP_Post' ) ) {
            return;
        }
        
        if ( ! has_shortcode( $post->post_content, 'hotel_cleaning_calculator' ) && 
             ! has_shortcode( $post->post_content, 'hcc_quote_form' ) ) {
            return;
        }
        
        // Main CSS
        wp_enqueue_style(
            'hcc-public-styles',
            HCC_PLUGIN_URL . 'public/css/hcc-public.css',
            array(),
            HCC_VERSION,
            'all'
        );
        
        // Responsive CSS
        wp_enqueue_style(
            'hcc-responsive-styles',
            HCC_PLUGIN_URL . 'public/css/hcc-responsive.css',
            array( 'hcc-public-styles' ),
            HCC_VERSION,
            'all'
        );
        
        // Theme CSS (if theme preset is selected)
        $theme = get_option( 'hcc_theme_preset', 'default' );
        if ( $theme !== 'default' ) {
            wp_enqueue_style(
                'hcc-theme-styles',
                HCC_PLUGIN_URL . 'public/css/hcc-themes.css',
                array( 'hcc-public-styles' ),
                HCC_VERSION,
                'all'
            );
        }
        
        // Main JS
        wp_enqueue_script(
            'hcc-public-scripts',
            HCC_PLUGIN_URL . 'public/js/hcc-public.js',
            array( 'jquery' ),
            HCC_VERSION,
            true
        );
        
        // Calculator JS
        wp_enqueue_script(
            'hcc-calculator',
            HCC_PLUGIN_URL . 'public/js/hcc-calculator.js',
            array( 'jquery', 'hcc-public-scripts' ),
            HCC_VERSION,
            true
        );
        
        // Room repeater JS
        wp_enqueue_script(
            'hcc-room-repeater',
            HCC_PLUGIN_URL . 'public/js/hcc-room-repeater.js',
            array( 'jquery' ),
            HCC_VERSION,
            true
        );
        
        // Quote form JS (only if quote form shortcode present)
        if ( has_shortcode( $post->post_content, 'hcc_quote_form' ) ) {
            wp_enqueue_script(
                'hcc-quote-form',
                HCC_PLUGIN_URL . 'public/js/hcc-quote-form.js',
                array( 'jquery' ),
                HCC_VERSION,
                true
            );
        }
        
        // Mobile gestures JS (for touch devices)
        if ( wp_is_mobile() ) {
            wp_enqueue_script(
                'hcc-mobile-gestures',
                HCC_PLUGIN_URL . 'public/js/hcc-mobile-gestures.js',
                array( 'jquery' ),
                HCC_VERSION,
                true
            );
        }
        
        // Localize script
        $calculator = new HCC_Calculator();
        $room_types = $calculator->get_active_room_types();
        
        wp_localize_script(
            'hcc-calculator',
            'hccData',
            array(
                'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
                'nonce'         => wp_create_nonce( 'hcc_public_nonce' ),
                'roomTypes'     => $room_types,
                'currency'      => $calculator->get_currency_symbol(),
                'currencyPos'   => get_option( 'hcc_currency_position', 'before' ),
                'decimalPlaces' => intval( get_option( 'hcc_decimal_places', 2 ) ),
                'strings'       => $this->get_translated_strings(),
            )
        );
    }

    /**
     * Add custom styles to head
     *
     * @since    2.0.0
     */
    public function add_custom_styles() {
        
        // Only add on pages with calculator
        global $post;
        if ( ! is_a( $post, 'WP_Post' ) ) {
            return;
        }
        
        if ( ! has_shortcode( $post->post_content, 'hotel_cleaning_calculator' ) && 
             ! has_shortcode( $post->post_content, 'hcc_quote_form' ) ) {
            return;
        }
        
        // Get customization options
        $primary_color = get_option( 'hcc_primary_color', '#2563eb' );
        $secondary_color = get_option( 'hcc_secondary_color', '#64748b' );
        $accent_color = get_option( 'hcc_accent_color', '#10b981' );
        $background_color = get_option( 'hcc_background_color', '#ffffff' );
        $text_color = get_option( 'hcc_text_color', '#1e293b' );
        $border_color = get_option( 'hcc_border_color', '#e2e8f0' );
        
        $font_family = get_option( 'hcc_font_family', 'system-ui' );
        $font_size = get_option( 'hcc_font_size', '16' );
        $border_radius = get_option( 'hcc_border_radius', '8' );
        
        $custom_css = get_option( 'hcc_custom_css', '' );
        
        ?>
        <style type="text/css" id="hcc-custom-styles">
            :root {
                --hcc-primary-color: <?php echo esc_attr( $primary_color ); ?>;
                --hcc-secondary-color: <?php echo esc_attr( $secondary_color ); ?>;
                --hcc-accent-color: <?php echo esc_attr( $accent_color ); ?>;
                --hcc-background-color: <?php echo esc_attr( $background_color ); ?>;
                --hcc-text-color: <?php echo esc_attr( $text_color ); ?>;
                --hcc-border-color: <?php echo esc_attr( $border_color ); ?>;
                --hcc-font-family: <?php echo esc_attr( $font_family ); ?>;
                --hcc-font-size: <?php echo esc_attr( $font_size ); ?>px;
                --hcc-border-radius: <?php echo esc_attr( $border_radius ); ?>px;
            }
            
            .hcc-calculator-wrapper {
                font-family: var(--hcc-font-family);
                font-size: var(--hcc-font-size);
                color: var(--hcc-text-color);
                background-color: var(--hcc-background-color);
            }
            
            .hcc-button-primary {
                background-color: var(--hcc-primary-color);
                border-radius: var(--hcc-border-radius);
            }
            
            .hcc-button-secondary {
                background-color: var(--hcc-secondary-color);
                border-radius: var(--hcc-border-radius);
            }
            
            .hcc-input,
            .hcc-select {
                border-color: var(--hcc-border-color);
                border-radius: var(--hcc-border-radius);
            }
            
            .hcc-room-card {
                border-color: var(--hcc-border-color);
                border-radius: var(--hcc-border-radius);
            }
            
            <?php echo wp_strip_all_tags( $custom_css ); ?>
        </style>
        <?php
    }

    /**
     * Render calculator
     *
     * @since    2.0.0
     * @param    array    $atts    Shortcode attributes
     * @return   string            HTML output
     */
    public function render_calculator( $atts ) {
        
        // Extract attributes
        $theme = isset( $atts['theme'] ) ? sanitize_text_field( $atts['theme'] ) : 'default';
        $show_logo = isset( $atts['show_logo'] ) ? sanitize_text_field( $atts['show_logo'] ) : 'yes';
        $show_title = isset( $atts['show_title'] ) ? sanitize_text_field( $atts['show_title'] ) : 'yes';
        $custom_class = isset( $atts['custom_class'] ) ? sanitize_html_class( $atts['custom_class'] ) : '';
        
        // Get calculator instance
        $calculator = new HCC_Calculator();
        $room_types = $calculator->get_active_room_types();
        
        // Check if room types exist
        if ( empty( $room_types ) ) {
            return '<div class="hcc-error"><p>' . esc_html__( 'No room types configured. Please contact the administrator.', 'hotel-cleaning-calculator-pro' ) . '</p></div>';
        }
        
        // Start output buffering
        ob_start();
        
        // Include calculator template
        include HCC_PLUGIN_DIR . 'public/partials/hcc-calculator-display.php';
        
        return ob_get_clean();
    }

    /**
     * Get translated strings for JavaScript
     *
     * @since    2.0.0
     * @return   array    Translated strings
     */
    private function get_translated_strings() {
        return array(
            'calculatorTitle'   => get_option( 'hcc_text_calculator_title', __( 'Calculate Cleaning Cost', 'hotel-cleaning-calculator-pro' ) ),
            'addRoom'           => get_option( 'hcc_text_add_room', __( 'Add Room', 'hotel-cleaning-calculator-pro' ) ),
            'removeRoom'        => get_option( 'hcc_text_remove_room', __( 'Remove', 'hotel-cleaning-calculator-pro' ) ),
            'roomType'          => get_option( 'hcc_text_room_type', __( 'Room Type', 'hotel-cleaning-calculator-pro' ) ),
            'roomArea'          => get_option( 'hcc_text_room_area', __( 'Area (m²)', 'hotel-cleaning-calculator-pro' ) ),
            'subtotal'          => get_option( 'hcc_text_room_subtotal', __( 'Subtotal', 'hotel-cleaning-calculator-pro' ) ),
            'total'             => get_option( 'hcc_text_total', __( 'Total Price', 'hotel-cleaning-calculator-pro' ) ),
            'calculate'         => get_option( 'hcc_text_calculate', __( 'Calculate', 'hotel-cleaning-calculator-pro' ) ),
            'getQuote'          => get_option( 'hcc_text_get_quote', __( 'Get Quote', 'hotel-cleaning-calculator-pro' ) ),
            'selectRoomType'    => get_option( 'hcc_text_select_room_type', __( 'Select Room Type', 'hotel-cleaning-calculator-pro' ) ),
            'discountApplied'   => get_option( 'hcc_text_discount_applied', __( 'Discount Applied', 'hotel-cleaning-calculator-pro' ) ),
            'errorMessage'      => get_option( 'hcc_text_error_message', __( 'An error occurred. Please try again.', 'hotel-cleaning-calculator-pro' ) ),
            'calculating'       => __( 'Calculating...', 'hotel-cleaning-calculator-pro' ),
            'pleaseWait'        => __( 'Please wait...', 'hotel-cleaning-calculator-pro' ),
        );
    }

    /**
     * Get logo URL
     *
     * @since    2.0.0
     * @return   string|bool    Logo URL or false
     */
    public function get_logo_url() {
        $logo_url = get_option( 'hcc_logo_url', '' );
        
        if ( empty( $logo_url ) ) {
            return false;
        }
        
        return esc_url( $logo_url );
    }

    /**
     * Get company name
     *
     * @since    2.0.0
     * @return   string    Company name
     */
    public function get_company_name() {
        return get_option( 'hcc_company_name', get_bloginfo( 'name' ) );
    }

    /**
     * Get company tagline
     *
     * @since    2.0.0
     * @return   string    Company tagline
     */
    public function get_company_tagline() {
        return get_option( 'hcc_company_tagline', '' );
    }

    /**
     * Check if logo should be displayed
     *
     * @since    2.0.0
     * @return   bool    True if logo should be displayed
     */
    public function should_show_logo() {
        return get_option( 'hcc_show_logo', 'yes' ) === 'yes';
    }
}