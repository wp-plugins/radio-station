<?php

/*
 * Master Show schedule
 * Author: Nikki Blight
 * @Since: 2.1.1
 */

// - Master Schedule Shortcode
// - Schedule Loader Script
// - AJAX Schedule Loader
// - Show Genre Selector
// - Table View Javascript
// - Tabbed View Javascript
// - List View Javascript


// -------------------------
// Master Schedule Shortcode
// -------------------------
add_shortcode( 'master-schedule', 'radio_station_master_schedule' );
function radio_station_master_schedule( $atts ) {

	// --- make attributes backward compatible ---
	// 2.3.0: convert old list attribute to view
	if ( !isset( $atts['view'] ) && isset( $atts['list'] ) ) {
		if ( 1 === (int) $atts['list'] ) {
			$atts['list'] = 'list';
		}
		$atts['view'] = $atts['list'];
		unset( $atts['list'] );
	}
	// 2.3.0: convert show_djs attribute to show_hosts
	if ( !isset( $atts['show_hosts'] ) && isset( $atts['show_djs'] ) ) {
		$atts['show_hosts'] = $atts['show_djs'];
		unset( $atts['show_djs'] );
	}
	// 2.3.0: convert display_show_time attribute to show_times
	if ( !isset( $atts['show_times'] ) && isset( $atts['display_show_time'] ) ) {
		$atts['show_times'] = $atts['display_show_time'];
		unset( $atts['display_show_time'] );
	}
	// 2.3.0: convert single_day attribute to days
	if ( !isset( $atts['days'] ) && isset( $atts['single_day'] ) ) {
		$atts['days'] = $atts['single_day'];
		unset( $atts['single_day'] );
	}

	// --- get default clock display setting ---
	$clock = radio_station_get_setting( 'schedule_clock' );

	// --- merge shortcode attributes with defaults ---
	// 2.3.0: added show_desc (default off)
	// 2.3.0: added show_hosts (alias of show_djs)
	// 2.3.0: added show_file attribute (default off)
	// 2.3.0: added show_encore attribute (default on)
	// 2.3.0: added display clock attribute (default on)
	// 2.3.0: added display selector attribute (default on)
	// 2.3.0: added link_hosts attribute (default off)
	// 2.3.0: set default time format according to plugin setting
	// 2.3.0: set default table display to new table formatting
	// 2.3.2: added start_day attribute (for use width days)
	// 2.3.2: added display_day, display_date and display_month attributes
	// 2.3.3.9: added start_date attribute for non-now schedules
	// 2.3.3.9: added active_date attribute for schedule switching
	$time_format = (int) radio_station_get_setting( 'clock_time_format' );
	$defaults = array(

		// --- control display options ---
		'selector'          => 1,
		'clock'             => $clock,
		'timezone'          => 1,

		// --- schedule display options ---
		'time'              => $time_format,
		'show_times'        => 1,
		'show_link'         => 1,
		'view'              => 'table',
		'days'              => false,
		'start_day'         => false,
		'start_date'        => false,
		'active_date'       => false,
		'display_day'       => 'short',
		'display_date'      => 'jS',
		'display_month'	    => 'short',

		// --- converted and deprecated ---
		// 'list'              => 0,
		// 'show_djs'          => 0,
		// 'display_show_time' => 1,

		// --- show display options ---
		'show_image'        => 0,
		'show_desc'         => 0,
		'show_hosts'        => 0,
		'link_hosts'        => 0,
		'show_genres'       => 0,
		'show_encore'       => 1,
		'show_file'         => 0,
	);
	// 2.3.0: change some defaults for tabbed and list view
	// 2.3.2: check for comma separated view list
	$view = '';
	$views = array();
	if ( isset( $atts['view'] ) ) {

		// 2.3.2: view value to lowercase to be case insensitive
		$view = $atts['view'] = strtolower( $atts['view'] );
		if ( strstr( $atts['view'], ',' ) ) {
			$views = explode( ',', $atts['view'] );
			foreach ( $views as $i => $aview ) {
				if ( 'tabbed' == $aview ) {
					$aview = 'tabs';
				}
				$views[$i] = trim( $aview );
			}
			// 2.3.3.9: set default view for multiviews to table
			$defaults['default_view'] = 'table';
		} elseif ( 'tabbed' == $view ) {
			// 2.3.3.9: fix for possible misspelt tab view
			$view = 'tabs';
		}

		// 2.3.3.9: set prefixed defaults for multiple views
		if ( 'tabs' == $atts['view'] ) {
			// 2.3.2: add show descriptions default for tabbed view
			// 2.3.2: add display_day and display_date attributes
			// 2.3.3: revert show description default for tabbed view
			// 2.3.3.8: add default show image position (left aligned)
			// 2.3.3.8: add default for hide past shows (false)
			$defaults['show_image'] = 1;
			$defaults['show_hosts'] = 1;
			$defaults['show_genres'] = 1;
			$defaults['display_day'] = 'full';
			$defaults['display_date'] = false;
			$defaults['image_position'] = 'left';
			$defaults['hide_past_shows'] = false;
		} elseif ( in_array( 'tabs', $views ) ) {
			$defaults['tabs_show_image'] = 1;
			$defaults['tabs_show_hosts'] = 1;
			$defaults['tabs_show_genres'] = 1;
			$defaults['tabs_display_day'] = 'full';
			$defaults['tabs_display_date'] = false;
			$defaults['tabs_image_position'] = 'left';
			$defaults['tabs_hide_past_shows'] = false;
		}
		if ( 'list' == $atts['view'] ) {
			// 2.3.2: add display date attribute
			$defaults['show_genres'] = 1;
			$defaults['display_date'] = false;
		} elseif ( in_array( 'list', $views ) ) {
			$defaults['list_show_genres'] = 1;
			$defaults['list_display_date'] = false;		
		}
		if ( ( 'divs' == $atts['view'] ) || ( in_array( 'divs', $views ) ) ) {
			// 2.3.3.8: moved divs view only default here
			// 2.3.3.9: added check if divs in views array
			$defaults['divheight'] = 45;
		}
	}
	// 2.3.3.9: filter defaults according to view(s)
	$defaults = apply_filters( 'radio_station_master_schedule_default_atts', $defaults, $view, $views );

	// --- merge attributes with defaults ---
	$atts = shortcode_atts( $defaults, $atts, 'master-schedule' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Master Schedule Shortcode Attributes: ' . print_r( $atts, true ) . '</span>';
	}

	// --- enqueue schedule stylesheet ---
	// 2.3.0: use abstracted method for enqueueing widget styles
	radio_station_enqueue_style( 'schedule' );

	// --- set initial empty output string ---
	$output = '';

	// 2.3.3.9: remove check if clock shortcode present
	// 2.3.3.6: set new line for easier debug viewing
	$newline = '';
	if ( RADIO_STATION_DEBUG ) {
		$newline = "\n";
	}

	// --- table for selector and clock  ---
	// 2.3.0: moved out from templates to apply to all views
	// 2.3.2: moved shortcode calls inside and added filters
	$output .= '<div id="master-schedule-controls-wrapper">' . $newline;

		$controls = array();

		// --- display radio clock or timezone (or neither) ---
		if ( $atts['clock'] ) {

			// --- radio clock ---
			$controls['clock'] = '<div id="master-schedule-clock-wrapper">' . $newline;
			$clock_atts = apply_filters( 'radio_station_schedule_clock', array(), $atts );
			$controls['clock'] .= radio_station_clock_shortcode( $clock_atts );
			$controls['clock'] .= PHP_EOL . '</div>' . $newline;

		} elseif ( $atts['timezone'] ) {

			// --- radio timezone ---
			$controls['timezone'] = '<div id="master-schedule-timezone-wrapper">' . $newline;
			$timezone_atts = apply_filters( 'radio_station_schedule_clock', array(), $atts );
			$controls['timezone'] .= radio_station_timezone_shortcode( $timezone_atts );
			$controls['timezone'] .= PHP_EOL . '</div>' . $newline;

		}

		// --- genre selector ---
		if ( $atts['selector'] ) {
			$controls['selector'] = '<div id="master-schedule-selector-wrapper">' . $newline;
			$controls['selector'] .= radio_station_master_schedule_genre_selector();
			$controls['selector'] .= PHP_EOL . '</div>' . $newline;
		}

		// 2.3.1: add filters for control order
		$control_order = array( 'clock', 'timezone', 'selector' );
		$control_order = apply_filters( 'radio_station_schedule_control_order', $control_order, $atts );

		// 2.3.1: add filter for controls HTML
		$controls = apply_filters( 'radio_station_schedule_controls', $controls, $atts );

		// --- add ordered controls to output ---
		if ( is_array( $control_order ) && ( count( $control_order ) > 0 ) ) {
			foreach ( $control_order as $control ) {
				if ( isset( $controls[$control] ) && ( '' != $control ) ) {
					$output .= $controls[$control];
				}
			}
		}

	$output .= '<br></div><br>' . $newline;
	$output = apply_filters( 'radio_station_schedule_controls_output', $output, $atts );

	// --- hidden inputs for calendar start/active dates ---
	// 2.3.3.9: added for schedule week change reloading
	if ( isset( $atts['start_date'] ) && $atts['start_date'] ) {
		if ( $atts['start_date'] == date( 'Y-m-d', strtotime( $atts['start_date'] ) ) ) {
			$start_date = $atts['start_date'];
		}
	}
	if ( !isset( $start_date ) ) {
		$now = radio_station_get_now();
		$start_date = radio_station_get_time( 'date', $now );
	}
	$output .= '<input type="hidden" id="schedule-start-date" value="' . esc_attr( $start_date ) . '">';
	$active_date = $start_date;
	if ( isset( $atts['active_date'] ) && $atts['active_date'] ) {
		if ( $atts['active_date'] == date( 'Y-m-d', strtotime( $atts['active_date'] ) ) ) {
			$active_date = $atts['active_date'];
		}
	}
	$output .= '<input type="hidden" id="schedule-active-date" value="' . esc_attr( $active_date ) . '">';
	// 2.3.3.9: also added schedule start day input
	$start_day = ( $atts['start_day'] ) ? $atts['start_day'] : '';
	$output .= '<input type="hidden" id="schedule-start-day" value="' . esc_attr( $start_day ) . '">';

	// --- enqueue schedule loader script ---
	$js = radio_station_master_schedule_loader_js( $atts );
	wp_add_inline_script( 'radio-station', $js );

	// --- schedule display override ---
	// 2.3.1: add full schedule override filter
	// 2.3.3.9: add existing controls output to filter
	$override = apply_filters( 'radio_station_schedule_override', $output, $atts );
	if ( strstr( $override, '<!-- SCHEDULE OVERRIDE -->' ) ) {
		$override = str_replace( '<!-- SCHEDULE OVERRIDE -->', '', $override );
		return $override;
	}

	// -------------------------
	// New Master Schedule Views
	// -------------------------

	// --- load master schedule template ---
	// 2.2.7: added tabbed master schedule template
	// 2.2.7: add tabbed view javascript to footer
	// 2.3.0: use new data model for table and tabs view
	// 2.3.0: check for user theme templates
	// 2.3.3.9: use output buffering on templates
	// 2.3.3.9: get and enqueue scripts inline directly
	if ( 'table' == $atts['view'] ) {
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-table.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();
		$html = apply_filters( 'master_schedule_table_view', $html, $atts );
		$js = radio_station_master_schedule_table_js();
		wp_add_inline_script( 'radio-station', $js );
		return $output . $html;
	} elseif ( 'tabs' == $atts['view'] ) {
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-tabs.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();
		$html = apply_filters( 'master_schedule_tabs_view', $html, $atts );
		$js = radio_station_master_schedule_tabs_js();
		wp_add_inline_script( 'radio-station', $js );		
		return $output . $html;
	} elseif ( 'list' == $atts['view'] ) {
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-list.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();
		$html = apply_filters( 'master_schedule_list_view', $html, $atts );
		$js = radio_station_master_schedule_list_js();
		wp_add_inline_script( 'radio-station', $js );
		return $output . $html;
	}

	// ----------------------
	// Legacy Master Schedule
	// ----------------------
	// note: Legacy and Divs Views do not include Schedule Overrides

	global $wpdb;

	// 2.3.0: remove unused default DJ name option
	// $default_dj = get_option( 'dj_default_name' );

	// --- check to see what day of the week we need to start on ---
	$start_of_week = get_option( 'start_of_week' );
	$days_of_the_week = array(
		'Sunday'    => array(),
		'Monday'    => array(),
		'Tuesday'   => array(),
		'Wednesday' => array(),
		'Thursday'  => array(),
		'Friday'    => array(),
		'Saturday'  => array(),
	);
	$week_start = array_slice( $days_of_the_week, $start_of_week );
	foreach ( $days_of_the_week as $i => $weekday ) {
		if ( $start_of_week > 0 ) {
			$add = $days_of_the_week[$i];
			unset( $days_of_the_week[$i] );
			$days_of_the_week[$i] = $add;
		}
		$start_of_week --;
	}

	// --- create the master_list array based on the start of the week ---
	$master_list = array();
	for ( $i = 0; $i < 24; $i ++ ) {
		$master_list[$i] = $days_of_the_week;
	}

	// --- get the show schedules, excluding shows marked as inactive ---
	$show_shifts = $wpdb->get_results(
		"SELECT meta.post_id, meta.meta_value
		FROM {$wpdb->postmeta} AS meta
		JOIN {$wpdb->postmeta} AS active
			ON meta.post_id = active.post_id
		JOIN {$wpdb->posts} as posts
			ON posts.ID = meta.post_id
		WHERE meta.meta_key = 'show_sched' AND
			posts.post_status = 'publish' AND
			(
				active.meta_key = 'show_active' AND
				active.meta_value = 'on'
			)"
	);

	// --- insert scheduled shifts into the master list ---
	foreach ( $show_shifts as $shift ) {
		$shift->meta_value = maybe_unserialize( $shift->meta_value );

		// if a show is not scheduled yet, unserialize will return false... fix that.
		if ( !is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();
		}

		foreach ( $shift->meta_value as $time ) {

			// 2.3.0: added check for show disabled switch
			if ( !isset( $time['disabled'] ) || ( 'yes' == $time['disabled'] ) ) {

				// --- switch to 24-hour time ---
				if ( 'pm' === $time['start_meridian'] && 12 !== (int) $time['start_hour'] ) {
					$time['start_hour'] += 12;
				}
				if ( 'am' === $time['start_meridian'] && 12 === (int) $time['start_hour'] ) {
					$time['start_hour'] = 0;
				}

				if ( 'pm' === $time['end_meridian'] && 12 !== (int) $time['end_hour'] ) {
					$time['end_hour'] += 12;
				}
				if ( 'am' === $time['end_meridian'] && 12 === (int) $time['end_hour'] ) {
					$time['end_hour'] = 0;
				}

				// --- check if we are spanning multiple days ---
				$time['multi-day'] = 0;
				if ( $time['start_hour'] > $time['end_hour'] || $time['start_hour'] === $time['end_hour'] ) {
					$time['multi-day'] = 1;
				}

				$master_list[$time['start_hour']][$time['day']][$time['start_min']] = array(
					'id'   => $shift->post_id,
					'time' => $time,
				);
			}
		}
	}

	// --- sort the array by time ---
	foreach ( $master_list as $hour => $days ) {
		foreach ( $days as $day => $min ) {
			ksort( $min );
			$master_list[$hour][$day] = $min;

			// we need to take into account shows that start late at night and end the following day
			foreach ( $min as $i => $time ) {

				// if it ends at midnight, we don't need to worry about carry-over
				if ( 0 === (int) $time['time']['end_hour'] && 0 === (int) $time['time']['end_min'] ) {
					continue;
				}

				// if it ends after midnight, fix it
				// if it starts at night and ends in the morning, end hour is on the following day
				if ( ( 'pm' === $time['time']['start_meridian'] && 'am' === $time['time']['end_meridian'] ) ||
				     // if the start and end times are identical, assume the end time is the following day
				     ( $time['time']['start_hour'] . $time['time']['start_min'] . $time['time']['start_meridian'] === $time['time']['end_hour'] . $time['time']['end_min'] . $time['time']['end_meridian'] ) ||
				     // if the start hour is in the morning, and greater than the end hour, assume end hour is the following day
				     ( 'am' === $time['time']['start_meridian'] && $time['time']['start_hour'] > $time['time']['end_hour'] )
				) {

					if ( 12 === (int) $atts['time'] ) {
						$time['time']['real_start'] = ( $time['time']['start_hour'] - 12 ) . ':' . $time['time']['start_min'];
					} else {
						$pad_hour = '';
						if ( $time['time']['start_hour'] < 10 ) {
							$pad_hour = '0';
						}
						$time['time']['real_start'] = $pad_hour . $time['time']['start_hour'] . ':' . $time['time']['start_min'];
					}
					$time['time']['rollover'] = 1;

					// 2.3.0: use new get next day function
					$nextday = radio_station_get_next_day( $day );

					$master_list[0][$nextday]['00'] = $time;

				}
			}
		}
	}

	// --- check for schedule overrides ---
	// ? TODO - check/include schedule overrides in legacy template views
	// $overrides = radio_station_master_get_overrides( true );

	// --- include the specified master schedule output template ---
	// 2.3.0: check for user theme templates
	if ( 'divs' == $atts['view'] ) {
		$output = ''; // no selector / clock support yet
		$template = radio_station_get_template( 'file', 'master-schedule-div.php' );
		require $template;
	} elseif ( 'legacy' == $atts['view'] ) {
		$template = radio_station_get_template( 'file', 'master-schedule-legacy.php' );
		require $template;
	}

	return $output;
}

