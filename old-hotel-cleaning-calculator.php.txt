<?php
/**
 * Plugin Name:       Hotel Cleaning Calculator PRO
 * Plugin URI:        https://yourwebsite.com/hotel-cleaning-calculator-pro
 * Description:       Professional hotel cleaning cost calculator with advanced pricing logic, quote management, conditional discounts, and full UI customization.
 * Version:           2.0.0
 * Author:            Your Name
 * Author URI:        https://yourwebsite.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hotel-cleaning-calculator-pro
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 * Start at version 2.0.0 and use SemVer - https://semver.org
 */
define( 'HCC_VERSION', '2.0.0' );

/**
 * Plugin base name
 */
define( 'HCC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Plugin directory path
 */
define( 'HCC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL
 */
define( 'HCC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Database table names
 */
global $wpdb;
define( 'HCC_QUOTES_TABLE', $wpdb->prefix . 'hcc_quotes' );
define( 'HCC_DISCOUNT_RULES_TABLE', $wpdb->prefix . 'hcc_discount_rules' );
define( 'HCC_ACTIVITY_LOG_TABLE', $wpdb->prefix . 'hcc_activity_log' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hcc-activator.php
 */
function activate_hotel_cleaning_calculator() {
    require_once HCC_PLUGIN_DIR . 'includes/class-hcc-activator.php';
    HCC_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hcc-deactivator.php
 */
function deactivate_hotel_cleaning_calculator() {
    require_once HCC_PLUGIN_DIR . 'includes/class-hcc-deactivator.php';
    HCC_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hotel_cleaning_calculator' );
register_deactivation_hook( __FILE__, 'deactivate_hotel_cleaning_calculator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require HCC_PLUGIN_DIR . 'includes/class-hcc-calculator.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-database.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-admin.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-public.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-ajax.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-quotes.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-discounts.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-integrations.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-customizer.php';
require HCC_PLUGIN_DIR . 'includes/class-hcc-translation.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_hotel_cleaning_calculator() {
    
    // Initialize core components
    $calculator = new HCC_Calculator();
    $database = new HCC_Database();
    $admin = new HCC_Admin();
    $public = new HCC_Public();
    $ajax = new HCC_Ajax();
    $quotes = new HCC_Quotes();
    $discounts = new HCC_Discounts();
    $integrations = new HCC_Integrations();
    $customizer = new HCC_Customizer();
    $translation = new HCC_Translation();
    
    // Initialize all components
    $calculator->init();
    $database->init();
    $admin->init();
    $public->init();
    $ajax->init();
    $quotes->init();
    $discounts->init();
    $integrations->init();
    $customizer->init();
    $translation->init();
    
    // Load Elementor widget if Elementor is active
    if ( did_action( 'elementor/loaded' ) ) {
        require_once HCC_PLUGIN_DIR . 'elementor/class-hcc-elementor-widget.php';
        add_action( 'elementor/widgets/widgets_registered', 'hcc_register_elementor_widget' );
    }
    
    // Load integrations conditionally
    load_hcc_integrations();
}

/**
 * Load third-party integrations if plugins are active
 *
 * @since    2.0.0
 */
function load_hcc_integrations() {
    
    // Fluent Forms integration
    // if ( defined( 'FLUENTFORM' ) ) {
    //     require_once HCC_PLUGIN_DIR . 'integrations/class-hcc-fluent-forms.php';
    //     $fluent_forms = new HCC_Fluent_Forms_Integration();
    //     $fluent_forms->init();
    // }
    
    // Contact Form 7 integration
    if ( defined( 'WPCF7_VERSION' ) ) {
        require_once HCC_PLUGIN_DIR . 'integrations/class-hcc-contact-form-7.php';
        $cf7 = new HCC_Contact_Form_7_Integration();
        $cf7->init();
    }
    
    // WPForms integration
    // if ( function_exists( 'wpforms' ) ) {
    //     require_once HCC_PLUGIN_DIR . 'integrations/class-hcc-wpforms.php';
    //     $wpforms = new HCC_WPForms_Integration();
    //     $wpforms->init();
    // }
    
    // Telegram integration
    $telegram_enabled = get_option( 'hcc_telegram_enabled', false );
    if ( $telegram_enabled ) {
        require_once HCC_PLUGIN_DIR . 'integrations/class-hcc-telegram.php';
        $telegram = new HCC_Telegram();
        $telegram->init();
    }
    
    // Email/SMTP integration
    require_once HCC_PLUGIN_DIR . 'integrations/class-hcc-email.php';
    $email = new HCC_Email();
    $email->init();
}

/**
 * Register Elementor widget
 *
 * @since    2.0.0
 */
function hcc_register_elementor_widget() {
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \HCC_Elementor_Widget() );
}

/**
 * Load plugin textdomain for translations
 *
 * @since    2.0.0
 */
function hcc_load_textdomain() {
    load_plugin_textdomain(
        'hotel-cleaning-calculator-pro',
        false,
        dirname( HCC_PLUGIN_BASENAME ) . '/languages/'
    );
}
add_action( 'plugins_loaded', 'hcc_load_textdomain' );

/**
 * Add settings link on plugin page
 *
 * @since    2.0.0
 * @param    array    $links    Existing links
 * @return   array              Modified links
 */
function hcc_add_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'admin.php?page=hotel-cleaning-calculator' ) . '">' . __( 'Settings', 'hotel-cleaning-calculator-pro' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . HCC_PLUGIN_BASENAME, 'hcc_add_settings_link' );

/**
 * Add custom admin notices
 *
 * @since    2.0.0
 */
function hcc_admin_notices() {
    
    // Check if PHP version is compatible
    if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
        ?>
        <div class="notice notice-error">
            <p>
                <?php 
                echo sprintf(
                    __( 'Hotel Cleaning Calculator PRO requires PHP version 7.4 or higher. You are running version %s. Please upgrade PHP.', 'hotel-cleaning-calculator-pro' ),
                    PHP_VERSION
                );
                ?>
            </p>
        </div>
        <?php
    }
    
    // Check if WordPress version is compatible
    global $wp_version;
    if ( version_compare( $wp_version, '5.8', '<' ) ) {
        ?>
        <div class="notice notice-error">
            <p>
                <?php 
                echo sprintf(
                    __( 'Hotel Cleaning Calculator PRO requires WordPress version 5.8 or higher. You are running version %s. Please upgrade WordPress.', 'hotel-cleaning-calculator-pro' ),
                    $wp_version
                );
                ?>
            </p>
        </div>
        <?php
    }
    
    // Show welcome notice on first activation
    if ( get_transient( 'hcc_activation_notice' ) ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php _e( 'Hotel Cleaning Calculator PRO activated!', 'hotel-cleaning-calculator-pro' ); ?></strong>
                <?php 
                echo sprintf(
                    __( 'Get started by <a href="%s">configuring your room types</a>.', 'hotel-cleaning-calculator-pro' ),
                    admin_url( 'admin.php?page=hotel-cleaning-calculator' )
                );
                ?>
            </p>
        </div>
        <?php
        delete_transient( 'hcc_activation_notice' );
    }
}
add_action( 'admin_notices', 'hcc_admin_notices' );

/**
 * Register main shortcode
 *
 * @since    2.0.0
 * @param    array    $atts    Shortcode attributes
 * @return   string            HTML output
 */
function hcc_calculator_shortcode( $atts ) {
    
    // Parse shortcode attributes
    $atts = shortcode_atts( array(
        'theme'          => 'default',
        'show_logo'      => 'yes',
        'show_title'     => 'yes',
        'button_text'    => '',
        'custom_class'   => '',
    ), $atts, 'hotel_cleaning_calculator' );
    
    // Get public instance and render calculator
    $public = new HCC_Public();
    return $public->render_calculator( $atts );
}
add_shortcode( 'hotel_cleaning_calculator', 'hcc_calculator_shortcode' );

/**
 * Register quote form shortcode
 *
 * @since    2.0.0
 * @param    array    $atts    Shortcode attributes
 * @return   string            HTML output
 */
function hcc_quote_form_shortcode( $atts ) {
    
    // Parse shortcode attributes
    $atts = shortcode_atts( array(
        'show_title'     => 'yes',
        'redirect_url'   => '',
        'custom_class'   => '',
    ), $atts, 'hcc_quote_form' );
    
    // Get quotes instance and render form
    $quotes = new HCC_Quotes();
    return $quotes->render_quote_form( $atts );
}
add_shortcode( 'hcc_quote_form', 'hcc_quote_form_shortcode' );

/**
 * Check for plugin updates
 *
 * @since    2.0.0
 */
function hcc_check_for_updates() {
    // This would connect to your update server if you're selling the plugin
    // For now, it's a placeholder for future functionality
    $current_version = HCC_VERSION;
    $stored_version = get_option( 'hcc_version', '1.0.0' );
    
    if ( version_compare( $current_version, $stored_version, '>' ) ) {
        // Run upgrade routine if needed
        do_action( 'hcc_plugin_updated', $stored_version, $current_version );
        update_option( 'hcc_version', $current_version );
    }
}
add_action( 'admin_init', 'hcc_check_for_updates' );

/**
 * Register REST API endpoints
 *
 * @since    2.0.0
 */
function hcc_register_rest_routes() {
    register_rest_route( 'hcc/v1', '/calculate', array(
        'methods'             => 'POST',
        'callback'            => 'hcc_rest_calculate',
        'permission_callback' => '__return_true',
    ) );
    
    register_rest_route( 'hcc/v1', '/quote', array(
        'methods'             => 'POST',
        'callback'            => 'hcc_rest_submit_quote',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'hcc_register_rest_routes' );

/**
 * REST API calculate endpoint
 *
 * @since    2.0.0
 * @param    WP_REST_Request    $request    Request object
 * @return   WP_REST_Response               Response object
 */
function hcc_rest_calculate( $request ) {
    $calculator = new HCC_Calculator();
    $rooms = $request->get_param( 'rooms' );
    
    if ( empty( $rooms ) || ! is_array( $rooms ) ) {
        return new WP_REST_Response( array(
            'success' => false,
            'message' => __( 'Invalid room data', 'hotel-cleaning-calculator-pro' ),
        ), 400 );
    }
    
    $result = $calculator->calculate_total( $rooms );
    
    return new WP_REST_Response( array(
        'success' => true,
        'data'    => $result,
    ), 200 );
}

/**
 * REST API quote submission endpoint
 *
 * @since    2.0.0
 * @param    WP_REST_Request    $request    Request object
 * @return   WP_REST_Response               Response object
 */
function hcc_rest_submit_quote( $request ) {
    $quotes = new HCC_Quotes();
    $result = $quotes->submit_quote( $request->get_params() );
    
    if ( $result['success'] ) {
        return new WP_REST_Response( $result, 200 );
    } else {
        return new WP_REST_Response( $result, 400 );
    }
}

/**
 * Initialize the plugin
 */
run_hotel_cleaning_calculator();

/**
 * Plugin fully loaded hook
 * Allows other plugins/themes to hook into HCC after it's loaded
 *
 * @since    2.0.0
 */
do_action( 'hcc_loaded' );