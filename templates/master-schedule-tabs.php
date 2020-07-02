<?php
/**
 * Template for master schedule shortcode tabs style.
 * ref: http://nlb-creations.com/2014/06/06/radio-station-tutorial-creating-a-tabbed-programming-schedule/
 */

// --- get all the required info ---
$schedule = radio_station_get_current_schedule();
$hours = radio_station_get_hours();
$now = radio_station_get_now();
$date = radio_station_get_time( 'date', $now );
$today =  radio_station_get_time( 'day', $now );
$am = str_replace( ' ', '', radio_station_translate_meridiem( 'am' ) );
$pm = str_replace( ' ', '', radio_station_translate_meridiem( 'pm' ) );

// --- get schedule days and dates ---
// 2.3.2: allow for start day attibute
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$weekdays = radio_station_get_schedule_weekdays( $atts['start_day'] );
} else {
	$weekdays = radio_station_get_schedule_weekdays();
}
$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

// --- filter show avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'tabs' );

// --- filter excerpt length and more ---
if ( $atts['show_desc'] ) {
	$length = apply_filters( 'radio_station_schedule_tabs_excerpt_length', false );
	$more = apply_filters( 'radio_station_schedule_tabs_excerpt_more', '[&hellip;]' );
}

// --- start tabbed schedule output ---
$output .= '<ul id="master-schedule-tabs">';

