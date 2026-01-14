<?php
/**
 * Email/SMTP Integration
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/integrations
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Email/SMTP Integration Class
 *
 * Handles email sending with custom SMTP configuration
 *
 * @since 2.0.0
 */
class HCC_Email {

    /**
     * Initialize email integration
     *
     * @since 2.0.0
     */
    public function init() {
        // Hook into phpmailer for SMTP configuration
        add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
        
        // Override from email if custom is set
        add_filter( 'wp_mail_from', array( $this, 'custom_mail_from' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'custom_mail_from_name' ) );
    }

    /**
     * Configure SMTP settings for PHPMailer
     *
     * @since  2.0.0
     * @param  object  $phpmailer  PHPMailer instance
     */
    public function configure_smtp( $phpmailer ) {
        
        $smtp_enabled = get_option( 'hcc_smtp_enabled', 'no' );
        
        if ( $smtp_enabled !== 'yes' ) {
            return;
        }

        $smtp_host = get_option( 'hcc_smtp_host', '' );
        $smtp_port = get_option( 'hcc_smtp_port', '587' );
        $smtp_username = get_option( 'hcc_smtp_username', '' );
        $smtp_password = get_option( 'hcc_smtp_password', '' );
        $smtp_encryption = get_option( 'hcc_smtp_encryption', 'tls' );

        if ( empty( $smtp_host ) ) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_host;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $smtp_port;
        $phpmailer->Username = $smtp_username;
        $phpmailer->Password = $smtp_password;
        
        if ( $smtp_encryption !== 'none' ) {
            $phpmailer->SMTPSecure = $smtp_encryption;
        }
        
        // Enable debug output for testing (only if debug mode is on)
        if ( get_option( 'hcc_debug_mode', 'no' ) === 'yes' ) {
            $phpmailer->SMTPDebug = 2;
            $phpmailer->Debugoutput = 'error_log';
        }
    }

    /**
     * Custom mail from address
     *
     * @since  2.0.0
     * @param  string  $email  Default from email
     * @return string          Custom from email
     */
    public function custom_mail_from( $email ) {
        $custom_email = get_option( 'hcc_email_from', '' );
        return ! empty( $custom_email ) ? $custom_email : $email;
    }

    /**
     * Custom mail from name
     *
     * @since  2.0.0
     * @param  string  $name  Default from name
     * @return string         Custom from name
     */
    public function custom_mail_from_name( $name ) {
        $custom_name = get_option( 'hcc_email_from_name', '' );
        return ! empty( $custom_name ) ? $custom_name : $name;
    }

    /**
     * Send test email
     *
     * @since  2.0.0
     * @param  string  $to_email  Recipient email address
     * @return array              Result array
     */
    public function send_test_email( $to_email = '' ) {
        
        if ( empty( $to_email ) ) {
            $to_email = get_option( 'hcc_quote_email_admin', get_option( 'admin_email' ) );
        }

        $subject = __( 'Hotel Cleaning Calculator PRO - Test Email', 'hotel-cleaning-calculator-pro' );
        
        $message = $this->get_email_header();
        $message .= '<h2 style="color: #2563eb; margin-top: 0;">Test Email Successful!</h2>';
        $message .= '<p>This is a test email from <strong>Hotel Cleaning Calculator PRO</strong>.</p>';
        $message .= '<p>Your email configuration is working correctly.</p>';
        $message .= '<div style="margin: 30px 0; padding: 20px; background: #f3f4f6; border-left: 4px solid #2563eb;">';
        $message .= '<p style="margin: 0;"><strong>SMTP Status:</strong> ' . ( get_option( 'hcc_smtp_enabled', 'no' ) === 'yes' ? 'Enabled' : 'Disabled' ) . '</p>';
        $message .= '<p style="margin: 10px 0 0 0;"><strong>From:</strong> ' . $this->custom_mail_from( '' ) . '</p>';
        $message .= '<p style="margin: 10px 0 0 0;"><strong>Date:</strong> ' . current_time( 'mysql' ) . '</p>';
        $message .= '</div>';
        $message .= '<p>You can now safely send quote notifications to your clients.</p>';
        $message .= $this->get_email_footer();

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        $result = wp_mail( $to_email, $subject, $message, $headers );

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
     * Get email header template
     *
     * @since  2.0.0
     * @return string  HTML header
     */
    public function get_email_header() {
        
        $logo_url = get_option( 'hcc_logo_url', '' );
        $company_name = get_option( 'hcc_company_name', get_bloginfo( 'name' ) );
        
        ob_start();
        include HCC_PLUGIN_DIR . 'templates/email-styles.php';
        $styles = ob_get_clean();
        
        $header = $styles;
        $header .= '<div class="email-container">';
        $header .= '<div class="email-header">';
        
        if ( ! empty( $logo_url ) ) {
            $header .= '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $company_name ) . '" class="email-logo"/>';
        } else {
            $header .= '<h1 style="margin: 0; color: #fff;">' . esc_html( $company_name ) . '</h1>';
        }
        
        $header .= '</div>';
        $header .= '<div class="email-body">';
        
        return $header;
    }

    /**
     * Get email footer template
     *
     * @since  2.0.0
     * @return string  HTML footer
     */
    public function get_email_footer() {
        
        $company_name = get_option( 'hcc_company_name', get_bloginfo( 'name' ) );
        $site_url = home_url();
        
        $footer = '</div>'; // Close email-body
        $footer .= '<div class="email-footer">';
        $footer .= '<p>&copy; ' . date( 'Y' ) . ' ' . esc_html( $company_name ) . '. All rights reserved.</p>';
        $footer .= '<p><a href="' . esc_url( $site_url ) . '" style="color: #2563eb;">' . esc_html( $site_url ) . '</a></p>';
        $footer .= '<p style="font-size: 12px; color: #9ca3af;">Powered by Hotel Cleaning Calculator PRO</p>';
        $footer .= '</div>';
        $footer .= '</div>'; // Close email-container
        
        return $footer;
    }

    /**
     * Send admin notification email
     *
     * @since  2.0.0
     * @param  object  $quote  Quote object
     * @return bool            True on success
     */
    public function send_admin_notification( $quote ) {
        
        $admin_email = get_option( 'hcc_quote_email_admin', get_option( 'admin_email' ) );
        
        if ( empty( $admin_email ) ) {
            return false;
        }

        $subject = sprintf(
            __( '[%s] New Cleaning Quote Request #%s', 'hotel-cleaning-calculator-pro' ),
            get_bloginfo( 'name' ),
            $quote->quote_number
        );

        ob_start();
        include HCC_PLUGIN_DIR . 'templates/email-quote-admin.php';
        $message = ob_get_clean();

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $quote->client_email,
        );

        return wp_mail( $admin_email, $subject, $message, $headers );
    }

