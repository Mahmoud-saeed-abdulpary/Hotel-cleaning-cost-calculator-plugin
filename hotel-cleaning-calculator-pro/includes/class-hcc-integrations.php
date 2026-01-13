<?php
/**
 * Integration management functionality
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Integration management functionality.
 *
 * Handles integrations with external services and plugins.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Integrations {

    /**
     * Initialize integrations
     *
     * @since    2.0.0
     */
    public function init() {
        // Integration setup is handled in main plugin file
    }

    /**
     * Test Telegram connection
     *
     * @since    2.0.0
     * @return   array    Test result
     */
    public function test_telegram_connection() {
        
        $bot_token = get_option( 'hcc_telegram_bot_token', '' );
        $chat_id = get_option( 'hcc_telegram_chat_id', '' );
        
        if ( empty( $bot_token ) || empty( $chat_id ) ) {
            return array(
                'success' => false,
                'message' => __( 'Bot token and Chat ID are required', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        
        $response = wp_remote_post( $url, array(
            'body' => array(
                'chat_id' => $chat_id,
                'text'    => '✅ Hotel Cleaning Calculator PRO: Connection test successful!',
            ),
            'timeout' => 15,
        ) );
        
        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => $response->get_error_message(),
            );
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( isset( $body['ok'] ) && $body['ok'] === true ) {
            return array(
                'success' => true,
                'message' => __( 'Telegram connection successful! Check your Telegram chat.', 'hotel-cleaning-calculator-pro' ),
            );
        } else {
            return array(
                'success' => false,
                'message' => isset( $body['description'] ) ? $body['description'] : __( 'Connection failed', 'hotel-cleaning-calculator-pro' ),
            );
        }
    }

    /**
     * Test SMTP connection
     *
     * @since    2.0.0
     * @return   array    Test result
     */
    public function test_smtp_connection() {
        
        $smtp_enabled = get_option( 'hcc_smtp_enabled', 'no' );
        
        if ( $smtp_enabled !== 'yes' ) {
            return array(
                'success' => false,
                'message' => __( 'SMTP is not enabled', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        $admin_email = get_option( 'hcc_quote_email_admin', get_option( 'admin_email' ) );
        $from_email = get_option( 'hcc_email_from', $admin_email );
        $from_name = get_option( 'hcc_email_from_name', get_bloginfo( 'name' ) );
        
        $subject = __( 'Hotel Cleaning Calculator PRO - SMTP Test', 'hotel-cleaning-calculator-pro' );
        $message = __( 'This is a test email to verify your SMTP configuration is working correctly.', 'hotel-cleaning-calculator-pro' );
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
        );
        
        add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
        
        $result = wp_mail( $admin_email, $subject, $message, $headers );
        
        remove_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
        
        if ( $result ) {
            return array(
                'success' => true,
                'message' => __( 'Test email sent successfully! Check your inbox.', 'hotel-cleaning-calculator-pro' ),
            );
        } else {
            return array(
                'success' => false,
                'message' => __( 'Failed to send test email. Please check your SMTP settings.', 'hotel-cleaning-calculator-pro' ),
            );
        }
    }

    /**
     * Configure SMTP for PHPMailer
     *
     * @since    2.0.0
     * @param    object    $phpmailer    PHPMailer instance
     */
    public function configure_smtp( $phpmailer ) {
        
        $smtp_host = get_option( 'hcc_smtp_host', '' );
        $smtp_port = get_option( 'hcc_smtp_port', '587' );
        $smtp_username = get_option( 'hcc_smtp_username', '' );
        $smtp_password = get_option( 'hcc_smtp_password', '' );
        $smtp_encryption = get_option( 'hcc_smtp_encryption', 'tls' );
        
        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_host;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $smtp_port;
        $phpmailer->Username = $smtp_username;
        $phpmailer->Password = $smtp_password;
        $phpmailer->SMTPSecure = $smtp_encryption;
        $phpmailer->From = get_option( 'hcc_email_from', get_option( 'admin_email' ) );
        $phpmailer->FromName = get_option( 'hcc_email_from_name', get_bloginfo( 'name' ) );
    }

    /**
     * Test webhook connection
     *
     * @since    2.0.0
     * @return   array    Test result
     */
    public function test_webhook() {
        
        $webhook_url = get_option( 'hcc_webhook_url', '' );
        
        if ( empty( $webhook_url ) ) {
            return array(
                'success' => false,
                'message' => __( 'Webhook URL is required', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        $test_payload = array(
            'test' => true,
            'message' => 'Hotel Cleaning Calculator PRO - Webhook test',
            'timestamp' => current_time( 'mysql' ),
        );
        
        $response = wp_remote_post( $webhook_url, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => json_encode( $test_payload ),
            'timeout' => 15,
        ) );
        
        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => $response->get_error_message(),
            );
        }
        
        $status_code = wp_remote_retrieve_response_code( $response );
        
        if ( $status_code >= 200 && $status_code < 300 ) {
            return array(
                'success' => true,
                'message' => __( 'Webhook test successful!', 'hotel-cleaning-calculator-pro' ),
            );
        } else {
            return array(
                'success' => false,
                'message' => sprintf(
                    __( 'Webhook returned status code: %d', 'hotel-cleaning-calculator-pro' ),
                    $status_code
                ),
            );
        }
    }

    /**
     * Get available form plugins
     *
     * @since    2.0.0
     * @return   array    Array of available form plugins
     */
    public function get_available_form_plugins() {
        
        $plugins = array();
        
        // Check for Fluent Forms
        if ( defined( 'FLUENTFORM' ) ) {
            $plugins['fluent_forms'] = array(
                'name'      => 'Fluent Forms',
                'version'   => FLUENTFORM_VERSION ?? 'Unknown',
                'active'    => true,
                'forms'     => $this->get_fluent_forms(),
            );
        }
        
        // Check for Contact Form 7
        if ( defined( 'WPCF7_VERSION' ) ) {
            $plugins['contact_form_7'] = array(
                'name'      => 'Contact Form 7',
                'version'   => WPCF7_VERSION,
                'active'    => true,
                'forms'     => $this->get_cf7_forms(),
            );
        }
        
        // Check for WPForms
        if ( function_exists( 'wpforms' ) ) {
            $plugins['wpforms'] = array(
                'name'      => 'WPForms',
                'version'   => WPFORMS_VERSION ?? 'Unknown',
                'active'    => true,
                'forms'     => $this->get_wpforms(),
            );
        }
        
        return $plugins;
    }

    /**
     * Get Fluent Forms list
     *
     * @since    2.0.0
     * @return   array    Array of forms
     */
    private function get_fluent_forms() {
        
        if ( ! defined( 'FLUENTFORM' ) ) {
            return array();
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'fluentform_forms';
        
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
            return array();
        }
        
        $forms = $wpdb->get_results( "SELECT id, title FROM $table ORDER BY title ASC" );
        
        $form_list = array();
        foreach ( $forms as $form ) {
            $form_list[ $form->id ] = $form->title;
        }
        
        return $form_list;
    }

    /**
     * Get Contact Form 7 forms list
     *
     * @since    2.0.0
     * @return   array    Array of forms
     */
    private function get_cf7_forms() {
        
        if ( ! defined( 'WPCF7_VERSION' ) ) {
            return array();
        }
        
        $forms = get_posts( array(
            'post_type'      => 'wpcf7_contact_form',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );
        
        $form_list = array();
        foreach ( $forms as $form ) {
            $form_list[ $form->ID ] = $form->post_title;
        }
        
        return $form_list;
    }

    /**
     * Get WPForms list
     *
     * @since    2.0.0
     * @return   array    Array of forms
     */
    private function get_wpforms() {
        
        if ( ! function_exists( 'wpforms' ) ) {
            return array();
        }
        
        $forms = wpforms()->form->get( '', array(
            'orderby' => 'title',
            'order'   => 'ASC',
        ) );
        
        $form_list = array();
        foreach ( $forms as $form ) {
            $form_list[ $form->ID ] = $form->post_title;
        }
        
        return $form_list;
    }

    /**
     * Map calculator data to form fields
     *
     * @since    2.0.0
     * @param    string    $form_type    Form plugin type
     * @param    int       $form_id      Form ID
     * @param    array     $mapping      Field mapping
     * @param    array     $data         Calculator data
     * @return   bool                    Success status
     */
    public function map_to_form( $form_type, $form_id, $mapping, $data ) {
        
        switch ( $form_type ) {
            case 'fluent_forms':
                return $this->map_to_fluent_forms( $form_id, $mapping, $data );
                
            case 'contact_form_7':
                return $this->map_to_cf7( $form_id, $mapping, $data );
                
            case 'wpforms':
                return $this->map_to_wpforms( $form_id, $mapping, $data );
                
            default:
                return false;
        }
    }

    /**
     * Map data to Fluent Forms
     *
     * @since    2.0.0
     * @param    int      $form_id     Form ID
     * @param    array    $mapping     Field mapping
     * @param    array    $data        Calculator data
     * @return   bool                  Success status
     */
    private function map_to_fluent_forms( $form_id, $mapping, $data ) {
        
        if ( ! defined( 'FLUENTFORM' ) ) {
            return false;
        }
        
        // Store mapping for later use when form is submitted
        update_option( 'hcc_fluent_forms_mapping_' . $form_id, array(
            'mapping' => $mapping,
            'enabled' => true,
        ) );
        
        return true;
    }

    /**
     * Map data to Contact Form 7
     *
     * @since    2.0.0
     * @param    int      $form_id     Form ID
     * @param    array    $mapping     Field mapping
     * @param    array    $data        Calculator data
     * @return   bool                  Success status
     */
    private function map_to_cf7( $form_id, $mapping, $data ) {
        
        if ( ! defined( 'WPCF7_VERSION' ) ) {
            return false;
        }
        
        // Store mapping for later use when form is submitted
        update_option( 'hcc_cf7_mapping_' . $form_id, array(
            'mapping' => $mapping,
            'enabled' => true,
        ) );
        
        return true;
    }

    /**
     * Map data to WPForms
     *
     * @since    2.0.0
     * @param    int      $form_id     Form ID
     * @param    array    $mapping     Field mapping
     * @param    array    $data        Calculator data
     * @return   bool                  Success status
     */
    private function map_to_wpforms( $form_id, $mapping, $data ) {
        
        if ( ! function_exists( 'wpforms' ) ) {
            return false;
        }
        
        // Store mapping for later use when form is submitted
        update_option( 'hcc_wpforms_mapping_' . $form_id, array(
            'mapping' => $mapping,
            'enabled' => true,
        ) );
        
        return true;
    }

    /**
     * Get integration statistics
     *
     * @since    2.0.0
     * @return   array    Statistics array
     */
    public function get_statistics() {
        
        $stats = array(
            'telegram_enabled'  => get_option( 'hcc_telegram_enabled', 'no' ) === 'yes',
            'smtp_enabled'      => get_option( 'hcc_smtp_enabled', 'no' ) === 'yes',
            'webhook_enabled'   => get_option( 'hcc_webhook_enabled', 'no' ) === 'yes',
            'form_integrations' => array(),
        );
        
        // Check form integrations
        $form_plugins = $this->get_available_form_plugins();
        $stats['form_integrations'] = array_keys( $form_plugins );
        
        return $stats;
    }

    /**
     * Clear integration cache
     *
     * @since    2.0.0
     */
    public function clear_cache() {
        delete_transient( 'hcc_integration_cache' );
        delete_transient( 'hcc_telegram_cache' );
        delete_transient( 'hcc_smtp_cache' );
    }

    /**
     * Log integration event
     *
     * @since    2.0.0
     * @param    string    $integration    Integration name
     * @param    string    $event          Event type
     * @param    array     $data           Event data
     */
    public function log_event( $integration, $event, $data = array() ) {
        
        HCC_Database::log_activity(
            'integration_' . $event,
            'integration',
            0,
            array(
                'integration' => $integration,
                'event'       => $event,
                'data'        => $data,
            )
        );
    }
}