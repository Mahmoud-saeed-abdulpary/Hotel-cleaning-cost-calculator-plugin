<?php
/**
 * Core calculator engine
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Core calculator engine.
 *
 * Handles all calculation logic for room pricing, discounts, and totals.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Calculator {

    /**
     * Room types cache
     *
     * @since    2.0.0
     * @access   private
     * @var      array    $room_types    Room types array
     */
    private $room_types;

    /**
     * Initialize the calculator
     *
     * @since    2.0.0
     */
    public function init() {
        $this->load_room_types();
    }

    /**
     * Load room types from database
     *
     * @since    2.0.0
     */
    private function load_room_types() {
        $this->room_types = get_option( 'hcc_room_types', array() );
    }

    /**
     * Calculate total price for multiple rooms
     *
     * @since    2.0.0
     * @param    array    $rooms    Array of room data
     * @return   array              Calculation result
     */
    public function calculate_total( $rooms ) {
        
        if ( empty( $rooms ) || ! is_array( $rooms ) ) {
            return array(
                'success' => false,
                'message' => __( 'No rooms provided', 'hotel-cleaning-calculator-pro' ),
            );
        }

        $this->load_room_types();
        
        $room_calculations = array();
        $subtotal = 0;
        $total_area = 0;
        
        // Calculate each room
        foreach ( $rooms as $index => $room ) {
            $calculation = $this->calculate_room( $room );
            
            if ( $calculation['success'] ) {
                $room_calculations[] = $calculation['data'];
                $subtotal += $calculation['data']['subtotal'];
                $total_area += $calculation['data']['area'];
            }
        }
        
        // Apply discounts
        $discount_data = $this->apply_discounts( $room_calculations, $subtotal, $total_area );
        
        $total_price = $subtotal - $discount_data['total_discount'];
        
        return array(
            'success'           => true,
            'data'              => array(
                'rooms'             => $room_calculations,
                'subtotal'          => $this->format_price( $subtotal ),
                'subtotal_raw'      => $subtotal,
                'total_area'        => $total_area,
                'discount_amount'   => $this->format_price( $discount_data['total_discount'] ),
                'discount_raw'      => $discount_data['total_discount'],
                'applied_discounts' => $discount_data['applied_discounts'],
                'total_price'       => $this->format_price( $total_price ),
                'total_price_raw'   => $total_price,
                'currency'          => $this->get_currency_symbol(),
            ),
        );
    }

    /**
     * Calculate single room price
     *
     * @since    2.0.0
     * @param    array    $room    Room data (type_id, area)
     * @return   array             Calculation result
     */
    public function calculate_room( $room ) {
        
        // Validate required fields
        if ( empty( $room['type_id'] ) || empty( $room['area'] ) ) {
            return array(
                'success' => false,
                'message' => __( 'Room type and area are required', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        $type_id = sanitize_text_field( $room['type_id'] );
        $area = floatval( $room['area'] );
        
        // Validate area
        if ( $area <= 0 ) {
            return array(
                'success' => false,
                'message' => __( 'Area must be greater than 0', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        // Get room type
        $room_type = $this->get_room_type( $type_id );
        
        if ( ! $room_type ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid room type', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        // Check if room type is active
        if ( ! $room_type['active'] ) {
            return array(
                'success' => false,
                'message' => __( 'This room type is not available', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        $price_per_m2 = floatval( $room_type['price_per_m2'] );
        $subtotal = $area * $price_per_m2;
        
        return array(
            'success' => true,
            'data'    => array(
                'type_id'      => $type_id,
                'type_name'    => $room_type['name'],
                'area'         => $area,
                'price_per_m2' => $price_per_m2,
                'subtotal'     => $subtotal,
                'subtotal_formatted' => $this->format_price( $subtotal ),
            ),
        );
    }

    /**
     * Apply discounts to calculation
     *
     * @since    2.0.0
     * @param    array    $rooms         Room calculations
     * @param    float    $subtotal      Subtotal amount
     * @param    float    $total_area    Total area
     * @return   array                   Discount data
     */
    private function apply_discounts( $rooms, $subtotal, $total_area ) {
        
        // Get active discount rules
        $discount_rules = HCC_Database::get_active_discounts();
        
        if ( empty( $discount_rules ) ) {
            return array(
                'total_discount'    => 0,
                'applied_discounts' => array(),
            );
        }
        
        $applied_discounts = array();
        $total_discount = 0;
        $stacking_enabled = get_option( 'hcc_discount_stacking', 'no' ) === 'yes';
        
        foreach ( $discount_rules as $rule ) {
            
            // Check if rule conditions are met
            if ( ! $this->check_discount_conditions( $rule, $rooms, $subtotal, $total_area ) ) {
                continue;
            }
            
            // Calculate discount amount
            $discount_amount = $this->calculate_discount_amount( $rule, $subtotal );
            
            if ( $discount_amount > 0 ) {
                $applied_discounts[] = array(
                    'rule_id'   => $rule->id,
                    'rule_name' => $rule->rule_name,
                    'amount'    => $discount_amount,
                    'formatted' => $this->format_price( $discount_amount ),
                );
                
                $total_discount += $discount_amount;
                
                // If stacking is disabled, apply only the first matching discount
                if ( ! $stacking_enabled ) {
                    break;
                }
            }
        }
        
        return array(
            'total_discount'    => $total_discount,
            'applied_discounts' => $applied_discounts,
        );
    }

    /**
     * Check if discount conditions are met
     *
     * @since    2.0.0
     * @param    object    $rule          Discount rule
     * @param    array     $rooms         Room calculations
     * @param    float     $subtotal      Subtotal amount
     * @param    float     $total_area    Total area
     * @return   bool                     True if conditions met
     */
    private function check_discount_conditions( $rule, $rooms, $subtotal, $total_area ) {
        
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
            $conditions = json_decode( $rule->conditions, true );
            
            // Minimum area condition
            if ( isset( $conditions['min_area'] ) && $total_area < $conditions['min_area'] ) {
                return false;
            }
            
            // Minimum rooms condition
            if ( isset( $conditions['min_rooms'] ) && count( $rooms ) < $conditions['min_rooms'] ) {
                return false;
            }
            
            // Minimum subtotal condition
            if ( isset( $conditions['min_subtotal'] ) && $subtotal < $conditions['min_subtotal'] ) {
                return false;
            }
            
            // Specific room types condition
            if ( isset( $conditions['room_types'] ) && ! empty( $conditions['room_types'] ) ) {
                $room_type_ids = array_column( $rooms, 'type_id' );
                $required_types = $conditions['room_types'];
                
                $has_required_type = false;
                foreach ( $required_types as $required_type ) {
                    if ( in_array( $required_type, $room_type_ids ) ) {
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
    private function calculate_discount_amount( $rule, $subtotal ) {
        
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
     * Get room type by ID
     *
     * @since    2.0.0
     * @param    string    $type_id    Room type ID
     * @return   array|bool            Room type data or false
     */
    public function get_room_type( $type_id ) {
        
        if ( empty( $this->room_types ) ) {
            $this->load_room_types();
        }
        
        foreach ( $this->room_types as $room_type ) {
            if ( $room_type['id'] === $type_id ) {
                return $room_type;
            }
        }
        
        return false;
    }

    /**
     * Get all active room types
     *
     * @since    2.0.0
     * @return   array    Array of active room types
     */
    public function get_active_room_types() {
        
        if ( empty( $this->room_types ) ) {
            $this->load_room_types();
        }
        
        return array_filter( $this->room_types, function( $room_type ) {
            return $room_type['active'] === true;
        } );
    }

    /**
     * Format price with currency
     *
     * @since    2.0.0
     * @param    float    $price    Price value
     * @return   string             Formatted price
     */
    public function format_price( $price ) {
        
        $decimal_places = intval( get_option( 'hcc_decimal_places', 2 ) );
        $decimal_sep = get_option( 'hcc_decimal_separator', '.' );
        $thousand_sep = get_option( 'hcc_thousand_separator', ',' );
        
        $formatted = number_format( $price, $decimal_places, $decimal_sep, $thousand_sep );
        
        $currency = $this->get_currency_symbol();
        $position = get_option( 'hcc_currency_position', 'before' );
        
        if ( $position === 'before' ) {
            return $currency . $formatted;
        } else {
            return $formatted . $currency;
        }
    }

    /**
     * Get currency symbol
     *
     * @since    2.0.0
     * @return   string    Currency symbol
     */
    public function get_currency_symbol() {
        return get_option( 'hcc_currency_symbol', '$' );
    }

    /**
     * Validate discount code
     *
     * @since    2.0.0
     * @param    string    $code    Discount code
     * @return   object|bool        Discount rule or false
     */
    public function validate_discount_code( $code ) {
        
        if ( empty( $code ) ) {
            return false;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $rule = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE discount_code = %s AND active = 1",
                sanitize_text_field( $code )
            )
        );
        
        if ( ! $rule ) {
            return false;
        }
        
        // Check usage limit
        if ( ! empty( $rule->usage_limit ) && $rule->usage_count >= $rule->usage_limit ) {
            return false;
        }
        
        return $rule;
    }

    /**
     * Increment discount usage count
     *
     * @since    2.0.0
     * @param    int    $rule_id    Discount rule ID
     */
    public function increment_discount_usage( $rule_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'hcc_discount_rules';
        
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table SET usage_count = usage_count + 1 WHERE id = %d",
                intval( $rule_id )
            )
        );
    }

    /**
     * Get calculation summary for display
     *
     * @since    2.0.0
     * @param    array    $calculation_data    Calculation result
     * @return   string                        HTML summary
     */
    public function get_calculation_summary( $calculation_data ) {
        
        if ( ! $calculation_data['success'] ) {
            return '<p class="hcc-error">' . esc_html( $calculation_data['message'] ) . '</p>';
        }
        
        $data = $calculation_data['data'];
        
        ob_start();
        ?>
        <div class="hcc-calculation-summary">
            <div class="hcc-summary-row">
                <span class="hcc-summary-label"><?php echo esc_html( get_option( 'hcc_text_subtotal', 'Subtotal' ) ); ?>:</span>
                <span class="hcc-summary-value"><?php echo esc_html( $data['subtotal'] ); ?></span>
            </div>
            
            <?php if ( $data['discount_raw'] > 0 ) : ?>
                <div class="hcc-summary-row hcc-discount-row">
                    <span class="hcc-summary-label"><?php echo esc_html( get_option( 'hcc_text_discount_applied', 'Discount' ) ); ?>:</span>
                    <span class="hcc-summary-value">-<?php echo esc_html( $data['discount_amount'] ); ?></span>
                </div>
                
                <?php foreach ( $data['applied_discounts'] as $discount ) : ?>
                    <div class="hcc-discount-detail">
                        <small><?php echo esc_html( $discount['rule_name'] ); ?>: -<?php echo esc_html( $discount['formatted'] ); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="hcc-summary-row hcc-total-row">
                <span class="hcc-summary-label"><strong><?php echo esc_html( get_option( 'hcc_text_total', 'Total' ) ); ?>:</strong></span>
                <span class="hcc-summary-value"><strong><?php echo esc_html( $data['total_price'] ); ?></strong></span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}