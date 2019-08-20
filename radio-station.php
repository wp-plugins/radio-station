<?php
/**
 * @package Radio Station
 * @version 2.2.4
 */
/*
Plugin Name: Radio Station
Plugin URI: https://netmix.com/radio-station
Description: Adds Show pages, DJ role, playlist and on-air programming functionality to your site.
Author: Tony Zeoli <tonyzeoli@netmix.com>
Version: 2.2.4
Text Domain: radio-station
Domain Path: /languages
Author URI: https://netmix.com/radio-station
GitHub Plugin URI: netmix/radio-station

Copyright 2019 Digital Strategy Works  (email : info@digitalstrategyworks.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// === Setup ===
// - Include Necessary Files
// - Load Text Domain
// - Enqueue Stylesheets
// - Enqueue Admin Scripts
// - Enqueue Admin Styles
// === Template Filters ===
// - Single Show/Playlist Template
// - Playlist Archive Template
// === Roles ===
// - Add DJ Role and Capabilities
// - maybe Revoke Edit Show Capability
// === Menus ===
// - Add Menus
// - maybe Remove Add Show Bar Link
// - Output Help Page
// - Output Export Page

// -------------
// === Setup ===
// -------------

// --- include necessary files ---
require 'includes/post-types.php';
require 'includes/master-schedule.php';
require 'includes/shortcodes.php';
require 'includes/class-dj-upcoming-widget.php';
require 'includes/class-dj-widget.php';
require 'includes/class-playlist-widget.php';
require 'includes/support-functions.php';

// --- load the text domain ---
function radio_station_init() {
	load_plugin_textdomain( 'radio-station', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'radio_station_init' );

// --- flush rewrite rules on activate / deactivation ---
// 2.2.3: added this for custom post types rewrite flushing
register_activation_hook( __FILE__, 'radio_station_flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
function radio_station_flush_rewrite_flag() {
	add_option( 'radio_station_flush_rewrite_rules', true );
}

// --- enqueue necessary stylesheets ---
function radio_station_load_styles() {

	$program_css = get_stylesheet_directory() . '/program-schedule.css';
	if ( file_exists( $program_css ) ) {
		$version = filemtime( $program_css );
		$url     = get_stylesheet_directory_uri() . '/program-schedule.css';
	} else {
		$version = filemtime( dirname( __FILE__ ) . '/css/program-schedule.css' );
		$url     = plugins_url( 'css/program-schedule.css', __FILE__ );
	}
	wp_enqueue_style( 'program-schedule', $url, array(), $version );

	// note: djonair.css style enqueueing moved to /includes/widget_djonair.php
}
add_action( 'wp_enqueue_scripts', 'radio_station_load_styles' );

// --- enqueue admin scripts ---
// jQuery is needed by the output of this code, so let us make sure we have it available
function radio_station_master_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	// $url = plugins_url( 'css/jquery-ui.css', dirname(__FILE__).'/radio-station.php' );
	// wp_enqueue_style( 'jquery-style', $url, array(), '1.8.2' );
	if ( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';}
	$url = $protocol . '://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css';
	wp_enqueue_style( 'jquery-ui-style', $url, array(), '1.8.2' );
}
add_action( 'wp_enqueue_scripts', 'radio_station_master_scripts' );
add_action( 'admin_enqueue_scripts', 'radio_station_master_scripts' );

// --- add some style rules to certain parts of the admin area ---
function radio_station_load_admin_styles() {
	global $post;

	// hide the first submenu item to prevent duplicate of main menu item
	$styles = ' #toplevel_page_radio-station .wp-first-item { display: none; }' . "\n";

	if ( isset( $post->post_type ) && ( 'playlist' === $post->post_type ) ) {
		$styles .= ' .wp-editor-container textarea.wp-editor-area { height: 100px; }' . "\n";
	}

	echo '<style type="text/css">' . $styles . '</style>';

}
add_action( 'admin_print_styles', 'radio_station_load_admin_styles' );


// ------------------------
// === Template Filters ===
// ------------------------

// --- load the theme file for the playlist and show post types ---
function radio_station_load_template( $single_template ) {
	global $post;

	if ( 'playlist' === $post->post_type ) {
		// first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory() . '/single-playlist.php';
		if ( ! file_exists( $user_theme ) ) {
			$single_template = dirname( __FILE__ ) . '/templates/single-playlist.php';
		}
	}

	if ( 'show' === $post->post_type ) {
		// first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory() . '/single-show.php';
		if ( ! file_exists( $user_theme ) ) {
			$single_template = dirname( __FILE__ ) . '/templates/single-show.php';
		}
	}

	return $single_template;
}
add_filter( 'single_template', 'radio_station_load_template' );

// --- load the theme file for the playlist archive pages ---
function radio_station_load_custom_post_type_template( $archive_template ) {
	global $post;

	if ( is_post_type_archive( 'playlist' ) ) {
		$playlist_archive_theme = get_stylesheet_directory() . '/archive-playlist.php';
		if ( ! file_exists( $playlist_archive_theme ) ) {
			$archive_template = dirname( __FILE__ ) . '/templates/archive-playlist.php';
		}
	}

	return $archive_template;
}
add_filter( 'archive_template', 'radio_station_load_custom_post_type_template' );



// -------------
// === Roles ===
// -------------

// --- set up the DJ role and user capabilities ---
function radio_station_set_roles() {

	global $wp_roles;

	// set only the necessary capabilities for DJs
	$caps = array(
		'edit_shows'               => true,
		'edit_published_shows'     => true,
		'edit_others_shows'        => true,
		'read_shows'               => true,
		'edit_playlists'           => true,
		'edit_published_playlists' => true,
		// 'edit_others_playlists'	=> true,  // uncomment to allow DJs to edit all playlists
		'read_playlists'           => true,
		'publish_playlists'        => true,
		'read'                     => true,
		'upload_files'             => true,
		'edit_posts'               => true,
		'edit_published_posts'     => true,
		'publish_posts'            => true,
		'delete_posts'             => true,
	);
	// $wp_roles->remove_role('dj'); // we need this here in case we ever update the capabilities list
	// TODO: translate role name ?
	$wp_roles->add_role( 'dj', 'DJ', $caps );

	// grant all new capabilities to admin users
	$wp_roles->add_cap( 'administrator', 'edit_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_published_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_others_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_private_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_published_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_others_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_private_shows', true );
	$wp_roles->add_cap( 'administrator', 'read_shows', true );
	$wp_roles->add_cap( 'administrator', 'publish_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_playlists', true );
	$wp_roles->add_cap( 'administrator', 'edit_published_playlists', true );
	$wp_roles->add_cap( 'administrator', 'edit_others_playlists', true );
	$wp_roles->add_cap( 'administrator', 'edit_private_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_published_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_others_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_private_playlists', true );
	$wp_roles->add_cap( 'administrator', 'read_playlists', true );
	$wp_roles->add_cap( 'administrator', 'publish_playlists', true );
}
if ( is_multisite() ) {
	add_action( 'init', 'radio_station_set_roles', 10, 0 );
} else {
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
}

// --- revoke the ability to edit a show if the user is not listed as a DJ on that show ---
function radio_station_revoke_show_edit_cap( $allcaps, $cap = 'edit_shows', $args ) {

	global $post, $wp_roles;

	$user = wp_get_current_user();

	// determine which roles should have full access aside from administrator
	$add_roles = array( 'administrator' );
	if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
		foreach ( $wp_roles->roles as $name => $role ) {
			foreach ( $role['capabilities'] as $capname => $capstatus ) {
				if ( 'publish_shows' === $capname && (bool) $capstatus ) {
					$add_roles[] = $name;
				}
			}
		}
	}

	// exclude administrators and custom roles with appropriate capabilities...
	// they should be able to do whatever they want
	$found = false;
	foreach ( $add_roles as $role ) {
		if ( in_array( $role, $user->roles, true ) ) {
			$found = true;}
	}

	if ( ! $found ) {

		// limit this to published shows
		if ( isset( $post->post_type ) ) {
			if ( is_admin() && ( 'show' === $post->post_type ) && ( 'publish' === $post->post_status ) ) {

				$djs = get_post_meta( $post->ID, 'show_user_list', true );

				if ( ! isset( $djs ) || empty( $djs ) ) {
					$djs = array();}

				// if they are not listed, temporarily revoke editing ability for this post
				if ( ! in_array( $user->ID, $djs, true ) ) {
					$allcaps['edit_shows']           = false;
					$allcaps['edit_published_shows'] = false;
				}
			}
		}
	}
	return $allcaps;
}
add_filter( 'user_has_cap', 'radio_station_revoke_show_edit_cap', 10, 3 );


// -------------
// === Menus ===
// -------------

function radio_station_add_admin_menus() {

	$icon       = plugins_url( 'images/radio-station-icon.png', __FILE__ );
	$position   = apply_filters( 'radio_station_menu_position', 5 );
	$capability = 'publish_playlists';
	add_menu_page( __( 'Radio Station', 'radio-station' ), __( 'Radio Station', 'radio-station' ), $capability, 'radio-station', 'radio_station_plugin_help', $icon, $position );

	add_submenu_page( 'radio-station', __( 'Shows', 'radio-station' ), __( 'Shows', 'radio-station' ), 'edit_shows', 'shows' );
	add_submenu_page( 'radio-station', __( 'Add Show', 'radio-station' ), __( 'Add Show', 'radio-station' ), 'publish_shows', 'add-show' );
	add_submenu_page( 'radio-station', __( 'Playlists', 'radio-station' ), __( 'Playlists', 'radio-station' ), 'edit_playlists', 'playlists' );
	add_submenu_page( 'radio-station', __( 'Add Playlist', 'radio-station' ), __( 'Add Playlist', 'radio-station' ), 'publish_playlists', 'add-playlist' );
	add_submenu_page( 'radio-station', __( 'Genres', 'radio-station' ), __( 'Genres', 'radio-station' ), 'publish_playlists', 'genres' );
	add_submenu_page( 'radio-station', __( 'Schedule Overrides', 'radio-station' ), __( 'Schedule Overrides', 'radio-station' ), 'edit_shows', 'schedule-overrides' );
	add_submenu_page( 'radio-station', __( 'Add Override', 'radio-station' ), __( 'Add Override', 'radio-station' ), 'publish_shows', 'add-override' );
	add_submenu_page( 'radio-station', __( 'Export Playlists', 'radio-station' ), __( 'Export Playlists', 'radio-station' ), 'manage_options', 'playlist-export', 'radio_station_admin_export' );
	add_submenu_page( 'radio-station', __( 'Help', 'radio-station' ), __( 'Help', 'radio-station' ), 'publish_playlists', 'radio-station-help', 'radio_station_plugin_help' );

	// hack the submenu global to post type add/edit URLs
	global $submenu;
	foreach ( $submenu as $i => $menu ) {
		if ( 'radio-station' === $i ) {
			foreach ( $menu as $j => $item ) {
				switch ( $item[2] ) {
					case 'add-show':
						// maybe remove the Add Show link for DJs
						// $user = wp_get_current_user();
						// if ( in_array( 'dj', $user->roles ) ) {
						if ( ! current_user_can( 'publish_shows' ) ) {
							unset( $submenu[ $i ][ $j ] );
						} else {
							$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=show';
						}
						break;
					case 'shows':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=show';
						break;
					case 'playlists':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=playlist';
						break;
					case 'add-playlist':
						$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=playlist';
						break;
					case 'genres':
						$submenu[ $i ][ $j ][2] = 'edit-tags.php?taxonomy=genres';
						break;
					case 'schedule-overrides':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=override';
						break;
					case 'add-override':
						$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=override';
						break;
				}
			}
		}
	}
}
add_action( 'admin_menu', 'radio_station_add_admin_menus' );

// --- expand main menu fix for plugin submenu items ---
// 2.2.2: added fix for genre taxonomy page and post type editing
function radio_station_fix_genre_parent( $parent_file = '' ) {
	global $pagenow, $post;
	$post_types = array( 'show', 'playlist', 'override' );
	if ( ( 'edit-tags.php' === $pagenow ) && isset( $_GET['taxonomy'] ) && 'genres' === $_GET['taxonomy'] ) {
		$parent_file = 'radio-station';
	} elseif ( 'post.php' === $pagenow && in_array( $post->post_type, $post_types, true ) ) {
		$parent_file = 'radio-station';
	}
	return $parent_file;
}
add_filter( 'parent_file', 'radio_station_fix_genre_parent', 11 );

// --- genre taxonomy submenu item fix ---
// 2.2.2: so genre submenu item link is set to current (bold)
function radio_station_genre_submenu_fix() {
	global $pagenow;
	if ( 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'genres' === $_GET['taxonomy'] ) {
		echo "<script>
	jQuery('#toplevel_page_radio-station ul li').each(function() {
		if (jQuery(this).find('a').attr('href') == 'edit-tags.php?taxonomy=genres') {
			jQuery(this).addClass('current').find('a').addClass('current').attr('aria-current', 'page');
		}
	});</script>";
	}
}
add_action( 'admin_footer', 'radio_station_genre_submenu_fix' );

// --- remove the Add Show link for DJs from the wp admin bar ---
// 2.2.2: re-add new post type items to admin bar
// (as no longer automatically added by register_post_type)
function station_radio_modify_admin_bar_menu( $wp_admin_bar ) {

	// --- new show ---
	if ( current_user_can( 'publish_shows' ) ) {

		$args = array(
			'id'     => 'new-show',
			'title'  => __( 'Show', 'radio-station' ),
			'parent' => 'new-content',
			'href'   => admin_url( 'post-new.php?post_type=show' ),
		);
		$wp_admin_bar->add_node( $args );
	}

	// --- new playlist ---
	if ( current_user_can( 'publish_playlists' ) ) {
		$args = array(
			'id'     => 'new-playlist',
			'title'  => __( 'Playlist', 'radio-station' ),
			'parent' => 'new-content',
			'href'   => admin_url( 'post-new.php?post_type=playlist' ),
		);
		$wp_admin_bar->add_node( $args );
	}

	// --- new schedule override ---
	if ( current_user_can( 'publish_shows' ) ) {
		$args = array(
			'id'     => 'new-override',
			'title'  => __( 'Override', 'radio-station' ),
			'parent' => 'new-content',
			'href'   => admin_url( 'post-new.php?post_type=override' ),
		);
		$wp_admin_bar->add_node( $args );
	}

}
add_action( 'admin_bar_menu', 'station_radio_modify_admin_bar_menu', 999 );

// --- output help page ---
function radio_station_plugin_help() {

	// 2.2.2: include patreon button link
	echo radio_station_patreon_blurb( false );

	// include help template
	include dirname( __FILE__ ) . '/templates/help.php';
}

// --- output playlist export page ---
function radio_station_admin_export() {
	global $wpdb;

	// first, delete any old exports from the export directory
	$dir = dirname( __FILE__ ) . '/export/';
	if ( is_dir( $dir ) ) {
		$get_contents = opendir( $dir );
		while ( $file = readdir( $get_contents ) ) {
			if ( '.' !== $file && '..' !== $file ) {
				unlink( $dir . $file );
			}
		}
		closedir( $get_contents );
	}

	// watch for form submission
	if ( isset( $_POST['export_action'] ) && ( 'station_playlist_export' === $_POST['export_action'] ) ) {

		// validate referrer and nonce field
		check_admin_referer( 'station_export_valid' );

		$start  = $_POST['station_export_start_year'] . '-' . $_POST['station_export_start_month'] . '-' . $_POST['station_export_start_day'];
		$start .= ' 00:00:00';
		$end    = $_POST['station_export_end_year'] . '-' . $_POST['station_export_end_month'] . '-' . $_POST['station_export_end_day'];
		$end   .= ' 23:59:59';

		// fetch all records that were created between the start and end dates
		$sql =
			'SELECT `posts`.`ID`, `posts`.`post_date` FROM ' . $wpdb->prefix . "posts AS `posts`
			WHERE `posts`.`post_type` = 'playlist'
			AND `posts`.`post_status` = 'publish'
			AND TO_DAYS(`posts`.`post_date`) >= TO_DAYS(%s)
			AND TO_DAYS(`posts`.`post_date`) <= TO_DAYS(%s)
			ORDER BY `posts`.`post_date` ASC;";
		// prepare query before executing
		$query     = $wpdb->prepare( $sql, array( $start, $end ) );
		$playlists = $wpdb->get_results( $query );

		if ( ! $playlists ) {
			$list = 'No playlists found for this period.';}

		// fetch the tracks for each playlist from the wp_postmeta table
		foreach ( $playlists as $i => $playlist ) {

			$songs = get_post_meta( $playlist->ID, 'playlist', true );

			// remove any entries that are marked as 'queued'
			foreach ( $songs as $j => $entry ) {
				if ( 'queued' === $entry['playlist_entry_status'] ) {
					unset( $songs[ $j ] );}
			}

			$playlists[ $i ]->songs = $songs;
		}

		$output = '';

		$date = '';
		foreach ( $playlists as $playlist ) {
			if ( ! isset( $playlist->post_date ) ) {
				continue;
			}
			$playlist_datetime  = explode( ' ', $playlist->post_date );
			$playlist_post_date = array_shift( $playlist_datetime );
			if ( empty( $date ) || $date !== $playlist_post_date ) {
				$date    = $playlist_post_date;
				$output .= $date . "\n\n";
			}

			foreach ( $playlist->songs as $song ) {
				$output .= $song['playlist_entry_artist'] . ' || ' . $song['playlist_entry_song'] . ' || ' . $song['playlist_entry_album'] . ' || ' . $song['playlist_entry_label'] . "\n";
			}
		}

		// save as file
		$dir  = dirname( __FILE__ ) . '/export/';
		$file = $date . '-export.txt';
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );}

		$f = fopen( $dir . $file, 'w' );
		fwrite( $f, $output );
		fclose( $f );

		// display link to file
		$url = get_bloginfo( 'url' ) . '/wp-content/plugins/radio-station/tmp/' . $file;
		echo wp_kses_post( '<div id="message" class="updated"><p><strong><a href="' . $url . '">' . __( 'Right-click and download this file to save your export', 'radio-station' ) . '</a></strong></p></div>' );
	}

	// display the export page
	include dirname( __FILE__ ) . '/templates/admin-export.php';

}


// --------------------
// === Admin Notice ===
// --------------------

// --- plugin announcement notice ---
// 2.2.2: added plugin announcement notice
function radio_station_announcement_notice() {

	// --- bug out if already dismissed ---
	if ( get_option( 'radio_station_announcement_dismissed' ) ) {
		return;
	}

	// --- bug out on certain plugin pages ---
	$pages = array( 'radio-station', 'radio-station-help' );
	if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pages, true ) ) {
		return;
	}

	// --- display plugin announcement ---
	echo '<div id="radio-station-announcement-notice" class="notice notice-info" style="position:relative;">';
		echo radio_station_patreon_blurb();
		echo '<iframe src="javascript:void(0);" name="radio-station-notice-iframe" style="display:none;"></iframe>';
	echo '</div>';
}
add_action( 'admin_notices', 'radio_station_announcement_notice' );

// --- dismiss plugin announcement notice ---
// 2.2.2: AJAX for announcement notice dismissal
function radio_station_announcement_dismiss() {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'update_plugins' ) ) {
		update_option( 'radio_station_announcement_dismissed', true );
		echo "<script>parent.document.getElementById('radio-station-announcement-notice').style.display = 'none';</script>";
		exit;
	}
}
add_action( 'wp_ajax_radio_station_announcement_dismiss', 'radio_station_announcement_dismiss' );

// --- Patreon supporter blurb ---
// 2.2.2: added simple patreon supporter blurb
function radio_station_patreon_blurb( $dismissable = true ) {

	$blurb                = '<ul style="list-style:none;">';
		$blurb           .= '<li style="display:inline-block; vertical-align:middle;">';
			$plugin_image = plugins_url( 'images/radio-station.png', __FILE__ );
			$blurb       .= '<img src="' . $plugin_image . '">';
		$blurb           .= '</li>';
		$blurb           .= '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">';
			$blurb       .= '<b style="font-size:17px;">' . __( 'Help support us to make improvements, modifications and introduce new features!', 'radio-station' ) . '</b><br>';
			$blurb       .= __( 'With over a thousand radio station users thanks to the original plugin author Nikki Blight', 'radio-station' ) . ', <br>';
			$blurb       .= __( 'since June 2019', 'radio-station' ) . ', ';
			$blurb       .= '<b>' . __( 'Radio Station', 'radio-station' ) . '</b> ';
			$blurb       .= __( ' plugin development has been actively taken over by', 'radio-station' );
			$blurb       .= ' <a href="http://netmix.com" target="_blank">Netmix</a>.<br>';
			$blurb       .= __( 'We invite you to', 'radio-station' );
			$blurb       .= ' <a href="https://patreon.com/radiostation" target="_blank">';
				$blurb   .= __( 'Become a Radio Station Patreon Supporter', 'radio-station' );
			$blurb       .= '</a> ' . __( 'to make it better for everyone', 'radio-station' ) . '!';
		$blurb           .= '</li>';
		$blurb           .= '<li style="display:inline-block; vertical-align:middle; margin-left:40px;">';
			$blurb       .= radio_station_patreon_button();
		$blurb           .= '</li>';
	$blurb               .= '</ul>';
	if ( $dismissable ) {
		?>
		<?php
		$blurb          .= '<div style="position:absolute; top:20px; right: 20px;">';
			$dismiss_url = admin_url( 'admin-ajax.php?action=radio_station_announcement_dismiss' );
			$blurb      .= '<a href="' . $dismiss_url . '" target="radio-station-notice-iframe" style="text-decoration:none;">';
			$blurb      .= '<span class="dashicons dashicons-dismiss" title="' . __( 'Dismiss this Notice', 'radio-station' ) . '"></span></a>';
		$blurb          .= '</div>';
	}
	return $blurb;
}

// --- Patreon supporter button ---
// 2.2.2: added simple patreon supporter image button
function radio_station_patreon_button() {
	$image_url = plugins_url( 'images/patreon-button.jpg', __FILE__ );
	$button    = '<a href="https://patreon.com/radiostation" target="_blank">';
	$button   .= '<img id="radio-station-patreon-button" src="' . $image_url . '" border="0">';
	$button   .= '</a>';
	return $button;
}
