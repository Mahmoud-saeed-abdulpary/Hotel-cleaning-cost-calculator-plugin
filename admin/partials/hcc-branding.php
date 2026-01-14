<?php
/**
 * Branding Settings Page
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Handle form submission
if ( isset( $_POST['hcc_save_branding'] ) && wp_verify_nonce( $_POST['hcc_branding_nonce'], 'hcc_save_branding' ) ) {
    
    update_option( 'hcc_company_name', sanitize_text_field( $_POST['company_name'] ) );
    update_option( 'hcc_company_tagline', sanitize_text_field( $_POST['company_tagline'] ) );
    update_option( 'hcc_show_logo', isset( $_POST['show_logo'] ) ? 'yes' : 'no' );
    update_option( 'hcc_show_powered_by', isset( $_POST['show_powered_by'] ) ? 'yes' : 'no' );
    
    echo '<div class="hcc-notice success"><p>' . __( 'Branding settings saved successfully!', 'hotel-cleaning-calculator-pro' ) . '</p></div>';
}

// Get current settings
$logo_url = get_option( 'hcc_logo_url', '' );
$company_name = get_option( 'hcc_company_name', get_bloginfo( 'name' ) );
$company_tagline = get_option( 'hcc_company_tagline', '' );
$show_logo = get_option( 'hcc_show_logo', 'yes' );
$show_powered_by = get_option( 'hcc_show_powered_by', 'no' );
?>

<div class="wrap hcc-admin-wrap">
    <div class="hcc-admin-header">
        <h1><?php _e( 'Branding', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Customize your company logo and branding elements', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        
        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field( 'hcc_save_branding', 'hcc_branding_nonce' ); ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <!-- Logo Upload Section -->
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Company Logo', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label><?php _e( 'Upload Logo', 'hotel-cleaning-calculator-pro' ); ?></label>
                            
                            <div id="logo-preview-container" style="margin: 20px 0; padding: 30px; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px; text-align: center;">
                                <?php if ( $logo_url ) : ?>
                                    <img id="logo-preview" src="<?php echo esc_url( $logo_url ); ?>" alt="Logo" style="max-width: 100%; max-height: 200px;"/>
                                <?php else : ?>
                                    <div id="logo-placeholder" style="padding: 40px; color: #9ca3af;">
                                        <span class="dashicons dashicons-format-image" style="font-size: 64px; opacity: 0.3;"></span>
                                        <p><?php _e( 'No logo uploaded', 'hotel-cleaning-calculator-pro' ); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <button type="button" id="upload-logo-btn" class="hcc-btn hcc-btn-primary">
                                    <span class="dashicons dashicons-upload"></span>
                                    <?php _e( 'Upload Logo', 'hotel-cleaning-calculator-pro' ); ?>
                                </button>
                                <?php if ( $logo_url ) : ?>
                                    <button type="button" id="remove-logo-btn" class="hcc-btn hcc-btn-danger">
                                        <span class="dashicons dashicons-trash"></span>
                                        <?php _e( 'Remove', 'hotel-cleaning-calculator-pro' ); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <p class="description" style="margin-top: 10px;">
                                <?php _e( 'Recommended: PNG or SVG format, max 500KB, transparent background', 'hotel-cleaning-calculator-pro' ); ?>
                            </p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="show_logo" value="yes" <?php checked( $show_logo, 'yes' ); ?>/>
                                <?php _e( 'Display logo in calculator', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Company Information Section -->
                <div class="hcc-card">
                    <div class="hcc-card-header">
                        <h3><?php _e( 'Company Information', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div class="hcc-form-group">
                            <label for="company_name"><?php _e( 'Company Name', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <input 
                                type="text" 
                                id="company_name" 
                                name="company_name" 
                                value="<?php echo esc_attr( $company_name ); ?>"
                                placeholder="<?php _e( 'Your Company Name', 'hotel-cleaning-calculator-pro' ); ?>"
                            />
                            <p class="description"><?php _e( 'Displayed in emails and quote forms', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="company_tagline"><?php _e( 'Company Tagline / Slogan', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <input 
                                type="text" 
                                id="company_tagline" 
                                name="company_tagline" 
                                value="<?php echo esc_attr( $company_tagline ); ?>"
                                placeholder="<?php _e( 'Your company slogan (optional)', 'hotel-cleaning-calculator-pro' ); ?>"
                            />
                            <p class="description"><?php _e( 'Optional tagline to display below logo', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label>
                                <input type="checkbox" name="show_powered_by" value="yes" <?php checked( $show_powered_by, 'yes' ); ?>/>
                                <?php _e( 'Show "Powered by" credit', 'hotel-cleaning-calculator-pro' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Display plugin credit in footer (optional)', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </div>
                        
                    </div>
                </div>
                
            </div>
            
            <!-- Preview Section -->
            <div class="hcc-card" style="margin-top: 20px;">
                <div class="hcc-card-header">
                    <h3><?php _e( 'Preview', 'hotel-cleaning-calculator-pro' ); ?></h3>
                </div>
                <div class="hcc-card-body">
                    <div style="padding: 40px; background: #f3f4f6; border-radius: 8px; text-align: center;">
                        
                        <?php if ( $logo_url && $show_logo === 'yes' ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo" style="max-width: 200px; max-height: 100px; margin-bottom: 15px;"/>
                        <?php endif; ?>
                        
                        <h2 style="margin: 10px 0; color: #111827; font-size: 28px;">
                            <?php echo esc_html( $company_name ); ?>
                        </h2>
                        
                        <?php if ( ! empty( $company_tagline ) ) : ?>
                            <p style="margin: 0; color: #6b7280; font-size: 16px;">
                                <?php echo esc_html( $company_tagline ); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div style="margin-top: 30px; padding: 20px; background: white; border-radius: 8px; display: inline-block;">
                            <p style="margin: 0; color: #9ca3af; font-size: 14px;">
                                <?php _e( 'Calculator would appear here', 'hotel-cleaning-calculator-pro' ); ?>
                            </p>
                        </div>
                        
                        <?php if ( $show_powered_by === 'yes' ) : ?>
                            <p style="margin-top: 20px; color: #9ca3af; font-size: 12px;">
                                <?php _e( 'Powered by Hotel Cleaning Calculator PRO', 'hotel-cleaning-calculator-pro' ); ?>
                            </p>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
            
            <!-- Save Button -->
            <div style="margin-top: 20px;">
                <button type="submit" name="hcc_save_branding" class="hcc-btn hcc-btn-primary hcc-btn-lg">
                    <span class="dashicons dashicons-yes"></span>
                    <?php _e( 'Save Branding Settings', 'hotel-cleaning-calculator-pro' ); ?>
                </button>
            </div>
            
        </form>
        
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    
    // WordPress Media Uploader
    var mediaUploader;
    
    $('#upload-logo-btn').on('click', function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: '<?php _e( 'Choose Logo', 'hotel-cleaning-calculator-pro' ); ?>',
            button: {
                text: '<?php _e( 'Use this logo', 'hotel-cleaning-calculator-pro' ); ?>'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Upload via AJAX
            $.post(ajaxurl, {
                action: 'hcc_upload_logo',
                nonce: '<?php echo wp_create_nonce( 'hcc_admin_nonce' ); ?>',
                attachment_id: attachment.id,
                logo_url: attachment.url
            }, function(response) {
                if (response.success) {
                    $('#logo-placeholder').remove();
                    $('#logo-preview-container').html(
                        '<img id="logo-preview" src="' + response.data.logo_url + '" alt="Logo" style="max-width: 100%; max-height: 200px;"/>'
                    );
                    
                    if ($('#remove-logo-btn').length === 0) {
                        $('#upload-logo-btn').after(
                            '<button type="button" id="remove-logo-btn" class="hcc-btn hcc-btn-danger"><span class="dashicons dashicons-trash"></span> <?php _e( 'Remove', 'hotel-cleaning-calculator-pro' ); ?></button>'
                        );
                    }
                    
                    alert('✅ <?php _e( 'Logo uploaded successfully!', 'hotel-cleaning-calculator-pro' ); ?>');
                }
            });
        });
        
        mediaUploader.open();
    });
    
    // Remove logo
    $(document).on('click', '#remove-logo-btn', function(e) {
        e.preventDefault();
        
        if (!confirm('<?php _e( 'Remove logo?', 'hotel-cleaning-calculator-pro' ); ?>')) {
            return;
        }
        
        $.post(ajaxurl, {
            action: 'hcc_remove_logo',
            nonce: '<?php echo wp_create_nonce( 'hcc_admin_nonce' ); ?>'
        }, function(response) {
            if (response.success) {
                $('#logo-preview-container').html(
                    '<div id="logo-placeholder" style="padding: 40px; color: #9ca3af;"><span class="dashicons dashicons-format-image" style="font-size: 64px; opacity: 0.3;"></span><p><?php _e( 'No logo uploaded', 'hotel-cleaning-calculator-pro' ); ?></p></div>'
                );
                $('#remove-logo-btn').remove();
                alert('✅ <?php _e( 'Logo removed!', 'hotel-cleaning-calculator-pro' ); ?>');
            }
        });
    });
    
    // Live preview update
    $('#company_name, #company_tagline').on('input', function() {
        var name = $('#company_name').val() || '<?php echo esc_js( $company_name ); ?>';
        var tagline = $('#company_tagline').val();
        
        $('.hcc-card-body h2').text(name);
        
        if (tagline) {
            if ($('.hcc-card-body p[style*="color: #6b7280"]').length === 0) {
                $('.hcc-card-body h2').after('<p style="margin: 0; color: #6b7280; font-size: 16px;">' + tagline + '</p>');
            } else {
                $('.hcc-card-body p[style*="color: #6b7280"]').text(tagline);
            }
        } else {
            $('.hcc-card-body p[style*="color: #6b7280"]').remove();
        }
    });
});
</script>