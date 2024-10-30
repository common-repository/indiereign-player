<?php

require_once("helper.php");

/**
 * IRPlayer widget, to show embed player. It provides facility
 * to show embed player by using widget section or by using
 * shortcode "irplayer"
 *
 * Plugin Name: IRPlayer
 * Plugin URI: http://www.indiereign.com
 * Description: Show video from http://www.indiereign.com
 * Version: 1.0.0
 * Author: indiereign.com
 * Author URI: http://www.indiereign.com
 * License: 
 */

class IRPlayer extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'ir_player', // Base ID
			'IRPlayer', // Name
			array( 'description' => __( 'Show video from www.indiereign.com', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title  = apply_filters( 'widget_title', $instance['title'] );
		$vcode  = apply_filters( 'widget_title', $instance['video_slug'] );
		$width  = apply_filters( 'widget_title', $instance['video_width'] );
		$height = apply_filters( 'widget_title', $instance['video_height'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		if ( ! empty($vcode) ) {
			$ir_player = load_ir_player($vcode);
		}

		echo get_player_iframe($ir_player, $width, $height);
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}

		// IR Video Short Code
		if ( isset( $instance[ 'video_slug' ] ) ) {
			$vcode = $instance[ 'video_slug' ];
		}
		else {
			$vcode = __( '', 'text_domain' );
		}

		// Video Width
		if ( isset( $instance[ 'video_width' ] ) ) {
			$vwidth = $instance[ 'video_width' ];
		}
		else {
			$vwidth = __( '990', 'text_domain' );
		}

		// Video Height
		if ( isset( $instance[ 'video_height' ] ) ) {
			$vheight = $instance[ 'video_height' ];
		}
		else {
			$vheight = __( '560', 'text_domain' );
		}

		$widget_fields = file_get_contents(dirname(__FILE__).'/ir_widget_template.html');

		$form_fields  = $this->get_form_field_html('title', $title, __('Title:'), $widget_fields);
		$form_fields .= $this->get_form_field_html('video_slug', $vcode, __('Video Slug:'), $widget_fields);
		$form_fields .= $this->get_form_field_html('video_width', $vwidth, __('Video Width:'), $widget_fields);
		$form_fields .= $this->get_form_field_html('video_height', $vheight, __('Video Height:'), $widget_fields);
		
		echo $form_fields;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']        = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['video_slug']   = ( ! empty( $new_instance['video_slug'] ) ) ? strip_tags( $new_instance['video_slug'] ) : '';
		$instance['video_width']  = ( ! empty( $new_instance['video_width'] ) ) ? strip_tags( $new_instance['video_width'] ) : '';
		$instance['video_height'] = ( ! empty( $new_instance['video_height'] ) ) ? strip_tags( $new_instance['video_height'] ) : '';

		return $instance;
	}

        /**
         * Create widget form fields
         * 
         * @return html form fields
         */
	private function get_form_field_html($field_name, $field_value, $field_lable, $field_template)
	{
		$form_html = '';

		$f_name  = $this->get_field_name( $field_name );
		$f_id    = $this->get_field_id( $field_name );
		$f_value = esc_attr( $field_value );

		$form_html = sprintf($field_template, $f_name, $field_lable, $f_id, $f_name, $f_value);

		return $form_html;
	}

} // Class End

add_action( 'widgets_init', 'register_irplayer_widget' );
add_shortcode( 'irplayer', 'irplayer_shortcode_handler' );

?>
