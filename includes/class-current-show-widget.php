<?php

/* Current Show Widget - (DJ On Air)
 * Displays the current on-air Show / DJ
 * Since 2.1.1
 */

// note: widget class name to remain unchanged for backwards compatibility
class DJ_Widget extends WP_Widget {

	// --- use __contruct instead of DJ_Widget ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'DJ_Widget',
			'description' => __( 'The currently playing on-air Show.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Current Show On-Air', 'radio-station' );
		parent::__construct( 'DJ_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );

		// 2.3.3: set time format default to plugin setting
		$title = $instance['title'];
		$display_djs = isset( $instance['show_desc'] ) ? $instance['display_djs'] : false;
		$djavatar = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$default = isset( $instance['default'] ) ? $instance['default'] : __( 'No Show scheduled for this time.', 'radio-station' );
		$link = isset( $instance['link'] ) ? $instance['link'] : false;
		$time = isset( $instance['time'] ) ? $instance['time'] : '';
		$show_sched = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;
		$show_playlist = isset( $instance['show_playlist'] ) ? $instance['show_playlist'] : false;
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false;
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false;

		// 2.2.4: added title position, avatar width and DJ link options
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.3.3.8: added show encore text display option
		$title_position = isset( $instance['title_position'] ) ? $instance['title_position'] : 'below';
		$avatar_width = isset( $instance['avatar_width'] ) ? $instance['avatar_width'] : '';
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';
		$show_encore = isset( $instance['show_encore'] ) ? $instance['show_encore'] : true;
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : false;
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : '';

		// 2.3.0: convert template style code to strings
		// 2.3.2: added AJAX load option field
		$fields = '
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'ajax' ) ) . '">
				<select id="' .esc_attr( $this->get_field_id( 'ajax' ) ) . '" name="' . esc_attr( $this->get_field_name( 'ajax' ) ) . '">
					<option value="" ' . selected( $ajax, '', false ) . '>' . esc_html( __( 'Default', 'radio-station' ) ) . '</option>
					<option value="on" ' . selected( $ajax, 'on', false ) . '>' . esc_html( __( 'On', 'radio-station' ) ) . '</option>
					<option value="off" ' . selected( $ajax, 'off', false ) . '>' . esc_html( __( 'Off', 'radio-station' ) ) . '</option>
				</select>
				' . esc_html( __( 'AJAX Load Widget?', 'radio-station' ) ) . '
			</label>
        </p>

        <p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
			' . esc_html( __( 'Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'link' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link' ) ) . '" type="checkbox" ' . checked( $link, true, false ) . '>
				' . esc_html( __( 'Link the title to the Show page', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'title_position' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'title_position' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title_position' ) ) . '">';

		$positions = array(
			'above' => __( 'Above', 'radio-station' ),
			'left'  => __( 'Left', 'radio-station' ),
			'right' => __( 'Right', 'radio-station' ),
			'below' => __( 'Below', 'radio-station' ),
		);
		foreach ( $positions as $position => $label ) {
			$fields .= '<option value="' . esc_attr( $position ) . '" ' . selected( $title_position, $position, false ) . '>' . esc_html( $label ) . '</option>';
		}

		$fields .= '</select>
				' . esc_html( __( 'Show Title Position (relative to Avatar)', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '" name="' . esc_attr( $this->get_field_name( 'djavatar' ) ) . '" type="checkbox" ' . checked( $djavatar, true, false ) . '>
				' . esc_html( __( 'Display Show Avatar', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '">
				' . esc_html( __( 'Avatar Width', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '" name="' . esc_attr( $this->get_field_name( 'avatar_width' ) ) . '" type="text" value="' . esc_attr( $avatar_width ) . '">
			</label>
			<small>' . esc_html( __( 'Width of Show Avatar (in pixels, default full width)', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'display_djs' ) ) . '" type="checkbox" ' . checked( $display_djs, true, false ) . '>
				' . esc_html( __( 'Display names of the DJs on the Show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link_djs' ) ) . '" type="checkbox" ' . checked( $link_djs, true, false ) . '>
				' . esc_html( __( 'Link Host/DJ names to author pages', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_sched' ) ) . '" type="checkbox" ' . checked( $show_sched, true, false ) . '>
				' . esc_html( __( 'Display schedule info for this show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_all_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_all_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_all_sched' ) ) . '" type="checkbox" ' . checked( $show_all_sched, true, false ) . '>
				' . esc_html( __( 'Display multiple schedules (if show airs more than once per week)', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_encore' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_encore' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_encore' ) ) . '" type="checkbox" ' . checked( $show_encore, true, false ) . '>
				' . esc_html( __( 'Display encore presentation text for Show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_desc' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_desc' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_desc' ) ) . '" type="checkbox" ' . checked( $show_desc, true, false ) . '>
				' . esc_html( __( 'Display description of show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_playlist' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_playlist' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_playlist' ) ) . '" type="checkbox" ' . checked( $show_playlist, true, false ) . '>
				' . esc_html( __( "Display link to show's playlist", 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
				' . esc_html( __( 'No Show Display Text', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="text" value="' . esc_attr( $default ) . '">
			</label>
			<small>' . esc_html( __( 'Text to display if no Show is scheduled for the current time.', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'time' ) ) . '">' . esc_html( __( 'Time Format', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'time' ) ) . '" name="' . esc_attr( $this->get_field_name( 'time' ) ) . '">
					<option value="" ' . selected( $time, '', false ) . '>' . esc_html( __( 'Default', 'radio-station' ) ) . '</option>
					<option value="12" ' . selected( $time, 12, false ) . '>' . esc_html( __( '12 Hour', 'radio-station' ) ) . '</option>
					<option value="24" ' . selected( $time, 24, false ) . '>' . esc_html( __( '24 Hour', 'radio-station' ) ) . '</option>
				</select>
			</label>
			<br />
			<small>' . esc_html( __( 'Choose time format for displayed schedules', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '">
			<input id="' .esc_attr( $this->get_field_id( 'countdown' ) ) . '" name="' . esc_attr( $this->get_field_name( 'countdown' ) ) . '" type="checkbox" ' . checked( $countdown, true, false ) . '>
				' . esc_html( __( 'Display Countdown Timer', 'radio-station' ) ) . '
			</label>
        </p>';

		// --- filter and output ---
		// 2.3.0: added field filter for extra fields
		// 2.3.2: fix for second and third filter arguments
		$fields = apply_filters( 'radio_station_current_show_widget_fields', $fields, $this, $instance );
		echo $fields;
	}

	// --- update widget instance values ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// 2.2.7: fix checkbox value saving
		$instance['title'] = $new_instance['title'];
		$instance['display_djs'] = isset( $new_instance['display_djs'] ) ? 1 : 0;
		$instance['djavatar'] = isset( $new_instance['djavatar'] ) ? 1 : 0;
		$instance['link'] = isset( $new_instance['link'] ) ? 1 : 0;
		$instance['default'] = $new_instance['default'];
		$instance['time'] = $new_instance['time'];
		$instance['show_sched'] = isset( $new_instance['show_sched'] ) ? 1 : 0;
		$instance['show_playlist'] = isset( $new_instance['show_playlist'] ) ? 1 : 0;
		$instance['show_all_sched'] = isset( $new_instance['show_all_sched'] ) ? 1 : 0;
		$instance['show_desc'] = isset( $new_instance['show_desc'] ) ? 1 : 0;

		// 2.2.4: added title position and avatar width settings
		// 2.3.0: added countdown display option
		// 2.3.2: added ajax load option
		$instance['title_position'] = $new_instance['title_position'];
		$instance['avatar_width'] = $new_instance['avatar_width'];
		$instance['link_djs'] = isset( $new_instance['link_djs'] ) ? 1 : 0;
		$instance['show_encore'] = isset( $new_instance['show_encore'] ) ? 1 : 0;
		$instance['countdown'] = isset( $new_instance['countdown'] ) ? 1 : 0;
		$instance['ajax'] = isset( $new_instance['ajax'] ) ? $new_instance['ajax'] : 0;

		// 2.3.0: filter widget update instance
		$instance = apply_filters( 'radio_station_current_show_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- widget output ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.0: added unique widget id
		if ( !isset( $radio_station_data['widgets']['current-show'] ) ) {
			$id = $radio_station_data['widgets']['current-show'] = 0;
		} else {
			$id = $radio_station_data['widgets']['current-show']++;
		}

		// 2.3.0: filter widget_title whether empty or not
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );
		$display_djs = $instance['display_djs'];
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
		$default = empty( $instance['default'] ) ? '' : $instance['default'];
		$time = empty( $instance['time'] ) ? '' : $instance['time'];
		$show_sched = $instance['show_sched'];
		$show_playlist = $instance['show_playlist'];

		// 2.3.2: fix old false values to use 0 for shortcodes
		// keep the default settings for people updating from 1.6.2 or earlier
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : 0;
		// keep the default settings for people updating from 2.0.12 or earlier
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : 0;
		$encore = isset( $instance['show_encore'] ) ? $instance['show_encore'] : 0;

		// 2.2.4: added title position, avatar width and DJ link settings
		// 2.3.2: added AJAX load option
		$position = empty( $instance['title_position'] ) ? 'bottom' : $instance['title_position'];
		$width = empty( $instance['avatar_width'] ) ? '' : $instance['avatar_width'];
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : 0;
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : 0;

		// --- set shortcode attributes ---
		// 2.3.0: map widget options to shortcode attributes
		// 2.3.2: added AJAX load option
		$atts = array(
			// --- legacy widget options ---
			'title'          => $title,
			'display_hosts'  => $display_djs,
			'show_avatar'    => $djavatar,
			'show_link'      => $link,
			'default_name'   => $default,
			'time'           => $time,
			'show_sched'     => $show_sched,
			'show_playlist'  => $show_playlist,
			'show_all_sched' => $show_all_sched,
			'show_desc'      => $show_desc,
			// --- new widget options ---
			'title_position' => $position,
			'avatar_width'   => $width,
			'link_djs'       => $link_djs,
			'show_encore'    => $encore,
			'countdown'      => $countdown,
			'widget'         => 1,
			'id'             => $id,
		);

		// 2.3.2: only set AJAX attribute if overriding default
		if ( in_array( $ajax, array( 'on', 'off' ) ) ) {
			$atts['ajax'] = $ajax;
		}

		// 2.3.3.9: add filter for default widget attributes
		$atts = apply_filters( 'radio_station_current_show_widget_atts', $atts, $instance );

		// --- before widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_widget'];

		// --- open widget container ---
		// 2.3.0: add unique id to widget
		// 2.3.2: add class to widget
		// 2.4.0.1: add current-show-widget class
		echo '<div id="current-show-widget-' . esc_attr( $id ) . '" class="current-show-widget widget">';

		// --- widget title ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_title'];
		if ( !empty( $title ) ) {
			echo esc_html( $title );
		}
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_title'];

		// 2.3.3.9: add div wrapper for widget contents
		echo '<div id="current-show-widget-contents-' . esc_attr( $id ) . '" class="current-show-wrap">';

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_current_show_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
		$output = apply_filters( 'radio_station_current_show_widget_override', $output, $args, $atts );

		// --- output widget display ---
		if ( $output ) {
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $output;
		}

		// --- close widget contents wrapper ---
		echo '</div>';

		// --- close widget container ---
		echo '</div>';

		// --- after widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_widget'];

		// --- enqueue widget stylesheet in footer ---
		// (this means it will only load if widget is on page)
		// 2.2.4: renamed djonair.css to widgets.css and load for all widgets
		// 2.3.0: widgets.css merged into rs-shortcodes.css
		// 2.3.0: use abstracted method for enqueueing widget styles
		radio_station_enqueue_style( 'shortcodes' );

	}
}

// --- register the widget ---
// 2.2.7: revert anonymous function usage for backwards compatibility
add_action( 'widgets_init', 'radio_station_register_current_show_widget' );
function radio_station_register_current_show_widget() {
	// note: widget class name to remain unchanged for backwards compatibility
	register_widget( 'DJ_Widget' );
}
