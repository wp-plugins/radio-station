<?php
/**
 * @package Radio Station
 * @version 1.3.3
 */
/*
Plugin Name: Radio Station
Plugin URI: http://nlb-creations.com/2013/02/25/wordpress-plugin-radio-station/ 
Description: Adds playlist and on-air programming functionality to your site.
Author: Nikki Blight <nblight@nlb-creations.com>
Version: 1.3.3
Author URI: http://www.nlb-creations.com
*/

//let's include some files
include('includes/playlist.php');
include('includes/dj-on-air.php');
include('includes/master_schedule.php');

//add the necessary stylesheets
if ( !is_admin() ) {
	$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	
	$program_css = get_stylesheet_directory_uri().'/program-schedule.css';
	if(!file_exists($program_css)) {
		wp_enqueue_style( 'program-schedule', $dir.'/templates/program-schedule.css' );
	}
	else {
		wp_enqueue_style( 'program-schedule', $program_css);
	}
	
	$dj_widget_css = get_stylesheet_directory_uri().'/djonair.css';
	if(!file_exists($dj_widget_css)) {
		wp_enqueue_style( 'dj-widget', $dir.'/templates/djonair.css' );
	}
	else {
		wp_enqueue_style( 'dj-widget', $dj_widget_css);
	}
}

// load the theme file for the playlist and show post types
function station_load_templates($single_template) {
	global $post;

	if ($post->post_type == 'playlist') {
		//first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory_uri().'/single-playlist.php';
		if(!file_exists($user_theme)) {
			$single_template = ABSPATH.'wp-content/plugins/radio-station/templates/single-playlist.php';
		}
	}
	 
	if ($post->post_type == 'show') {
		//first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory_uri().'/single-show.php';
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
	
		$playlist_archive_theme = get_stylesheet_directory_uri().'/archive-playlist.php';
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
	
	if ($post->post_type == 'playlist') {
		echo '<style type="text/css">
	           .wp-editor-container textarea.wp-editor-area { height: 100px; }
	         </style>';
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
				'publish_posts' => true
			);
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
add_action('admin_init', 'set_station_roles', 10, 0);

//revoke the ability to edit a show if the user is not listed as a DJ on that show
function revoke_show_edit_cap($allcaps, $cap = 'edit_shows', $args) {
	global $post;
	
	$user = wp_get_current_user();
	
	//exclude administrators... they should be able to do whatever they want
	if(!in_array('administrator', $user->roles)) {	
		
		//limit this to published shows
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
	return $allcaps;
}
add_filter('user_has_cap', 'revoke_show_edit_cap', 10, 3);


//create a menu item for the options page
function station_admin_menu() {
	if (function_exists('add_options_page')) {

		//add to Announcements tab
		add_submenu_page( 'edit.php?post_type=playlist', 'Export Playlists', 'Export', 'manage_options', 'radio-station', 'station_admin_export' );
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
		
		if(!$playlist) {
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
		echo '<div id="message" class="updated"><p><strong><a href="'.get_bloginfo('url').'/wp-content/plugins/radio-station/tmp/'.$file.'">Right-click and download this file to save your export</a></strong></p></div>';
	}

	//display the page
	?>
 
<div style="width: 620px; padding: 10px">
	<h2><?php _e('Export Playlists'); ?></h2>
	<form action="" method="post" id="export_form" accept-charset="utf-8" style="position:relative">
		
		<?php wp_nonce_field('station_export_valid'); ?>
		
		<input type="hidden" name="export_action" value="station_playlist_export" />
		<table class="form-table">
			
			<tr valign="top">
				<?php $smonth = isset($_POST['station_export_start_month']) ? $_POST['station_export_start_month'] : ''; ?>
				<th scope="row">Start Date</th>
				<td>
					<select name="station_export_start_month" id="station_export_start_month">
						<option value="01" <?php if($smonth == '01') { echo 'selected="selected"'; } ?>>Jan</option>
						<option value="02" <?php if($smonth == '02') { echo 'selected="selected"'; } ?>>Feb</option>
						<option value="03" <?php if($smonth == '03') { echo 'selected="selected"'; } ?>>Mar</option>
						<option value="04" <?php if($smonth == '04') { echo 'selected="selected"'; } ?>>Apr</option>
						<option value="05" <?php if($smonth == '05') { echo 'selected="selected"'; } ?>>May</option>
						<option value="06" <?php if($smonth == '06') { echo 'selected="selected"'; } ?>>Jun</option>
						<option value="07" <?php if($smonth == '07') { echo 'selected="selected"'; } ?>>Jul</option>
						<option value="08" <?php if($smonth == '08') { echo 'selected="selected"'; } ?>>Aug</option>
						<option value="09" <?php if($smonth == '09') { echo 'selected="selected"'; } ?>>Sep</option>
						<option value="10" <?php if($smonth == '10') { echo 'selected="selected"'; } ?>>Oct</option>
						<option value="11" <?php if($smonth == '11') { echo 'selected="selected"'; } ?>>Nov</option>
						<option value="12" <?php if($smonth == '12') { echo 'selected="selected"'; } ?>>Dec</option>
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
				<th scope="row">End Date</th>
				<td>
					<?php $emonth = isset($_POST['station_export_end_month']) ? $_POST['station_export_end_month'] : ''; ?>
					<select name="station_export_end_month" id="station_export_end_month">
						<option value="01" <?php if($emonth == '01') { echo 'selected="selected"'; } ?>>Jan</option>
						<option value="02" <?php if($emonth == '02') { echo 'selected="selected"'; } ?>>Feb</option>
						<option value="03" <?php if($emonth == '03') { echo 'selected="selected"'; } ?>>Mar</option>
						<option value="04" <?php if($emonth == '04') { echo 'selected="selected"'; } ?>>Apr</option>
						<option value="05" <?php if($emonth == '05') { echo 'selected="selected"'; } ?>>May</option>
						<option value="06" <?php if($emonth == '06') { echo 'selected="selected"'; } ?>>Jun</option>
						<option value="07" <?php if($emonth == '07') { echo 'selected="selected"'; } ?>>Jul</option>
						<option value="08" <?php if($emonth == '08') { echo 'selected="selected"'; } ?>>Aug</option>
						<option value="09" <?php if($emonth == '09') { echo 'selected="selected"'; } ?>>Sep</option>
						<option value="10" <?php if($emonth == '10') { echo 'selected="selected"'; } ?>>Oct</option>
						<option value="11" <?php if($emonth == '11') { echo 'selected="selected"'; } ?>>Nov</option>
						<option value="12" <?php if($emonth == '12') { echo 'selected="selected"'; } ?>>Dec</option>
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
					<input type="submit" name="Submit" class="button-primary" value="<?php _e('Export') ?>"/>
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
			__( 'Related to Show', 'myplugin_textdomain' ),
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
?>