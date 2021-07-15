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
		$url = isset( $instance['url'] ) ? $instance['url'] : '';
		// $format = isset( $instance['format'] ) ? $instance['format'] : '';
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$station = isset( $instance['station'] ) ? $instance['station'] : '';
		$image = isset( $instance['image'] ) ? $instance['image'] : '';
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

		// --- stream or file URL ---
		$fields = '
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'url' ) ) . '">
			' . esc_html( __( 'Stream or File URL', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'url' ) ) . '" name="' . esc_attr( $this->get_field_name( 'url' ) ) . '" type="text" value="' . esc_attr( $url ) . '" />
			</label><br>
			' . esc_html( 'Note: Leave blank to use default stream URL.', 'radio-station' ) . '
		</p>' . PHP_EOL;

		// --- widget title ---
		$fields .= '
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
			' . esc_html( __( 'Widget Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
			</label>
		</p>' . PHP_EOL;

		// --- station text ---
		$fields .= '
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'station' ) ) . '">
			' . esc_html( __( 'Player Station Text', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'station' ) ) . '" name="' . esc_attr( $this->get_field_name( 'station' ) ) . '" type="text" value="' . esc_attr( $station ) . '" />
			</label><br>
			(' . esc_html( 'Empty for default, 0 for none', 'radio-station' ) . ')
		</p>' . PHP_EOL;

		// --- station image ---
		$fields .= '<p>
			<label for="' . esc_attr( $this->get_field_id( 'script' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'script' ) ) . '" name="' . esc_attr( $this->get_field_name( 'script' ) ) . '">';
				$options = array(
					'default' => __( 'Plugin Setting', 'radio-station' ),
					'on'      => __( 'Display Station Image', 'radio-station' ),
					'off'     => __( 'Do Not Display Image', 'radio-station' ),
				);
				foreach ( $options as $option => $label ) {
					$fields .= '<option value="' . esc_attr( $option ) . '" ' . selected( $script, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}

				$fields .= '</select>
				' . esc_html( __( 'Whether to display Station Image in Player.', 'radio-station' ) ) . '
		</p>' . PHP_EOL;

		// --- player script ---
		$fields .= '<p>
			<label for="' . esc_attr( $this->get_field_id( 'script' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'script' ) ) . '" name="' . esc_attr( $this->get_field_name( 'script' ) ) . '">';
				$options = array(
					'default'   => __( 'Plugin Setting', 'radio-station' ),
					'amplitude' => __( 'Amplitude', 'radio-station' ),
					'amplitude' => __( 'Howler', 'radio-station' ),
					'jplayer'   => __( 'jPlayer', 'radio-station' ),
				);
				foreach ( $options as $option => $label ) {
					$fields .= '<option value="' . esc_attr( $option ) . '" ' . selected( $script, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}

				$fields .= '</select>
				' . esc_html( __( 'Player Script to load by default.', 'radio-station' ) ) . '
			</label>
		</p>' . PHP_EOL;

		// --- player layout ---
		$fields .= '<p>
			<label for="' . esc_attr( $this->get_field_id( 'layout' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'layout' ) ) . '" name="' . esc_attr( $this->get_field_name( 'layout' ) ) . '">';
				$options = array(
					'vertical' => __( 'Vertical (Stacked)', 'radio-station' ),
					'horizontal'  => __( 'Horizontal (Inline)', 'radio-station' ),
				);
				foreach ( $options as $option => $label ) {
					$fields .= '<option value="' . esc_attr( $option ) . '" ' . selected( $layout, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}

				$fields .= '</select>
				' . esc_html( __( 'Layout Style for Widget Area (wide or tall)', 'radio-station' ) ) . '
			</label>
		</p>' . PHP_EOL;

		// --- player theme ---
		$fields .= '<p>
			<label for="' . esc_attr( $this->get_field_id( 'theme' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'theme' ) ) . '" name="' . esc_attr( $this->get_field_name( 'theme' ) ) . '">';
				$options = array(
					'default'	=> __( 'Plugin Setting', 'radio-station' ),
					'light'		=> __( 'Light', 'radio-station' ),
					'dark'		=> __( 'Dark', 'radio-station' ),
				);
				$options = apply_filters( 'radio_station_player_theme_options', $options );
				foreach ( $options as $option => $label ) {
					$fields .= '<option value="' . esc_attr( $option ) . '" ' . selected( $theme, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}

				$fields .= '</select>
				' . esc_html( __( 'Player Theme Style', 'radio-station' ) ) . '
			</label>
		</p>' . PHP_EOL;

		// --- player buttons ---
		$fields .= '<p>
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
					$fields .= '<option value="' . esc_attr( $option ) . '" ' . selected( $buttons, $option, false ) . '>' . esc_html( $label ) . '</option>';
				}

				$fields .= '</select>
				' . esc_html( __( 'Layout Style for Widget Area', 'radio-station' ) ) . '
			</label>
		</p>' . PHP_EOL;

		// --- player volume ---
		$fields .= '<p>
			<label for="' . esc_attr( $this->get_field_id( 'volume' ) ) . '">
			' . esc_html( __( 'Player Start Volume', 'radio-station' ) ) . ' (0 to 100, empty for default):
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'volume' ) ) . '" name="' . esc_attr( $this->get_field_name( 'volume' ) ) . '" type="text" value="' . esc_attr( $volume ) . '" />
			</label>
		</p>' . PHP_EOL;

		// --- player default instance ---
		$fields .= '<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="checkbox" ' . checked( $default, true, false ) . '>
				' . esc_html( __( 'Use this as the default Player instance.', 'radio-station' ) ) . '
			</label>
		</p>' . PHP_EOL;

		// --- filter and output ---
		$fields = apply_filters( 'radio_station_player_widget_fields', $fields, $this, $instance );
		echo $fields;
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
		$instance['default'] = isset( $new_instance['default'] ) ? 1 : 0;;

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
		if ( !isset( $radio_station_data['widgets']['player'] ) ) {
			$id = $radio_station_data['widgets']['player'] = 0;
		} else {
			$id = $radio_station_data['widgets']['player']++;
		}

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
			// --- additional displays
			// 'shows'          => $shows,
			// 'hosts'          => $hosts,
			// 'producers'      => $producers,
			// --- widget data ---
			'widget'         => 1,
			'id'             => $id,
		);

		// --- before widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_widget'];

		// --- open widget container ---
		$id = 'radio-player-widget-' . $id;
		echo '<div id="' . esc_attr( $id ) . '" class="widget">';

		// --- widget title ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_title'];
		if ( !empty( $title ) ) {
			echo esc_html( $title );
		}
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_title'];

		// --- get default display output ---
		$output = radio_station_player_shortcode( $atts );

		// --- check for widget output override ---
		$output = apply_filters( 'radio_station_player_widget_override', $output, $args, $atts );

		// --- output widget display ---
		if ( $output ) {
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $output;
		}

		echo '</div>';

		// --- after widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_widget'];

	}
}

// ----------------------
// Register Player Widget
// ----------------------
add_action( 'widgets_init', 'radio_station_register_player_widget' );
function radio_station_register_player_widget() {
	register_widget( 'Radio_Player_Widget' );
}
