<?php
/**
 * Quote management functionality
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Quote management functionality.
 *
 * Handles quote submission, management, and email notifications.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Quotes {

    /**
     * Initialize the quote management
     *
     * @since    2.0.0
     */
    public function init() {
        // Quote functionality is called as needed
    }

    /**
     * Submit a new quote
     *
     * @since    2.0.0
     * @param    array    $data    Quote data
     * @return   array             Result array
     */
    public function submit_quote( $data ) {
        
        // Validate required fields
        if ( empty( $data['client_name'] ) || empty( $data['client_email'] ) ) {
            return array(
                'success' => false,
                'message' => __( 'Name and email are required', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        // Insert quote into database
        $quote_id = HCC_Database::insert_quote( $data );
        
        if ( ! $quote_id ) {
            return array(
                'success' => false,
                'message' => __( 'Failed to submit quote. Please try again.', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        // Get the inserted quote
        $quote = HCC_Database::get_quote( $quote_id );
        
        // Send notifications
        $this->send_admin_notification( $quote );
        $this->send_client_notification( $quote );
        
        // Send to integrations
        $this->send_to_integrations( $quote );
        
        return array(
            'success'      => true,
            'message'      => get_option( 'hcc_text_success_message', __( 'Thank you! Your quote request has been submitted.', 'hotel-cleaning-calculator-pro' ) ),
            'quote_id'     => $quote_id,
            'quote_number' => $quote->quote_number,
        );
    }

    /**
     * Send admin notification email
     *
     * @since    2.0.0
     * @param    object    $quote    Quote object
     * @return   bool                True on success
     */
    private function send_admin_notification( $quote ) {
        
        $admin_email = get_option( 'hcc_quote_email_admin', get_option( 'admin_email' ) );
        
        if ( empty( $admin_email ) ) {
            return false;
        }
        
        $subject = sprintf(
            __( '[%s] New Cleaning Quote Request #%s', 'hotel-cleaning-calculator-pro' ),
            get_bloginfo( 'name' ),
            $quote->quote_number
        );
        
        // Get email template
        ob_start();
        include HCC_PLUGIN_DIR . 'templates/email-quote-admin.php';
        $message = ob_get_clean();
        
        // Set email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option( 'hcc_email_from_name', get_bloginfo( 'name' ) ) . ' <' . get_option( 'hcc_email_from', $admin_email ) . '>',
            'Reply-To: ' . $quote->client_email,
        );
        
        return wp_mail( $admin_email, $subject, $message, $headers );
    }

    /**
     * Send client notification email
     *
     * @since    2.0.0
     * @param    object    $quote    Quote object
     * @return   bool                True on success
     */
    private function send_client_notification( $quote ) {
        
        $send_to_client = get_option( 'hcc_quote_email_client', 'yes' );
        
        if ( $send_to_client !== 'yes' ) {
            return false;
        }
        
        $subject = sprintf(
            __( '[%s] Your Cleaning Quote Request #%s', 'hotel-cleaning-calculator-pro' ),
            get_bloginfo( 'name' ),
            $quote->quote_number
        );
        
        // Get email template
        ob_start();
        include HCC_PLUGIN_DIR . 'templates/email-quote-client.php';
        $message = ob_get_clean();
        
        // Set email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option( 'hcc_email_from_name', get_bloginfo( 'name' ) ) . ' <' . get_option( 'hcc_email_from', get_option( 'admin_email' ) ) . '>',
        );
        
        return wp_mail( $quote->client_email, $subject, $message, $headers );
    }

    /**
     * Send quote to integrations (Telegram, Webhook, etc.)
     *
     * @since    2.0.0
     * @param    object    $quote    Quote object
     */
    private function send_to_integrations( $quote ) {
        
        // Telegram notification
        if ( get_option( 'hcc_telegram_enabled', 'no' ) === 'yes' ) {
            $telegram = new HCC_Telegram();
            $telegram->send_quote_notification( $quote );
        }
        
        // Webhook notification
        if ( get_option( 'hcc_webhook_enabled', 'no' ) === 'yes' ) {
            $this->send_webhook_notification( $quote );
        }
    }

    /**
     * Send webhook notification
     *
     * @since    2.0.0
     * @param    object    $quote    Quote object
     * @return   bool                True on success
     */
    private function send_webhook_notification( $quote ) {
        
        $webhook_url = get_option( 'hcc_webhook_url', '' );
        
        if ( empty( $webhook_url ) ) {
            return false;
        }
        
        $calculator = new HCC_Calculator();
        
        $payload = array(
            'quote_number'    => $quote->quote_number,
            'client_name'     => $quote->client_name,
            'client_email'    => $quote->client_email,
            'client_phone'    => $quote->client_phone,
            'client_address'  => $quote->client_address,
            'total_area'      => $quote->total_area,
            'subtotal'        => $calculator->format_price( $quote->subtotal ),
            'discount_amount' => $calculator->format_price( $quote->discount_amount ),
            'total_price'     => $calculator->format_price( $quote->total_price ),
            'status'          => $quote->status,
            'created_at'      => $quote->created_at,
        );
        
        $response = wp_remote_post( $webhook_url, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => json_encode( $payload ),
            'timeout' => 15,
        ) );
        
        return ! is_wp_error( $response );
    }

    /**
     * Render quote form
     *
     * @since    2.0.0
     * @param    array    $atts    Shortcode attributes
     * @return   string            HTML output
     */
    public function render_quote_form( $atts ) {
        
        $show_title = isset( $atts['show_title'] ) ? sanitize_text_field( $atts['show_title'] ) : 'yes';
        $redirect_url = isset( $atts['redirect_url'] ) ? esc_url( $atts['redirect_url'] ) : '';
        $custom_class = isset( $atts['custom_class'] ) ? sanitize_html_class( $atts['custom_class'] ) : '';
        
        ob_start();
        include HCC_PLUGIN_DIR . 'public/partials/hcc-quote-form.php';
        return ob_get_clean();
    }

    /**
     * Get quote statistics
     *
     * @since    2.0.0
     * @return   array    Statistics array
     */
    public function get_statistics() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_quotes';
        
        $stats = array(
            'total'     => $wpdb->get_var( "SELECT COUNT(*) FROM $table" ),
            'pending'   => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE status = %s", 'pending' ) ),
            'approved'  => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE status = %s", 'approved' ) ),
            'rejected'  => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE status = %s", 'rejected' ) ),
            'total_revenue' => $wpdb->get_var( $wpdb->prepare( "SELECT SUM(total_price) FROM $table WHERE status = %s", 'approved' ) ),
        );
        
        return $stats;
    }

    /**
     * Export quotes to CSV
     *
     * @since    2.0.0
     * @param    array    $quote_ids    Array of quote IDs to export
     * @return   string                 CSV file path
     */
    public function export_to_csv( $quote_ids = array() ) {
        
        if ( empty( $quote_ids ) ) {
            // Export all quotes
            $quotes = HCC_Database::get_quotes( array( 'limit' => 9999 ) );
        } else {
            // Export specific quotes
            $quotes = array();
            foreach ( $quote_ids as $id ) {
                $quotes[] = HCC_Database::get_quote( intval( $id ) );
            }
        }
        
        if ( empty( $quotes ) ) {
            return false;
        }
        
        // Create CSV file
        $upload_dir = wp_upload_dir();
        $csv_dir = $upload_dir['basedir'] . '/hcc-uploads/exports';
        $csv_file = $csv_dir . '/quotes-' . date( 'Y-m-d-His' ) . '.csv';
        
        $fp = fopen( $csv_file, 'w' );
        
        // Add CSV headers
        fputcsv( $fp, array(
            'Quote Number',
            'Client Name',
            'Client Email',
            'Client Phone',
            'Total Area (m²)',
            'Subtotal',
            'Discount',
            'Total Price',
            'Status',
            'Created Date',
        ) );
        
        // Add quote data
        $calculator = new HCC_Calculator();
        foreach ( $quotes as $quote ) {
            fputcsv( $fp, array(
                $quote->quote_number,
                $quote->client_name,
                $quote->client_email,
                $quote->client_phone,
                $quote->total_area,
                $quote->subtotal,
                $quote->discount_amount,
                $quote->total_price,
                $quote->status,
                $quote->created_at,
            ) );
        }
        
        fclose( $fp );
        
        return $csv_file;
    }

    /**
     * Generate PDF quote
     *
     * @since    2.0.0
     * @param    int    $quote_id    Quote ID
     * @return   string              PDF file path or false
     */
    public function generate_pdf( $quote_id ) {
        
        $quote = HCC_Database::get_quote( $quote_id );
        
        if ( ! $quote ) {
            return false;
        }
        
        // Note: PDF generation requires a library like TCPDF or Dompdf
        // This is a placeholder for the PDF generation logic
        // You would need to install and configure a PDF library
        
        return false; // Placeholder
    }

    /**
     * Get quote by number
     *
     * @since    2.0.0
     * @param    string    $quote_number    Quote number
     * @return   object|null                Quote object or null
     */
    public function get_quote_by_number( $quote_number ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'hcc_quotes';
        
        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table WHERE quote_number = %s", sanitize_text_field( $quote_number ) )
        );
    }

    /**
     * Update quote notes
     *
     * @since    2.0.0
     * @param    int       $quote_id    Quote ID
     * @param    string    $notes       Admin notes
     * @return   bool                   True on success
     */
    public function update_notes( $quote_id, $notes ) {
        return HCC_Database::update_quote( $quote_id, array(
            'admin_notes' => sanitize_textarea_field( $notes ),
        ) );
    }

    /**
     * Bulk delete quotes
     *
     * @since    2.0.0
     * @param    array    $quote_ids    Array of quote IDs
     * @return   int                    Number of deleted quotes
     */
    public function bulk_delete( $quote_ids ) {
        
        if ( empty( $quote_ids ) ) {
            return 0;
        }
        
        $deleted = 0;
        
        foreach ( $quote_ids as $quote_id ) {
            if ( HCC_Database::delete_quote( intval( $quote_id ) ) ) {
                $deleted++;
            }
        }
        
        return $deleted;
    }

    /**
     * Bulk update quote status
     *
     * @since    2.0.0
     * @param    array     $quote_ids    Array of quote IDs
     * @param    string    $status       New status
     * @return   int                     Number of updated quotes
     */
    public function bulk_update_status( $quote_ids, $status ) {
        
        if ( empty( $quote_ids ) || empty( $status ) ) {
            return 0;
        }
        
        $updated = 0;
        
        foreach ( $quote_ids as $quote_id ) {
            if ( HCC_Database::update_quote( intval( $quote_id ), array( 'status' => $status ) ) ) {
                $updated++;
            }
        }
        
        return $updated;
    }
}