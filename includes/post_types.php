<?php
/*
 * Define all new post types and create custom fields for them
 * Author: Nikki Blight
 * Since: 2.0.0
 */


// === Post Types ===
// - Register Post Types
// - Add Show Thumbnail Support
// - Add Genre Taxonomy
// === Playlists ===
// - Playlist Data Metabox
// === Shows ===
// - Related Show Metabox
// - Assign Playlist to Show Metabox
// - Playlist Information Metabox
// - Assign DJ to Show Metabox
// === Schedule Overrides ===
// - Schedule Override Metabox


// ------------------
// === Post Types ===
// ------------------

// -------------------
// Register Post Types
// -------------------
// create post types for playlists and shows
function radio_station_create_post_types() {

	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/playlist-menu-icon.png';
	$icon = plugins_url( 'images/playlist-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type( 'playlist',
		array(
			'labels' => array(
				'name'			=> __( 'Playlists', 'radio-station' ),
				'singular_name'	=> __( 'Playlist', 'radio-station' ),
				'add_new'		=> __( 'Add Playlist', 'radio-station' ),
				'add_new_item'	=> __( 'Add Playlist', 'radio-station' ),
				'edit_item'		=> __( 'Edit Playlist', 'radio-station' ),
				'new_item'		=> __( 'New Playlist', 'radio-station' ),
				'view_item'		=> __( 'View Playlist', 'radio-station' )
			),
			'show_ui'			=> true,
			'show_in_menu'		=> false, // now added to main menu
			'description'		=> __('Post type for Playlist descriptions', 'radio-station'),
			'menu_position'		=> 5,
			'menu_icon'			=> $icon,
			'public'			=> true,
			'hierarchical'		=> false,
			'supports'			=> array( 'title', 'editor', 'comments' ),
			'can_export'		=> true,
			'has_archive'		=> 'playlists-archive',
			'rewrite'			=> array('slug' => 'playlists'),
			'capability_type'	=> 'playlist',
			'map_meta_cap'		=> true
		)
	);

	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png';
	$icon = plugins_url( 'images/show-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type( 'show',
		array(
			'labels' => array(
				'name'			=> __( 'Shows', 'radio-station' ),
				'singular_name'	=> __( 'Show', 'radio-station' ),
				'add_new'		=> __( 'Add Show', 'radio-station' ),
				'add_new_item'	=> __( 'Add Show', 'radio-station' ),
				'edit_item'		=> __( 'Edit Show', 'radio-station' ),
				'new_item'		=> __( 'New Show', 'radio-station' ),
				'view_item'		=> __( 'View Show', 'radio-station' )
			),
			'show_ui'			=> true,
			'show_in_menu'		=> false, // now added to main menu
			'description'		=> __('Post type for Show descriptions', 'radio-station'),
			'menu_position'		=> 5,
			'menu_icon'			=> $icon,
			'public'			=> true,
			'taxonomies'		=> array( 'genres' ),
			'hierarchical'		=> false,
			'supports'			=> array( 'title', 'editor', 'thumbnail', 'comments' ),
			'can_export'		=> true,
			'capability_type'	=> 'show',
			'map_meta_cap'		=> true
		)
	);

	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png';
	$icon = plugins_url( 'images/show-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type( 'override',
		array(
				'labels' => array(
					'name'			=> __( 'Schedule Override', 'radio-station' ),
					'singular_name' => __( 'Schedule Override', 'radio-station' ),
					'add_new'		=> __( 'Add Schedule Override', 'radio-station' ),
					'add_new_item'	=> __( 'Add Schedule Override', 'radio-station' ),
					'edit_item'		=> __( 'Edit Schedule Override', 'radio-station' ),
					'new_item'		=> __( 'New Schedule Override', 'radio-station' ),
					'view_item'		=> __( 'View Schedule Override', 'radio-station' )
				),
				'show_ui'			=> true,
				'show_in_menu'		=> false, // now added to main menu
				'description' =>	__('Post type for Schedule Override', 'radio-station'),
				'menu_position'		=> 5,
				'menu_icon'			=> $icon,
				'public'			=> true,
				'hierarchical'		=> false,
				'supports'			=> array( 'title', 'thumbnail' ),
				'can_export'		=> true,
				'rewrite'			=> array('slug' => 'show-override'),
				'capability_type'	=> 'show',
				'map_meta_cap'		=> true
		)
	);
}
add_action( 'init', 'radio_station_create_post_types' );

// --------------------------
// Add Show Thumbnail Support
// --------------------------
// add featured image support to "show" post type
// (this is probably no longer necessary as declared in register_post_type for show)
function radio_station_add_featured_image_support() {
    $supportedTypes = get_theme_support( 'post-thumbnails' );

    if ( $supportedTypes === false ) {
        add_theme_support( 'post-thumbnails', array( 'show' ) );
    } elseif ( is_array( $supportedTypes ) ) {
        $supportedTypes[0][] = 'show';
        add_theme_support( 'post-thumbnails', $supportedTypes[0] );
    }
}
add_action( 'init', 'radio_station_add_featured_image_support' );

