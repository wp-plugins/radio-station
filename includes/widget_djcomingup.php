<?php
/* Sidebar Widget - Upcoming DJ
 * Displays the the next show(s)/DJ(s) in the schedule 
 * Since 2.0.0
 */
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
 		
 		//print_r($djs);
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
		 				
		 				if(is_array($dj) && $dj['type'] == 'override') {
		 					echo '<li class="on-air-dj">';
		 					echo $dj['title'];
		 					if($show_sched) {
		 						
		 						if($time == 12) {
		 							echo '<span class="on-air-dj-sched">'.$dj['sched']['start_hour'].':'.$dj['sched']['start_min'].' '.$dj['sched']['start_meridian'].'-'.$dj['sched']['end_hour'].':'.$dj['sched']['end_min'].' '.$dj['sched']['end_meridian'].'</span><br />';
		 						}
		 						else {
		 							$dj['sched'] = station_convert_schedule_to_24hour($dj['sched']);
		 						
		 							echo '<span class="on-air-dj-sched">'.$dj['sched']['start_hour'].':'.$dj['sched']['start_min'].' '.'-'.$dj['sched']['end_hour'].':'.$dj['sched']['end_min'].'</span><br />';
		 						
		 						}
		 					}
		 					echo '</li>';
		 				}
		 				else { //print as normal
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
				 				
				 				if(isset($scheds[0])) {
				 					if($time == 12) {
				 						echo '<span class="on-air-dj-sched">'.__($scheds[0]['day'], 'radio-station').', '.$scheds[0]['start_hour'].':'.$scheds[0]['start_min'].' '.$scheds[0]['start_meridian'].'-'.$scheds[0]['end_hour'].':'.$scheds[0]['end_min'].' '.$scheds[0]['end_meridian'].'</span><br />';
				 					}
				 					else {
				 						
				 						$scheds[0] = station_convert_schedule_to_24hour($scheds[0]);
				 					
				 						echo '<span class="on-air-dj-sched">'.__($scheds[0]['day'], 'radio-station').', '.$scheds[0]['start_hour'].':'.$scheds[0]['start_min'].' '.'-'.$scheds[0]['end_hour'].':'.$scheds[0]['end_min'].'</span><br />';
				 					}
				 				}
			 				}
			 		
			 				echo '</li>';
			 			}
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