// ----------------------
// Schedule Loader Script
// ----------------------
function radio_station_master_schedule_loader_js( $atts ) {

	// --- set AJAX URL with attribute keys  ---
	$loader_url = esc_url( add_query_arg( 'action', 'radio_station_schedule', admin_url( 'admin-ajax.php' ) ) );
	$ignore_keys = array( 'start_date', 'view' );
	foreach ( $atts as $key => $value ) {
		if ( !in_array( $key, $ignore_keys ) ) {
			$loader_url .= '&' . esc_js( $key ) . '=' . esc_js( $value );
		}
	}

	// --- schedule loader function ---
	$js = "function radio_load_schedule(direction,view,clear) {
		if (document.getElementById('schedule-loader-frame')) {
			view = radio_cookie.get('admin_schedule_view'); radio_load_view(view); return;
		}
		startday = document.getElementById('schedule-start-day').value;
		startdate = document.getElementById('schedule-start-date').value;
		activedate = document.getElementById('schedule-active-date').value;
		if (!view) {
			if (jQuery('.master-schedule-view-tab.current-view').length) {
				view = jQuery('.master-schedule-view-tab.current-view').attr('id').replace('master-schedule-view-tab-','');
			} else {
				if (jQuery('#master-program-schedule').length) {view = 'table';}
				else if (jQuery('#master-schedule-tabs').length) {view = 'tabs';}
				else if (jQuery('#master-schedule-list').length) {view = 'list';}
				else if (jQuery('#master-schedule-grid').length) {view = 'grid';}
				else if (jQuery('#master-schedule-calendar').length) {view = 'calendar';}
			}
		}
		if (radio.debug) {console.log('Reloading Schedule View: '+view);}
		if (!view) {return;}
		if (!direction) {offset = 0;}
		else if (direction == 'previous') {offset = -(7 * 24 * 60 * 60 * 1000);}
		else if (direction == 'next') {offset = (7 * 24 * 60 * 60 * 1000);}
		newdate = new Date(new Date(startdate).getTime() + offset).toISOString().substr(0,10);
		url = '" . $loader_url . "&view='+view;
		timestamp = Math.floor((new Date()).getTime() / 1000);
		url += '&timestamp='+timestamp+'&start_date='+newdate+'&active_date='+activedate;
		if (startday != '') {url += '&start_day='+startday;}
		if (clear) {url += '&clear=1';}
		if (radio.debug) {url += '&rs-debug=1'; console.log('Reload View URL: '+url);}
		if (document.getElementById('schedule-'+view+'-loader').src != url) {
			document.getElementById('schedule-'+view+'-loader').src = url;
		}
	}" . PHP_EOL;

	// --- filter and return ---
	$js = apply_filters( 'radio_station_master_schedule_loader_js', $js );
	return $js;
} 
	