// -----------------------
// Register Genre Taxonomy
// -----------------------
// create custom taxonomy for the Show post type
function radio_station_myplaylist_create_show_taxonomy() {

	// add taxonomy labels
	$labels = array(
		'name'              => _x( 'Genres', 'taxonomy general name', 'radio-station' ),
		'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'radio-station' ),
		'search_items'      => __( 'Search Genres', 'radio-station' ),
		'all_items'         => __( 'All Genres', 'radio-station' ),
		'parent_item'       => __( 'Parent Genre', 'radio-station' ),
		'parent_item_colon' => __( 'Parent Genre:', 'radio-station' ),
		'edit_item'         => __( 'Edit Genre', 'radio-station' ),
		'update_item'       => __( 'Update Genre', 'radio-station' ),
		'add_new_item'      => __( 'Add New Genre', 'radio-station' ),
		'new_item_name'     => __( 'New Genre Name', 'radio-station' ),
		'menu_name'         => __( 'Genre', 'radio-station' ),
	);

	// register the genre taxonomy
	register_taxonomy( 'genres', array( 'show' ),
		array(
			'hierarchical'	=> true,
			'labels'		=> $labels,
			'public'		=> true,
			'show_tagcloud'	=> false,
			'query_var'		=> true,
			'rewrite'		=> array('slug' => 'genre'),
			'capabilities' => array(
				'manage_terms'	=> 'edit_shows',
				'edit_terms'	=> 'edit_shows',
				'delete_terms'	=> 'edit_shows',
				'assign_terms'	=> 'edit_shows'
			),
		)
	);

}
add_action('init', 'radio_station_myplaylist_create_show_taxonomy');


// -----------------
// === Playlists ===
// -----------------

// ---------------------
// Playlist Data Metabox
// ---------------------

// Add custom repeating meta field for the playlist edit form... Stores multiple associated values as a serialized string
// Borrowed and adapted from http://wordpress.stackexchange.com/questions/19838/create-more-meta-boxes-as-needed/19852#19852
function radio_station_myplaylist_add_custom_box() {
	add_meta_box(
        'dynamic_sectionid',
		__( 'Playlist Entries', 'radio-station' ),
        'radio_station_myplaylist_inner_custom_box',
        'playlist'
	);
}
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_custom_box', 1 );

