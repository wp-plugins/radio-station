<?php
/* Sidebar Widget - Upcoming DJ
 * Displays the the next show(s)/DJ(s) in the schedule
 * Since 2.1.1
 */
class DJ_Upcoming_Widget extends WP_Widget {

	// use __construct instead of DJ_Upcoming_Widget
	function __construct() {
		$widget_ops = array( 'classname' => 'DJ_Upcoming_Widget', 'description' => __('The upcoming DJs/Shows.', 'radio-station') );
		$widget_display_name = __( '(Radio Station) Upcoming DJ On-Air', 'radio-station' );
		parent::__construct('DJ_Upcoming_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	function form( $instance ) {

		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$display_djs = isset( $instance['display_djs'] ) ? $instance['display_djs'] : false;
		$djavatar = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$default = isset( $instance['default'] ) ? $instance['default'] : '';
		$link = isset( $instance['link'] ) ? $instance['link'] : false;
		$limit = isset( $instance['limit'] ) ? $instance['limit'] : 1;
		$time = isset( $instance['time'] ) ? $instance['time']: 12;
		$show_sched = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;

		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'radio-station' ); ?>
		  		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'display_djs' ); ?>">
		  		<input id="<?php echo $this->get_field_id( 'display_djs' ); ?>" name="<?php echo $this->get_field_name( 'display_djs' ); ?>" type="checkbox" <?php if ( $display_djs ) {echo 'checked="checked"';} ?> />
		  		<?php _e( 'Display names of the DJs on the show', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'djavatar' ); ?>">
		  		<input id="<?php echo $this->get_field_id( 'djavatar' ); ?>" name="<?php echo $this->get_field_name( 'djavatar' ); ?>" type="checkbox" <?php if ( $djavatar ) {echo 'checked="checked"';} ?> />
		  		<?php _e( 'Show Avatars', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id('link'); ?>">
		  		<input id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="checkbox" <?php if ( $link ) {echo 'checked="checked"';} ?> />
		  		<?php _e( "Link to Show/DJ's user profile", 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'default' ); ?>"><?php _e('No Additional Schedules', 'radio-station'); ?>:
		  		<input class="widefat" id="<?php echo $this->get_field_id( 'default' ); ?>" name="<?php echo $this->get_field_name('default'); ?>" type="text" value="<?php echo esc_attr( $default ); ?>" />
		  		</label>
		  		<small><?php _e( 'If no Show/DJ is scheduled for the current hour, display this name/text.', 'radio-station' ); ?></small>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'show_sched' ); ?>">
		  		<input id="<?php echo $this->get_field_id( 'show_sched' ); ?>" name="<?php echo $this->get_field_name( 'show_sched' ); ?>" type="checkbox" <?php if ( $show_sched ) {echo 'checked="checked"';} ?> />
		  		<?php _e( 'Display schedule info for this show', 'radio-station' ); ?>
		  		</label>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit', 'radio-station' ); ?>:
		  		<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
		  		</label>
		  		<small><?php _e( 'Number of upcoming DJs/Shows to display.', 'radio-station' ); ?></small>
		  	</p>