// --------------------
// AJAX Schedule Loader
// --------------------
add_action( 'wp_ajax_radio_station_schedule', 'radio_station_ajax_schedule_loader' );
add_action( 'wp_ajax_nopriv_radio_station_schedule', 'radio_station_ajax_schedule_loader' );
function radio_station_ajax_schedule_loader() {

	// --- maybe clear cached data ---
	if ( isset( $_REQUEST['clear'] ) && ( '1' == $_REQUEST['clear'] ) ) {
		radio_station_clear_cached_data( false );
	}

	// --- sanitize shortcode attributes ---
	$atts = radio_station_sanitize_shortcode_values( 'master-schedule' );
	if ( RADIO_STATION_DEBUG ) {
		print_r( $_REQUEST );
		echo "Sanitized Master Schedule Shortcode Attributes: " . print_r( $atts, true );
	}

	// --- output schedule contents ---
	echo '<div id="schedule-contents">';
	echo radio_station_master_schedule( $atts );
	echo '</div>';

	$js = '';
	if ( strstr( $atts['view'], ',' ) ) {
		$views = explode( ',', $atts['view'] );
	} else {
		$views = array( $atts['view'] );
	}
	foreach ( $views as $view ) {

		$view = trim( $view );

		// --- set schedule element ID ---
		if ( 'table' == $view ) {
			$schedule_id = 'master-program-schedule';
		} elseif ( 'tabs' == $view ) {
			$schedule_id = 'master-schedule-tabs';
			$panels_id = 'master-schedule-tab-panels';
		} elseif ( 'list' == $view ) {
			$schedule_id = 'master-list';
		} elseif ( 'grid' == $view ) {
			$schedule_id = 'master-schedule-grid';
		} elseif ( 'calendar' == $view ) {
			$schedule_id = 'master-schedule-calendar';
		}

		// --- send new schedule to parent window ---
		// $js .= "document.getElementById('schedule-contents').innerHTML = '';" . PHP_EOL;
		$js .= "schedule = document.getElementById('" . esc_attr( $schedule_id ) . "').innerHTML;" . PHP_EOL;
		$js .= "parent.document.getElementById('" . esc_attr( $schedule_id ) . "').innerHTML = schedule;" . PHP_EOL;
		if ( isset( $panels_id ) ) {
			$js .= "panels = document.getElementById('" . esc_attr( $panels_id ) . "').innerHTML;" . PHP_EOL;
			$js .= "parent.document.getElementById('" . esc_attr( $panels_id ) . "').innerHTML = panels;" . PHP_EOL;
		}

		// --- copy the new start date value to the parent window ---
		$js .= "start_date = document.getElementById('schedule-start-date').value;" . PHP_EOL;
		$js .= "parent.document.getElementById('schedule-start-date').value = start_date;" . PHP_EOL;

		// --- maybe retrigger view(s) javascript in parent window ---
		// (uses set interval cycle in case script not yet loaded)
		$js .= "var genres_highlighted = false;" . PHP_EOL;
		$js .= "schedule_loader = setInterval(function() {" . PHP_EOL;
			$js .= "if (!genres_highlighted && (typeof parent.radio_genre_highlight == 'function')) {" . PHP_EOL;
				$js .= "parent.radio_genre_highlight();" . PHP_EOL;
				$js .= "genres_highlighted = true;" . PHP_EOL;
			$js .= "}" . PHP_EOL;
			if ( 'table' == $view ) {
				$js .= "if (typeof parent.radio_table_initialize == 'function') {";
					$js .= "parent.radio_table_initialize();" . PHP_EOL;
					$js .= "clearInterval(schedule_loader);" . PHP_EOL;
				$js .= "}" . PHP_EOL;
			} elseif ( 'tabs' == $view ) {
				$js .= "if (typeof parent.radio_tabs_initialize == 'function') {";
					$js .= "parent.radio_tabs_init = false;" . PHP_EOL;
					$js .= "parent.radio_tabs_initialize();" . PHP_EOL;
					$js .= "clearInterval(schedule_loader);" . PHP_EOL;
				$js .= "}" . PHP_EOL;
			} elseif ( 'list' == $view ) {
				$js .= "if (typeof parent.radio_list_highlight == 'function') {";
					$js .= "parent.radio_list_highlight();" . PHP_EOL;
					$js .= "clearInterval(schedule_loader);" . PHP_EOL;
			}
			$js .= "if (typeof parent.radio_convert_times == 'function') {parent.radio_convert_times();}" . PHP_EOL;

			// --- placeholder for extra loader functions ---
			// (do not remove or modify this line)
			$js .= "/* LOADER PLACEHOLDER */";

		$js .= "}, 2000);" . PHP_EOL;
	}

	// --- filter load script and output ---
	$js = apply_filters( 'radio_station_master_schedule_load_script', $js, $atts );
	if ( '' != $js ) {
		echo "<script>" . $js . "</script>";
	}

	exit;

}


