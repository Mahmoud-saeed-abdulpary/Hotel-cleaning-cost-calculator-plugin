<?php
/**
 * Room Types Management Page
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get room types
$room_types = get_option( 'hcc_room_types', array() );
$calculator = new HCC_Calculator();
?>

<div class="wrap hcc-admin-wrap">
    
    <div class="hcc-admin-header">
        <h1><?php _e( 'Room Types', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Configure room types and pricing per square meter', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        
        <!-- Header Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><?php _e( 'Manage Room Types', 'hotel-cleaning-calculator-pro' ); ?></h2>
            <div>
                <button type="button" id="hcc-add-room-type" class="hcc-btn hcc-btn-primary">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e( 'Add Room Type', 'hotel-cleaning-calculator-pro' ); ?>
                </button>
                <button type="button" id="hcc-save-room-types" class="hcc-btn hcc-btn-success">
                    <span class="dashicons dashicons-yes"></span>
                    <?php _e( 'Save All Changes', 'hotel-cleaning-calculator-pro' ); ?>
                </button>
            </div>
        </div>
        
        <!-- Info Notice -->
        <div class="hcc-notice info">
            <p>
                <strong><?php _e( 'Drag and drop', 'hotel-cleaning-calculator-pro' ); ?></strong>
                <?php _e( ' to reorder room types. Changes will be saved when you click "Save All Changes".', 'hotel-cleaning-calculator-pro' ); ?>
            </p>
        </div>
        
        <!-- Room Types List -->
        <div class="hcc-card">
            <div class="hcc-card-body">
                <ul class="hcc-room-type-list">
                    <?php if ( ! empty( $room_types ) ) : ?>
                        <?php foreach ( $room_types as $room_type ) : ?>
                            <li class="hcc-room-type-item" data-id="<?php echo esc_attr( $room_type['id'] ); ?>">
                                
                                <!-- Drag Handle -->
                                <div class="hcc-room-type-drag">
                                    <span class="dashicons dashicons-menu"></span>
                                </div>
                                
                                <!-- Room Type Info -->
                                <div class="hcc-room-type-info">
                                    <div class="hcc-room-type-name">
                                        <?php echo esc_html( $room_type['name'] ); ?>
                                    </div>
                                    <div class="hcc-room-type-price">
                                        <?php echo esc_html( $calculator->format_price( $room_type['price_per_m2'] ) ); ?> / m²
                                    </div>
                                </div>
                                
                                <!-- Active Toggle -->
                                <label class="hcc-toggle">
                                    <input 
                                        type="checkbox" 
                                        class="hcc-room-active-toggle" 
                                        <?php checked( $room_type['active'], true ); ?>
                                    />
                                    <span class="hcc-toggle-slider"></span>
                                </label>
                                
                                <!-- Actions -->
                                <div class="hcc-room-type-actions">
                                    <button type="button" class="hcc-btn hcc-btn-sm hcc-btn-secondary hcc-edit-room">
                                        <span class="dashicons dashicons-edit"></span>
                                        <?php _e( 'Edit', 'hotel-cleaning-calculator-pro' ); ?>
                                    </button>
                                    <button type="button" class="hcc-btn hcc-btn-sm hcc-btn-danger hcc-delete-room">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                                
                                <!-- Hidden Order Input -->
                                <input type="hidden" class="room-order" value="<?php echo esc_attr( $room_type['order'] ?? 0 ); ?>"/>
                                
                                <!-- Edit Form (Hidden by default) -->
                                <div class="hcc-room-edit-form" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                                    <div class="hcc-form-row">
                                        <div class="hcc-form-group">
                                            <label><?php _e( 'Room Type Name', 'hotel-cleaning-calculator-pro' ); ?> <span style="color: #ef4444;">*</span></label>
                                            <input 
                                                type="text" 
                                                class="room-name" 
                                                value="<?php echo esc_attr( $room_type['name'] ); ?>"
                                                required
                                            />
                                        </div>
                                        
                                        <div class="hcc-form-group">
                                            <label><?php _e( 'Price per m²', 'hotel-cleaning-calculator-pro' ); ?> <span style="color: #ef4444;">*</span></label>
                                            <input 
                                                type="number" 
                                                class="room-price" 
                                                value="<?php echo esc_attr( $room_type['price_per_m2'] ); ?>"
                                                step="0.01"
                                                min="0"
                                                required
                                            />
                                        </div>
                                    </div>
                                    
                                    <div class="hcc-form-group">
                                        <label><?php _e( 'Description', 'hotel-cleaning-calculator-pro' ); ?></label>
                                        <textarea 
                                            class="room-description" 
                                            rows="3"
                                        ><?php echo esc_textarea( $room_type['description'] ?? '' ); ?></textarea>
                                    </div>
                                    
                                    <div class="hcc-form-group">
                                        <label><?php _e( 'Icon (Dashicon class)', 'hotel-cleaning-calculator-pro' ); ?></label>
                                        <input 
                                            type="text" 
                                            class="room-icon" 
                                            value="<?php echo esc_attr( $room_type['icon'] ?? 'admin-multisite' ); ?>"
                                            placeholder="admin-multisite"
                                        />
                                        <p class="description">
                                            <?php _e( 'Enter a Dashicon class name. View available icons at:', 'hotel-cleaning-calculator-pro' ); ?>
                                            <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">Dashicons</a>
                                        </p>
                                    </div>
                                </div>
                                
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li style="padding: 40px; text-align: center; color: #6b7280;">
                            <span class="dashicons dashicons-admin-multisite" style="font-size: 48px; opacity: 0.3;"></span>
                            <p><?php _e( 'No room types yet. Click "Add Room Type" to get started.', 'hotel-cleaning-calculator-pro' ); ?></p>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
    </div>
    
</div>

<!-- Room Type Template (Hidden) -->
<script type="text/html" id="hcc-room-type-template">
    <li class="hcc-room-type-item" data-id="{ID}">
        <div class="hcc-room-type-drag">
            <span class="dashicons dashicons-menu"></span>
        </div>
        <div class="hcc-room-type-info">
            <div class="hcc-room-type-name"><?php _e( 'New Room Type', 'hotel-cleaning-calculator-pro' ); ?></div>
            <div class="hcc-room-type-price">$0.00 / m²</div>
        </div>
        <label class="hcc-toggle">
            <input type="checkbox" class="hcc-room-active-toggle" checked/>
            <span class="hcc-toggle-slider"></span>
        </label>
        <div class="hcc-room-type-actions">
            <button type="button" class="hcc-btn hcc-btn-sm hcc-btn-secondary hcc-edit-room">
                <span class="dashicons dashicons-edit"></span>
                <?php _e( 'Edit', 'hotel-cleaning-calculator-pro' ); ?>
            </button>
            <button type="button" class="hcc-btn hcc-btn-sm hcc-btn-danger hcc-delete-room">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
        <input type="hidden" class="room-order" value="0"/>
        <div class="hcc-room-edit-form" style="display: block; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <div class="hcc-form-row">
                <div class="hcc-form-group">
                    <label><?php _e( 'Room Type Name', 'hotel-cleaning-calculator-pro' ); ?> <span style="color: #ef4444;">*</span></label>
                    <input type="text" class="room-name" required/>
                </div>
                <div class="hcc-form-group">
                    <label><?php _e( 'Price per m²', 'hotel-cleaning-calculator-pro' ); ?> <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="room-price" step="0.01" min="0" required/>
                </div>
            </div>
            <div class="hcc-form-group">
                <label><?php _e( 'Description', 'hotel-cleaning-calculator-pro' ); ?></label>
                <textarea class="room-description" rows="3"></textarea>
            </div>
            <div class="hcc-form-group">
                <label><?php _e( 'Icon (Dashicon class)', 'hotel-cleaning-calculator-pro' ); ?></label>
                <input type="text" class="room-icon" value="admin-multisite"/>
            </div>
        </div>
    </li>
</script>