<?php
/*
 * Master Show schedule
 * Author: Nikki Blight
 * @Since: 2.0.13
 */

//jQuery is needed by the output of this code, so let's make sure we have it available
function master_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}
add_action( 'init', 'master_scripts' );

//shortcode to display a full schedule of DJs and shows
function master_schedule($atts) {
	global $wpdb;
	
	extract( shortcode_atts( array(
			'time' => '12',
			'show_link' => 1,
			'display_show_time' => 1,
			'list' => 0,
			'show_image' => 0
	), $atts ) );
	
	$timeformat = $time;
	
	//$overrides = master_get_overrides(true);

	//set up the structure of the master schedule
	$default_dj = get_option('dj_default_name');
	
	//check to see what day of the week we need to start on
	$start_of_week = get_option('start_of_week');
	$days_of_the_week = array('Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array());
	$week_start = array_slice($days_of_the_week, $start_of_week);

	foreach($days_of_the_week as $i => $weekday) {
		if($start_of_week > 0) {
			$add = $days_of_the_week[$i];
			unset($days_of_the_week[$i]);
			
			$days_of_the_week[$i] = $add;
		}
		$start_of_week--;
	}
	
	//create the master_list array based on the start of the week
	$master_list = array();
	for($i=0; $i<24; $i++) {
		$master_list[$i] = $days_of_the_week; 
	}
	
	//get the show schedules, excluding shows marked as inactive
	$show_shifts = $wpdb->get_results("SELECT `meta`.`post_id`, `meta`.`meta_value` FROM ".$wpdb->prefix."postmeta AS `meta`
										JOIN ".$wpdb->prefix."postmeta AS `active` ON `meta`.`post_id` = `active`.`post_id`
										JOIN ".$wpdb->prefix."posts as `posts` ON `posts`.`ID` = `meta`.`post_id` 
													WHERE `meta`.`meta_key` = 'show_sched'
													AND `posts`.`post_status` = 'publish' 
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
			
			//check if we're spanning multiple days
			$time['multi-day'] = 0;
			if( ($time['start_hour'] > $time['end_hour']) || ($time['start_hour'] == $time['end_hour']) ) {
				$time['multi-day'] = 1;
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
				if( 	($time['time']['start_meridian'] ==  'pm' && $time['time']['end_meridian'] == 'am') || //if it starts at night and ends in the morning, end hour is on the following day
						($time['time']['start_hour'].$time['time']['start_min'].$time['time']['start_meridian'] == $time['time']['end_hour'].$time['time']['end_min'].$time['time']['end_meridian']) || //if the start and end times are identical, assume the end time is the following day
						($time['time']['start_meridian'] ==  'am' && $time['time']['start_hour'] > $time['time']['end_hour']) //if the start hour is in the morning, and greater than the end hour, assume end hour is the following day
					) {
					
					if($timeformat == 12) {
						$time['time']['real_start'] = ($time['time']['start_hour']-12).':'.$time['time']['start_min'];
					}
					else {
						$pad_hour = "";
						if($time['time']['start_hour'] < 10) {
							$pad_hour = "0";
						}
						$time['time']['real_start'] = $pad_hour.$time['time']['start_hour'].':'.$time['time']['start_min'];
					}
					//$time['time']['start_hour'] = "0";
					//$time['time']['start_min'] = "00";
					//$time['time']['start_meridian'] = "am";
					
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
		$flip = $days_of_the_week;
		foreach($master_list as $hour => $days) {
			foreach($days as $day => $mins) {
				foreach($mins as $fmin => $fshow) {
					$flip[$day][$hour][$fmin] = $fshow;
				}
			}
		}
		
		$output .= '<ul class="master-list">';
		
		foreach($flip as $day => $hours) {
			$output .= '<li class="master-list-day" id="list-header-'.strtolower($day).'">';
			$output .= '<span class="master-list-day-name">'.$day.'</span>';
			$output .= '<ul class="master-list-day-'.strtolower($day).'-list">';
			foreach($hours as $hour => $mins) {
				
				foreach($mins as $min => $show) {
					$output .= '<li>';
					
					if($show_image) {
						$output .= '<span class="show-image">';
						if(has_post_thumbnail($show['id'])) {
							$output .= get_the_post_thumbnail($show['id'], 'thumbnail');
						}
						$output .= '</span>';
					}
					
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
						else {
							//we need to add a leading 0 to times before 10 in 24-hour format
							if($show['time']['start_hour'] < 10) {
								$show['time']['start_hour'] = '0'.$show['time']['start_hour'];
							}
							if($show['time']['end_hour'] < 10) {
								$show['time']['end_hour'] = '0'.$show['time']['end_hour'];
							}
						}
					
						$output .= ' <span class="show-time">';
							//if we're spanning days, this is going to look confusing, so add the day to the time for clarification
							if($show['time']['day'] != $day) {
								$output .= '<em>('.$show['time']['day'].')</em> ';
							}
						
							$output .= $show['time']['start_hour'].':'.$show['time']['start_min'];
							if($timeformat == 12) {
								$output .= $show['time']['start_meridian'];;
							}
								
							$output .= ' - ';
							
							if($show['time']['day'] != $day) {
								$output .= '<em>('.$day.')</em> ';
							}
							
							$output .= $show['time']['end_hour'].':'.$show['time']['end_min'];
								
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
		
		//output the headings in the correct order
		$output .= '<tr class="master-program-day-row"> <th></th>';
		foreach($days_of_the_week as $heading => $info) {
			$output .= '<th>'.__(substr($heading, 0, 3), 'radio-station').'</th>';
		}
		$output .= '</tr>';
		//$output .= '<tr class="master-program-day-row"> <th></th> <th>'.__('Sun', 'radio-station').'</th> <th>'.__('Mon', 'radio-station').'</th> <th>'.__('Tue', 'radio-station').'</th> <th>'.__('Wed', 'radio-station').'</th> <th>'.__('Thu', 'radio-station').'</th> <th>'.__('Fri', 'radio-station').'</th> <th>'.__('Sat', 'radio-station').'</th> </tr>';
		
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
					
					if($shift['time']['start_hour'] == 0 && $shift['time']['end_hour'] == 0) { ///midnight to midnight shows
						$rowspan = 24;
					}
					
					if($shift['time']['end_hour'] == 0 && $shift['time']['start_hour'] != 0) { //fix shows that end at midnight (BUT take into account shows that start at midnight and end before the hour is up e.g. 12:00 - 12:30am), otherwise you could end up with a negative row span
						$rowspan = $rowspan + (24 - $shift['time']['start_hour']);
					}
					elseif($shift['time']['start_hour'] > $shift['time']['end_hour']) { // show runs from before midnight night until the next morning
						//print_r($shift);die('moo');
						if(isset($shift['time']['real_start'])) { //if we're on the second day of a show that spans two days
							
							$rowspan = $shift['time']['end_hour'];
						}
						else {  //if we're on the first day of a show that spans two days
							$rowspan = $rowspan + (24 - $shift['time']['start_hour']);
						}
					}
					else {  //all other shows
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
					
					if($show_image) {
						$output .= '<span class="show-image">';
						if(has_post_thumbnail($shift['id'])) {
							$output .= get_the_post_thumbnail($shift['id'], 'thumbnail');
						}
						$output .= '</span>';
					}
					
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
							
							if(isset($shift['time']['real_start'])) {
								//die(print_r($shift['time']));
								$realstart = explode(':', $shift['time']['real_start']);
								if( $realstart[0] > 12) {
									$shift['time']['real_start'] = ($realstart[0] - 12).':'.$realstart[1];
									
								}
								if($realstart[0] == 0) {
									$shift['time']['real_start'] = '12:'.$realstart[1];
								}
							}
						}
						else {
							//we need to add a leading 0 to times before 10 in 24-hour format
							if($shift['time']['start_hour'] < 10) {
								$shift['time']['start_hour'] = '0'.$shift['time']['start_hour'];
							}
							if($shift['time']['end_hour'] < 10) {
								$shift['time']['end_hour'] = '0'.$shift['time']['end_hour'];
							}
						}
						
						$output .= '<span class="show-time">';
						if(isset($shift['time']['real_start'])) {
							
							$output .= __($day, 'radio-station').'<br />'.$shift['time']['real_start'];
							if($timeformat == 12) {
								$output .= $shift['time']['start_meridian'];
							}
							
							$output .= ' - '.$shift['time']['end_hour'].':'.$shift['time']['end_min'];
							if($timeformat == 12) {
								$output .= $shift['time']['end_meridian'];
							}
						}
						else {
							$output .= __($day, 'radio-station').'<br />'.$shift['time']['start_hour'].':'.$shift['time']['start_min'];
							if($timeformat == 12) {
								$output .= $shift['time']['start_meridian'];
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