// Prints the playlist entry box to the main column on the edit screen
function radio_station_myplaylist_inner_custom_box() {

	global $post;

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
	?>
    <div id="meta_inner">
    <?php

    // get the saved meta as an arry
    $entries = get_post_meta( $post->ID, 'playlist', false );
	//print_r($prices);
    $c = 1;

    echo '<table id="here" class="widefat">';
    echo "<tr>";
    echo "<th></th><th>".__('Artist', 'radio-station')."</th><th>".__('Song', 'radio-station')."</th><th>".__('Album', 'radio-station')."</th><th>".__('Record Label', 'radio-station')."</th><th>".__('DJ Comments', 'radio-station')."</th><th>".__('New', 'radio-station')."</th><th>".__('Status', 'radio-station')."</th><th>".__('Remove', 'radio-station')."</th>";
    echo "</tr>";

    if ( isset( $entries[0] ) && !empty( $entries[0] ) ) {

        foreach( $entries[0] as $track ){
            if ( isset( $track['playlist_entry_artist'] ) || isset( $track['playlist_entry_song'] )
              || isset( $track['playlist_entry_album'] ) || isset( $track['playlist_entry_label'] )
              || isset( $track['playlist_entry_comments'] ) || isset( $track['playlist_entry_new'] )
              || isset( $track['playlist_entry_status'] ) ) {

                echo '<tr>';
                echo '<td>'.$c.'</td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_artist]" value="'.$track['playlist_entry_artist'].'" /></td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_song]" value="'.$track['playlist_entry_song'].'" /></td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_album]" value="'.$track['playlist_entry_album'].'" /></td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_label]" value="'.$track['playlist_entry_label'].'" /></td>';
                echo '<td><textarea name="playlist['.$c.'][playlist_entry_comments]">'.$track['playlist_entry_comments'].'</textarea></td>';

                echo '<td><input type="checkbox" name="playlist['.$c.'][playlist_entry_new]"';
				if(isset($track['playlist_entry_new']) && $track['playlist_entry_new']) {
					echo ' checked="checked"';
				}
				echo ' /></td>';

                echo '<td>';
                echo '<select name="playlist['.$c.'][playlist_entry_status]">';

                echo '<option value="queued"';
                if ( $track['playlist_entry_status'] == 'queued' ) {echo ' selected="selected"';}
                echo '>'.__('Queued', 'radio-station').'</option>';

                echo '<option value="played"';
                if($track['playlist_entry_status'] == "played") {echo ' selected="selected"';}
                echo '>'.__('Played', 'radio-station').'</option>';

                echo '</select></td>';

                echo '<td><span class="remove button-secondary" style="cursor: pointer;">'.__('Remove', 'radio-station').'</span></td>';
                echo '</tr>';
                $c++;
            }
        }
    }
    echo '</table>';

    ?>
<a class="add button-primary" style="cursor: pointer; float: right; margin-top: 5px;"><?php echo __('Add Entry', 'radio-station'); ?></a>
<div style="clear: both;"></div>
<script>
    var shiftadda =jQuery.noConflict();
    shiftadda(document).ready(function() {
        var count = <?php echo $c; ?>;
        shiftadda(".add").click(function() {

        	shiftadda('#here').append('<tr><td>'+count+'</td><td><input type="text" name="playlist['+count+'][playlist_entry_artist]" value="" /></td><td><input type="text" name="playlist['+count+'][playlist_entry_song]" value="" /></td><td><input type="text" name="playlist['+count+'][playlist_entry_album]" value="" /></td><td><input type="text" name="playlist['+count+'][playlist_entry_label]" value="" /></td><td><textarea name="playlist['+count+'][playlist_entry_comments]"></textarea></td><td><input type="checkbox" name="playlist['+count+'][playlist_entry_new]" /></td><td><select name="playlist['+count+'][playlist_entry_status]"><option value="queued"><?php _e('Queued', 'radio-station'); ?></option><option value="played"><?php _e('Played', 'radio-station'); ?></option></select></td><td><span class="remove button-secondary" style="cursor: pointer;"><?php _e('Remove', 'radio-station'); ?></span></td></tr>' );
            count = count + 1;
            return false;
        });
        shiftadda(".remove").live('click', function() {
        	shiftadda(this).parent().parent().remove();
        });
    });
    </script>
</div>

<div id="publishing-action-bottom">
	<br /><br />
	<?php
	$can_publish = current_user_can('publish_playlists');
	//borrowed from wp-admin/includes/meta-boxes.php
	if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
		if ( $can_publish ) :
		if ( !empty($post->post_date_gmt) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) : ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule','radio-station'); ?>" />
			<?php submit_button( __( 'Schedule' ), 'primary', 'publish', false, array( 'tabindex' => '50', 'accesskey' => 'o' ) ); ?>
	<?php	else : ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish', 'radio-station'); ?>" />
			<?php submit_button( __( 'Publish' ), 'primary', 'publish', false, array( 'tabindex' => '50', 'accesskey' => 'o' ) ); ?>
	<?php	endif;
		else : ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review', 'radio-station'); ?>" />
			<?php submit_button( __( 'Update Playlist' ), 'primary', 'publish', false, array( 'tabindex' => '50', 'accesskey' => 'o' ) ); ?>
	<?php
		endif;
	} else { ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update', 'radio-station'); ?>" />
			<input name="save" type="submit" class="button-primary" id="publish" tabindex="50" accesskey="o" value="<?php esc_attr_e('Update Playlist', 'radio-station'); ?>" />
	<?php
	} ?>
</div>

<?php
}

// --- When a playlist is saved, saves our custom data ---
function radio_station_myplaylist_save_postdata( $post_id ) {

	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

	if ( isset( $_POST['playlist'] ) || isset( $_POST['playlist_show_id'] ) ) {

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( isset( $_POST['dynamicMeta_noncename'] ) ) {
			if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) )
				return;
		} else {return;}

		if ( isset( $_POST['dynamicMetaShow_noncename'] ) ) {
			if ( !wp_verify_nonce( $_POST['dynamicMetaShow_noncename'], plugin_basename( __FILE__ ) ) )
				return;
		} else {return;}

		// OK, we are authenticated: we need to find and save the data
		$playlist = isset($_POST['playlist']) ? $_POST['playlist'] : array();

		// move songs that are still queued to the end of the list so that order is maintained
		foreach ( $playlist as $i => $song ) {
			if ( $song['playlist_entry_status'] == 'queued' ) {
				$playlist[] = $song;
				unset($playlist[$i]);
			}
		}
		update_post_meta($post_id, 'playlist', $playlist);

		// sanitize and save show ID
		$show = $_POST['playlist_show_id'];
		if ( $show == '') {
			delete_post_meta( $post_id, 'playlist_show_id' );
		} else {
			$show = absint( $show );
			if ( $show > 0 ) {
				update_post_meta( $post_id, 'playlist_show_id', $show );
			}
		}
	}

}
add_action( 'save_post', 'radio_station_myplaylist_save_postdata' );


// -------------
// === Shows ===
// -------------

// --------------------
// Related Show Metabox
// --------------------

