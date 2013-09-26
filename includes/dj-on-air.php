<?php
/*
 * DJ and Show scheduling
 * Author: Nikki Blight
 * @Since: 1.4.6
 */

//shortcode function for current DJ on-air
function dj_show_widget($atts) {
	extract( shortcode_atts( array(
		'title' => '',	
		'show_avatar' => 0,
		'show_link' => 0,
		'default_name' => '',
		'time' => '12',
		'show_sched' => 1,
		'show_playlist' => 1
	), $atts ) );
	
	//find out which DJ(s) are currently scheduled to be on-air and display them
	$djs = dj_get_current();
	$playlist = myplaylist_get_now_playing();
	
	$dj_str = '';
	
	$dj_str .= '<div class="on-air-embedded">';
	if($title != '') {
		$dj_str .= '<h3>'.$title.'</h3>';
	}
	$dj_str .= '<ul class="on-air-list">';
	
	//echo the show/dj currently on-air
	if(count($djs['all']) > 0) {
		foreach($djs['all'] as $dj) {
			$dj_str .= '<li class="on-air-dj">';
			if($show_avatar) {
				$dj_str .= '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($dj->ID, 'thumbnail').'</span>';
			}

			if($show_link) {
				$dj_str .= '<a href="';
				$dj_str .= get_permalink($dj->ID);
				$dj_str .= '">';
				$dj_str .= $dj->post_title.'</a>';
			}
			else {
				$dj_str .= $dj->post_title;
			}
			
			if($show_playlist) {
				$dj_str .= '<span class="on-air-dj-playlist"><a href="'.$playlist['playlist_permalink'].'">View Playlist</a></span>';
			}
			
			$dj_str .= '<span class="radio-clear"></span>';
			
			if($show_sched) {
				$scheds = get_post_meta($dj->ID, 'show_sched', true);
				foreach($scheds as $sched) {
					$dj_str .= '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' ';
					if($time == 12) {
						$dj_str .= $sched['start_meridian'];
					}
					
					$dj_str .= '-'.$sched['end_hour'].':'.$sched['end_min'].' ';
					if($time == 12) {
						$dj_str .= $sched['end_meridian'];
					}
					
					$dj_str .= '</span><br />';
				}
			}
			
			$dj_str .= '</li>';
		}
	}
	else {
		$dj_str .= '<li class="on-air-dj default-dj">'.$default_name.'</li>';
	}

	$dj_str .= '</ul>';
	$dj_str .= '</div>';
	
	return $dj_str;
	
}
add_shortcode( 'dj-widget', 'dj_show_widget');

