<?php
/**
 * Telegram Bot Integration
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/integrations
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Telegram Bot Integration Class
 *
 * Sends quote notifications to Telegram via Bot API
 *
 * @since 2.0.0
 */
class HCC_Telegram {

    /**
     * Bot token
     *
     * @var string
     */
    private $bot_token;

    /**
     * Chat ID
     *
     * @var string
     */
    private $chat_id;

    /**
     * Telegram API base URL
     *
     * @var string
     */
    private $api_url = 'https://api.telegram.org/bot';

    /**
     * Initialize Telegram integration
     *
     * @since 2.0.0
     */
    public function init() {
        $this->bot_token = get_option( 'hcc_telegram_bot_token', '' );
        $this->chat_id = get_option( 'hcc_telegram_chat_id', '' );
    }

    /**
     * Send quote notification to Telegram
     *
     * @since  2.0.0
     * @param  object  $quote  Quote object from database
     * @return array           Result array
     */
    public function send_quote_notification( $quote ) {
        
        if ( empty( $this->bot_token ) || empty( $this->chat_id ) ) {
            return array(
                'success' => false,
                'message' => __( 'Telegram bot token or chat ID not configured', 'hotel-cleaning-calculator-pro' ),
            );
        }

        // Build message
        $message = $this->build_quote_message( $quote );

        // Send to Telegram
        $response = $this->send_message( $message );

        if ( $response['success'] ) {
            // Log successful notification
            HCC_Database::log_activity( 'telegram_sent', 'quote', $quote->id, array(
                'quote_number' => $quote->quote_number,
                'message_id'   => $response['message_id'] ?? null,
            ) );
        }

        return $response;
    }

    /**
     * Build formatted message for quote
     *
     * @since  2.0.0
     * @param  object  $quote  Quote object
     * @return string          Formatted message
     */
    private function build_quote_message( $quote ) {
        
        $calculator = new HCC_Calculator();
        
        // Emoji indicators
        $emoji = array(
            'new'      => '🆕',
            'quote'    => '📋',
            'client'   => '👤',
            'email'    => '📧',
            'phone'    => '📱',
            'location' => '📍',
            'area'     => '📏',
            'money'    => '💰',
            'discount' => '🎁',
            'total'    => '✅',
            'time'     => '🕐',
        );

        $message = "<b>{$emoji['new']} New Cleaning Quote Request</b>\n\n";
        
        $message .= "<b>{$emoji['quote']} Quote #:</b> <code>{$quote->quote_number}</code>\n";
        $message .= "<b>{$emoji['client']} Client:</b> {$quote->client_name}\n";
        $message .= "<b>{$emoji['email']} Email:</b> {$quote->client_email}\n";
        
        if ( ! empty( $quote->client_phone ) ) {
            $message .= "<b>{$emoji['phone']} Phone:</b> {$quote->client_phone}\n";
        }
        
        if ( ! empty( $quote->client_address ) ) {
            $message .= "<b>{$emoji['location']} Address:</b> {$quote->client_address}\n";
        }
        
        $message .= "\n<b>━━━━━━━━━━━━━━━━━━━━</b>\n\n";
        
        // Room details
        $rooms_data = json_decode( $quote->rooms_data, true );
        if ( ! empty( $rooms_data ) ) {
            $message .= "<b>🏠 Room Details:</b>\n";
            foreach ( $rooms_data as $index => $room ) {
                $room_num = $index + 1;
                $message .= "  {$room_num}. {$room['type_name']}: {$room['area']} m² @ {$calculator->format_price( $room['price_per_m2'] )}/m²\n";
            }
            $message .= "\n";
        }
        
        // Pricing
        $message .= "<b>{$emoji['area']} Total Area:</b> {$quote->total_area} m²\n";
        $message .= "<b>{$emoji['money']} Subtotal:</b> {$calculator->format_price( $quote->subtotal )}\n";
        
        if ( $quote->discount_amount > 0 ) {
            $message .= "<b>{$emoji['discount']} Discount:</b> -{$calculator->format_price( $quote->discount_amount )}\n";
        }
        
        $message .= "\n<b>{$emoji['total']} TOTAL: {$calculator->format_price( $quote->total_price )}</b>\n\n";
        
        $message .= "<b>━━━━━━━━━━━━━━━━━━━━</b>\n\n";
        
        $message .= "<b>{$emoji['time']} Submitted:</b> {$quote->created_at}\n";
        
        // Admin link
        $admin_url = admin_url( 'admin.php?page=hcc-quotes' );
        $message .= "\n<a href='{$admin_url}'>View in Admin Panel</a>";
        
        return $message;
    }

    /**
     * Send message to Telegram
     *
     * @since  2.0.0
     * @param  string  $message  Message text
     * @param  string  $parse_mode  Parse mode (HTML, Markdown)
     * @return array              Result array
     */
    public function send_message( $message, $parse_mode = 'HTML' ) {
        
        $url = $this->api_url . $this->bot_token . '/sendMessage';
        
        $data = array(
            'chat_id'                  => $this->chat_id,
            'text'                     => $message,
            'parse_mode'               => $parse_mode,
            'disable_web_page_preview' => true,
        );

        $response = wp_remote_post( $url, array(
            'body'    => $data,
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
                'success'    => true,
                'message'    => __( 'Message sent to Telegram successfully', 'hotel-cleaning-calculator-pro' ),
                'message_id' => $body['result']['message_id'] ?? null,
            );
        } else {
            return array(
                'success' => false,
                'message' => $body['description'] ?? __( 'Failed to send Telegram message', 'hotel-cleaning-calculator-pro' ),
            );
        }
    }

