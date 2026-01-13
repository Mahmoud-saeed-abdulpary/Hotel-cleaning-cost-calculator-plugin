<?php
/**
 * Translation management functionality
 *
 * @link       https://yourwebsite.com
 * @since      2.0.0
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 */

/**
 * Translation management functionality.
 *
 * Handles all text string customization and translation.
 *
 * @since      2.0.0
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/includes
 * @author     Your Name <email@example.com>
 */
class HCC_Translation {

    /**
     * Default text strings
     *
     * @since    2.0.0
     * @access   private
     * @var      array    $default_strings    Default text strings
     */
    private $default_strings = array();

    /**
     * Initialize translation manager
     *
     * @since    2.0.0
     */
    public function init() {
        $this->set_default_strings();
    }

    /**
     * Set default text strings
     *
     * @since    2.0.0
     */
    private function set_default_strings() {
        $this->default_strings = array(
            // Calculator strings
            'calculator_title'    => __( 'Calculate Cleaning Cost', 'hotel-cleaning-calculator-pro' ),
            'add_room'            => __( 'Add Room', 'hotel-cleaning-calculator-pro' ),
            'remove_room'         => __( 'Remove', 'hotel-cleaning-calculator-pro' ),
            'room_type'           => __( 'Room Type', 'hotel-cleaning-calculator-pro' ),
            'room_area'           => __( 'Area (m²)', 'hotel-cleaning-calculator-pro' ),
            'room_subtotal'       => __( 'Subtotal', 'hotel-cleaning-calculator-pro' ),
            'total'               => __( 'Total Price', 'hotel-cleaning-calculator-pro' ),
            'calculate'           => __( 'Calculate', 'hotel-cleaning-calculator-pro' ),
            'get_quote'           => __( 'Get Quote', 'hotel-cleaning-calculator-pro' ),
            'discount_applied'    => __( 'Discount Applied', 'hotel-cleaning-calculator-pro' ),
            'select_room_type'    => __( 'Select Room Type', 'hotel-cleaning-calculator-pro' ),
            
            // Quote form strings
            'quote_form_title'    => __( 'Request a Quote', 'hotel-cleaning-calculator-pro' ),
            'name'                => __( 'Full Name', 'hotel-cleaning-calculator-pro' ),
            'email'               => __( 'Email Address', 'hotel-cleaning-calculator-pro' ),
            'phone'               => __( 'Phone Number', 'hotel-cleaning-calculator-pro' ),
            'address'             => __( 'Property Address', 'hotel-cleaning-calculator-pro' ),
            'message'             => __( 'Additional Notes', 'hotel-cleaning-calculator-pro' ),
            'submit'              => __( 'Submit Quote Request', 'hotel-cleaning-calculator-pro' ),
            
            // Messages
            'success_message'     => __( 'Thank you! Your quote request has been submitted.', 'hotel-cleaning-calculator-pro' ),
            'error_message'       => __( 'Sorry, there was an error. Please try again.', 'hotel-cleaning-calculator-pro' ),
            'required_fields'     => __( 'Please fill in all required fields.', 'hotel-cleaning-calculator-pro' ),
            'invalid_email'       => __( 'Please enter a valid email address.', 'hotel-cleaning-calculator-pro' ),
            
            // Labels
            'subtotal_label'      => __( 'Subtotal', 'hotel-cleaning-calculator-pro' ),
            'discount_label'      => __( 'Discount', 'hotel-cleaning-calculator-pro' ),
            'total_label'         => __( 'Total', 'hotel-cleaning-calculator-pro' ),
            'room_label'          => __( 'Room', 'hotel-cleaning-calculator-pro' ),
            
            // Buttons
            'add_another_room'    => __( 'Add Another Room', 'hotel-cleaning-calculator-pro' ),
            'clear_all'           => __( 'Clear All', 'hotel-cleaning-calculator-pro' ),
            'apply_discount'      => __( 'Apply Discount', 'hotel-cleaning-calculator-pro' ),
            'discount_code'       => __( 'Discount Code', 'hotel-cleaning-calculator-pro' ),
            
            // Email strings
            'email_subject_admin' => __( 'New Cleaning Quote Request', 'hotel-cleaning-calculator-pro' ),
            'email_subject_client' => __( 'Your Cleaning Quote Request', 'hotel-cleaning-calculator-pro' ),
            'email_greeting'      => __( 'Hello', 'hotel-cleaning-calculator-pro' ),
            'email_footer'        => __( 'Best regards', 'hotel-cleaning-calculator-pro' ),
        );
    }

