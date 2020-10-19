<?php
/**
 * Template for master schedule shortcode list style.
 */

// --- get all the required info ---
$hours = radio_station_get_hours();
$now = radio_station_get_now();
$date = radio_station_get_time( 'date', $now );
// $am = str_replace( ' ', '', radio_station_translate_meridiem( 'am' ) );
// $pm = str_replace( ' ', '', radio_station_translate_meridiem( 'pm' ) );

// --- set shift time formats ---
// 2.3.2: set time formats early
if ( 24 == (int) $atts['time'] ) {
	$start_data_format = $end_data_format = 'H:i';
} else {
	$start_data_format = $end_data_format = 'g:i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-list', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-list', $atts );

// --- get schedule days and dates ---
// 2.3.2: allow for start day attibute
// 2.3.3.5: use the start_day value for getting the current schedule
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$start_day = $atts['start_day'];
	$schedule = radio_station_get_current_schedule( $now , $start_day );
} else {
	// 2.3.3.5: add filter for changing start day (to accept 'today')
	$start_day = apply_filters( 'radio_station_schedule_start_day', false, 'list' );
	if ( $start_day ) {
		$schedule = radio_station_get_current_schedule( $now , $start_day );
	} else {
		$schedule = radio_station_get_current_schedule();
	}
}
$weekdays = radio_station_get_schedule_weekdays( $start_day );
$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

// --- filter show avatar size ---
// 2.3.3.5: fix to incorrect context value (tabs)
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'list' );

// --- filter excerpt length and more ---
if ( $atts['show_desc'] ) {
	$length = apply_filters( 'radio_station_schedule_list_excerpt_length', false );
	$more = apply_filters( 'radio_station_schedule_list_excerpt_more', '[&hellip;]' );
}

// --- start list schedule output ---
$output .= '<ul id="master-list" class="master-list">';