// --- Add custom meta box for show assigment on blog posts ---
function radio_station_add_showblog_box() {

	// make sure a show exists before adding metabox
	$args = array(
		'numberposts'     => -1,
		'offset'          => 0,
		'orderby'         => 'post_title',
		'order'           => 'ASC',
		'post_type'       => 'show',
		'post_status'     => 'publish'
	);
	$shows = get_posts( $args );

	if ( count( $shows ) > 0 ) {

		// add a filter for which post types to show metabox on
		$post_types = apply_filters( 'radio_station_show_related_post_types', array( 'post' ) );

		add_meta_box(
			'dynamicShowBlog_sectionid',
			__( 'Related to Show', 'radio-station' ),
			'radio_station_inner_showblog_custom_box',
			$post_types,
			'side'
		);
	}
}
add_action( 'add_meta_boxes', 'radio_station_add_showblog_box' );

// --- Prints the box content for the Show field ---
function radio_station_inner_showblog_custom_box() {
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
	$shows = get_posts( $args );
	$current = get_post_meta( $post->ID, 'post_showblog_id', true);

	?>
    <div id="meta_inner">

    <select name="post_showblog_id">
    	<option value=""></option>
    <?php
    	foreach ($shows as $show) {
    		if ( $show->ID == $current ) {$selected = ' selected="selected"';} else {$selected = '';}
    		echo '<option value="'.$show->ID.'"'.$selected.'>'.$show->post_title.'</option>';
    	}
	?>
	</select>
	</div>
   <?php
}

// --- When a playlist is saved, saves our custom data ---
function radio_station_save_postdata( $post_id ) {

    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

    if ( isset( $_POST['post_showblog_id'] ) ) {
	    // verify this came from the our screen and with proper authorization,
	    // because save_post can be triggered at other times

	    if ( isset( $_POST['dynamicMetaShowBlog_noncename'] ) ) {
	    	if ( !wp_verify_nonce( $_POST['dynamicMetaShowBlog_noncename'], plugin_basename( __FILE__ ) ) )
	    	return;
	    } else {return;}

	    // OK, we are authenticated: we need to find and save the data
	    $show = $_POST['post_showblog_id'];

	    if ( $show == '' ) {
	    	// remove show from post
			delete_post_meta( $post_id, 'post_showblog_id');
		} else {
			// sanitize to numeric before updating
			$show = absint($show);
			if ( $show > 0 ) {update_post_meta( $post_id, 'post_showblog_id', $show );}
		}
    }

}
add_action( 'save_post', 'radio_station_save_postdata' );


// -------------------------------
// Assign Playlist to Show Metabox
// -------------------------------

// --- Add custom meta box for show assigment ---
function radio_station_myplaylist_add_show_box() {
	add_meta_box(
        'dynamicShow_sectionid',
		__( 'Show', 'radio-station' ),
        'radio_station_myplaylist_inner_show_custom_box',
        'playlist',
		'side'
	);
}
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_show_box' );

// --- Prints the box content for the Show field ---
function radio_station_myplaylist_inner_show_custom_box() {

	global $post, $wpdb;

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaShow_noncename' );

	$user = wp_get_current_user();

	// exclude administrators... they should be able to do whatever they want
	if ( !in_array('administrator', $user->roles ) ) {

		// get the user lists for all shows
	    $allowed_shows = array();

	    $show_user_lists = $wpdb->get_results( "SELECT pm.meta_value, pm.post_id FROM {$wpdb->postmeta} pm WHERE pm.meta_key = 'show_user_list'" );

	    // check each list for the current user
	    foreach($show_user_lists as $list) {

	    	$list->meta_value = unserialize( $list->meta_value );

	    	// if a list has no users, unserialize() will return false instead of an empty array... fix that to prevent errors in the foreach loop.
	    	if ( !is_array( $list->meta_value ) ) {$list->meta_value = array();}

	    	// only include shows the user is assigned to
	    	foreach ( $list->meta_value as $user_id ) {
	    		if ( $user->ID == $user_id ) {
	    			$allowed_shows[] = $list->post_id;
	    		}
	    	}
	    }

		$args = array(
			'numberposts'     => -1,
			'offset'          => 0,
			'orderby'         => 'post_title',
			'order'           => 'aSC',
			'post_type'       => 'show',
			'post_status'     => 'publish',
			'include'		  => implode( ',', $allowed_shows )
		);

		$shows = get_posts( $args );

	} else {
		// you are an administrator
		$args = array(
				'numberposts'     => -1,
				'offset'          => 0,
				'orderby'         => 'post_title',
				'order'           => 'aSC',
				'post_type'       => 'show',
				'post_status'     => 'publish'
		);

		$shows = get_posts( $args );
	}

	$current = get_post_meta( $post->ID, 'playlist_show_id', true );

	?>
    <div id="meta_inner">

    <select name="playlist_show_id">
    <?php
    	if ( !$current ) {$selected = ' selected="selected"';} else {$selected = '';}
    	echo '<option value=""'.$selected.'>'.__( 'Unassigned', 'radio-station' ).'</option>';
    	foreach ( $shows as $show ) {
    		if ( $show->ID == $current ) {$selected = ' selected="selected"';} else {$selected = '';}
    		echo '<option value="'.$show->ID.'"'.$selected.'>'.$show->post_title.'</option>';
    	}
	?>
	</select>
	</div>
   <?php
}

