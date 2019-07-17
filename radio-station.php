<?php
/**
 * @package Radio Station
 * @version 2.1.1
 */
/*
Plugin Name: Radio Station
Plugin URI: https://netmix.com/radio-station 
Description: Adds show page, show schedule, DJ member role, playlist and on-air programming functionality to your site.
Author: Nikki Blight <nblight@nlb-creations.com>, Tony Zeoli <tonyzeoli@netmix.com>
Version: 2.1.1
Text Domain: radio-station
Domain Path: /languages
Author URI: https://netmix.com/radio-station

Copyright 2013 Nikki Blight  (email : nblight@nlb-creations.com)

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

//let's include some files
include('includes/post_types.php');
include('includes/master_schedule.php');
include('includes/shortcodes.php');
include('includes/widget_nowplaying.php');
include('includes/widget_djonair.php');
include('includes/widget_djcomingup.php');
include('includes/support_functions.php');

//add "show" as a post type that supports featured images
function station_add_featured_image_support() {
    $supportedTypes = get_theme_support( 'post-thumbnails' );
    
    if( $supportedTypes === false ) {
        add_theme_support( 'post-thumbnails', array( 'show' ) );
    }               
    elseif( is_array( $supportedTypes ) ) {
        $supportedTypes[0][] = 'show';
        add_theme_support( 'post-thumbnails', $supportedTypes[0] );
    }
}
add_action( 'init', 'station_add_featured_image_support' );

//load the text domain
function station_init() {
	load_plugin_textdomain( 'radio-station', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'station_init');

//add the necessary stylesheets
function station_load_styles() {
	if ( !is_admin() ) {
		$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		
		$program_css = get_stylesheet_directory().'/program-schedule.css';
		if(!file_exists($program_css)) {
			wp_enqueue_style( 'program-schedule', $dir.'templates/program-schedule.css' );
		}
		else {
			wp_enqueue_style( 'program-schedule', get_stylesheet_directory_uri().'/program-schedule.css');
		}
		
		$dj_widget_css = get_stylesheet_directory().'/djonair.css';
		if(!file_exists($dj_widget_css)) {
			wp_enqueue_style( 'dj-widget', $dir.'templates/djonair.css' );
		}
		else {
			wp_enqueue_style( 'dj-widget', get_stylesheet_directory_uri().'/djonair.css');
		}
	}
}
add_action('wp_enqueue_scripts', 'station_load_styles');

// load the theme file for the playlist and show post types
function station_load_templates($single_template) {
	global $post;

	if ($post->post_type == 'playlist') {
		//first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory().'/single-playlist.php';
		if(!file_exists($user_theme)) {
			$single_template = ABSPATH.'wp-content/plugins/radio-station/templates/single-playlist.php';
		}
	}
	 
	if ($post->post_type == 'show') {
		//first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory().'/single-show.php';
		if(!file_exists($user_theme)) {
			$single_template = ABSPATH.'wp-content/plugins/radio-station/templates/single-show.php';
		}
	}
	 
	return $single_template;
}
add_filter( "single_template", "station_load_templates" ) ;

// load the theme file for the playlist archive pages
function station_load_custom_post_type_template( $archive_template ) {
	global $post;
	
	if(is_post_type_archive('playlist')) {
	
		$playlist_archive_theme = get_stylesheet_directory().'/archive-playlist.php';
		if(!file_exists($playlist_archive_theme)) {
			$archive_template = ABSPATH.'wp-content/plugins/radio-station/templates/archive-playlist.php';
		}
	}
	
	return $archive_template;
}
add_filter( 'archive_template', 'station_load_custom_post_type_template' ) ;

//add some style rules to certain parts of the admin area
function station_load_admin_styles() {
	global $post;
	
	if(isset($post->post_type)) {
		if ($post->post_type == 'playlist') {
			echo '<style type="text/css">
		           .wp-editor-container textarea.wp-editor-area { height: 100px; }
		         </style>';
		}
	}
}
add_action('admin_head', 'station_load_admin_styles');

//set up the DJ role and user capabilities
function set_station_roles() {
	global $wp_roles;
	
	//set only the necessary capabilities for DJs
	$caps = array(
				'edit_shows' => true,
				'edit_published_shows' => true,
				'edit_others_shows' => true,
				'read_shows' => true,
				'edit_playlists' => true,
				'edit_published_playlists' => true,
				//'edit_others_playlists' => true,  //uncomment to allow DJs to edit all playlists
				'read_playlists' => true,
				'publish_playlists' => true,
				'read' => true,
				'upload_files' => true,
				'edit_posts' => true,
				'edit_published_posts' => true,
				'publish_posts' => true,
				'delete_posts' => true
			);
	//$wp_roles->remove_role('dj'); //we need this here in case we ever update the capabilities list
	$wp_roles->add_role( 'dj', 'DJ', $caps );
	
	//grant all new capabilities to admin users
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
if(is_multisite()) {
	add_action('init', 'set_station_roles', 10, 0);
}
else {
	add_action('admin_init', 'set_station_roles', 10, 0);
}

//revoke the ability to edit a show if the user is not listed as a DJ on that show
function revoke_show_edit_cap($allcaps, $cap = 'edit_shows', $args) {
	global $post;
	global $wp_roles;
	
	$user = wp_get_current_user();
	
	//determine which roles should have full access aside from administrator
	$add_roles = array('administrator');
	if(isset($wp_roles->roles) && is_array($wp_roles->roles)) {
		foreach($wp_roles->roles as $name => $role) {
			foreach($role['capabilities'] as $capname => $capstatus) {
				if($capname == "publish_shows" && ($capstatus == 1 || $capstatus == true)) {
					$add_roles[] = $name;
				}
			}
		}
	}
	
	//exclude administrators and custom roles with appropriate capabilities... they should be able to do whatever they want
	$found = false;
	foreach($add_roles as $role) {
		if(in_array($role, $user->roles)) {
			$found = true;
		}
	}

	if(!$found) {	
		
		//limit this to published shows
		if(isset($post->post_type)) {
			if(is_admin() && $post->post_type == 'show' && $post->post_status == 'publish') {
				$djs = get_post_meta($post->ID, "show_user_list", true);
				
				if($djs == '') {
					$djs = array();
				}
				
				//if they're not listed, temporarily revoke editing ability for this post
				if(!in_array($user->ID, $djs)) {
					$allcaps['edit_shows'] = false;
					$allcaps['edit_published_shows'] = false;
				}
			}
		}
	}
	return $allcaps;
}
add_filter('user_has_cap', 'revoke_show_edit_cap', 10, 3);

//remove the Add Show link for DJs from the admin side menu
function radio_modify_shows_menu() {
	global $submenu;
	
	$user = wp_get_current_user();
	if(in_array('dj', $user->roles)) {
		unset($submenu['edit.php?post_type=show'][10]);
	}
}
add_action('admin_menu','radio_modify_shows_menu');

//remove the Add Show link for DJs from the wp admin bar
function radio_modify_admin_bar_menu($wp_admin_bar) {

	$user = wp_get_current_user();
	if(in_array('dj', $user->roles)) {
		$wp_admin_bar->remove_node('new-show');
	}
}
add_action( 'admin_bar_menu', 'radio_modify_admin_bar_menu', 999 );

//create a menu item for the options page
function station_admin_menu() {
	if (function_exists('add_options_page')) {

		//add to Announcements tab
		add_submenu_page( 'edit.php?post_type=playlist', __('Export Playlists', 'radio-station'), __('Export', 'radio-station'), 'manage_options', 'radio-station', 'station_admin_export' );
	}
}
add_action( 'admin_menu', 'station_admin_menu' );

//output the options page
function station_admin_export() {
	global $wpdb;
	
	//first, delete any old exports from the tmp directory
	$dir = ABSPATH.'/wp-content/plugins/radio-station/tmp/';
	$get_contents = opendir($dir);
	while($file = readdir($get_contents)) {
		if($file != "." && $file != "..") {
			//chmod($dir.$file, 0777);
			unlink($dir.$file);
		}
	}
	closedir($get_contents);
	
	//watch for form submission
	if (!empty($_POST['export_action'])) {
		//validate the referrer field
		check_admin_referer('station_export_valid');
		
		$start = $_POST['station_export_start_year'].'-'.$_POST['station_export_start_month'].'-'.$_POST['station_export_start_day'];
		$end = $_POST['station_export_end_year'].'-'.$_POST['station_export_end_month'].'-'.$_POST['station_export_end_day'];

		//fetch all records that were created between the start and end dates
		$playlists = $wpdb->get_results("SELECT `posts`.`ID`, `posts`.`post_date` FROM ".$wpdb->prefix."posts AS `posts`
										WHERE `posts`.`post_type` = 'playlist'  
										AND `posts`.`post_status` = 'publish'
										AND TO_DAYS(`posts`.`post_date`) >= TO_DAYS('".$start." 00:00:00') AND TO_DAYS(`posts`.`post_date`) <= TO_DAYS('".$end." 23:59:59')  
										ORDER BY `posts`.`post_date` ASC;");
		
		if(!$playlists) {
			$list = 'No playlists found for this period.';
		}
		
		//fetch the tracks for each playlist from the wp_postmeta table
		foreach($playlists as $i => $playlist) {
			$songs = get_post_meta($playlist->ID, 'playlist', true);
			
			//removed any entries that are marked as 'queued'
			foreach($songs as $j => $entry) {
				if($entry['playlist_entry_status'] == 'queued') {
					unset($songs[$j]);
				}
			}
			
			$playlists[$i]->songs = $songs;
		}
		
		$output = '';
		
		$date = '';
		foreach($playlists as $playlist) {
			if($date == '' || $date != array_shift(explode(" ", $playlist->post_date))) {
				$date = array_shift(explode(" ", $playlist->post_date));
				$output .= $date."\n\n";
			}
			
			foreach($playlist->songs as $song) {
				$output .= $song['playlist_entry_artist'].' || '.$song['playlist_entry_song'].' || '.$song['playlist_entry_album'].' || '.$song['playlist_entry_label']."\n";
			}
		}

		//save as file
		$dir = ABSPATH.'/wp-content/plugins/radio-station/tmp/';
		$file = $date."export.txt";
		if(!file_exists($dir)) {
			mkdir($dir);
		}
		
		$f = fopen($dir.$file, "w");
		fwrite($f, $output);
		fclose($f);
		
		//display link to file
		echo '<div id="message" class="updated"><p><strong><a href="'.get_bloginfo('url').'/wp-content/plugins/radio-station/tmp/'.$file.'">'.__('Right-click and download this file to save your export', 'radio-station').'</a></strong></p></div>';
	}

	//display the page
	?>
 
<div style="width: 620px; padding: 10px">
	<h2><?php _e('Export Playlists', 'radio-station'); ?></h2>
	<form action="" method="post" id="export_form" accept-charset="utf-8" style="position:relative">
		
		<?php wp_nonce_field('station_export_valid'); ?>
		
		<input type="hidden" name="export_action" value="station_playlist_export" />
		<table class="form-table">
			
			<tr valign="top">
				<?php $smonth = isset($_POST['station_export_start_month']) ? $_POST['station_export_start_month'] : ''; ?>
				<th scope="row"><?php _e('Start Date', 'radio-station'); ?></th>
				<td>
					<select name="station_export_start_month" id="station_export_start_month">
						<option value="01" <?php if($smonth == '01') { echo 'selected="selected"'; } ?>>01 (Jan)</option>
						<option value="02" <?php if($smonth == '02') { echo 'selected="selected"'; } ?>>02 (Feb)</option>
						<option value="03" <?php if($smonth == '03') { echo 'selected="selected"'; } ?>>03 (Mar)</option>
						<option value="04" <?php if($smonth == '04') { echo 'selected="selected"'; } ?>>04 (Apr)</option>
						<option value="05" <?php if($smonth == '05') { echo 'selected="selected"'; } ?>>05 (May)</option>
						<option value="06" <?php if($smonth == '06') { echo 'selected="selected"'; } ?>>06 (Jun)</option>
						<option value="07" <?php if($smonth == '07') { echo 'selected="selected"'; } ?>>07 (Jul)</option>
						<option value="08" <?php if($smonth == '08') { echo 'selected="selected"'; } ?>>08 (Aug)</option>
						<option value="09" <?php if($smonth == '09') { echo 'selected="selected"'; } ?>>09 (Sep)</option>
						<option value="10" <?php if($smonth == '10') { echo 'selected="selected"'; } ?>>10 (Oct)</option>
						<option value="11" <?php if($smonth == '11') { echo 'selected="selected"'; } ?>>11 (Nov)</option>
						<option value="12" <?php if($smonth == '12') { echo 'selected="selected"'; } ?>>12 (Dec)</option>
					</select>
					
					<?php $sday = isset($_POST['station_export_start_day']) ? $_POST['station_export_start_day'] : ''; ?>
					<select name="station_export_start_day" id="station_export_start_day">
						<?php 
							for($i=1; $i<=31; $i++) {
								$day = $i;
								if($i < 10) { $day = '0'.$day; }
								echo '<option value="'.$day.'"';
								if($sday == $day) {
									echo ' selected="selected"';
								}
								echo '>'.$i.'</option>';	
							}
						?>
					</select>
					
					<?php $syear = isset($_POST['station_export_start_year']) ? $_POST['station_export_start_year'] : ''; ?>
					<select name="station_export_start_year" id="station_export_start_year">
						<?php 
							$year = date('Y');
							for($i=$year-5; $i<=$year+5; $i++) {
								$selected = '';
								if($i == $syear) { 
									$selected = ' selected="selected"';
								}
								else {
									if($i == $year && $syear == '') {
										$selected = ' selected="selected"';
									}
								}
								echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';	
							}
						?>
					</select>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('End Date', 'radio-station'); ?></th>
				<td>
					<?php $emonth = isset($_POST['station_export_end_month']) ? $_POST['station_export_end_month'] : ''; ?>
					<select name="station_export_end_month" id="station_export_end_month">
						<option value="01" <?php if($emonth == '01') { echo 'selected="selected"'; } ?>>01 (Jan)</option>
						<option value="02" <?php if($emonth == '02') { echo 'selected="selected"'; } ?>>02 (Feb)</option>
						<option value="03" <?php if($emonth == '03') { echo 'selected="selected"'; } ?>>03 (Mar)</option>
						<option value="04" <?php if($emonth == '04') { echo 'selected="selected"'; } ?>>04 (Apr)</option>
						<option value="05" <?php if($emonth == '05') { echo 'selected="selected"'; } ?>>05 (May)</option>
						<option value="06" <?php if($emonth == '06') { echo 'selected="selected"'; } ?>>06 (Jun)</option>
						<option value="07" <?php if($emonth == '07') { echo 'selected="selected"'; } ?>>07 (Jul)</option>
						<option value="08" <?php if($emonth == '08') { echo 'selected="selected"'; } ?>>08 (Aug)</option>
						<option value="09" <?php if($emonth == '09') { echo 'selected="selected"'; } ?>>09 (Sep)</option>
						<option value="10" <?php if($emonth == '10') { echo 'selected="selected"'; } ?>>10 (Oct)</option>
						<option value="11" <?php if($emonth == '11') { echo 'selected="selected"'; } ?>>11 (Nov)</option>
						<option value="12" <?php if($emonth == '12') { echo 'selected="selected"'; } ?>>12 (Dec)</option>
					</select>
					
					<?php $eday = isset($_POST['station_export_end_day']) ? $_POST['station_export_end_day'] : ''; ?>
					<select name="station_export_end_day" id="station_export_end_day">
						<?php 
							for($i=1; $i<=31; $i++) {
								$day = $i;
								if($i < 10) { $day = '0'.$day; }
								echo '<option value="'.$day.'"';
								if($eday == $day) {
									echo ' selected="selected"';
								}
								echo '>'.$i.'</option>';	
							}
						?>
					</select>
					
					<?php $eyear = isset($_POST['station_export_end_year']) ? $_POST['station_export_end_year'] : ''; ?>
					<select name="station_export_end_year" id="station_export_end_year">
						<?php 
							$year = date('Y');
							for($i=$year-5; $i<=$year+5; $i++) {
								$selected = '';
								if($i == $eyear) { 
									$selected = ' selected="selected"';
								}
								else {
									if($i == $year && $eyear == '') {
										$selected = ' selected="selected"';
									}
								}
								echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';	
							}
						?>
					</select>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td>
					<input type="submit" name="Submit" class="button-primary" value="<?php _e('Export', 'radio-station'); ?>"/>
				</td>
			</tr>
		</table>
	</form>
</div>
 
<?php
}


//Add custom meta box for show assigment on blog posts
function station_add_showblog_box() {
	add_meta_box(
			'dynamicShowBlog_sectionid',
			__( 'Related to Show', 'radio-station' ),
			'station_inner_showblog_custom_box',
			'post',
			'side');
}
add_action( 'add_meta_boxes', 'station_add_showblog_box' );

//Prints the box content for the Show field
function station_inner_showblog_custom_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaShowBlog_noncename' );

	$args = array(
			'numberposts'     => -1,
			'offset'          => 0,
			'orderby'         => 'post_title',
			'order'           => 'ASC',
			'post_type'       => 'show',
			'post_status'     => 'publish'
	);

	$shows = get_posts($args);
	$current = get_post_meta($post->ID,'post_showblog_id',true);

	?>
    <div id="meta_inner">
    
    <select name="post_showblog_id">
    	<option value=""></option>
    <?php
    	foreach($shows as $show) {
    		$selected = '';
    		if($show->ID == $current) {
    			$selected = ' selected="selected"';
    		}
    		
    		echo '<option value="'.$show->ID.'"'.$selected.'>'.$show->post_title.'</option>';	
    	}
	?>
	</select>
	</div>
   <?php
}

//When a playlist is saved, saves our custom data
function station_save_postdata( $post_id ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;

    if(isset($_POST['post_showblog_id'])) {
	    // verify this came from the our screen and with proper authorization,
	    // because save_post can be triggered at other times
	    
	    if (isset($_POST['dynamicMetaShowBlog_noncename'])){
	    	if ( !wp_verify_nonce( $_POST['dynamicMetaShowBlog_noncename'], plugin_basename( __FILE__ ) ) )
	    	return;
	    }else{return;}
	    
	    // OK, we're authenticated: we need to find and save the data
	    $show = $_POST['post_showblog_id'];

	    update_post_meta($post_id,'post_showblog_id',$show);
    }
    
}
add_action( 'save_post', 'station_save_postdata' );


//add a help page
function station_plugin_menu() {
	if (function_exists('add_options_page')) {
		//add to Shows tab
		add_submenu_page( 'edit.php?post_type=show', 'Radio Station Help', 'Radio Station Help', 'manage_options', '', 'station_plugin_help' );
	}
}
add_action( 'admin_menu', 'station_plugin_menu' );


function station_plugin_help() {
	?>
	<h2>Radio Station Help/FAQs</h2>

	<strong>I've scheduled all my shows, but they're not showing up on the programming grid!</strong>
	<p>Did you remember to check the "Active" checkbox for each show?  If a show is not marked active, the plugin assumes that it's not currently in production and hides it on the grid.</p>

	<hr />
	
	<strong>I'm seeing 404 Not Found errors when I click on the link for a show!</strong>
	<p>Try re-saving your site's permalink settings.  Wordpress sometimes gets confused with a new custom post type is added.</p>

	<hr />
	
	<strong>How do I display a full schedule of my station's shows? </strong>
	<p>Use the shortcode <code>[master-schedule]</code> on any page.  This will generate a full-page schedule in one of three formats.
	<br /><br />
	The following attributes are available for the shortcode:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>'list' => If set to a value of 'list', the schedule will display in list format rather than table or div format. Valid values are 'list', 'divs', 'table'.  Default value is 'table'.</li>
			<li>'time' => The time format you with to use.  Valid values are 12 and 24.  Default is 12.</li>
			<li>'show_link' => Display the title of the show as a link to its profile page.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
			<li>'display_show_time' => Display start and end times of each show after the title in the grid.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
			<li>'show_image' => If set to a value of 1, the show's avatar will be displayed.  Default value is 0.</li>
			<li>'show_djs' => If set to a value of 1, the names of the show's DJs will be displayed.  Default value is 0.</li>
			<li>'divheight' => Set the height, in pixels, of the individual divs in the 'divs' layout.  Default is 45.</li>
		</ul>
	<br /><br />		
	For example, if you wish to display the schedule in 24-hour time format, use <code>[master-schedule time="24"]</code>.</p>

	<hr />
	
	<strong>How do I schedule a show? </strong>

	<p>Simply create a new show.  You will be able to assign it to any timeslot you wish on the edit page.</p>

	<hr />
	
	<strong>What if I have a special event? </strong>

	<p>If you have a one-off event that you need to show up in the On-Air or Coming Up Next widgets, you can create a Schedule Override by clicking the Schedule Override tab in the Dashboard menu.  This will allow you to set aside a block of time on a specific date, and will display the title you give it in the widgets.  Please note that this will only override the widgets and their corresponding shortcodes.  If you are using the weekly master schedule shortcode on a page, its output will not be altered.</p>

	<hr />
	
	<strong>How do I get the last song played to show up? </strong>

	<p>You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode <code>[now-playing]</code> in your page/post, or use <code>do_shortcode('[now-playing]');</code> in your template files.
	<br /><br />
	The following attributes are available for the shortcode:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>'title' => The title you would like to appear over the now playing block</li>
			<li>'artist' => Display artist name.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
			<li>'song' => Display song name.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
			<li>'album' => Display album name.  Valid values are 0 for hide, 1 for show.  Default is 0.</li>
			<li>'label' => Display label name.  Valid values are 0 for hide, 1 for show.  Default is 0.</li>
			<li>'comments' => Display DJ comments.  Valid values are 0 for hide, 1 for show.  Default is 0.</li>
		</ul>
	<br /><br />
	Example:<br />
	<code>[now-playing title="Current Song" artist="1" song="1" album="1" label="1" comments="0"]</code></p>

	<hr />
	
	<strong>What about displaying the current DJ on air? </strong>

	<p>You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode <code>[dj-widget]</code> in your page/post, or you can use <code>do_shortcode('[dj-widget]');</code> in your template files.
	<br /><br />
	The following attributes are available for the shortcode:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>'title' </strong>> The title you would like to appear over the on-air block</li> 
			<li>'display_djs' </strong>> Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.</li>
			<li>'show_avatar' </strong>> Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.</li>
			<li>'show_link' </strong>> Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.</li>
			<li>'default_name' </strong>> The text you would like to display when no show is schedule for the current time.</li>
			<li>'time' </strong>> The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.</li>
			<li>'show_sched' </strong>> Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.</li>
			<li>'show_playlist' </strong>> Display a link to the show's current playlist.  Valid values are 0 for hide link, 1 for show link.  Default is 1.</li>
			<li>'show_all_sched' </strong>> Displays all schedules for a show if it airs on multiple days.  Valid values are 0 for current schedule, 1 for all schedules.  Default is 0.</li>
			<li>'show_desc' </strong>> Displays the first 20 words of the show's description. Valid values are 0 for hide descripion, 1 for show description.  Default is 0.</li>
		</ul>
	<br /><br />
	Example:<br />
	<code>[dj-widget title</strong>"Now On-Air" display_djs</strong>"1" show_avatar</strong>"1" show_link</strong>"1" default_name</strong>"RadioBot" time</strong>"12" show_sched</strong>"1" show_playlist</strong>"1"]</code></p>

	<hr />

	<strong>Can I display upcoming shows, too? </strong>

	<p>You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode <code>[dj-coming-up-widget]</code> in your page/post, or you can use <code>do_shortcode('[dj-coming-up-widget]');</code> in your template files.
	<br /><br />
	The following attributes are available for the shortcode:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>'title' => The title you would like to appear over the on-air block</li> 
			<li>'display_djs' => Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.</li>
			<li>'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.</li>
			<li>'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.</li>
			<li>'limit' => The number of upcoming shows to display.  Default is 1.</li>
			<li>'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.</li>
			<li>'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.</li>
		</ul>
		<br /><br />
		Example:<br />
		<code>[dj-coming-up-widget title</strong>"Coming Up On-Air" display_djs</strong>"1" show_avatar</strong>"1" show_link</strong>"1" limit</strong>"3" time</strong>"12" schow_sched</strong>"1"]`</code></p>

	<hr />
		
	<strong>Can I change how show pages are laid out/displayed? </strong>

	<p>Yes.  Copy the radio-station/templates/single-show.php file into your theme directory, and alter as you wish.  This template, and all of the other templates in this plugin, are based on the TwentyEleven theme.  If you're using a different theme, you may have to rework them to reflect your theme's layout.</p>

	<hr />
	
	<strong>What about playlist pages? </strong>

	<p>Same deal.  Grab the radio-station/templates/single-playlist.php file, copy it to your theme directory, and go to town.</p>

	<hr />
	
	<strong>And playlist archive pages?  </strong>

	<p>Same deal.  Grab the radio-station/templates/archive-playlist.php file, copy it to your theme directory, and go to town.</p>

	<hr />
	
	<strong>And the program schedule, too? 	</strong>

	<p>Because of the complexity of outputting the data, you can't directly alter the template, but you can copy the radio-station/templates/program-schedule.css file into your theme directory and change the CSS rules for the page.</p>
	
	<hr />

	<strong>What if I want to style the DJ on air sidebar widget? </strong>

	<p>Copy the radio-station/templates/djonair.css file to your theme directory.</p>

	<hr />
	
	<strong>How do I get an archive page that lists ALL of the playlists instead of just the archives of individual shows? </strong>

	<p>First, grab the radio-station/templates/playlist-archive-template.php file, and copy it to your active theme directory.
	<br /><br />
	Then, create a Page in wordpress to hold the playlist archive.
	<br /><br />
	Under Page Attributes, set the template to Playlist Archive.  Please note: If you don't copy the template file to your theme first, the option to select it will not appear.</p>
	
	<hr />

	<strong>Can show pages link to an archive of related blog posts? </strong>

	<p>Yes, in much the same way as the full playlist archive described above. First, grab the radio-station/templates/show-blog-archive-template.php file, and copy it to your active theme directory.
	<br /><br />
	Then, create a Page in wordpress to hold the blog archive.
	<br /><br />
	Under Page Attributes, set the template to Show Blog Archive.</p>

	<hr />
	
	<strong>How can I list all of my shows? </strong>

	<p>Use the shortcode <code>[list-shows]</code> in your page/posts or use <code>do_shortcode(['list-shows']);</code> in your template files.  This will output an unordered list element containing the titles of and links to all shows marked as "Active". 
	<br /><br />
	The following attributes are available for the shortcode:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>'genre' => Displays shows only from the specified genre(s).  Separate multiple genres with a comma, e.g. genre="pop,rock".</li>
		</ul>
	<br /><br />
	Example:
	<code>`[list-shows genre="pop"]`</code>
	<code>[list-shows genre="pop,rock,metal"]</code></p>
	
	<hr />

	<strong>I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types.  How do I do that? </strong>

	<p>Since I'm stongly opposed to reinventing the wheel, I recommend Justin Tadlock's excellent "Members" plugin for that purpose.  You can find it on Wordpress.org, here: <a href="http://wordpress.org/extend/plugins/members/" target="_blank">http://wordpress.org/extend/plugins/members/</a>
	<br /><br />
	Add the following capabilities to any role you want to give access to Shows and Playlist:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>edit_shows</li>
			<li>edit_published_shows</li>
			<li>edit_others_shows</li>
			<li>read_shows</li>
			<li>edit_playlists</li>
			<li>edit_published_playlists</li>
			<li>read_playlists</li>
			<li>publish_playlists</li>
			<li>read</li>
			<li>upload_files</li>
			<li>edit_posts</li>
			<li>edit_published_posts</li>
			<li>publish_posts</li>
		</ul>
	<br /><br />
	If you want the new role to be able to create or approve new shows, you should also give them the following capabilities:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>publish_shows</li>
			<li>edit_others_shows</li>
		</ul>
	</p>
	
	<hr />

	<strong>How do I change the DJ's avatar in the sidebar widget? </strong>

	<p>The avatar is whatever image is assigned as the DJ/Show's featured image.  All you have to do is set a new featured image.</p>

	<hr />
	
	<strong>Why don't any users show up in the DJs list on the Show edit page? </strong>

	<p>You did remember to assign the DJ role to the users you want to be DJs, right?</p>
	
	<hr />

	<strong>My DJs can't edit a show page.  What do I do? </strong>

	<p>The only DJs that can edit a show are the ones listed as being ON that show in the DJs select menu.  This is to prevent DJs from editing other DJs shows without permission.</p>
	
	<hr />

	<strong>How can I export a list of songs played on a given date? </strong>

	<p>Under the Playlists menu in the dashboard is an Export link.  Simply specify the a date range, and a text file will be generated for you.</p>
	
	<hr />

	<strong>Can my DJ's have customized user pages in addition to Show pages? </strong>

	<p>Yes.  These pages are the same as any other author page (edit or create the author.php template file in your theme directory).  A sample can be found in the radio-station/templates/author.php file (please note that this file doesn't actually do anything unless you copy it over to your theme's directory).  Like the other theme templates included with this plugin, this file is based on the TwentyEleven theme and may need to be modified in order to work with your theme.</p>
	
	<hr />

	<strong>I don't want to use Gravatar for my DJ's image on their profile page. </strong>

	<p>Then you'll need to install a plugin that lets you add a different image to your DJ's user account and edit your author.php theme file accordingly.  That's a little out of the scope of this plugin.  I recommend Cimy User Extra Fields:  <a href="http://wordpress.org/extend/plugins/cimy-user-extra-fields/" target="_blank">http://wordpress.org/extend/plugins/cimy-user-extra-fields/</a></p>
	
	<hr />

	<strong>What languages other than English is the plugin available in, and can you translate the plugin into my language? </strong>
	
	<p>Right now:
		<ul style="list-style: disc inside none; text-indent: 50px;">
			<li>Albanian (sq_AL)</li>
			<li>French (fr_FR)</li>
			<li>German (de_DE)</li>
			<li>Italian (it_IT)</li>
			<li>Russion (ru_RU)</li>
			<li>Serbian (sr_RS)</li>
			<li>Spanish (es_ES)</li>
		</ul>

	My foreign language skills are rather lacking.  I managed a Spanish translation, sheerly due to the fact that I still remember at least some of what I learned in high school Spanish class.  But I've included the .pot file in the /languages directory.  If you want to give it a shot, be my guest.  If you <a href="mailto:nblight@nlb-creations.com">send me</a> your finished translation, I'd love to include it.</p>
	
	<?php
}

?>