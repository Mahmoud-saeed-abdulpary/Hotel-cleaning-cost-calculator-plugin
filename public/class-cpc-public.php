<?php
/**
 * The public-facing functionality of the plugin - FIXED VERSION
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/public
 */

class CPC_Public {
    
    /**
     * The ID of this plugin
     */
    private $plugin_name;
    
    /**
     * The version of this plugin
     */
    private $version;
    
    /**
     * Track if assets are already enqueued
     */
    private static $assets_enqueued = false;
    
    /**
     * Initialize the class and set its properties
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the public-facing side
     */
    public function enqueue_styles() {
        // Check if we should load assets
        if (!$this->should_load_assets()) {
            return;
        }
        
        // Prevent double loading
        if (self::$assets_enqueued) {
            return;
        }
        
        wp_enqueue_style(
            $this->plugin_name,
            CPC_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            $this->version,
            'all'
        );
        
        // Add inline custom colors
        $this->add_custom_colors();
    }
    
    /**
     * Register the JavaScript for the public-facing side
     */
    public function enqueue_scripts() {
        // Check if we should load assets
        if (!$this->should_load_assets()) {
            return;
        }
        
        // Prevent double loading
        if (self::$assets_enqueued) {
            return;
        }
        
        self::$assets_enqueued = true;
        
        wp_enqueue_script(
            $this->plugin_name,
            CPC_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            $this->version,
            true
        );
        
        wp_localize_script($this->plugin_name, 'cpcFrontend', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cpc_frontend_nonce'),
            'currency' => get_option('cpc_currency', 'EUR'),
            'formDisplay' => get_option('cpc_quote_form_display', 'modal'),
            'strings' => array(
                'addRoom' => __('Please add at least one room', 'cleaning-price-calculator'),
                'removeRoom' => __('Remove', 'cleaning-price-calculator'),
                'selectRoomType' => __('Select Room Type', 'cleaning-price-calculator'),
                'area' => __('Area (m²)', 'cleaning-price-calculator'),
                'subtotal' => __('Subtotal', 'cleaning-price-calculator'),
                'total' => __('Total Price', 'cleaning-price-calculator'),
                'requiredField' => __('Please fill in all required fields', 'cleaning-price-calculator'),
                'invalidEmail' => __('Please enter a valid email address', 'cleaning-price-calculator'),
                'submitting' => __('Submitting...', 'cleaning-price-calculator'),
                'sending' => __('Sending your quote request...', 'cleaning-price-calculator'),
                'success' => __('Quote submitted successfully! We will contact you soon.', 'cleaning-price-calculator'),
                'error' => __('An error occurred. Please try again.', 'cleaning-price-calculator'),
            ),
        ));
    }
    
    /**
     * Check if we should load assets
     */
    private function should_load_assets() {
        // Always load on pages (not posts)
        if (is_page()) {
            return true;
        }
        
        // Check if current post/page has the shortcode
        global $post;
        if (is_a($post, 'WP_Post')) {
            // Check post content
            if (has_shortcode($post->post_content, 'cleaning_price_calculator')) {
                return true;
            }
            
            // Check if it's in a widget or page builder
            if (is_active_widget(false, false, 'text') || 
                did_action('elementor/loaded') || 
                function_exists('et_pb_is_pagebuilder_used')) {
                return true;
            }
        }
        
        // Check if shortcode exists anywhere in the content
        global $wp_query;
        if (isset($wp_query->posts) && is_array($wp_query->posts)) {
            foreach ($wp_query->posts as $p) {
                if (stripos($p->post_content, '[cleaning_price_calculator') !== false) {
                    return true;
                }
            }
        }
        
        // Always load on home page to be safe
        if (is_front_page() || is_home()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Add custom color CSS
     */
    private function add_custom_colors() {
        $primary_color = get_option('cpc_primary_color', '#2563eb');
        $button_color = get_option('cpc_button_color', '#10b981');
        $accent_color = get_option('cpc_accent_color', '#f59e0b');
        
        $custom_css = "
            .cpc-calculator {
                --cpc-primary: {$primary_color};
                --cpc-button: {$button_color};
                --cpc-accent: {$accent_color};
            }
        ";
        
        wp_add_inline_style($this->plugin_name, $custom_css);
    }
    
    /**
     * Force load assets - called by shortcode
     */
    public static function force_enqueue_assets() {
        // This will be called from the shortcode to ensure assets are loaded
        add_filter('wp_enqueue_scripts', function() {
            return true;
        });
    }
}