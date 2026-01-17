<?php
/**
 * Settings view - Single page without tabs
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin/views
 */

if (!defined('WPINC')) {
    die;
}

// Display messages
if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully.', 'cleaning-price-calculator') . '</p></div>';
}
if (isset($_GET['message'])) {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(urldecode($_GET['message'])) . '</p></div>';
}
if (isset($_GET['error'])) {
    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html(urldecode($_GET['error'])) . '</p></div>';
}
?>

<div class="wrap cpc-admin-wrap">
    <h1><?php esc_html_e('Settings', 'cleaning-price-calculator'); ?></h1>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('cpc_save_settings', 'cpc_settings_nonce'); ?>
        <input type="hidden" name="action" value="cpc_save_settings">
        
        <!-- Company Information Section -->
        <div class="cpc-settings-section" style="display: block;">
            <h2><?php esc_html_e('Company Information', 'cleaning-price-calculator'); ?></h2>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="cpc_company_name"><?php esc_html_e('Company Name', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cpc_company_name" name="cpc_company_name" class="regular-text"
                                   value="<?php echo esc_attr(get_option('cpc_company_name', '')); ?>">
                            <p class="description"><?php esc_html_e('Your company name for emails and display', 'cleaning-price-calculator'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_contact_phone"><?php esc_html_e('Contact Phone', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cpc_contact_phone" name="cpc_contact_phone" class="regular-text"
                                   value="<?php echo esc_attr(get_option('cpc_contact_phone', '')); ?>">
                            <p class="description"><?php esc_html_e('Phone number for the contact button', 'cleaning-price-calculator'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_admin_email"><?php esc_html_e('Admin Email', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="cpc_admin_email" name="cpc_admin_email" class="regular-text"
                                   value="<?php echo esc_attr(get_option('cpc_admin_email', get_option('admin_email'))); ?>">
                            <p class="description"><?php esc_html_e('Email address to receive quote notifications', 'cleaning-price-calculator'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_currency"><?php esc_html_e('Currency', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <select id="cpc_currency" name="cpc_currency">
                                <?php
                                $currencies = CPC_Settings::get_currencies();
                                $current_currency = get_option('cpc_currency', 'EUR');
                                foreach ($currencies as $code => $label):
                                ?>
                                <option value="<?php echo esc_attr($code); ?>" <?php selected($current_currency, $code); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Email Configuration Section -->
        <div class="cpc-settings-section" style="display: block; margin-top: 30px;">
            <h2><?php esc_html_e('Email Configuration', 'cleaning-price-calculator'); ?></h2>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="cpc_smtp_enabled"><?php esc_html_e('Enable SMTP', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <select id="cpc_smtp_enabled" name="cpc_smtp_enabled">
                                <option value="no" <?php selected(get_option('cpc_smtp_enabled', 'no'), 'no'); ?>>
                                    <?php esc_html_e('No', 'cleaning-price-calculator'); ?>
                                </option>
                                <option value="yes" <?php selected(get_option('cpc_smtp_enabled', 'no'), 'yes'); ?>>
                                    <?php esc_html_e('Yes', 'cleaning-price-calculator'); ?>
                                </option>
                            </select>
                            <p class="description"><?php esc_html_e('Enable SMTP for reliable email delivery', 'cleaning-price-calculator'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_smtp_host"><?php esc_html_e('SMTP Host', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cpc_smtp_host" name="cpc_smtp_host" class="regular-text"
                                   value="<?php echo esc_attr(get_option('cpc_smtp_host', '')); ?>"
                                   placeholder="smtp.example.com">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_smtp_port"><?php esc_html_e('SMTP Port', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="cpc_smtp_port" name="cpc_smtp_port" class="small-text"
                                   value="<?php echo esc_attr(get_option('cpc_smtp_port', '587')); ?>">
                            <p class="description"><?php esc_html_e('Usually 587 for TLS or 465 for SSL', 'cleaning-price-calculator'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_smtp_encryption"><?php esc_html_e('Encryption', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <select id="cpc_smtp_encryption" name="cpc_smtp_encryption">
                                <option value="tls" <?php selected(get_option('cpc_smtp_encryption', 'tls'), 'tls'); ?>>TLS</option>
                                <option value="ssl" <?php selected(get_option('cpc_smtp_encryption', 'tls'), 'ssl'); ?>>SSL</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_smtp_username"><?php esc_html_e('SMTP Username', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cpc_smtp_username" name="cpc_smtp_username" class="regular-text"
                                   value="<?php echo esc_attr(get_option('cpc_smtp_username', '')); ?>">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_smtp_password"><?php esc_html_e('SMTP Password', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="cpc_smtp_password" name="cpc_smtp_password" class="regular-text"
                                   value="<?php echo esc_attr(get_option('cpc_smtp_password', '')); ?>"
                                   autocomplete="off">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Language Settings Section -->
        <div class="cpc-settings-section" style="display: block; margin-top: 30px;">
            <h2><?php esc_html_e('Language Settings', 'cleaning-price-calculator'); ?></h2>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="cpc_default_language"><?php esc_html_e('Default Language', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <select id="cpc_default_language" name="cpc_default_language">
                                <?php
                                $languages = CPC_i18n::get_available_languages();
                                $current_lang = get_option('cpc_default_language', 'de_DE');
                                foreach ($languages as $code => $label):
                                ?>
                                <option value="<?php echo esc_attr($code); ?>" <?php selected($current_lang, $code); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Design Settings Section -->
        <div class="cpc-settings-section" style="display: block; margin-top: 30px;">
            <h2><?php esc_html_e('Design Customization', 'cleaning-price-calculator'); ?></h2>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="cpc_primary_color"><?php esc_html_e('Primary Color', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cpc_primary_color" name="cpc_primary_color" class="cpc-color-picker"
                                   value="<?php echo esc_attr(get_option('cpc_primary_color', '#2563eb')); ?>"
                                   data-default-color="#2563eb">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_button_color"><?php esc_html_e('Button Color', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cpc_button_color" name="cpc_button_color" class="cpc-color-picker"
                                   value="<?php echo esc_attr(get_option('cpc_button_color', '#10b981')); ?>"
                                   data-default-color="#10b981">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cpc_accent_color"><?php esc_html_e('Accent Color', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cpc_accent_color" name="cpc_accent_color" class="cpc-color-picker"
                                   value="<?php echo esc_attr(get_option('cpc_accent_color', '#f59e0b')); ?>"
                                   data-default-color="#f59e0b">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Form Display Settings Section -->
        <div class="cpc-settings-section" style="display: block; margin-top: 30px;">
            <h2><?php esc_html_e('Quote Form Display', 'cleaning-price-calculator'); ?></h2>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="cpc_quote_form_display"><?php esc_html_e('Display Mode', 'cleaning-price-calculator'); ?></label>
                        </th>
                        <td>
                            <select id="cpc_quote_form_display" name="cpc_quote_form_display">
                                <option value="modal" <?php selected(get_option('cpc_quote_form_display', 'modal'), 'modal'); ?>>
                                    <?php esc_html_e('Popup Modal', 'cleaning-price-calculator'); ?>
                                </option>
                                <option value="inline" <?php selected(get_option('cpc_quote_form_display', 'modal'), 'inline'); ?>>
                                    <?php esc_html_e('Inline Below Totals', 'cleaning-price-calculator'); ?>
                                </option>
                                <option value="replace" <?php selected(get_option('cpc_quote_form_display', 'modal'), 'replace'); ?>>
                                    <?php esc_html_e('Replace Calculator View', 'cleaning-price-calculator'); ?>
                                </option>
                            </select>
                            <p class="description"><?php esc_html_e('How the quote form should be displayed when user clicks "Request a Quote"', 'cleaning-price-calculator'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php submit_button(__('Save All Settings', 'cleaning-price-calculator'), 'primary large', 'submit'); ?>
    </form>
</div>

<style>
/* Remove tab styles since we're not using tabs */
.cpc-settings-tabs {
    display: none;
}

/* Section styling */
.cpc-settings-section {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
}

.cpc-settings-section h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 18px;
    color: #1f2937;
    border-bottom: 2px solid #2563eb;
    padding-bottom: 10px;
}

/* Form table improvements */
.form-table th {
    width: 200px;
    padding: 15px 10px 15px 0;
}

.form-table td {
    padding: 15px 10px;
}

/* Color picker styling */
.wp-picker-container {
    display: inline-block;
}

/* Submit button */
.wrap .submit {
    padding: 0;
    margin-top: 20px;
}
</style>