    /**
     * Get all translatable strings
     *
     * @since    2.0.0
     * @return   array    Array of translatable strings
     */
    public function get_all_strings() {
        $strings = array();
        
        foreach ( $this->default_strings as $key => $default ) {
            $strings[ $key ] = array(
                'key'     => $key,
                'default' => $default,
                'current' => get_option( 'hcc_text_' . $key, $default ),
            );
        }
        
        return $strings;
    }

    /**
     * Get a specific translated string
     *
     * @since    2.0.0
     * @param    string    $key    String key
     * @return   string            Translated string
     */
    public function get_string( $key ) {
        $default = isset( $this->default_strings[ $key ] ) ? $this->default_strings[ $key ] : '';
        return get_option( 'hcc_text_' . $key, $default );
    }

    /**
     * Update a translated string
     *
     * @since    2.0.0
     * @param    string    $key      String key
     * @param    string    $value    New value
     * @return   bool                True on success
     */
    public function update_string( $key, $value ) {
        
        if ( ! isset( $this->default_strings[ $key ] ) ) {
            return false;
        }
        
        update_option( 'hcc_text_' . $key, sanitize_text_field( $value ) );
        
        return true;
    }

    /**
     * Update multiple strings at once
     *
     * @since    2.0.0
     * @param    array    $strings    Array of key => value pairs
     * @return   int                  Number of updated strings
     */
    public function update_strings( $strings ) {
        
        $updated = 0;
        
        foreach ( $strings as $key => $value ) {
            if ( $this->update_string( $key, $value ) ) {
                $updated++;
            }
        }
        
        // Log activity
        if ( $updated > 0 ) {
            HCC_Database::log_activity( 'settings_updated', 'settings', 0, array(
                'setting' => 'translations',
                'count'   => $updated,
            ) );
        }
        
        return $updated;
    }

    /**
     * Reset a string to default
     *
     * @since    2.0.0
     * @param    string    $key    String key
     * @return   bool              True on success
     */
    public function reset_string( $key ) {
        
        if ( ! isset( $this->default_strings[ $key ] ) ) {
            return false;
        }
        
        delete_option( 'hcc_text_' . $key );
        
        return true;
    }

    /**
     * Reset all strings to defaults
     *
     * @since    2.0.0
     * @return   int    Number of reset strings
     */
    public function reset_all_strings() {
        
        $reset = 0;
        
        foreach ( $this->default_strings as $key => $value ) {
            if ( $this->reset_string( $key ) ) {
                $reset++;
            }
        }
        
        return $reset;
    }

