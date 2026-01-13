<?php
/**
 * Discounts Management Page
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/partials
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$discount_rules = HCC_Database::get_active_discounts();
$calculator = new HCC_Calculator();
?>

<div class="wrap hcc-admin-wrap">
    <div class="hcc-admin-header">
        <h1><?php _e( 'Discount Rules', 'hotel-cleaning-calculator-pro' ); ?></h1>
        <p><?php _e( 'Create conditional discount rules to incentivize customers', 'hotel-cleaning-calculator-pro' ); ?></p>
    </div>
    
    <div class="hcc-admin-content">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <h2><?php _e( 'Manage Discounts', 'hotel-cleaning-calculator-pro' ); ?></h2>
            <button id="hcc-add-discount" class="hcc-btn hcc-btn-primary">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php _e( 'Add Discount Rule', 'hotel-cleaning-calculator-pro' ); ?>
            </button>
        </div>
        
        <div class="hcc-table-container">
            <table class="hcc-table" id="hcc-discounts-table">
                <thead>
                    <tr>
                        <th><?php _e( 'Rule Name', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Type', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Value', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Priority', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Status', 'hotel-cleaning-calculator-pro' ); ?></th>
                        <th><?php _e( 'Actions', 'hotel-cleaning-calculator-pro' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $discount_rules ) ) : foreach ( $discount_rules as $rule ) : ?>
                        <tr data-id="<?php echo esc_attr( $rule->id ); ?>">
                            <td><strong><?php echo esc_html( $rule->rule_name ); ?></strong></td>
                            <td><?php echo esc_html( ucfirst( $rule->discount_type ) ); ?></td>
                            <td>
                                <?php 
                                echo $rule->discount_type === 'percentage' 
                                    ? esc_html( $rule->discount_value ) . '%' 
                                    : esc_html( $calculator->format_price( $rule->discount_value ) );
                                ?>
                            </td>
                            <td><?php echo esc_html( $rule->priority ); ?></td>
                            <td>
                                <span class="hcc-badge <?php echo $rule->active ? 'status-active' : 'status-rejected'; ?>">
                                    <?php echo $rule->active ? __( 'Active', 'hotel-cleaning-calculator-pro' ) : __( 'Inactive', 'hotel-cleaning-calculator-pro' ); ?>
                                </span>
                            </td>
                            <td>
                                <button class="hcc-btn hcc-btn-sm hcc-btn-secondary hcc-edit-discount" data-id="<?php echo esc_attr( $rule->id ); ?>">
                                    <span class="dashicons dashicons-edit"></span>
                                </button>
                                <button class="hcc-btn hcc-btn-sm hcc-btn-danger hcc-delete-discount" data-id="<?php echo esc_attr( $rule->id ); ?>">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" style="text-align: center; padding: 40px;"><?php _e( 'No discount rules yet. Create your first one!', 'hotel-cleaning-calculator-pro' ); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Discount Modal (Hidden) -->
<div id="hcc-discount-modal" class="hcc-modal" style="display: none;">
    <div class="hcc-modal-backdrop"></div>
    <div class="hcc-modal-content" style="max-width: 800px;">
        <div class="hcc-modal-header">
            <h2><?php _e( 'Discount Rule', 'hotel-cleaning-calculator-pro' ); ?></h2>
            <button class="hcc-modal-close">&times;</button>
        </div>
        <div class="hcc-modal-body">
            <form id="hcc-discount-form">
                <input type="hidden" id="discount-id" value=""/>
                
                <div class="hcc-form-group">
                    <label for="rule-name" class="required"><?php _e( 'Rule Name', 'hotel-cleaning-calculator-pro' ); ?></label>
                    <input type="text" id="rule-name" required/>
                </div>
                
                <div class="hcc-form-row">
                    <div class="hcc-form-group">
                        <label for="discount-type"><?php _e( 'Discount Type', 'hotel-cleaning-calculator-pro' ); ?></label>
                        <select id="discount-type">
                            <option value="percentage"><?php _e( 'Percentage', 'hotel-cleaning-calculator-pro' ); ?></option>
                            <option value="fixed"><?php _e( 'Fixed Amount', 'hotel-cleaning-calculator-pro' ); ?></option>
                        </select>
                    </div>
                    <div class="hcc-form-group">
                        <label for="discount-value" class="required"><?php _e( 'Discount Value', 'hotel-cleaning-calculator-pro' ); ?></label>
                        <input type="number" id="discount-value" step="0.01" min="0" required/>
                    </div>
                </div>
                
                <div class="hcc-form-group">
                    <label><input type="checkbox" class="hcc-condition-toggle" data-target="#min-area" id="enable-min-area"/> <?php _e( 'Minimum Area Condition', 'hotel-cleaning-calculator-pro' ); ?></label>
                    <input type="number" id="min-area" placeholder="<?php _e( 'Minimum m²', 'hotel-cleaning-calculator-pro' ); ?>" disabled style="margin-top: 10px;"/>
                </div>
                
                <div class="hcc-form-row">
                    <div class="hcc-form-group">
                        <label for="priority"><?php _e( 'Priority', 'hotel-cleaning-calculator-pro' ); ?></label>
                        <input type="number" id="priority" value="0"/>
                    </div>
                    <div class="hcc-form-group">
                        <label><input type="checkbox" id="stackable"/> <?php _e( 'Stackable with other discounts', 'hotel-cleaning-calculator-pro' ); ?></label>
                        <label><input type="checkbox" id="discount-active" checked/> <?php _e( 'Active', 'hotel-cleaning-calculator-pro' ); ?></label>
                    </div>
                </div>
                
                <button type="submit" class="hcc-btn hcc-btn-primary"><?php _e( 'Save Discount Rule', 'hotel-cleaning-calculator-pro' ); ?></button>
            </form>
        </div>
    </div>
</div>