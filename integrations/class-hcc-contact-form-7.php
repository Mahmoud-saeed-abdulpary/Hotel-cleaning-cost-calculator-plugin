<?php
/**
 * Contact Form 7 Integration
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/integrations
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Contact Form 7 Integration Class
 *
 * Integrates calculator with Contact Form 7
 *
 * @since 2.0.0
 */
class HCC_Contact_Form_7_Integration {

    /**
     * Initialize integration
     *
     * @since 2.0.0
     */
    public function init() {
        
        if ( ! defined( 'WPCF7_VERSION' ) ) {
            return;
        }

        // Add custom form tags
        add_action( 'wpcf7_init', array( $this, 'add_form_tags' ) );
        
        // Add calculator data to mail
        add_filter( 'wpcf7_mail_tag_replaced', array( $this, 'replace_calculator_tags' ), 10, 4 );
        
        // Add calculator fields to form submission
        add_action( 'wpcf7_before_send_mail', array( $this, 'attach_calculator_data' ) );
        
        // Add admin menu for field mapping
        add_action( 'admin_menu', array( $this, 'add_mapping_submenu' ), 20 );
    }

    /**
     * Add custom Contact Form 7 tags
     *
     * @since 2.0.0
     */
    public function add_form_tags() {
        
        // Register calculator hidden fields
        wpcf7_add_form_tag(
            'hcc_total',
            array( $this, 'calculator_total_tag' ),
            array( 'name-attr' => true )
        );

        wpcf7_add_form_tag(
            'hcc_rooms',
            array( $this, 'calculator_rooms_tag' ),
            array( 'name-attr' => true )
        );

        wpcf7_add_form_tag(
            'hcc_area',
            array( $this, 'calculator_area_tag' ),
            array( 'name-attr' => true )
        );
    }

    /**
     * Calculator total tag handler
     *
     * @since  2.0.0
     * @param  object  $tag  CF7 tag object
     * @return string        HTML output
     */
    public function calculator_total_tag( $tag ) {
        return '<input type="hidden" name="hcc_total" class="hcc-cf7-total" value="" />';
    }

    /**
     * Calculator rooms tag handler
     *
     * @since  2.0.0
     * @param  object  $tag  CF7 tag object
     * @return string        HTML output
     */
    public function calculator_rooms_tag( $tag ) {
        return '<input type="hidden" name="hcc_rooms" class="hcc-cf7-rooms" value="" />';
    }

    /**
     * Calculator area tag handler
     *
     * @since  2.0.0
     * @param  object  $tag  CF7 tag object
     * @return string        HTML output
     */
    public function calculator_area_tag( $tag ) {
        return '<input type="hidden" name="hcc_area" class="hcc-cf7-area" value="" />';
    }

    /**
     * Replace calculator mail tags
     *
     * @since  2.0.0
     * @param  string  $replaced  Replaced value
     * @param  string  $submitted Submitted value
     * @param  bool    $html      HTML mode
     * @param  object  $mail_tag  Mail tag object
     * @return string             Replaced value
     */
    public function replace_calculator_tags( $replaced, $submitted, $html, $mail_tag ) {
        
        $tag_name = $mail_tag->field_name();
        
        if ( strpos( $tag_name, 'hcc_' ) !== 0 ) {
            return $replaced;
        }

        $calculator = new HCC_Calculator();
        
        switch ( $tag_name ) {
            case 'hcc_total':
                if ( ! empty( $submitted ) ) {
                    $replaced = $calculator->format_price( floatval( $submitted ) );
                }
                break;
                
            case 'hcc_rooms':
                if ( ! empty( $submitted ) ) {
                    $rooms = json_decode( stripslashes( $submitted ), true );
                    if ( $html ) {
                        $replaced = $this->format_rooms_html( $rooms );
                    } else {
                        $replaced = $this->format_rooms_text( $rooms );
                    }
                }
                break;
                
            case 'hcc_area':
                if ( ! empty( $submitted ) ) {
                    $replaced = $submitted . ' m²';
                }
                break;
        }
        
        return $replaced;
    }