// -------------------
// Show Genre Selector
// -------------------
// 2.3.3.9: change name from radio_station_master_schedule_selector
function radio_station_master_schedule_genre_selector() {

	// --- get genres ---
	$args = array(
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	$genres = get_terms( RADIO_STATION_GENRES_SLUG, $args );
	// 2.3.2: bug out if there are no genre terms
	if ( !$genres || !is_array( $genres ) ) {
		return '';
	}

	// --- open genre highlighter div ---
	$html = '<div id="master-genre-list">';
	$html .= '<span class="heading">' . esc_html( __( 'Genres', 'radio-station' ) ) . ': </span>';

	// --- genre highlight links ---
	// 2.3.0: fix by imploding with genre link spacer
	// 2.3.3.9: escape genre slug and assign javascript to onclick
	$genre_links = array();
	foreach ( $genres as $i => $genre ) {
		$slug = sanitize_title_with_dashes( $genre->name );
		$onclick = "radio_genre_highlight('" . esc_attr( $slug  ) . "');";
		$title = __( 'Click to toggle Highlight of Shows with this Genre.', 'radio-station' );
		$genre_link = '<a id="genre-highlight-' . esc_attr( $slug ) . '" class="genre-highlight" href="javascript:void(0);" onclick="' . $onclick . '" title="' . esc_attr( $title ) . '">';
		$genre_link .= esc_html( $genre->name ) . '</a>';
		$genre_links[] = $genre_link;
	}
	$html .= implode( ' | ', $genre_links );

	$html .= '</div>';

	// --- genre highlighter script ---
	// 2.3.0: improved to highlight / unhighlight multiple genres
	// 2.3.0: improved to work with table, tabs or list view
	// 2.3.3.9: added genre class targets for grid and calendar views
	// 2.3.3.9: added accepting false to retrigger highlights for AJAX
	$js = "var radio_genres_selected = new Array();
	function radio_genre_highlight(genre) {
		classes = '.master-show-entry, .master-schedule-tabs-show, .master-list-day-item, .master-schedule-grid-show, .master-schedule-calendar-date, .master-schedule-calendar-show';
		if (genre === false) {
			jQuery(classes).removeClass('highlighted');
			for (i = 0; i < radio_genres_selected.length; i++) {
				jQuery('.'+radio_genres_selected[i]).addClass('highlighted');
			}		
		} else {
			if (jQuery('#genre-highlight-'+genre).hasClass('highlighted')) {
				jQuery('#genre-highlight-'+genre).removeClass('highlighted');
				jQuery(classes).removeClass('highlighted');

				j = 0; new_genre_highlights = new Array();
				for (i = 0; i < radio_genres_selected.length; i++) {
					if (radio_genres_selected[i] != genre) {
						jQuery('.'+radio_genres_selected[i]).addClass('highlighted');
						new_genre_highlights[j] = radio_genres_selected[i]; j++;
					}
				}
				radio_genres_selected = new_genre_highlights;

			} else {
				jQuery('#genre-highlight-'+genre).addClass('highlighted');
				radio_genres_selected[radio_genres_selected.length] = genre;
				jQuery('.'+genre).each(function () {
					jQuery(this).addClass('highlighted');
				});
			}
		}
		/* console.log(jQuery(classes)); */
	}";

	// --- enqueue script ---
	// 2.3.0: add script code to existing handle
	wp_add_inline_script( 'radio-station', $js );

	return $html;
}

// ---------------------
// Table View Javascript
// ---------------------
// 2.3.0: added for table responsiveness
function radio_station_master_schedule_table_js() {

	// 2.3.2: added current show highlighting cycle
	// 2.3.2: fix to currenthour substr
	// 2.3.3.5: change selected day and arrow logic (to single day shifting)
	// 2.3.3.6: also highlight split shift via matching shift class
	// 2.3.3.9: prefix show-info selector with .master-program-hour-row
	// 2.3.3.9: use setInterval instead of setTimeout for highlighting
	// 2.3.3.9: check for required elements before executing functions
	// 2.3.3.9: fix to check before and after current time not show
	$js = "/* Initialize Table */
	var radio_table_init = false;
	jQuery(document).ready(function() {
		radio_table_initialize();
		var radio_table_highlighting = setInterval(radio_table_highlight, 60000);
	});
	jQuery(window).resize(function () {
		radio_resize_debounce(function() {radio_table_responsive(false);}, 500, 'scheduletable');
	});

	/* Table Initialize */
	function radio_table_initialize() {
		radio_table_responsive(false);
		radio_table_highlight();
		radio_table_init = true;
	}

	/* Current Time Highlighting */
	function radio_table_highlight() {
		if (!jQuery('.master-program-day').length) {return;}
		radio.current_time = Math.floor((new Date()).getTime() / 1000);
		radio.offset_time = radio.current_time + radio.timezone.offset;
		if (radio.debug) {console.log(radio.current_time+' - '+radio.offset_time);}
		if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
		jQuery('.master-program-day').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				jQuery(this).addClass('current-day');
			} else {jQuery(this).removeClass('current-day');}
		});
		jQuery('.master-program-hour').each(function() {
			hour = parseInt(jQuery(this).find('.master-program-server-hour').attr('data'));
			offset_time = radio.current_time + radio.timezone.offset;
			current = new Date(offset_time * 1000).toISOString();
			currenthour = current.substr(11, 2);
    		if (currenthour.substr(0,1) == '0') {currenthour = currenthour.substr(1,1);}
			if (hour == currenthour) {jQuery(this).addClass('current-hour');}
			else {jQuery(this).removeClass('current-hour');}
		});
		for (i = 0; i < 7; i++) {
			jQuery('#master-program-schedule .day-'+i).each(function() {
				var radio_table_shift = false;
				jQuery(this).find('.master-show-entry').each(function() {
					start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
					end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
					if (radio.debug) {console.log(jQuery(this)); console.log(start+' - '+end);}
					if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
						if (radio.debug) {console.log('^ Now Playing ^');}
						jQuery(this).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
						/* also highlight split shift via matching shift class */
						if (jQuery(this).hasClass('overnight')) {
							classes = jQuery(this).attr('class').split(/\s+/);
							for (i = 0; i < classes.length; i++) {
								if (classes[i].substr(0,6) == 'split-') {radio_table_shift = classes[i];}
							}
						}
					} else {
						jQuery(this).removeClass('nowplaying');
						if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
						else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
					}
				});
				if (radio_table_shift) {
					jQuery('.'+radio_table_shift).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
				}
			});
		}
	}

	/* Make Table Responsive */
	function radio_table_responsive(leftright) {
		if (!jQuery('.master-program-day').length) {return;}

		fallback = -1; selected = -1; foundday = false;
		if (!leftright || (leftright == 'left')) {
			if (jQuery('.master-program-day.first-column').length) {
				start = jQuery('.master-program-day.first-column');
			} else {start = jQuery('.master-program-day').first(); fallback = 0;}
			classes = start.attr('class').split(' ');
		} else if (leftright == 'right') {
			if (jQuery('.master-program-day.last-column').length) {
				end = jQuery('.master-program-day.last-column');
			} else {end = jQuery('.master-program-day').last(); fallback = 6;}
			classes = end.attr('class').split(' ');
		}
		for (i = 0; i < classes.length; i++) {
			if (classes[i].indexOf('day-') === 0) {selected = parseInt(classes[i].replace('day-',''));}
		}
		if (selected < 0) {selected = fallback;}
		if (radio.debug) {console.log('Current Column: '+selected);}

		if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
		if (selected < 0) {selected = 0;} else if (selected > 6) {selected = 6;}
		if (!jQuery('.master-program-day.day-'+selected).length) {
			while (!foundday) {
				if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
				if (jQuery('.master-program-day.day-'+selected).length) {foundday = true;}
				if ((selected < 0) || (selected > 6)) {selected = fallback; foundday = true;}
			}
		}
		if (radio.debug) {console.log('Selected Column: '+selected);}

		totalwidth = jQuery('#master-program-hour-heading').width();
		jQuery('.master-program-day, .master-program-hour-row .show-info').removeClass('first-column').removeClass('last-column').hide();
		jQuery('#master-program-schedule').css('width','100%');
		tablewidth = jQuery('#master-program-schedule').width();
		jQuery('#master-program-schedule').css('width','auto');
		columns = 0; firstcolumn = -1; lastcolumn = 7; endtable = false;
		for (i = selected; i < 7; i++) {
			if (!endtable && (jQuery('.master-program-day.day-'+i).length)) {
				if ((i > 0) && (i == selected)) {jQuery('.master-program-day.day-'+i).addClass('first-column'); firstcolumn = i;}
				else if (i < 6) {jQuery('.master-program-day.day-'+i).addClass('last-column');}
				jQuery('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).show();
				colwidth = jQuery('.master-program-day.day-'+i).width();
				totalwidth = totalwidth + colwidth;
				if (radio.debug) {console.log('('+colwidth+') : '+totalwidth+' / '+tablewidth);}
				jQuery('.master-program-day.day-'+i).removeClass('last-column');
				if (totalwidth > tablewidth) {
					if (radio.debug) {console.log('Hiding Column '+i);}
					jQuery('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).hide(); endtable = true;
				} else {
					if (radio.debug) {console.log('Showing Column '+i);}
					jQuery('.master-program-day.day-'+i).removeClass('last-column');
					totalwidth = totalwidth - colwidth + jQuery('.master-program-day.day-'+i).width();
					lastcolumn = i; columns++;
				}
			}

		}
		if (lastcolumn < 6) {jQuery('.master-program-day.day-'+lastcolumn).addClass('last-column');}

		if (leftright == 'right') {
			for (i = (selected - 1); i > -1; i--) {
				if (!endtable && (jQuery('.master-program-day.day-'+i).length)) {
					jQuery('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).show();
					colwidth = jQuery('.master-program-day.day-'+i).width();
					totalwidth = totalwidth + colwidth;
					if (radio.debug) {console.log('('+colwidth+') : '+totalwidth+' / '+tablewidth);}
					if (totalwidth > tablewidth) {
						if (radio.debug) {console.log('Hiding Column '+i);}
						jQuery('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).hide(); endtable = true;
					} else {
						if (radio.debug) {console.log('Showing Column '+i);}
						jQuery('.master-program-day').removeClass('first-column');
						jQuery('.master-program-day.day-'+i).addClass('first-column');
						columns++;
					}
				}
			}
		}
		jQuery('#master-program-schedule').css('width','100%');
	}

	/* Shift Day Left /  Right */
	function radio_shift_day(leftright) {
		radio_table_responsive(leftright); return false;
	}" . PHP_EOL;

	// --- filter and return ---
	// 2.3.3.9: add filter and return instead of inline enqueue
	$js = apply_filters( 'radio_station_master_schedule_table_js', $js );
	return $js;
}

// ----------------------
// Tabbed View Javascript
// ----------------------
// 2.2.7: added for tabbed schedule view
function radio_station_master_schedule_tabs_js() {

	// --- tab switching function ---
	// 2.3.2: added fallback if current day is not viewed
	// TODO: check current server time for onload display
	/* date = new Date(); dayweek = date.getDay(); day = radio_get_weekday(dayweek);
	if (jQuery('#master-schedule-tabs-header-'+day).length) {
		id = jQuery('.master-schedule-tabs-day.selected-day').first().attr('id');
		day = id.replace('master-schedule-tabs-header-','');
		jQuery('#master-schedule-tabs-header-'+day).addClass('active-day-tab');
		jQuery('#master-schedule-tabs-day-'+day).addClass('active-day-panel');
	} else {
		jQuery('.master-schedule-tabs-day').first().addClass('active-day-tab');
		jQuery('.master-schedule-tabs-panel').first().addClass('active-day-panel');
	} */

	// 2.3.3.6: allow for clicking on date to change days
	// 2.3.3.8: make entire heading label div clickable to change tabs
	// 2.3.3.9: make into function and add to document ready code block
	$js = "function radio_tabs_clicks() {
		jQuery('.master-schedule-tabs-headings').bind('click', function (event) {
			headerID = jQuery(event.target).closest('li').attr('id');
			panelID = headerID.replace('header', 'day');
			jQuery('.master-schedule-tabs-day').removeClass('active-day-tab');
			jQuery('#'+headerID).addClass('active-day-tab');
			jQuery('.master-schedule-tabs-panel').removeClass('active-day-panel');
			jQuery('#'+panelID).addClass('active-day-panel');
		});
	}" . PHP_EOL;

	// --- tabbed view responsiveness ---
	// 2.3.0: added for tabbed responsiveness
	// 2.3.2: display selected day message if outside view
	// 2.3.3.5: change selected day and arrow logic (to single day shifting)
	// 2.3.3.6: also highlight split shift via matching shift class
	// 2.3.3.9: use setInterval instead of setTimeout for highlighting check
	// 2.3.3.9: check for required elements before executing functions
	// 2.3.3.9: fix to check before and after current time not show
	// 2.3.3.9: adjust responsive tabs for possible loader control presence
	$js .= "/* Initialize Tabs */
	var radio_tabs_init = false;
	jQuery(document).ready(function() {
		radio_tabs_initialize();
		var radio_tab_highlighting = setInterval(radio_tabs_show_highlight, 60000);
	});
	jQuery(window).resize(function () {
		radio_resize_debounce(function() {radio_tabs_responsive(false);}, 500, 'scheduletabs');
	});

	function radio_tabs_initialize() {
		radio_tabs_clicks();
		radio_tabs_responsive(false);
		radio_tabs_show_highlight();
		radio_tabs_init = true;	
	}

	/* Set Day Tab on Load */
	function radio_tabs_active_tab(day) {
		if (radio_tabs_init) {return;}
		if (!jQuery('.master-schedule-tabs-headings').length) {return;}
		jQuery('.master-schedule-tabs-day').removeClass('active-day-tab');
		jQuery('.master-schedule-tabs-panel').removeClass('active-day-panel');
		if (!day) {
			jQuery('.master-schedule-tabs-day').first().addClass('active-day-tab');
			jQuery('.master-schedule-tabs-panel').first().addClass('active-day-panel');
		} else {
			jQuery('#master-schedule-tabs-header-'+day).addClass('active-day-tab');
			jQuery('#master-schedule-tabs-day-'+day).addClass('active-day-panel');
		}
	}

	/* Current Show Highlighting */
	function radio_tabs_show_highlight() {
		if (!jQuery('.master-schedule-tabs-headings').length) {return;}
		radio.current_time = Math.floor( (new Date()).getTime() / 1000 );
		radio.offset_time = radio.current_time + radio.timezone.offset;
		if (radio.debug) {console.log(radio.current_time+' - '+radio.offset_time);}
		if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
		jQuery('.master-schedule-tabs-day').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				jQuery(this).addClass('current-day');
				day = jQuery(this).attr('id').replace('master-schedule-tabs-header-', '');
				radio_tabs_active_tab(day);
			} else {jQuery(this).removeClass('current-day');}
		});
		radio_tabs_active_tab(false); /* fallback */
		var radio_tabs_split = false;
		jQuery('.master-schedule-tabs-show').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if (radio.debug) {console.log(start+' - '+end);}
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				if (radio.debug) {console.log('^ Now Playing ^');}
				jQuery(this).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
				/* also highlight split shift via matching shift class */
				if (jQuery(this).hasClass('overnight')) {
					classes = jQuery(this).attr('class').split(/\s+/);
					for (i = 0; i < classes.length; i++) {
						if (classes[i].substr(0,6) == 'split-') {radio_tabs_split = classes[i];}
					}
				}
			} else {
				jQuery(this).removeClass('nowplaying');
				if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
				else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
			}
		});
		if (radio_tabs_split) {
			jQuery('.'+radio_tabs_split).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
		}
	}

	/* Make Tabs Responsive */
	function radio_tabs_responsive(leftright) {
		if (!jQuery('.master-schedule-tabs-headings').length) {return;}

		fallback = -1; selected = -1; foundtab = false;
		if (!leftright || (leftright == 'left')) {
			if (jQuery('.master-schedule-tabs-day.first-tab').length) {
				start = jQuery('.master-schedule-tabs-day.first-tab');
			} else {start = jQuery('.master-schedule-tabs-day').first(); fallback = 0;}
			classes = start.attr('class').split(' ');
		} else if (leftright == 'right') {
			if (jQuery('.master-schedule-tabs-day.last-tab').length) {
				end = jQuery('.master-schedule-tabs-day.last-tab');
			} else {end = jQuery('.master-schedule-tabs-day').last(); fallback = 6;}
			classes = end.attr('class').split(' ');
		}
		for (i = 0; i < classes.length; i++) {
			if (classes[i].indexOf('day-') === 0) {selected = parseInt(classes[i].replace('day-',''));}
		}
		if (selected < 0) {selected = fallback;}
		if (radio.debug) {console.log('Current Tab: '+selected);}

		if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
		if (selected < 0) {selected = 0;} else if (selected > 6) {selected = 6;}
		if (!jQuery('.master-schedule-tabs-day.day-'+selected).length) {
			while (!foundtab) {
				if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
				if (jQuery('.master-schedule-tabs-day.day-'+selected).length) {foundtab = true;}
				if ((selected < 0) || (selected > 6)) {selected = fallback; foundtab = true;}
			}
		}
		if (radio.debug) {console.log('Selected Tab: '+selected);}

		jQuery('#master-schedule-tabs').css('width','100%');
		tabswidth = jQuery('#master-schedule-tabs').width();
		jQuery('#master-schedule-tabs').css('width','auto');
		jQuery('.master-schedule-tabs-day').removeClass('first-tab').removeClass('last-tab').hide();

		totalwidth = 0; tabs = 0; firsttab = -1; lasttab = 7; endtabs = false;
		if (jQuery('#master-schedule-tabs-loader-left').length) {totalwidth = totalwidth + jQuery('#master-schedule-tabs-loader-left').width();}
		if (jQuery('#master-schedule-tabs-loader-right').length) {totalwidth = totalwidth + jQuery('#master-schedule-tabs-loader-right').width();}
		
		for (i = selected; i < 7; i++) {
			if (!endtabs && (jQuery('.master-schedule-tabs-day.day-'+i).length)) {
				if ((i > 0) && (i == selected)) {jQuery('.master-schedule-tabs-day.day-'+i).addClass('first-tab'); firsttab = i;}
				else if (i < 6) {jQuery('.master-schedule-tabs-day.day-'+i).addClass('last-tab');}
				tabwidth = jQuery('.master-schedule-tabs-day.day-'+i).show().width();
				mleft = parseInt(jQuery('.master-schedule-tabs-day.day-'+i).css('margin-left').replace('px',''));
				mright = parseInt(jQuery('.master-schedule-tabs-day.day-'+i).css('margin-right').replace('px',''));
				totalwidth = totalwidth + tabwidth + mleft + mright;
				if (radio.debug) {console.log(tabwidth+' - ('+mleft+'/'+mright+') - '+totalwidth+' / '+tabswidth);}
				if (totalwidth > tabswidth) {
					if (radio.debug) {console.log('Hiding Tab '+i);}
					jQuery('.master-schedule-tabs-day.day-'+i).hide(); endtabs = true;
				} else {
					jQuery('.master-schedule-tabs-day.day-'+i).removeClass('last-tab');
					totalwidth = totalwidth - tabwidth + jQuery('.master-schedule-tabs-day.day-'+i).width();
					if (radio.debug) {console.log('Showing Tab '+i);}
					lasttab = i; tabs++;
				}
			}
		}
		if (lasttab < 6) {jQuery('.master-schedule-tabs-day.day-'+lasttab).addClass('last-tab');}

		if (leftright == 'right') {
			for (i = (selected - 1); i > -1; i--) {
				if (!endtabs && (jQuery('.master-schedule-tabs-day.day-'+i).length)) {
					tabwidth = jQuery('.master-schedule-tabs-day.day-'+i).show().width();
					mleft = parseInt(jQuery('.master-schedule-tabs-day.day-'+i).css('margin-left').replace('px',''));
					mright = parseInt(jQuery('.master-schedule-tabs-day.day-'+i).css('margin-right').replace('px',''));
					totalwidth = totalwidth + tabwidth + mleft + mright;
					if (radio.debug) {console.log(tabwidth+' - ('+mleft+'/'+mright+') - '+totalwidth+' / '+tabswidth);}
					if (totalwidth > tabswidth) {
						if (radio.debug) {console.log('Hiding Tab '+i);}
						jQuery('.master-schedule-tabs-day.day-'+i).hide(); endtabs = true;
					} else {
						jQuery('.master-schedule-tabs-day').removeClass('first-tab');
						jQuery('.master-schedule-tabs-day.day-'+i).addClass('first-tab');
						if (radio.debug) {console.log('Showing Tab '+i);}
						tabs++;
					}
				}
			}
		}
		jQuery('#master-schedule-tabs').css('width','100%');

		/* display selected day message if outside view */
		activeday = false;
		for (i = 0; i < 7; i++) {
			if (jQuery('.master-schedule-tabs-day.day-'+i).length) {
				if (jQuery('.master-schedule-tabs-day.day-'+i).hasClass('active-day-tab')) {activeday = i;}
			}
		}
		jQuery('.master-schedule-tabs-selected').hide();
		if ( activeday && ( (activeday > lasttab) || (activeday < firsttab ) ) ) {
			jQuery('#master-schedule-tabs-selected-'+activeday).show();
		}

		if (radio.debug) {
			console.log('Active Day: '+activeday);
			console.log('Selected: '+selected);
			console.log('Fallback: '+fallback);
			console.log('First Tab: '+firsttab);
			console.log('Last Tab: '+lasttab);
			console.log('Visible Tabs: '+tabs);
		}
	}

	/* Shift Day Left / Right */
	function radio_shift_tab(leftright) {
		radio_tabs_responsive(leftright); return false;
	}" . PHP_EOL;

	// --- filter and return ---
	// 2.3.3.9: add filter and return instead of inline enqueue
	$js = apply_filters( 'radio_station_master_schedule_tabs_js', $js );
	return $js;
}

