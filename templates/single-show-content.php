<?php

// === Show Content Template ===
// Package: radio-station
// Author: Tony Hayes
// @since 2.3.0

// -----------------
// Set Template Data
// -----------------

// --- get global and get show post ID ---
global $radio_station_data, $post;
$post_id = $radio_station_data['show-id'] = $post->ID;

// 2.3.3.6: set new line for easier debug viewing
$newline = '';
if ( RADIO_STATION_DEBUG ) {
	$newline = "\n";
}

// --- get schedule time format ---
$time_format = (int) radio_station_get_setting( 'clock_time_format', $post_id );

// --- get show meta data ---
$show_title = get_the_title( $post_id );
$header_id = get_post_meta( $post_id, 'show_header', true );
$avatar_id = get_post_meta( $post_id, 'show_avatar', true );
$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
$genres = wp_get_post_terms( $post_id, RADIO_STATION_GENRES_SLUG );
$languages = wp_get_post_terms( $post_id, RADIO_STATION_LANGUAGES_SLUG );
$hosts = get_post_meta( $post_id, 'show_user_list', true );
$producers = get_post_meta( $post_id, 'show_producer_list', true );
$active = get_post_meta( $post_id, 'show_active', true );
$shifts = radio_station_get_show_schedule( $post_id );

// --- get show icon / button data ---
// 2.3.3.6: added Show phone display
$show_file = get_post_meta( $post_id, 'show_file', true );
$show_link = get_post_meta( $post_id, 'show_link', true );
$show_email = get_post_meta( $post_id, 'show_email', true );
$show_phone = get_post_meta( $post_id, 'show_phone', true );
$show_patreon = get_post_meta( $post_id, 'show_patreon', true );
// $show_rss = get_post_meta( $post_id, 'show_rss', true );
$show_rss = false; // TEMP

// 2.3.2: added show download disabled check
$show_download = true;
$download = get_post_meta( $post_id, 'show_download', true );
if ( 'on' == $download ) {
	// note: on = disabled
	$show_download = false;
}

// --- filter all show meta data ---
// 2.3.2: added show download filter
$show_title = apply_filters( 'radio_station_show_title', $show_title, $post_id );
$header_id = apply_filters( 'radio_station_show_header', $header_id, $post_id );
$avatar_id = apply_filters( 'radio_station_show_avatar', $avatar_id, $post_id );
$thumbnail_id = apply_filters( 'radio_station_show_thumbnail', $thumbnail_id, $post_id );
$genres = apply_filters( 'radio_station_show_genres', $genres, $post_id );
$languages = apply_filters( 'radio_station_show_languages', $languages, $post_id );
$hosts = apply_filters( 'radio_station_show_hosts', $hosts, $post_id );
$producers = apply_filters( 'radio_station_show_producers', $producers, $post_id );
$active = apply_filters( 'radio_station_show_active', $active, $post_id );
$shifts = apply_filters( 'radio_station_show_shifts', $shifts, $post_id );
$show_file = apply_filters( 'radio_station_show_file', $show_file, $post_id );
$show_download = apply_filters( 'radio_station_show_download', $show_download, $post_id );
$show_link = apply_filters( 'radio_station_show_link', $show_link, $post_id );
$show_email = apply_filters( 'radio_station_show_email', $show_email, $post_id );
$show_phone = apply_filters( 'radio_station_show_phone', $show_phone, $post_id );
$show_patreon = apply_filters( 'radio_station_show_patreon', $show_patreon, $post_id );
$show_rss = apply_filters( 'radio_station_show_rss', $show_rss, $post_id );
$social_icons = apply_filters( 'radio_station_show_social_icons', false, $post_id );

// --- create show icon display early ---
// 2.3.0: converted show links to icons
$show_icons = array();
$icon_colors = array(
	'website'	=> '#A44B73',
	'email'		=> '#0086CC',
	'phone'     => '#008000',
	'rss'		=> '#FF6E01',
);