    /**
     * Send client confirmation email
     *
     * @since  2.0.0
     * @param  object  $quote  Quote object
     * @return bool            True on success
     */
    public function send_client_confirmation( $quote ) {
        
        $send_to_client = get_option( 'hcc_quote_email_client', 'yes' );
        
        if ( $send_to_client !== 'yes' ) {
            return false;
        }

        $subject = sprintf(
            __( '[%s] Your Cleaning Quote Request #%s', 'hotel-cleaning-calculator-pro' ),
            get_bloginfo( 'name' ),
            $quote->quote_number
        );

        ob_start();
        include HCC_PLUGIN_DIR . 'templates/email-quote-client.php';
        $message = ob_get_clean();

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
        );

        return wp_mail( $quote->client_email, $subject, $message, $headers );
    }

    /**
     * Send quote status update email
     *
     * @since  2.0.0
     * @param  object  $quote   Quote object
     * @param  string  $status  New status
     * @return bool             True on success
     */
    public function send_status_update( $quote, $status ) {
        
        $calculator = new HCC_Calculator();
        
        $subject = sprintf(
            __( '[%s] Quote #%s Status Update: %s', 'hotel-cleaning-calculator-pro' ),
            get_bloginfo( 'name' ),
            $quote->quote_number,
            ucfirst( $status )
        );

        $message = $this->get_email_header();
        
        $message .= '<h2 style="color: #2563eb;">Quote Status Updated</h2>';
        $message .= '<p>Dear ' . esc_html( $quote->client_name ) . ',</p>';
        $message .= '<p>The status of your cleaning quote has been updated.</p>';
        
        $message .= '<div style="margin: 30px 0; padding: 20px; background: #f3f4f6; border-radius: 8px;">';
        $message .= '<table style="width: 100%;">';
        $message .= '<tr><td><strong>Quote Number:</strong></td><td>' . esc_html( $quote->quote_number ) . '</td></tr>';
        $message .= '<tr><td><strong>Status:</strong></td><td><span style="background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 12px;">' . ucfirst( $status ) . '</span></td></tr>';
        $message .= '<tr><td><strong>Total Amount:</strong></td><td><strong>' . esc_html( $calculator->format_price( $quote->total_price ) ) . '</strong></td></tr>';
        $message .= '</table>';
        $message .= '</div>';
        
        if ( $status === 'approved' ) {
            $message .= '<p>Great news! Your quote has been approved. We will contact you shortly to schedule the cleaning service.</p>';
        } elseif ( $status === 'rejected' ) {
            $message .= '<p>Unfortunately, we are unable to proceed with this quote at this time. Please feel free to submit a new quote request or contact us for more information.</p>';
        }
        
        $message .= '<p>If you have any questions, please don\'t hesitate to contact us.</p>';
        
        $message .= $this->get_email_footer();

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        return wp_mail( $quote->client_email, $subject, $message, $headers );
    }

    /**
     * Verify SMTP connection
     *
     * @since  2.0.0
     * @return array  Verification result
     */
    public function verify_smtp_connection() {
        
        $smtp_enabled = get_option( 'hcc_smtp_enabled', 'no' );
        
        if ( $smtp_enabled !== 'yes' ) {
            return array(
                'success' => false,
                'message' => __( 'SMTP is not enabled', 'hotel-cleaning-calculator-pro' ),
            );
        }

        $smtp_host = get_option( 'hcc_smtp_host', '' );
        $smtp_port = get_option( 'hcc_smtp_port', '587' );

        if ( empty( $smtp_host ) ) {
            return array(
                'success' => false,
                'message' => __( 'SMTP host is not configured', 'hotel-cleaning-calculator-pro' ),
            );
        }

        // Try to connect
        $connection = @fsockopen( $smtp_host, $smtp_port, $errno, $errstr, 10 );
        
        if ( $connection ) {
            fclose( $connection );
            return array(
                'success' => true,
                'message' => __( 'SMTP connection successful', 'hotel-cleaning-calculator-pro' ),
            );
        } else {
            return array(
                'success' => false,
                'message' => sprintf(
                    __( 'Failed to connect to SMTP server: %s', 'hotel-cleaning-calculator-pro' ),
                    $errstr
                ),
            );
        }
    }
}