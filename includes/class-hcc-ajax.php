<?php
/**
 * AJAX request handler
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * AJAX request handler.
 *
 * Handles all AJAX requests for calculations, quote submissions, and admin operations.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Ajax {

    /**
     * Initialize AJAX handlers
     *
     * @since    2.0.0
     */
    public function init() {
        
        // Public AJAX actions (logged in + non-logged in)
        add_action( 'wp_ajax_hcc_calculate_price', array( $this, 'calculate_price' ) );
        add_action( 'wp_ajax_nopriv_hcc_calculate_price', array( $this, 'calculate_price' ) );
        
        add_action( 'wp_ajax_hcc_submit_quote', array( $this, 'submit_quote' ) );
        add_action( 'wp_ajax_nopriv_hcc_submit_quote', array( $this, 'submit_quote' ) );
        
        add_action( 'wp_ajax_hcc_validate_discount_code', array( $this, 'validate_discount_code' ) );
        add_action( 'wp_ajax_nopriv_hcc_validate_discount_code', array( $this, 'validate_discount_code' ) );
        
        // Admin AJAX actions (logged in only)
        add_action( 'wp_ajax_hcc_save_room_types', array( $this, 'save_room_types' ) );
        add_action( 'wp_ajax_hcc_delete_room_type', array( $this, 'delete_room_type' ) );
        
        add_action( 'wp_ajax_hcc_save_discount_rule', array( $this, 'save_discount_rule' ) );
        add_action( 'wp_ajax_hcc_delete_discount_rule', array( $this, 'delete_discount_rule' ) );
        
        add_action( 'wp_ajax_hcc_update_quote_status', array( $this, 'update_quote_status' ) );
        add_action( 'wp_ajax_hcc_delete_quote', array( $this, 'delete_quote' ) );
        
        add_action( 'wp_ajax_hcc_save_customization', array( $this, 'save_customization' ) );
        add_action( 'wp_ajax_hcc_save_translations', array( $this, 'save_translations' ) );
        add_action( 'wp_ajax_hcc_upload_logo', array( $this, 'upload_logo' ) );
    }

    /**
     * Calculate price via AJAX
     *
     * @since    2.0.0
     */
    public function calculate_price() {
        
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_public_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Get rooms data
        $rooms = isset( $_POST['rooms'] ) ? json_decode( stripslashes( $_POST['rooms'] ), true ) : array();
        
        if ( empty( $rooms ) ) {
            wp_send_json_error( array(
                'message' => __( 'No rooms provided', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Get discount code if provided
        $discount_code = isset( $_POST['discount_code'] ) ? sanitize_text_field( $_POST['discount_code'] ) : '';
        
        // Calculate
        $calculator = new HCC_Calculator();
        $result = $calculator->calculate_total( $rooms );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result['data'] );
        } else {
            wp_send_json_error( array(
                'message' => $result['message'],
            ) );
        }
    }

    /**
     * Submit quote via AJAX
     *
     * @since    2.0.0
     */
    public function submit_quote() {
        
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_public_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Sanitize and validate input
        $client_name = isset( $_POST['client_name'] ) ? sanitize_text_field( $_POST['client_name'] ) : '';
        $client_email = isset( $_POST['client_email'] ) ? sanitize_email( $_POST['client_email'] ) : '';
        $client_phone = isset( $_POST['client_phone'] ) ? sanitize_text_field( $_POST['client_phone'] ) : '';
        $client_address = isset( $_POST['client_address'] ) ? sanitize_textarea_field( $_POST['client_address'] ) : '';
        $rooms_data = isset( $_POST['rooms_data'] ) ? sanitize_textarea_field( $_POST['rooms_data'] ) : '';
        $calculation_data = isset( $_POST['calculation_data'] ) ? sanitize_textarea_field( $_POST['calculation_data'] ) : '';
        
        // Validate required fields
        if ( empty( $client_name ) || empty( $client_email ) ) {
            wp_send_json_error( array(
                'message' => __( 'Name and email are required', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Validate email
        if ( ! is_email( $client_email ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid email address', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Parse calculation data
        $calc_data = json_decode( stripslashes( $calculation_data ), true );
        
        // Prepare quote data
        $quote_data = array(
            'client_name'       => $client_name,
            'client_email'      => $client_email,
            'client_phone'      => $client_phone,
            'client_address'    => $client_address,
            'rooms_data'        => $rooms_data,
            'total_area'        => isset( $calc_data['total_area'] ) ? floatval( $calc_data['total_area'] ) : 0,
            'subtotal'          => isset( $calc_data['subtotal_raw'] ) ? floatval( $calc_data['subtotal_raw'] ) : 0,
            'discount_amount'   => isset( $calc_data['discount_raw'] ) ? floatval( $calc_data['discount_raw'] ) : 0,
            'total_price'       => isset( $calc_data['total_price_raw'] ) ? floatval( $calc_data['total_price_raw'] ) : 0,
            'applied_discounts' => isset( $calc_data['applied_discounts'] ) ? json_encode( $calc_data['applied_discounts'] ) : '',
        );
        
        // Insert quote
        $quotes = new HCC_Quotes();
        $result = $quotes->submit_quote( $quote_data );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * Validate discount code via AJAX
     *
     * @since    2.0.0
     */
    public function validate_discount_code() {
        
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_public_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $code = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';
        
        if ( empty( $code ) ) {
            wp_send_json_error( array(
                'message' => __( 'Please enter a discount code', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $calculator = new HCC_Calculator();
        $rule = $calculator->validate_discount_code( $code );
        
        if ( $rule ) {
            wp_send_json_success( array(
                'message'   => __( 'Discount code applied!', 'hotel-cleaning-calculator-pro' ),
                'rule_id'   => $rule->id,
                'rule_name' => $rule->rule_name,
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Invalid or expired discount code', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
    }

    /**
     * Save room types via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function save_room_types() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Get room types data
        $room_types = isset( $_POST['room_types'] ) ? json_decode( stripslashes( $_POST['room_types'] ), true ) : array();
        
        if ( empty( $room_types ) ) {
            wp_send_json_error( array(
                'message' => __( 'No room types data provided', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Sanitize room types
        $sanitized_room_types = array();
        foreach ( $room_types as $room_type ) {
            $sanitized_room_types[] = array(
                'id'           => sanitize_text_field( $room_type['id'] ),
                'name'         => sanitize_text_field( $room_type['name'] ),
                'description'  => sanitize_textarea_field( $room_type['description'] ),
                'price_per_m2' => floatval( $room_type['price_per_m2'] ),
                'active'       => isset( $room_type['active'] ) ? (bool) $room_type['active'] : true,
                'icon'         => sanitize_text_field( $room_type['icon'] ),
                'order'        => intval( $room_type['order'] ),
            );
        }
        
        // Save to database
        update_option( 'hcc_room_types', $sanitized_room_types );
        
        // Clear cache
        delete_transient( 'hcc_room_types_cache' );
        
        // Log activity
        HCC_Database::log_activity( 'settings_updated', 'settings', 0, array(
            'setting' => 'room_types',
        ) );
        
        wp_send_json_success( array(
            'message' => __( 'Room types saved successfully', 'hotel-cleaning-calculator-pro' ),
        ) );
    }

    /**
     * Delete room type via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function delete_room_type() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $room_type_id = isset( $_POST['room_type_id'] ) ? sanitize_text_field( $_POST['room_type_id'] ) : '';
        
        if ( empty( $room_type_id ) ) {
            wp_send_json_error( array(
                'message' => __( 'Room type ID required', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Get current room types
        $room_types = get_option( 'hcc_room_types', array() );
        
        // Remove the room type
        $room_types = array_filter( $room_types, function( $rt ) use ( $room_type_id ) {
            return $rt['id'] !== $room_type_id;
        } );
        
        // Reindex array
        $room_types = array_values( $room_types );
        
        // Save
        update_option( 'hcc_room_types', $room_types );
        
        wp_send_json_success( array(
            'message' => __( 'Room type deleted successfully', 'hotel-cleaning-calculator-pro' ),
        ) );
    }

    /**
     * Save discount rule via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function save_discount_rule() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Get discount data
        $discount_data = isset( $_POST['discount_data'] ) ? json_decode( stripslashes( $_POST['discount_data'] ), true ) : array();
        
        if ( empty( $discount_data ) ) {
            wp_send_json_error( array(
                'message' => __( 'No discount data provided', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Sanitize data
        $sanitized_data = array(
            'rule_name'       => sanitize_text_field( $discount_data['rule_name'] ),
            'description'     => sanitize_textarea_field( $discount_data['description'] ),
            'discount_type'   => sanitize_text_field( $discount_data['discount_type'] ),
            'discount_value'  => floatval( $discount_data['discount_value'] ),
            'conditions'      => isset( $discount_data['conditions'] ) ? json_encode( $discount_data['conditions'] ) : '',
            'date_start'      => isset( $discount_data['date_start'] ) ? sanitize_text_field( $discount_data['date_start'] ) : null,
            'date_end'        => isset( $discount_data['date_end'] ) ? sanitize_text_field( $discount_data['date_end'] ) : null,
            'days_of_week'    => isset( $discount_data['days_of_week'] ) ? json_encode( $discount_data['days_of_week'] ) : '',
            'priority'        => intval( $discount_data['priority'] ),
            'stackable'       => isset( $discount_data['stackable'] ) ? 1 : 0,
            'discount_code'   => sanitize_text_field( $discount_data['discount_code'] ),
            'usage_limit'     => isset( $discount_data['usage_limit'] ) ? intval( $discount_data['usage_limit'] ) : null,
            'active'          => isset( $discount_data['active'] ) ? 1 : 0,
        );
        
        // Insert or update
        if ( isset( $discount_data['id'] ) && ! empty( $discount_data['id'] ) ) {
            // Update existing
            global $wpdb;
            $table = $wpdb->prefix . 'hcc_discount_rules';
            $wpdb->update( $table, $sanitized_data, array( 'id' => intval( $discount_data['id'] ) ) );
            $message = __( 'Discount rule updated successfully', 'hotel-cleaning-calculator-pro' );
        } else {
            // Insert new
            HCC_Database::insert_discount_rule( $sanitized_data );
            $message = __( 'Discount rule created successfully', 'hotel-cleaning-calculator-pro' );
        }
        
        wp_send_json_success( array(
            'message' => $message,
        ) );
    }

    /**
     * Delete discount rule via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function delete_discount_rule() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $rule_id = isset( $_POST['rule_id'] ) ? intval( $_POST['rule_id'] ) : 0;
        
        if ( empty( $rule_id ) ) {
            wp_send_json_error( array(
                'message' => __( 'Rule ID required', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'hcc_discount_rules';
        $wpdb->delete( $table, array( 'id' => $rule_id ) );
        
        // Clear cache
        delete_transient( 'hcc_discount_rules_cache' );
        
        wp_send_json_success( array(
            'message' => __( 'Discount rule deleted successfully', 'hotel-cleaning-calculator-pro' ),
        ) );
    }

    /**
     * Update quote status via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function update_quote_status() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $quote_id = isset( $_POST['quote_id'] ) ? intval( $_POST['quote_id'] ) : 0;
        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
        
        if ( empty( $quote_id ) || empty( $status ) ) {
            wp_send_json_error( array(
                'message' => __( 'Quote ID and status required', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $result = HCC_Database::update_quote( $quote_id, array( 'status' => $status ) );
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( 'Quote status updated successfully', 'hotel-cleaning-calculator-pro' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Failed to update quote status', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
    }

    /**
     * Delete quote via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function delete_quote() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $quote_id = isset( $_POST['quote_id'] ) ? intval( $_POST['quote_id'] ) : 0;
        
        if ( empty( $quote_id ) ) {
            wp_send_json_error( array(
                'message' => __( 'Quote ID required', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        $result = HCC_Database::delete_quote( $quote_id );
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( 'Quote deleted successfully', 'hotel-cleaning-calculator-pro' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Failed to delete quote', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
    }

    /**
     * Save customization settings via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function save_customization() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Get settings data
        $settings = isset( $_POST['settings'] ) ? json_decode( stripslashes( $_POST['settings'] ), true ) : array();
        
        // Save each setting
        foreach ( $settings as $key => $value ) {
            update_option( 'hcc_' . $key, sanitize_text_field( $value ) );
        }
        
        wp_send_json_success( array(
            'message' => __( 'Customization saved successfully', 'hotel-cleaning-calculator-pro' ),
        ) );
    }

    /**
     * Save translations via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function save_translations() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Get translations data
        $translations = isset( $_POST['translations'] ) ? json_decode( stripslashes( $_POST['translations'] ), true ) : array();
        
        // Save each translation
        foreach ( $translations as $key => $value ) {
            update_option( 'hcc_text_' . $key, sanitize_text_field( $value ) );
        }
        
        wp_send_json_success( array(
            'message' => __( 'Translations saved successfully', 'hotel-cleaning-calculator-pro' ),
        ) );
    }

    /**
     * Upload logo via AJAX (Admin)
     *
     * @since    2.0.0
     */
    public function upload_logo() {
        
        // Verify nonce and capabilities
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hcc_admin_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Check if file was uploaded
        if ( empty( $_FILES['logo'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'No file uploaded', 'hotel-cleaning-calculator-pro' ),
            ) );
        }
        
        // Handle file upload
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        
        $upload = wp_handle_upload( $_FILES['logo'], array( 'test_form' => false ) );
        
        if ( isset( $upload['error'] ) ) {
            wp_send_json_error( array(
                'message' => $upload['error'],
            ) );
        }
        
        // Save logo URL
        update_option( 'hcc_logo_url', $upload['url'] );
        
        wp_send_json_success( array(
            'message'  => __( 'Logo uploaded successfully', 'hotel-cleaning-calculator-pro' ),
            'logo_url' => $upload['url'],
        ) );
    }
}