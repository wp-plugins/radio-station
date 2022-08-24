<?php

// ---------------------
// === Player Widget ===
// ---------------------

// -------------
// Player Widget
// -------------
class Radio_Player_Widget extends WP_Widget {

	// --- construct widget ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'Radio_Player_Widget',
			'description' => __( 'Radio Station Stream Player.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Stream Player', 'radio-station' );
		parent::__construct( 'Radio_Player_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );

		// 2.5.0: fix player image default value to 'default'
		$url = isset( $instance['url'] ) ? $instance['url'] : '';
		// $format = isset( $instance['format'] ) ? $instance['format'] : '';
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$station = isset( $instance['station'] ) ? $instance['station'] : '';
		$image = isset( $instance['image'] ) ? $instance['image'] : 'default';
		$script = isset( $instance['script'] ) ? $instance['script'] : 'default';
		$layout = isset( $instance['layout'] ) ? $instance['layout'] : 'vertical';
		$theme = isset( $instance['theme'] ) ? $instance['theme'] : 'default';
		$buttons = isset( $instance['buttons'] ) ? $instance['buttons'] : 'default';
		$volume = isset( $instance['volume'] ) ? $instance['volume'] : '';
		$default = isset( $instance['default'] ) ? $instance['default'] : 0;

		// --- additional displays ---
		// $shows = isset( $instance['show_display'] ) ? $instance['show_display'] : false;
		// $hosts = isset( $instance['show_hosts'] ) ? $instance['show_hosts'] : false;
		// $producers = isset( $instance['show_producers'] ) ? $instance['show_producers'] : false;

		// 2.5.0: set fields array
		$fields = array();

		// === Player Content ===
		$fields['player_styles'] = '<h4>' . esc_html( __( 'Player Content', 'radio-station' ) ) . '</h4>' . "\n";

		// --- Widget Title ---
		$fields['title'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
			' . esc_html( __( 'Widget Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
			</label>
		</p>';

		// --- Stream or File URL ---
		$fields['url'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'url' ) ) . '">
			' . esc_html( __( 'Stream or File URL', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'url' ) ) . '" name="' . esc_attr( $this->get_field_name( 'url' ) ) . '" type="text" value="' . esc_attr( $url ) . '" />
			</label><br>
			' . esc_html( 'Leave blank to use default stream URL.', 'radio-station' ) . '
		</p>';

		// --- Station Text ---
		$fields['station'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'station' ) ) . '">
			' . esc_html( __( 'Player Station Text', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'station' ) ) . '" name="' . esc_attr( $this->get_field_name( 'station' ) ) . '" type="text" value="' . esc_attr( $station ) . '" />
			</label><br>
			(' . esc_html( 'Empty for default, 0 for none.', 'radio-station' ) . ')
		</p>';

