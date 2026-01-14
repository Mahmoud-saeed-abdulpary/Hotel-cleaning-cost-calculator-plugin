<?php
/**
 * Integrations Settings Page
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Handle form submission
if ( isset( $_POST['hcc_save_integrations'] ) && wp_verify_nonce( $_POST['hcc_integrations_nonce'], 'hcc_save_integrations' ) ) {
    
    // Telegram
    update_option( 'hcc_telegram_enabled', isset( $_POST['telegram_enabled'] ) ? 'yes' : 'no' );
    update_option( 'hcc_telegram_bot_token', sanitize_text_field( $_POST['telegram_bot_token'] ) );
    update_option( 'hcc_telegram_chat_id', sanitize_text_field( $_POST['telegram_chat_id'] ) );
    
    // SMTP
    update_option( 'hcc_smtp_enabled', isset( $_POST['smtp_enabled'] ) ? 'yes' : 'no' );
    update_option( 'hcc_smtp_host', sanitize_text_field( $_POST['smtp_host'] ) );
    update_option( 'hcc_smtp_port', sanitize_text_field( $_POST['smtp_port'] ) );
    update_option( 'hcc_smtp_username', sanitize_text_field( $_POST['smtp_username'] ) );
    if ( ! empty( $_POST['smtp_password'] ) ) {
        update_option( 'hcc_smtp_password', sanitize_text_field( $_POST['smtp_password'] ) );
    }
    update_option( 'hcc_smtp_encryption', sanitize_text_field( $_POST['smtp_encryption'] ) );
    update_option( 'hcc_email_from', sanitize_email( $_POST['email_from'] ) );
    update_option( 'hcc_email_from_name', sanitize_text_field( $_POST['email_from_name'] ) );
    
    // Webhook
    update_option( 'hcc_webhook_enabled', isset( $_POST['webhook_enabled'] ) ? 'yes' : 'no' );
    update_option( 'hcc_webhook_url', esc_url_raw( $_POST['webhook_url'] ) );
    
    echo '<div class="hcc-notice success"><p>' . __( 'Integration settings saved!', 'hotel-cleaning-calculator-pro' ) . '</p></div>';
}

// Get current settings
$telegram_enabled = get_option( 'hcc_telegram_enabled', 'no' );
$telegram_bot_token = get_option( 'hcc_telegram_bot_token', '' );
$telegram_chat_id = get_option( 'hcc_telegram_chat_id', '' );

$smtp_enabled = get_option( 'hcc_smtp_enabled', 'no' );
$smtp_host = get_option( 'hcc_smtp_host', '' );
$smtp_port = get_option( 'hcc_smtp_port', '587' );
$smtp_username = get_option( 'hcc_smtp_username', '' );
$smtp_password = get_option( 'hcc_smtp_password', '' );
$smtp_encryption = get_option( 'hcc_smtp_encryption', 'tls' );
$email_from = get_option( 'hcc_email_from', get_option( 'admin_email' ) );
$email_from_name = get_option( 'hcc_email_from_name', get_bloginfo( 'name' ) );

$webhook_enabled = get_option( 'hcc_webhook_enabled', 'no' );
$webhook_url = get_option( 'hcc_webhook_url', '' );

$integrations = new HCC_Integrations();
$available_forms = $integrations->get_available_form_plugins();
?>

<div class="wrap hcc-admin-wrap">
    <div class="hcc-admin-header">
        <h1><?php _e( 'Integrations', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Connect with Telegram, Email, Forms, and more', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        
        <form method="post" action="">
            <?php wp_nonce_field( 'hcc_save_integrations', 'hcc_integrations_nonce' ); ?>
            
            <!-- Tabs -->
            <ul class="hcc-nav-tabs">
                <li><a href="#telegram" class="active"><?php _e( 'Telegram', 'hotel-cleaning-calculator-pro' ); ?></a></li>
                <li><a href="#email"><?php _e( 'Email/SMTP', 'hotel-cleaning-calculator-pro' ); ?></a></li>
                <li><a href="#webhook"><?php _e( 'Webhooks', 'hotel-cleaning-calculator-pro' ); ?></a></li>
                <li><a href="#forms"><?php _e( 'Form Plugins', 'hotel-cleaning-calculator-pro' ); ?></a></li>
            </ul>
            
            <!-- Telegram Tab -->
            <div id="telegram" class="hcc-tab-content">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Telegram Bot Integration', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="telegram_enabled" value="yes" <?php checked( $telegram_enabled, 'yes' ); ?>/>
                                <?php _e( 'Enable Telegram Notifications', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Receive instant quote notifications via Telegram', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="telegram_bot_token"><?php _e( 'Bot Token', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <input type="text" id="telegram_bot_token" name="telegram_bot_token" value="<?php echo esc_attr( $telegram_bot_token ); ?>" placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz"/>
                            <p class="description">
                                <?php _e( 'Get your bot token from', 'hotel-cleaning-calculator-pro' ); ?> 
                                <a href="https://t.me/BotFather" target="_blank">@BotFather</a>
                            </p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="telegram_chat_id"><?php _e( 'Chat ID', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <input type="text" id="telegram_chat_id" name="telegram_chat_id" value="<?php echo esc_attr( $telegram_chat_id ); ?>" placeholder="123456789"/>
                            <p class="description">
                                <?php _e( 'Get your chat ID from', 'hotel-cleaning-calculator-pro' ); ?> 
                                <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a>
                            </p>
                        </div>
                        
                        <button type="button" id="test-telegram" class="hcc-btn hcc-btn-secondary">
                            <span class="dashicons dashicons-share"></span>
                            <?php _e( 'Test Connection', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                        
                    </div>
                </div>
            </div>
            
            <!-- Email/SMTP Tab -->
            <div id="email" class="hcc-tab-content" style="display: none;">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'SMTP Configuration', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="smtp_enabled" value="yes" <?php checked( $smtp_enabled, 'yes' ); ?>/>
                                <?php _e( 'Use Custom SMTP', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Override WordPress default email with custom SMTP', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-row">
                            <div class="hcc-form-group">
                                <label for="smtp_host"><?php _e( 'SMTP Host', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="smtp_host" name="smtp_host" value="<?php echo esc_attr( $smtp_host ); ?>" placeholder="smtp.gmail.com"/>
                            </div>
                            <div class="hcc-form-group">
                                <label for="smtp_port"><?php _e( 'SMTP Port', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="smtp_port" name="smtp_port" value="<?php echo esc_attr( $smtp_port ); ?>" placeholder="587"/>
                            </div>
                        </div>
                        
                        <div class="hcc-form-row">
                            <div class="hcc-form-group">
                                <label for="smtp_username"><?php _e( 'Username', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="smtp_username" name="smtp_username" value="<?php echo esc_attr( $smtp_username ); ?>" autocomplete="off"/>
                            </div>
                            <div class="hcc-form-group">
                                <label for="smtp_password"><?php _e( 'Password', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="password" id="smtp_password" name="smtp_password" value="<?php echo esc_attr( $smtp_password ); ?>" placeholder="<?php echo $smtp_password ? '••••••••' : ''; ?>" autocomplete="off"/>
                            </div>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="smtp_encryption"><?php _e( 'Encryption', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <select id="smtp_encryption" name="smtp_encryption">
                                <option value="tls" <?php selected( $smtp_encryption, 'tls' ); ?>>TLS</option>
                                <option value="ssl" <?php selected( $smtp_encryption, 'ssl' ); ?>>SSL</option>
                                <option value="none" <?php selected( $smtp_encryption, 'none' ); ?>><?php _e( 'None', 'hotel-cleaning-calculator-pro' ); ?></option>
                            </select>
                        </div>
                        
                        <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;"/>
                        
                        <div class="hcc-form-row">
                            <div class="hcc-form-group">
                                <label for="email_from"><?php _e( 'From Email', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="email" id="email_from" name="email_from" value="<?php echo esc_attr( $email_from ); ?>"/>
                            </div>
                            <div class="hcc-form-group">
                                <label for="email_from_name"><?php _e( 'From Name', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="email_from_name" name="email_from_name" value="<?php echo esc_attr( $email_from_name ); ?>"/>
                            </div>
                        </div>
                        
                        <button type="button" id="test-smtp" class="hcc-btn hcc-btn-secondary">
                            <span class="dashicons dashicons-email"></span>
                            <?php _e( 'Send Test Email', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                        
                    </div>
                </div>
            </div>
            
            <!-- Webhook Tab -->
            <div id="webhook" class="hcc-tab-content" style="display: none;">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Webhook Integration', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="webhook_enabled" value="yes" <?php checked( $webhook_enabled, 'yes' ); ?>/>
                                <?php _e( 'Enable Webhook', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Send quote data to external API endpoint', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="webhook_url"><?php _e( 'Webhook URL', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <input type="url" id="webhook_url" name="webhook_url" value="<?php echo esc_attr( $webhook_url ); ?>" placeholder="https://your-api.com/webhook"/>
                            <p class="description"><?php _e( 'Quote data will be sent as JSON POST request', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <button type="button" id="test-webhook" class="hcc-btn hcc-btn-secondary">
                            <span class="dashicons dashicons-admin-plugins"></span>
                            <?php _e( 'Test Webhook', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                        
                    </div>
                </div>
            </div>
            
            <!-- Form Plugins Tab -->
            <div id="forms" class="hcc-tab-content" style="display: none;">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Form Plugin Integrations', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <?php if ( ! empty( $available_forms ) ) : ?>
                            <?php foreach ( $available_forms as $form_key => $form_data ) : ?>
                                <div style="padding: 20px; background: #f9fafb; border-radius: 8px; margin-bottom: 15px;">
                                    <h4 style="margin: 0 0 10px 0;">
                                        <span class="dashicons dashicons-yes-alt" style="color: #10b981;"></span>
                                        <?php echo esc_html( $form_data['name'] ); ?>
                                        <span style="color: #6b7280; font-size: 13px; font-weight: normal;">v<?php echo esc_html( $form_data['version'] ); ?></span>
                                    </h4>
                                    <p style="margin: 0; color: #6b7280;">
                                        <?php printf( __( '%d forms detected', 'hotel-cleaning-calculator-pro' ), count( $form_data['forms'] ) ); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="hcc-notice info">
                                <p><?php _e( 'No compatible form plugins detected. Install Fluent Forms, Contact Form 7, or WPForms to use this feature.', 'hotel-cleaning-calculator-pro' ); ?></p>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
            
            <!-- Save Button -->
            <div style="margin-top: 20px;">
                <button type="submit" name="hcc_save_integrations" class="hcc-btn hcc-btn-primary hcc-btn-lg">
                    <span class="dashicons dashicons-yes"></span>
                    <?php _e( 'Save Integration Settings', 'hotel-cleaning-calculator-pro' ); ?>
                </button>
            </div>
            
        </form>
        
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Test Telegram
    $('#test-telegram').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="hcc-loading"></span> Testing...');
        
        $.post(ajaxurl, {
            action: 'hcc_test_telegram',
            nonce: '<?php echo wp_create_nonce( 'hcc_admin_nonce' ); ?>'
        }, function(response) {
            if (response.success) {
                alert('✅ ' + response.data.message);
            } else {
                alert('❌ ' + response.data.message);
            }
            $btn.prop('disabled', false).html('<span class="dashicons dashicons-share"></span> Test Connection');
        });
    });
    
    // Test SMTP
    $('#test-smtp').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="hcc-loading"></span> Sending...');
        
        $.post(ajaxurl, {
            action: 'hcc_test_smtp',
            nonce: '<?php echo wp_create_nonce( 'hcc_admin_nonce' ); ?>'
        }, function(response) {
            if (response.success) {
                alert('✅ ' + response.data.message);
            } else {
                alert('❌ ' + response.data.message);
            }
            $btn.prop('disabled', false).html('<span class="dashicons dashicons-email"></span> Send Test Email');
        });
    });
    
    // Test Webhook
    $('#test-webhook').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="hcc-loading"></span> Testing...');
        
        $.post(ajaxurl, {
            action: 'hcc_test_webhook',
            nonce: '<?php echo wp_create_nonce( 'hcc_admin_nonce' ); ?>'
        }, function(response) {
            if (response.success) {
                alert('✅ ' + response.data.message);
            } else {
                alert('❌ ' + response.data.message);
            }
            $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-plugins"></span> Test Webhook');
        });
    });
});
</script>