// ----------------------------
// Playlist Information Metabox
// ----------------------------
// Adds a box to the side column of the show edit screens
function radio_station_myplaylist_add_metainfo_box() {
	add_meta_box(
        'dynamicShowMeta_sectionid',
		__( 'Information', 'radio-station' ),
        'radio_station_myplaylist_inner_metainfo_custom_box',
        'show');
}
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_metainfo_box' );

// --- Prints the box for additional meta data for the Show post type ---
function radio_station_myplaylist_inner_metainfo_custom_box() {

	global $post;

	$file = get_post_meta( $post->ID, 'show_file', true );
	$email = get_post_meta( $post->ID, 'show_email', true );
	$active = get_post_meta( $post->ID, 'show_active', true );
	$link = get_post_meta( $post->ID, 'show_link', true );

	// added max-width to prevent metabox overflows
	?>
    <div id="meta_inner">

    <p><label><?php _e('Active', 'radio-station'); ?></label>
    <input type="checkbox" name="show_active" <?php if ( $active == 'on' ) {echo 'checked="checked"';} ?>  /><br />
    <em><?php _e('Check this box if show is currently active (Show will not appear on programming schedule if unchecked)', 'radio-station'); ?></em></p>

    <p><label><?php _e('Current Audio File', 'radio-station'); ?>:</label><br />
    <input type="text" name="show_file" size="100" style="max-width:100%;" value="<?php echo $file; ?>" /></p>

    <p><label><?php _e('DJ Email', 'radio-station'); ?>:</label><br />
    <input type="text" name="show_email" size="100" style="max-width:100%;" value="<?php echo $email; ?>" /></p>

    <p><label><?php _e('Website Link', 'radio-station'); ?>:</label><br />
    <input type="text" name="show_link" size="100" style="max-width:100%;" value="<?php echo $link; ?>" /></p>

	</div>
   <?php
}


// --------------------------
// Assign DJs to Show Metabox
// --------------------------

// --- Adds a box to the side column of the show edit screens ---
function radio_station_myplaylist_add_user_box() {
	add_meta_box(
        'dynamicUser_sectionid',
		__( 'DJs', 'radio-station' ),
        'radio_station_myplaylist_inner_user_custom_box',
        'show',
		'side'
	);
}
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_user_box' );

// Prints the box for user assignement for the Show post type
function radio_station_myplaylist_inner_user_custom_box() {

	global $post, $wp_roles, $wpdb;

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaUser_noncename' );

	// check for roles that have the edit_shows capability enabled
	$add_roles = array( 'dj' );
	foreach ( $wp_roles->roles as $name => $role ) {
		foreach($role['capabilities'] as $capname => $capstatus) {
			if ( ( $capname == 'edit_shows' ) && ( ( $capstatus == 1 ) || ( $capstatus == true ) ) ) {
				$add_roles[] = $name;
			}
		}
	}
	$add_roles = array_unique( $add_roles );

	// create the meta query for get_users()
	$meta_query = array( 'relation' => 'OR' );
	foreach ( $add_roles as $role ) {
		$meta_query[] = array(
			'key' => $wpdb->prefix.'capabilities',
			'value' => $role,
			'compare' => 'like'
		);
	}

	// get all eligible users
	$args = array(
		'meta_query' => $meta_query,
		'orderby' => 'display_name',
		'order' => 'ASC',
		//'fields' => array('ID, display_name'),
	);
	$users = get_users( $args );

	// get the DJs currently assigned to the show
	$current = get_post_meta( $post->ID, 'show_user_list', true);
	if ( !$current ) {$current = array();}

	// move any selected DJs to the top of the list
	foreach ( $users as $i => $dj ) {
    	if ( in_array($dj->ID, $current ) ) {
    		unset( $users[$i] ); //unset first, or prepending will change the index numbers and cause you to delete the wrong item
    		array_unshift( $users, $dj );  //prepend the user to the beginning of the array
    	}
	}

	?>
    <div id="meta_inner">

    <select name="show_user_list[]" multiple="multiple" style="height: 150px;">
    	<option value=""></option>
    <?php
    	foreach( $users as $dj ) {
    		if ( in_array( $dj->ID, $current ) ) {$selected = ' selected="selected"';} else {$selected = '';}
    		echo '<option value="'.$dj->ID.'"'.$selected.'>'.$dj->display_name.'</option>';
    	}
	?>
	</select>
	</div>
   <?php
}

// --- Adds a box to the side column of the show edit screens ---
function radio_station_myplaylist_add_sched_box() {
	add_meta_box(
        'dynamicSched_sectionid',
		__( 'Schedules', 'radio-station' ),
        'radio_station_myplaylist_inner_sched_custom_box',
        'show'
	);
}
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_sched_box' );

