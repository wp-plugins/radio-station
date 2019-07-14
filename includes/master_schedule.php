<?php
/*
 * Master Show schedule
 * Author: Nikki Blight
 * @Since: 2.1.1
 */

// --- shortcode to display a full schedule of DJs and shows ---
function radio_station_master_schedule( $atts ) {
	global $wpdb;

	extract( shortcode_atts( array(
			'time' => '12',
			'show_link' => 1,
			'display_show_time' => 1,
			'list' => 'table',
			'show_image' => 0,
			'show_djs' => 0,
			'divheight' => 45
	), $atts ) );

	$timeformat = $time;

	// $overrides = radio_station_master_get_overrides(true);

	// set up the structure of the master schedule
	$default_dj = get_option( 'dj_default_name' );

	// check to see what day of the week we need to start on
	$start_of_week = get_option( 'start_of_week' );
	$days_of_the_week = array( 'Sunday' => array(), 'Monday' => array(), 'Tuesday' => array(), 'Wednesday' => array(), 'Thursday' => array(), 'Friday' => array(), 'Saturday' => array() );
	$week_start = array_slice( $days_of_the_week, $start_of_week );

	foreach ( $days_of_the_week as $i => $weekday ) {
		if ( $start_of_week > 0 ) {
			$add = $days_of_the_week[$i];
			unset( $days_of_the_week[$i] );
			$days_of_the_week[$i] = $add;
		}
		$start_of_week--;
	}

	// create the master_list array based on the start of the week
	$master_list = array();
	for ( $i=0; $i<24; $i++ ) {$master_list[$i] = $days_of_the_week;}


	// get the show schedules, excluding shows marked as inactive
	$show_shifts = $wpdb->get_results( "SELECT `meta`.`post_id`, `meta`.`meta_value` FROM ".$wpdb->prefix."postmeta AS `meta`
										JOIN ".$wpdb->prefix."postmeta AS `active` ON `meta`.`post_id` = `active`.`post_id`
										JOIN ".$wpdb->prefix."posts as `posts` ON `posts`.`ID` = `meta`.`post_id`
													WHERE `meta`.`meta_key` = 'show_sched'
													AND `posts`.`post_status` = 'publish'
													AND ( `active`.`meta_key` = 'show_active'
													AND `active`.`meta_value` = 'on');" );

	// insert schedules into the master list
	foreach ( $show_shifts as $shift ) {
		$shift->meta_value = unserialize( $shift->meta_value );

		// if a show is not scheduled yet, unserialize will return false... fix that.
		if ( !is_array($shift->meta_value ) ) {$shift->meta_value = array();}

		foreach ( $shift->meta_value as $time ) {

			// switch to 24-hour time
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

			// check if we're spanning multiple days
			$time['multi-day'] = 0;
			if( ($time['start_hour'] > $time['end_hour']) || ($time['start_hour'] == $time['end_hour']) ) {
				$time['multi-day'] = 1;
			}

			$master_list[$time['start_hour']][$time['day']][$time['start_min']] = array( 'id'=> $shift->post_id, 'time' => $time );
		}
	}

	// sort the array by time
	foreach ( $master_list as $hour => $days ) {
		foreach ( $days as $day => $min ) {
			ksort($min);
			$master_list[$hour][$day] = $min;

			// we need to take into account shows that start late at night and end the following day
			foreach($min as $i => $time) {

				// if it ends at midnight, we don't need to worry about carry-over
				if($time['time']['end_hour'] == "0" && $time['time']['end_min'] == "00") {
					continue;
				}

				// if it ends after midnight, fix it
				if( 	($time['time']['start_meridian'] ==  'pm' && $time['time']['end_meridian'] == 'am') || //if it starts at night and ends in the morning, end hour is on the following day
						($time['time']['start_hour'].$time['time']['start_min'].$time['time']['start_meridian'] == $time['time']['end_hour'].$time['time']['end_min'].$time['time']['end_meridian']) || //if the start and end times are identical, assume the end time is the following day
						($time['time']['start_meridian'] ==  'am' && $time['time']['start_hour'] > $time['time']['end_hour']) //if the start hour is in the morning, and greater than the end hour, assume end hour is the following day
					) {

					if($timeformat == 12) {
						$time['time']['real_start'] = ($time['time']['start_hour']-12).':'.$time['time']['start_min'];
					} else {
						$pad_hour = "";
						if ( $time['time']['start_hour'] < 10) {
							$pad_hour = "0";
						}
						$time['time']['real_start'] = $pad_hour.$time['time']['start_hour'].':'.$time['time']['start_min'];
					}
					// $time['time']['start_hour'] = "0";
					// $time['time']['start_min'] = "00";
					// $time['time']['start_meridian'] = "am";
					$time['time']['rollover'] = 1;

					if ( $day == 'Sunday' ) {$nextday = 'Monday';}
					if ( $day == 'Monday' ) {$nextday = 'Tuesday';}
					if ( $day == 'Tuesday' ) {$nextday = 'Wednesday';}
					if ( $day == 'Wednesday' ) {$nextday = 'Thursday';}
					if ( $day == 'Thursday' ) {$nextday = 'Friday';}
					if ( $day == 'Friday' ) {$nextday = 'Saturday';}
					if ( $day == 'Saturday' ) {$nextday = 'Sunday';}

					$master_list[0][$nextday]['00'] = $time;

				}
			}
		}
	}

	$output = '';

	if ( ($list == 1) || ($list == 'list') ) {

		// output as a list
		$flip = $days_of_the_week;
		foreach ( $master_list as $hour => $days ) {
			foreach ( $days as $day => $mins ) {
				foreach($mins as $fmin => $fshow) {
					$flip[$day][$hour][$fmin] = $fshow;
				}
			}
		}

		$output .= '<ul class="master-list">';

		foreach ( $flip as $day => $hours ) {
			$output .= '<li class="master-list-day" id="list-header-'.strtolower($day).'">';
			$output .= '<span class="master-list-day-name">'.__($day, 'radio-station').'</span>';
			$output .= '<ul class="master-list-day-'.strtolower($day).'-list">';
			foreach ($hours as $hour => $mins) {

				foreach ($mins as $min => $show ) {
					$output .= '<li>';

					if($show_image) {
						$output .= '<span class="show-image">';
						if ( has_post_thumbnail( $show['id'] ) ) {
							$output .= get_the_post_thumbnail($show['id'], 'thumbnail');
						}
						$output .= '</span>';
					}

					$output .= '<span class="show-title">';
					if ( $show_link ) {
						$output .= '<a href="'.get_permalink($show['id']).'">'.get_the_title($show['id']).'</a>';
					} else {
						$output .= get_the_title( $show['id'] );
					}
					$output .= '</span>';

					if ( $show_djs ) {
						$output .= '<span class="show-dj-names">';

						$names = get_post_meta( $show['id'], 'show_user_list', true );
						$count = 0;

						if ( $names ) {
							$output .= '<span class="show-dj-names-leader"> with </span>';
							foreach ( $names as $name ) {
								$count ++;
								$user_info = get_userdata( $name );

								$output .= $user_info->display_name;

								if ( ( ( $count == 1 ) && ( count($names) == 2 ) )
								  || ( (count($names) > 2 ) && ( $count == ( count($names) - 1 ) ) ) ) {
									$output .= ' and ';
								} elseif ( ( $count < count($names) ) && ( count($names) > 2 ) ) {
									$output .= ', ';
								}
							}
						}

						$output .= '</span> ';
					}

					if ( $display_show_time ) {

						$output .= '<span class="show-time">';

						if ( $timeformat == 12 ) {

							//$output .= $weekday.' ';
							$output .= date('g:i a', strtotime('1981-04-28 '.$show['time']['start_hour'].':'.$show['time']['start_min'].':00 '));
							$output .= ' - ';
							$output .= date('g:i a', strtotime('1981-04-28 '.$show['time']['end_hour'].':'.$show['time']['end_min'].':00 '));

						} else {

							$output .= date('H:i', strtotime('1981-04-28 '.$show['time']['start_hour'].':'.$show['time']['start_min'].':00 '));
							$output .= ' - ';
							$output .= date('H:i', strtotime('1981-04-28 '.$show['time']['end_hour'].':'.$show['time']['end_min'].':00 '));

						}

						$output .= '</span>';
					}

					if ( isset( $show['time']['encore'] ) && ( $show['time']['encore'] == 'on' ) ) {
						$output .= ' <span class="show-encore">'.__('encore airing', 'radio-station').'</span>';
					}

					$link = get_post_meta( $show['id'], 'show_file', true );
					if ( $link && ( $link != '' ) ) {
						$output .= ' <span class="show-file"><a href="'.$link.'">'.__('Audio File', 'radio-station').'</a>';
					}

					$output .= '</li>';
				}
			}
			$output .= '</ul>';
			$output .= '</li>';
		}

		$output .= '</ul>';

	} elseif ( $list == 'divs' ) {

		// output some dynamic styles
		$output .= '<style type="text/css">';
		for ( $i = 2; $i < 24; $i++ ) {
			$rowheight = $divheight * $i;

			$output .= '#master-schedule-divs .rowspan'.$i.' { ';
			$output .= 'height: '.($rowheight).'px; }';
		}

		$output .= '#master-schedule-divs .rowspan-half { height: 15px; margin-top: -7px; }';
		$output .= '#master-schedule-divs .rowspan { top: '.$divheight.'px; }';
		$output .= '</style>';

		// output the schedule
		$output .= radio_station_master_fetch_js_filter();
		$output .= '<div id="master-schedule-divs">';
		$weekdays = array_keys($days_of_the_week);

		$output .= '<div class="master-schedule-hour">';
		$output .= '<div class="master-schedule-hour-header">&nbsp;</div>';
		foreach( $weekdays as $weekday ) {
			$translated = radio_station_translate_weekday( $weekday );
			$output .= '<div class="master-schedule-weekday-header master-schedule-weekday">'.$translated.'</div>';
		}
		$output .= '</div>';

		foreach($master_list as $hour => $days) {

			$output .= '<div class="master-schedule-'.$hour.' master-schedule-hour">';

			// output the hour labels
			$output .= '<div class="master-schedule-hour-header">';
			if ( $timeformat == 12 ) {
				$output .= date('ga', strtotime('1981-04-28 '.$hour.':00:00')); //random date needed to convert time to 12-hour format
			} else {
				$output .= date('H:i', strtotime('1981-04-28 '.$hour.':00:00')); //random date needed to convert time to 24-hour format
			}
			$output .= '</div>';

			foreach ( $weekdays as $weekday ) {
				$output .= '<div class="master-schedule-'.strtolower($weekday).' master-schedule-weekday" style="height: '.$divheight.'px;">';
				if ( isset( $days[$weekday] ) ) {
					foreach ( $days[$weekday] as $min => $showdata ) {

						$terms = wp_get_post_terms( $showdata['id'], 'genres', array() );
						$classes = ' show-id-'.$showdata['id'].' '.sanitize_title_with_dashes(str_replace("_", "-", get_the_title($showdata['id']))).' ';
						foreach ( $terms as $term ) {
							$classes .= sanitize_title_with_dashes($term->name).' ';
						}

						$output .= '<div class="master-show-entry'.$classes.'">';

						// featured image
						if ( $show_image ) {
							$output .= '<span class="show-image">';
							if ( has_post_thumbnail( $showdata['id'] ) ) {
								$output .= get_the_post_thumbnail( $showdata['id'], 'thumbnail' );
							}
							$output .= '</span>';
						}

						// title + link to page if requested
						$output .= '<span class="show-title">';
						if ( $show_link ) {
							$output .= '<a href="'.get_permalink($showdata['id']).'">'.get_the_title($showdata['id']).'</a>';
						} else {
							$output .= get_the_title($showdata['id']);
						}
						$output .= '</span>';

						// list of DJs
						if ( $show_djs ) {

							$output .= '<span class="show-dj-names">';

							$names = get_post_meta( $showdata['id'], 'show_user_list', true );
							$count = 0;

							if ( $names ) {

								$output .= '<span class="show-dj-names-leader"> with </span>';

								foreach ( $names as $name ) {

									$count++;
									$user_info = get_userdata( $name );

									$output .= $user_info->display_name;

									if ( ( ($count == 1) && ( count($names) == 2) )
									  || ( (count($names) > 2 ) && ( $count == ( count($names) -1 ) ) ) ) {
										$output .= ' and ';
									} elseif ( ( $count < count($names) ) && ( count($names) > 2 ) ) {
										$output .= ', ';
									}
								}
							}

							$output .= '</span>';
						}

						// show's schedule
						if ( $display_show_time ) {

							$output .= '<span class="show-time">';

							if ( $timeformat == 12 ) {
								// $output .= $weekday.' ';
								$output .= date( 'g:i a', strtotime('1981-04-28 '.$showdata['time']['start_hour'].':'.$showdata['time']['start_min'].':00 ') );
								$output .= ' - ';
								$output .= date( 'g:i a', strtotime('1981-04-28 '.$showdata['time']['end_hour'].':'.$showdata['time']['end_min'].':00 ') );
							} else {
								$output .= date( 'H:i', strtotime('1981-04-28 '.$showdata['time']['start_hour'].':'.$showdata['time']['start_min'].':00 ') );
								$output .= ' - ';
								$output .= date( 'H:i', strtotime('1981-04-28 '.$showdata['time']['end_hour'].':'.$showdata['time']['end_min'].':00 ') );
							}
							$output .= '</span>';
						}

						// designate as encore
						if ( isset($showdata['time']['encore']) && ( $showdata['time']['encore'] == 'on' ) ) {
							$output .= '<span class="show-encore">'.__('encore airing', 'radio-station').'</span>';
						}

						// link to media file
						$link = get_post_meta( $showdata['id'], 'show_file', true );
						if ( $link && ( $link != '' ) ) {
							$output .= '<span class="show-file"><a href="'.$link.'">'.__('Audio File', 'radio-station').'</a>';
						}

						// calculate duration of show for rowspanning
						if ( isset( $showdata['time']['rollover'] ) ) { //show started on the previous day
							$duration = $showdata['time']['end_hour'];
						} else {
							if ( $showdata['time']['end_hour'] >=  $showdata['time']['start_hour'] ) {
								$duration = $showdata['time']['end_hour'] - $showdata['time']['start_hour'];
							} else {
								$duration =  23 - $showdata['time']['start_hour'];
							}
						}

						if ( $duration >= 1 ) {
							$output .= '<div class="rowspan rowspan'.$duration.'"></div>';

							if ( $showdata['time']['end_min'] != '00' ) {
								$output .= '<div class="rowspan rowspan-half"></div>';
							}
						}

						$output .= '</div>'; // end master-show-entry
					}
				}
				$output .= '</div>'; // end master-schedule-weekday
			}
			$output .= '</div>'; // end master-schedule-hour
		}
		$output .= '</div>'; // end master-schedule-divs

	} else {

		// create the output in a table
		$output .= radio_station_master_fetch_js_filter();

		$output .= '<table id="master-program-schedule">';

		// output the headings in the correct order
		$output .= '<tr class="master-program-day-row"> <th></th>';
		foreach($days_of_the_week as $weekday => $info) {
			$heading = substr( $heading, 0, 3 );
			$heading = radio_station_translate_weekday( $heading, true );
			$output .= '<th>'.$heading.'</th>';
		}
		$output .= '</tr>';
		// $output .= '<tr class="master-program-day-row"> <th></th> <th>'.__('Sun', 'radio-station').'</th> <th>'.__('Mon', 'radio-station').'</th> <th>'.__('Tue', 'radio-station').'</th> <th>'.__('Wed', 'radio-station').'</th> <th>'.__('Thu', 'radio-station').'</th> <th>'.__('Fri', 'radio-station').'</th> <th>'.__('Sat', 'radio-station').'</th> </tr>';

		if ( !isset( $nextskip ) ) {$nextskip = array();}

		foreach ( $master_list as $hour => $days ) {

			$output .= '<tr>';
			$output .= '<th class="master-program-hour">';

			if ( $timeformat == 12 ) {
				if ( $hour == 0 ) {$output .= '12am';}
				elseif ( $hour < 12 ) {$output .= $hour.'am';}
				elseif ( $hour == 12 ) {$output .= '12pm';}
				else {$output .= ($hour-12).'pm';}
			} else {
				if ( $hour < 10 ) {$output .= "0";}
				$output .= $hour.":00";
			}

			$output .= '</th>';

			$curskip = $nextskip;
			$nextskip = array();

			foreach ( $days as $day => $min ) {

				// overly complex way of determining if we need to accomodate a rowspan due to a show spanning multiple hours
				$continue = 0;
				foreach ( $curskip as $x => $skip ) {

					if ( $skip['day'] == $day ) {
						if ( $skip['span'] > 1 ) {
							$continue = 1;
							$skip['span'] = $skip['span']-1;
							$curskip[$x]['span'] = $skip['span'];
							$nextskip = $curskip;
						}
					}
				}

				$rowspan = 0;
				foreach ( $min as $shift ) {

					if ( $shift['time']['start_hour'] == 0 && $shift['time']['end_hour'] == 0 ) { ///midnight to midnight shows
						if ( $shift['time']['start_min'] == $shift['time']['end_min'] ) { //accomodate shows that end midway through the 12am hour
							$rowspan = 24;
						}
					}

					if ( $shift['time']['end_hour'] == 0 && $shift['time']['start_hour'] != 0 ) { //fix shows that end at midnight (BUT take into account shows that start at midnight and end before the hour is up e.g. 12:00 - 12:30am), otherwise you could end up with a negative row span
						$rowspan = $rowspan + (24 - $shift['time']['start_hour']);
					} elseif($shift['time']['start_hour'] > $shift['time']['end_hour'] ) { // show runs from before midnight night until the next morning
						// print_r($shift);die('moo');
						if ( isset($shift['time']['real_start'] ) ) {
							// if we're on the second day of a show that spans two days
							$rowspan = $shift['time']['end_hour'];
						} else {
							// if we're on the first day of a show that spans two days
							$rowspan = $rowspan + ( 24 - $shift['time']['start_hour'] );
						}
					} else {
						// all other shows
						$rowspan = $rowspan + ($shift['time']['end_hour'] - $shift['time']['start_hour']);
					}
				}

				$span = '';
				if ( $rowspan > 1 ) {
					$span = ' rowspan="'.$rowspan.'"';
					// add to both arrays
					$curskip[] = array( 'day' => $day, 'span' => $rowspan, 'show' => get_the_title( $shift['id'] ) );
					$nextskip[] = array( 'day' => $day, 'span' => $rowspan, 'show' => get_the_title( $shift['id'] ) );
				}

				// if we need to accomodate a rowspan, skip this iteration so we don't end up with an extra table cell.
				if ($continue) {continue;}

				$output .= '<td'.$span.'>';

				foreach ( $min as $shift ) {

					//print_r($shift);

					$terms = wp_get_post_terms( $shift['id'], 'genres', array() );
					$classes = ' show-id-'.$shift['id'].' '.sanitize_title_with_dashes(str_replace("_", "-", get_the_title($shift['id']))).' ';
					foreach ( $terms as $term ) {
						$classes .= sanitize_title_with_dashes( $term->name ).' ';
					}

					$output .= '<div class="master-show-entry'.$classes.'">';

					if ( $show_image ) {
						$output .= '<span class="show-image">';
						if ( has_post_thumbnail($shift['id'] ) ) {
							$output .= get_the_post_thumbnail($shift['id'], 'thumbnail');
						}
						$output .= '</span>';
					}

					$output .= '<span class="show-title">';
					if ( $show_link ) {
						$output .= '<a href="'.get_permalink($shift['id']).'">'.get_the_title($shift['id']).'</a>';
					} else {
						$output .= get_the_title($shift['id']);
					}
					$output .= '</span>';

					if ( $show_djs ) {

						$output .= '<span class="show-dj-names">';

						$names = get_post_meta( $shift['id'], 'show_user_list', true );
						$count = 0;

						if ( $names ) {

							$output .= '<span class="show-dj-names-leader"> '.__( 'with','radio-station' ).' </span>';
							foreach( $names as $name ) {
								$count ++;
								$user_info = get_userdata($name);

								$output .= $user_info->display_name;

								if ( ( ( $count == 1 ) && ( count($names) == 2 ) )
								  || ( ( count($names) > 2) && ( $count == ( count($names) -1 ) ) ) ) {
									$output .= ' and ';
								} elseif ( ( $count < count($names) ) && ( count($names) > 2 ) ) {
									$output .= ', ';
								}
							}
						}

						$output .= '</span>';
					}

					if ( $display_show_time ) {

						$output .= '<span class="show-time">';

						if ( $timeformat == 12 ) {
							//$output .= $weekday.' ';
							$output .= date( 'g:i a', strtotime('1981-04-28 '.$shift['time']['start_hour'].':'.$shift['time']['start_min'].':00 ') );
							$output .= ' - ';
							$output .= date( 'g:i a', strtotime('1981-04-28 '.$shift['time']['end_hour'].':'.$shift['time']['end_min'].' ') );
						} else {
							$output .= date( 'H:i', strtotime('1981-04-28 '.$shift['time']['start_hour'].':'.$shift['time']['start_min'].':00 ') );
							$output .= ' - ';
							$output .= date( 'H:i', strtotime('1981-04-28 '.$shift['time']['end_hour'].':'.$shift['time']['end_min'].':00 ') );
						}
						$output .= '</span>';
					}

					if ( isset($shift['time']['encore']) && $shift['time']['encore'] == 'on' ) {
						$output .= '<span class="show-encore">'.__('encore airing', 'radio-station').'</span>';
					}

					$link = get_post_meta($shift['id'], 'show_file', true);
					if ( $link && ( $link != '' ) ) {
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
add_shortcode( 'master-schedule', 'radio_station_master_schedule');

// --- add javascript for highlighting shows based on genre ---
function radio_station_master_fetch_js_filter(){

	$js = '<div id="master-genre-list"><span class="heading">'.__('Genres', 'radio-station').': </span>';

	$taxes = get_terms( 'genres', array('hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC') );
	foreach ( $taxes as $i => $tax ) {
		$js .= '<a href="javascript:show_highlight(\''.sanitize_title_with_dashes($tax->name).'\')">'.$tax->name.'</a>';
		if($i < count($taxes)) {
			$js .= ' | ';
		}
	}

	$js .= '</div>';

	$js .= '<script type="text/javascript">';
	$js .= 'function show_highlight(myclass) {';
	$js .= '	jQuery(".master-show-entry").css("border","none");';
	$js .= '	jQuery("." + myclass).css("border","3px solid red");';
	$js .= '}';
	$js .= '</script>';

	return $js;
}
