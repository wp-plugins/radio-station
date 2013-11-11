<?php
/*
 * Master Show schedule
 * Author: Nikki Blight
 * @Since: 1.6.0
 */

//jQuery is needed by the output of this code, so let's make sure we have it available
function master_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}
add_action( 'init', 'master_scripts' );

function master_create_post_types() {

	register_post_type( 'override',
			array(
					'labels' => array(
							'name' => __( 'Schedule Override', 'radio-station' ),
							'singular_name' => __( 'Schedule Override', 'radio-station' ),
							'add_new' => __( 'Add Schedule Override', 'radio-station' ),
							'add_new_item' => __( 'Add Schedule Override', 'radio-station' ),
							'edit_item' => __( 'Edit Schedule Override', 'radio-station' ),
							'new_item' => __( 'New Schedule Override', 'radio-station' ),
							'view_item' => __( 'View Schedule Override', 'radio-station' )
					),
					'show_ui' => true,
					'description' => __('Post type for Schedule Override', 'radio-station'),
					'menu_position' => 5,
					'menu_icon' => WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png',
					'public' => true,
					'hierarchical' => false,
					'supports' => array('title'),
					'can_export' => true,
					'rewrite' => array('slug' => 'show-override'),
					'capability_type' => 'show',
					'map_meta_cap' => true
			)
	);
}
add_action( 'init', 'master_create_post_types' );

//Adds a box to the side column of the show edit screens
function master_add_sched_box() {
	add_meta_box(
			'dynamicSchedOver_sectionid',
			__( 'Override Schedule', 'radio-station' ),
			'master_inner_sched_custom_box',
			'override');
}
add_action( 'add_meta_boxes', 'master_add_sched_box' );

