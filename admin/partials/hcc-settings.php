<?php
/**
 * Settings Page
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Handle form submission
if ( isset( $_POST['hcc_save_settings'] ) && wp_verify_nonce( $_POST['hcc_settings_nonce'], 'hcc_save_settings' ) ) {
    
    // General Settings
    update_option( 'hcc_currency_symbol', sanitize_text_field( $_POST['currency_symbol'] ) );
    update_option( 'hcc_currency_position', sanitize_text_field( $_POST['currency_position'] ) );
    update_option( 'hcc_decimal_separator', sanitize_text_field( $_POST['decimal_separator'] ) );
    update_option( 'hcc_thousand_separator', sanitize_text_field( $_POST['thousand_separator'] ) );
    update_option( 'hcc_decimal_places', intval( $_POST['decimal_places'] ) );
    
    // Quote Settings
    update_option( 'hcc_quote_email_admin', sanitize_email( $_POST['quote_email_admin'] ) );
    update_option( 'hcc_quote_email_client', sanitize_text_field( $_POST['quote_email_client'] ) );
    update_option( 'hcc_quote_prefix', sanitize_text_field( $_POST['quote_prefix'] ) );
    
    // Discount Settings
    update_option( 'hcc_discount_stacking', sanitize_text_field( $_POST['discount_stacking'] ) );
    update_option( 'hcc_discount_display', sanitize_text_field( $_POST['discount_display'] ) );
    
    // Advanced Settings
    update_option( 'hcc_enable_activity_log', sanitize_text_field( $_POST['enable_activity_log'] ) );
    update_option( 'hcc_cache_calculations', sanitize_text_field( $_POST['cache_calculations'] ) );
    update_option( 'hcc_debug_mode', sanitize_text_field( $_POST['debug_mode'] ) );
    update_option( 'hcc_keep_data_on_uninstall', sanitize_text_field( $_POST['keep_data_on_uninstall'] ) );
    
    echo '<div class="hcc-notice success"><p>' . __( 'Settings saved successfully!', 'hotel-cleaning-calculator-pro' ) . '</p></div>';
}

// Get current settings
$currency_symbol = get_option( 'hcc_currency_symbol', '$' );
$currency_position = get_option( 'hcc_currency_position', 'before' );
$decimal_separator = get_option( 'hcc_decimal_separator', '.' );
$thousand_separator = get_option( 'hcc_thousand_separator', ',' );
$decimal_places = get_option( 'hcc_decimal_places', 2 );

$quote_email_admin = get_option( 'hcc_quote_email_admin', get_option( 'admin_email' ) );
$quote_email_client = get_option( 'hcc_quote_email_client', 'yes' );
$quote_prefix = get_option( 'hcc_quote_prefix', 'HCC' );

$discount_stacking = get_option( 'hcc_discount_stacking', 'no' );
$discount_display = get_option( 'hcc_discount_display', 'yes' );

$enable_activity_log = get_option( 'hcc_enable_activity_log', 'yes' );
$cache_calculations = get_option( 'hcc_cache_calculations', 'no' );
$debug_mode = get_option( 'hcc_debug_mode', 'no' );
$keep_data = get_option( 'hcc_keep_data_on_uninstall', 'no' );
?>

<div class="wrap hcc-admin-wrap">
    
    <div class="hcc-admin-header">
        <h1><?php _e( 'Settings', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Configure general plugin settings', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        
        <form method="post" action="">
            <?php wp_nonce_field( 'hcc_save_settings', 'hcc_settings_nonce' ); ?>
            
            <!-- Tabs Navigation -->
            <ul class="hcc-nav-tabs">
                <li><a href="#general" class="active"><?php _e( 'General', 'hotel-cleaning-calculator-pro' ); ?></a></li>
                <li><a href="#quotes"><?php _e( 'Quotes', 'hotel-cleaning-calculator-pro' ); ?></a></li>
                <li><a href="#discounts"><?php _e( 'Discounts', 'hotel-cleaning-calculator-pro' ); ?></a></li>
                <li><a href="#advanced"><?php _e( 'Advanced', 'hotel-cleaning-calculator-pro' ); ?></a></li>
            </ul>
            
            <!-- General Settings Tab -->
            <div id="general" class="hcc-tab-content">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Currency Settings', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-row">
                            <div class="hcc-form-group">
                                <label for="currency_symbol"><?php _e( 'Currency Symbol', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo esc_attr( $currency_symbol ); ?>" required/>
                                <p class="description"><?php _e( 'Symbol to display for currency (e.g., $, €, £)', 'hotel-cleaning-calculator-pro' ); ?></p>
                            </div>
                            
                            <div class="hcc-form-group">
                                <label for="currency_position"><?php _e( 'Currency Position', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <select id="currency_position" name="currency_position">
                                    <option value="before" <?php selected( $currency_position, 'before' ); ?>><?php _e( 'Before amount ($100)', 'hotel-cleaning-calculator-pro' ); ?></option>
                                    <option value="after" <?php selected( $currency_position, 'after' ); ?>><?php _e( 'After amount (100$)', 'hotel-cleaning-calculator-pro' ); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="hcc-form-row">
                            <div class="hcc-form-group">
                                <label for="decimal_separator"><?php _e( 'Decimal Separator', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="decimal_separator" name="decimal_separator" value="<?php echo esc_attr( $decimal_separator ); ?>" maxlength="1" required/>
                                <p class="description"><?php _e( 'Character for decimal point (usually . or ,)', 'hotel-cleaning-calculator-pro' ); ?></p>
                            </div>
                            
                            <div class="hcc-form-group">
                                <label for="thousand_separator"><?php _e( 'Thousand Separator', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="thousand_separator" name="thousand_separator" value="<?php echo esc_attr( $thousand_separator ); ?>" maxlength="1"/>
                                <p class="description"><?php _e( 'Character for thousands (usually , or .)', 'hotel-cleaning-calculator-pro' ); ?></p>
                            </div>
                            
                            <div class="hcc-form-group">
                                <label for="decimal_places"><?php _e( 'Decimal Places', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="number" id="decimal_places" name="decimal_places" value="<?php echo esc_attr( $decimal_places ); ?>" min="0" max="4" required/>
                                <p class="description"><?php _e( 'Number of decimal places to display', 'hotel-cleaning-calculator-pro' ); ?></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Quote Settings Tab -->
            <div id="quotes" class="hcc-tab-content" style="display: none;">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Quote Email Settings', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label for="quote_email_admin"><?php _e( 'Admin Notification Email', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <input type="email" id="quote_email_admin" name="quote_email_admin" value="<?php echo esc_attr( $quote_email_admin ); ?>" required/>
                            <p class="description"><?php _e( 'Email address to receive new quote notifications', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="quote_email_client"><?php _e( 'Send Email to Client', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <select id="quote_email_client" name="quote_email_client">
                                <option value="yes" <?php selected( $quote_email_client, 'yes' ); ?>><?php _e( 'Yes', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="no" <?php selected( $quote_email_client, 'no' ); ?>><?php _e( 'No', 'hotel-cleaning-calculator-pro' ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'Send confirmation email to client after quote submission', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="quote_prefix"><?php _e( 'Quote Number Prefix', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <input type="text" id="quote_prefix" name="quote_prefix" value="<?php echo esc_attr( $quote_prefix ); ?>" maxlength="10" required/>
                            <p class="description"><?php _e( 'Prefix for quote numbers (e.g., HCC-001234)', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Discount Settings Tab -->
            <div id="discounts" class="hcc-tab-content" style="display: none;">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Discount Rules Settings', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label for="discount_stacking"><?php _e( 'Allow Discount Stacking', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <select id="discount_stacking" name="discount_stacking">
                                <option value="yes" <?php selected( $discount_stacking, 'yes' ); ?>><?php _e( 'Yes - Apply all matching discounts', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="no" <?php selected( $discount_stacking, 'no' ); ?>><?php _e( 'No - Apply only highest discount', 'hotel-cleaning-calculator-pro' ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'Whether to apply multiple discounts or just the best one', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="discount_display"><?php _e( 'Display Discount Details', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <select id="discount_display" name="discount_display">
                                <option value="yes" <?php selected( $discount_display, 'yes' ); ?>><?php _e( 'Yes', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="no" <?php selected( $discount_display, 'no' ); ?>><?php _e( 'No', 'hotel-cleaning-calculator-pro' ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'Show discount names and amounts in calculation', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings Tab -->
            <div id="advanced" class="hcc-tab-content" style="display: none;">
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Advanced Settings', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="enable_activity_log" value="yes" <?php checked( $enable_activity_log, 'yes' ); ?>/>
                                <?php _e( 'Enable Activity Log', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Log all plugin activities for debugging and auditing', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="cache_calculations" value="yes" <?php checked( $cache_calculations, 'yes' ); ?>/>
                                <?php _e( 'Cache Calculations', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Cache calculation results for better performance', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="debug_mode" value="yes" <?php checked( $debug_mode, 'yes' ); ?>/>
                                <?php _e( 'Debug Mode', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Enable debug mode for troubleshooting (displays errors)', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="keep_data_on_uninstall" value="yes" <?php checked( $keep_data, 'yes' ); ?>/>
                                <?php _e( 'Keep Data on Uninstall', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Keep all plugin data when uninstalling (for reinstall)', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                    </div>
                </div>
                
                <!-- System Info -->
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'System Information', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        <table class="widefat">
                            <tbody>
                                <tr>
                                    <td><strong><?php _e( 'Plugin Version', 'hotel-cleaning-calculator-pro' ); ?>:</strong></td>
                                    <td><?php echo HCC_VERSION; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php _e( 'WordPress Version', 'hotel-cleaning-calculator-pro' ); ?>:</strong></td>
                                    <td><?php echo get_bloginfo( 'version' ); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php _e( 'PHP Version', 'hotel-cleaning-calculator-pro' ); ?>:</strong></td>
                                    <td><?php echo PHP_VERSION; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php _e( 'MySQL Version', 'hotel-cleaning-calculator-pro' ); ?>:</strong></td>
                                    <td><?php echo $wpdb->db_version(); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php _e( 'Server', 'hotel-cleaning-calculator-pro' ); ?>:</strong></td>
                                    <td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Save Button -->
            <div style="margin-top: 20px;">
                <button type="submit" name="hcc_save_settings" class="hcc-btn hcc-btn-primary hcc-btn-lg">
                    <span class="dashicons dashicons-yes"></span>
                    <?php _e( 'Save Settings', 'hotel-cleaning-calculator-pro' ); ?>
                </button>
            </div>
            
        </form>
        
    </div>
    
</div>