function radio_station_myplaylist_inner_sched_custom_box() {

	global $post;

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSched_noncename' );
	?>
	    <div id="meta_inner">
	    <?php

	    // get the saved meta as an array
	    $shifts = get_post_meta( $post->ID, 'show_sched', false);

		//print_r($shifts);

	    $c = 0;
	    if ( isset( $shifts[0] ) && is_array( $shifts[0] ) ) {
	        foreach ( $shifts[0] as $track ){
	            if ( isset( $track['day'] ) || isset( $track['time'] ) ){
	            	?>
	            	<p>
	            		<?php _e('Day', 'radio-station'); ?>:
	            		<select name="show_sched[<?php echo $c; ?>][day]">
	            			<option value=""></option>
	            			<option value="Monday"<?php if($track['day'] == "Monday") { echo ' selected="selected"'; } ?>><?php _e('Monday', 'radio-station'); ?></option>
	            			<option value="Tuesday"<?php if($track['day'] == "Tuesday") { echo ' selected="selected"'; } ?>><?php _e('Tuesday', 'radio-station'); ?></option>
	            			<option value="Wednesday"<?php if($track['day'] == "Wednesday") { echo ' selected="selected"'; } ?>><?php _e('Wednesday', 'radio-station'); ?></option>
	            			<option value="Thursday"<?php if($track['day'] == "Thursday") { echo ' selected="selected"'; } ?>><?php _e('Thursday', 'radio-station'); ?></option>
	            			<option value="Friday"<?php if($track['day'] == "Friday") { echo ' selected="selected"'; } ?>><?php _e('Friday', 'radio-station'); ?></option>
	            			<option value="Saturday"<?php if($track['day'] == "Saturday") { echo ' selected="selected"'; } ?>><?php _e('Saturday', 'radio-station'); ?></option>
	            			<option value="Sunday"<?php if($track['day'] == "Sunday") { echo ' selected="selected"'; } ?>><?php _e('Sunday', 'radio-station'); ?></option>
	            		</select>
	            		 -
	            		<?php _e('Start Time', 'radio-station'); ?>:
	            		<select name="show_sched[<?php echo $c; ?>][start_hour]">
	            			<option value=""></option>
	            		<?php for ( $i=1; $i <= 12; $i++ ) {
	            			if ($track['start_hour'] == $i) {$selected = ' selected="selected"';} else {$selected = '';}
	            			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	            		} ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][start_min]">
	            			<option value=""></option>
	            		<?php for ( $i = 0; $i < 60; $i++ ){
								$min = $i;
								if ($i < 10) {$min = '0'.$i;}
								if ( $track['start_min'] == $min ) {$selected = ' selected="selected"';} else {$selected = '';}
	            				echo '<option value="'.$min.'"'.$selected.'>'.$min.'</option>';
	            		} ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][start_meridian]">
	            			<option value=""></option>
	            			<option value="am"<?php if($track['start_meridian'] == "am") { echo ' selected="selected"'; } ?>>am</option>
	            			<option value="pm"<?php if($track['start_meridian'] == "pm") { echo ' selected="selected"'; } ?>>pm</option>
	            		</select>

	            		 -
	            		<?php _e('End Time', 'radio-station'); ?>:
	            		<select name="show_sched[<?php echo $c; ?>][end_hour]">
	            			<option value=""></option>
	            		<?php for ( $i = 1; $i <= 12; $i++ ) {
	            			if ( $track['end_hour'] == $i ) {$selected = ' selected="selected"';} else {$selected = '';}
	            			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	            		} ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][end_min]">
	            			<option value=""></option>
	            		<?php for ( $i = 0; $i < 60; $i++ ) {
							$min = $i;
							if ( $i < 10 ) {$min = '0'.$i;}
							if ( $track['end_min'] == $min ) {$selected = ' selected="selected"';} else {$selected = '';}
							echo '<option value="'.$min.'"'.$selected.'>'.$min.'</option>';
	            		} ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][end_meridian]">
	            			<option value=""></option>
	            			<option value="am"<?php if ( $track['end_meridian'] == "am" ) {echo ' selected="selected"';} ?>>am</option>
	            			<option value="pm"<?php if ( $track['end_meridian'] == "pm" ) {echo ' selected="selected"';} ?>>pm</option>
	            		</select>
	            		<input type="checkbox" name="show_sched[<?php echo $c; ?>][encore]" <?php if ( isset( $track['encore'] ) && ( $track['encore'] == 'on' ) ) {echo 'checked="checked"';} ?>/> <?php _e( 'Encore Presentation', 'radio-station' ); ?>
	            		<span class="remove button-secondary" style="cursor: pointer;"><?php _e( 'Remove', 'radio-station' ); ?></span>
	            	</p>
	            	<?php
	                $c++;
	            }
	        }
	    }

	    ?>
	<span id="here"></span>
	<a class="add button-primary" style="cursor: pointer; display:block; width: 150px; padding: 8px; text-align: center; line-height: 1em;"><?php echo __('Add Shift', 'radio-station'); ?></a>
	<script>
	    var shiftaddb =jQuery.noConflict();
	    shiftaddb(document).ready(function() {
	        var count = <?php echo $c; ?>;
	        shiftaddb(".add").click(function() {
	            count = count + 1;
				output = '<p><?php _e( 'Day', 'radio-station' ); ?>: ';
				output += '<select name="show_sched[' + count + '][day]">';
				output += '<option value=""></option>';
				output += '<option value="Monday"><?php _e( 'Monday', 'radio-station' ); ?></option>';
				output += '<option value="Tuesday"><?php _e( 'Tuesday', 'radio-station' ); ?></option>';
				output += '<option value="Wednesday"><?php _e( 'Wednesday', 'radio-station' ); ?></option>';
				output += '<option value="Thursday"><?php _e( 'Thursday', 'radio-station' ); ?></option>';
				output += '<option value="Friday"><?php _e( 'Friday', 'radio-station' ); ?></option>';
				output += '<option value="Saturday"><?php _e( 'Saturday', 'radio-station' ); ?></option>';
				output += '<option value="Sunday"><?php _e( 'Sunday', 'radio-station' ); ?></option>';
				output += '</select>';
				output += ' - <?php _e('Start Time', 'radio-station'); ?>: ';

				output += '<select name="show_sched[' + count + '][start_hour]">';
				output += '<option value=""></option>';
	    		<?php for ( $i = 1; $i <= 12; $i++) { ?>
    			output += '<option value="<?php echo $i; ?>"><?php echo $i; ?></option>';
				<?php } ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_min]">';
				output += '<option value=""></option>';
				<?php for ($i=0; $i<60; $i++) :
					$min = $i;
					if ($i < 10) {$min = '0'.$i;}
				?>
    			output += '<option value="<?php echo $min; ?>"><?php echo $min; ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_meridian]">';
				output += '<option value=""></option>';
    			output += '<option value="am">am</option>';
    			output += '<option value="pm">pm</option>';
				output += '</select> ';

				output += ' - <?php _e( 'End Time', 'radio-station' ); ?>: ';
				output += '<select name="show_sched[' + count + '][end_hour]">';
				output += '<option value=""></option>';
				<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
				output += '<option value="<?php echo $i; ?>"><?php echo $i; ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_min]">';
				output += '<option value=""></option>';
				<?php for ( $i = 0; $i < 60 ; $i++ ) :
					$min = $i;
					if($i < 10) {$min = '0'.$i;}
				?>
    			output += '<option value="<?php echo $min; ?>"><?php echo $min; ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_meridian]">';
				output += '<option value=""></option>';
    			output += '<option value="am">am</option>';
    			output += '<option value="pm">pm</option>';
				output += '</select> ';

				output += '<input type="checkbox" name="show_sched[' + count + '][encore]" /> <?php _e( 'Encore Presentation', 'radio-station' ); ?> ';

				output += '<span class="remove button-secondary" style="cursor: pointer;"><?php _e( 'Remove', 'radio-station' ); ?></span></p>';
				shiftaddb('#here').append( output );

	            return false;
	        });
	        shiftaddb(".remove").live('click', function() {
	        	shiftaddb(this).parent().remove();
	        });
	    });
	    </script>
	</div>