    /**
     * Format rooms data as HTML
     *
     * @since  2.0.0
     * @param  array   $rooms  Rooms data
     * @return string          Formatted HTML
     */
    private function format_rooms_html( $rooms ) {
        
        if ( empty( $rooms ) || ! is_array( $rooms ) ) {
            return '';
        }

        $calculator = new HCC_Calculator();
        $html = '<table style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead><tr style="background: #f3f4f6;">';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #e5e7eb;">Room Type</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #e5e7eb;">Area (m²)</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #e5e7eb;">Price/m²</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #e5e7eb;">Subtotal</th>';
        $html .= '</tr></thead><tbody>';
        
        foreach ( $rooms as $room ) {
            $html .= '<tr>';
            $html .= '<td style="padding: 8px; border: 1px solid #e5e7eb;">' . esc_html( $room['type_name'] ?? 'Room' ) . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #e5e7eb;">' . esc_html( $room['area'] ?? 0 ) . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #e5e7eb;">' . esc_html( $calculator->format_price( $room['price_per_m2'] ?? 0 ) ) . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>' . esc_html( $calculator->format_price( $room['subtotal'] ?? 0 ) ) . '</strong></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        
        return $html;
    }

    /**
     * Format rooms data as plain text
     *
     * @since  2.0.0
     * @param  array   $rooms  Rooms data
     * @return string          Formatted text
     */
    private function format_rooms_text( $rooms ) {
        
        if ( empty( $rooms ) || ! is_array( $rooms ) ) {
            return '';
        }

        $calculator = new HCC_Calculator();
        $text = "\nROOM DETAILS:\n";
        $text .= str_repeat( '-', 60 ) . "\n";
        
        foreach ( $rooms as $index => $room ) {
            $num = $index + 1;
            $text .= "{$num}. {$room['type_name']}: {$room['area']} m² @ {$calculator->format_price($room['price_per_m2'])}/m² = {$calculator->format_price($room['subtotal'])}\n";
        }
        
        $text .= str_repeat( '-', 60 ) . "\n";
        
        return $text;
    }

    /**
     * Attach calculator data to form submission
     *
     * @since 2.0.0
     * @param  object  $contact_form  CF7 contact form object
     */
    public function attach_calculator_data( $contact_form ) {
        
        $submission = WPCF7_Submission::get_instance();
        
        if ( ! $submission ) {
            return;
        }

        $posted_data = $submission->get_posted_data();
        
        // Check if calculator data exists
        if ( empty( $posted_data['hcc_total'] ) ) {
            return;
        }

        // Store calculator data in form meta
        $contact_form->set_properties( array(
            'additional_settings' => $contact_form->prop( 'additional_settings' ) . "\nhcc_calculator_data: yes"
        ) );

        // Log submission if enabled
        if ( get_option( 'hcc_enable_activity_log', 'yes' ) === 'yes' ) {
            HCC_Database::log_activity( 'cf7_submission', 'integration', $contact_form->id(), array(
                'form_id'   => $contact_form->id(),
                'form_name' => $contact_form->title(),
                'total'     => $posted_data['hcc_total'] ?? '',
            ) );
        }
    }

    /**
     * Add mapping submenu
     *
     * @since 2.0.0
     */
    public function add_mapping_submenu() {
        add_submenu_page(
            null, // Hidden menu
            __( 'CF7 Calculator Mapping', 'hotel-cleaning-calculator-pro' ),
            __( 'CF7 Mapping', 'hotel-cleaning-calculator-pro' ),
            'manage_options',
            'hcc-cf7-mapping',
            array( $this, 'render_mapping_page' )
        );
    }

    /**
     * Render mapping page
     *
     * @since 2.0.0
     */
    public function render_mapping_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Contact Form 7 - Calculator Integration', 'hotel-cleaning-calculator-pro' ); ?></h1>
            
            <div class="hcc-card">
                <div class="hcc-card-body">
                    <h2><?php _e( 'How to Use Calculator with Contact Form 7', 'hotel-cleaning-calculator-pro' ); ?></h2>
                    
                    <h3><?php _e( '1. Add Calculator to Your Page', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    <p><?php _e( 'Place the calculator shortcode above your Contact Form 7 shortcode:', 'hotel-cleaning-calculator-pro' ); ?></p>
                    <pre style="background: #f3f4f6; padding: 15px; border-radius: 6px;">[hotel_cleaning_calculator]
[contact-form-7 id="123" title="Quote Form"]</pre>
                    
                    <h3><?php _e( '2. Add Hidden Fields to Your CF7 Form', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    <p><?php _e( 'Add these custom tags to your Contact Form 7 form editor:', 'hotel-cleaning-calculator-pro' ); ?></p>
                    <pre style="background: #f3f4f6; padding: 15px; border-radius: 6px;">[hcc_total]
[hcc_rooms]
[hcc_area]</pre>
                    
                    <h3><?php _e( '3. Use in Email Templates', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    <p><?php _e( 'In your CF7 mail template, use these tags:', 'hotel-cleaning-calculator-pro' ); ?></p>
                    <pre style="background: #f3f4f6; padding: 15px; border-radius: 6px;">Total Price: [hcc_total]
Room Details: [hcc_rooms]
Total Area: [hcc_area]</pre>
                    
                    <h3><?php _e( '4. Add JavaScript to Connect Calculator', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    <p><?php _e( 'Add this code to your theme or use a code snippet plugin:', 'hotel-cleaning-calculator-pro' ); ?></p>
                    <pre style="background: #282c34; color: #abb2bf; padding: 15px; border-radius: 6px; overflow-x: auto;"><code>jQuery(document).ready(function($) {
    $('.hcc-calculate-btn').on('click', function() {
        setTimeout(function() {
            var calcData = $('.hcc-calculator-wrapper').data('calculation');
            if (calcData) {
                $('.hcc-cf7-total').val(calcData.total_price_raw);
                $('.hcc-cf7-rooms').val(JSON.stringify(calcData.rooms));
                $('.hcc-cf7-area').val(calcData.total_area);
            }
        }, 500);
    });
});</code></pre>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get available CF7 forms
     *
     * @since  2.0.0
     * @return array  Array of forms
     */
    public function get_forms() {
        
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
}