<?php
/**
 * Admin-specific functionality
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Admin-specific functionality.
 *
 * Defines the admin menu, settings, and admin-facing functionality.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Admin {

    /**
     * Initialize the admin functionality
     *
     * @since    2.0.0
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
    }

    /**
     * Add admin menu pages
     *
     * @since    2.0.0
     */
    public function add_admin_menu() {
        
        // Main menu
        add_menu_page(
            __( 'Cleaning Calculator', 'hotel-cleaning-calculator-pro' ),
            __( 'Cleaning Calculator', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hotel-cleaning-calculator',
            array( $this, 'render_main_page' ),
            'dashicons-calculator',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Dashboard', 'hotel-cleaning-calculator-pro' ),
            __( 'Dashboard', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hotel-cleaning-calculator',
            array( $this, 'render_main_page' )
        );
        
        // Room Types submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Room Types', 'hotel-cleaning-calculator-pro' ),
            __( 'Room Types', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-room-types',
            array( $this, 'render_room_types_page' )
        );
        
        // Quotes submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Quotes', 'hotel-cleaning-calculator-pro' ),
            __( 'Quotes', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-quotes',
            array( $this, 'render_quotes_page' )
        );
        
        // Discounts submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Discounts', 'hotel-cleaning-calculator-pro' ),
            __( 'Discounts', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-discounts',
            array( $this, 'render_discounts_page' )
        );
        
        // UI Customization submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'UI Customization', 'hotel-cleaning-calculator-pro' ),
            __( 'UI Customization', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-customizer',
            array( $this, 'render_customizer_page' )
        );
        
        // Integrations submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Integrations', 'hotel-cleaning-calculator-pro' ),
            __( 'Integrations', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-integrations',
            array( $this, 'render_integrations_page' )
        );
        
        // Translations submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Translations', 'hotel-cleaning-calculator-pro' ),
            __( 'Translations', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-translations',
            array( $this, 'render_translations_page' )
        );
        
        // Branding submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Branding', 'hotel-cleaning-calculator-pro' ),
            __( 'Branding', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-branding',
            array( $this, 'render_branding_page' )
        );
        
        // Settings submenu
        add_submenu_page(
            'hotel-cleaning-calculator',
            __( 'Settings', 'hotel-cleaning-calculator-pro' ),
            __( 'Settings', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Enqueue admin assets
     *
     * @since    2.0.0
     * @param    string    $hook    Current admin page hook
     */
    public function enqueue_admin_assets( $hook ) {
        
        // Only load on plugin pages
        if ( strpos( $hook, 'hotel-cleaning-calculator' ) === false && strpos( $hook, 'hcc-' ) === false ) {
            return;
        }
        
        // Admin CSS
        wp_enqueue_style(
            'hcc-admin-styles',
            HCC_PLUGIN_URL . 'admin/css/hcc-admin.css',
            array(),
            HCC_VERSION,
            'all'
        );
        
        // Customizer CSS (only on customizer page)
        if ( $hook === 'cleaning-calculator_page_hcc-customizer' ) {
            wp_enqueue_style(
                'hcc-customizer-styles',
                HCC_PLUGIN_URL . 'admin/css/hcc-customizer.css',
                array(),
                HCC_VERSION,
                'all'
            );
        }
        
        // WordPress color picker
        wp_enqueue_style( 'wp-color-picker' );
        
        // Admin JS
        wp_enqueue_script(
            'hcc-admin-scripts',
            HCC_PLUGIN_URL . 'admin/js/hcc-admin.js',
            array( 'jquery', 'wp-color-picker' ),
            HCC_VERSION,
            true
        );
        
        // Room manager JS (only on room types page)
        if ( $hook === 'cleaning-calculator_page_hcc-room-types' ) {
            wp_enqueue_script(
                'hcc-room-manager',
                HCC_PLUGIN_URL . 'admin/js/hcc-room-manager.js',
                array( 'jquery', 'jquery-ui-sortable' ),
                HCC_VERSION,
                true
            );
        }
        
        // Discount manager JS (only on discounts page)
        if ( $hook === 'cleaning-calculator_page_hcc-discounts' ) {
            wp_enqueue_script(
                'hcc-discount-manager',
                HCC_PLUGIN_URL . 'admin/js/hcc-discount-manager.js',
                array( 'jquery' ),
                HCC_VERSION,
                true
            );
        }
        
        // Quote manager JS (only on quotes page)
        if ( $hook === 'cleaning-calculator_page_hcc-quotes' ) {
            wp_enqueue_script(
                'hcc-quote-manager',
                HCC_PLUGIN_URL . 'admin/js/hcc-quote-manager.js',
                array( 'jquery' ),
                HCC_VERSION,
                true
            );
        }
        
        // Customizer JS (only on customizer page)
        if ( $hook === 'cleaning-calculator_page_hcc-customizer' ) {
            wp_enqueue_script(
                'hcc-customizer',
                HCC_PLUGIN_URL . 'admin/js/hcc-customizer.js',
                array( 'jquery', 'wp-color-picker' ),
                HCC_VERSION,
                true
            );
            
            wp_enqueue_script(
                'hcc-color-picker',
                HCC_PLUGIN_URL . 'admin/js/hcc-color-picker.js',
                array( 'jquery', 'wp-color-picker' ),
                HCC_VERSION,
                true
            );
        }
        
        // Localize script
        wp_localize_script(
            'hcc-admin-scripts',
            'hccAdmin',
            array(
                'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
                'nonce'      => wp_create_nonce( 'hcc_admin_nonce' ),
                'pluginUrl'  => HCC_PLUGIN_URL,
                'strings'    => array(
                    'confirmDelete' => __( 'Are you sure you want to delete this item?', 'hotel-cleaning-calculator-pro' ),
                    'saved'         => __( 'Saved successfully!', 'hotel-cleaning-calculator-pro' ),
                    'error'         => __( 'An error occurred. Please try again.', 'hotel-cleaning-calculator-pro' ),
                ),
            )
        );
    }

    /**
     * Register plugin settings
     *
     * @since    2.0.0
     */
    public function register_settings() {
        
        // General settings
        register_setting( 'hcc_general_settings', 'hcc_currency_symbol' );
        register_setting( 'hcc_general_settings', 'hcc_currency_position' );
        register_setting( 'hcc_general_settings', 'hcc_decimal_separator' );
        register_setting( 'hcc_general_settings', 'hcc_thousand_separator' );
        register_setting( 'hcc_general_settings', 'hcc_decimal_places' );
        
        // Discount settings
        register_setting( 'hcc_discount_settings', 'hcc_discount_stacking' );
        register_setting( 'hcc_discount_settings', 'hcc_discount_display' );
        
        // Quote settings
        register_setting( 'hcc_quote_settings', 'hcc_quote_email_admin' );
        register_setting( 'hcc_quote_settings', 'hcc_quote_email_client' );
        register_setting( 'hcc_quote_settings', 'hcc_quote_prefix' );
        
        // Advanced settings
        register_setting( 'hcc_advanced_settings', 'hcc_enable_activity_log' );
        register_setting( 'hcc_advanced_settings', 'hcc_cache_calculations' );
        register_setting( 'hcc_advanced_settings', 'hcc_debug_mode' );
        register_setting( 'hcc_advanced_settings', 'hcc_keep_data_on_uninstall' );
    }

    /**
     * Render main dashboard page
     *
     * @since    2.0.0
     */
    public function render_main_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-admin-display.php';
    }

    /**
     * Render room types page
     *
     * @since    2.0.0
     */
    public function render_room_types_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-room-types.php';
    }

    /**
     * Render quotes page
     *
     * @since    2.0.0
     */
    public function render_quotes_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-quotes.php';
    }

    /**
     * Render discounts page
     *
     * @since    2.0.0
     */
    public function render_discounts_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-discounts.php';
    }

    /**
     * Render customizer page
     *
     * @since    2.0.0
     */
    public function render_customizer_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-customizer-panel.php';
    }

    /**
     * Render integrations page
     *
     * @since    2.0.0
     */
    public function render_integrations_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-integrations.php';
    }

    /**
     * Render translations page
     *
     * @since    2.0.0
     */
    public function render_translations_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-translations.php';
    }

    /**
     * Render branding page
     *
     * @since    2.0.0
     */
    public function render_branding_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-branding.php';
    }

    /**
     * Render settings page
     *
     * @since    2.0.0
     */
    public function render_settings_page() {
        require_once HCC_PLUGIN_DIR . 'admin/partials/hcc-settings.php';
    }

    /**
     * Add dashboard widgets
     *
     * @since    2.0.0
     */
    public function add_dashboard_widgets() {
        wp_add_dashboard_widget(
            'hcc_dashboard_widget',
            __( 'Hotel Cleaning Calculator - Recent Quotes', 'hotel-cleaning-calculator-pro' ),
            array( $this, 'render_dashboard_widget' )
        );
    }

    /**
     * Render dashboard widget
     *
     * @since    2.0.0
     */
    public function render_dashboard_widget() {
        
        $recent_quotes = HCC_Database::get_quotes( array(
            'limit' => 5,
        ) );
        
        if ( empty( $recent_quotes ) ) {
            echo '<p>' . esc_html__( 'No quotes yet.', 'hotel-cleaning-calculator-pro' ) . '</p>';
            return;
        }
        
        echo '<table class="widefat">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__( 'Quote #', 'hotel-cleaning-calculator-pro' ) . '</th>';
        echo '<th>' . esc_html__( 'Client', 'hotel-cleaning-calculator-pro' ) . '</th>';
        echo '<th>' . esc_html__( 'Total', 'hotel-cleaning-calculator-pro' ) . '</th>';
        echo '<th>' . esc_html__( 'Status', 'hotel-cleaning-calculator-pro' ) . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        
        foreach ( $recent_quotes as $quote ) {
            $calculator = new HCC_Calculator();
            echo '<tr>';
            echo '<td>' . esc_html( $quote->quote_number ) . '</td>';
            echo '<td>' . esc_html( $quote->client_name ) . '</td>';
            echo '<td>' . esc_html( $calculator->format_price( $quote->total_price ) ) . '</td>';
            echo '<td>' . esc_html( ucfirst( $quote->status ) ) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
        
        echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=hcc-quotes' ) ) . '" class="button">';
        echo esc_html__( 'View All Quotes', 'hotel-cleaning-calculator-pro' );
        echo '</a></p>';
    }

    /**
     * Get statistics for dashboard
     *
     * @since    2.0.0
     * @return   array    Statistics data
     */
    public function get_statistics() {
        global $wpdb;
        
        $quotes_table = $wpdb->prefix . 'hcc_quotes';
        
        $stats = array();
        
        // Total quotes
        $stats['total_quotes'] = $wpdb->get_var( "SELECT COUNT(*) FROM $quotes_table" );
        
        // Pending quotes
        $stats['pending_quotes'] = $wpdb->get_var(
            $wpdb->prepare( "SELECT COUNT(*) FROM $quotes_table WHERE status = %s", 'pending' )
        );
        
        // Total revenue (approved quotes)
        $stats['total_revenue'] = $wpdb->get_var(
            $wpdb->prepare( "SELECT SUM(total_price) FROM $quotes_table WHERE status = %s", 'approved' )
        );
        
        // This month's quotes
        $stats['month_quotes'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $quotes_table WHERE MONTH(created_at) = %d AND YEAR(created_at) = %d",
                date( 'n' ),
                date( 'Y' )
            )
        );
        
        return $stats;
    }
}