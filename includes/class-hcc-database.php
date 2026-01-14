<?php
/**
 * Database operations handler
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Database operations handler.
 *
 * Handles all custom database table operations for quotes, discounts, and activity logs.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Database {

    /**
     * Initialize the class
     *
     * @since    2.0.0
     */
    public function init() {
        // Database methods are called statically as needed
    }

    /**
     * Insert a new quote
     *
     * @since    2.0.0
     * @param    array    $data    Quote data
     * @return   int|bool          Quote ID on success, false on failure
     */
    public static function insert_quote( $data ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_quotes';
        
        $defaults = array(
            'quote_number'        => self::generate_quote_number(),
            'client_name'         => '',
            'client_email'        => '',
            'client_phone'        => '',
            'client_address'      => '',
            'rooms_data'          => '',
            'total_area'          => 0.00,
            'subtotal'            => 0.00,
            'discount_amount'     => 0.00,
            'total_price'         => 0.00,
            'applied_discounts'   => '',
            'custom_fields'       => '',
            'additional_services' => '',
            'status'              => 'pending',
            'admin_notes'         => '',
            'ip_address'          => self::get_client_ip(),
            'user_agent'          => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
            'created_at'          => current_time( 'mysql' ),
            'updated_at'          => current_time( 'mysql' ),
        );
        
        $data = wp_parse_args( $data, $defaults );
        
        // Sanitize data
        $data['client_name']    = sanitize_text_field( $data['client_name'] );
        $data['client_email']   = sanitize_email( $data['client_email'] );
        $data['client_phone']   = sanitize_text_field( $data['client_phone'] );
        $data['client_address'] = sanitize_textarea_field( $data['client_address'] );
        $data['status']         = sanitize_text_field( $data['status'] );
        $data['admin_notes']    = sanitize_textarea_field( $data['admin_notes'] );
        
        $result = $wpdb->insert( $table, $data );
        
        if ( $result ) {
            $quote_id = $wpdb->insert_id;
            
            // Log activity
            self::log_activity( 'quote_created', 'quote', $quote_id, array(
                'quote_number' => $data['quote_number'],
                'client_email' => $data['client_email'],
            ) );
            
            return $quote_id;
        }
        
        return false;
    }

    /**
     * Update an existing quote
     *
     * @since    2.0.0
     * @param    int      $quote_id    Quote ID
     * @param    array    $data        Data to update
     * @return   bool                  True on success, false on failure
     */
    public static function update_quote( $quote_id, $data ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_quotes';
        
        $data['updated_at'] = current_time( 'mysql' );
        
        // Sanitize data
        if ( isset( $data['client_name'] ) ) {
            $data['client_name'] = sanitize_text_field( $data['client_name'] );
        }
        if ( isset( $data['client_email'] ) ) {
            $data['client_email'] = sanitize_email( $data['client_email'] );
        }
        if ( isset( $data['status'] ) ) {
            $data['status'] = sanitize_text_field( $data['status'] );
        }
        if ( isset( $data['admin_notes'] ) ) {
            $data['admin_notes'] = sanitize_textarea_field( $data['admin_notes'] );
        }
        
        $result = $wpdb->update(
            $table,
            $data,
            array( 'id' => intval( $quote_id ) )
        );
        
        if ( $result !== false ) {
            // Log activity
            self::log_activity( 'quote_updated', 'quote', $quote_id, $data );
            return true;
        }
        
        return false;
    }

    /**
     * Get a quote by ID
     *
     * @since    2.0.0
     * @param    int    $quote_id    Quote ID
     * @return   object|null         Quote object or null
     */
    public static function get_quote( $quote_id ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_quotes';
        
        $quote = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", intval( $quote_id ) )
        );
        
        return $quote;
    }

    /**
     * Get quotes with filtering and pagination
     *
     * @since    2.0.0
     * @param    array    $args    Query arguments
     * @return   array             Array of quotes
     */
    public static function get_quotes( $args = array() ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_quotes';
        
        $defaults = array(
            'status'      => '',
            'search'      => '',
            'date_from'   => '',
            'date_to'     => '',
            'orderby'     => 'created_at',
            'order'       => 'DESC',
            'limit'       => 20,
            'offset'      => 0,
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where = array( '1=1' );
        $prepare_values = array();
        
        // Status filter
        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $prepare_values[] = sanitize_text_field( $args['status'] );
        }
        
        // Search filter
        if ( ! empty( $args['search'] ) ) {
            $where[] = '(client_name LIKE %s OR client_email LIKE %s OR quote_number LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $prepare_values[] = $search_term;
            $prepare_values[] = $search_term;
            $prepare_values[] = $search_term;
        }
        
        // Date range filter
        if ( ! empty( $args['date_from'] ) ) {
            $where[] = 'created_at >= %s';
            $prepare_values[] = sanitize_text_field( $args['date_from'] );
        }
        if ( ! empty( $args['date_to'] ) ) {
            $where[] = 'created_at <= %s';
            $prepare_values[] = sanitize_text_field( $args['date_to'] );
        }
        
        $where_clause = implode( ' AND ', $where );
        
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
        
        $limit = intval( $args['limit'] );
        $offset = intval( $args['offset'] );
        
        $sql = "SELECT * FROM $table WHERE $where_clause ORDER BY $orderby LIMIT $limit OFFSET $offset";
        
        if ( ! empty( $prepare_values ) ) {
            $sql = $wpdb->prepare( $sql, $prepare_values );
        }
        
        return $wpdb->get_results( $sql );
    }

    /**
     * Delete a quote
     *
     * @since    2.0.0
     * @param    int     $quote_id    Quote ID
     * @return   bool                 True on success, false on failure
     */
    public static function delete_quote( $quote_id ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_quotes';
        
        // Get quote data before deletion for logging
        $quote = self::get_quote( $quote_id );
        
        $result = $wpdb->delete(
            $table,
            array( 'id' => intval( $quote_id ) )
        );
        
        if ( $result ) {
            // Log activity
            self::log_activity( 'quote_deleted', 'quote', $quote_id, array(
                'quote_number' => $quote->quote_number ?? '',
            ) );
            return true;
        }
        
        return false;
    }

    /**
     * Generate unique quote number
     *
     * @since    2.0.0
     * @return   string    Quote number
     */
    private static function generate_quote_number() {
        $prefix = get_option( 'hcc_quote_prefix', 'HCC' );
        $counter = get_option( 'hcc_quote_counter', 1000 );
        
        $quote_number = $prefix . '-' . str_pad( $counter, 6, '0', STR_PAD_LEFT );
        
        // Increment counter
        update_option( 'hcc_quote_counter', $counter + 1 );
        
        return $quote_number;
    }

    /**
     * Insert discount rule
     *
     * @since    2.0.0
     * @param    array    $data    Discount rule data
     * @return   int|bool          Rule ID on success, false on failure
     */
    public static function insert_discount_rule( $data ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $defaults = array(
            'rule_name'       => '',
            'description'     => '',
            'discount_type'   => 'percentage',
            'discount_value'  => 0.00,
            'conditions'      => '',
            'date_start'      => null,
            'date_end'        => null,
            'days_of_week'    => '',
            'priority'        => 0,
            'stackable'       => 0,
            'discount_code'   => '',
            'usage_limit'     => null,
            'usage_count'     => 0,
            'active'          => 1,
            'created_at'      => current_time( 'mysql' ),
            'updated_at'      => current_time( 'mysql' ),
        );
        
        $data = wp_parse_args( $data, $defaults );
        
        // Sanitize data
        $data['rule_name']     = sanitize_text_field( $data['rule_name'] );
        $data['description']   = sanitize_textarea_field( $data['description'] );
        $data['discount_type'] = sanitize_text_field( $data['discount_type'] );
        $data['discount_code'] = sanitize_text_field( $data['discount_code'] );
        
        $result = $wpdb->insert( $table, $data );
        
        if ( $result ) {
            $rule_id = $wpdb->insert_id;
            
            // Clear discount cache
            delete_transient( 'hcc_discount_rules_cache' );
            
            // Log activity
            self::log_activity( 'discount_created', 'discount', $rule_id, array(
                'rule_name' => $data['rule_name'],
            ) );
            
            return $rule_id;
        }
        
        return false;
    }

    /**
     * Get all active discount rules
     *
     * @since    2.0.0
     * @return   array    Array of discount rules
     */
    public static function get_active_discounts() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        // Check cache first
        $cache_key = 'hcc_discount_rules_cache';
        $discounts = get_transient( $cache_key );
        
        if ( false === $discounts ) {
            $discounts = $wpdb->get_results(
                "SELECT * FROM $table WHERE active = 1 ORDER BY priority DESC"
            );
            
            // Cache for 1 hour
            set_transient( $cache_key, $discounts, HOUR_IN_SECONDS );
        }
        
        return $discounts;
    }

    /**
     * Log activity
     *
     * @since    2.0.0
     * @param    string    $action_type    Type of action
     * @param    string    $object_type    Type of object
     * @param    int       $object_id      Object ID
     * @param    array     $metadata       Additional metadata
     * @return   bool                      True on success, false on failure
     */
    public static function log_activity( $action_type, $object_type = '', $object_id = 0, $metadata = array() ) {
        
        // Check if activity logging is enabled
        if ( get_option( 'hcc_enable_activity_log', 'yes' ) !== 'yes' ) {
            return false;
        }
        
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_activity_log';
        
        $user = wp_get_current_user();
        
        $data = array(
            'action_type'  => sanitize_text_field( $action_type ),
            'object_type'  => sanitize_text_field( $object_type ),
            'object_id'    => intval( $object_id ),
            'user_id'      => $user->ID,
            'user_name'    => $user->display_name,
            'description'  => self::generate_activity_description( $action_type, $object_type, $metadata ),
            'metadata'     => json_encode( $metadata ),
            'ip_address'   => self::get_client_ip(),
            'user_agent'   => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
            'created_at'   => current_time( 'mysql' ),
        );
        
        return $wpdb->insert( $table, $data ) !== false;
    }

    /**
     * Generate activity description
     *
     * @since    2.0.0
     * @param    string    $action_type    Action type
     * @param    string    $object_type    Object type
     * @param    array     $metadata       Metadata
     * @return   string                    Description
     */
    private static function generate_activity_description( $action_type, $object_type, $metadata ) {
        $descriptions = array(
            'quote_created'    => 'Quote created: ' . ( $metadata['quote_number'] ?? '' ),
            'quote_updated'    => 'Quote updated',
            'quote_deleted'    => 'Quote deleted: ' . ( $metadata['quote_number'] ?? '' ),
            'discount_created' => 'Discount rule created: ' . ( $metadata['rule_name'] ?? '' ),
            'discount_updated' => 'Discount rule updated',
            'discount_deleted' => 'Discount rule deleted',
            'settings_updated' => 'Settings updated',
        );
        
        return $descriptions[ $action_type ] ?? $action_type;
    }

    /**
     * Get client IP address
     *
     * @since    2.0.0
     * @return   string    IP address
     */
    private static function get_client_ip() {
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
     * Get activity logs
     *
     * @since    2.0.0
     * @param    array    $args    Query arguments
     * @return   array             Array of activity logs
     */
    public static function get_activity_logs( $args = array() ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_activity_log';
        
        $defaults = array(
            'action_type' => '',
            'limit'       => 50,
            'offset'      => 0,
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where = '1=1';
        $prepare_values = array();
        
        if ( ! empty( $args['action_type'] ) ) {
            $where .= ' AND action_type = %s';
            $prepare_values[] = sanitize_text_field( $args['action_type'] );
        }
        
        $limit = intval( $args['limit'] );
        $offset = intval( $args['offset'] );
        
        $sql = "SELECT * FROM $table WHERE $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        
        if ( ! empty( $prepare_values ) ) {
            $sql = $wpdb->prepare( $sql, $prepare_values );
        }
        
        return $wpdb->get_results( $sql );
    }
}