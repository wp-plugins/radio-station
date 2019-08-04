<?php
/* Sidebar Widget - Now Playing
 * Displays the currently playing song according to the entered playlists
 * Since 2.1.1
 */
class Playlist_Widget extends WP_Widget {

	// --- use __constuct instead of Playlist_Widget ---
	function __construct() {
		$widget_ops = array( 'classname' => 'Playlist_Widget', 'description' => __('Display the current song.', 'radio-station') );
		$widget_display_name = __('(Radio Station) Now Playing', 'radio-station' );
		parent::__construct('Playlist_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	function form( $instance ) {

		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$artist = isset( $instance['artist'] ) ? $instance['artist'] : true;
		$song = isset( $instance['song'] ) ? $instance['song'] : true;
		$album = isset( $instance['album'] ) ? $instance['album'] : false;
		$label = isset( $instance['label'] ) ? $instance['label'] : false;
		$comments = isset( $instance['comments'] ) ? $instance['comments'] : false;

		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'radio-station' ); ?>:
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'artist' ); ?>">
		  		<input id="<?php echo $this->get_field_id( 'artist' ); ?>" name="<?php echo $this->get_field_name( 'artist' ); ?>" type="checkbox" <?php if ( $artist ) {echo 'checked="checked"';} ?> />
		  		<?php _e( 'Show Artist Name', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'song' ); ?>">
		  		<input id="<?php echo $this->get_field_id( 'song' ); ?>" name="<?php echo $this->get_field_name( 'song' ); ?>" type="checkbox" <?php if ( $song ) {echo 'checked="checked"';} ?> />
		  		<?php _e( 'Show Song Title', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'album' ); ?>">
		  		<input id="<?php echo $this->get_field_id( 'album' ); ?>" name="<?php echo $this->get_field_name( 'album' ); ?>" type="checkbox" <?php if ( $album ) {echo 'checked="checked"';} ?> />
		  		<?php _e(' Show Album Name', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'label' ); ?>">
		  		<input id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" type="checkbox" <?php if ( $label ) {echo 'checked="checked"';} ?> />
		  		<?php _e( 'Show Record Label Name', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('comments'); ?>">
		  		<input id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" type="checkbox" <?php if ( $comments ) {echo 'checked="checked"';} ?> />
		  		<?php _e( 'Show DJ Comments', 'radio-station' ); ?>
		  		</label>
		  	</p>
		<?php
	}

	// --- update widget instance ---
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['artist'] = ( isset( $new_instance['artist'] ) ? 1 : 0 );
		$instance['song'] = ( isset( $new_instance['song'] ) ? 1 : 0 );
		$instance['album'] = ( isset( $new_instance['album'] ) ? 1 : 0 );
		$instance['label'] = ( isset( $new_instance['label'] ) ? 1 : 0 );
		$instance['comments'] = ( isset( $new_instance['comments'] ) ? 1 : 0 );

		return $instance;
	}

	// --- output widget display ---
	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$artist = $instance['artist'];
		$song = $instance['song'];
		$album = $instance['album'];
		$label = $instance['label'];
		$comments = $instance['comments'];


 		// --- fetch the current song ---
		$most_recent = radio_station_myplaylist_get_now_playing();
		echo "<!-- MOST RECENT: ".print_r($most_recent,true)." -->";

		?>
		<div class="widget">
			<?php
				if ( !empty( $title ) ) {echo $before_title.$title.$after_title;}
				else {echo $before_title.$after_title;}

				if ( $most_recent ) {

					$class= '';
					if ( isset($most_recent['playlist_entry_new']) && ( $most_recent['playlist_entry_new'] == 'on' ) ) {
						$class= ' class="new"';
					}

					echo '<div id="myplaylist-nowplaying"'.$class.'>';

						// 2.2.3: convert span tags to div tags
						// 2.2.4: check value keys are set before outputting
						if ( $song && isset( $most_recent['playlist_entry_song']) ) {
							echo '<div class="myplaylist-song">'.$most_recent['playlist_entry_song'].'</div> ';
						}

						if ( $artist && isset( $most_recent['playlist_entry_artist'] ) ) {
							echo '<div class="myplaylist-artist">'.$most_recent['playlist_entry_artist'].'</div> ';
						}

						if ( $album && isset( $most_recent['playlist_entry_album'] ) ) {
							echo '<div class="myplaylist-album">'.$most_recent['playlist_entry_album'].'</div> ';
						}

						if ( $label && isset( $most_recent['playlist_entry_label'] ) ) {
							echo '<div class="myplaylist-label">'.$most_recent['playlist_entry_label'].'</div> ';
						}

						if ( $comments && isset( $most_recent['playlist_entry_comments'] ) ) {
							echo '<div class="myplaylist-comments">'.$most_recent['playlist_entry_comments'].'</div> ';
						}

						if ( isset( $most_recent['playlist_permalink'] ) ) {
							echo '<div class="myplaylist-link">';
								echo '<a href="'.$most_recent['playlist_permalink'].'">'.__('View Playlist', 'radio-station').'</a>';
							echo '</div>';
						}

					echo '</div>';

				} else {
					// 2.2.3: added missing translation wrapper
					echo '<div>'.__('No playlists available.','radio-station').'</div>';
				}

			?>


		</div>
		<?php

 		// --- enqueue widget stylesheet in footer ---
 		// (this means it will only load if widget is on page)
 		// 2.2.4: renamed djonair.css and load for all widgets
 		$dj_widget_css = get_stylesheet_directory().'/widgets.css';
 		if ( file_exists( $dj_widget_css ) ) {
 			$version = filemtime( $dj_widget_css );
 			$url = get_stylesheet_directory_uri().'/widgets.css';
 		} else {
 			$version = filemtime( dirname(dirname(__FILE__)).'/css/widgets.css' );
 			$url = plugins_url('css/widgets.css', dirname(dirname(__FILE__)).'/radio-station.php' );
		}
		wp_enqueue_style( 'dj-widget', $url, array(), $version, 'all' );

		echo $after_widget;
	}
}

// --- register the widget ---
add_action( 'widgets_init', 'radio_station_register_nowplaying_widget');
function radio_station_register_nowplaying_widget() {register_widget('Playlist_Widget');}