// --- show home link icon ---
// 2.3.3.4: added filter for title attribute
// 2.3.3.8: added alt text to span for screen readers
if ( $show_link ) {
	$title = __( 'Visit Show Website', 'radio-station' );
	$title = apply_filters( 'radio_station_show_website_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['website'] ) . ';" class="dashicons dashicons-admin-links" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_show_home_icon', $icon, $post_id );
	$show_icons['home'] = '<div class="show-icon show-website">' . $newline;
	$show_icons['home'] .= '<a href="' . esc_url( $show_link ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '" target="_blank">' . $newline;
	$show_icons['home'] .= $icon;
	$show_icons['home'] .= '</a>' . $newline;
	$show_icons['home'] .= '</div>' . $newline;
}

// --- phone number icon ---
// 2.3.3.6: added show phone icon
// 2.3.3.8: added aria label to link and hidden to span icon
if ( $show_phone ) {
	$title = __( 'Call in Phone Number', 'radio-station' );
	$title = apply_filters( 'radio_station_show_phone_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['phone'] ) . ';" class="dashicons dashicons-phone" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_show_phone_icon', $icon, $post_id );
	$show_icons['phone'] = '<div class="show-icon show-phone">' . $newline;
	$show_icons['phone'] .= '<a href="tel:' . esc_attr( $show_phone ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
	$show_icons['phone'] .= $icon . $newline;
	$show_icons['phone'] .= '</a>' . $newline;
	$show_icons['phone'] .= '</div>' . $newline;
}

// --- email DJ / host icon ---
// 2.3.3.4: added filter for title attribute
// 2.3.3.8: added aria label to link and hidden to span icon
if ( $show_email ) {
	$title = __( 'Email Show Host', 'radio-station' );
	$title = apply_filters( 'radio_station_show_email_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['email'] ) . ';" class="dashicons dashicons-email" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_show_email_icon', $icon, $post_id );
	$show_icons['email'] = '<div class="show-icon show-email">' . $newline;
	$show_icons['email'] .= '<a href="mailto:' . sanitize_email( $show_email ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
	$show_icons['email'] .= $icon . $newline;
	$show_icons['email'] .= '</a>' . $newline;
	$show_icons['email'] .= '</div>' . $newline;
}

// --- show RSS feed icon ---
// 2.3.3.4: added filter for title attribute
// 2.3.3.8: added aria label to link and hidden to span icon
if ( $show_rss ) {
	$feed_url = radio_station_get_show_rss_url( $post_id );
	$title =  __( 'Show RSS Feed', 'radio-station' );
	$title = apply_filters( 'radio_station_show_rss_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['rss'] ) . ';" class="dashicons dashicons-rss" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_show_rss_icon', $icon, $post_id );
	$show_icons['rss'] = '<div class="show-icon show-rss">' . $newline;
	$show_icons['rss'] .= '<a href="' . esc_url( $feed_url ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
	$show_icons['rss'] .= $icon . $newline;
	$show_icons['rss'] .= '</a>' . $newline;
	$show_icons['rss'] .= '</div>' . $newline;
}

// --- filter show icons ---
$show_icons = apply_filters( 'radio_station_show_page_icons', $show_icons, $post_id );

// --- set show related defaults ---
$show_latest = $show_posts = $show_playlists = $show_episodes = false;

// --- check for latest show blog posts ---
// $latest_limit = radio_station_get_setting( 'show_latest_posts' );
// $latest_limit = false;
// $latest_limit = apply_filters( 'radio_station_show_page_latest_limit', $latest_limit, $post_id );
// if ( absint( $latest_limit ) > 0 ) {
//	$show_latest = radio_station_get_show_posts( $post_id, array( 'limit' => $latest_limit ) );
// }

// --- check for show blog posts ---
$posts_per_page = radio_station_get_setting( 'show_posts_per_page' );
if ( absint( $posts_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_show_page_posts_limit', false, $post_id );
	$show_posts = radio_station_get_show_posts( $post_id, array( 'limit' => $limit ) );
}

// --- check for show playlists ---
$playlists_per_page = radio_station_get_setting( 'show_playlists_per_page' );
if ( absint( $playlists_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_show_page_playlist_limit', false, $post_id );
	$show_playlists = radio_station_get_show_playlists( $post_id, array( 'limit' => $limit ) );
}

// --- check for show episodes ---
$episodes_per_page = radio_station_get_setting( 'show_episodes_per_page' );
$show_episodes = apply_filters( 'radio_station_show_page_episodes', false, $post_id );

// --- get layout display settings ----
$block_position = radio_station_get_setting( 'show_block_position' );
$section_layout = radio_station_get_setting( 'show_section_layout' );
$jump_links = apply_filters( 'radio_station_show_jump_links', 'yes', $post_id );


// --------------------------
// === Set Blocks Content ===
// --------------------------

// --- set empty blocks ---
$blocks = array( 'show_images' => '', 'show_meta' => '', 'show_schedule' => '' );

// -----------------
// Show Images Block
// -----------------
if ( ( $avatar_id || $thumbnail_id ) || ( count( $show_icons ) > 0 ) || ( $show_file ) ) {

	$image_blocks = array();

	// --- Show Avatar ---
	if ( $avatar_id || $thumbnail_id ) {
		// --- get show avatar (with thumbnail fallback) ---
		$size = apply_filters( 'radio_station_show_avatar_size', 'medium', $post_id, 'show-page' );
		$attr = array( 'class' => 'show-image' );
		if ( $show_title ) {
			$attr['alt'] = $attr['title'] = $show_title;
		}
		$show_avatar = radio_station_get_show_avatar( $post_id, $size, $attr );
		if ( $show_avatar ) {
			if ( $header_id ) {
				$class = ' has-header-image';
			} else {
				$class = '';
			}
			$blocks['show_images'] = '<div class="show-avatar' . esc_attr( $class ) . '">';
			$blocks['show_images'] .= $show_avatar;
			$blocks['show_images'] .= '</div>';
		}
	}

	// --- Show controls ---
	// 2.3.3.6: modify check to include social icons or patreon only
	if ( ( count( $show_icons ) > 0 ) || $social_icons || $show_patreon || $show_file ) {

		// --- Show Icons ---
		if ( count( $show_icons ) > 0 ) {
			$image_blocks['icons'] = '<div class="show-icons">';
			$image_blocks['icons'] .= implode( "\n", $show_icons );
			$image_blocks['icons'] .= '</div>';
		}

		// --- Social Icons ---
		// 2.3.3.6: added filter for social icon display output
		if ( $social_icons ) {
			$social_icons = apply_filters( 'radio_station_show_social_icons_display', '' );
			if ( '' != $social_icons ) {
				$image_blocks['social'] = '<div class="social-icons">';
				$image_blocks['social'] .= $social_icons;
				$image_blocks['social'] .= '</div>';
			}
		}

		// --- Show Patreon Button ---
		$patreon_button = '';
		if ( $show_patreon ) {
			$patreon_button .= '<div class="show-patreon">';
			$title = __( 'Become a Supporter for', 'radio-station' ) . ' ' . $show_title;
			$title = apply_filters( 'radio_station_show_patreon_title', $title, $post_id );
			$patreon_button .= radio_station_patreon_button( $show_patreon, $title );
			$patreon_button .= '</div>';
		}
		// 2.3.1: added filter for patreon button
		$patreon_button = apply_filters( 'radio_station_show_patreon_button', $patreon_button, $post_id );
		if ( '' != $patreon_button ) {
			$image_blocks['patreon'] = $patreon_button;
		}

		// --- Show Player ---
		// 2.3.0: embed latest broadcast audio player
		// 2.3.3.4: add filter for optional title above Show Player (default empty)
		// 2.3.3.4: add filter for title text on Show Download link icon
		if ( $show_file ) {

			$image_blocks['player'] = '<div class="show-player">' . $newline;
			$label = apply_filters( 'radio_station_show_player_label', '', $post_id );
			if ( $label && ( '' != $label ) ) {
				$image_blocks['player'] .= '<span class="show-player-label">' . esc_html( $label ) . '</span><br>';
			}
			$shortcode = '[audio src="' . $show_file . '" preload="metadata"]';
			$player_embed = do_shortcode( $shortcode );
			$image_blocks['player'] .= '<div class="show-embed">' . $newline;
			$image_blocks['player'] .= $player_embed . $newline;
			$image_blocks['player'] .= '</div>' . $newline;

			// --- Download Audio Icon ---
			// 2.3.2: check show download switch
			// 2.3.3.8: added aria label to link and hidden to span icon
			if ( $show_download ) {
				$title = __( 'Download Latest Broadcast', 'radio-station' );
				$title = apply_filters( 'radio_station_show_download_title', $title, $post_id );
				$image_blocks['player'] .= '<div class="show-download">' . $newline;
				$image_blocks['player'] .= '<a href="' . esc_url( $show_file ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
				$image_blocks['player'] .= '<span style="color:#7DBB00;" class="dashicons dashicons-download" aria-hidden="true"></span>' . $newline;
				$image_blocks['player'] .= '</a>' . $newline;
				$image_blocks['player'] .= '</div>' . $newline;
			}

			$image_blocks['player'] .= '</div>' . $newline;
		}

		// 2.3.3.6: allow subblock order to be changed
		$image_blocks = apply_filters( 'radio_station_show_images_blocks', $image_blocks, $post_id );
		$image_block_order = array( 'icons', 'social', 'patreon', 'player' );
		$image_block_order = apply_filters( 'radio_station_show_image_block_order', $image_block_order, $post_id );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Image Block Order: ' . print_r( $image_block_order, true ) . '</span>';
			echo '<!-- Image Blocks: ' . print_r( $image_blocks, true ) . ' -->';
		}

		// --- combine image blocks to show images block ---
		if ( is_array( $image_blocks ) && ( count( $image_blocks ) > 0 )
		  && is_array( $image_block_order ) && ( count( $image_block_order ) > 0 ) ) {
			$blocks['show_images'] .= '<div class="show-controls">';
			foreach ( $image_block_order as $image_block ) {
				if ( isset( $image_blocks[$image_block] ) ) {
					$blocks['show_images'] .= $image_blocks[$image_block];
				}
			}
			$blocks['show_images'] .= '</div>' . $newline;
		}

	}
}

// ---------------
// Show Meta Block
// ---------------
// 2.3.3.6: added Show phone display section
if ( $show_phone || $hosts || $producers || $genres || $languages ) {

	$meta_blocks = array();

	// --- show meta title ---
	// 2.3.3.4: added filter for show info label
	// 2.3.3.4: added class to show info label tag
	$label = __( 'Show Info', 'radio-station' );
	$label = apply_filters( 'radio_station_show_info_label', $label, $post_id );
	$blocks['show_meta'] = '<h4 class="show-info-label">' . esc_html( $label ) . '</h4>' . $newline;

	// --- Show DJs / Hosts ---
	// 2.3.3.4: added filter for hosted by label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $hosts ) {
		$meta_blocks['hosts'] = '<div class="show-djs show-hosts">' . $newline;
		$label = __( 'Hosted by', 'radio-station' );
		$label = apply_filters( 'radio_station_show_hosts_label', $label, $post_id );
		$meta_blocks['hosts'] .= '<span class="show-hosts-label">' . esc_html( $label ) . '</span>: ' . $newline;
		$count = 0;
		$host_count = count( $hosts );
		foreach ( $hosts as $host ) {
			$count ++;
			$user_info = get_userdata( $host );

			// --- DJ / Host URL and/or display ---
			$host_url = radio_station_get_host_url( $host );
			if ( $host_url ) {
				$meta_blocks['hosts'] .= '<a href="' . esc_url( $host_url ) . '">';
			}
			$meta_blocks['hosts'] .= esc_html( $user_info->display_name );
			if ( $host_url ) {
				$meta_blocks['hosts'] .= '</a>' . $newline;
			}

			if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
			     || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
				$meta_blocks['hosts'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $hosts ) > 2 ) && ( $count < count( $hosts ) ) ) {
				$meta_blocks['hosts'] .= ', ';
			}
		}
		$meta_blocks['hosts'] .= '</div>' . $newline;
	}

	// --- Show Producers ---
	// 2.3.0: added assigned producer display
	// 2.3.3.4: added filter for produced by label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $producers ) {
		$meta_blocks['producers'] = '<div class="show-producers">' . $newline;
		$label = __( 'Produced by', 'radio-station' );
		$label = apply_filters( 'radio_station_show_producers_label', $label, $post_id );
		$meta_blocks['producers'] .= '<span class="show-producers-label">' . esc_html( $label ) . '</span>: ' . $newline;
		$count = 0;
		$producer_count = count( $producers );
		foreach ( $producers as $producer ) {
			$count ++;
			$user_info = get_userdata( $producer );

			// --- Producer URL / display ---
			$producer_url = radio_station_get_producer_url( $producer );
			if ( $producer_url ) {
				$meta_blocks['producers'] .= '<a href="' . esc_url( $producer_url ) . '">';
			}
			$meta_blocks['producers'] .= esc_html( $user_info->display_name );
			if ( $producer_url ) {
				$meta_blocks['producers'] .= '</a>' . $newline;
			}

			if ( ( ( 1 === $count ) && ( 2 === $producer_count ) )
			     || ( ( $producer_count > 2 ) && ( ( $producer_count - 1 ) === $count ) ) ) {
				$meta_blocks['producers'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $producers ) > 2 ) && ( $count < count( $producers ) ) ) {
				$meta_blocks['producers'] .= ', ';
			}
		}
		$meta_blocks['producers'] .= '</div>' . $newline;
	}

	// --- Show Genre(s) ---
	// 2.3.0: only display if genre assigned
	// 2.3.3.4: added filter for genres label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $genres ) {
		$tax_object = get_taxonomy( RADIO_STATION_GENRES_SLUG );
		if ( count( $genres ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}
		$label = apply_filters( 'radio_station_show_genres_label', $label, $post_id );
		$meta_blocks['genres'] = '<div class="show-genres">' . $newline;
		$meta_blocks['genres'] .= '<span class="show-genres-label">' . esc_html( $label ) . '</span>: ' . $newline;
		$genre_links = array();
		foreach ( $genres as $genre ) {
			$genre_link = get_term_link( $genre );
			$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>' . $newline;
		}
		$meta_blocks['genres'] .= implode( ', ', $genre_links ) . $newline;
		$meta_blocks['genres'] .= '</div>' . $newline;
	}

	// --- Show Language(s) ---
	// 2.3.0: only display if language is assigned
	// 2.3.3.4: added filter for languages label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $languages ) {
		$tax_object = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
		if ( count( $languages ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}
		$label = apply_filters( 'radio_station_show_languages_label', $label, $post_id );
		$meta_blocks['languages'] = '<div class="show-languages">' . $newline;
		$meta_blocks['languages'] .= '<span class="show-languages-label">' . esc_html( $label ) . '</span>: ' . $newline;
		$language_links = array();
		foreach ( $languages as $language ) {
			$lang_label = $language->name;
			if ( !empty( $language->description ) ) {
				$lang_label .= ' (' . $language->description . ')';
			}
			$language_link = get_term_link( $language );
			$language_links[] = '<a href="' . esc_url( $language_link ) . '">' . esc_html( $lang_label ) . '</a>' . $newline;
		}
		$meta_blocks['languages'] .= implode( ', ', $language_links ) . $newline;
		$meta_blocks['languages'] .= '</div>' . $newline;
	}

	// --- Show Phone ---
	if ( $show_phone ) {
		$meta_blocks['phone'] = '<div class="show-phone">';
		$label = __( 'Call in', 'radio-station' );
		$label = apply_filters( 'radio_station_show_phone_label', $label, $post_id );
		$meta_blocks['phone'] .= '<span class="show-phone-label"><b>' . esc_html( $label ) . '</b></span>: ' . $newline;
		$meta_blocks['phone'] .= '<a href="tel:' . esc_attr( $show_phone ) . '">' . esc_html( $show_phone ) . '</a>';
		$meta_blocks['phone'] .= '</div>';
	}

	// --- filter meta blocks and order ---
	// 2.3.3.6: allow subblock order to be changed
	$meta_blocks = apply_filters( 'radio_station_show_meta_blocks', $meta_blocks, $post_id );
	$meta_block_order = array( 'hosts', 'producers', 'genres', 'languages', 'phone' );
	$meta_block_order = apply_filters( 'radio_station_show_meta_block_order', $meta_block_order, $post_id );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Meta Block Order: ' . print_r( $meta_block_order, true ) . '</span>';
		echo '<!-- Meta Blocks: ' . print_r( $meta_blocks, true ) . ' -->';
	}

	// --- combine meta blocks to show meta block ---
	if ( is_array( $meta_blocks ) && ( count( $meta_blocks ) > 0 )
	  && is_array( $meta_block_order ) && ( count( $meta_block_order ) > 0 ) ) {
		foreach ( $meta_block_order as $meta_block ) {
			if ( isset( $meta_blocks[$meta_block] ) ) {
				$blocks['show_meta'] .= $meta_blocks[$meta_block];
			}
		}
	}
}

// ----------------
// Show Times Block
// ----------------

// --- check to remove incomplete and disabled shifts ---
if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
	foreach ( $shifts as $i => $shift ) {
		$shift = radio_station_validate_shift( $shift );
		if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
			unset( $shifts[$i] );
		}
	}
	if ( count( $shifts ) == 0 ) {
		$shifts = false;
	}
}

// --- show times title ---
$label = __( 'Show Times', 'radio-station' );
$label = apply_filters( 'radio_station_show_times_label', $label, $post_id );
$blocks['show_times'] = '<h4>' . esc_html( $label ) . '</h4>' . $newline;

// --- check if show is active and has shifts ---
if ( !$active || !$shifts ) {

	$label = __( 'Not Currently Scheduled.', 'radio-station' );
	$label = apply_filters( 'radio_station_show_no_shifts_label', $label, $post_id );
	$blocks['show_times'] .= esc_html( $label );

} else {

	// --- get timezone and offset ---
	// 2.3.2: use get timezone function
	$timezone = radio_station_get_timezone();
	if ( strstr( $timezone, 'UTC' ) ) {
		$offset = str_replace( 'UTC', '', $timezone );
	} else {
		$timezone_code = radio_station_get_timezone_code( $timezone );
		$datetimezone = new DateTimeZone( $timezone );
		$offset = $datetimezone->getOffset( new DateTime() );
		$offset = round( $offset / 60 / 60 );

		if ( strstr( (string) $offset, '.' ) ) {
			if ( substr( $offset, - 2, 2 ) == '.5' ) {
				$offset = str_replace( '.5', ':30', $offset );
			} elseif ( substr( $offset, - 3, 3 ) == '.75' ) {
				$offset = str_replace( '.75', ':45', $offset );
			} elseif ( substr( $offset, - 3, 3 ) == '.25' ) {
				$offset = str_replace( '.25', ':15', $offset );
			}
		}
	}

	if ( ( 0 == $offset ) || ( '' == $offset ) ) {
		$utc_offset = '';
	} elseif ( $offset > 0 ) {
		$utc_offset = '+' . $offset;
	} else {
		$utc_offset = $offset;
	}

	// --- display timezone ---
	// 2.3.3.4: added filter for timezone label
	$label = __( 'Timezone', 'radio-station' );
	$label = apply_filters( 'radio_station_show_timezone_label', $label, $post_id );
	$blocks['show_times'] .= '<span class="show-timezone-label">' . esc_html( $label ) . '</span>: ' . $newline;
	if ( !isset( $timezone_code ) ) {
		$blocks['show_times'] .= esc_html( __( 'UTC', 'radio-station' ) ) . $utc_offset;
	} else {
		$blocks['show_times'] .= esc_html( $timezone_code );
		$blocks['show_times'] .= '<span class="show-offset">';
		$blocks['show_times'] .= ' [' . esc_html( __( 'UTC', 'radio-station' ) ) . $utc_offset . ']';
		$blocks['show_times'] .= '</span>' . $newline;
	}

	// TODO: --- display user timezone ---
	// $block['show_times'] .= ...

	$blocks['show_times'] .= '<table class="show-times" cellpadding="0" cellspacing="0">' . $newline;

	$found_encore = false;

	// --- get data format ---
	// 2.3.2: set filterable time formats
	if ( 24 == (int) $time_format ) {
		$start_data_format = $end_data_format = 'H:i';
	} else {
		$start_data_format = $end_data_format = 'g:i a';
	}
	$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'show-template', $post_id );
	$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'show-template', $post_id );

	$weekdays = radio_station_get_schedule_weekdays();
	$now = radio_station_get_now();
	foreach ( $weekdays as $day ) {
		$show_times = array();
		if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $shift ) {
				if ( $day == $shift['day'] ) {

					// --- convert shift info ---
					// 2.3.2: replace strtotime with to_time for timezones
					// 2.3.2: fix to convert to 24 hour format first
					$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
					$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
					$start_time = radio_station_convert_shift_time( $start );
					$end_time = radio_station_convert_shift_time( $end );
					$shift_start_time = radio_station_to_time( $start_time );
					$shift_end_time = radio_station_to_time( $end_time );
					if ( $shift_end_time < $shift_start_time ) {
						$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
					}

					// --- get shift time display ---
					$start = radio_station_get_time( $start_data_format, $shift_start_time );
					$end = radio_station_get_time( $end_data_format, $shift_end_time );
					$start = radio_station_translate_time( $start );
					$end = radio_station_translate_time( $end );

					// --- check if current shift ---
					$classes = array( 'show-shift-time' );
					if ( ( $now > $shift_start_time ) && ( $now < $shift_end_time ) ) {
						$classes[] = 'current-shift';
					}
					$class = implode( ' ', $classes );

					// --- set show time output ---
					// 2.3.4: fix to start data_format attribute
					$show_time = '<div class="' . esc_attr( $class ) . '">' . $newline;
					$show_time .= '<span class="rs-time rs-start-time" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . $newline;
					$show_time .= '<span class="rs-sep"> - </span>' . $newline;
					$show_time .= '<span class="rs-time rs-end-time" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . $newline;
					if ( isset( $shift['encore'] ) && ( 'on' == $shift['encore'] ) ) {
						$found_encore = true;
						$show_time .= '<span class="show-encore">*</span>' . $newline;
					}
					$show_time .= '</div>' . $newline;
					$show_times[] = $show_time;
				}
			}
		}
		$show_times_count = count( $show_times );
		if ( $show_times_count > 0 ) {
			$blocks['show_times'] .= '<td class="show-day-time ' . strtolower( $day ) . '">' . $newline;
			$weekday = radio_station_translate_weekday( $day, true );
			$blocks['show_times'] .= '<b>' . esc_html( $weekday ) . '</b>: ' . $newline;
			$blocks['show_times'] .= '</td><td>' . $newline;
			foreach ( $show_times as $i => $show_time ) {
				$blocks['show_times'] .= '<span class="show-time">' . $show_time . '</span>';
				// if ( $i < ( $show_times_count - 1 ) ) {
				//	$blocks['show_times'] .= '<br>';
				// }
			}
			$blocks['show_times'] .= '</td></tr>' . $newline;
		}
	}

	// --- * encore note ---
	// 2.3.3.4: added filter for encore label
	if ( $found_encore ) {
		$blocks['show_times'] .= '<tr><td></td><td>' . $newline;
		$blocks['show_times'] .= '<span class="show-encore">*</span> ' . $newline;
		$blocks['show_times'] .= '<span class="show-encore-label">';
		$label = __( 'Encore Presentation', 'radio-station' );
		$label = apply_filters( 'radio_station_show_encore_label', $label, $post_id );
		$blocks['show_times'] .= esc_html( $label );
		$blocks['show_times'] .= '</span>' . $newline;
		$blocks['show_times'] .= '</td></tr>' . $newline;
	}

	$blocks['show_times'] .= '</table>' . $newline;
}