$tcount = 0;
// 2.3.0: loop weekdays instead of legacy master list
foreach ( $weekdays as $weekday ) {

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

		// 2.3.2: move up time calculations for optional date display
		$day_start_time = radio_station_to_time( $weekdates[$weekday] . ' 00:00' );
		$day_end_time = $day_start_time + ( 24 * 60 * 60 );

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

		// 2.3.2: add classes
		$classes = array( 'master-list-day' );
		$weekdate = $weekdates[$weekday];
		if ( $weekdate == $date ) {
			$classes[] = 'current-day';
		}
		$classlist = implode( ' ', $classes );

		// 2.2.2: use translate function for weekday string
		// 2.3.2: added optional display-date attribute
		$display_day = radio_station_translate_weekday( $weekday );
		$output .= '<li id="list-header-' . strtolower( $weekday ) . '" class="' . esc_attr( $classlist ) . '" >';
		$output .= '<span class="master-list-day-name"';
		if ( !$atts['display_date'] ) {
			$output .= ' title="' . esc_attr( $date_subheading ) . '"';
		}
		$output .= '>' . esc_html( $display_day ) . '</span>';
		if ( $atts['display_date'] ) {
			$output .= ' <span class="master-list-day-date">' . esc_html( $date_subheading ) . '</span>';
		}

		// 2.3.2: add output of day start and end times
		// 2.3.2: replace strtotime with to_time for timezones
		$output .= '<span class="rs-time rs-start-time" data="' . esc_attr( $day_start_time ) . '"></span>';
		$output .= '<span class="rs-time rs-end-time" data="' . esc_attr( $day_end_time ) . '"></span>';

		// --- open day list ---
		$output .= '<ul class="master-list-day-' . esc_attr( strtolower( $weekday ) ) . '-list">';

		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
		} else {
			$shifts = array();
		}

		// 2.3.0: loop schedule day shifts instead of hours and minutes
		if ( count( $shifts ) > 0 ) {
			foreach ( $shifts as $shift ) {

				$show = $shift['show'];

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

				// --- get split shift real start and end times ---
				// 2.3.2: added for shift display output
				$real_shift_start = $real_shift_end = false;
				if ( isset( $shift['split'] ) && $shift['split'] ) {
					if ( isset( $shift['real_start'] ) ) {
						$real_shift_start = radio_station_convert_shift_time( $shift['real_start'] );
						$real_shift_start = radio_station_to_time( $weekdate . ' ' . $real_shift_start ) - ( 24 * 60 * 60 );
					} elseif ( isset( $shift['real_end'] ) ) {
						$real_shift_end = radio_station_convert_shift_time( $shift['real_end'] );
						$real_shift_end = radio_station_to_time( $weekdate . ' ' . $real_shift_end ) + ( 24 * 60 * 60 );
					}
				}

				// 2.3.0: filter show link by show and context
				$show_link = false;
				if ( $atts['show_link'] ) {
					$show_link = apply_filters( 'radio_station_schedule_show_link', $show['url'], $show['id'], 'list' );
				}

				// 2.3.0: add genre classes for highlighting
				$classes = array( 'master-list-day-item' );
				$terms = wp_get_post_terms( $show['id'], RADIO_STATION_GENRES_SLUG, array() );
				if ( $terms && ( count( $terms ) > 0 ) ) {
					foreach ( $terms as $term ) {
						$classes[] = strtolower( $term->slug );
					}
				}
				// 2.3.2: check for now playing shift
				if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$classes[] = 'nowplaying';
				}

				// --- open show list item ---
				$classlist = implode( ' ', $classes );
				$output .= '<li class="' . esc_attr( $classlist ) . '">';

				// --- show avatar ---
				if ( $atts['show_image'] ) {
					// 2.3.0: filter show avatar via show ID and context
					$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size );
					$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'list' );
					if ( $show_avatar ) {
						$output .= '<div class="show-image">';
						if ( $show_link ) {
							$output .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>';
						} else {
							$output .= $show_avatar;
						}
						$output .= '</div>';
					}
				}

				// --- show title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
				} else {
					$show_title = esc_html( $show['name'] );
				}
				$output .= '<span class="show-title">';
				$output .= $show_title;
				$output .= '</span>';

				// --- show hosts ---
				// 2.3.0: changed from show_djs
				if ( $atts['show_hosts'] ) {

					$hosts = '';
					if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

						$count = 0;
						$host_count = count( $show['hosts'] );
						$hosts .= '<span class="show-dj-names-leader">';
						$hosts .= esc_html( __( 'with', 'radio-station' ) );
						// 2.3.2: fix variable to close span tag
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
								 || ( ( $host_count > 2 ) && ( ( $count === $host_count - 1 ) ) ) ) {
								$hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$hosts .= ', ';
							}
						}
					}

					// 2.3.3.5: fix to incorrect context value (tabs)
					$hosts = apply_filters( 'radio_station_schedule_show_hosts', $hosts, $show['id'], 'list' );
					if ( $hosts ) {
						$output .= '<div class="show-dj-names show-host-names">';
						$output .= $hosts;
						$output .= '</div>';
					}
				}

				// --- show time ---
				if ( $atts['show_times'] ) {

					// --- convert shift time for display ---
					// 2.3.0: updated to use new schedule data
					/* if ( '00:00 am' == $shift['start'] ) {
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
					} else {
						$start = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm), $shift['start'] );
						// 2.3.2: display real end of split shift
						if ( isset( $shift['split'] ) && $shift['split'] && isset( $shift['real_end'] ) ) {
							$end = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm ), $shift['real_end'] );
						} else {
							$end = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm ), $shift['end'] );
						}
					} */

					// --- get start and end times ---
					// 2.3.2: maybe use real start and end times
					if ( $real_shift_start ) {
						$start = radio_station_get_time( $start_data_format, $real_shift_start );
					} else {
						$start = radio_station_get_time( $start_data_format, $shift_start_time );
					}
					if ( $real_shift_end ) {
						$end = radio_station_get_time( $end_data_format, $real_shift_end );
					} else {
						$end = radio_station_get_time( $end_data_format, $shift_end_time );
					}
					$start = radio_station_translate_time( $start );
					$end = radio_station_translate_time( $end );

					// 2.3.0: filter show time by show and context
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>';
					$show_time .= '<span class="rs-sep"> ' . esc_html( __( 'to', 'radio-station' ) ) . ' </span>';
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>';
					$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show['id'], 'list' );

					$output .= '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '">' . $show_time . '</div>';
					$output .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '"></div>';
					$tcount ++;

				}

				// --- encore ---
				if ( $atts['show_encore'] ) {
					// 2.3.0: filter encore by show and context ---
					if ( isset( $shift['encore'] ) ) {
						$show_encore = $shift['encore'];
					} else {
						$show_encore = false;
					}
					$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show['id'], 'list' );
					if ( 'on' == $show_encore ) {
						$output .= '<div class="show-encore">';
						$output .= esc_html( __( 'encore airing', 'radio-station' ) );
						$output .= '</div>';
					}
				}

				// --- show file ---
				if ( $atts['show_file'] ) {
					// 2.3.0: filter show file by show and context
					// 2.3.2: check disable download meta
					// 2.3.3: fix to incorrect filter name
					$show_file = get_post_meta( $show['id'], 'show_file', true );
					$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show['id'], 'list' );
					$disable_download = get_post_meta( $show['id'], 'show_download', true );
					if ( $show_file && !empty( $show_file ) && !$disable_download ) {
						$output .= '<div class="show-file">';
						$output .= '<a href="' . esc_url( $show_file ) . '">';
						$output .= esc_html( __( 'Audio File', 'radio-station' ) );
						$output .= '</a>';
						$output .= '</div>';
					}
				}

				// --- show genres ---
				// (defaults to on)
				// 2.3.0: add genres to list view
				if ( $atts['show_genres'] ) {
					$output .= '<div class="show-genres">';
					$genres = array();
					if ( count( $terms ) > 0 ) {
						foreach ( $terms as $term ) {
							$genres[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
						}
						$genre_display = implode( ', ', $genres );
						$output .= esc_html( __( 'Genres', 'radio-station' ) ) . ': ' . $genre_display;
					}
					$output .= '</div>';
				}

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
					$excerpt = apply_filters( 'radio_station_schedule_show_excerpt', $excerpt, $show['id'], 'list' );

					// --- output excerpt ---
					$output .= '<div class="show-desc">';
					$output .= $excerpt;
					$output .= '</div>';

				}

				$output .= '</li>';
			}
		}
		$output .= '</ul>';

		// --- close master list day item ---
		$output .= '</li>';
	}
}

// --- close master list ---
$output .= '</ul>';