function master_inner_sched_custom_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSchedOver_noncename' );
	?>
	    	<div id="meta_inner" class="sched-override">
		    <?php
		
		    //get the saved meta as an array
		    $track = get_post_meta($post->ID,'show_override_sched',false);
		    if($track) {
		    	$track = $track[0];
		    }
		            
			?>
			<script type="text/javascript">
			jQuery(document).ready(function() {
			    jQuery('#OverrideDate').datepicker({
			        dateFormat : 'yy-mm-dd'
			    });
			});
			</script>
						
            	<p>
            		<?php _e('Date', 'radio-station'); ?>: 
            		<input type="text" id="OverrideDate" name="show_sched[date]" value="<?php if(isset($track['date']) && $track['date'] != '') { echo $track['date']; } ?>"/>
            		 - 
            		<?php _e('Start Time', 'radio-station'); ?>: 
            		<select name="show_sched[start_hour]">
            			<option value=""></option>
            		<?php for($i=1; $i<=12; $i++): ?>
            			<option value="<?php echo $i; ?>"<?php if(isset($track['start_hour']) && $track['start_hour'] == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
            		<?php endfor; ?>
            		</select>
            		<select name="show_sched[start_min]">
            			<option value=""></option>
            		<?php for($i=0; $i<60; $i++): ?>
            			<?php 
							$min = $i;
							if($i < 10) {
								$min = '0'.$i;
							}
						?>
            			<option value="<?php echo $min; ?>"<?php if(isset($track['start_min']) && $track['start_min'] == $min) { echo ' selected="selected"'; } ?>><?php echo $min; ?></option>
            		<?php endfor; ?>
            		</select>
            		<select name="show_sched[start_meridian]">
            			<option value=""></option>
            			<option value="am"<?php if(isset($track['start_meridian']) && $track['start_meridian'] == "am") { echo ' selected="selected"'; } ?>>am</option>
            			<option value="pm"<?php if(isset($track['start_meridian']) && $track['start_meridian'] == "pm") { echo ' selected="selected"'; } ?>>pm</option>
            		</select>
            		
            		 -  
            		<?php _e('End Time', 'radio-station'); ?>: 
            		<select name="show_sched[end_hour]">
            			<option value=""></option>
            		<?php for($i=1; $i<=12; $i++): ?>
            			<option value="<?php echo $i; ?>"<?php if(isset($track['end_hour']) && $track['end_hour'] == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
            		<?php endfor; ?>
            		</select>
            		<select name="show_sched[end_min]">
            			<option value=""></option>
            		<?php for($i=0; $i<60; $i++): ?>
            			<?php 
							$min = $i;
							if($i < 10) {
								$min = '0'.$i;
							}
						?>
            			<option value="<?php echo $min; ?>"<?php if(isset($track['end_min']) && $track['end_min'] == $min) { echo ' selected="selected"'; } ?>><?php echo $min; ?></option>
            		<?php endfor; ?>
            		</select>
            		<select name="show_sched[end_meridian]">
            			<option value=""></option>
            			<option value="am"<?php if(isset($track['end_meridian']) && $track['end_meridian'] == "am") { echo ' selected="selected"'; } ?>>am</option>
            			<option value="pm"<?php if(isset($track['end_meridian']) && $track['end_meridian'] == "pm") { echo ' selected="selected"'; } ?>>pm</option>
            		</select>
            		
            	</p>
		</div>
<?php
}

//save the custom fields when a show override is saved
function master_save_showpostdata( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times	
	if (isset($_POST['dynamicMetaSchedOver_noncename'])){
		if ( !wp_verify_nonce( $_POST['dynamicMetaSchedOver_noncename'], plugin_basename( __FILE__ ) ) )
		return;
	}else{return;
	}
	
	// OK, we're authenticated: we need to find and save the data
	$sched = $_POST['show_sched'];
	update_post_meta($post_id,'show_override_sched',$sched);
}
add_action( 'save_post', 'master_save_showpostdata' );

//get any schedule overrides for today's date.  If currenthour is true, only overrides that are in effect NOW will be returned
function master_get_overrides($currenthour = false) {
	global $wpdb;
	
	$now = strtotime(current_time("mysql"));
	$date = date('Y-m-d', $now);
	$tomDate = date('Y-m-d', ( $now + 36400)); //get the date for tomorrow
	
	$show_shifts = $wpdb->get_results("SELECT `meta`.`post_id` FROM ".$wpdb->prefix."postmeta AS `meta`
			WHERE `meta_key` = 'show_override_sched'
			AND `meta_value` LIKE '%".$date."%';");
	
	$scheds = array();
	if($show_shifts) {
		foreach($show_shifts as $shift) {
			$next_sched = get_post_meta($shift->post_id,'show_override_sched',false);
			$time = $next_sched[0];
			
			if($currenthour) {
				//convert to 24 hour time
				$check = array();
				$check = $time;
				
				if($time['start_hour'] < 10) {
					$check['start_hour'] = '0'.$time['start_hour'];
				}
				
				if($time['end_hour'] < 10) {
					$check['end_hour'] = '0'.$time['end_hour'];
				}
				
				if($time['start_meridian'] == 'pm' && $time['start_hour'] != 12) {
					$check['start_hour'] = $time['start_hour'] + 12;
				}
				
				if($time['end_meridian'] == 'pm' && $time['end_hour'] != 12) {
					$check['end_hour'] = $time['end_hour'] + 12;
				}
				
				if($time['start_meridian'] == 'am' && $time['start_hour'] == 12) {
					$check['start_hour'] = '00';
				}
				
				if($time['end_meridian'] == 'am' && $time['end_hour'] == 12) {
					$check['end_hour'] = '00';
				}
				
				//get a timestamp for the schedule start and end
				$start_time = strtotime($date.' '.$check['start_hour'].':'.$check['start_min']);
				
				if($check['start_meridian'] ==  'pm' && $check['end_meridian'] == 'am') { //check for shows that run overnight into the next morning
					$end_time = strtotime($tomDate.' '.$check['end_hour'].':'.$check['end_min']);
				}
				else {
					$end_time = strtotime($date.' '.$check['end_hour'].':'.$check['end_min']);
				}
				
				//compare to the current timestamp
				if($start_time <= $now && $end_time >= $now) {
					$title = get_the_title($shift->post_id);
					
					$scheds[] = array('post_id' => $shift->post_id, 'title' => $title, 'sched' => $time);
				}
				else {
					continue;
				}
			}
			else {
			
				$title = get_the_title($shift->post_id);
				$sched = get_post_meta($shift->post_id,'show_override_sched',false);
				$scheds[] = array('post_id' => $shift->post_id, 'title' => $title, 'sched' => $sched[0]);
			}
		}
	}

	return $scheds;
}

//shortcode to display a full schedule of DJs and shows
function master_schedule($atts) {
	global $wpdb;
	
	extract( shortcode_atts( array(
			'time' => '12',
			'show_link' => 1,
			'display_show_time' => 1,
			'list' => 0
	), $atts ) );
	
	$timeformat = $time;
	
	//$overrides = master_get_overrides(true);

	//set up the structure of the master schedule
	$default_dj = get_option('dj_default_name');
	$master_list = array(
					'0' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'1' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'2' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'3' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'4' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'5' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'6' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'7' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'8' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'9' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'10' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'11' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'12' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'13' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'14' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'15' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'16' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'17' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'18' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'19' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'20' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'21' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'22' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					'23' => array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array()),
					);

	
	//get the shows schedules
	//$show_shifts = $wpdb->get_results("SELECT `meta`.`post_id`, `meta`.`meta_value` FROM ".$wpdb->prefix."postmeta AS `meta`
	//											WHERE `meta_key` = 'show_sched';");
	
	//get the show schedules, excluding shows marked as inactive
	$show_shifts = $wpdb->get_results("SELECT `meta`.`post_id`, `meta`.`meta_value` FROM ".$wpdb->prefix."postmeta AS `meta`
										JOIN ".$wpdb->prefix."postmeta AS `active` ON `meta`.`post_id` = `active`.`post_id`
													WHERE `meta`.`meta_key` = 'show_sched'
													AND ( `active`.`meta_key` = 'show_active'
													AND `active`.`meta_value` = 'on');");
	
	//insert schedules into the master list
	foreach($show_shifts as $shift) {
		$shift->meta_value = unserialize($shift->meta_value);
		
		//if a show is not scheduled yet, unserialize will return false... fix that.
		if(!is_array($shift->meta_value)) {
			$shift->meta_value = array();
		}
		
		foreach($shift->meta_value as $time) {
			//switch to 24-hour time
			if($time['start_meridian'] == 'pm' && $time['start_hour'] != 12) {
				$time['start_hour'] += 12;
			}
			if($time['start_meridian'] == 'am' && $time['start_hour'] == 12) {
				$time['start_hour'] = 0;
			}
			
			if($time['end_meridian'] == 'pm' && $time['end_hour'] != 12) {
				$time['end_hour'] += 12;
			}
			if($time['end_meridian'] == 'am' && $time['end_hour'] == 12) {
				$time['end_hour'] = 0;
			}
			
			$master_list[$time['start_hour']][$time['day']][$time['start_min']] = array('id'=> $shift->post_id, 'time' => $time);
		}
	}
	
	//sort the array by time
	foreach($master_list as $hour => $days) {
		foreach($days as $day => $min) {
			ksort($min);
			$master_list[$hour][$day] = $min;
			
			//we need to take into account shows that start late at night and end the following day
			foreach($min as $i => $time) {
				//if it ends at midnight, we don't need to worry about carry-over
				if($time['time']['end_hour'] == "0" && $time['time']['end_min'] == "00") {
					continue;
				}
				
				//if it ends after midnight, fix it
				if($time['time']['start_meridian'] ==  'pm' && $time['time']['end_meridian'] == 'am') {
					if($time == 12) {
						$time['time']['real_start'] = ($time['time']['start_hour']-12).':'.$time['time']['start_min'].$time['time']['start_meridian'];
					}
					else {
						$pad_hour = "";
						if($time['time']['start_hour'] < 10) {
							$pad_hour = "0";
						}
						$time['time']['real_start'] = $pad_hour.$time['time']['start_hour'].':'.$time['time']['start_min'];
					}
					$time['time']['start_hour'] = "0";
					$time['time']['start_min'] = "00";
					$time['time']['start_meridian'] = "am";
					
					if($day == 'Sunday') { $nextday = 'Monday'; }
					if($day == 'Monday') { $nextday = 'Tuesday'; }
					if($day == 'Tuesday') { $nextday = 'Wednesday'; }
					if($day == 'Wednesday') { $nextday = 'Thursday'; }
					if($day == 'Thursday') { $nextday = 'Friday'; }
					if($day == 'Friday') { $nextday = 'Saturday'; }
					if($day == 'Saturday') { $nextday = 'Sunday'; }
					
					$master_list[0][$nextday]['00'] = $time;
				}
			}
		}
	}
	
	$output = '';
	//print_r($master_list);
	if($list == 1) {
		//output as a list	
		$flip = array("Sunday" => array(), "Monday" => array(), "Tuesday" => array(), "Wednesday" => array(), "Thursday" => array(), "Friday" => array(), "Saturday" => array());
		foreach($master_list as $hour => $days) {
			foreach($days as $day => $mins) {
				foreach($mins as $fmin => $fshow) {
					$flip[$day][$hour][$fmin] = $fshow;
				}
			}
		}
		
		$output .= '<ul class="master-list">';
		
		foreach($flip as $day => $hours) {
			$output .= '<li class="master-list-day">';
			$output .= $day;
			$output .= '<ul>';
			foreach($hours as $hour => $mins) {
				
				foreach($mins as $min => $show) {
					$output .= '<li>';
					$output .= '<span class="show-title">';
					if($show_link) {
						$output .= '<a href="'.get_permalink($show['id']).'">'.get_the_title($show['id']).'</a>';
					}
					else {
						$output .= get_the_title($show['id']);
					}
					$output .= '</span>';
					
					if($display_show_time) {
						//convert back to 12-hour time if 12-hour has been selected
						if($timeformat == 12) {
							if($show['time']['start_meridian'] == 'pm' && $show['time']['start_hour'] != 12) {
								$show['time']['start_hour'] = $show['time']['start_hour'] - 12;
							}
							if($show['time']['start_meridian'] == 'am' && $show['time']['start_hour'] == 0) {
								$show['time']['start_hour'] = 12;
							}
								
							if($show['time']['end_meridian'] == 'pm' && $show['time']['end_hour'] != 12) {
								$show['time']['end_hour'] = $show['time']['end_hour'] - 12;
							}
							if($show['time']['end_meridian'] == 'am' && $show['time']['end_hour'] == 0) {
								$show['time']['end_hour'] = 12;
							}
						}
					
						$output .= ' <span class="show-time">';
							$output .= $show['time']['start_hour'].':'.$show['time']['start_min'];
							if($timeformat == 12) {
								$output .= $show['time']['start_meridian'];;
							}
								
							$output .= ' - '.$show['time']['end_hour'].':'.$show['time']['end_min'];
								
							if($timeformat == 12) {
								$output .= $show['time']['end_meridian'];
							}
						
						$output .= '</span>';
					}
					
					if(isset($show['time']['encore']) && $show['time']['encore'] == 'on') {
						$output .= ' <span class="show-encore">'.__('encore airing', 'radio-station').'</span>';
					}
						
					$link = get_post_meta($show['id'], 'show_file', true);
					if($link != '') {
						$output .= ' <span class="show-file"><a href="'.$link.'">'.__('Audio File', 'radio-station').'</a>';
					}
					
					$output .= '</li>';
				}
			}
			$output .= '</ul>';
			$output .= '</li>';
		}
		
		$output .= '</ul>';
	}
	else {
		//create the output in a table
		$output .= master_fetch_js_filter();
		
		$output .= '<table id="master-program-schedule">';
		$output .= '<tr class="master-program-day-row"> <th></th> <th>'.__('Sun', 'radio-station').'</th> <th>'.__('Mon', 'radio-station').'</th> <th>'.__('Tue', 'radio-station').'</th> <th>'.__('Wed', 'radio-station').'</th> <th>'.__('Thu', 'radio-station').'</th> <th>'.__('Fri', 'radio-station').'</th> <th>'.__('Sat', 'radio-station').'</th> </tr>';
		
		if(!isset($nextskip)) {
			$nextskip = array();
		}
		
		foreach($master_list as $hour => $days) {
			$output .= '<tr>';
			$output .= '<th class="master-program-hour">';
			
			if($timeformat == 12) {
				if($hour == 0) {
					$output .= '12am';
				}
				elseif($hour < 12) {
					$output .= $hour.'am';
				}
				elseif($hour == 12) {
					$output .= '12pm';
				}
				else {
					$output .= ($hour-12).'pm';
				}
			}
			else {
				if($hour < 10) {
					$output .= "0";
				}
				$output .= $hour.":00";
			}
			
			$output .= '</th>';
			
			$curskip = $nextskip;
			$nextskip = array();
			
			foreach($days as $day => $min) {
				
				//overly complex way of determining if we need to accomodate a rowspan due to a show spanning multiple hours
				$continue = 0;
				foreach($curskip as $x => $skip) {
					
					if($skip['day'] == $day) {
						if($skip['span'] > 1 ) {
							$continue = 1;
							$skip['span'] = $skip['span']-1;
							$curskip[$x]['span'] = $skip['span'];
							$nextskip = $curskip;
						}
					}	
				}
				
				$rowspan = 0;
				foreach($min as $shift) {
					
					if($shift['time']['end_hour'] == 0) { //fix shows that end at midnight, otherwise you could end up with a negative row span
						$rowspan = $rowspan + (24 - $shift['time']['start_hour']);
					}
					elseif($shift['time']['start_hour'] > $shift['time']['end_hour']) { // show runs from before midnight night until the next morning
						$rowspan = $rowspan + (24 - $shift['time']['start_hour']);
					}
					else {
						$rowspan = $rowspan + ($shift['time']['end_hour'] - $shift['time']['start_hour']);
					}
				}
				
				$span = '';
				if($rowspan > 1) {
					$span = ' rowspan="'.$rowspan.'"';
					//add to both arrays
					$curskip[] = array('day' => $day, 'span' => $rowspan, 'show' => get_the_title($shift['id']));
					$nextskip[] = array('day' => $day, 'span' => $rowspan, 'show' => get_the_title($shift['id']));
				}
				
				//if we need to accomodate a rowspan, skip this iteration so we don't end up with an extra table cell.
				if($continue) {
					continue;
				}
				
				$output .= '<td'.$span.'>';
				
				foreach($min as $shift) {
					//print_r($shift);
					
					$terms = wp_get_post_terms( $shift['id'], 'genres', array() );
					$classes = ' show-id-'.$shift['id'].' '.sanitize_title_with_dashes(str_replace("_", "-", get_the_title($shift['id']))).' ';
					foreach($terms as $term) {
						$classes .= $term->name.' ';
					}
					
					$output .= '<div class="master-show-entry'.$classes.'">';
					$output .= '<span class="show-title">';
					if($show_link) {
						$output .= '<a href="'.get_permalink($shift['id']).'">'.get_the_title($shift['id']).'</a>';
					}
					else {
						$output .= get_the_title($shift['id']);
					}
					$output .= '</span>';
					
					if($display_show_time) {
						//convert back to 12-hour time if 12-hour has been selected
						if($timeformat == 12) {
							if($shift['time']['start_meridian'] == 'pm' && $shift['time']['start_hour'] != 12) {
								$shift['time']['start_hour'] = $shift['time']['start_hour'] - 12;
							}
							if($shift['time']['start_meridian'] == 'am' && $shift['time']['start_hour'] == 0) {
								$shift['time']['start_hour'] = 12;
							}
							
							if($shift['time']['end_meridian'] == 'pm' && $shift['time']['end_hour'] != 12) {
								$shift['time']['end_hour'] = $shift['time']['end_hour'] - 12;
							}
							if($shift['time']['end_meridian'] == 'am' && $shift['time']['end_hour'] == 0) {
								$shift['time']['end_hour'] = 12;
							}
						}
						
						$output .= '<span class="show-time">';
						if(isset($shift['time']['real_start'])) {
							$output .= __($day, 'radio-station').'<br />'.$shift['time']['real_start'].' - '.$shift['time']['end_hour'].':'.$shift['time']['end_min'];
							if($timeformat == 12) {
								$output .= $shift['time']['end_meridian'];
							}
						}
						else {
							$output .= __($day, 'radio-station').'<br />'.$shift['time']['start_hour'].':'.$shift['time']['start_min'];
							if($timeformat == 12) {
								$output .= $shift['time']['start_meridian'];;
							}
							
							$output .= ' - '.$shift['time']['end_hour'].':'.$shift['time']['end_min'];
							
							if($timeformat == 12) {
								$output .= $shift['time']['end_meridian'];
							}
						}
						$output .= '</span>';
					}
					
					if(isset($shift['time']['encore']) && $shift['time']['encore'] == 'on') {
						$output .= '<span class="show-encore">'.__('encore airing', 'radio-station').'</span>';
					}
					
					$link = get_post_meta($shift['id'], 'show_file', true);
					if($link != '') {
						$output .= '<span class="show-file"><a href="'.$link.'">'.__('Audio File', 'radio-station').'</a>';
					}
					
					$output .= '</div>';
				}
				$output .= '</td>';
			}
			
			$output .= '</tr>';
		}
		$output .= '</table>';
	}
	
	return $output;
	
}
add_shortcode( 'master-schedule', 'master_schedule');

function master_fetch_js_filter(){
	$js = '<div id="master-genre-list"><span class="heading">'.__('Genres', 'radio-station').': </span>';
	
	$taxes = get_terms('genres', array('hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC'));
	foreach($taxes as $i => $tax) {
		$js .= '<a href="javascript:show_highlight(\''.$tax->name.'\')">'.$tax->name.'</a>';
		if($i < count($taxes)) {
			$js .= ' | ';
		}
	}
	
	$js .= '</div>';
	
	$js .= '<script type="text/javascript">';
	$js .= 'function show_highlight(myclass) {';
	$js .= '	jQuery(".master-show-entry").css("border","1px solid white");';
	$js .= '	jQuery("." + myclass).css("border","3px solid red");';
	$js .= '}';
	$js .= '</script>';
	
	return $js;
}

?>