// --- maybe add link to full schedule page ---
// 2.3.3.4: added filters for schedule link title and anchor
$schedule_page = radio_station_get_setting( 'schedule_page' );
if ( $schedule_page && !empty( $schedule_page ) ) {
	$schedule_link = get_permalink( $schedule_page );
	$blocks['show_times'] .= '<div class="show-schedule-link">' . $newline;
	$title = __( 'Go to Full Station Schedule Page', 'radio-station' ) . $newline;
	$title = apply_filters( 'radio_station_show_schedule_link_title', $title, $post_id );
	$blocks['show_times'] .= '<a href="' . esc_url( $schedule_link ) . '" title="' . esc_attr( $title ) . '">' . $newline;
	$label = __( 'Full Station Schedule', 'radio-station' );
	$label = apply_filters( 'radio_station_show_schedule_link_anchor', $label, $post_id );
	$blocks['show_times'] .= '&larr; ' . esc_html( $label ) . '</a>' . $newline;
	$blocks['show_times'] .= '</div>' . $newline;
}

// --- filter all show info blocks ---
$blocks = apply_filters( 'radio_station_show_page_blocks', $blocks, $post_id );


// -------------------------
// === Set Show Sections ===
// -------------------------
// 2.3.0: add show information sections

// --------------------
// Set Show Description
// --------------------
$show_description = false;
if ( strlen( trim( $content ) ) > 0 ) {
	$show_description = '<div class="show-desc-content">' . $content . '</div>' . $newline;
	$show_description .= '<div id="show-more-overlay"></div>' . $newline;
	$show_desc_buttons = '<div id="show-desc-buttons">' . $newline;
	$label = __( 'Show More', 'radio-station' );
	$label = apply_filters( 'radio_station_show_more_label', $label, $post_id );
	$show_desc_buttons .= '	<input type="button" id="show-desc-more" onclick="radio_show_desc(\'more\');" value="' . esc_html( $label ) . '">' . $newline;
	$label = __( 'Show Less', 'radio-station' );
	$label = apply_filters( 'radio_station_show_less_label', $label, $post_id );
	$show_desc_buttons .= '	<input type="button" id="show-desc-less" onclick="radio_show_desc(\'less\');" value="' . esc_html( $label ) . '">' . $newline;
	$show_desc_buttons .= '	<input type="hidden" id="show-desc-state" value="">' . $newline;
	$show_desc_buttons .= '</div>' . $newline;
}

