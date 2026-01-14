<?php
/**
 * Quote Form Template
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/public/partials
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get translated strings
$form_title = get_option( 'hcc_text_quote_form_title', __( 'Request a Quote', 'hotel-cleaning-calculator-pro' ) );
$name_label = get_option( 'hcc_text_name', __( 'Full Name', 'hotel-cleaning-calculator-pro' ) );
$email_label = get_option( 'hcc_text_email', __( 'Email Address', 'hotel-cleaning-calculator-pro' ) );
$phone_label = get_option( 'hcc_text_phone', __( 'Phone Number', 'hotel-cleaning-calculator-pro' ) );
$address_label = get_option( 'hcc_text_address', __( 'Property Address', 'hotel-cleaning-calculator-pro' ) );
$message_label = get_option( 'hcc_text_message', __( 'Additional Notes', 'hotel-cleaning-calculator-pro' ) );
$submit_label = get_option( 'hcc_text_submit', __( 'Submit Quote Request', 'hotel-cleaning-calculator-pro' ) );

// Check if custom fields are configured
$custom_fields = get_option( 'hcc_quote_custom_fields', array() );
?>

<div class="hcc-quote-form-container">
    
    <div class="hcc-card" style="border: 2px solid var(--hcc-border-color); border-radius: var(--hcc-border-radius); padding: 30px; margin-top: 20px;">
        
        <!-- Form Header -->
        <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid var(--hcc-border-color);">
            <h3 style="margin: 0; font-size: calc(var(--hcc-font-size) * 1.5); color: var(--hcc-text-color);">
                <?php echo esc_html( $form_title ); ?>
            </h3>
            <p style="margin: 10px 0 0 0; color: var(--hcc-secondary-color); font-size: calc(var(--hcc-font-size) * 0.9);">
                <?php _e( 'Fill out the form below to receive your personalized cleaning quote', 'hotel-cleaning-calculator-pro' ); ?>
            </p>
        </div>
        
        <!-- Quote Form -->
        <form class="hcc-quote-form" method="post">
            
            <div style="display: grid; gap: 20px;">
                
                <!-- Full Name -->
                <div class="hcc-form-group">
                    <label class="hcc-form-label required" for="hcc-client-name">
                        <?php echo esc_html( $name_label ); ?>
                    </label>
                    <input 
                        type="text" 
                        id="hcc-client-name"
                        name="client_name" 
                        class="hcc-input" 
                        required
                        placeholder="<?php esc_attr_e( 'John Doe', 'hotel-cleaning-calculator-pro' ); ?>"
                    />
                </div>
                
                <!-- Email & Phone (Two Column) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <!-- Email -->
                    <div class="hcc-form-group">
                        <label class="hcc-form-label required" for="hcc-client-email">
                            <?php echo esc_html( $email_label ); ?>
                        </label>
                        <input 
                            type="email" 
                            id="hcc-client-email"
                            name="client_email" 
                            class="hcc-input" 
                            required
                            placeholder="<?php esc_attr_e( 'john@example.com', 'hotel-cleaning-calculator-pro' ); ?>"
                        />
                    </div>
                    
                    <!-- Phone -->
                    <div class="hcc-form-group">
                        <label class="hcc-form-label" for="hcc-client-phone">
                            <?php echo esc_html( $phone_label ); ?>
                        </label>
                        <input 
                            type="tel" 
                            id="hcc-client-phone"
                            name="client_phone" 
                            class="hcc-input"
                            placeholder="<?php esc_attr_e( '+1 (555) 123-4567', 'hotel-cleaning-calculator-pro' ); ?>"
                        />
                    </div>
                    
                </div>
                
                <!-- Property Address -->
                <div class="hcc-form-group">
                    <label class="hcc-form-label" for="hcc-client-address">
                        <?php echo esc_html( $address_label ); ?>
                    </label>
                    <input 
                        type="text" 
                        id="hcc-client-address"
                        name="client_address" 
                        class="hcc-input"
                        placeholder="<?php esc_attr_e( '123 Main Street, City, State, ZIP', 'hotel-cleaning-calculator-pro' ); ?>"
                    />
                </div>
                
                <!-- Custom Fields -->
                <?php if ( ! empty( $custom_fields ) ) : ?>
                    <?php foreach ( $custom_fields as $field ) : ?>
                        <div class="hcc-form-group">
                            <label class="hcc-form-label<?php echo isset( $field['required'] ) && $field['required'] ? ' required' : ''; ?>" for="hcc-custom-<?php echo esc_attr( $field['id'] ); ?>">
                                <?php echo esc_html( $field['label'] ); ?>
                            </label>
                            
                            <?php if ( $field['type'] === 'textarea' ) : ?>
                                <textarea 
                                    id="hcc-custom-<?php echo esc_attr( $field['id'] ); ?>"
                                    name="custom_<?php echo esc_attr( $field['id'] ); ?>" 
                                    class="hcc-input"
                                    rows="3"
                                    <?php echo isset( $field['required'] ) && $field['required'] ? 'required' : ''; ?>
                                ></textarea>
                            <?php else : ?>
                                <input 
                                    type="<?php echo esc_attr( $field['type'] ); ?>" 
                                    id="hcc-custom-<?php echo esc_attr( $field['id'] ); ?>"
                                    name="custom_<?php echo esc_attr( $field['id'] ); ?>" 
                                    class="hcc-input"
                                    <?php echo isset( $field['required'] ) && $field['required'] ? 'required' : ''; ?>
                                />
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Additional Notes -->
                <div class="hcc-form-group">
                    <label class="hcc-form-label" for="hcc-client-message">
                        <?php echo esc_html( $message_label ); ?>
                    </label>
                    <textarea 
                        id="hcc-client-message"
                        name="client_message" 
                        class="hcc-input" 
                        rows="4"
                        placeholder="<?php esc_attr_e( 'Any special requirements or questions?', 'hotel-cleaning-calculator-pro' ); ?>"
                    ></textarea>
                </div>
                
                <!-- Privacy Notice -->
                <div style="padding: 15px; background: #f9fafb; border-radius: var(--hcc-border-radius); font-size: calc(var(--hcc-font-size) * 0.875); color: var(--hcc-secondary-color);">
                    <p style="margin: 0;">
                        <strong style="color: var(--hcc-text-color);">
                            <?php _e( 'Privacy Notice:', 'hotel-cleaning-calculator-pro' ); ?>
                        </strong>
                        <?php _e( 'Your information will be used solely to provide you with a cleaning quote and will not be shared with third parties.', 'hotel-cleaning-calculator-pro' ); ?>
                    </p>
                </div>
                
                <!-- Submit Button -->
                <div>
                    <button type="submit" class="hcc-btn hcc-btn-success hcc-btn-block" style="padding: 16px 32px; font-size: calc(var(--hcc-font-size) * 1.125);">
                        <span class="hcc-submit-text"><?php echo esc_html( $submit_label ); ?></span>
                        <span class="hcc-loading" style="display: none;"></span>
                    </button>
                </div>
                
            </div>
            
        </form>
        
    </div>
    
</div>

<style>
/* Quote Form Specific Styles */
.hcc-quote-form textarea.hcc-input {
    resize: vertical;
    min-height: 100px;
}

.hcc-quote-form .hcc-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Responsive adjustments for quote form */
@media (max-width: 768px) {
    .hcc-quote-form > div > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
    
    .hcc-card {
        padding: 20px !important;
    }
}
</style>