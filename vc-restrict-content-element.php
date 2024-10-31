<?php

/**
 * Adds new shortcode "wpb-restrict-content" and registers it to
 * the WPBakery Visual Composer plugin
 *
 */

// If this file is called directly, abort

if (!defined('ABSPATH')) {
    die('Silly human what are you doing here &#128513;');
}

if (!class_exists('vcRestrictContent')) {

    class vcRestrictContent
    {

        /**
         * Main constructor
         *
         */
        public function __construct()
        {
            // Registers the shortcode in WordPress
            add_shortcode('wpb-restrict-content', __CLASS__ . '::output');

            // Map shortcode to Visual Composer
            if (function_exists('vc_lean_map')) {
                vc_lean_map('wpb-restrict-content', __CLASS__ . '::map');
            }
        }

        /**
         * Map shortcode to VC
         *
         * This is an array of all your settings which become the shortcode attributes ($atts)
         * for the output.
         *
         */
        public static function map()
        {
            return array(
                'name' => esc_html__('Restricted Block', 'wpb-restrict-content'),
                'description' => esc_html__('Add new restricted block', 'wpb-restrict-content'),
                'base' => 'wpb-restrict-content',
                'category' => esc_html__('Content', 'wpb-restrict-content'),
                'icon' => plugin_dir_url(__FILE__) . 'assets/img/note.png',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Avaialble for this role:'),
                        'param_name' => 'avaiable_for',
                        'admin_label' => true,
                        'value' => self::getRoles(),
                        //'std' => 'two', // Your default value
                        'description' => esc_html__('Select the role for to allow content.'),
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type' => 'attach_image',
                        'holder' => 'img',
                        'heading' => esc_html__('Image', 'wpb-restrict-content'),
                        'param_name' => 'bgimg',
                        // 'value' => esc_html__( 'Default value', 'wpb-restrict-content' ),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show Heading?', 'wpb-restrict-content' ),
                        'param_name' => 'show_heading',
                        'value'      => array(
                            esc_html__( 'No', 'wpb-restrict-content' )  => 'no',
                            esc_html__( 'Yes', 'wpb-restrict-content' ) => 'yes',
                        ),
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Heading Tag?', 'wpb-restrict-content' ),
                        'param_name' => 'heading_tag',
                        'value'      => self::getHeadingTags(),
                        'dependency' => array( 'element' => 'show_heading', 'value' => 'yes' ),
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type' => 'textfield',
                        'holder' => 'h3',
                        'class' => 'heading-class',
                        'heading' => esc_html__('Heading', 'wpb-restrict-content'),
                        'param_name' => 'heading',
                        //'value' => esc_html__('', 'wpb-restrict-content'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type' => 'textarea_html',
                        'holder' => 'div',
                        'class' => 'wpc-text-class',
                        'heading' => esc_html__('Description', 'wpb-restrict-content'),
                        'param_name' => 'content',
                        //'value' => esc_html__('', 'wpb-restrict-content'),
                        //'description' => esc_html__('To add link highlight text or url and click the chain to apply hyperlink', 'wpb-restrict-content'),
                        // 'admin_label' => false,
                        // 'weight' => 0,
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show Restricted Message?', 'wpb-restrict-content' ),
                        'param_name' => 'show_restricted_message',
                        'value'      => array(
                            esc_html__( 'No', 'wpb-restrict-content' )  => 'no',
                            esc_html__( 'Yes', 'wpb-restrict-content' ) => 'yes',
                        ),
                        'std' => 'no',
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type' => 'textarea',
                        'holder' => 'div',
                        'class' => 'wpc-text-class',
                        'heading' => esc_html__('Restricted Message', 'wpb-restrict-content'),
                        'param_name' => 'restricted_message',
                        'value' => esc_html__('Content is restricted.', 'wpb-restrict-content'),
                        'description' => esc_html__('Add message to display for user with restricted role.', 'wpb-restrict-content'),
                        'dependency' => array( 'element' => 'show_restricted_message', 'value' => 'yes' ),
                        'group' => 'RestrictedBlock',
                    ),
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show Restricted Message to?', 'wpb-restrict-content' ),
                        'param_name' => 'show_restricted_message_to',
                        'value'      => array(
                            esc_html__( 'Non-Logged in user ', 'wpb-restrict-content' ) => '0',
                            esc_html__( 'Logged in user only', 'wpb-restrict-content' )  => '1',
                            esc_html__( 'Both users ', 'wpb-restrict-content' ) => '2',
                        ),
                        'std' => '1',
                        'dependency' => array( 'element' => 'show_restricted_message', 'value' => 'yes' ),
                        'group' => 'RestrictedBlock',
                    ),
                ),
            );
        }

        public static function getRoles()
        {
            global $wp_roles;
            $options = array();
            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }

            $options = $wp_roles->get_names();
            $options = array_flip($options);
            return $options;
        }

        public static function getHeadingTags()
        {
            $heading_tags_arr =  array(
                esc_html__( 'H1', 'wpb-restrict-content' )  => 'h1',
                esc_html__( 'H2', 'wpb-restrict-content' ) => 'h2',
                esc_html__( 'H3', 'wpb-restrict-content' ) => 'h3',
                esc_html__( 'H4', 'wpb-restrict-content' ) => 'h4',
                esc_html__( 'H5', 'wpb-restrict-content' ) => 'h5',
                esc_html__( 'H6', 'wpb-restrict-content' ) => 'h6',
            );
            return $heading_tags_arr;
        }

        /**
         * Shortcode output
         *
         */
        public static function output($atts, $content = null)
        {
            // Extract shortcode attributes (based on the vc_lean_map function - see next function)
            $atts = vc_map_get_attributes( 'wpb-restrict-content', $atts );
            
            $output = '';
            $allowed_roles = 'administrator';
            $user = wp_get_current_user();
            $heading_tags_arr = self::getHeadingTags();
            if (is_user_logged_in() &&
                (in_array($atts['avaiable_for'], (array) $user->roles) || in_array($allowed_roles, (array) $user->roles))) {
                
                // Define output and open element div.
                $output = '<div class="wp-restrict-content__wrap">';

                if ( isset( $atts['bgimg'] )
                    && ! empty( $atts['bgimg'] )
                ) {
                    $img_url = wp_get_attachment_image_src((int)$atts['bgimg'], "full");
                    $img_url = $img_url[0];
                }

                $output .= ($img_url !== '' ? '<img class="wp-restrict-content__image" src="' . $img_url . '">' : '');
                
                // Display custom heading if enabled and set.
                if ( isset( $atts['show_heading'] )
                    && 'yes' === $atts['show_heading']
                    && ! empty( $atts['heading'] )
                ) {
                    if( isset($atts['heading_tag']) && in_array($atts['heading_tag'],$heading_tags_arr) ){
                        $output .= ($atts['heading'] != '' ? '<'.$atts['heading_tag'].' class="wp-restrict-content__heading">' . $atts['heading'] . '</'.$atts['heading_tag'].'>' : '');
                    }else{
                        $output .= '<h2 class="wp-restrict-content__heading">' . esc_html( $atts['heading'] ) . '</h2>';
                    }
                }

                // Display content.
                $output .= '<div class="wp-restrict-content__content">';
                if ( $content ) { $output .= wp_kses_post( $content ); }
                $output .= '</div>';

                // Close element.
                $output .= '</div>';
            }

            if ( (isset($atts['show_restricted_message']) && $atts['show_restricted_message'] === 'yes') && (!in_array($allowed_roles, (array) $user->roles))  )  {
                // Define output and open element div.
                $output = '<div class="wp-restrict-content__wrap ">';
                if( is_user_logged_in() && $atts['show_restricted_message_to'] == 1 ){
                    $output .= '<div class="wp-restrict-content__loggedin">';
                    $output .= wp_kses_post( $atts['restricted_message'] );
                    $output .= '</div>';
                    
                } elseif(!is_user_logged_in() && $atts['show_restricted_message_to'] == 0 ){
                    $output .= '<div class="wp-restrict-content__nonloggedin">';
                    $output .= wp_kses_post( $atts['restricted_message'] );
                    $output .= '</div>';
                } elseif( $atts['show_restricted_message_to'] == 2 ){
                    $output .= '<div class="wp-restrict-content__non__loggedin">';
                    $output .= wp_kses_post( $atts['restricted_message'] );
                    $output .= '</div>';
                }

                // Close element.
                $output .= '</div>';
                
            }
            return $output;
        }
    }
}
new vcRestrictContent;