    /**
     * Send photo to Telegram
     *
     * @since  2.0.0
     * @param  string  $photo_url  URL to photo
     * @param  string  $caption    Photo caption
     * @return array               Result array
     */
    public function send_photo( $photo_url, $caption = '' ) {
        
        $url = $this->api_url . $this->bot_token . '/sendPhoto';
        
        $data = array(
            'chat_id'    => $this->chat_id,
            'photo'      => $photo_url,
            'caption'    => $caption,
            'parse_mode' => 'HTML',
        );

        $response = wp_remote_post( $url, array(
            'body'    => $data,
            'timeout' => 20,
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
                'message' => __( 'Photo sent to Telegram successfully', 'hotel-cleaning-calculator-pro' ),
            );
        } else {
            return array(
                'success' => false,
                'message' => $body['description'] ?? __( 'Failed to send photo', 'hotel-cleaning-calculator-pro' ),
            );
        }
    }

    /**
     * Test Telegram connection
     *
     * @since  2.0.0
     * @return array  Test result
     */
    public function test_connection() {
        
        if ( empty( $this->bot_token ) || empty( $this->chat_id ) ) {
            return array(
                'success' => false,
                'message' => __( 'Bot token and Chat ID are required', 'hotel-cleaning-calculator-pro' ),
            );
        }

        $message = "✅ <b>Hotel Cleaning Calculator PRO</b>\n\n";
        $message .= "Connection test successful!\n";
        $message .= "Your Telegram bot is configured correctly.\n\n";
        $message .= "You will receive quote notifications here.";

        return $this->send_message( $message );
    }

    /**
     * Get bot info
     *
     * @since  2.0.0
     * @return array  Bot information or error
     */
    public function get_bot_info() {
        
        $url = $this->api_url . $this->bot_token . '/getMe';
        
        $response = wp_remote_get( $url, array(
            'timeout' => 10,
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
                'data'    => $body['result'],
            );
        } else {
            return array(
                'success' => false,
                'message' => __( 'Failed to get bot info', 'hotel-cleaning-calculator-pro' ),
            );
        }
    }

    /**
     * Send custom notification
     *
     * @since  2.0.0
     * @param  string  $title    Notification title
     * @param  string  $message  Notification message
     * @param  array   $data     Additional data to include
     * @return array             Result array
     */
    public function send_custom_notification( $title, $message, $data = array() ) {
        
        $text = "<b>{$title}</b>\n\n";
        $text .= $message . "\n";
        
        if ( ! empty( $data ) ) {
            $text .= "\n<b>━━━━━━━━━━━━━━━━━━━━</b>\n\n";
            foreach ( $data as $key => $value ) {
                $text .= "<b>{$key}:</b> {$value}\n";
            }
        }
        
        return $this->send_message( $text );
    }

    /**
     * Send quote status update
     *
     * @since  2.0.0
     * @param  object  $quote   Quote object
     * @param  string  $status  New status
     * @return array            Result array
     */
    public function send_status_update( $quote, $status ) {
        
        $emoji = array(
            'pending'  => '⏳',
            'approved' => '✅',
            'rejected' => '❌',
        );
        
        $icon = $emoji[ $status ] ?? '📝';
        
        $message = "<b>{$icon} Quote Status Updated</b>\n\n";
        $message .= "<b>Quote #:</b> <code>{$quote->quote_number}</code>\n";
        $message .= "<b>Client:</b> {$quote->client_name}\n";
        $message .= "<b>New Status:</b> " . ucfirst( $status ) . "\n\n";
        $message .= "<b>Total:</b> " . ( new HCC_Calculator() )->format_price( $quote->total_price );
        
        return $this->send_message( $message );
    }

    /**
     * Format message for Telegram HTML
     *
     * @since  2.0.0
     * @param  string  $text  Raw text
     * @return string         HTML formatted text
     */
    private function format_html( $text ) {
        // Escape HTML entities
        $text = htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
        
        // Replace newlines with HTML line breaks
        $text = nl2br( $text );
        
        return $text;
    }

    /**
     * Validate bot token format
     *
     * @since  2.0.0
     * @param  string  $token  Bot token
     * @return bool            True if valid format
     */
    public static function validate_token( $token ) {
        // Telegram bot tokens follow pattern: 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
        return preg_match( '/^\d+:[A-Za-z0-9_-]+$/', $token );
    }

    /**
     * Validate chat ID format
     *
     * @since  2.0.0
     * @param  string  $chat_id  Chat ID
     * @return bool               True if valid format
     */
    public static function validate_chat_id( $chat_id ) {
        // Chat IDs are numeric (can be negative for groups)
        return preg_match( '/^-?\d+$/', $chat_id );
    }
}