<?php
/**
 * Discount management functionality
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Discount management functionality.
 *
 * Handles discount rule creation, management, and application logic.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Discounts {

    /**
     * Initialize discount functionality
     *
     * @since    2.0.0
     */
    public function init() {
        // Discount functionality is called as needed
    }

    /**
     * Create a new discount rule
     *
     * @since    2.0.0
     * @param    array    $data    Discount rule data
     * @return   int|bool          Rule ID on success, false on failure
     */
    public function create_rule( $data ) {
        
        // Validate required fields
        if ( empty( $data['rule_name'] ) || empty( $data['discount_type'] ) || empty( $data['discount_value'] ) ) {
            return false;
        }
        
        return HCC_Database::insert_discount_rule( $data );
    }

    /**
     * Update discount rule
     *
     * @since    2.0.0
     * @param    int      $rule_id    Rule ID
     * @param    array    $data       Updated data
     * @return   bool                 True on success
     */
    public function update_rule( $rule_id, $data ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $data['updated_at'] = current_time( 'mysql' );
        
        $result = $wpdb->update(
            $table,
            $data,
            array( 'id' => intval( $rule_id ) )
        );
        
        if ( $result !== false ) {
            // Clear cache
            delete_transient( 'hcc_discount_rules_cache' );
            
            // Log activity
            HCC_Database::log_activity( 'discount_updated', 'discount', $rule_id, $data );
            
            return true;
        }
        
        return false;
    }

    /**
     * Delete discount rule
     *
     * @since    2.0.0
     * @param    int    $rule_id    Rule ID
     * @return   bool               True on success
     */
    public function delete_rule( $rule_id ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $result = $wpdb->delete(
            $table,
            array( 'id' => intval( $rule_id ) )
        );
        
        if ( $result ) {
            // Clear cache
            delete_transient( 'hcc_discount_rules_cache' );
            
            // Log activity
            HCC_Database::log_activity( 'discount_deleted', 'discount', $rule_id );
            
            return true;
        }
        
        return false;
    }

    /**
     * Get discount rule by ID
     *
     * @since    2.0.0
     * @param    int    $rule_id    Rule ID
     * @return   object|null        Rule object or null
     */
    public function get_rule( $rule_id ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", intval( $rule_id ) )
        );
    }

    /**
     * Get all discount rules
     *
     * @since    2.0.0
     * @param    array    $args    Query arguments
     * @return   array             Array of discount rules
     */
    public function get_rules( $args = array() ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $defaults = array(
            'active_only' => false,
            'orderby'     => 'priority',
            'order'       => 'DESC',
            'limit'       => 100,
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where = '1=1';
        
        if ( $args['active_only'] ) {
            $where .= ' AND active = 1';
        }
        
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
        $limit = intval( $args['limit'] );
        
        $sql = "SELECT * FROM $table WHERE $where ORDER BY $orderby LIMIT $limit";
        
        return $wpdb->get_results( $sql );
    }

    /**
     * Toggle discount rule active status
     *
     * @since    2.0.0
     * @param    int    $rule_id    Rule ID
     * @return   bool               True on success
     */
    public function toggle_active( $rule_id ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $rule = $this->get_rule( $rule_id );
        
        if ( ! $rule ) {
            return false;
        }
        
        $new_status = $rule->active ? 0 : 1;
        
        $result = $wpdb->update(
            $table,
            array( 'active' => $new_status ),
            array( 'id' => intval( $rule_id ) )
        );
        
        if ( $result !== false ) {
            delete_transient( 'hcc_discount_rules_cache' );
            return true;
        }
        
        return false;
    }

    /**
     * Get applicable discounts for given conditions
     *
     * @since    2.0.0
     * @param    array    $conditions    Conditions (rooms, area, date, etc.)
     * @return   array                   Array of applicable discount rules
     */
    public function get_applicable_discounts( $conditions ) {
        
        $all_rules = HCC_Database::get_active_discounts();
        
        if ( empty( $all_rules ) ) {
            return array();
        }
        
        $applicable = array();
        
        foreach ( $all_rules as $rule ) {
            if ( $this->check_rule_conditions( $rule, $conditions ) ) {
                $applicable[] = $rule;
            }
        }
        
        return $applicable;
    }

    /**
     * Check if discount rule conditions are met
     *
     * @since    2.0.0
     * @param    object    $rule         Discount rule
     * @param    array     $conditions   Current conditions
     * @return   bool                    True if conditions met
     */
    private function check_rule_conditions( $rule, $conditions ) {
        
        // Check date range
        if ( ! empty( $rule->date_start ) && ! empty( $rule->date_end ) ) {
            $now = current_time( 'timestamp' );
            $start = strtotime( $rule->date_start );
            $end = strtotime( $rule->date_end );
            
            if ( $now < $start || $now > $end ) {
                return false;
            }
        }
        
        // Check day of week
        if ( ! empty( $rule->days_of_week ) ) {
            $current_day = strtolower( date( 'l' ) );
            $allowed_days = array_map( 'strtolower', json_decode( $rule->days_of_week, true ) );
            
            if ( ! in_array( $current_day, $allowed_days ) ) {
                return false;
            }
        }
        
        // Check custom conditions
        if ( ! empty( $rule->conditions ) ) {
            $rule_conditions = json_decode( $rule->conditions, true );
            
            // Minimum area
            if ( isset( $rule_conditions['min_area'] ) && isset( $conditions['total_area'] ) ) {
                if ( $conditions['total_area'] < $rule_conditions['min_area'] ) {
                    return false;
                }
            }
            
            // Minimum rooms
            if ( isset( $rule_conditions['min_rooms'] ) && isset( $conditions['room_count'] ) ) {
                if ( $conditions['room_count'] < $rule_conditions['min_rooms'] ) {
                    return false;
                }
            }
            
            // Minimum subtotal
            if ( isset( $rule_conditions['min_subtotal'] ) && isset( $conditions['subtotal'] ) ) {
                if ( $conditions['subtotal'] < $rule_conditions['min_subtotal'] ) {
                    return false;
                }
            }
            
            // Specific room types
            if ( isset( $rule_conditions['room_types'] ) && ! empty( $rule_conditions['room_types'] ) ) {
                if ( ! isset( $conditions['room_types'] ) ) {
                    return false;
                }
                
                $has_required_type = false;
                foreach ( $rule_conditions['room_types'] as $required_type ) {
                    if ( in_array( $required_type, $conditions['room_types'] ) ) {
                        $has_required_type = true;
                        break;
                    }
                }
                
                if ( ! $has_required_type ) {
                    return false;
                }
            }
        }
        
        // Check usage limit
        if ( ! empty( $rule->usage_limit ) && $rule->usage_count >= $rule->usage_limit ) {
            return false;
        }
        
        return true;
    }

    /**
     * Calculate discount amount
     *
     * @since    2.0.0
     * @param    object    $rule        Discount rule
     * @param    float     $subtotal    Subtotal amount
     * @return   float                  Discount amount
     */
    public function calculate_discount_amount( $rule, $subtotal ) {
        
        $discount_amount = 0;
        
        if ( $rule->discount_type === 'percentage' ) {
            $discount_amount = ( $subtotal * $rule->discount_value ) / 100;
        } elseif ( $rule->discount_type === 'fixed' ) {
            $discount_amount = $rule->discount_value;
        }
        
        // Ensure discount doesn't exceed subtotal
        if ( $discount_amount > $subtotal ) {
            $discount_amount = $subtotal;
        }
        
        return $discount_amount;
    }

    /**
     * Get discount statistics
     *
     * @since    2.0.0
     * @return   array    Statistics array
     */
    public function get_statistics() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $stats = array(
            'total_rules'   => $wpdb->get_var( "SELECT COUNT(*) FROM $table" ),
            'active_rules'  => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE active = %d", 1 ) ),
            'inactive_rules' => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE active = %d", 0 ) ),
            'total_usage'   => $wpdb->get_var( "SELECT SUM(usage_count) FROM $table" ),
        );
        
        return $stats;
    }

    /**
     * Duplicate discount rule
     *
     * @since    2.0.0
     * @param    int    $rule_id    Rule ID to duplicate
     * @return   int|bool           New rule ID or false
     */
    public function duplicate_rule( $rule_id ) {
        
        $rule = $this->get_rule( $rule_id );
        
        if ( ! $rule ) {
            return false;
        }
        
        $new_data = array(
            'rule_name'       => $rule->rule_name . ' (Copy)',
            'description'     => $rule->description,
            'discount_type'   => $rule->discount_type,
            'discount_value'  => $rule->discount_value,
            'conditions'      => $rule->conditions,
            'date_start'      => $rule->date_start,
            'date_end'        => $rule->date_end,
            'days_of_week'    => $rule->days_of_week,
            'priority'        => $rule->priority,
            'stackable'       => $rule->stackable,
            'discount_code'   => '', // Don't copy discount code
            'usage_limit'     => $rule->usage_limit,
            'active'          => 0, // Set as inactive by default
        );
        
        return $this->create_rule( $new_data );
    }

    /**
     * Bulk delete discount rules
     *
     * @since    2.0.0
     * @param    array    $rule_ids    Array of rule IDs
     * @return   int                   Number of deleted rules
     */
    public function bulk_delete( $rule_ids ) {
        
        if ( empty( $rule_ids ) ) {
            return 0;
        }
        
        $deleted = 0;
        
        foreach ( $rule_ids as $rule_id ) {
            if ( $this->delete_rule( intval( $rule_id ) ) ) {
                $deleted++;
            }
        }
        
        return $deleted;
    }

    /**
     * Bulk activate/deactivate rules
     *
     * @since    2.0.0
     * @param    array    $rule_ids    Array of rule IDs
     * @param    bool     $activate    True to activate, false to deactivate
     * @return   int                   Number of updated rules
     */
    public function bulk_toggle_active( $rule_ids, $activate ) {
        
        if ( empty( $rule_ids ) ) {
            return 0;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $updated = 0;
        $new_status = $activate ? 1 : 0;
        
        foreach ( $rule_ids as $rule_id ) {
            $result = $wpdb->update(
                $table,
                array( 'active' => $new_status ),
                array( 'id' => intval( $rule_id ) )
            );
            
            if ( $result !== false ) {
                $updated++;
            }
        }
        
        if ( $updated > 0 ) {
            delete_transient( 'hcc_discount_rules_cache' );
        }
        
        return $updated;
    }

    /**
     * Export discount rules to JSON
     *
     * @since    2.0.0
     * @return   string|false    JSON string or false
     */
    public function export_rules() {
        
        $rules = $this->get_rules();
        
        if ( empty( $rules ) ) {
            return false;
        }
        
        return json_encode( $rules, JSON_PRETTY_PRINT );
    }

    /**
     * Import discount rules from JSON
     *
     * @since    2.0.0
     * @param    string    $json    JSON string
     * @return   array              Result array
     */
    public function import_rules( $json ) {
        
        $rules = json_decode( $json, true );
        
        if ( empty( $rules ) || ! is_array( $rules ) ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid JSON data', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        $imported = 0;
        
        foreach ( $rules as $rule ) {
            // Remove ID to create new rule
            unset( $rule['id'] );
            unset( $rule['created_at'] );
            unset( $rule['updated_at'] );
            
            // Reset usage count
            $rule['usage_count'] = 0;
            
            if ( $this->create_rule( $rule ) ) {
                $imported++;
            }
        }
        
        return array(
            'success' => true,
            'message' => sprintf(
                __( 'Successfully imported %d discount rules', 'hotel-cleaning-calculator-pro' ),
                $imported
            ),
            'count'   => $imported,
        );
    }
}