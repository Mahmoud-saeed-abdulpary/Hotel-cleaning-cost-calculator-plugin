<?php
/**
 * Fired during plugin activation
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Activator {

    /**
     * Plugin activation handler
     *
     * Creates database tables, sets default options, and performs initial setup.
     *
     * @since    2.0.0
     */
    public static function activate() {
        
        // Check WordPress version
        global $wp_version;
        if ( version_compare( $wp_version, '5.8', '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'Hotel Cleaning Calculator PRO requires WordPress 5.8 or higher.', 'hotel-cleaning-calculator-pro' ) );
        }
        
        // Check PHP version
        if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'Hotel Cleaning Calculator PRO requires PHP 7.4 or higher.', 'hotel-cleaning-calculator-pro' ) );
        }
        
        // Create custom database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create default room types
        self::create_default_room_types();
        
        // Create upload directory
        self::create_upload_directory();
        
        // Set activation flag
        set_transient( 'hcc_activation_notice', true, 30 );
        
        // Store activation time
        update_option( 'hcc_activation_time', current_time( 'timestamp' ) );
        
        // Store plugin version
        update_option( 'hcc_version', HCC_VERSION );
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create custom database tables
     *
     * @since    2.0.0
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        // Table 1: Quotes
        $quotes_table = $wpdb->prefix . 'hcc_quotes';
        $sql_quotes = "CREATE TABLE IF NOT EXISTS $quotes_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            quote_number varchar(50) NOT NULL,
            client_name varchar(255) NOT NULL,
            client_email varchar(255) NOT NULL,
            client_phone varchar(50) DEFAULT NULL,
            client_address text DEFAULT NULL,
            rooms_data longtext NOT NULL,
            total_area decimal(10,2) NOT NULL DEFAULT 0.00,
            subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
            discount_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            total_price decimal(10,2) NOT NULL DEFAULT 0.00,
            applied_discounts text DEFAULT NULL,
            custom_fields longtext DEFAULT NULL,
            additional_services longtext DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'pending',
            admin_notes text DEFAULT NULL,
            ip_address varchar(100) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY quote_number (quote_number),
            KEY status (status),
            KEY client_email (client_email),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Table 2: Discount Rules
        $discounts_table = $wpdb->prefix . 'hcc_discount_rules';
        $sql_discounts = "CREATE TABLE IF NOT EXISTS $discounts_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            rule_name varchar(255) NOT NULL,
            description text DEFAULT NULL,
            discount_type varchar(50) NOT NULL DEFAULT 'percentage',
            discount_value decimal(10,2) NOT NULL,
            conditions longtext DEFAULT NULL,
            date_start datetime DEFAULT NULL,
            date_end datetime DEFAULT NULL,
            days_of_week varchar(255) DEFAULT NULL,
            priority int(11) NOT NULL DEFAULT 0,
            stackable tinyint(1) NOT NULL DEFAULT 0,
            discount_code varchar(100) DEFAULT NULL,
            usage_limit int(11) DEFAULT NULL,
            usage_count int(11) NOT NULL DEFAULT 0,
            active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY active (active),
            KEY priority (priority),
            KEY discount_code (discount_code),
            KEY date_range (date_start, date_end)
        ) $charset_collate;";
        
        // Table 3: Activity Log
        $activity_table = $wpdb->prefix . 'hcc_activity_log';
        $sql_activity = "CREATE TABLE IF NOT EXISTS $activity_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            action_type varchar(100) NOT NULL,
            object_type varchar(100) DEFAULT NULL,
            object_id bigint(20) DEFAULT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_name varchar(255) DEFAULT NULL,
            description text DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            ip_address varchar(100) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY action_type (action_type),
            KEY object_type (object_type),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Execute table creation
        dbDelta( $sql_quotes );
        dbDelta( $sql_discounts );
        dbDelta( $sql_activity );
        
        // Log table creation
        error_log( 'HCC PRO: Database tables created successfully' );
    }

    /**
     * Set default plugin options
     *
     * @since    2.0.0
     */
    private static function set_default_options() {
        
        // General Settings
        add_option( 'hcc_currency_symbol', '$' );
        add_option( 'hcc_currency_position', 'before' );
        add_option( 'hcc_decimal_separator', '.' );
        add_option( 'hcc_thousand_separator', ',' );
        add_option( 'hcc_decimal_places', 2 );
        
        // Default room types (will be populated separately)
        add_option( 'hcc_room_types', array() );
        
        // Discount Settings
        add_option( 'hcc_discount_stacking', 'no' );
        add_option( 'hcc_discount_display', 'yes' );
        
        // Quote Settings
        add_option( 'hcc_quote_email_admin', get_option( 'admin_email' ) );
        add_option( 'hcc_quote_email_client', 'yes' );
        add_option( 'hcc_quote_prefix', 'HCC' );
        add_option( 'hcc_quote_counter', 1000 );
        add_option( 'hcc_quote_custom_fields', array() );
        
        // UI Customization - Colors
        add_option( 'hcc_primary_color', '#2563eb' );
        add_option( 'hcc_secondary_color', '#64748b' );
        add_option( 'hcc_accent_color', '#10b981' );
        add_option( 'hcc_background_color', '#ffffff' );
        add_option( 'hcc_text_color', '#1e293b' );
        add_option( 'hcc_border_color', '#e2e8f0' );
        
        // UI Customization - Typography
        add_option( 'hcc_font_family', 'system-ui' );
        add_option( 'hcc_font_size', '16' );
        add_option( 'hcc_font_weight', '400' );
        add_option( 'hcc_heading_font_size', '24' );
        
        // UI Customization - Layout
        add_option( 'hcc_border_radius', '8' );
        add_option( 'hcc_spacing', 'standard' );
        add_option( 'hcc_button_style', 'solid' );
        add_option( 'hcc_layout_preset', 'standard' );
        add_option( 'hcc_custom_css', '' );
        
        // Branding
        add_option( 'hcc_logo_url', '' );
        add_option( 'hcc_company_name', get_bloginfo( 'name' ) );
        add_option( 'hcc_company_tagline', '' );
        add_option( 'hcc_show_logo', 'yes' );
        add_option( 'hcc_show_powered_by', 'no' );
        
        // Translation Strings
        add_option( 'hcc_text_calculator_title', 'Calculate Cleaning Cost' );
        add_option( 'hcc_text_add_room', 'Add Room' );
        add_option( 'hcc_text_remove_room', 'Remove' );
        add_option( 'hcc_text_room_type', 'Room Type' );
        add_option( 'hcc_text_room_area', 'Area (m²)' );
        add_option( 'hcc_text_room_subtotal', 'Subtotal' );
        add_option( 'hcc_text_total', 'Total Price' );
        add_option( 'hcc_text_calculate', 'Calculate' );
        add_option( 'hcc_text_get_quote', 'Get Quote' );
        add_option( 'hcc_text_discount_applied', 'Discount Applied' );
        add_option( 'hcc_text_select_room_type', 'Select Room Type' );
        
        // Quote Form Translations
        add_option( 'hcc_text_quote_form_title', 'Request a Quote' );
        add_option( 'hcc_text_name', 'Full Name' );
        add_option( 'hcc_text_email', 'Email Address' );
        add_option( 'hcc_text_phone', 'Phone Number' );
        add_option( 'hcc_text_address', 'Property Address' );
        add_option( 'hcc_text_message', 'Additional Notes' );
        add_option( 'hcc_text_submit', 'Submit Quote Request' );
        add_option( 'hcc_text_success_message', 'Thank you! Your quote request has been submitted.' );
        add_option( 'hcc_text_error_message', 'Sorry, there was an error. Please try again.' );
        
        // Integration Settings
        add_option( 'hcc_telegram_enabled', 'no' );
        add_option( 'hcc_telegram_bot_token', '' );
        add_option( 'hcc_telegram_chat_id', '' );
        add_option( 'hcc_smtp_enabled', 'no' );
        add_option( 'hcc_smtp_host', '' );
        add_option( 'hcc_smtp_port', '587' );
        add_option( 'hcc_smtp_username', '' );
        add_option( 'hcc_smtp_password', '' );
        add_option( 'hcc_smtp_encryption', 'tls' );
        add_option( 'hcc_email_from', get_option( 'admin_email' ) );
        add_option( 'hcc_email_from_name', get_bloginfo( 'name' ) );
        
        // Advanced Settings
        add_option( 'hcc_enable_activity_log', 'yes' );
        add_option( 'hcc_cache_calculations', 'no' );
        add_option( 'hcc_debug_mode', 'no' );
        add_option( 'hcc_keep_data_on_uninstall', 'no' );
        
        // Webhook Settings
        add_option( 'hcc_webhook_url', '' );
        add_option( 'hcc_webhook_enabled', 'no' );
        
        // Form Integration Settings
        add_option( 'hcc_fluent_forms_mapping', array() );
        add_option( 'hcc_cf7_mapping', array() );
        add_option( 'hcc_wpforms_mapping', array() );
        
        // Elementor Settings
        add_option( 'hcc_elementor_default_theme', 'default' );
    }

    /**
     * Create default room types
     *
     * @since    2.0.0
     */
    private static function create_default_room_types() {
        
        $default_room_types = array(
            array(
                'id'          => 'bedroom',
                'name'        => 'Bedroom',
                'description' => 'Standard bedroom cleaning',
                'price_per_m2' => 5.00,
                'active'      => true,
                'icon'        => 'bed',
                'order'       => 1,
            ),
            array(
                'id'          => 'bathroom',
                'name'        => 'Bathroom',
                'description' => 'Complete bathroom cleaning and sanitization',
                'price_per_m2' => 8.00,
                'active'      => true,
                'icon'        => 'bath',
                'order'       => 2,
            ),
            array(
                'id'          => 'kitchen',
                'name'        => 'Kitchen',
                'description' => 'Thorough kitchen cleaning including appliances',
                'price_per_m2' => 7.00,
                'active'      => true,
                'icon'        => 'utensils',
                'order'       => 3,
            ),
            array(
                'id'          => 'living-room',
                'name'        => 'Living Room',
                'description' => 'Living room and common areas',
                'price_per_m2' => 4.50,
                'active'      => true,
                'icon'        => 'sofa',
                'order'       => 4,
            ),
            array(
                'id'          => 'office',
                'name'        => 'Office',
                'description' => 'Office and workspace cleaning',
                'price_per_m2' => 4.00,
                'active'      => true,
                'icon'        => 'briefcase',
                'order'       => 5,
            ),
        );
        
        update_option( 'hcc_room_types', $default_room_types );
    }

    /**
     * Create upload directory for logos and exports
     *
     * @since    2.0.0
     */
    private static function create_upload_directory() {
        $upload_dir = wp_upload_dir();
        $hcc_upload_dir = $upload_dir['basedir'] . '/hcc-uploads';
        
        if ( ! file_exists( $hcc_upload_dir ) ) {
            wp_mkdir_p( $hcc_upload_dir );
            
            // Create .htaccess for security
            $htaccess_content = "Options -Indexes\n";
            $htaccess_content .= "<Files *.php>\n";
            $htaccess_content .= "deny from all\n";
            $htaccess_content .= "</Files>\n";
            
            file_put_contents( $hcc_upload_dir . '/.htaccess', $htaccess_content );
            
            // Create index.php for additional security
            file_put_contents( $hcc_upload_dir . '/index.php', '<?php // Silence is golden' );
        }
        
        // Create subdirectories
        $subdirs = array( 'logos', 'exports', 'temp' );
        foreach ( $subdirs as $subdir ) {
            $path = $hcc_upload_dir . '/' . $subdir;
            if ( ! file_exists( $path ) ) {
                wp_mkdir_p( $path );
                file_put_contents( $path . '/index.php', '<?php // Silence is golden' );
            }
        }
    }
}