<?php
/**
 * Admin Dashboard Display
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get statistics
$admin = new HCC_Admin();
$quotes = new HCC_Quotes();
$discounts = new HCC_Discounts();

$stats = $admin->get_statistics();
$quote_stats = $quotes->get_statistics();
$discount_stats = $discounts->get_statistics();
?>

<div class="wrap hcc-admin-wrap">
    
    <!-- Header -->
    <div class="hcc-admin-header">
        <h1><?php _e( 'Hotel Cleaning Calculator PRO', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Professional cleaning cost calculator with advanced features', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <!-- Content -->
    <div class="hcc-admin-content">
        
        <!-- Quick Actions -->
        <div class="hcc-card">
            <div class="hcc-card-header">
                <h3><?php _e( 'Quick Actions', 'hotel-cleaning-calculator-pro' ); ?></h3>
            </div>
            <div class="hcc-card-body">
                <div class="hcc-btn-group">
                    <a href="<?php echo admin_url( 'admin.php?page=hcc-room-types' ); ?>" class="hcc-btn hcc-btn-primary">
                        <span class="dashicons dashicons-admin-multisite"></span>
                        <?php _e( 'Manage Room Types', 'hotel-cleaning-calculator-pro' ); ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=hcc-quotes' ); ?>" class="hcc-btn hcc-btn-secondary">
                        <span class="dashicons dashicons-media-text"></span>
                        <?php _e( 'View Quotes', 'hotel-cleaning-calculator-pro' ); ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=hcc-discounts' ); ?>" class="hcc-btn hcc-btn-success">
                        <span class="dashicons dashicons-tag"></span>
                        <?php _e( 'Manage Discounts', 'hotel-cleaning-calculator-pro' ); ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=hcc-customizer' ); ?>" class="hcc-btn hcc-btn-outline">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        <?php _e( 'Customize Design', 'hotel-cleaning-calculator-pro' ); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Statistics Grid -->
        <div class="hcc-stats-grid">
            
            <!-- Total Quotes -->
            <div class="hcc-stat-card primary">
                <div class="stat-icon">
                    <span class="dashicons dashicons-media-text"></span>
                </div>
                <h3><?php echo esc_html( $stats['total_quotes'] ); ?></h3>
                <p><?php _e( 'Total Quotes', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <!-- Pending Quotes -->
            <div class="hcc-stat-card warning">
                <div class="stat-icon">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <h3><?php echo esc_html( $stats['pending_quotes'] ); ?></h3>
                <p><?php _e( 'Pending Quotes', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <!-- This Month -->
            <div class="hcc-stat-card success">
                <div class="stat-icon">
                    <span class="dashicons dashicons-calendar-alt"></span>
                </div>
                <h3><?php echo esc_html( $stats['month_quotes'] ); ?></h3>
                <p><?php _e( 'This Month', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <!-- Total Revenue -->
            <div class="hcc-stat-card primary">
                <div class="stat-icon">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <?php 
                $calculator = new HCC_Calculator();
                $revenue = $stats['total_revenue'] ?? 0;
                ?>
                <h3><?php echo esc_html( $calculator->format_price( $revenue ) ); ?></h3>
                <p><?php _e( 'Total Revenue', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
        </div>
        
        <!-- Two Column Layout -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <!-- Recent Quotes -->
            <div class="hcc-card">
                <div class="hcc-card-header">
                    <h3><?php _e( 'Recent Quotes', 'hotel-cleaning-calculator-pro' ); ?></h3>
                    <a href="<?php echo admin_url( 'admin.php?page=hcc-quotes' ); ?>" class="hcc-btn hcc-btn-sm hcc-btn-outline">
                        <?php _e( 'View All', 'hotel-cleaning-calculator-pro' ); ?>
                    </a>
                </div>
                <div class="hcc-card-body">
                    <?php
                    $recent_quotes = HCC_Database::get_quotes( array( 'limit' => 5 ) );
                    
                    if ( ! empty( $recent_quotes ) ) :
                    ?>
                        <div class="hcc-table-container">
                            <table class="hcc-table">
                                <thead>
                                    <tr>
                                        <th><?php _e( 'Quote #', 'hotel-cleaning-calculator-pro' ); ?></th>
                                        <th><?php _e( 'Client', 'hotel-cleaning-calculator-pro' ); ?></th>
                                        <th><?php _e( 'Amount', 'hotel-cleaning-calculator-pro' ); ?></th>
                                        <th><?php _e( 'Status', 'hotel-cleaning-calculator-pro' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ( $recent_quotes as $quote ) : ?>
                                        <tr>
                                            <td><?php echo esc_html( $quote->quote_number ); ?></td>
                                            <td><?php echo esc_html( $quote->client_name ); ?></td>
                                            <td><?php echo esc_html( $calculator->format_price( $quote->total_price ) ); ?></td>
                                            <td>
                                                <span class="hcc-badge status-<?php echo esc_attr( $quote->status ); ?>">
                                                    <?php echo esc_html( ucfirst( $quote->status ) ); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p><?php _e( 'No quotes yet.', 'hotel-cleaning-calculator-pro' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- System Info -->
            <div class="hcc-card">
                <div class="hcc-card-header">
                    <h3><?php _e( 'System Information', 'hotel-cleaning-calculator-pro' ); ?></h3>
                </div>
                <div class="hcc-card-body">
                    <table class="widefat" style="border: none;">
                        <tbody>
                            <tr>
                                <td style="width: 50%; padding: 10px 0; border: none;">
                                    <strong><?php _e( 'Plugin Version', 'hotel-cleaning-calculator-pro' ); ?>:</strong>
                                </td>
                                <td style="padding: 10px 0; border: none;"><?php echo HCC_VERSION; ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border: none;">
                                    <strong><?php _e( 'WordPress Version', 'hotel-cleaning-calculator-pro' ); ?>:</strong>
                                </td>
                                <td style="padding: 10px 0; border: none;"><?php echo get_bloginfo( 'version' ); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border: none;">
                                    <strong><?php _e( 'PHP Version', 'hotel-cleaning-calculator-pro' ); ?>:</strong>
                                </td>
                                <td style="padding: 10px 0; border: none;"><?php echo PHP_VERSION; ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border: none;">
                                    <strong><?php _e( 'Active Discounts', 'hotel-cleaning-calculator-pro' ); ?>:</strong>
                                </td>
                                <td style="padding: 10px 0; border: none;"><?php echo esc_html( $discount_stats['active_rules'] ?? 0 ); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border: none;">
                                    <strong><?php _e( 'Room Types', 'hotel-cleaning-calculator-pro' ); ?>:</strong>
                                </td>
                                <td style="padding: 10px 0; border: none;">
                                    <?php 
                                    $room_types = get_option( 'hcc_room_types', array() );
                                    echo count( $room_types );
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                        <h4><?php _e( 'Shortcode', 'hotel-cleaning-calculator-pro' ); ?></h4>
                        <p><?php _e( 'Use this shortcode to display the calculator on any page:', 'hotel-cleaning-calculator-pro' ); ?></p>
                        <input 
                            type="text" 
                            value="[hotel_cleaning_calculator]" 
                            readonly 
                            onclick="this.select();" 
                            style="width: 100%; padding: 10px; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 6px; font-family: monospace;"
                        />
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Getting Started -->
        <div class="hcc-card">
            <div class="hcc-card-header">
                <h3><?php _e( 'Getting Started', 'hotel-cleaning-calculator-pro' ); ?></h3>
            </div>
            <div class="hcc-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    
                    <div>
                        <h4><span class="dashicons dashicons-admin-multisite"></span> <?php _e( 'Step 1: Configure Room Types', 'hotel-cleaning-calculator-pro' ); ?></h4>
                        <p><?php _e( 'Set up your room types with pricing per square meter.', 'hotel-cleaning-calculator-pro' ); ?></p>
                        <a href="<?php echo admin_url( 'admin.php?page=hcc-room-types' ); ?>" class="hcc-btn hcc-btn-sm hcc-btn-primary">
                            <?php _e( 'Manage Room Types', 'hotel-cleaning-calculator-pro' ); ?>
                        </a>
                    </div>
                    
                    <div>
                        <h4><span class="dashicons dashicons-admin-customizer"></span> <?php _e( 'Step 2: Customize Design', 'hotel-cleaning-calculator-pro' ); ?></h4>
                        <p><?php _e( 'Customize colors, fonts, and layout to match your brand.', 'hotel-cleaning-calculator-pro' ); ?></p>
                        <a href="<?php echo admin_url( 'admin.php?page=hcc-customizer' ); ?>" class="hcc-btn hcc-btn-sm hcc-btn-secondary">
                            <?php _e( 'Customize UI', 'hotel-cleaning-calculator-pro' ); ?>
                        </a>
                    </div>
                    
                    <div>
                        <h4><span class="dashicons dashicons-admin-page"></span> <?php _e( 'Step 3: Add to Page', 'hotel-cleaning-calculator-pro' ); ?></h4>
                        <p><?php _e( 'Add the calculator shortcode to any page or post.', 'hotel-cleaning-calculator-pro' ); ?></p>
                        <a href="<?php echo admin_url( 'post-new.php?post_type=page' ); ?>" class="hcc-btn hcc-btn-sm hcc-btn-success">
                            <?php _e( 'Create Page', 'hotel-cleaning-calculator-pro' ); ?>
                        </a>
                    </div>
                    
                    <div>
                        <h4><span class="dashicons dashicons-tag"></span> <?php _e( 'Step 4: Add Discounts (Optional)', 'hotel-cleaning-calculator-pro' ); ?></h4>
                        <p><?php _e( 'Create conditional discount rules to incentivize customers.', 'hotel-cleaning-calculator-pro' ); ?></p>
                        <a href="<?php echo admin_url( 'admin.php?page=hcc-discounts' ); ?>" class="hcc-btn hcc-btn-sm hcc-btn-outline">
                            <?php _e( 'Add Discounts', 'hotel-cleaning-calculator-pro' ); ?>
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
        
    </div>
    
</div>