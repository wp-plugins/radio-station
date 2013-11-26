<?php
/* Sidebar Widget - Now Playing
 * Displays the currently playing song according to the entered playlists 
 * Since 2.0.0
 */
class Playlist_Widget extends WP_Widget {

	//define the widget
	function Playlist_Widget() {
		$widget_ops = array('classname' => 'Playlist_Widget', 'description' => __('Display the current song.', 'radio-station'));
		$this->WP_Widget('Playlist_Widget', __('Radio Station: Now Playing', 'radio-station'), $widget_ops);
	}

	//build the backend widget form
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$artist = $instance['artist'];
		$song = $instance['song'];
		$album = $instance['album'];
		$label = $instance['label'];
		$comments = $instance['comments'];

		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'radio-station'); ?>: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('artist'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('artist'); ?>" name="<?php echo $this->get_field_name('artist'); ?>" type="checkbox" <?php if($artist) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Show Artist Name', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('song'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('song'); ?>" name="<?php echo $this->get_field_name('song'); ?>" type="checkbox" <?php if($song) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Show Song Title', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('album'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" type="checkbox" <?php if($album) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Show Album Name', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('label'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="checkbox" <?php if($label) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Show Record Label Name', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('comments'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" type="checkbox" <?php if($comments) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Show DJ Comments', 'radio-station'); ?>
		  		</label>
		  	</p>
		<?php
	}
 
	//handle saves and updates
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['artist'] = ( isset( $new_instance['artist'] ) ? 1 : 0 );
		$instance['song'] = ( isset( $new_instance['song'] ) ? 1 : 0 );
		$instance['album'] = ( isset( $new_instance['album'] ) ? 1 : 0 );
		$instance['label'] = ( isset( $new_instance['label'] ) ? 1 : 0 );
		$instance['comments'] = ( isset( $new_instance['comments'] ) ? 1 : 0 );
		 
		return $instance;
	}
 
	//display the widget in the front end
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$artist = $instance['artist'];
		$song = $instance['song'];
		$album = $instance['album'];
		$label = $instance['label'];
		$comments = $instance['comments'];
 		
		
 		//fetch the current song
		$most_recent = myplaylist_get_now_playing();
		
		?>
		<div class="widget">
			<?php 
				if (!empty($title)) {
					echo $before_title . $title . $after_title;
				}
				else {
					echo $before_title.$after_title;
				}
				
				if($most_recent) {
					$class= '';
					if(isset($most_recent['playlist_entry_new']) && $most_recent['playlist_entry_new'] == 'on') {
						$class= ' class="new"';	
					}
					
					echo '<div id="myplaylist-nowplaying"'.$class.'>';
					
					if($song) {
						echo '<span class="myplaylist-song">'.$most_recent['playlist_entry_song'].'</span> ';
					}
					
					if($artist) {
						echo '<span class="myplaylist-artist">'.$most_recent['playlist_entry_artist'].'</span> ';
					}
					
					if($album) {
						echo '<span class="myplaylist-album">'.$most_recent['playlist_entry_album'].'</span> ';
					}
					
					if($label) {
						echo '<span class="myplaylist-label">'.$most_recent['playlist_entry_label'].'</span> ';
					}
					
					if($comments) {
						echo '<span class="myplaylist-comments">'.$most_recent['playlist_entry_comments'].'</span> ';
					}
					
					echo '<span class="myplaylist-link">';
					echo '<a href="'.$most_recent['playlist_permalink'].'">'.__('View Playlist', 'radio-station').'</a>';
					echo '</span>';
					echo '</div>';
				}
				else {
					echo 'No playlists available.';
				}
				
			?>
			
			
		</div>
		<?php
 
		echo $after_widget;
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("Playlist_Widget");') );
?>