    /**
     * Export translations to JSON
     *
     * @since    2.0.0
     * @return   string    JSON string
     */
    public function export_translations() {
        
        $translations = array();
        
        foreach ( $this->default_strings as $key => $default ) {
            $translations[ $key ] = $this->get_string( $key );
        }
        
        return json_encode( $translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    /**
     * Import translations from JSON
     *
     * @since    2.0.0
     * @param    string    $json    JSON string
     * @return   array              Result array
     */
    public function import_translations( $json ) {
        
        $translations = json_decode( $json, true );
        
        if ( empty( $translations ) || ! is_array( $translations ) ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid JSON data', 'hotel-cleaning-calculator-pro' ),
            );
        }
        
        $imported = $this->update_strings( $translations );
        
        return array(
            'success' => true,
            'message' => sprintf(
                __( 'Successfully imported %d translations', 'hotel-cleaning-calculator-pro' ),
                $imported
            ),
            'count'   => $imported,
        );
    }

    /**
     * Get translation groups for organization
     *
     * @since    2.0.0
     * @return   array    Grouped translations
     */
    public function get_grouped_strings() {
        
        $groups = array(
            'calculator' => array(
                'title'   => __( 'Calculator Interface', 'hotel-cleaning-calculator-pro' ),
                'strings' => array(),
            ),
            'quote_form' => array(
                'title'   => __( 'Quote Form', 'hotel-cleaning-calculator-pro' ),
                'strings' => array(),
            ),
            'messages' => array(
                'title'   => __( 'Messages', 'hotel-cleaning-calculator-pro' ),
                'strings' => array(),
            ),
            'labels' => array(
                'title'   => __( 'Labels', 'hotel-cleaning-calculator-pro' ),
                'strings' => array(),
            ),
            'buttons' => array(
                'title'   => __( 'Buttons', 'hotel-cleaning-calculator-pro' ),
                'strings' => array(),
            ),
            'email' => array(
                'title'   => __( 'Email Templates', 'hotel-cleaning-calculator-pro' ),
                'strings' => array(),
            ),
        );
        
        // Categorize strings
        foreach ( $this->default_strings as $key => $default ) {
            
            $group = 'calculator'; // Default group
            
            if ( strpos( $key, 'quote_form' ) !== false || in_array( $key, array( 'name', 'email', 'phone', 'address', 'message', 'submit' ) ) ) {
                $group = 'quote_form';
            } elseif ( strpos( $key, 'message' ) !== false || strpos( $key, 'error' ) !== false || strpos( $key, 'success' ) !== false ) {
                $group = 'messages';
            } elseif ( strpos( $key, 'label' ) !== false ) {
                $group = 'labels';
            } elseif ( strpos( $key, 'button' ) !== false || in_array( $key, array( 'add_room', 'remove_room', 'calculate', 'get_quote', 'apply_discount' ) ) ) {
                $group = 'buttons';
            } elseif ( strpos( $key, 'email' ) !== false ) {
                $group = 'email';
            }
            
            $groups[ $group ]['strings'][ $key ] = array(
                'key'     => $key,
                'default' => $default,
                'current' => $this->get_string( $key ),
            );
        }
        
        return $groups;
    }

    /**
     * Search translations
     *
     * @since    2.0.0
     * @param    string    $search_term    Search term
     * @return   array                     Matching strings
     */
    public function search_strings( $search_term ) {
        
        $search_term = strtolower( $search_term );
        $results = array();
        
        foreach ( $this->default_strings as $key => $default ) {
            $current = $this->get_string( $key );
            
            if ( strpos( strtolower( $key ), $search_term ) !== false ||
                 strpos( strtolower( $default ), $search_term ) !== false ||
                 strpos( strtolower( $current ), $search_term ) !== false ) {
                
                $results[ $key ] = array(
                    'key'     => $key,
                    'default' => $default,
                    'current' => $current,
                );
            }
        }
        
        return $results;
    }

    /**
     * Get translation statistics
     *
     * @since    2.0.0
     * @return   array    Statistics array
     */
    public function get_statistics() {
        
        $total = count( $this->default_strings );
        $customized = 0;
        
        foreach ( $this->default_strings as $key => $default ) {
            $current = get_option( 'hcc_text_' . $key, false );
            if ( $current !== false && $current !== $default ) {
                $customized++;
            }
        }
        
        return array(
            'total'      => $total,
            'customized' => $customized,
            'default'    => $total - $customized,
            'percentage' => $total > 0 ? round( ( $customized / $total ) * 100, 2 ) : 0,
        );
    }

    /**
     * Generate .pot file for translation
     *
     * @since    2.0.0
     * @return   string|false    POT file content or false
     */
    public function generate_pot_file() {
        
        $pot_content = '# Hotel Cleaning Calculator PRO Translation File' . "\n";
        $pot_content .= '# Copyright (C) ' . date( 'Y' ) . "\n";
        $pot_content .= 'msgid ""' . "\n";
        $pot_content .= 'msgstr ""' . "\n";
        $pot_content .= '"Content-Type: text/plain; charset=UTF-8\n"' . "\n";
        $pot_content .= '"Language: en\n"' . "\n\n";
        
        foreach ( $this->default_strings as $key => $default ) {
            $pot_content .= '# Translation key: ' . $key . "\n";
            $pot_content .= 'msgid "' . addslashes( $default ) . '"' . "\n";
            $pot_content .= 'msgstr ""' . "\n\n";
        }
        
        return $pot_content;
    }

    /**
     * Check if translations are RTL (Right-to-Left)
     *
     * @since    2.0.0
     * @return   bool    True if RTL
     */
    public function is_rtl() {
        return is_rtl();
    }

    /**
     * Get available languages
     *
     * @since    2.0.0
     * @return   array    Array of available languages
     */
    public function get_available_languages() {
        
        $languages = array(
            'en_US' => __( 'English (US)', 'hotel-cleaning-calculator-pro' ),
            'ar'    => __( 'Arabic', 'hotel-cleaning-calculator-pro' ),
            'es_ES' => __( 'Spanish', 'hotel-cleaning-calculator-pro' ),
            'fr_FR' => __( 'French', 'hotel-cleaning-calculator-pro' ),
            'de_DE' => __( 'German', 'hotel-cleaning-calculator-pro' ),
            'it_IT' => __( 'Italian', 'hotel-cleaning-calculator-pro' ),
            'pt_BR' => __( 'Portuguese (Brazil)', 'hotel-cleaning-calculator-pro' ),
            'ru_RU' => __( 'Russian', 'hotel-cleaning-calculator-pro' ),
            'zh_CN' => __( 'Chinese (Simplified)', 'hotel-cleaning-calculator-pro' ),
            'ja'    => __( 'Japanese', 'hotel-cleaning-calculator-pro' ),
        );
        
        return $languages;
    }
}