<?php
}

// --- save the custom fields when a show is saved ---
function radio_station_myplaylist_save_showpostdata( $post_id ) {

	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( isset( $_POST['dynamicMetaUser_noncename'] ) ){
		if ( !wp_verify_nonce( $_POST['dynamicMetaUser_noncename'], plugin_basename( __FILE__ ) ) )
		return;
	} else {return;}

	if ( isset( $_POST['dynamicMetaSched_noncename'] ) ) {
		if ( !wp_verify_nonce( $_POST['dynamicMetaSched_noncename'], plugin_basename( __FILE__ ) ) )
		return;
	} else {return;}

	// OK, we are authenticated: we need to find and save the data
	$djs = $_POST['show_user_list'];
	$sched = $_POST['show_sched'];
	$file = $_POST['show_file'];
	$email = $_POST['show_email'];
	$active = $_POST['show_active'];
	$link = $_POST['show_link'];

	update_post_meta( $post_id, 'show_user_list', $djs );
	update_post_meta( $post_id, 'show_sched', $sched );
	update_post_meta( $post_id, 'show_file', $file );
	update_post_meta( $post_id, 'show_email', $email );
	update_post_meta( $post_id, 'show_active', $active );
	update_post_meta( $post_id, 'show_link', $link );
}
add_action( 'save_post', 'radio_station_myplaylist_save_showpostdata' );


// --------------------------
// === Schedule Overrides ===
// --------------------------

