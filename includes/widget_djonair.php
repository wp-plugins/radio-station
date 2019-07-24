<?php
/* Sidebar Widget - DJ On Air
 * Displays the current on-air show/DJ
 * Since 2.1.1
 */
class DJ_Widget extends WP_Widget {

	// use __contruct instead of DJ_Widget
	function __construct() {
		$widget_ops = array( 'classname' => 'DJ_Widget', 'description' => __('The current on-air DJ.', 'radio-station') );
		// $this->WP_Widget( 'DJ_Widget', __('Radio Station: Show/DJ On-Air', 'radio-station'), $widget_ops );
		parent::__construct( 'DJ_Widget', __('Radio Station: Show/DJ On-Air', 'radio-station' ), $widget_ops );
	}

	// --- widget instance form ---
	function form( $instance ) {

		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$display_djs = isset( $instance['show_desc'] ) ? $instance['display_djs'] : false;
		$djavatar = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$default = isset( $instance['default'] ) ? $instance['default'] : '';
		$link = isset( $instance['link'] ) ? $instance['link'] : false;
		$time = isset( $instance['time'] ) ? $instance['time'] : 12;
		$show_sched = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;
		$show_playlist = isset( $instance['show_playlist'] ) ? $instance['show_playlist']: false;
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false;
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false;

		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'radio-station'); ?>:
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('display_djs'); ?>">
		  		<input id="<?php echo $this->get_field_id('display_djs'); ?>" name="<?php echo $this->get_field_name( 'display_djs' ); ?>" type="checkbox" <?php if ( $display_djs ) { echo 'checked="checked"'; } ?> />
		  		<?php _e( 'Display names of the DJs on the show', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('djavatar'); ?>">
		  		<input id="<?php echo $this->get_field_id('djavatar'); ?>" name="<?php echo $this->get_field_name( 'djavatar' ); ?>" type="checkbox" <?php if ( $djavatar ) { echo 'checked="checked"'; } ?> />
		  		<?php _e( 'Show Avatars', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('link'); ?>">
		  		<input id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="checkbox" <?php if ( $link ) { echo 'checked="checked"'; } ?> />
		  		<?php _e( "Link to the Show/DJ's profile", 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('show_sched'); ?>">
		  		<input id="<?php echo $this->get_field_id('show_sched'); ?>" name="<?php echo $this->get_field_name( 'show_sched' ); ?>" type="checkbox" <?php if ( $show_sched ) { echo 'checked="checked"'; } ?> />
		  		<?php _e( 'Display schedule info for this show', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('show_all_sched'); ?>">
		  		<input id="<?php echo $this->get_field_id('show_all_sched'); ?>" name="<?php echo $this->get_field_name( 'show_all_sched' ); ?>" type="checkbox" <?php if ( $show_all_sched ) { echo 'checked="checked"'; } ?> />
		  		<?php _e('Display multiple schedules (if show airs more than once per week)', 'radio-station'); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('show_desc'); ?>">
		  		<input id="<?php echo $this->get_field_id('show_desc'); ?>" name="<?php echo $this->get_field_name('show_desc'); ?>" type="checkbox" <?php if ( $show_desc ) {echo 'checked="checked"';} ?> />
		  		<?php _e('Display description of show', 'radio-station'); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('show_playlist'); ?>">
		  		<input id="<?php echo $this->get_field_id('show_playlist'); ?>" name="<?php echo $this->get_field_name('show_playlist'); ?>" type="checkbox" <?php if ( $show_playlist ) {echo 'checked="checked"';} ?> />
		  		<?php _e( "Display link to show's playlist", 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('default'); ?>"><?php _e('Default DJ Name', 'radio-station'); ?>:
		  		<input class="widefat" id="<?php echo $this->get_field_id('default'); ?>" name="<?php echo $this->get_field_name('default'); ?>" type="text" value="<?php echo esc_attr( $default ); ?>" />
		  		</label>
		  		<small><?php _e( 'If no Show/DJ is scheduled for the current hour, display this name/text.', 'radio-station' ); ?></small>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('time'); ?>"><?php _e( 'Time Format', 'radio-station' ); ?>:<br />
		  		<select id="<?php echo $this->get_field_id('time'); ?>" name="<?php echo $this->get_field_name( 'time' ); ?>">
		  			<option value="12" <?php if ( esc_attr( $time ) == 12 ) { echo 'selected="selected"'; } ?>><?php _e( '12-hour', 'radio-station' ); ?></option>
		  			<option value="24" <?php if ( esc_attr( $time ) == 24 ) { echo 'selected="selected"'; } ?>><?php _e( '24-hour', 'radio-station' ); ?></option>
		  		</select>
		  		</label><br />
		  		<small><?php _e( 'Choose time format for displayed schedules' , 'radio-station'); ?></small>
		  	</p>
		<?php
	}

	// --- update widget instance values ---
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['display_djs'] = ( isset( $new_instance['display_djs'] ) ? 1 : 0 );
		$instance['djavatar'] = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link'] = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default'] = $new_instance['default'];
		$instance['time'] = $new_instance['time'];
		$instance['show_sched'] = $new_instance['show_sched'];
		$instance['show_playlist'] = $new_instance['show_playlist'];
		$instance['show_all_sched'] = $new_instance['show_all_sched'];
		$instance['show_desc'] = $new_instance['show_desc'];
		return $instance;

	}

	// --- widget output ---
	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;
		$title = empty( $instance['title'] ) ? '' : apply_filters('widget_title', $instance['title']);
		$display_djs = $instance['display_djs'];
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
 		$default = empty( $instance['default'] ) ? '' : $instance['default'];
 		$time = empty( $instance['time'] ) ? '' : $instance['time'];
 		$show_sched = $instance['show_sched'];
 		$show_playlist = $instance['show_playlist'];
 		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false; //keep the default settings for people updating from 1.6.2 or earlier
 		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false; //keep the default settings for people updating from 2.0.12 or earlier

 		// fetch the current DJs
		$djs = radio_station_dj_get_current();
		$playlist = radio_station_myplaylist_get_now_playing();

		?>
		<div class="widget">
			<?php
				if ( !empty( $title ) ) {echo $before_title.$title.$after_title;}
				else {echo $before_title.$after_title;}
			?>

			<ul class="on-air-list">
				<?php

				// find out which DJ/show is currently scheduled to be on-air and display them
				if ( $djs['type'] == 'override' ) {
					//print_r($djs);
					echo '<li class="on-air-dj">';

					if ( $djavatar ) {
						if ( has_post_thumbnail($djs['all'][0]['post_id']) ) {
							echo '<span class="on-air-dj-avatar">'.get_the_post_thumbnail( $djs['all'][0]['post_id'], 'thumbnail' ).'</span>';
						}
					}

					echo $djs['all'][0]['title'];

					// display the schedule override if requested
					if ( $show_sched ) {

						if ( $time == 12 ) {
							$start_hour = $djs['all'][0]['sched']['start_hour'];
							if ( substr( $djs['all'][0]['sched']['start_hour'], 0, 1 ) === '0' ) {
								$start_hour = substr($djs['all'][0]['sched']['start_hour'], 1);
							}

							$end_hour = $djs['all'][0]['sched']['end_hour'];
							if ( substr( $djs['all'][0]['sched']['end_hour'], 0, 1) === '0' ) {
								$end_hour = substr($djs['all'][0]['sched']['end_hour'], 1);
							}

							echo ' <span class="on-air-dj-sched">'.$start_hour.':'.$djs['all'][0]['sched']['start_min'].' '.$djs['all'][0]['sched']['start_meridian'].'-'.$end_hour.':'.$djs['all'][0]['sched']['end_min'].' '.$djs['all'][0]['sched']['end_meridian'].'</span><br />';

						} else {

							$djs['all'][0]['sched'] = radio_station_convert_schedule_to_24hour($djs['all'][0]['sched']);
							echo ' <span class="on-air-dj-sched">'.$djs['all'][0]['sched']['start_hour'].':'.$djs['all'][0]['sched']['start_min'].' '.'-'.$djs['all'][0]['sched']['end_hour'].':'.$djs['all'][0]['sched']['end_min'].'</span><br />';

						}

						echo '</li>';
					}

				} else {

					if ( isset( $djs['all'] ) && ( count($djs['all']) > 0 ) ) {
						foreach( $djs['all'] as $dj ) {

							$scheds = get_post_meta( $dj->ID, 'show_sched', true );

							echo '<li class="on-air-dj">';
							if ( $djavatar ) {
								echo '<span class="on-air-dj-avatar">'.get_the_post_thumbnail( $dj->ID, 'thumbnail' ).'</span>';
							}

							if ( $link ) {
								echo '<a href="'.get_permalink( $dj->ID ).'">'.$dj->post_title.'</a>';
							} else {
								echo $dj->post_title;
							}

							if ( $display_djs ) {

								$names = get_post_meta( $dj->ID, 'show_user_list', true );
								$count = 0;

								if ( $names ) {

									echo '<div class="on-air-dj-names">'.__( 'With', 'radio-station' ).' ';
									foreach( $names as $name ) {
										$count ++;
										$user_info = get_userdata($name);

										echo $user_info->display_name;

										if ( ( ( $count == 1 ) && ( count($names) == 2 ) )
										  || ( ( count($names) > 2 ) && ( $count == ( count($names) - 1 ) ) ) ) {
											echo ' and ';
										} elseif ( ( $count < count($names) ) && ( count($names) > 2 ) ) {
											echo ', ';
										}
									}
									echo '</div>';
								}
							}

							if ( $show_desc ) {
								$desc_string = radio_station_shorten_string( strip_tags( $dj->post_content ), 20 );
								echo '<span class="on-air-show-desc">'.$desc_string.'</span>';
							}

							if ( $show_playlist ) {
								echo '<span class="on-air-dj-playlist"><a href="'.$playlist['playlist_permalink'].'">'.__('View Playlist', 'radio-station').'</a></span>';
							}
							echo '<span class="radio-clear"></span>';

							if ( $show_sched ) {

								if ( !$show_all_sched ) {
								    // if we only want the schedule that's relevant now to display...
									$current_sched = radio_station_current_schedule($scheds);

									if ( $current_sched ) {
										if ( $time == 12 ) {
											echo '<span class="on-air-dj-sched">'.__($current_sched['day'], 'radio-station').', '.$current_sched['start_hour'].':'.$current_sched['start_min'].' '.$current_sched['start_meridian'].'-'.$current_sched['end_hour'].':'.$current_sched['end_min'].' '.$current_sched['end_meridian'].'</span><br />';
										} else {
											$current_sched = radio_station_convert_schedule_to_24hour($current_sched);
											echo '<span class="on-air-dj-sched">'.__($current_sched['day'], 'radio-station').', '.$current_sched['start_hour'].':'.$current_sched['start_min'].' '.'-'.$current_sched['end_hour'].':'.$current_sched['end_min'].'</span><br />';
										}
									}
								} else {

									foreach ( $scheds as $sched ) {
										if ( $time == 12 ) {
											echo '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';
										} else {
											$sched = radio_station_convert_schedule_to_24hour($sched);
											echo '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.'-'.$sched['end_hour'].':'.$sched['end_min'].'</span><br />';
										}
									}
								}
							}
							echo '</li>';

						}
					} else {
						echo '<li class="on-air-dj default-dj">'.$default.'</li>';
					}
				}

				?>
			</ul>
		</div>
		<?php

 		// --- enqueue widget stylesheet in footer ---
 		// (this means it will only load if widget is on page)
 		$dj_widget_css = get_stylesheet_directory().'/djonair.css';
 		// 2.2.2: fix to file check logic (file_exists not !file_exists)
 		if ( file_exists( $dj_widget_css ) ) {
 			$version = filemtime( $dj_widget_css );
 			$url = get_stylesheet_directory_uri().'/djonair.css';
 		} else {
 			$version = filemtime( dirname(__FILE__).'/css/djonair.css' );
 			$url = plugins_url('css/djonair.css', dirname(dirname(__FILE__)).'/radio-station.php' );
		}
		wp_enqueue_style( 'dj-widget', $url, array(), $version, true );

		echo $after_widget;
	}
}

// --- register the widget ---
add_action( 'widgets_init', 'radio_station_register_dj_widget' );
function radio_station_register_dj_widget() {register_widget('DJ_Widget');}
