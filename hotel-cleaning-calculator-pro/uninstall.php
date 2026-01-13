<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Delete plugin options
 *
 * @since    2.0.0
 */
function hcc_delete_plugin_options() {
    
    // General settings
    delete_option( 'hcc_version' );
    delete_option( 'hcc_currency_symbol' );
    delete_option( 'hcc_currency_position' );
    delete_option( 'hcc_decimal_separator' );
    delete_option( 'hcc_thousand_separator' );
    delete_option( 'hcc_decimal_places' );
    
    // Room types (stored as serialized array)
    delete_option( 'hcc_room_types' );
    
    // Discount settings
    delete_option( 'hcc_discount_stacking' );
    delete_option( 'hcc_discount_display' );
    
    // Quote settings
    delete_option( 'hcc_quote_email_admin' );
    delete_option( 'hcc_quote_email_client' );
    delete_option( 'hcc_quote_prefix' );
    delete_option( 'hcc_quote_counter' );
    delete_option( 'hcc_quote_custom_fields' );
    
    // UI Customization
    delete_option( 'hcc_primary_color' );
    delete_option( 'hcc_secondary_color' );
    delete_option( 'hcc_accent_color' );
    delete_option( 'hcc_background_color' );
    delete_option( 'hcc_text_color' );
    delete_option( 'hcc_font_family' );
    delete_option( 'hcc_font_size' );
    delete_option( 'hcc_border_radius' );
    delete_option( 'hcc_spacing' );
    delete_option( 'hcc_custom_css' );
    
    // Branding
    delete_option( 'hcc_logo_url' );
    delete_option( 'hcc_company_name' );
    delete_option( 'hcc_company_tagline' );
    
    // Translation strings (delete all hcc_text_* options)
    delete_option( 'hcc_text_calculator_title' );
    delete_option( 'hcc_text_add_room' );
    delete_option( 'hcc_text_remove_room' );
    delete_option( 'hcc_text_room_type' );
    delete_option( 'hcc_text_room_area' );
    delete_option( 'hcc_text_subtotal' );
    delete_option( 'hcc_text_total' );
    delete_option( 'hcc_text_calculate' );
    delete_option( 'hcc_text_get_quote' );
    delete_option( 'hcc_text_quote_form_title' );
    delete_option( 'hcc_text_name' );
    delete_option( 'hcc_text_email' );
    delete_option( 'hcc_text_phone' );
    delete_option( 'hcc_text_address' );
    delete_option( 'hcc_text_message' );
    delete_option( 'hcc_text_submit' );
    delete_option( 'hcc_text_success_message' );
    delete_option( 'hcc_text_error_message' );
    
    // Integration settings
    delete_option( 'hcc_telegram_enabled' );
    delete_option( 'hcc_telegram_bot_token' );
    delete_option( 'hcc_telegram_chat_id' );
    delete_option( 'hcc_smtp_enabled' );
    delete_option( 'hcc_smtp_host' );
    delete_option( 'hcc_smtp_port' );
    delete_option( 'hcc_smtp_username' );
    delete_option( 'hcc_smtp_password' );
    delete_option( 'hcc_smtp_encryption' );
    delete_option( 'hcc_email_from' );
    delete_option( 'hcc_email_from_name' );
    
    // Elementor settings
    delete_option( 'hcc_elementor_default_theme' );
    
    // Form integration settings
    delete_option( 'hcc_fluent_forms_mapping' );
    delete_option( 'hcc_cf7_mapping' );
    delete_option( 'hcc_wpforms_mapping' );
    
    // Webhook settings
    delete_option( 'hcc_webhook_url' );
    delete_option( 'hcc_webhook_enabled' );
    
    // Advanced settings
    delete_option( 'hcc_enable_activity_log' );
    delete_option( 'hcc_cache_calculations' );
    delete_option( 'hcc_debug_mode' );
}

/**
 * Delete custom database tables
 *
 * @since    2.0.0
 */
function hcc_delete_custom_tables() {
    global $wpdb;
    
    $tables = array(
        $wpdb->prefix . 'hcc_quotes',
        $wpdb->prefix . 'hcc_discount_rules',
        $wpdb->prefix . 'hcc_activity_log',
    );
    
    foreach ( $tables as $table ) {
        $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
    }
}

/**
 * Delete uploaded files (logos, exports, etc.)
 *
 * @since    2.0.0
 */
function hcc_delete_uploaded_files() {
    $upload_dir = wp_upload_dir();
    $hcc_upload_dir = $upload_dir['basedir'] . '/hcc-uploads';
    
    if ( is_dir( $hcc_upload_dir ) ) {
        hcc_recursive_delete( $hcc_upload_dir );
    }
}

/**
 * Recursively delete directory and its contents
 *
 * @since    2.0.0
 * @param    string    $dir    Directory path
 */
function hcc_recursive_delete( $dir ) {
    if ( ! is_dir( $dir ) ) {
        return;
    }
    
    $files = array_diff( scandir( $dir ), array( '.', '..' ) );
    
    foreach ( $files as $file ) {
        $path = $dir . '/' . $file;
        if ( is_dir( $path ) ) {
            hcc_recursive_delete( $path );
        } else {
            unlink( $path );
        }
    }
    
    rmdir( $dir );
}

/**
 * Delete transients
 *
 * @since    2.0.0
 */
function hcc_delete_transients() {
    global $wpdb;
    
    $wpdb->query(
        "DELETE FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_hcc_%' 
        OR option_name LIKE '_transient_timeout_hcc_%'"
    );
}

/**
 * Delete user meta data
 *
 * @since    2.0.0
 */
function hcc_delete_user_meta() {
    global $wpdb;
    
    $wpdb->query(
        "DELETE FROM {$wpdb->usermeta} 
        WHERE meta_key LIKE 'hcc_%'"
    );
}

/**
 * Main uninstall function
 * Execute all cleanup operations
 *
 * @since    2.0.0
 */
function hcc_uninstall() {
    
    // Check if user wants to keep data on uninstall
    $keep_data = get_option( 'hcc_keep_data_on_uninstall', false );
    
    if ( ! $keep_data ) {
        
        // Delete all options
        hcc_delete_plugin_options();
        
        // Delete custom database tables
        hcc_delete_custom_tables();
        
        // Delete uploaded files
        hcc_delete_uploaded_files();
        
        // Delete transients
        hcc_delete_transients();
        
        // Delete user meta
        hcc_delete_user_meta();
        
        // Clear any cached data
        wp_cache_flush();
    }
    
    // Delete the "keep data" option itself (always delete this)
    delete_option( 'hcc_keep_data_on_uninstall' );
}

/**
 * Handle multisite uninstall
 *
 * @since    2.0.0
 */
function hcc_uninstall_multisite() {
    global $wpdb;
    
    if ( is_multisite() ) {
        
        // Get all blog IDs
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
        
        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            hcc_uninstall();
            restore_current_blog();
        }
        
    } else {
        hcc_uninstall();
    }
}

/**
 * Execute uninstall
 */
if ( is_multisite() ) {
    hcc_uninstall_multisite();
} else {
    hcc_uninstall();
}

/**
 * Log uninstall event
 * This runs even if data is kept
 */
error_log( 'Hotel Cleaning Calculator PRO has been uninstalled at ' . current_time( 'mysql' ) );