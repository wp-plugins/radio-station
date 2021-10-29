<?php

// --------------------------
// === Radio Clock Widget ===
// --------------------------
// @since 2.3.2

class Radio_Clock_Widget extends WP_Widget {

	// --- construct widget class ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'Radio_Clock_Widget',
			'description' => __( 'Display current radio and user times.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Radio Clock', 'radio-station' );
		parent::__construct( 'Radio_Clock_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'Radio Clock', 'radio-station' ) ) );

		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$time = isset( $instance['time'] ) ? $instance['time'] : '';
		$seconds = isset( $instance['seconds'] ) ? $instance['seconds'] : 0;
		$day = isset( $instance['day'] ) ? $instance['day'] : 'full';
		$date = isset( $instance['date'] ) ? $instance['date'] : 1;
		$month = isset( $instance['month'] ) ? $instance['month'] : 'full';
		$zone = isset( $instance['zone'] ) ? $instance['zone'] : 1;

		// --- widget options form ---
		echo '
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
				' . esc_html( __( 'Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '">
			</label>
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
			<label for="' . esc_attr( $this->get_field_id( 'seconds' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'seconds' ) ) . '" name="' . esc_attr( $this->get_field_name( 'seconds' ) ) . '" type="checkbox" ' . checked( $seconds, true, false ) . '>
				' . esc_html( __( 'Include seconds display.', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'day' ) ) . '">' . esc_html( __( 'Day Display', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'day' ) ) . '" name="' . esc_attr( $this->get_field_name( 'day' ) ) . '">
					<option value="full" ' . selected( $day, 'full', false ) . '>' . esc_html( __( 'Full', 'radio-station' ) ) . '</option>
					<option value="short" ' . selected( $day, 'short', false ) . '>' . esc_html( __( 'Short', 'radio-station' ) ) . '</option>
					<option value="none" ' . selected( $day, 'none', false ) . '>' . esc_html( __( 'None', 'radio-station' ) ) . '</option>
				</select>
			</label>
			<br />
			<small>' . esc_html( __( 'Display day with clock times.', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'date' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'date' ) ) . '" name="' . esc_attr( $this->get_field_name( 'date' ) ) . '" type="checkbox" ' . checked( $date, true, false ) . '>
				' . esc_html( __( 'Include date display.', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'month' ) ) . '">' . esc_html( __( 'Month Display', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'month' ) ) . '" name="' . esc_attr( $this->get_field_name( 'month' ) ) . '">
					<option value="full" ' . selected( $month, 'full', false ) . '>' . esc_html( __( 'Full', 'radio-station' ) ) . '</option>
					<option value="short" ' . selected( $month, 'short', false ) . '>' . esc_html( __( 'Short', 'radio-station' ) ) . '</option>
					<option value="none" ' . selected( $month, 'none', false ) . '>' . esc_html( __( 'None', 'radio-station' ) ) . '</option>
				</select>
			</label>
			<br />
			<small>' . esc_html( __( 'Display month with clock times.', 'radio-station' ) ) . '</small>
		</p>
		
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'zone' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'zone' ) ) . '" name="' . esc_attr( $this->get_field_name( 'zone' ) ) . '" type="checkbox" ' . checked( $zone, true, false ) . '>
				' . esc_html( __( 'Include timezone display.', 'radio-station' ) ) . '
			</label>
		</p>';

	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];

		// --- update widget options ---
		$instance['time'] = isset( $new_instance['time'] ) ? $new_instance['time'] : 12;
		$instance['seconds'] = isset( $new_instance['seconds'] ) ? 1 : 0;
		$instance['day'] = isset( $new_instance['day'] ) ? $new_instance['day'] : 'full';
		$instance['date'] = isset( $new_instance['date'] ) ? 1 : 0;
		$instance['month'] = isset( $new_instance['month'] ) ? $new_instance['month'] : 'full';
		$instance['zone'] = isset( $new_instance['zone'] ) ? 1 : 0;
			
		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.3.9: added unique widget id
		if ( !isset( $radio_station_data['widgets']['clock'] ) ) {
			$id = $radio_station_data['widgets']['clock'] = 0;
		} else {
			$id = $radio_station_data['widgets']['clock']++;
		}

		// 2.3.0: added hide widget if empty option
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );

		$time = $instance['time'];
		$seconds = $instance['seconds'];
		$day = $instance['day'];
		$date = $instance['date'];
		$zone = $instance['zone'];
		$month = $instance['month'];

		// --- set shortcode attributes for display ---
		$atts = array(
			'time'    => $time,
			'seconds' => $seconds,
			'day'     => $day,
			'date'    => $date,
			'month'   => $month,
			'zone'    => $zone,
			'widget'  => 1,
		);

		// 2.3.3.9: add missing filter for clock widget attributes
		$atts = apply_filters( 'radio_station_clock_widget_atts', $atts, $instance );

		// --- before widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_widget'];

		// --- open widget container ---
		// 2.3.0: add instance id and class to widget container
		echo '<div id="radio-clock-widget-' . esc_attr( $id ). '" class="radio-clock-widget widget">';

		// --- output widget title ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_title'];
		if ( !empty( $title ) ) {
			echo esc_html( $title );
		}
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_title'];

		echo '<div id="radio-clock-widget-contents-' . esc_attr( $id ) . '" class="radio-clock-wrap">';

		// --- get default display output ---
		$output = radio_station_clock_shortcode( $atts );

		// --- check for widget output override ---
		$output = apply_filters( 'radio_station_radio_clock_widget_override', $output, $args, $atts );

		// --- output widget display ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $output;

		// --- close widget contents ---
		echo '</div>';

		// --- close widget container ---
		echo '</div>';

		// --- after widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_widget'];

		// --- enqueue widget stylesheet in footer ---
		// (this means it will only load if widget is on page)
		// 2.4.0.4: fix to load shortcode stylesheet
		radio_station_enqueue_style( 'shortcodes' );

	}
}

// --- register the widget ---
add_action( 'widgets_init', 'radio_station_register_radio_clock_widget' );
function radio_station_register_radio_clock_widget() {
	register_widget( 'Radio_Clock_Widget' );
}
