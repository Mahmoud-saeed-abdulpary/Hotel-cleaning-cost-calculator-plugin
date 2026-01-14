<?php
/**
 * Calculator Display Template
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/public/partials
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get customization settings
$show_logo = get_option( 'hcc_show_logo', 'yes' );
$logo_url = get_option( 'hcc_logo_url', '' );
$company_name = get_option( 'hcc_company_name', get_bloginfo( 'name' ) );
$company_tagline = get_option( 'hcc_company_tagline', '' );
$show_powered_by = get_option( 'hcc_show_powered_by', 'no' );

// Get translated strings
$calculator_title = get_option( 'hcc_text_calculator_title', __( 'Calculate Cleaning Cost', 'hotel-cleaning-calculator-pro' ) );
$add_room_text = get_option( 'hcc_text_add_room', __( 'Add Room', 'hotel-cleaning-calculator-pro' ) );
$calculate_text = get_option( 'hcc_text_calculate', __( 'Calculate', 'hotel-cleaning-calculator-pro' ) );
$get_quote_text = get_option( 'hcc_text_get_quote', __( 'Get Quote', 'hotel-cleaning-calculator-pro' ) );

// Theme class
$theme_class = isset( $atts['theme'] ) && $atts['theme'] !== 'default' ? 'hcc-theme-' . sanitize_html_class( $atts['theme'] ) : '';
$custom_class = isset( $atts['custom_class'] ) ? sanitize_html_class( $atts['custom_class'] ) : '';
?>

<div class="hcc-calculator-wrapper <?php echo esc_attr( $theme_class . ' ' . $custom_class ); ?>">
    
    <!-- Header -->
    <div class="hcc-calculator-header">
        
        <?php if ( $show_logo === 'yes' && ! empty( $logo_url ) ) : ?>
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $company_name ); ?>" class="hcc-calculator-logo"/>
        <?php endif; ?>
        
        <?php if ( isset( $atts['show_title'] ) && $atts['show_title'] === 'yes' ) : ?>
            <h2 class="hcc-calculator-title"><?php echo esc_html( $calculator_title ); ?></h2>
        <?php endif; ?>
        
        <?php if ( ! empty( $company_tagline ) ) : ?>
            <p class="hcc-calculator-subtitle"><?php echo esc_html( $company_tagline ); ?></p>
        <?php endif; ?>
        
    </div>
    
    <!-- Rooms Container -->
    <div class="hcc-rooms-container">
        
        <div class="hcc-rooms-list">
            <!-- Rooms will be added dynamically via JavaScript -->
        </div>
        
        <!-- Add Room Button -->
        <button type="button" class="hcc-add-room-btn">
            <?php echo esc_html( $add_room_text ); ?>
        </button>
        
    </div>
    
    <!-- Discount Code (Optional) -->
    <?php if ( get_option( 'hcc_enable_discount_codes', 'yes' ) === 'yes' ) : ?>
        <div class="hcc-discount-code-section" style="margin-top: 20px;">
            <div style="display: flex; gap: 10px;">
                <input 
                    type="text" 
                    class="hcc-input hcc-discount-code-input" 
                    placeholder="<?php echo esc_attr( get_option( 'hcc_text_discount_code', __( 'Enter discount code', 'hotel-cleaning-calculator-pro' ) ) ); ?>"
                    style="flex: 1;"
                />
                <button type="button" class="hcc-btn hcc-btn-secondary hcc-apply-discount">
                    <?php echo esc_html( get_option( 'hcc_text_apply_discount', __( 'Apply', 'hotel-cleaning-calculator-pro' ) ) ); ?>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Calculation Summary (Hidden initially, shown after calculation) -->
    <div class="hcc-calculation-summary" style="display: none;">
        <!-- Summary will be populated via JavaScript -->
    </div>
    
    <!-- Actions -->
    <div class="hcc-actions">
        <button type="button" class="hcc-btn hcc-btn-primary hcc-btn-block hcc-calculate-btn">
            <?php echo esc_html( $calculate_text ); ?>
        </button>
        
        <?php if ( get_option( 'hcc_enable_quote_form', 'yes' ) === 'yes' ) : ?>
            <button type="button" class="hcc-btn hcc-btn-success hcc-btn-block hcc-get-quote-btn" onclick="document.querySelector('.hcc-quote-form-section').scrollIntoView({behavior: 'smooth'});">
                <?php echo esc_html( $get_quote_text ); ?>
            </button>
        <?php endif; ?>
    </div>
    
    <!-- Quote Form Section -->
    <?php if ( get_option( 'hcc_enable_quote_form', 'yes' ) === 'yes' ) : ?>
        <div class="hcc-quote-form-section" style="margin-top: 40px;">
            <?php include plugin_dir_path( __FILE__ ) . 'hcc-quote-form.php'; ?>
        </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <?php if ( $show_powered_by === 'yes' ) : ?>
        <div class="hcc-calculator-footer">
            <p class="hcc-powered-by">
                <?php _e( 'Powered by', 'hotel-cleaning-calculator-pro' ); ?>
                <strong>Cleaning Calculator PRO - <a href="https://wpseoatlas.com"> WPSEOATLAS</a></strong>
            </p>
        </div>
    <?php endif; ?>
    
</div>

<style>
/* Dynamic custom CSS from admin */
<?php 
$custom_css = get_option( 'hcc_custom_css', '' );
if ( ! empty( $custom_css ) ) {
    echo wp_strip_all_tags( $custom_css );
}
?>
</style>