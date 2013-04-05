<?php
/*
 * DJ and Show scheduling
 * Author: Nikki Blight
 */

//shortcode function for current DJ on-air
function dj_show_widget($atts) {
	extract( shortcode_atts( array(
		'title' => '',	
		'show_avatar' => 0,
		'show_link' => 0,
		'default_name' => ''
	), $atts ) );
	
	//find out which DJ(s) are currently scheduled to be on-air and display them
	$djs = dj_get_current();
	$scheds = get_post_meta($dj->ID, 'show_sched', true);
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
			foreach($scheds as $sched) {
				$dj_str .= '<span class="on-air-dj-sched">'.$sched['day'].'s, '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';
			}
			
			$dj_str .= '</li>';
			
			$dj_str .= '<span class="on-air-dj-playlist"><a href="'.$playlist['playlist_permalink'].'">View Playlist</a></span>';
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
	$hour = date('H', strtotime(current_time("mysql")));
	$min = date('i', strtotime(current_time("mysql")));
	$curDay = date('l', strtotime(current_time("mysql")));
	$curDate = date('Y-m-d', strtotime(current_time("mysql")));
	$now = strtotime(current_time("mysql"));
	
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
				
				//get a timestamp for the schedule start and end
				$start_time = strtotime($curDate.' '.$time['start_hour'].':'.$time['start_min']);
				$end_time = strtotime($curDate.' '.$time['end_hour'].':'.$time['end_min']);
				
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

	//get the current time
	$hour = date('H', strtotime(current_time("mysql")));
	$min = date('i', strtotime(current_time("mysql")));
	$curDay = date('l', strtotime(current_time("mysql")));
	$curDate = date('Y-m-d', strtotime(current_time("mysql")));
	$now = strtotime(current_time("mysql"));

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
			if($time['day'] != $curDay) {
				continue;
			}

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

			//get a timestamp for the schedule start and end
			$start_time = strtotime($curDate.' '.$time['start_hour'].':'.$time['start_min']);
			$end_time = strtotime($curDate.' '.$time['end_hour'].':'.$time['end_min']);

			
			//if the start time is at some point in the future, we want it
			if($start_time > $now) {
				$show_ids[$start_time] = $shift->post_id;
			}
		}
		//sort by start time
		ksort($show_ids);
		
		$show_ids = array_slice($show_ids, 0, $limit);
	}

	$shows = array();
	foreach($show_ids as $id) {
		$shows['all'][] = get_post($id);
	}
	$shows['type'] = 'shows';

	return $shows;
}

//shortcode for displaying upcoming DJs/shows
function dj_coming_up($atts) {
	extract( shortcode_atts( array(
			'title' => '',
			'show_avatar' => 0,
			'show_link' => 0,
			'limit' => 1
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
	if(count($djs['all']) > 0) {
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
			
			$scheds = get_post_meta($dj->ID, 'show_sched', true);
				
			$dj_str .= '<span class="radio-clear"></span>';
			foreach($scheds as $sched) {
				$dj_str .= '<span class="on-air-dj-sched">'.$sched['day'].'s, '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';
			}
				
			$dj_str .= '</li>';
		}
	}
	else {
		$dj_str .= '<li class="on-air-dj default-dj">None Upcoming</li>';
	}

	$dj_str .= '</ul>';
	$dj_str .= '</div>';

	return $dj_str;

}
add_shortcode( 'dj-coming-up-widget', 'dj_coming_up');

/* Sidebar widget functions */
class DJ_Widget extends WP_Widget {
	
	function DJ_Widget() {
		$widget_ops = array('classname' => 'DJ_Widget', 'description' => 'The current on-air DJ.');
		$this->WP_Widget('DJ_Widget', 'Radio Station: DJ On-Air', $widget_ops);
	}
 
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$djavatar = $instance['djavatar'];
		$default = $instance['default'];
		$link = $instance['link'];
		
		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('djavatar'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('djavatar'); ?>" name="<?php echo $this->get_field_name('djavatar'); ?>" type="checkbox" <?php if($djavatar) { echo 'checked="checked"'; } ?> /> 
		  		Show Avatars
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('link'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="checkbox" <?php if($link) { echo 'checked="checked"'; } ?> /> 
		  		Link to DJ's user profile
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('default'); ?>">Default DJ Name: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('default'); ?>" name="<?php echo $this->get_field_name('default'); ?>" type="text" value="<?php echo attribute_escape($default); ?>" />
		  		</label>
		  		<small>If no DJ is scheduled for the current hour, display this name/text.</small>
		  	</p>
		<?php
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['djavatar'] = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link'] = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default'] = $new_instance['default']; 
		return $instance;
	}
 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
 		$default = empty($instance['default']) ? '' : $instance['default'];
		
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
				
				if(count($djs['all']) > 0) {
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
						
						echo '<span class="radio-clear"></span>';
						foreach($scheds as $sched) {
							echo '<span class="on-air-dj-sched">'.$sched['day'].'s, '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';	
						}
						echo '</li>';
						
						echo '<span class="on-air-dj-playlist"><a href="'.$playlist['playlist_permalink'].'">View Playlist</a></span>';
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
		$widget_ops = array('classname' => 'DJ_Upcoming_Widget', 'description' => 'The upcoming DJs/Shows.');
		$this->WP_Widget('DJ_Upcoming_Widget', 'Radio Station: Upcoming DJ On-Air', $widget_ops);
	}

	function form($instance) {
		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$djavatar = $instance['djavatar'];
		$default = $instance['default'];
		$link = $instance['link'];
		$limit = $instance['limit'];

		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('djavatar'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('djavatar'); ?>" name="<?php echo $this->get_field_name('djavatar'); ?>" type="checkbox" <?php if($djavatar) { echo 'checked="checked"'; } ?> /> 
		  		Show Avatars
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('link'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="checkbox" <?php if($link) { echo 'checked="checked"'; } ?> /> 
		  		Link to DJ's user profile
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('default'); ?>">No Additional Schedules: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('default'); ?>" name="<?php echo $this->get_field_name('default'); ?>" type="text" value="<?php echo attribute_escape($default); ?>" />
		  		</label>
		  		<small>If no DJ is scheduled for the current hour, display this name/text.</small>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('limit'); ?>">Limit: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo attribute_escape($limit); ?>" />
		  		</label>
		  		<small>Number of upcoming DJs/Shows to display.</small>
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
		 		if(count($djs['all']) > 0) {
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
		 		
		 				$scheds = get_post_meta($dj->ID, 'show_sched', true);
		 				
		 				echo '<span class="radio-clear"></span>';
		 				foreach($scheds as $sched) {
		 					echo '<span class="on-air-dj-sched">'.$sched['day'].'s, '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';
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