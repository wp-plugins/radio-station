<?php

/* Current Playlist Widget - (Now Playing)
 * Displays the currently playing song according to the entered playlists
 * Since 2.1.1
 */

// note: widget class name to remain unchanged for backwards compatibility
class Playlist_Widget extends WP_Widget {

	// --- use __constuct instead of Playlist_Widget ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'Playlist_Widget',
			'description' => __( 'Display currently playing playlist.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Now Playing List', 'radio-station' );
		parent::__construct( 'Playlist_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );

		// 2.3.0: added hide widget if empty option
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.3.3.8: added playlist link option
		$title = $instance['title'];
		$link = isset( $instance['link'] ) ? $instance['link'] : true;
		$artist = isset( $instance['artist'] ) ? $instance['artist'] : true;
		$song = isset( $instance['song'] ) ? $instance['song'] : true;
		$album = isset( $instance['album'] ) ? $instance['album'] : false;
		$label = isset( $instance['label'] ) ? $instance['label'] : false;
		$comments = isset( $instance['comments'] ) ? $instance['comments'] : false;
		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : true;
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : false;
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : '';

		// 2.3.0: convert template style code to strings
		// 2.3.2: added AJAX load option field
		// 2.3.3.8: added playlist link field
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
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '">
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'link' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link' ) ) . '" type="checkbox" ' . checked( $link, true, false ) . '>
				' . esc_html( __( 'Link to Playlist?', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'song' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'song' ) ) . '" name="' . esc_attr( $this->get_field_name( 'song' ) ) . '" type="checkbox" ' . checked( $song, true, false ) . '>
				' . esc_html( __( 'Show Song Title', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'artist' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'artist' ) ) . '" name="' . esc_attr( $this->get_field_name( 'artist' ) ) . '" type="checkbox"' . checked( $artist, true, false ) . '>
				' . esc_html( __( 'Show Artist Name', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'album' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'album' ) ) . '" name="' . esc_attr( $this->get_field_name( 'album' ) ) . '" type="checkbox" ' . checked( $album, true, false ) . '>
				' . esc_html( __( ' Show Album Name', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'label' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'label' ) ) . '" name="' . esc_attr( $this->get_field_name( 'label' ) ) . '" type="checkbox" ' . checked( $label, true, false ) . '>
				' . esc_html( __( 'Show Record Label Name', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'comments' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'comments' ) ) . '" name="' . esc_attr( $this->get_field_name( 'comments' ) ) . '" type="checkbox" ' . checked( $comments, true, false ) . '>
				' . esc_html( __( 'Show DJ Comments', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '" name="' . esc_attr( $this->get_field_name( 'hide_empty' ) ) . '" type="checkbox" ' . checked( $hide_empty, true, false ) . '>
				' . esc_html( __( 'Hide Widget if Empty', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '">
			<input id="' .esc_attr( $this->get_field_id( 'countdown' ) ) . '" name="' . esc_attr( $this->get_field_name( 'countdown' ) ) . '" type="checkbox" ' . checked( $countdown, true, false ) . '>
				' . esc_html( __( 'Display Countdown Timer', 'radio-station' ) ) . '
			</label>
        </p>';

		// --- filter and output ---
		// 2.3.0: filter to allow for extra fields
		$fields = apply_filters( 'radio_station_playlist_widget_fields', $fields, $this, $instance );
		echo $fields;
	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// 2.3.0: added hide widget if empty option
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.3.3.8: added playlist link option
		$instance['title'] = $new_instance['title'];
		$instance['link'] = isset( $new_instance['link'] ) ? 1 : 0;
		$instance['artist'] = isset( $new_instance['artist'] ) ? 1 : 0;
		$instance['song'] = isset( $new_instance['song'] ) ? 1 : 0;
		$instance['album'] = isset( $new_instance['album'] ) ? 1 : 0;
		$instance['label'] = isset( $new_instance['label'] ) ? 1 : 0;
		$instance['comments'] = isset( $new_instance['comments'] ) ? 1 : 0;
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? 1 : 0;
		$instance['countdown'] = isset( $new_instance['countdown'] ) ? 1 : 0;
		$instance['ajax'] = isset( $new_instance['ajax'] ) ? $new_instance['ajax'] : 0;

		// 2.3.0: apply filters to widget instance update
		$instance = apply_filters( 'radio_station_playlist_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.0: added unique widget id
		if ( !isset( $radio_station_data['widgets']['current-playlist'] ) ) {
			$id = $radio_station_data['widgets']['current-playlist'] = 0;
		} else {
			$id = $radio_station_data['widgets']['current-playlist']++;
		}

		// 2.3.0: filter widget_title whether empty or not
		// 2.3.0: added hide widget if empty option
		// 2.3.0: added countdown display option
		// 2.3.2: set fallback options to numeric for shortcode
		// 2.3.2: added AJAX load option
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );
		$link = isset( $instance['link'] ) ? $instance['link'] : 1;
		$artist = $instance['artist'];
		$song = $instance['song'];
		$album = $instance['album'];
		$label = $instance['label'];
		$comments = $instance['comments'];
		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : 1;
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : 0;
		$dynamic = isset( $instance['dynamic'] ) ? $instance['dynamic'] : 1;
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : 0;

		// --- set shortcode attributes for display ---
		$atts = array(
			'title'     => $title,
			'link'      => $link,
			'artist'    => $artist,
			'song'      => $song,
			'album'     => $album,
			'label'     => $label,
			'comments'  => $comments,
			'countdown' => $countdown,
			'dynamic'   => $dynamic,
			'widget'    => 1,
			'id'        => $id,
		);

		// 2.3.2: only set AJAX attribute if overriding default
		if ( in_array( $ajax, array( 'on', 'off' ) ) ) {
			$atts['ajax'] = $ajax;
		}

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_current_playlist_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
		$output = apply_filters( 'radio_station_current_playlist_widget_override', $output, $args, $atts );

		// 2.3.0: added hide widget if empty option
		if ( !$hide_empty || ( $hide_empty && $output ) ) {

			// --- beore widget ---
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $args['before_widget'];

			// --- open widget container ---
			// 2.3.0: add unique id to widget
			// 2.3.2: add class to widget
			$id = 'current-playlist-widget-' . $id;
			echo '<div id="' . esc_attr( $id ) . '" class="current-playlist-wrap widget">';

			// --- output widget title ---
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $args['before_title'];
			if ( !empty( $title ) ) {
				echo esc_html( $title );
			}
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $args['after_title'];

			// --- output widget display ---
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $output;

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
}

// --- register the widget ---
// 2.2.7: revert anonymous function usage for backwards compatibility
add_action( 'widgets_init', 'radio_station_register_current_playlist_widget' );
function radio_station_register_current_playlist_widget() {
	// note: widget class name to remain unchanged for backwards compatibility
	register_widget( 'Playlist_Widget' );
}
