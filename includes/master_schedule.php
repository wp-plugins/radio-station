<?php
/*
 * Master Show schedule
 * Author: Nikki Blight
 */

//jQuery is needed by the output of this code, so let's make sure we have it available
function master_scripts() {
	wp_enqueue_script( 'jquery' );
}
add_action( 'init', 'master_scripts' );

//shortcode to display a full schedule of DJs and shows
function master_schedule($atts) {
	global $wpdb;
	
	extract( shortcode_atts( array(
			'time' => '12',
			'show_link' => 1,
			'display_show_time' => 1
	), $atts ) );
	
	$timeformat = $time;

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
	
	//print_r($master_list);
	
	//create the output in a table
	$output = '';
	$output .= master_fetch_js_filter();
	
	$output .= '<table id="master-program-schedule">';
	$output .= '<tr> <th></th> <th>Sun</th> <th>Mon</th> <th>Tue</th> <th>Wed</th> <th>Thu</th> <th>Fri</th> <th>Sat</th> </tr>';
	
	if(!isset($nextskip)) {
		$nextskip = array();
	}
	
	foreach($master_list as $hour => $days) {
		$output .= '<tr>';
		$output .= '<th>';
		
		if($time == 12) {
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
				$classes = ' ';
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
						$output .= $day.'<br />'.$shift['time']['real_start'].' - '.$shift['time']['end_hour'].':'.$shift['time']['end_min'];
						if($timeformat == 12) {
							$output .= $shift['time']['end_meridian'];
						}
					}
					else {
						$output .= $day.'<br />'.$shift['time']['start_hour'].':'.$shift['time']['start_min'];
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
					$output .= '<span class="show-encore">encore airing</span>';
				}
				
				$link = get_post_meta($shift['id'], 'show_file', true);
				if($link != '') {
					$output .= '<span class="show-file"><a href="'.$link.'">Audio File</a>';
				}
				
				$output .= '</div>';
			}
			$output .= '</td>';
		}
		
		$output .= '</tr>';
	}
	$output .= '</table>';
	
	return $output;
	
}
add_shortcode( 'master-schedule', 'master_schedule');

function master_fetch_js_filter(){
	$js = '<div id="master-genre-list"><span class="heading">Genres: </span>';
	
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