<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Creating the widget 
class wpat_button_widget extends WP_Widget {
  
    function __construct() {
        parent::__construct(
        
        // Base ID of your widget
        'wpat_button_widget', 
        
        // Widget name will appear in UI
        __('Automatic Translator Button', 'auto-translate'), 
        
        // Widget description
        array( 'description' => __( 'Translation button', 'auto-translate' ), ) 
        );
    }
      
    // Creating widget front-end
      
    public function widget( $args, $instance ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme-provided wrapper HTML.
        echo isset( $args['before_widget'] ) ? $args['before_widget'] : '';
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode returns plugin-generated markup.
        echo do_shortcode( '[auto_translate_button]' );
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme-provided wrapper HTML.
        echo isset( $args['after_widget'] ) ? $args['after_widget'] : '';
    }
              
    // Widget Backend 
    public function form( $instance ) {
    }
          
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
    }
     
} 