		// --- Station Image ---
		// 2.5.0: fix to image field key (script)
		$field = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'image' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'image' ) ) . '" name="' . esc_attr( $this->get_field_name( 'image' ) ) . '">';
				$options = array(
					'default' => __( 'Plugin Setting', 'radio-station' ),
					'on'      => __( 'Display Station Image', 'radio-station' ),
					'off'     => __( 'Do Not Display Image', 'radio-station' ),
					// 'custom' => __( 'Use Custom Image', 'radio-station' ),
				);
				foreach ( $options as $option => $label ) {
					$field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $image, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}
				$field .= '</select>
				' . esc_html( __( 'Whether to display Station Image in Player.', 'radio-station' ) ) . '
		</p>';
		$fields['image'] = $field;

		// === Player Options ===
		$fields['player_options'] = '<h4>' . esc_html( __( 'Player Options', 'radio-station' ) ) . '</h4>' . "\n";

		// --- Player Script ---
		$field = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'script' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'script' ) ) . '" name="' . esc_attr( $this->get_field_name( 'script' ) ) . '">';
				$options = array(
					'default'   => __( 'Plugin Setting', 'radio-station' ),
					'amplitude' => __( 'Amplitude', 'radio-station' ),
					'howler'    => __( 'Howler', 'radio-station' ),
					'jplayer'   => __( 'jPlayer', 'radio-station' ),
				);
				foreach ( $options as $option => $label ) {
					$field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $script, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}
				$field .= '</select>
				' . esc_html( __( 'Player Script to load by default.', 'radio-station' ) ) . '
			</label>
		</p>';
		$fields['script'] = $field;
		
		// --- Player Volume ---
		$fields['volume'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'volume' ) ) . '">
			' . esc_html( __( 'Player Start Volume', 'radio-station' ) ) . ' (0 to 100, empty for default):
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'volume' ) ) . '" name="' . esc_attr( $this->get_field_name( 'volume' ) ) . '" type="text" value="' . esc_attr( $volume ) . '" />
			</label>
		</p>';

		// --- Default Player ---
		$fields['default'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="checkbox" ' . checked( $default, true, false ) . '>
				' . esc_html( __( 'Use this as the default Player instance.', 'radio-station' ) ) . '
			</label>
		</p>';

		// === Player Styles ===
		$fields['player_styles'] = '<h4>' . esc_html( __( 'Player Styles', 'radio-station' ) ) . '</h4>' . "\n";

		// --- Player Layout ---
		$field = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'layout' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'layout' ) ) . '" name="' . esc_attr( $this->get_field_name( 'layout' ) ) . '">';
				$options = array(
					'vertical' => __( 'Vertical (Stacked)', 'radio-station' ),
					'horizontal' => __( 'Horizontal (Inline)', 'radio-station' ),
				);
				foreach ( $options as $option => $label ) {
					$field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $layout, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}
				$field .= '</select>
				' . esc_html( __( 'Layout Style (tall or wide)', 'radio-station' ) ) . '
			</label>
		</p>';
		$fields['layout'] = $field;

		// --- Player Theme ---
		$field = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'theme' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'theme' ) ) . '" name="' . esc_attr( $this->get_field_name( 'theme' ) ) . '">';
				$options = array(
					'default'	=> __( 'Plugin Setting', 'radio-station' ),
					'light'		=> __( 'Light', 'radio-station' ),
					'dark'		=> __( 'Dark', 'radio-station' ),
				);
				$options = apply_filters( 'radio_station_player_theme_options', $options );
				foreach ( $options as $option => $label ) {
					$field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $theme, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}

				$field .= '</select>
				' . esc_html( __( 'Player Theme Style', 'radio-station' ) ) . '
			</label>
		</p>';
		$fields['theme'] = $field;

		// --- Player Buttons ---
		$field = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'buttons' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'buttons' ) ) . '" name="' . esc_attr( $this->get_field_name( 'buttons' ) ) . '">';
				$options = array(
					'default'	=> __( 'Plugin Setting', 'radio-station' ),
					'circular'	=> __( 'Circular', 'radio-station' ),
					'rounded'	=> __( 'Rounded', 'radio-station' ),
					'square'	=> __( 'Square', 'radio-station' ),
				);
				$options = apply_filters( 'radio_station_player_button_options', $options );
				foreach ( $options as $option => $label ) {
					$field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $buttons, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}
				$field .= '</select>
				' . esc_html( __( 'Player Button Style', 'radio-station' ) ) . '
			</label>
		</p>';
		$fields['buttons'] = $field;

		// --- filter and output ---
		// 2.5.0: added filter for array fields for ease of adding fields
		$fields = apply_filters( 'radio_station_player_widget_fields_list', $fields, $this, $instance );
		$fields_html = implode( "\n", $fields );
		$fields_html = apply_filters( 'radio_station_player_widget_fields', $fields_html, $this, $instance );
		// 2.5.0: use wp_kses on field settings output
		$allowed = radio_station_allowed_html( 'content', 'settings' );
		echo wp_kses( $fields_html, $allowed );
	}

	// --- update widget instance values ---
	public function update( $new_instance, $old_instance ) {

		// --- get new widget options ---
		$instance = $old_instance;
		$instance['url'] = isset( $new_instance['url'] ) ? $new_instance['url'] : '';
		$instance['title'] = isset( $new_instance['title'] ) ? $new_instance['title'] : '';
		$instance['station'] = isset( $new_instance['station'] ) ? $new_instance['station'] : '';
		$instance['image'] = isset( $new_instance['image'] ) ? $new_instance['image'] : $old_instance['image'];
		$instance['script'] = isset( $new_instance['script'] ) ? $new_instance['script'] : $old_instance['script'];
		$instance['layout'] = isset( $new_instance['layout'] ) ? $new_instance['layout'] : $old_instance['layout'];
		$instance['theme'] = isset( $new_instance['theme'] ) ? $new_instance['theme'] : $old_instance['theme'];
		$instance['buttons'] = isset( $new_instance['buttons'] ) ? $new_instance['buttons'] : $old_instance['buttons'];
		$instance['volume'] = isset( $new_instance['volume'] ) ? $new_instance['volume'] : $old_instance['volume'];
		if ( '' != $instance['volume'] ) {
			$instance['volume'] = absint( $instance['volume'] );
			if ( $instance['volume'] > 100 ) {
				$instance['volume'] = 100;
			} elseif ( $instance['volume'] < 0 ) {
				$instance['volume'] = 0;
			}
		}
		$instance['default'] = isset( $new_instance['default'] ) ? 1 : 0;

		// --- additional displays ---
		// $instance['show_display'] = isset( $new_instance['show_display'] ) ? 1 : 0;
		// $instance['show_hosts'] = isset( $new_instance['show_hosts'] ) ? 1 : 0;
		// $instance['show_producers'] = isset( $new_instance['show_producers'] ) ? 1 : 0;

		// --- filter and return ---
		$instance = apply_filters( 'radio_station_player_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- widget output ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.5.0: simplify widget id setting
		if ( !isset( $radio_station_data['widgets']['player'] ) ) {
			$radio_station_data['widgets']['player'] = 0;
		}
		$radio_station_data['widgets']['player']++;
		$id = $radio_station_data['widgets']['player'];

		// --- get widget options ---
		$url = $instance['url'];
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );
		$station = $instance['station'];
		$image = $instance['image'];
		if ( 'on' == $image ) {
			$image = 1;
		} elseif ( 'off' == $image ) {
			$image = 0;
		}
		$script = $instance['script'];
		$layout = $instance['layout'];
		$theme = $instance['theme'];
		$buttons = $instance['buttons'];
		$volume = $instance['volume'];
		if ( !$volume ) {
			$volume = 'default';
		}
		$default = $instance['default'];

		// --- additional displays ---
		// $instance['show_display'] = $instance['show_display'] ) ? 1 : 0;
		// $instance['show_hosts'] = $instance['show_hosts'];
		// $instance['show_producers'] = $instance['show_producers'];

		// --- set shortcode attributes ---
		// note: station text mapped to shortcode title attribute
		$atts = array(
			// --- main player settings ---
			'url'            => $url,
			'title'          => $station,
			'image'          => $image,
			'script'         => $script,
			'layout'         => $layout,
			'theme'          => $theme,
			'buttons'        => $buttons,
			'volume'         => $volume,
			'default'        => $default,
			// --- additional displays ---
			// 'shows'          => $shows,
			// 'hosts'          => $hosts,
			// 'producers'      => $producers,
			// --- widget data ---
			// 2.4.0.1: prefix widget player ID
			'widget'         => 1,
			'id'             => $id,
		);

		// 2.5.0: add filter for default widget attributes
		$atts = apply_filters( 'radio_station_player_widget_atts', $atts, $instance );

		// --- maybe debug widget attributes --
		// 2.5.0: added for debugging widget attributes
		if ( isset( $_REQUEST['player-debug'] ) && ( '1' == $_REQUEST['player-debug'] ) ) {
			echo '<span style="display:none;">Radio Player Widget Attributes: ';
			echo esc_html( print_r( $atts, true ) ) . '</span>';
		}

		// 2.5.0: get context filtered allowed HTML
		$allowed = radio_station_allowed_html( 'widget', 'radio-player' );

		// --- before widget ---
		// 2.5.0: use wp_kses on output
		echo wp_kses( $args['before_widget'], $allowed );

		// --- open widget container ---
		// 2.5.0: added class radio-player-widget
		echo '<div id="radio-player-widget-' . esc_attr( $id ) . '" class="radio-player-widget widget">' . "\n";

			// --- widget title ---
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['before_title'], $allowed );
			if ( !empty( $title ) ) {
				echo wp_kses( $title, $allowed );
			}
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['after_title'], $allowed );

			// --- get default display output ---
			$output = radio_station_player_shortcode( $atts );

			// --- check for widget output override ---
			$output = apply_filters( 'radio_station_player_widget_override', $output, $args, $atts );

			// --- output widget display ---
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $output;

		echo '</div>' . "\n";

		// --- after widget ---
		// 2.5.0: use wp_kses on output
		echo wp_kses( $args['after_widget'], $allowed );

	}
}

// ----------------------
// Register Player Widget
// ----------------------
add_action( 'widgets_init', 'radio_station_register_player_widget' );
function radio_station_register_player_widget() {
	register_widget( 'Radio_Player_Widget' );
}