//fetch the current DJ(s) on-air
function dj_get_current() {	
	//load the info for the DJ
	global $wpdb;
	
	//get the current time
	$now = strtotime(current_time("mysql"));
	$hour = date('H', $now);
	$min = date('i', $now);
	$curDay = date('l', $now);
	$curDate = date('Y-m-d', $now);
	$tomDate = date('Y-m-d', ( $now + 36400)); //get the date for tomorrow
	
	//first check to see if a show is scheduled
	$show_shifts = $wpdb->get_results("SELECT `meta`.`post_id`, `meta`.`meta_value` FROM ".$wpdb->prefix."postmeta AS `meta`
												WHERE `meta_key` = 'show_sched';");
	
	$show_ids = array();
	foreach($show_shifts as $shift) {
		$shift->meta_value = unserialize($shift->meta_value);
		
		//if a show has no shifts, unserialize() will return false instead of an empty array... fix that to prevent errors in the foreach loop.
		if(!is_array($shift->meta_value)) {
			$shift->meta_value = array();
		}
		
		foreach($shift->meta_value as $time) {
			//check if the shift is for the current day.  If it's not, skip it
			
			
			if($time['day'] == $curDay) {
				
				//convert to 24 hour time
				if($time['start_hour'] < 10) {
					$time['start_hour'] = '0'.$time['start_hour'];
				}
				
				if($time['end_hour'] < 10) {
					$time['end_hour'] = '0'.$time['end_hour'];
				}
				
				if($time['start_meridian'] == 'pm' && $time['start_hour'] != 12) {
					$time['start_hour'] = $time['start_hour'] + 12;
				}
				
				if($time['end_meridian'] == 'pm' && $time['end_hour'] != 12) {
					$time['end_hour'] = $time['end_hour'] + 12;
				}
				
				if($time['start_meridian'] == 'am' && $time['start_hour'] == 12) {
					$time['start_hour'] = '00';
				}
				
				if($time['end_meridian'] == 'am' && $time['end_hour'] == 12) {
					$time['end_hour'] = '00';
				}
				
				//get a timestamp for the schedule start and end
				$start_time = strtotime($curDate.' '.$time['start_hour'].':'.$time['start_min']);
				
				if($time['start_meridian'] ==  'pm' && $time['end_meridian'] == 'am') { //check for shows that run overnight into the next morning
					$end_time = strtotime($tomDate.' '.$time['end_hour'].':'.$time['end_min']);
				}
				else {
					$end_time = strtotime($curDate.' '.$time['end_hour'].':'.$time['end_min']);
				}
				
				//compare to the current timestamp
				if($start_time <= $now && $end_time >= $now) {	
					$show_ids[] = $shift->post_id;
				}
			}
		}
	}
	
	$shows = array();
	foreach($show_ids as $id) {	
		$shows['all'][] = get_post($id);
	}
	$shows['type'] = 'shows';
	
	return $shows;
}

//get the next DJ or DJs scheduled to be on air based on the current time
function dj_get_next($limit = 1) {
	//load the info for the DJ
	global $wpdb;

	//get the various times/dates we need
	$curDay = date('l', strtotime(current_time("mysql")));
	$curDate = date('Y-m-d', strtotime(current_time("mysql")));
	$now = strtotime(current_time("mysql"));
	$tomorrow = date( "Y-m-d", (strtotime($curDate) + 86400) );
	$tomorrowDay = date( "l", (strtotime($curDate) + 86400) );
	
	//Fetch all schedules
	$show_shifts = $wpdb->get_results("SELECT `meta`.`post_id`, `meta`.`meta_value` FROM ".$wpdb->prefix."postmeta AS `meta`
			WHERE `meta_key` = 'show_sched';");

	$show_ids = array();
	
	foreach($show_shifts as $shift) {
		$shift->meta_value = unserialize($shift->meta_value);

		//if a show has no shifts, unserialize() will return false instead of an empty array... fix that to prevent errors in the foreach loop.
		if(!is_array($shift->meta_value)) {
			$shift->meta_value = array();
		}

		foreach($shift->meta_value as $time) {

			//check if the shift is for the current day or for tomorrow.  If it's not, skip it
			if($time['day'] != $curDay  && $time['day'] != $tomorrowDay) {
				continue;
			}
			
			//determine is the particular shift is for today or tomorrow and assign a real timestamp accordingly
			if($time['day'] == $tomorrowDay) {
				$curShift = strtotime($tomorrow.' '.$time['start_hour'].':'.$time['start_min'].':00 '.$time['start_meridian']);
			}
			else {
				$curShift = strtotime($curDate.' '.$time['start_hour'].':'.$time['start_min'].':00 '.$time['start_meridian']);
			}
			
			//if the shift occurs later than the current time, we want it
			if($curShift >= $now) {
				$show_ids[$curShift] = $shift->post_id;
			}
			
		}
	}
	
	//sort the shows by start time
	ksort($show_ids);
	
	//grab the number of shows from the list the user wants to display
	$show_ids = array_slice($show_ids, 0, $limit);
	
	//fetch detailed show information
	$shows = array();
	foreach($show_ids as $id) {
		$shows['all'][$id] = get_post($id);
	}
	$shows['type'] = 'shows';
	
	//return the information
	return $shows;
}

//shortcode for displaying upcoming DJs/shows
function dj_coming_up($atts) {
	extract( shortcode_atts( array(
			'title' => '',
			'show_avatar' => 0,
			'show_link' => 0,
			'limit' => 1,
			'time' => '12',
			'show_sched' => 1
	), $atts ) );

	//find out which DJ(s) are coming up today
	$djs = dj_get_next($limit);

	$dj_str = '';

	$dj_str .= '<div class="on-air-embedded">';
	if($title != '') {
		$dj_str .= '<h3>'.$title.'</h3>';
	}
	$dj_str .= '<ul class="on-air-list">';

	//echo the show/dj currently on-air
	if(isset($djs['all']) && count($djs['all']) > 0) {
		foreach($djs['all'] as $dj) {
			//print_r($dj);
			$dj_str .= '<li class="on-air-dj">';
			if($show_avatar) {
				$dj_str .= '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($dj->ID, 'thumbnail').'</span>';
			}

			if($show_link) {
				$dj_str .= '<a href="';
				$dj_str .= get_permalink($dj->ID);
				$dj_str .= '">';
				$dj_str .= $dj->post_title.'</a>';
			}
			else {
				$dj_str .= $dj->post_title;
			}
			
			$dj_str .= '<span class="radio-clear"></span>';
			
			if($show_sched) {
				$scheds = get_post_meta($dj->ID, 'show_sched', true);
				
				foreach($scheds as $sched) {
					$dj_str .= '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' ';
					if($time == 12) {
						$dj_str .= $sched['start_meridian'];
					}
				
					$dj_str .= '-'.$sched['end_hour'].':'.$sched['end_min'].' ';
					if($time == 12) {
						$dj_str .= $sched['end_meridian'];
					}
				
					$dj_str .= '</span><br />';
				}
			}
				
			$dj_str .= '</li>';
		}
	}
	else {
		$dj_str .= '<li class="on-air-dj default-dj">'.__('None Upcoming', 'radio-station').'</li>';
	}

	$dj_str .= '</ul>';
	$dj_str .= '</div>';

	return $dj_str;

}
add_shortcode( 'dj-coming-up-widget', 'dj_coming_up');

/* Sidebar widget functions */
class DJ_Widget extends WP_Widget {
	
	function DJ_Widget() {
		$widget_ops = array('classname' => 'DJ_Widget', 'description' => __('The current on-air DJ.', 'radio-station'));
		$this->WP_Widget('DJ_Widget', __('Radio Station: Show/DJ On-Air', 'radio-station'), $widget_ops);
	}
 
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$djavatar = $instance['djavatar'];
		$default = $instance['default'];
		$link = $instance['link'];
		$time = $instance['time'];
		$show_sched = $instance['show_sched'];
		$show_playlist = $instance['show_playlist'];
		
		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'radio-station'); ?>: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('djavatar'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('djavatar'); ?>" name="<?php echo $this->get_field_name('djavatar'); ?>" type="checkbox" <?php if($djavatar) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Show Avatars', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('link'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="checkbox" <?php if($link) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e("Link to the Show/DJ's profile", 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('show_sched'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('show_sched'); ?>" name="<?php echo $this->get_field_name('show_sched'); ?>" type="checkbox" <?php if($show_sched) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Display schedule info for this show', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('show_playlist'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('show_playlist'); ?>" name="<?php echo $this->get_field_name('show_playlist'); ?>" type="checkbox" <?php if($show_playlist) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e("Display link to show's playlist", 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('default'); ?>"><?php _e('Default DJ Name', 'radio-station'); ?>: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('default'); ?>" name="<?php echo $this->get_field_name('default'); ?>" type="text" value="<?php echo esc_attr($default); ?>" />
		  		</label>
		  		<small><?php _e('If no Show/DJ is scheduled for the current hour, display this name/text.', 'radio-station'); ?></small>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('time'); ?>"><?php _e('Time Format', 'radio-station'); ?>:<br /> 
		  		<select id="<?php echo $this->get_field_id('time'); ?>" name="<?php echo $this->get_field_name('time'); ?>">
		  			<option value="12" <?php if(esc_attr($time) == 12) { echo 'selected="selected"'; } ?>><?php _e('12-hour', 'radio-station'); ?></option>
		  			<option value="24" <?php if(esc_attr($time) == 24) { echo 'selected="selected"'; } ?>><?php _e('24-hour', 'radio-station'); ?></option>
		  		</select>
		  		</label><br />
		  		<small><?php _e('Choose time format for displayed schedules', 'radio-station'); ?></small>
		  	</p>
		<?php
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['djavatar'] = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link'] = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default'] = $new_instance['default'];
		$instance['time'] = $new_instance['time'];
		$instance['show_sched'] = $new_instance['show_sched'];
		$instance['show_playlist'] = $new_instance['show_playlist'];
		return $instance;
	}
 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
 		$default = empty($instance['default']) ? '' : $instance['default'];
 		$time = empty($instance['time']) ? '' : $instance['time'];
 		$show_sched = $instance['show_sched'];
 		$show_playlist = $instance['show_playlist'];
 		
 		//fetch the current DJs
		$djs = dj_get_current();
		$playlist = myplaylist_get_now_playing();
		?>
		<div class="widget">
			<?php 
				if (!empty($title)) {
					echo $before_title . $title . $after_title;
				}
				else {
					echo $before_title.$after_title;
				}
			?>
			
			<ul class="on-air-list">
				<?php 
				//find out which DJ/show is currently scheduled to be on-air and display them
				
				if(isset($djs['all']) && count($djs['all']) > 0) {
					foreach($djs['all'] as $dj) {
						
						$scheds = get_post_meta($dj->ID, 'show_sched', true);
						
						echo '<li class="on-air-dj">';
						if($djavatar) {
							echo '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($dj->ID, 'thumbnail').'</span>';
						}
							
						if($link) {
							echo '<a href="';
							echo get_permalink($dj->ID);
							echo '">';
							echo $dj->post_title.'</a>';
						}
						else {
							echo $dj->post_title;
						}
						
						if($show_playlist) {
							echo '<span class="on-air-dj-playlist"><a href="'.$playlist['playlist_permalink'].'">View Playlist</a></span>';
						}
						echo '<span class="radio-clear"></span>';
						
						if($show_sched) {
							foreach($scheds as $sched) {
								if($time == 12) {
									echo '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';
								}
								else {
									if($sched['start_meridian'] == 'pm' && $sched['start_hour'] != 12) {
										$sched['start_hour'] = $sched['start_hour'] + 12;
									}
									if($sched['start_meridian'] == 'am' && $sched['start_hour'] < 10) {
										$sched['start_hour'] = "0".$sched['start_hour'];
									}
									if($sched['start_meridian'] == 'am' && $sched['start_hour'] == 12) {
										$sched['start_hour'] = '00';
									}
									
									if($sched['end_meridian'] == 'pm' && $sched['end_hour'] != 12) {
										$sched['end_hour'] = $sched['end_hour'] + 12;
									}
									if($sched['end_meridian'] == 'am' && $sched['end_hour'] < 10) {
										$sched['end_hour'] = "0".$sched['end_hour'];
									}
									if($sched['end_meridian'] == 'am' && $sched['end_hour'] == 12) {
										$sched['end_hour'] = '00';
									}
									
									echo '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.'-'.$sched['end_hour'].':'.$sched['end_min'].'</span><br />';
								}
							}
						}
						echo '</li>';
						
					}
				}
				else {
					echo '<li class="on-air-dj default-dj">'.$default.'</li>';
				}
				
				?>
			</ul>
		</div>
		<?php
 
		echo $after_widget;
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("DJ_Widget");') );


/* Sidebar widget functions */
class DJ_Upcoming_Widget extends WP_Widget {

	function DJ_Upcoming_Widget() {
		$widget_ops = array('classname' => 'DJ_Upcoming_Widget', 'description' => __('The upcoming DJs/Shows.', 'radio-station'));
		$this->WP_Widget('DJ_Upcoming_Widget', __('Radio Station: Upcoming DJ On-Air', 'radio-station'), $widget_ops);
	}

	function form($instance) {
		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$djavatar = $instance['djavatar'];
		$default = $instance['default'];
		$link = $instance['link'];
		$limit = $instance['limit'];
		$time = $instance['time'];
		$show_sched = $instance['show_sched'];

		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'radio-station'); ?> 
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('djavatar'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('djavatar'); ?>" name="<?php echo $this->get_field_name('djavatar'); ?>" type="checkbox" <?php if($djavatar) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Show Avatars', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('link'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="checkbox" <?php if($link) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e("Link to Show/DJ's user profile", 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('default'); ?>"><?php _e('No Additional Schedules', 'radio-station'); ?>: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('default'); ?>" name="<?php echo $this->get_field_name('default'); ?>" type="text" value="<?php echo esc_attr($default); ?>" />
		  		</label>
		  		<small><?php _e('If no Show/DJ is scheduled for the current hour, display this name/text.', 'radio-station'); ?></small>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('show_sched'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('show_sched'); ?>" name="<?php echo $this->get_field_name('show_sched'); ?>" type="checkbox" <?php if($show_sched) { echo 'checked="checked"'; } ?> /> 
		  		<?php _e('Display schedule info for this show', 'radio-station'); ?>
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit', 'radio-station'); ?>: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($limit); ?>" />
		  		</label>
		  		<small><?php _e('Number of upcoming DJs/Shows to display.', 'radio-station'); ?></small>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('time'); ?>"><?php _e('Time Format', 'radio-station'); ?>:<br /> 
		  		<select id="<?php echo $this->get_field_id('time'); ?>" name="<?php echo $this->get_field_name('time'); ?>">
		  			<option value="12" <?php if(esc_attr($time) == 12) { echo 'selected="selected"'; } ?>><?php _e('12-hour', 'radio-station'); ?></option>
		  			<option value="24" <?php if(esc_attr($time) == 24) { echo 'selected="selected"'; } ?>><?php _e('24-hour', 'radio-station'); ?></option>
		  		</select>
		  		</label><br />
		  		<small><?php _e('Choose time format for displayed schedules.', 'radio-station'); ?></small>
		  	</p>
		<?php
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['djavatar'] = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link'] = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default'] = $new_instance['default'];
		$instance['limit'] = $new_instance['limit'];
		$instance['time'] = $new_instance['time'];
		$instance['show_sched'] = $new_instance['show_sched'];
		return $instance;
	}
 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
 		$default = empty($instance['default']) ? '' : $instance['default'];
 		$limit = empty($instance['limit']) ? '1' : $instance['limit'];
 		$time = empty($instance['time']) ? '' : $instance['time'];
 		$show_sched = $instance['show_sched'];

 		//find out which DJ(s) are coming up today
 		$djs = dj_get_next($limit);
 		?>
 		
 		<div class="widget">
 		<?php
 		if (!empty($title)) {
 			echo $before_title . $title . $after_title;
 		}
 		else {
 			echo $before_title.$after_title;
 		}
 		?>
 		<ul class="on-air-upcoming-list">
			<?php 
		 		//echo the show/dj currently on-air
		 		if(isset($djs['all']) && count($djs['all']) > 0) {
		 			foreach($djs['all'] as $dj) {
		 				//print_r($dj);
		 				echo '<li class="on-air-dj">';
		 				if($djavatar) {
		 					echo '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($dj->ID, 'thumbnail').'</span>';
		 				}
		 		
		 				if($link) {
		 					echo '<a href="';
		 					echo get_permalink($dj->ID);
		 					echo '">';
		 					echo $dj->post_title.'</a>';
		 				}
		 				else {
		 					echo $dj->post_title;
		 				}
		 				
		 				echo '<span class="radio-clear"></span>';
		 				
		 				if($show_sched) {
			 				$scheds = get_post_meta($dj->ID, 'show_sched', true);
			 				
			 				foreach($scheds as $sched) {
			 					if($time == 12) {
			 						echo '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';
			 					}
			 					else {
			 						if($sched['start_meridian'] == 'pm' && $sched['start_hour'] != 12) {
			 							$sched['start_hour'] = $sched['start_hour'] + 12;
			 						}
			 						if($sched['start_meridian'] == 'am' && $sched['start_hour'] < 10) {
			 							$sched['start_hour'] = "0".$sched['start_hour'];
			 						}
			 						if($sched['start_meridian'] == 'am' && $sched['start_hour'] == 12) {
			 							$sched['start_hour'] = '00';
			 						}
			 						
			 						if($sched['end_meridian'] == 'pm' && $sched['end_hour'] != 12) {
			 							$sched['end_hour'] = $sched['end_hour'] + 12;
			 						}
			 						if($sched['end_meridian'] == 'am' && $sched['end_hour'] < 10) {
			 							$sched['end_hour'] = "0".$sched['end_hour'];
			 						}
			 						if($sched['end_meridian'] == 'am' && $sched['end_hour'] == 12) {
			 							$sched['end_hour'] = '00';
			 						}
			 					
			 						echo '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.'-'.$sched['end_hour'].':'.$sched['end_min'].'</span><br />';
			 					}
			 				}
		 				}
		 		
		 				echo '</li>';
		 			}
		 		}
		 		else {
		 			if($default != '') {
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
add_action( 'widgets_init', create_function('', 'return register_widget("DJ_Upcoming_Widget");') );

?>