// -------------
// Show Sections
// -------------
$sections = array();
if ( ( strlen( trim( $content ) ) > 0 ) || $show_posts || $show_playlists || $show_episodes ) {

	// --- About Show Tab (Post Content) ---
	// 2.3.3.4: added filter for show description label and anchor
	$i = 0;
	if ( $show_description ) {

		$sections['about']['heading'] = '<a name="show-description"></a>' . $newline;
		$label = __( 'About the Show', 'radio-station' );
		$label = apply_filters( 'radio_station_show_description_label', $label, $post_id );
		$sections['about']['heading'] .= '<h3 id="show-section-about">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = __( 'About', 'radio-station' );
		$anchor = apply_filters( 'radio_station_show_description_anchor', $anchor, $post_id );
		$sections['about']['anchor'] = $anchor;

		$sections['about']['content'] = '<div id="show-about" class="show-tab tab-active"><br>' . $newline;
		$sections['about']['content'] .= '<div id="show-description" class="show-description">' . $newline;
		$sections['about']['content'] .= $show_description;
		$sections['about']['content'] .= '</div>' . $newline;
		$sections['about']['content'] .= $show_desc_buttons;
		$sections['about']['content'] .= '</div>' . $newline;
		$i ++;
	}

	// --- Show Episodes Tab ---
	// 2.3.3.4: added filter for show epiodes label and anchor
	if ( $show_episodes ) {

		$sections['episodes']['heading'] = '<a name="show-episodes"></a>' . $newline;
		$label = __( 'Show Episodes', 'radio-station' );
		$label = apply_filters( 'radio_station_show_episodes_label', $label, $post_id );
		$sections['episodes']['heading'] .= '<h3 id="show-section-episodes">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = __( 'Episodes', 'radio-station' );
		$anchor = apply_filters( 'radio_station_show_episodes_anchor', $anchor, $post_id );
		$sections['episodes']['anchor'] = $anchor;

		$sections['episodes']['content'] = '<div id="show-episodes" class="show-section-content"><br>' . $newline;
		$radio_station_data['show-episodes'] = $show_posts;
		$shortcode = '[show-episodes-archive per_page="' . $episodes_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_episodes_shortcode', $shortcode, $post_id );
		$sections['episodes']['content'] .= do_shortcode( $shortcode );
		$sections['episodes']['content'] .= '</div>' . $newline;
		$i ++;
	}

	// --- Show Blog Posts Tab ---
	// 2.3.3.4: added filter for show posts label and anchor
	if ( $show_posts ) {

		$sections['posts']['heading'] = '<a name="show-posts"></a>' . $newline;
		$label = __( 'Show Posts', 'radio-station' );
		$label = apply_filters( 'radio_station_show_posts_label', $label, $post_id );
		$sections['posts']['heading'] .= '<h3 id="show-section-posts">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = __( 'Posts', 'radio-station' );
		$anchor = apply_filters( 'radio_station_show_posts_anchor', $anchor, $post_id );
		$sections['posts']['anchor'] = $anchor;

		$sections['posts']['content'] = '<div id="show-posts" class="show-section-content"><br>' . $newline;
		$radio_station_data['show-posts'] = $show_posts;
		$shortcode = '[show-posts-archive per_page="' . $posts_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_posts_shortcode', $shortcode, $post_id );
		$sections['posts']['content'] .= do_shortcode( $shortcode );
		$sections['posts']['content'] .= '</div>' . $newline;
		$i ++;
	}

	// --- Show Playlists Tab ---
	// 2.3.3.4: added filter for show playlists label and anchor
	if ( $show_playlists ) {

		$sections['playlists']['heading'] = '<a name="show-playlists">';
		$label = __( 'Show Playlists', 'radio-station' );
		$label = apply_filters( 'radio_station-show_playlists_label', $label, $post_id );
		$sections['playlists']['heading'] .= '<h3 id="show-section-playlists">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = __( 'Playlists', 'radio-station' );
		$anchor = apply_filters( 'radio_station_show_playlists_anchor', $anchor, $post_id );
		$sections['playlists']['anchor'] = $anchor;

		$sections['playlists']['content'] = '<div id="show-playlists" class="show-section-content"><br>' . $newline;
		$radio_station_data['show-playlists'] = $show_playlists;
		$shortcode = '[show-playlists-archive per_page="' . $playlists_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_playlists_shortcode', $shortcode, $post_id );
		$sections['playlists']['content'] .= do_shortcode( $shortcode );
		$sections['playlists']['content'] .= '</div>' . $newline;
		$i ++;
	}
}
// 2.3.3.8: remove duplicate post_id filter argument
$sections = apply_filters( 'radio_station_show_page_sections', $sections, $post_id );