// --------------------
// List View Javascript
// --------------------
// 2.3.2: added for list schedule view
function radio_station_master_schedule_list_js() {

	// --- list view javascript ---
	// 2.3.3.6: also highlight split shift via matching shift class
	// 2.3.3.9: use setInterval instead of setTimeout for highlighting
	// 2.3.3.9: check for required elements before executing functions
	// 2.3.3.9: fix to check before and after current time not show
	$js = "/* Initialize List */
	jQuery(document).ready(function() {
		radio_list_highlight();
		var radio_list_highlighting = setInterval(radio_list_highlight, 60000);
	});
	/* Current Show Highlighting */
	function radio_list_highlight() {
		if (!jQuery('.master-list-day').length) {return;}
		radio.current_time = Math.floor( (new Date()).getTime() / 1000 );
		radio.offset_time = radio.current_time + radio.timezone.offset;
		if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
		jQuery('.master-list-day').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').first().attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').first().attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				jQuery(this).addClass('current-day');
			} else {jQuery(this).removeClass('current-day');}
		});
		var radio_list_split = false;
		jQuery('.master-list-day-item').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				radio_list_current = true;
				if (radio.debug) {console.log('^ Now Playing ^');}
				jQuery(this).addClass('nowplaying');
				/* also highlight split shift via matching shift class */
				if (jQuery(this).hasClass('overnight')) {
					classes = jQuery(this).attr('class').split(/\s+/);
					for (i = 0; i < classes.length; i++) {
						if (classes[i].substr(0,6) == 'split-') {radio_list_split = classes[i];}
					}
				}
			} else {
				jQuery(this).removeClass('nowplaying');
				if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
				else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
			}
		});
		if (radio_list_split) {
			jQuery('.'+radio_list_split).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
		}
	}" . PHP_EOL;

	// --- filter and return ---
	// 2.3.3.9: add filter and return instead of inline enqueue
	$js = apply_filters( 'radio_station_master_schedule_list_js', $js );	
	return $js;
}
