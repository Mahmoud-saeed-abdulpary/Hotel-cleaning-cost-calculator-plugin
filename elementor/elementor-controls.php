<?php
/**
 * Elementor Widget Controls Configuration
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/elementor
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Controls Configuration
 *
 * @since 2.0.0
 */
class HCC_Elementor_Controls {

    /**
     * Register all widget controls
     *
     * @since    2.0.0
     * @param    object    $widget    Widget instance
     */
    public static function register_controls( $widget ) {
        
        // Content Tab
        self::register_content_section( $widget );
        self::register_display_settings_section( $widget );
        
        // Style Tab
        self::register_style_colors_section( $widget );
        self::register_style_typography_section( $widget );
        self::register_style_spacing_section( $widget );
        
        // Advanced Tab
        self::register_advanced_section( $widget );
    }

    /**
     * Register Content Section
     *
     * @since    2.0.0
     * @param    object    $widget    Widget instance
     */
    private static function register_content_section( $widget ) {
        
        $widget->start_controls_section(
            'content_section',
            [
                'label' => __( 'Calculator Settings', 'hotel-cleaning-calculator-pro' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Theme Preset
        $widget->add_control(
            'theme',
            [
                'label'   => __( 'Theme Preset', 'hotel-cleaning-calculator-pro' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'      => __( 'Default (Blue)', 'hotel-cleaning-calculator-pro' ),
                    'modern_dark'  => __( 'Modern Dark', 'hotel-cleaning-calculator-pro' ),
                    'professional' => __( 'Professional', 'hotel-cleaning-calculator-pro' ),
                    'elegant'      => __( 'Elegant', 'hotel-cleaning-calculator-pro' ),
                    'minimalist'   => __( 'Minimalist', 'hotel-cleaning-calculator-pro' ),
                ],
                'description' => __( 'Choose a pre-designed theme or customize colors in Style tab', 'hotel-cleaning-calculator-pro' ),
            ]
        );

        // Show Logo
        $widget->add_control(
            'show_logo',
            [
                'label'        => __( 'Show Company Logo', 'hotel-cleaning-calculator-pro' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', 'hotel-cleaning-calculator-pro' ),
                'label_off'    => __( 'Hide', 'hotel-cleaning-calculator-pro' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // Show Title
        $widget->add_control(
            'show_title',
            [
                'label'        => __( 'Show Calculator Title', 'hotel-cleaning-calculator-pro' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', 'hotel-cleaning-calculator-pro' ),
                'label_off'    => __( 'Hide', 'hotel-cleaning-calculator-pro' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // Custom Class
        $widget->add_control(
            'custom_class',
            [
                'label'       => __( 'Custom CSS Class', 'hotel-cleaning-calculator-pro' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'my-custom-class', 'hotel-cleaning-calculator-pro' ),
                'description' => __( 'Add custom CSS class for additional styling', 'hotel-cleaning-calculator-pro' ),
            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Register Display Settings Section
     *
     * @since    2.0.0
     * @param    object    $widget    Widget instance
     */
    private static function register_display_settings_section( $widget ) {
        
        $widget->start_controls_section(
            'display_settings_section',
            [
                'label' => __( 'Display Options', 'hotel-cleaning-calculator-pro' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Enable Quote Form
        $widget->add_control(
            'enable_quote_form',
            [
                'label'        => __( 'Enable Quote Form', 'hotel-cleaning-calculator-pro' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'hotel-cleaning-calculator-pro' ),
                'label_off'    => __( 'No', 'hotel-cleaning-calculator-pro' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'description'  => __( 'Show quote request form below calculator', 'hotel-cleaning-calculator-pro' ),
            ]
        );

        // Enable Discount Codes
        $widget->add_control(
            'enable_discount_codes',
            [
                'label'        => __( 'Enable Discount Codes', 'hotel-cleaning-calculator-pro' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'hotel-cleaning-calculator-pro' ),
                'label_off'    => __( 'No', 'hotel-cleaning-calculator-pro' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // Show Powered By
        $widget->add_control(
            'show_powered_by',
            [
                'label'        => __( 'Show "Powered By" Credit', 'hotel-cleaning-calculator-pro' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', 'hotel-cleaning-calculator-pro' ),
                'label_off'    => __( 'Hide', 'hotel-cleaning-calculator-pro' ),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        // Initial Rooms Count
        $widget->add_control(
            'initial_rooms',
            [
                'label'   => __( 'Initial Number of Rooms', 'hotel-cleaning-calculator-pro' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 10,
                'step'    => 1,
                'default' => 1,
            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Register Style Colors Section
     *
     * @since    2.0.0
     * @param    object    $widget    Widget instance
     */
    private static function register_style_colors_section( $widget ) {
        
        $widget->start_controls_section(
            'style_colors_section',
            [
                'label' => __( 'Colors', 'hotel-cleaning-calculator-pro' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Override Theme Colors
        $widget->add_control(
            'override_colors',
            [
                'label'        => __( 'Override Theme Colors', 'hotel-cleaning-calculator-pro' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'hotel-cleaning-calculator-pro' ),
                'label_off'    => __( 'No', 'hotel-cleaning-calculator-pro' ),
                'return_value' => 'yes',
                'default'      => 'no',
                'description'  => __( 'Customize colors independently from theme preset', 'hotel-cleaning-calculator-pro' ),
            ]
        );

        // Primary Color
        $widget->add_control(
            'primary_color',
            [
                'label'     => __( 'Primary Color', 'hotel-cleaning-calculator-pro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#2563eb',
                'selectors' => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-primary-color: {{VALUE}}',
                ],
                'condition' => [
                    'override_colors' => 'yes',
                ],
            ]
        );

        // Secondary Color
        $widget->add_control(
            'secondary_color',
            [
                'label'     => __( 'Secondary Color', 'hotel-cleaning-calculator-pro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#64748b',
                'selectors' => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-secondary-color: {{VALUE}}',
                ],
                'condition' => [
                    'override_colors' => 'yes',
                ],
            ]
        );

        // Accent Color
        $widget->add_control(
            'accent_color',
            [
                'label'     => __( 'Accent Color', 'hotel-cleaning-calculator-pro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#10b981',
                'selectors' => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-accent-color: {{VALUE}}',
                ],
                'condition' => [
                    'override_colors' => 'yes',
                ],
            ]
        );

        // Background Color
        $widget->add_control(
            'background_color',
            [
                'label'     => __( 'Background Color', 'hotel-cleaning-calculator-pro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-background-color: {{VALUE}}; background-color: {{VALUE}}',
                ],
                'condition' => [
                    'override_colors' => 'yes',
                ],
            ]
        );

        // Text Color
        $widget->add_control(
            'text_color',
            [
                'label'     => __( 'Text Color', 'hotel-cleaning-calculator-pro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#1e293b',
                'selectors' => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-text-color: {{VALUE}}; color: {{VALUE}}',
                ],
                'condition' => [
                    'override_colors' => 'yes',
                ],
            ]
        );

        // Border Color
        $widget->add_control(
            'border_color',
            [
                'label'     => __( 'Border Color', 'hotel-cleaning-calculator-pro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#e2e8f0',
                'selectors' => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-border-color: {{VALUE}}',
                ],
                'condition' => [
                    'override_colors' => 'yes',
                ],
            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Register Style Typography Section
     *
     * @since    2.0.0
     * @param    object    $widget    Widget instance
     */
    private static function register_style_typography_section( $widget ) {
        
        $widget->start_controls_section(
            'style_typography_section',
            [
                'label' => __( 'Typography', 'hotel-cleaning-calculator-pro' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Font Family
        $widget->add_control(
            'font_family',
            [
                'label'   => __( 'Font Family', 'hotel-cleaning-calculator-pro' ),
                'type'    => \Elementor\Controls_Manager::FONT,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => 'font-family: {{VALUE}}',
                ],
            ]
        );

        // Font Size
        $widget->add_control(
            'font_size',
            [
                'label'      => __( 'Font Size', 'hotel-cleaning-calculator-pro' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [
                        'min' => 12,
                        'max' => 24,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-font-size: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        // Heading Typography
        $widget->add_control(
            'heading_typography',
            [
                'label'     => __( 'Heading Typography', 'hotel-cleaning-calculator-pro' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        // Heading Font Size
        $widget->add_control(
            'heading_font_size',
            [
                'label'      => __( 'Heading Size', 'hotel-cleaning-calculator-pro' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [
                        'min' => 18,
                        'max' => 48,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .hcc-calculator-title' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Register Style Spacing Section
     *
     * @since    2.0.0
     * @param    object    $widget    Widget instance
     */
    private static function register_style_spacing_section( $widget ) {
        
        $widget->start_controls_section(
            'style_spacing_section',
            [
                'label' => __( 'Spacing & Layout', 'hotel-cleaning-calculator-pro' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Border Radius
        $widget->add_control(
            'border_radius',
            [
                'label'      => __( 'Border Radius', 'hotel-cleaning-calculator-pro' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => '--hcc-border-radius: {{SIZE}}{{UNIT}}; border-radius: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .hcc-room-card' => 'border-radius: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .hcc-btn' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        // Padding
        $widget->add_responsive_control(
            'calculator_padding',
            [
                'label'      => __( 'Calculator Padding', 'hotel-cleaning-calculator-pro' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 30,
                    'right'  => 30,
                    'bottom' => 30,
                    'left'   => 30,
                    'unit'   => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        // Margin
        $widget->add_responsive_control(
            'calculator_margin',
            [
                'label'      => __( 'Calculator Margin', 'hotel-cleaning-calculator-pro' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .hcc-calculator-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        // Box Shadow
        $widget->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'calculator_box_shadow',
                'label'    => __( 'Box Shadow', 'hotel-cleaning-calculator-pro' ),
                'selector' => '{{WRAPPER}} .hcc-calculator-wrapper',
            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Register Advanced Section
     *
     * @since    2.0.0
     * @param    object    $widget    Widget instance
     */
    private static function register_advanced_section( $widget ) {
        
        $widget->start_controls_section(
            'advanced_section',
            [
                'label' => __( 'Advanced', 'hotel-cleaning-calculator-pro' ),
                'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
            ]
        );

        // Custom CSS
        $widget->add_control(
            'custom_css',
            [
                'label'       => __( 'Custom CSS', 'hotel-cleaning-calculator-pro' ),
                'type'        => \Elementor\Controls_Manager::CODE,
                'language'    => 'css',
                'rows'        => 10,
                'description' => __( 'Add custom CSS for this calculator instance', 'hotel-cleaning-calculator-pro' ),
            ]
        );

        // Animation
        $widget->add_control(
            'entrance_animation',
            [
                'label'   => __( 'Entrance Animation', 'hotel-cleaning-calculator-pro' ),
                'type'    => \Elementor\Controls_Manager::ANIMATION,
                'default' => '',
            ]
        );

        // Help Documentation
        $widget->add_control(
            'help_documentation',
            [
                'type'            => \Elementor\Controls_Manager::RAW_HTML,
                'raw'             => sprintf(
                    __( 'Need help? Check out the <a href="%s" target="_blank">documentation</a> or <a href="%s">manage room types</a> in the admin panel.', 'hotel-cleaning-calculator-pro' ),
                    'https://yourwebsite.com/docs',
                    admin_url( 'admin.php?page=hcc-room-types' )
                ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $widget->end_controls_section();
    }
}