// -----------------------
// === Template Output ===
// -----------------------

// --- set content classes ---
$classes = array();
if ( 'right' == $block_position ) {
	$classes[] = 'right-blocks';
} elseif ( 'top' == $block_position ) {
	$classes[] = 'top-blocks';
} else {
	$classes[] = 'left-blocks';
}
$class = implode( ' ', $classes );

?>
	<!-- #show-content -->
	<div id="show-content" class="<?php echo esc_attr( $class ); ?>">

		<?php

		// --- Show Header ---
		// 2.3.0: added new optional show header display
		$header = radio_station_get_setting( 'show_header_image' );
		if ( $header && $header_id ) {
			$size = apply_filters( 'radio_station_show_header_size', 'full', $post_id );
			$header_src = wp_get_attachment_image_src( $header_id, $size );
			$header_url = $header_src[0];
			$header_width = $header_src[1];
			$header_height = $header_src[2];
			$header_image = '<div class="show-header">';
			$header_image .= '<img class="show-image" src="' . esc_url( $header_url ) . '" width="' . esc_attr( $header_width ) . '" height="' . esc_attr( $header_height ) . '">';
			$header_image .= '</div><br>';
			$header_image = apply_filters( 'radio_station_show_page_header_image', $header_image, $post_id );
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $header_image;
		}

		// --- Show Info Blocks ---
		// 2.3.3.4: add show-info element ID to div tag
		?>

	    <div id="show-info" class="show-info">

			<?php

			// --- filter block order ---
			$block_order = array( 'show_images', 'show_meta', 'show_times' );
			$block_order = apply_filters( 'radio_station_show_page_block_order', $block_order, $post_id );

			// --- loop blocks ---
			if ( is_array( $block_order ) && ( count( $block_order ) > 0 ) ) {
				foreach ( $block_order as $i => $block ) {
					if ( isset( $blocks[$block] ) && ( '' != trim( $blocks[$block] ) ) ) {

						// --- set block classes ---
						$classes = array( 'show-block' );
						$classes[] = str_replace( '_', '-', $block );
						if ( 0 == $i ) {
							$classes[] = 'first-block';
						} elseif ( count( $block_order ) == ( $i + 1 ) ) {
							$classes[] = 'last-block';
						}
						$class = implode( ' ', $classes );

						// --- output blocks ---
						echo '<div class="' . esc_attr( $class ) . '">';
						// phpcs:ignore WordPress.Security.OutputNotEscaped
						echo $blocks[$block];
						echo '</div>';

						$first = '';
					}
				}
			}
		?>

		</div>

		<div class="show-sections">

			<?php

			// --- Display Latest Show Posts ---
			if ( $show_latest ) {
			 	$label = __( 'Latest Show Posts', 'radio-station' );
			 	$label = apply_filters( 'radio_station_show_latest_posts_label', $label, $post_id );
				?>

				<div id="show-latest">
					<div class="show-latest-title">
						<span class="show-latest-label"><?php echo esc_html( $label ); ?></span>
					</div>
					<?php
						$radio_station_data['show-latests'] = $show_latest;
						$shortcode = '[show-latest-archive thumbnails="0" pagination="0" content="none"]';
						$shortcode = apply_filters( 'radio_station_show_page_latest_shortcode', $shortcode, $post_id );
						echo do_shortcode( $shortcode );
					?>
				</div>

				<?php
			}

			// --- filter section order ---
			$section_order = array( 'about', 'episodes', 'posts', 'playlists', 'hosts', 'producers' );
			$section_order = apply_filters( 'radio_station_show_page_section_order', $section_order, $post_id );

			// --- Display Show Sections ---
			// 2.3.0: filter show sections for display
			if ( ( is_array( $sections ) && ( count( $sections ) > 0 ) )
			     && is_array( $section_order ) && ( count( $section_order ) > 0 ) ) {

				// --- tabs for tabbed layout ---
				if ( 'tabbed' == $section_layout ) {

					// --- output first section as non-tabbed ---
					if ( isset( $sections[$section_order[0]] ) ) {
						// phpcs:ignore WordPress.Security.OutputNotEscaped
						echo $sections[$section_order[0]]['heading'];
						echo $sections[$section_order[0]]['content'];
					}
					unset( $section_order[0] );

					?>

					<div class="show-tabs">
						<?php
                        $i = 0;
						foreach ( $section_order as $section ) {
							if ( isset( $sections[$section] ) ) {
								if ( 0 == $i ) {
									$class = "tab-active";
								} else {
									$class = "tab-inactive";
								}
								echo '<div id="show-' . esc_attr( $section ) . '-tab" class="show-tab ' . esc_attr( $class ) . '" onclick="radio_show_tab(\'' . esc_attr( $section ) . '\');">';
								echo esc_html( $sections[$section]['anchor'] );
								echo '</div>';
								if ( ( $i + 1 ) < count( $sections ) ) {
									echo '<div class="show-tab-spacer">&nbsp;</div>';
								}
								$i++;
							}
						}
						?>
                    </div>
				<?php } ?>

                <div class="show-section">
                    <?php
					$i = 0;
					foreach ( $section_order as $section ) {
						if ( isset( $sections[$section] ) ) {

							if ( 'tabbed' != $section_layout ) {

								// --- section heading ---
								// phpcs:ignore WordPress.Security.OutputNotEscaped
								echo $sections[$section]['heading'];

								// --- section jump links ---
								if ( 'yes' == $jump_links ) {
									echo '<div class="show-jump-links">';
									echo '<b>' . esc_html( __( 'Jump to', 'radio-station' ) ) . '</b>: ';
									$found_link = false;
									foreach ( $section_order as $link ) {
										if ( isset( $sections[$link] ) && ( $link != $section ) ) {
											if ( $found_link ) {
												echo ' | ';
											}
											echo '<a href="javascript:void(0);" onclick="radio_scroll_link(\'' . esc_attr( $link ) . '\');">';
											echo esc_html( $sections[$link]['anchor'] );
											echo '</a>';
											$found_link = true;
										}
									}
									echo '</div>';
								}

							} else {

								// --- add tab classes to section ---
								$classes = array( 'show-tab' );
								if ( 0 == $i ) {
									$classes[] = 'tab-active';
								} else {
									$classes[] = 'tab-inactive';
								}
								$class = implode( ' ', $classes );
								$sections[$section]['content'] = str_replace( 'class="show-section-content"', 'class="' . esc_attr( $class ) . '"', $sections[$section]['content'] );

							}

							// --- section content ---
							// phpcs:ignore WordPress.Security.OutputNotEscaped
							echo $sections[$section]['content'];

							$i ++;
						}
					}
					?>

                </div>

			<?php } ?>

        </div>

    </div>
    <!-- /#show-content -->

<?php

// --- enqueue show page script ---
// 2.3.0: enqueue script instead of echoing
radio_station_enqueue_script( 'radio-station-show-page', array( 'radio-station' ), true );

// --- maybe detect and switch to # tab ---
if ( 'tabbed' == 'section_layout' ) {
	$js = "setTimeout(function() {
		if (window.location.hash) {
			hash = window.location.hash.substring(1);
			if (hash.indexOf('show-') > -1) {
				tab = hash.replace('show-', '');
				radio_show_tab('about');}
			}
		}
	}, 500);";
	wp_add_inline_script( 'radio-station-show-page', $js );
}
