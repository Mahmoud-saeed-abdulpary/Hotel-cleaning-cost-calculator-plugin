<?php
/**
 * UI Customizer Panel
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$customizer = new HCC_Customizer();
$colors = $customizer->get_color_scheme();
$typography = $customizer->get_typography_settings();
$layout = $customizer->get_layout_settings();
$theme_presets = $customizer->get_theme_presets();
$google_fonts = $customizer->get_google_fonts();
$custom_css = $customizer->get_custom_css();
?>

<div class="wrap hcc-admin-wrap">
    <div class="hcc-admin-header">
        <h1><?php _e( 'UI Customization', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Customize colors, fonts, and layout to match your brand', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        
        <div class="hcc-customizer-container">
            
            <!-- Controls Panel -->
            <div class="hcc-customizer-controls">
                
                <!-- Theme Presets Section -->
                <div class="hcc-control-section expanded">
                    <div class="hcc-control-section-header">
                        <h3><?php _e( 'Theme Presets', 'hotel-cleaning-calculator-pro' ); ?></h3>
                        <span class="dashicons dashicons-arrow-down"></span>
                    </div>
                    <div class="hcc-control-section-body">
                        <p style="color: #6b7280; font-size: 13px; margin-bottom: 15px;">
                            <?php _e( 'Choose a pre-designed theme or customize your own', 'hotel-cleaning-calculator-pro' ); ?>
                        </p>
                        <div class="hcc-theme-presets">
                            <?php foreach ( $theme_presets as $preset_id => $preset ) : ?>
                                <div class="hcc-theme-preset-card" data-preset="<?php echo esc_attr( $preset_id ); ?>">
                                    <div class="hcc-theme-preset-name"><?php echo esc_html( $preset['name'] ); ?></div>
                                    <div class="hcc-theme-preset-colors">
                                        <?php foreach ( $preset['colors'] as $color ) : ?>
                                            <div class="hcc-theme-preset-color" style="background-color: <?php echo esc_attr( $color ); ?>"></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Colors Section -->
                <div class="hcc-control-section">
                    <div class="hcc-control-section-header">
                        <h3><?php _e( 'Colors', 'hotel-cleaning-calculator-pro' ); ?></h3>
                        <span class="dashicons dashicons-arrow-down"></span>
                    </div>
                    <div class="hcc-control-section-body">
                        <div class="hcc-color-grid">
                            
                            <div class="hcc-color-control">
                                <label><?php _e( 'Primary Color', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="primary-color" class="hcc-color-picker" value="<?php echo esc_attr( $colors['primary'] ); ?>"/>
                            </div>
                            
                            <div class="hcc-color-control">
                                <label><?php _e( 'Secondary Color', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="secondary-color" class="hcc-color-picker" value="<?php echo esc_attr( $colors['secondary'] ); ?>"/>
                            </div>
                            
                            <div class="hcc-color-control">
                                <label><?php _e( 'Accent Color', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="accent-color" class="hcc-color-picker" value="<?php echo esc_attr( $colors['accent'] ); ?>"/>
                            </div>
                            
                            <div class="hcc-color-control">
                                <label><?php _e( 'Background', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="background-color" class="hcc-color-picker" value="<?php echo esc_attr( $colors['background'] ); ?>"/>
                            </div>
                            
                            <div class="hcc-color-control">
                                <label><?php _e( 'Text Color', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="text-color" class="hcc-color-picker" value="<?php echo esc_attr( $colors['text'] ); ?>"/>
                            </div>
                            
                            <div class="hcc-color-control">
                                <label><?php _e( 'Border Color', 'hotel-cleaning-calculator-pro' ); ?></label>
                                <input type="text" id="border-color" class="hcc-color-picker" value="<?php echo esc_attr( $colors['border'] ); ?>"/>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <!-- Typography Section -->
                <div class="hcc-control-section">
                    <div class="hcc-control-section-header">
                        <h3><?php _e( 'Typography', 'hotel-cleaning-calculator-pro' ); ?></h3>
                        <span class="dashicons dashicons-arrow-down"></span>
                    </div>
                    <div class="hcc-control-section-body">
                        
                        <div class="hcc-form-group">
                            <label for="font-family"><?php _e( 'Font Family', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <select id="font-family" class="hcc-font-family-select">
                                <?php foreach ( $google_fonts as $font_value => $font_name ) : ?>
                                    <option value="<?php echo esc_attr( $font_value ); ?>" <?php selected( $typography['font_family'], $font_value ); ?>>
                                        <?php echo esc_html( $font_name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="font-size"><?php _e( 'Base Font Size', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <div class="hcc-font-size-control">
                                <input type="range" id="font-size" min="12" max="24" value="<?php echo esc_attr( $typography['font_size'] ); ?>" class="hcc-font-size-slider"/>
                                <span id="font-size-value" class="hcc-font-size-value"><?php echo esc_html( $typography['font_size'] ); ?>px</span>
                            </div>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="font-weight"><?php _e( 'Font Weight', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <select id="font-weight">
                                <option value="300" <?php selected( $typography['font_weight'], '300' ); ?>><?php _e( 'Light', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="400" <?php selected( $typography['font_weight'], '400' ); ?>><?php _e( 'Regular', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="500" <?php selected( $typography['font_weight'], '500' ); ?>><?php _e( 'Medium', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="600" <?php selected( $typography['font_weight'], '600' ); ?>><?php _e( 'Semi Bold', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="700" <?php selected( $typography['font_weight'], '700' ); ?>><?php _e( 'Bold', 'hotel-cleaning-calculator-pro' ); ?></option>
                            </select>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="heading-font-size"><?php _e( 'Heading Font Size', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <div class="hcc-font-size-control">
                                <input type="range" id="heading-font-size" min="18" max="36" value="<?php echo esc_attr( $typography['heading_font_size'] ); ?>" class="hcc-font-size-slider"/>
                                <span id="heading-font-size-value" class="hcc-font-size-value"><?php echo esc_html( $typography['heading_font_size'] ); ?>px</span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Layout Section -->
                <div class="hcc-control-section">
                    <div class="hcc-control-section-header">
                        <h3><?php _e( 'Layout', 'hotel-cleaning-calculator-pro' ); ?></h3>
                        <span class="dashicons dashicons-arrow-down"></span>
                    </div>
                    <div class="hcc-control-section-body">
                        
                        <div class="hcc-form-group">
                            <label><?php _e( 'Spacing', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <div class="hcc-spacing-options">
                                <div class="hcc-spacing-option <?php echo $layout['spacing'] === 'compact' ? 'active' : ''; ?>" data-value="compact">
                                    <div style="font-size: 20px;">▪</div>
                                    <div><?php _e( 'Compact', 'hotel-cleaning-calculator-pro' ); ?></div>
                                </div>
                                <div class="hcc-spacing-option <?php echo $layout['spacing'] === 'standard' ? 'active' : ''; ?>" data-value="standard">
                                    <div style="font-size: 20px;">▪▪</div>
                                    <div><?php _e( 'Standard', 'hotel-cleaning-calculator-pro' ); ?></div>
                                </div>
                                <div class="hcc-spacing-option <?php echo $layout['spacing'] === 'spacious' ? 'active' : ''; ?>" data-value="spacious">
                                    <div style="font-size: 20px;">▪▪▪</div>
                                    <div><?php _e( 'Spacious', 'hotel-cleaning-calculator-pro' ); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="border-radius"><?php _e( 'Border Radius', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <div class="hcc-font-size-control">
                                <input type="range" id="border-radius" min="0" max="20" value="<?php echo esc_attr( $layout['border_radius'] ); ?>" class="hcc-font-size-slider"/>
                                <span id="border-radius-value" class="hcc-font-size-value"><?php echo esc_html( $layout['border_radius'] ); ?>px</span>
                            </div>
                        </div>
                        
                        <div class="hcc-form-group">
                            <label for="button-style"><?php _e( 'Button Style', 'hotel-cleaning-calculator-pro' ); ?></label>
                            <select id="button-style">
                                <option value="solid" <?php selected( $layout['button_style'], 'solid' ); ?>><?php _e( 'Solid', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="outline" <?php selected( $layout['button_style'], 'outline' ); ?>><?php _e( 'Outline', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="gradient" <?php selected( $layout['button_style'], 'gradient' ); ?>><?php _e( 'Gradient', 'hotel-cleaning-calculator-pro' ); ?></option>
                            </select>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Custom CSS Section -->
                <div class="hcc-control-section">
                    <div class="hcc-control-section-header">
                        <h3><?php _e( 'Custom CSS', 'hotel-cleaning-calculator-pro' ); ?></h3>
                        <span class="dashicons dashicons-arrow-down"></span>
                    </div>
                    <div class="hcc-control-section-body">
                        <div class="hcc-css-editor">
                            <textarea id="custom-css" class="hcc-css-textarea" placeholder="/* Add your custom CSS here */"><?php echo esc_textarea( $custom_css ); ?></textarea>
                            <div class="hcc-css-editor-hint">
                                <strong><?php _e( 'Tip:', 'hotel-cleaning-calculator-pro' ); ?></strong>
                                <?php _e( 'Use .hcc-calculator-wrapper as the parent selector for your custom styles.', 'hotel-cleaning-calculator-pro' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="hcc-customizer-actions">
                    <div>
                        <button type="button" id="reset-customization" class="hcc-btn hcc-btn-outline">
                            <span class="dashicons dashicons-image-rotate"></span>
                            <?php _e( 'Reset to Defaults', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span class="hcc-save-indicator"></span>
                        <button type="button" id="save-customization" class="hcc-btn hcc-btn-success">
                            <span class="dashicons dashicons-yes"></span>
                            <?php _e( 'Save Customization', 'hotel-cleaning-calculator-pro' ); ?>
                        </button>
                    </div>
                </div>
                
            </div>
            
            <!-- Preview Panel -->
            <div class="hcc-customizer-preview">
                <div class="hcc-preview-header">
                    <div class="hcc-preview-title"><?php _e( 'Live Preview', 'hotel-cleaning-calculator-pro' ); ?></div>
                    <div class="hcc-preview-actions">
                        <div class="hcc-preview-device-buttons">
                            <button type="button" class="hcc-device-btn active" data-device="desktop" title="<?php _e( 'Desktop', 'hotel-cleaning-calculator-pro' ); ?>">
                                <span class="dashicons dashicons-desktop"></span>
                            </button>
                            <button type="button" class="hcc-device-btn" data-device="tablet" title="<?php _e( 'Tablet', 'hotel-cleaning-calculator-pro' ); ?>">
                                <span class="dashicons dashicons-tablet"></span>
                            </button>
                            <button type="button" class="hcc-device-btn" data-device="mobile" title="<?php _e( 'Mobile', 'hotel-cleaning-calculator-pro' ); ?>">
                                <span class="dashicons dashicons-smartphone"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="hcc-preview-content">
                    <!-- Preview will be loaded via JavaScript -->
                </div>
            </div>
            
        </div>
        
    </div>
</div>