		  	<p>
		  		<label for="<?php echo $this->get_field_id( 'time' ); ?>"><?php _e( 'Time Format', 'radio-station' ); ?>:<br />
		  		<select id="<?php echo $this->get_field_id( 'time' ); ?>" name="<?php echo $this->get_field_name( 'time' ); ?>">
		  			<option value="12" <?php if ( esc_attr( $time ) == 12 ) {echo 'selected="selected"';} ?>><?php _e( '12-hour', 'radio-station' ); ?></option>
		  			<option value="24" <?php if ( esc_attr( $time ) == 24 ) {echo 'selected="selected"';} ?>><?php _e( '24-hour', 'radio-station' ); ?></option>
		  		</select>
		  		</label><br />
		  		<small><?php _e( 'Choose time format for displayed schedules.', 'radio-station' ); ?></small>
		  	</p>
		<?php
	}

	// --- update widget instance ---
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['display_djs'] = ( isset( $new_instance['display_djs'] ) ? 1 : 0 );
		$instance['djavatar'] = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link'] = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default'] = $new_instance['default'];
		$instance['limit'] = $new_instance['limit'];
		$instance['time'] = $new_instance['time'];
		$instance['show_sched'] = $new_instance['show_sched'];
		return $instance;
	}

	// --- output widget display ---
	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$display_djs = $instance['display_djs'];
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
 		$default = empty( $instance['default'] ) ? '' : $instance['default'];
 		$limit = empty( $instance['limit'] ) ? '1' : $instance['limit'];
 		$time = empty( $instance['time'] ) ? '' : $instance['time'];
 		$show_sched = $instance['show_sched'];

 		// --- find out which DJ(s) are coming up today ---
 		$djs = radio_station_dj_get_next($limit);
 		// print_r($djs);

		// 2.2.3: convert all span tags to div tags

 		?>

 		<div class="widget">
 		<?php
 		// --- output widget title ---
 		if ( !empty( $title ) ) {echo $before_title.$title.$after_title;}
 		else {echo $before_title.$after_title;}
 		?>
 		<ul class="on-air-upcoming-list">

			<?php
		 		// --- echo the show/dj currently on-air ---

		 		if ( isset($djs['all']) && ( count($djs['all']) > 0 ) ) {
		 			//print_r($djs['all']);

		 			foreach ( $djs['all'] as $showtime => $dj ) {

		 				if ( is_array($dj) && $dj['type'] == 'override' ) {

		 					echo '<li class="on-air-dj">';

								// --- show thumbnail ---
								if ( $djavatar ) {
									if ( has_post_thumbnail($dj['post_id'] ) ) {
										echo '<div class="on-air-dj-avatar">'.get_the_post_thumbnail( $dj['post_id'], 'thumbnail' ).'</div>';
									}
								}

								// --- show title ---
								echo '<div class="on-air-dj-title">'.$dj['title'].'</div>';

								// --- show schedule ---
								if ( $show_sched ) {

									if ( $time == 12 ) {
										$start_hour = $dj['sched']['start_hour'];
										if ( substr($dj['sched']['start_hour'], 0, 1) === '0' ) {
											$start_hour = substr($dj['sched']['start_hour'], 1);
										}

										$end_hour = $dj['sched']['end_hour'];
										if ( substr($dj['sched']['end_hour'], 0, 1) === '0' ) {
											$end_hour = substr($dj['sched']['end_hour'], 1);
										}

										echo '<div class="on-air-dj-sched">'.$start_hour.':'.$dj['sched']['start_min'].' '.$dj['sched']['start_meridian'].' - '.$end_hour.':'.$dj['sched']['end_min'].' '.$dj['sched']['end_meridian'].'</div>';
									} else {
										echo '<div class="on-air-dj-sched">'.$dj['sched']['start_hour'].':'.$dj['sched']['start_min'].' - '.$dj['sched']['end_hour'].':'.$dj['sched']['end_min'].'</div>';
									}
								}
		 					echo '</li>';

		 				} else {

			 				echo '<li class="on-air-dj">';

								// --- show thumbnail ---
								if ( $djavatar ) {
									echo '<div class="on-air-dj-avatar">'.get_the_post_thumbnail( $dj->ID, 'thumbnail' ).'</div>';
								}

								// --- show title ---
								echo '<div class="on-air-dj-title">';
									if ( $link ) {echo '<a href="'.get_permalink( $dj->ID ).'">'.$dj->post_title.'</a>';}
									else {echo $dj->post_title;}
								echo '</div>';

								// --- DJ names ---
								if ( $display_djs ) {

									$names = get_post_meta( $dj->ID, 'show_user_list', true );
									$count = 0;

									if($names) {
										echo '<div class="on-air-dj-names">'.__( 'With', 'radio-station' ).' ';
										foreach ( $names as $name ) {
											$count++;
											$user_info = get_userdata( $name );

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

								echo '<span class="radio-clear"></span>';

								// --- show schedule ---
								if ( $show_sched ) {

									$showtimes = explode( "|", $showtime );
									// 2.2.2: fix to weekday value to be translated
									$weekday = radio_station_translate_weekday( date('l', $showtimes[0] ) );

									if ( $time == 12 ) {
										echo '<div class="on-air-dj-sched">'.$weekday.', '.date( 'g:i a', $showtimes[0] ).' - '.date( 'g:i a', $showtimes[1] ).'</div>';
									} else {
										echo '<div class="on-air-dj-sched">'.$weekday.', '.date( 'H:i', $showtimes[0] ).' - '.date( 'H:i', $showtimes[1] ).'</div>';
									}

								}

			 				echo '</li>';
			 			}
		 			}
		 		} else {
		 			if ( $default != '' ) {
		 				echo '<li class="on-air-dj default-dj">'.$default.'</li>';
		 			}
		 		}
			?>
			</ul>
		</div>
		<?php

		echo $after_widget;
	}
}

// --- register the widget ---
add_action( 'widgets_init', 'radio_station_register_djcomingup_widget' );
function radio_station_register_djcomingup_widget() {register_widget('DJ_Upcoming_Widget');}
