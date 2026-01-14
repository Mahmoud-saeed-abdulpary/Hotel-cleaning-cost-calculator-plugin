<?php
/**
 * Translations Management Page
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Handle form submission
if ( isset( $_POST['hcc_save_translations'] ) && wp_verify_nonce( $_POST['hcc_translations_nonce'], 'hcc_save_translations' ) ) {
    $translation = new HCC_Translation();
    $updated = 0;
    
    foreach ( $_POST as $key => $value ) {
        if ( strpos( $key, 'hcc_text_' ) === 0 ) {
            $text_key = str_replace( 'hcc_text_', '', $key );
            update_option( $key, sanitize_text_field( $value ) );
            $updated++;
        }
    }
    
    echo '<div class="hcc-notice success"><p>' . sprintf( __( '%d translations saved successfully!', 'hotel-cleaning-calculator-pro' ), $updated ) . '</p></div>';
}

// Handle reset
if ( isset( $_POST['hcc_reset_translations'] ) && wp_verify_nonce( $_POST['hcc_translations_nonce'], 'hcc_save_translations' ) ) {
    $translation = new HCC_Translation();
    $reset = $translation->reset_all_strings();
    echo '<div class="hcc-notice success"><p>' . sprintf( __( '%d translations reset to defaults!', 'hotel-cleaning-calculator-pro' ), $reset ) . '</p></div>';
}

$translation = new HCC_Translation();
$grouped_strings = $translation->get_grouped_strings();
$stats = $translation->get_statistics();
?>

<div class="wrap hcc-admin-wrap">
    <div class="hcc-admin-header">
        <h1><?php _e( 'Translations', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Customize all text strings displayed in the calculator', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        
        <!-- Statistics -->
        <div class="hcc-stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 30px;">
            <div class="hcc-stat-card primary">
                <div class="stat-icon"><span class="dashicons dashicons-translation"></span></div>
                <h3><?php echo esc_html( $stats['total'] ); ?></h3>
                <p><?php _e( 'Total Strings', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <div class="hcc-stat-card success">
                <div class="stat-icon"><span class="dashicons dashicons-edit"></span></div>
                <h3><?php echo esc_html( $stats['customized'] ); ?></h3>
                <p><?php _e( 'Customized', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <div class="hcc-stat-card secondary">
                <div class="stat-icon"><span class="dashicons dashicons-admin-generic"></span></div>
                <h3><?php echo esc_html( $stats['default'] ); ?></h3>
                <p><?php _e( 'Using Defaults', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
        </div>
        
        <!-- Search and Actions -->
        <div class="hcc-card">
            <div class="hcc-card-body" style="padding: 20px;">
                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <input 
                            type="text" 
                            id="translation-search" 
                            placeholder="<?php _e( 'Search translations...', 'hotel-cleaning-calculator-pro' ); ?>"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                        />
                    </div>
                    <div>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field( 'hcc_save_translations', 'hcc_translations_nonce' ); ?>
                            <button type="submit" name="hcc_reset_translations" class="hcc-btn hcc-btn-outline" onclick="return confirm('<?php _e( 'Reset all translations to defaults?', 'hotel-cleaning-calculator-pro' ); ?>');">
                                <span class="dashicons dashicons-image-rotate"></span>
                                <?php _e( 'Reset All', 'hotel-cleaning-calculator-pro' ); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Translation Form -->
        <form method="post" action="">
            <?php wp_nonce_field( 'hcc_save_translations', 'hcc_translations_nonce' ); ?>
            
            <?php foreach ( $grouped_strings as $group_key => $group ) : ?>
                
                <div class="hcc-card translation-group" style="margin-top: 20px;">
                    <div class="hcc-card-header">
                        <h3>
                            <span class="dashicons dashicons-admin-<?php echo $group_key === 'calculator' ? 'settings' : ( $group_key === 'quote_form' ? 'page' : 'generic' ); ?>"></span>
                            <?php echo esc_html( $group['title'] ); ?>
                        </h3>
                        <span class="hcc-badge status-active">
                            <?php echo count( $group['strings'] ); ?> <?php _e( 'strings', 'hotel-cleaning-calculator-pro' ); ?>
                        </span>
                    </div>
                    <div class="hcc-card-body">
                        
                        <div style="display: grid; gap: 20px;">
                            <?php foreach ( $group['strings'] as $string ) : ?>
                                <div class="translation-item" data-key="<?php echo esc_attr( $string['key'] ); ?>">
                                    <div class="hcc-form-row" style="align-items: start;">
                                        
                                        <div class="hcc-form-group" style="flex: 1;">
                                            <label for="hcc_text_<?php echo esc_attr( $string['key'] ); ?>">
                                                <strong><?php echo esc_html( ucwords( str_replace( '_', ' ', $string['key'] ) ) ); ?></strong>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="hcc_text_<?php echo esc_attr( $string['key'] ); ?>"
                                                name="hcc_text_<?php echo esc_attr( $string['key'] ); ?>"
                                                value="<?php echo esc_attr( $string['current'] ); ?>"
                                                style="width: 100%;"
                                            />
                                            <p class="description" style="margin-top: 5px; color: #6b7280; font-size: 12px;">
                                                <strong><?php _e( 'Default:', 'hotel-cleaning-calculator-pro' ); ?></strong>
                                                <em><?php echo esc_html( $string['default'] ); ?></em>
                                            </p>
                                        </div>
                                        
                                        <div style="padding-top: 28px;">
                                            <?php if ( $string['current'] !== $string['default'] ) : ?>
                                                <span class="dashicons dashicons-yes-alt" style="color: #10b981; font-size: 20px;" title="<?php _e( 'Customized', 'hotel-cleaning-calculator-pro' ); ?>"></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                    </div>
                </div>
                
            <?php endforeach; ?>
            
            <!-- Save Button (Sticky) -->
            <div style="position: sticky; bottom: 20px; margin-top: 30px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 -4px 6px rgba(0,0,0,0.1); z-index: 10;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="margin: 0; color: #6b7280;">
                            <?php printf( __( '%d customized of %d total strings', 'hotel-cleaning-calculator-pro' ), $stats['customized'], $stats['total'] ); ?>
                        </p>
                    </div>
                    <div>
                        <button type="submit" name="hcc_save_translations" class="hcc-btn hcc-btn-primary hcc-btn-lg">
                            <span class="dashicons dashicons-yes"></span>
                            <?php _e( 'Save All Translations', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            
        </form>
        
        <!-- Export/Import Section -->
        <div class="hcc-card" style="margin-top: 30px;">
            <div class="hcc-card-header">
                <h3><?php _e( 'Import / Export', 'hotel-cleaning-calculator-pro' ); ?></h3>
            </div>
            <div class="hcc-card-body">
                <div class="hcc-form-row">
                    <div class="hcc-form-group">
                        <label><?php _e( 'Export Translations', 'hotel-cleaning-calculator-pro' ); ?></label>
                        <p class="description"><?php _e( 'Download all translations as JSON file', 'hotel-cleaning-calculator-pro' ); ?></p>
                        <button type="button" id="export-translations" class="hcc-btn hcc-btn-secondary">
                            <span class="dashicons dashicons-download"></span>
                            <?php _e( 'Export to JSON', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                    </div>
                    <div class="hcc-form-group">
                        <label><?php _e( 'Import Translations', 'hotel-cleaning-calculator-pro' ); ?></label>
                        <p class="description"><?php _e( 'Upload JSON file to import translations', 'hotel-cleaning-calculator-pro' ); ?></p>
                        <input type="file" id="import-translations" accept=".json" style="display: none;"/>
                        <button type="button" class="hcc-btn hcc-btn-secondary" onclick="document.getElementById('import-translations').click();">
                            <span class="dashicons dashicons-upload"></span>
                            <?php _e( 'Import from JSON', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Search functionality
    $('#translation-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        
        $('.translation-item').each(function() {
            var $item = $(this);
            var key = $item.data('key').toLowerCase();
            var label = $item.find('label strong').text().toLowerCase();
            var value = $item.find('input').val().toLowerCase();
            var defaultVal = $item.find('.description em').text().toLowerCase();
            
            if (key.indexOf(searchTerm) > -1 || 
                label.indexOf(searchTerm) > -1 || 
                value.indexOf(searchTerm) > -1 || 
                defaultVal.indexOf(searchTerm) > -1) {
                $item.show();
            } else {
                $item.hide();
            }
        });
        
        // Hide empty groups
        $('.translation-group').each(function() {
            var visibleItems = $(this).find('.translation-item:visible').length;
            if (visibleItems === 0) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
    
    // Export functionality
    $('#export-translations').on('click', function() {
        window.location.href = ajaxurl + '?action=hcc_export_translations&nonce=<?php echo wp_create_nonce( 'hcc_admin_nonce' ); ?>';
    });
    
    // Import functionality
    $('#import-translations').on('change', function() {
        var file = this.files[0];
        if (!file) return;
        
        var reader = new FileReader();
        reader.onload = function(e) {
            var json = e.target.result;
            
            $.post(ajaxurl, {
                action: 'hcc_import_translations',
                nonce: '<?php echo wp_create_nonce( 'hcc_admin_nonce' ); ?>',
                json: json
            }, function(response) {
                if (response.success) {
                    alert('✅ ' + response.data.message);
                    location.reload();
                } else {
                    alert('❌ ' + response.data.message);
                }
            });
        };
        reader.readAsText(file);
    });
});
</script>