$panels = '';
$tcount = 0;
// 2.3.0: loop weekdays instead of legacy master list
foreach ( $weekdays as $i => $weekday ) {

	// --- maybe skip all days but those specified ---
	// 2.3.2: improve days attribute checking logic
	$skip_day = false;
	if ( $atts['days'] ) {
		$days = explode( ',', $atts['days'] );
		$found_day = false;
		foreach ( $days as $day ) {
			$day = trim( $day );
			// 2.3.2: allow for numeric days (0=sunday to 6=saturday)
			if ( is_numeric( $day ) && ( $day > -1 ) && ( $day < 7 ) ) {
				$day = radio_station_get_weekday( $day );
			}
			if ( trim( strtolower( $day ) ) == strtolower( $weekday ) ) {
				$found_day = true;
			}
		}
		if ( !$found_day ) {
			$skip_day = true;
		}
	}

	if ( !$skip_day ) {

		// 2.3.2: set day start and end times
		// 2.3.2: replace strtotime with to_time for timezones
		$day_start_time = radio_station_to_time( $weekdates[$weekday] . ' 00:00' );
		$day_end_time = $day_start_time + ( 24 * 60 * 60 );

		// 2.2.2: use translate function for weekday string
		// 2.3.2: added check for short/long day display attribute
		if ( !in_array( $atts['display_day'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_day'] = 'long';
		}
		if ( 'short' == $atts['display_day'] ) {
			$display_day = radio_station_translate_weekday( $weekday, true );
		} elseif ( ( 'full' == $atts['display_day'] ) || ( 'long' == $atts['display_day'] ) ) {
			$display_day = radio_station_translate_weekday( $weekday, false );
		}

		// 2.3.2: add attribute for date subheading format (see PHP date() format)
		// $subheading = date( 'jS M', strtotime( $weekdate ) );
		if ( $atts['display_date'] ) {
			$date_subheading = radio_station_get_time( $atts['display_date'], $day_start_time );
		} else {
			$date_subheading = radio_station_get_time( 'j', $day_start_time );
		}

		// 2.3.2: add attribute for short or long month display
		$month = radio_station_get_time( 'F', $day_start_time );
		if ( $atts['display_month'] && !in_array( $atts['display_month'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_month'] = 'short';
		}
		if ( ( 'long' == $atts['display_month'] ) || ( 'full' == $atts['display_month'] ) ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, false );
		} elseif ( 'short' == $atts['display_month'] ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, true );
		}

		// --- set tab classes ---	
		$weekdate = $weekdates[$weekday];
		$classes = array( 'master-schedule-tabs-day', 'day-' . $i );
		if ( $weekdate == $date ) {
			$classes[] = 'current-day';
			$classes[] = 'selected-day';
			$classes[] = 'active-day-tab';
		}
		$classlist  = implode( ' ', $classes );

		// 2.3.0: added left/right arrow responsive controls
		// 2.3.1: added (negative) return to arrow onclick functions
		$arrows = array( 'right' => '&#9658;', 'left' => '&#9668;' );
		$arrows = apply_filters( 'radio_station_schedule_arrows', $arrows, 'tabs' );
		$output .= '<li id="master-schedule-tabs-header-' . strtolower( $weekday ) . '" class="' . esc_attr( $classlist ) . '">';
		$output .= '<div class="shift-left-arrow">';
		$output .= '<a href="javacript:void(0);" onclick="return radio_shift_tab(\'left\');" title="' . esc_attr( __( 'Previous Day', 'radio-station' ) ) . '">' . $arrows['left'] . '</a>';
		$output .= '</div>';
		
		// 2.3.2: added optional display_date attribute and subheading
		$output .= '<div class="master-schedule-tabs-headings">';
		$output .= '<div class="master-schedule-tabs-day-name"';
		if ( !$atts['display_date'] ) {
			$output .= ' title="' . esc_attr( $date_subheading ) . '"';
		}
		$output .= '>' . esc_html( $display_day ) . '</div>';
		if ( $atts['display_date'] ) {
			$output .= '<div class="master-schedule-tabs-date">' . esc_html( $date_subheading ) . '</div>';
		}
		$output .= '</div>';
		
		$output .= '<div class="shift-right-arrow">';
		$output .= '<a href="javacript:void(0);" onclick="return radio_shift_tab(\'right\');" title="' . esc_attr( __( 'Next Day', 'radio-station' ) ) . '">' . $arrows['right'] . '</a>';
		$output .= '</div>';
		$output .= '<div id="master-schedule-tab-bottom-' . strtolower( $weekday ) . '" class="master-schedule-tab-bottom"></div>';
		// 2.3.2: add start and end day times for automatic highlighting
		$output .= '<span class="rs-time rs-start-time" data="' . esc_attr( $day_start_time ) . '"></span>';
		$output .= '<span class="rs-time rs-end-time" data="' . esc_attr( $day_end_time ) . '"></span>';
		$output .= '</li>';

		// 2.2.7: separate headings from panels for tab view
		$classes = array( 'master-schedule-tabs-panel' );
		if ( $weekdate == $date ) {
			$classes[] = 'selected-day';
			$classes[] = 'active-day-panel';
		}
		$classlist = implode( ' ', $classes );
		$panels .= '<ul id="master-schedule-tabs-day-' . esc_attr( strtolower( $weekday ) ) . '" class="' . esc_attr( $classlist ) . '">';

		// 2.3.2: added extra current day display
		$display_day = radio_station_translate_weekday( $weekday, false );
		$panels .= '<div class="master-schedule-tabs-selected" id="master-schedule-tabs-selected-' . esc_attr( strtolower( $weekday ) ) . '">';
		$panels .= __( 'Viewing', 'radio-station' ) . ': ' . esc_html( $display_day ) . '</div>';

		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
		} else {
			$shifts = array();
		}

		$foundshows = false;

		// 2.3.0: loop schedule day shifts instead of hours and minutes
		if ( count( $shifts ) > 0 ) {

			$foundshows = true;

			$j = 0;
			foreach ( $shifts as $shift ) {

				$j++;
				$show = $shift['show'];

				$show_link = false;
				if ( $atts['show_link'] ) {
					$show_link = $show['url'];
				}
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show['id'], 'tabs' );

				// --- convert shift time data ---
				// 2.3.2: replace strtotime with to_time for timezones
				// 2.3.2: fix to convert to 24 hour format first
				// 2.3.2: fix timestamps for midnight/split shifts
				// $shift_start = radio_station_convert_shift_time( $shift['start'] );
				// $shift_end = radio_station_convert_shift_time( $shift['end'] );
				// $shift_start_time = radio_station_to_time( $shift['day'] . ' ' . $shift_start );
				// $shift_end_time = radio_station_to_time( $shift['day'] . ' ' . $shift_end );
				// if ( $shift_end_time < $shift_start_time ) {
				// 	$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
				// }
				if ( '00:00 am' == $shift['start'] ) {
					$shift_start_time = radio_station_to_time( $weekdate . ' 00:00' );
				} else {
					$shift_start = radio_station_convert_shift_time( $shift['start'] );
					$shift_start_time = radio_station_to_time( $weekdate . ' ' . $shift_start );
				}
				if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
					$shift_end_time = radio_station_to_time( $weekdate . ' 23:59:59' ) + 1;
				} else {
					$shift_end = radio_station_convert_shift_time( $shift['end'] );
					$shift_end_time = radio_station_to_time( $weekdate . ' ' . $shift_end );
				}

				// --- shift debug ---
				// 2.3.2: added shift debugging
				if ( isset( $_GET['rs-shift-debug'] ) && ( '1' == $_GET['rs-shift-debug'] ) ) {
					if ( !isset( $shiftdebug ) ) {$shiftdebug = '';}
					$shiftdebug .= 'Now: ' . $now . ' (' . radio_station_get_time( 'datetime', $now ) . ') -- Today: ' . $today . '<br>';
					$shiftdebug .= 'Shift Start: ' . $shift_start . ' (' . date( 'Y-m-d l H: i', $shift_start ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_start ) . ')' . '<br>';
					$shiftdebug .= 'Shift End: ' . $shift_end . ' (' . date( 'Y-m-d l H: i', $shift_end ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_end ) . ')' . '<br>';
				}

				// 2.3.0: add genre classes for highlighting
				$classes = array( 'master-schedule-tabs-show' );
				$terms = wp_get_post_terms( $show['id'], RADIO_STATION_GENRES_SLUG, array() );
				if ( $terms && ( count( $terms ) > 0 ) ) {
					foreach ( $terms as $term ) {
						$classes[] = strtolower( $term->slug );
					}
				}
				// 2.3.2: add first and last classes
				if ( 1 == $j ) {
					$classes[] = 'first-show';
				}
				if ( $j == count( $shifts ) ) {
					$classes[] = 'last-show';
				}				
				
				// 2.3.2: check for now playing shift
				if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$classes[] = 'nowplaying';
				}

				// --- open list item ---
				$classlist = implode( ' ' , $classes );
				$panels .= '<li class="' . esc_attr( $classlist ) . '">';

				// --- Show Image ---
				// (defaults to display on)
				if ( $atts['show_image'] ) {
					// 2.3.0: filter show avatar by show and context
					// 2.3.0: maybe link avatar to show
					$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size );
					$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'tabs' );
					if ( $show_avatar ) {
						$panels .= '<div class="show-image">';
						if ( $show_link ) {
							$panels .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>';
						} else {
							$panels .= $show_avatar;
						}
						$panels .= '</div>';
					} else {
						$panels .= '<div class="show-image"></div>';
					}
				}

				// --- Show Information ---
				$panels .= '<div class="show-info">';

				// --- show title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
				} else {
					$show_title = esc_html( $show['name'] );
				}
				$panels .= '<div class="show-title">';
				$panels .= $show_title;
				$panels .= '</div>';

				// --- show hosts ---
				if ( $atts['show_hosts'] ) {

					$hosts = '';
					if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

						$count = 0;
						$host_count = count( $show['hosts'] );
						$hosts .= '<span class="show-dj-names-leader"> ';
						$hosts .= esc_html( __( 'with', 'radio-station' ) );
						$hosts .= ' </span>';

						foreach ( $show['hosts'] as $host ) {
							$count ++;
							// 2.3.0: added link_hosts attribute check
							if ( $atts['link_hosts'] && !empty( $host['url'] ) ) {
								$hosts .= '<a href="' . esc_url( $host['url'] ) . '">' . esc_html( $host['name'] ) . '</a>';
							} else {
								$hosts .= esc_html( $host['name'] );
							}

							if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
								 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
								$hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$hosts .= ', ';
							}
						}
					}

					$hosts = apply_filters( 'radio_station_schedule_show_hosts', $hosts, $show['id'], 'tabs' );
					if ( $hosts ) {
						$panels .= '<div class="show-dj-names show-host-names">';
						// phpcs:ignore WordPress.Security.OutputNotEscaped
						$panels .= $hosts;
						$panels .= '</div>';
					}
				}

				// --- show times ---
				if ( $atts['show_times'] ) {

					// --- convert shift time for display ---
					// 2.3.0: updated to use new schedule data
					if ( '00:00 am' == $shift['start'] ) {
						$shift['start'] = '12:00 am';
					}
					if ( '11:59:59 pm' == $shift['end'] ) {
						$shift['end'] = '12:00 am';
					}
					if ( 24 == (int) $atts['time'] ) {
						$start = radio_station_convert_shift_time( $shift['start'], 24 );
						// 2.3.2: display real end of split shift
						if ( isset( $shift['split'] ) && $shift['split'] && isset( $shift['real_end'] ) ) {
							$end = radio_station_convert_shift_time( $shift['real_end'], 24 );
						} else {
							$end = radio_station_convert_shift_time( $shift['end'], 24 );
						}
						$data_format = 'H:i';
					} else {
						$start = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm), $shift['start'] );
						// 2.3.2: display real end of split shift
						if ( isset( $shift['split'] ) && $shift['split'] && isset( $shift['real_end'] ) ) {
							$end = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm ), $shift['real_end'] );
						} else {
							$end = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm ), $shift['end'] );
						}
						$data_format = 'g:i a';
					}

					// 2.3.0: filter show time by show and context
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="H:i">' . $start . '</span>';
					$show_time .= '<span class="rs-sep"> ' . esc_html( __( 'to', 'radio-station' ) ) . ' </span>';
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="H:i">' . $end . '</span>';
					$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show['id'], 'tabs' );

					$panels .= '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '">' . $show_time . '</div>';
					$panels .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '"></div>';
					$tcount ++;

				} else {
				
					// 2.3.2: added for now playing check
					$panels .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="H:i"></span>';
					$panels .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="H:i"></span>';

				}

				// --- encore ---
				// 2.3.0: filter encore switch by show and context
				if ( $atts['show_encore'] ) {
					if ( isset( $shift['encore'] ) ) {
						$show_encore = $shift['encore'];
					} else {
						$show_encore = false;
					}
					$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show['id'], 'tabs' );
					if ( 'on' == $show_encore ) {
						$panels .= '<div class="show-encore">';
						$panels .= esc_html( __( 'encore airing', 'radio-station' ) );
						$panels .= '</div>';
					}
				}

				// --- show audio file ---
				if ( $atts['show_file'] ) {
					// 2.3.0: filter audio file by show and context
					$show_file = get_post_meta( $show['id'], 'show_file', true );
					$show_file = apply_filters( 'radio_station_schedule_show_link', $show_file, $show['id'], 'tabs' );
					// 2.3.2: check show download meta
					$disable_download = get_post_meta( $show['id'], 'show_download', true );				
					if ( $show_file && !empty( $show_file ) && !$disable_download ) {
						$panels .= '<div class="show-file">';
						$panels .= '<a href="' . esc_url( $show_file ) . '">';
						$panels .= esc_html( __( 'Audio File', 'radio-station' ) );
						$panels .= '</a>';
						$panels .= '</div>';
					}
				}

				// --- show genres ---
				// (defaults to display on)
				if ( $atts['show_genres'] ) {
					$panels .= '<div class="show-genres">';
					$genres = array();
					if ( count( $terms ) > 0 ) {
						foreach ( $terms as $term ) {
							$genres[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
						}
						$genre_display = implode( ', ', $genres );
						$panels .= esc_html( __( 'Genres', 'radio-station' ) ) . ': ' . $genre_display;
					}
					$panels .= '</div>';
				}

				$panels .= '</div>';

				// --- show description ---
				if ( $atts['show_desc'] ) {

					$show_post = get_post( $show['id'] );
					$permalink = get_permalink( $show_post->ID );

					// --- get show excerpt ---
					if ( !empty( $show_post->post_excerpt ) ) {
						$excerpt = $show_post->post_excerpt;
						$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
					} else {
						$excerpt = radio_station_trim_excerpt( $show_post->post_content, $length, $more, $permalink );
					}

					// --- filter excerpt by context ---
					$excerpt = apply_filters( 'radio_station_schedule_show_excerpt', $excerpt, $show['id'], 'tabs' );

					// --- output excerpt ---
					$panels .= '<div class="show-desc">';
					$panels .= $excerpt;
					$panels .= '</div>';

				}

				$panels .= '</li>';
			}
		}

		if ( !$foundshows ) {
			// 2.3.2: added no shows class
			$panels .= '<li class="master-schedule-tabs-show master-schedule-tabs-no-shows">';
			$panels .= esc_html( __( 'No Shows scheduled for this day.', 'radio-station' ) );
			$panels .= '</li>';
		}
	}

	$panels .= '</ul>';
}

$output .= '</ul>';

$output .= '<div id="master-schedule-tab-panels">';
$output .= $panels;
$output .= '</div>';

if ( isset( $_GET['rs-shift-debug'] ) && ( '1' == $_GET['rs-shift-debug'] ) ) {
	$output .= $shiftdebug;
}