// -------------------------
// Schedule Override Metabox
// -------------------------

// --- Adds a box to the side column of the show edit screens ---
function radio_station_master_override_add_sched_box() {
	add_meta_box(
		'dynamicSchedOver_sectionid',
		__( 'Override Schedule', 'radio-station' ),
		'radio_station_master_override_inner_sched_custom_box',
		'override'
	);
}
add_action( 'add_meta_boxes', 'radio_station_master_override_add_sched_box' );

function radio_station_master_override_inner_sched_custom_box() {

	global $post;

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSchedOver_noncename' );
	?>
	    	<div id="meta_inner" class="sched-override">
		    <?php

		    // get the saved meta as an array
		    $track = get_post_meta($post->ID,'show_override_sched',false);
		    if ($track) {$track = $track[0];}

			?>
			<script type="text/javascript">
			jQuery(document).ready(function() {
			    jQuery('#OverrideDate').datepicker({
			        dateFormat : 'yy-mm-dd'
			    });
			});
			</script>

            	<p>
            		<?php _e('Date', 'radio-station'); ?>:
            		<input type="text" id="OverrideDate" name="show_sched[date]" value="<?php if ( isset( $track['date'] ) && ( $track['date'] != '' ) ) {echo $track['date'];} ?>"/>
            		 -
            		<?php _e('Start Time', 'radio-station'); ?>:
            		<select name="show_sched[start_hour]">
            			<option value=""></option>
            		<?php for ( $i = 1; $i <= 12; $i++ ) {
            			if ( isset( $track['start_hour'] ) && ( $track['start_hour'] == $i ) ) {$selected = ' selected="selected"';} else {$selected = '';}
            			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
            		} ?>
            		</select>
            		<select name="show_sched[start_min]">
            			<option value=""></option>
            		<?php for ( $i = 0; $i < 60; $i++ ) {
						$min = $i;
						if ( $i < 10 ) {$min = '0'.$i;}
						if ( isset( $track['start_min'] ) && ( $track['start_min'] == $min ) ) {$selected = ' selected="selected"';} else {$selected = '';}
            			echo '<option value="'.$min.'"'.$selected.'>'.$min.'</option>';
            		} ?>
            		</select>
            		<select name="show_sched[start_meridian]">
            			<option value=""></option>
            			<option value="am"<?php if ( isset( $track['start_meridian'] ) && ( $track['start_meridian'] == 'am' ) ) {echo ' selected="selected"';} ?>>am</option>
            			<option value="pm"<?php if ( isset( $track['start_meridian'] ) && ( $track['start_meridian'] == 'pm' ) ) {echo ' selected="selected"';} ?>>pm</option>
            		</select>

            		 -
            		<?php _e('End Time', 'radio-station'); ?>:
            		<select name="show_sched[end_hour]">
            			<option value=""></option>
            		<?php for ( $i = 1; $i <= 12; $i++) {
            			if ( isset( $track['end_hour'] ) && ( $track['end_hour'] == $i ) ) {$selected = ' selected="selected"';} else {$selected = '';}
            			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
            		} ?>
            		</select>
            		<select name="show_sched[end_min]">
            			<option value=""></option>
            		<?php for ( $i = 0; $i < 60; $i++ ) {
						$min = $i;
						if ( $i < 10 ) {$min = '0'.$i;}
						if ( isset( $track['end_min'] ) && ( $track['end_min'] == $min ) ) {$selected = ' selected="selected"';} else {$selected = '';}
            			echo '<option value="'.$min.'"'.$selected.'>'.$min.'</option>';
            		} ?>
            		</select>
            		<select name="show_sched[end_meridian]">
            			<option value=""></option>
            			<option value="am"<?php if ( isset( $track['end_meridian'] ) && ( $track['end_meridian'] == 'am' ) ) {echo ' selected="selected"'; } ?>>am</option>
            			<option value="pm"<?php if ( isset( $track['end_meridian'] ) && ( $track['end_meridian'] == 'pm' ) ) {echo ' selected="selected"'; } ?>>pm</option>
            		</select>

            	</p>
		</div>
<?php
}

// --- save the custom fields when a show override is saved ---
function radio_station_master_override_save_showpostdata( $post_id ) {

	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( isset( $_POST['dynamicMetaSchedOver_noncename']) ) {
		if ( !wp_verify_nonce( $_POST['dynamicMetaSchedOver_noncename'], plugin_basename( __FILE__ ) ) )
		return;
	} else {return;}

	// OK, we are authenticated: we need to find and save the data
	$sched = $_POST['show_sched'];

	// sanitize value before saving
	$valid = array( '', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
	if ( !in_array( $sched, $valid ) ) {return;}
	if ( $sched == '' ) {delete_post_meta( $post_id, 'show_override_sched' );}
	else {update_post_meta( $post_id, 'show_override_sched', $sched );}
}
add_action( 'save_post', 'radio_station_master_override_save_showpostdata' );

