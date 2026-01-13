<?php
/**
 * Quotes Management Page
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get filter parameters
$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
$search_query = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

// Build query args
$args = array(
    'limit' => 50,
    'offset' => 0,
);

if ( ! empty( $status_filter ) ) {
    $args['status'] = $status_filter;
}

if ( ! empty( $search_query ) ) {
    $args['search'] = $search_query;
}

// Get quotes
$quotes = HCC_Database::get_quotes( $args );
$quote_stats = ( new HCC_Quotes() )->get_statistics();
$calculator = new HCC_Calculator();
?>

<div class="wrap hcc-admin-wrap">
    
    <div class="hcc-admin-header">
        <h1><?php _e( 'Quote Requests', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Manage and track all cleaning quote requests', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        
        <!-- Statistics Cards -->
        <div class="hcc-stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 30px;">
            <div class="hcc-stat-card primary">
                <div class="stat-icon"><span class="dashicons dashicons-media-text"></span></div>
                <h3><?php echo esc_html( $quote_stats['total'] ?? 0 ); ?></h3>
                <p><?php _e( 'Total Quotes', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <div class="hcc-stat-card warning">
                <div class="stat-icon"><span class="dashicons dashicons-clock"></span></div>
                <h3><?php echo esc_html( $quote_stats['pending'] ?? 0 ); ?></h3>
                <p><?php _e( 'Pending', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <div class="hcc-stat-card success">
                <div class="stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                <h3><?php echo esc_html( $quote_stats['approved'] ?? 0 ); ?></h3>
                <p><?php _e( 'Approved', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
            
            <div class="hcc-stat-card danger">
                <div class="stat-icon"><span class="dashicons dashicons-dismiss"></span></div>
                <h3><?php echo esc_html( $quote_stats['rejected'] ?? 0 ); ?></h3>
                <p><?php _e( 'Rejected', 'hotel-cleaning-calculator-pro' ); ?></p>
            </div>
        </div>
        
        <!-- Filters and Actions Bar -->
        <div class="hcc-card">
            <div class="hcc-card-body" style="padding: 20px;">
                <form method="get" action="">
                    <input type="hidden" name="page" value="hcc-quotes"/>
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                        
                        <!-- Search -->
                        <div style="flex: 1; min-width: 200px;">
                            <input 
                                type="text" 
                                name="s" 
                                id="quote-search"
                                value="<?php echo esc_attr( $search_query ); ?>"
                                placeholder="<?php _e( 'Search by quote #, name, or email...', 'hotel-cleaning-calculator-pro' ); ?>"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            />
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <select name="status" class="hcc-quote-filter" data-filter="status" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                                <option value=""><?php _e( 'All Statuses', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="pending" <?php selected( $status_filter, 'pending' ); ?>><?php _e( 'Pending', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="approved" <?php selected( $status_filter, 'approved' ); ?>><?php _e( 'Approved', 'hotel-cleaning-calculator-pro' ); ?></option>
                                <option value="rejected" <?php selected( $status_filter, 'rejected' ); ?>><?php _e( 'Rejected', 'hotel-cleaning-calculator-pro' ); ?></option>
                            </select>
                        </div>
                        
                        <!-- Filter Button -->
                        <div>
                            <button type="submit" class="hcc-btn hcc-btn-primary">
                                <span class="dashicons dashicons-filter"></span>
                                <?php _e( 'Filter', 'hotel-cleaning-calculator-pro' ); ?>
                            </button>
                        </div>
                        
                        <!-- Reset Button -->
                        <?php if ( ! empty( $status_filter ) || ! empty( $search_query ) ) : ?>
                        <div>
                            <a href="<?php echo admin_url( 'admin.php?page=hcc-quotes' ); ?>" class="hcc-btn hcc-btn-outline">
                                <span class="dashicons dashicons-update"></span>
                                <?php _e( 'Reset', 'hotel-cleaning-calculator-pro' ); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Export Button -->
                        <div style="margin-left: auto;">
                            <button type="button" id="export-quotes" class="hcc-btn hcc-btn-secondary">
                                <span class="dashicons dashicons-download"></span>
                                <?php _e( 'Export to CSV', 'hotel-cleaning-calculator-pro' ); ?>
                            </button>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Bulk Actions -->
        <div class="hcc-card" style="margin-top: 20px;">
            <div class="hcc-card-body" style="padding: 15px 20px; background: #f9fafb;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select id="bulk-action-select" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value=""><?php _e( 'Bulk Actions', 'hotel-cleaning-calculator-pro' ); ?></option>
                        <option value="approved"><?php _e( 'Mark as Approved', 'hotel-cleaning-calculator-pro' ); ?></option>
                        <option value="rejected"><?php _e( 'Mark as Rejected', 'hotel-cleaning-calculator-pro' ); ?></option>
                        <option value="pending"><?php _e( 'Mark as Pending', 'hotel-cleaning-calculator-pro' ); ?></option>
                        <option value="delete"><?php _e( 'Delete', 'hotel-cleaning-calculator-pro' ); ?></option>
                    </select>
                    <button type="button" id="apply-bulk-action" class="hcc-btn hcc-btn-secondary" disabled>
                        <?php _e( 'Apply', 'hotel-cleaning-calculator-pro' ); ?> <span class="count"></span>
                    </button>
                    <span style="color: #6b7280; font-size: 13px; margin-left: 10px;">
                        <?php printf( __( '%d quotes found', 'hotel-cleaning-calculator-pro' ), count( $quotes ) ); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Quotes Table -->
        <div class="hcc-table-container" style="margin-top: 20px;">
            <table class="hcc-table" id="hcc-quotes-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all-quotes"/>
                        </th>
                        <th><?php _e( 'Quote #', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Client', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Email', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Total Area', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Total Price', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Status', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Date', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Actions', 'hotel-cleaning-calculator-pro' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $quotes ) ) : ?>
                        <?php foreach ( $quotes as $quote ) : ?>
                            <tr data-quote-id="<?php echo esc_attr( $quote->id ); ?>" data-status="<?php echo esc_attr( $quote->status ); ?>">
                                
                                <!-- Checkbox -->
                                <td>
                                    <input type="checkbox" class="quote-checkbox" value="<?php echo esc_attr( $quote->id ); ?>"/>
                                </td>
                                
                                <!-- Quote Number -->
                                <td>
                                    <strong><?php echo esc_html( $quote->quote_number ); ?></strong>
                                </td>
                                
                                <!-- Client Name -->
                                <td><?php echo esc_html( $quote->client_name ); ?></td>
                                
                                <!-- Email -->
                                <td>
                                    <a href="mailto:<?php echo esc_attr( $quote->client_email ); ?>">
                                        <?php echo esc_html( $quote->client_email ); ?>
                                    </a>
                                </td>
                                
                                <!-- Total Area -->
                                <td><?php echo esc_html( $quote->total_area ); ?> m²</td>
                                
                                <!-- Total Price -->
                                <td>
                                    <strong style="color: #2563eb; font-size: 16px;">
                                        <?php echo esc_html( $calculator->format_price( $quote->total_price ) ); ?>
                                    </strong>
                                    <?php if ( $quote->discount_amount > 0 ) : ?>
                                        <br/>
                                        <small style="color: #10b981;">
                                            <?php printf( __( '-%s discount', 'hotel-cleaning-calculator-pro' ), $calculator->format_price( $quote->discount_amount ) ); ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Status -->
                                <td>
                                    <select class="hcc-quote-status" data-original-status="<?php echo esc_attr( $quote->status ); ?>" style="padding: 5px 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                                        <option value="pending" <?php selected( $quote->status, 'pending' ); ?>><?php _e( 'Pending', 'hotel-cleaning-calculator-pro' ); ?></option>
                                        <option value="approved" <?php selected( $quote->status, 'approved' ); ?>><?php _e( 'Approved', 'hotel-cleaning-calculator-pro' ); ?></option>
                                        <option value="rejected" <?php selected( $quote->status, 'rejected' ); ?>><?php _e( 'Rejected', 'hotel-cleaning-calculator-pro' ); ?></option>
                                    </select>
                                </td>
                                
                                <!-- Date -->
                                <td>
                                    <?php echo date_i18n( get_option( 'date_format' ), strtotime( $quote->created_at ) ); ?>
                                    <br/>
                                    <small style="color: #6b7280;">
                                        <?php echo date_i18n( get_option( 'time_format' ), strtotime( $quote->created_at ) ); ?>
                                    </small>
                                </td>
                                
                                <!-- Actions -->
                                <td>
                                    <div class="hcc-btn-group" style="gap: 5px;">
                                        <button 
                                            type="button" 
                                            class="hcc-btn hcc-btn-sm hcc-btn-primary hcc-view-quote" 
                                            data-quote-id="<?php echo esc_attr( $quote->id ); ?>"
                                            title="<?php _e( 'View Details', 'hotel-cleaning-calculator-pro' ); ?>"
                                        >
                                            <span class="dashicons dashicons-visibility"></span>
                                        </button>
                                        <button 
                                            type="button" 
                                            class="hcc-btn hcc-btn-sm hcc-btn-danger hcc-delete-quote"
                                            data-confirm="<?php _e( 'Are you sure you want to delete this quote?', 'hotel-cleaning-calculator-pro' ); ?>"
                                            title="<?php _e( 'Delete', 'hotel-cleaning-calculator-pro' ); ?>"
                                        >
                                            <span class="dashicons dashicons-trash"></span>
                                        </button>
                                    </div>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 60px 20px;">
                                <span class="dashicons dashicons-media-text" style="font-size: 64px; color: #d1d5db; display: block; margin-bottom: 20px;"></span>
                                <h3 style="color: #6b7280; margin: 0 0 10px 0;">
                                    <?php _e( 'No quotes found', 'hotel-cleaning-calculator-pro' ); ?>
                                </h3>
                                <p style="color: #9ca3af; margin: 0;">
                                    <?php 
                                    if ( ! empty( $status_filter ) || ! empty( $search_query ) ) {
                                        _e( 'Try adjusting your filters.', 'hotel-cleaning-calculator-pro' );
                                    } else {
                                        _e( 'Quotes will appear here when customers submit quote requests.', 'hotel-cleaning-calculator-pro' );
                                    }
                                    ?>
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Helpful Tips -->
        <?php if ( empty( $quotes ) && empty( $status_filter ) && empty( $search_query ) ) : ?>
        <div class="hcc-card" style="margin-top: 30px;">
            <div class="hcc-card-header">
                <h3><?php _e( 'Getting Quote Requests', 'hotel-cleaning-calculator-pro' ); ?></h3>
            </div>
            <div class="hcc-card-body">
                <p><?php _e( 'To start receiving quote requests, make sure you have:', 'hotel-cleaning-calculator-pro' ); ?></p>
                <ol style="margin-left: 20px;">
                    <li><?php _e( 'Added the calculator to a page using the shortcode', 'hotel-cleaning-calculator-pro' ); ?>: <code>[hotel_cleaning_calculator]</code></li>
                    <li><?php _e( 'Configured your room types with pricing', 'hotel-cleaning-calculator-pro' ); ?></li>
                    <li><?php _e( 'Set up email notifications in Settings', 'hotel-cleaning-calculator-pro' ); ?></li>
                </ol>
                <div style="margin-top: 20px;">
                    <a href="<?php echo admin_url( 'admin.php?page=hcc-room-types' ); ?>" class="hcc-btn hcc-btn-primary">
                        <?php _e( 'Configure Room Types', 'hotel-cleaning-calculator-pro' ); ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=hcc-settings' ); ?>" class="hcc-btn hcc-btn-secondary">
                        <?php _e( 'Go to Settings', 'hotel-cleaning-calculator-pro' ); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
</div>

<style>
/* Additional styles for quotes page */
.hcc-quote-status {
    font-size: 13px;
}
.hcc-quote-status option[value="pending"] {
    background: #fef3c7;
}
.hcc-quote-status option[value="approved"] {
    background: #d1fae5;
}
.hcc-quote-status option[value="rejected"] {
    background: #fee2e2;
}
#hcc-quotes-table tbody tr:hover {
    background: #f9fafb;
}
</style>