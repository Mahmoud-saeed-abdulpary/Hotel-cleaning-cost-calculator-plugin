<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Deactivator {

    /**
     * Plugin deactivation handler
     *
     * Clears scheduled events, flushes caches, and performs cleanup.
     * Does NOT delete data - that's handled by uninstall.php
     *
     * @since    2.0.0
     */
    public static function deactivate() {
        
        // Clear all scheduled cron events
        self::clear_scheduled_events();
        
        // Clear transients
        self::clear_transients();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear object cache
        wp_cache_flush();
        
        // Log deactivation
        self::log_deactivation();
        
        // Send deactivation notification to admin (if enabled)
        self::notify_admin();
    }

    /**
     * Clear all scheduled cron events
     *
     * @since    2.0.0
     */
    private static function clear_scheduled_events() {
        
        // List of scheduled hooks
        $hooks = array(
            'hcc_daily_cleanup',
            'hcc_quote_reminder',
            'hcc_export_cleanup',
            'hcc_activity_log_cleanup',
            'hcc_cache_cleanup',
            'hcc_telegram_queue',
        );
        
        foreach ( $hooks as $hook ) {
            $timestamp = wp_next_scheduled( $hook );
            if ( $timestamp ) {
                wp_unschedule_event( $timestamp, $hook );
            }
        }
        
        // Clear all instances of recurring events
        foreach ( $hooks as $hook ) {
            wp_clear_scheduled_hook( $hook );
        }
    }

    /**
     * Clear plugin transients
     *
     * @since    2.0.0
     */
    private static function clear_transients() {
        global $wpdb;
        
        // Delete all HCC transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_hcc_%' 
            OR option_name LIKE '_transient_timeout_hcc_%'"
        );
        
        // Clear specific transients
        delete_transient( 'hcc_room_types_cache' );
        delete_transient( 'hcc_discount_rules_cache' );
        delete_transient( 'hcc_settings_cache' );
        delete_transient( 'hcc_activation_notice' );
    }

    /**
     * Log deactivation event
     *
     * @since    2.0.0
     */
    private static function log_deactivation() {
        
        // Only log if activity logging is enabled
        if ( get_option( 'hcc_enable_activity_log', 'yes' ) !== 'yes' ) {
            return;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'hcc_activity_log';
        
        // Check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
            return;
        }
        
        $user = wp_get_current_user();
        
        $wpdb->insert(
            $table,
            array(
                'action_type'  => 'plugin_deactivated',
                'object_type'  => 'plugin',
                'object_id'    => 0,
                'user_id'      => $user->ID,
                'user_name'    => $user->display_name,
                'description'  => 'Hotel Cleaning Calculator PRO was deactivated',
                'metadata'     => json_encode( array(
                    'version' => HCC_VERSION,
                    'php_version' => PHP_VERSION,
                    'wp_version' => get_bloginfo( 'version' ),
                ) ),
                'ip_address'   => self::get_user_ip(),
                'user_agent'   => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
                'created_at'   => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
        );
    }

    /**
     * Send deactivation notification to admin
     *
     * @since    2.0.0
     */
    private static function notify_admin() {
        
        // Check if notifications are enabled
        $notify = get_option( 'hcc_notify_deactivation', 'no' );
        
        if ( $notify !== 'yes' ) {
            return;
        }
        
        $admin_email = get_option( 'hcc_quote_email_admin', get_option( 'admin_email' ) );
        $site_name = get_bloginfo( 'name' );
        $user = wp_get_current_user();
        
        $subject = sprintf(
            __( '[%s] Hotel Cleaning Calculator PRO Deactivated', 'hotel-cleaning-calculator-pro' ),
            $site_name
        );
        
        $message = sprintf(
            __( "Hotel Cleaning Calculator PRO has been deactivated.\n\nDeactivated by: %s\nTime: %s\n\nSite: %s", 'hotel-cleaning-calculator-pro' ),
            $user->display_name,
            current_time( 'mysql' ),
            home_url()
        );
        
        wp_mail( $admin_email, $subject, $message );
    }

    /**
     * Get user IP address
     *
     * @since    2.0.0
     * @return   string    IP address
     */
    private static function get_user_ip() {
        $ip = '';
        
        if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] );
        } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
        }
        
        return $ip;
    }

    /**
     * Store deactivation feedback (if collected)
     *
     * @since    2.0.0
     * @param    array    $feedback    Feedback data
     */
    public static function store_feedback( $feedback ) {
        
        if ( empty( $feedback ) ) {
            return;
        }
        
        $feedback_data = get_option( 'hcc_deactivation_feedback', array() );
        
        $feedback_data[] = array(
            'reason'    => isset( $feedback['reason'] ) ? sanitize_text_field( $feedback['reason'] ) : '',
            'details'   => isset( $feedback['details'] ) ? sanitize_textarea_field( $feedback['details'] ) : '',
            'timestamp' => current_time( 'mysql' ),
            'user_id'   => get_current_user_id(),
        );
        
        update_option( 'hcc_deactivation_feedback', $feedback_data );
    }
}