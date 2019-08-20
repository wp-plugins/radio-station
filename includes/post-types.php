<?php
/*
 * Define all new post types and create custom fields for them
 * Author: Nikki Blight
 * Since: 2.0.0
 */


// === Post Types ===
// - Register Post Types
// - Set CPTs to Classic Editor
// - Add Show Thumbnail Support
// - Metaboxes Above Content Area
// === Taxonomies ===
// - Add Genre Taxonomy
// - Shift Genre Metabox
// === Playlists ===
// - Add Playlist Data Metabox
// - Playlist Data Metabox
// - Update Playlist Data
// === Shows ===
// - Add Related Show Metabox
// - Related Shows Metabox
// - Update Related Show
// - Add Assign Playlist to Show Metabox
// - Assign Playlist to Show Metabox
// - Add Playlist Info Metabox
// - Playlist Info Metabox
// - Add Assign DJs to Show Metabox
// - Assign DJs to Show Metabox
// - Add Show Shifts Metabox
// - Show Shifts Metabox
// - Update Show Metadata
// === Schedule Overrides ===
// - Add Schedule Override Metabox
// - Schedule Override Metabox
// - Update Schedule Override


// ------------------
// === Post Types ===
// ------------------

// -------------------
// Register Post Types
// -------------------
// --- create post types for playlists and shows ---
add_action( 'init', 'radio_station_create_post_types' );
function radio_station_create_post_types() {

	// ----
	// Show
	// ----
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png';
	// $icon = plugins_url( 'images/show-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type(
		'show',
		array(
			'labels'          => array(
				'name'          => __( 'Shows', 'radio-station' ),
				'singular_name' => __( 'Show', 'radio-station' ),
				'add_new'       => __( 'Add Show', 'radio-station' ),
				'add_new_item'  => __( 'Add Show', 'radio-station' ),
				'edit_item'     => __( 'Edit Show', 'radio-station' ),
				'new_item'      => __( 'New Show', 'radio-station' ),
				'view_item'     => __( 'View Show', 'radio-station' ),
			),
			'show_ui'         => true,
			'show_in_menu'    => false, // now added to main menu
			'description'     => __( 'Post type for Show descriptions', 'radio-station' ),
			// 'menu_position'	=> 5,
			// 'menu_icon'		=> $icon,
			'public'          => true,
			'taxonomies'      => array( 'genres' ),
			'hierarchical'    => false,
			'supports'        => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'can_export'      => true,
			'capability_type' => 'show',
			'map_meta_cap'    => true,
		)
	);

	// --------
	// Playlist
	// --------
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/playlist-menu-icon.png';
	// $icon = plugins_url( 'images/playlist-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type(
		'playlist',
		array(
			'labels'          => array(
				'name'          => __( 'Playlists', 'radio-station' ),
				'singular_name' => __( 'Playlist', 'radio-station' ),
				'add_new'       => __( 'Add Playlist', 'radio-station' ),
				'add_new_item'  => __( 'Add Playlist', 'radio-station' ),
				'edit_item'     => __( 'Edit Playlist', 'radio-station' ),
				'new_item'      => __( 'New Playlist', 'radio-station' ),
				'view_item'     => __( 'View Playlist', 'radio-station' ),
			),
			'show_ui'         => true,
			'show_in_menu'    => false, // now added to main menu
			'description'     => __( 'Post type for Playlist descriptions', 'radio-station' ),
			// 'menu_position'	=> 5,
			// 'menu_icon'		=> $icon,
			'public'          => true,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'editor', 'comments' ),
			'can_export'      => true,
			'has_archive'     => 'playlists-archive',
			'rewrite'         => array( 'slug' => 'playlists' ),
			'capability_type' => 'playlist',
			'map_meta_cap'    => true,
		)
	);

	// -----------------
	// Schedule Override
	// -----------------
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png';
	// $icon = plugins_url( 'images/show-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type(
		'override',
		array(
			'labels'          => array(
				'name'          => __( 'Schedule Override', 'radio-station' ),
				'singular_name' => __( 'Schedule Override', 'radio-station' ),
				'add_new'       => __( 'Add Schedule Override', 'radio-station' ),
				'add_new_item'  => __( 'Add Schedule Override', 'radio-station' ),
				'edit_item'     => __( 'Edit Schedule Override', 'radio-station' ),
				'new_item'      => __( 'New Schedule Override', 'radio-station' ),
				'view_item'     => __( 'View Schedule Override', 'radio-station' ),
			),
			'show_ui'         => true,
			'show_in_menu'    => false, // now added to main menu
			'description'     => __( 'Post type for Schedule Override', 'radio-station' ),
			// 'menu_position'	=> 5,
			// 'menu_icon'		=> $icon,
			'public'          => true,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'thumbnail' ),
			'can_export'      => true,
			'rewrite'         => array( 'slug' => 'show-override' ),
			'capability_type' => 'show',
			'map_meta_cap'    => true,
		)
	);

	// --- maybe trigger flush of rewrite rules ---
	if ( get_option( 'radio_station_flush_rewrite_rules' ) ) {
		add_action( 'init', 'flush_rewrite_rules', 20 );
		delete_option( 'radio_station_flush_rewrite_rules' );
	}
}

// ---------------------------------------
// Set Post Type Editing to Classic Editor
// ---------------------------------------
// 2.2.2: added so metabox displays can continue to use wide widths
add_filter( 'gutenberg_can_edit_post_type', 'radio_station_post_type_editor', 20, 2 );
add_filter( 'use_block_editor_for_post_type', 'radio_station_post_type_editor', 20, 2 );
function radio_station_post_type_editor( $can_edit, $post_type ) {
	$post_types = array( 'show', 'playlist', 'override' );
	if ( in_array( $post_type, $post_types, true ) ) {
		return false;}
	return $can_edit;
}

// --------------------------
// Add Show Thumbnail Support
// --------------------------
// --- add featured image support to "show" post type ---
// (this is probably no longer necessary as declared in register_post_type for show)
add_action( 'init', 'radio_station_add_featured_image_support' );
function radio_station_add_featured_image_support() {
	$supported_types = get_theme_support( 'post-thumbnails' );

	if ( false === $supported_types ) {
		add_theme_support( 'post-thumbnails', array( 'show' ) );
	} elseif ( is_array( $supported_types ) ) {
		$supported_types[0][] = 'show';
		add_theme_support( 'post-thumbnails', $supported_types[0] );
	}
}

// ----------------------------
// Metaboxes Above Content Area
// ----------------------------
// --- shows plugin metaboxes above editor box for plugin CPTs ---
add_action( 'edit_form_after_title', 'radio_station_top_meta_boxes' );
function radio_station_top_meta_boxes() {
	global $post;
	do_meta_boxes( get_current_screen(), 'top', $post );
}


// ------------------
// === Taxonomies ===
// ------------------

// -----------------------
// Register Genre Taxonomy
// -----------------------
// --- create custom taxonomy for the Show post type ---
add_action( 'init', 'radio_station_myplaylist_create_show_taxonomy' );
function radio_station_myplaylist_create_show_taxonomy() {

	// --- add taxonomy labels ---
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

	// --- register the genre taxonomy ---
	// 2.2.3: added show_admin_column and show_in_quick_edit arguments
	register_taxonomy(
		'genres',
		array( 'show' ),
		array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'public'             => true,
			'show_tagcloud'      => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'genre' ),
			'show_admin_column'  => true,
			'show_in_quick_edit' => true,
			'capabilities'       => array(
				'manage_terms' => 'edit_shows',
				'edit_terms'   => 'edit_shows',
				'delete_terms' => 'edit_shows',
				'assign_terms' => 'edit_shows',
			),
		)
	);

}

// ----------------------------
// Shift Genre Metabox on Shows
// ----------------------------
// --- moves genre metabox above publish box ---
add_action( 'add_meta_boxes_show', 'radio_station_genre_meta_box_order' );
function radio_station_genre_meta_box_order() {
	global $wp_meta_boxes;
	$genres = $wp_meta_boxes['show']['side']['core']['genresdiv'];
	unset( $wp_meta_boxes['show']['side']['core']['genresdiv'] );
	$wp_meta_boxes['show']['side']['high']['genresdiv'] = $genres;
}


// -----------------
// === Playlists ===
// -----------------

// -------------------------
// Add Playlist Data Metabox
// -------------------------
// --- Add custom repeating meta field for the playlist edit form ---
// (Stores multiple associated values as a serialized string)
// Borrowed and adapted from http://wordpress.stackexchange.com/questions/19838/create-more-meta-boxes-as-needed/19852#19852
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_custom_box', 1 );
function radio_station_myplaylist_add_custom_box() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'dynamic_sectionid',
		__( 'Playlist Entries', 'radio-station' ),
		'radio_station_myplaylist_inner_custom_box',
		'playlist',
		'top', // shift to top
		'high'
	);
}

// ---------------------
// Playlist Data Metabox
// ---------------------
// -- prints the playlist entry box to the main column on the edit screen ---
function radio_station_myplaylist_inner_custom_box() {

	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
	?>
	<div id="meta_inner">
	<?php

	// --- get the saved meta as an arry ---
	$entries = get_post_meta( $post->ID, 'playlist', false );
	// print_r($entries);
	$c = 1;

	echo '<table id="here" class="widefat">';
	echo '<tr>';
	echo '<th></th><th><b>' . esc_html__( 'Artist', 'radio-station' ) . '</b></th>';
	echo '<th><b>' . esc_html__( 'Song', 'radio-station' ) . '</b></th>';
	echo '<th><b>' . esc_html__( 'Album', 'radio-station' ) . '</b></th>';
	echo '<th><b>' . esc_html__( 'Record Label', 'radio-station' ) . '</th>';
	// echo "<th><b>".__('DJ Comments', 'radio-station')."</b></th>";
	// echo "<th><b>".__('New', 'radio-station')."</b></th>";
	// echo "<th><b>".__('Status', 'radio-station')."</b></th>";
	// echo "<th><b>".__('Remove', 'radio-station')."</b></th>";
	echo '</tr>';

	if ( isset( $entries[0] ) && ! empty( $entries[0] ) ) {

		foreach ( $entries[0] as $track ) {
			if ( isset( $track['playlist_entry_artist'] ) || isset( $track['playlist_entry_song'] )
			  || isset( $track['playlist_entry_album'] ) || isset( $track['playlist_entry_label'] )
			  || isset( $track['playlist_entry_comments'] ) || isset( $track['playlist_entry_new'] )
			  || isset( $track['playlist_entry_status'] ) ) {

				echo '<tr id="track-' . esc_attr( $c ) . '-rowa">';
				echo '<td>' . esc_html( $c ) . '</td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_artist]" value="' . esc_attr( $track['playlist_entry_artist'] ) . '" /></td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_song]" value="' . esc_attr( $track['playlist_entry_song'] ) . '" /></td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_album]" value="' . esc_attr( $track['playlist_entry_album'] ) . '" /></td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_label]" value="' . esc_attr( $track['playlist_entry_label'] ) . '" /></td>';

				echo '</tr><tr id="track-' . esc_attr( $c ) . '-rowb">';

				echo '<td colspan="3">' . esc_html__( 'Comments', 'radio-station' ) . ' ';
				echo '<input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_comments]" value="' . esc_attr( $track['playlist_entry_comments'] ) . '" style="width:320px;"></td>';

				echo '<td>' . esc_html__( 'New', 'radio-station' ) . ' ';
				echo '<input type="checkbox" name="playlist[' . esc_attr( $c ) . '][playlist_entry_new]" ' . checked( $track['playlist_entry_new'] ) . ' />';

				echo ' ' . esc_html__( 'Status', 'radio-station' ) . ' ';
				echo '<select name="playlist[' . esc_attr( $c ) . '][playlist_entry_status]">';

					echo '<option value="queued" ' . selected( $track['playlist_entry_status'], 'queued' ) . '>' . esc_html__( 'Queued', 'radio-station' ) . '</option>';

					echo '<option value="played" ' . selected( $track['playlist_entry_status'], 'played' ) . '>' . esc_html__( 'Played', 'radio-station' ) . '</option>';

				echo '</select></td>';

				echo '<td align="right"><span id="track-' . esc_attr( $c ) . '" class="remove button-secondary" style="cursor: pointer;">' . esc_html__( 'Remove', 'radio-station' ) . '</span></td>';
				echo '</tr>';
				$c++;
			}
		}
	}
	echo '</table>';

	?>
	<a class="add button-primary" style="cursor: pointer; float: right; margin-top: 5px;"><?php echo esc_html__( 'Add Entry', 'radio-station' ); ?></a>
	<div style="clear: both;"></div>
	<script>
		var shiftadda = jQuery.noConflict();
		shiftadda(document).ready(function() {
			var count = <?php echo esc_attr( $c ); ?>;
			shiftadda('.add').click(function() {

				output = '<tr id="track-'+count+'-rowa"><td>'+count+'</td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_artist]" value="" /></td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_song]" value="" /></td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_album]" value="" /></td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_label]" value="" /></td>';
				output += '</tr><tr id="track-'+count+'-rowb">';
					output += '<td colspan="3"><?php echo esc_html__( 'Comments', 'radio-station' ); ?>: <input type="text" name="playlist['+count+'][playlist_entry_comments]" value="" style="width:320px;"></td>';
					output += '<td><?php echo esc_html__( 'New', 'radio-station' ); ?>: <input type="checkbox" name="playlist['+count+'][playlist_entry_new]" />';
					output += ' <?php echo esc_html__( 'Status', 'radio-station' ); ?>: <select name="playlist['+count+'][playlist_entry_status]">';
						output += '<option value="queued"><?php esc_html_e( 'Queued', 'radio-station' ); ?></option>';
						output += '<option value="played"><?php esc_html_e( 'Played', 'radio-station' ); ?></option>';
					output += '</select></td>';
					output += '<td align="right"><span id="track-'+count+'" class="remove button-secondary" style="cursor: pointer;"><?php esc_html_e( 'Remove', 'radio-station' ); ?></span></td>';
				output += '</tr>';

				shiftadda('#here').append(output);
				count = count + 1;
				return false;
			});
			shiftadda('.remove').live('click', function() {
				rowid = shiftadda(this).attr('id');
				shiftadda('#'+rowid+'-rowa').remove();
				shiftadda('#'+rowid+'-rowb').remove();
			});
		});
		</script>
	</div>

	<div id="publishing-action-bottom">
		<br /><br />
		<?php
		$can_publish = current_user_can( 'publish_playlists' );
		//borrowed from wp-admin/includes/meta-boxes.php
		if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ), true ) || 0 === $post->ID ) {
			if ( $can_publish ) :
				if ( ! empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) :
					?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Schedule', 'radio-station' ); ?>" />
					<?php
					submit_button(
						__( 'Schedule' ),
						'primary',
						'publish',
						false,
						array(
							'tabindex'  => '50',
							'accesskey' => 'o',
						)
					);
					?>
			<?php	else : ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'radio-station' ); ?>" />
				<?php
				submit_button(
					__( 'Publish' ),
					'primary',
					'publish',
					false,
					array(
						'tabindex'  => '50',
						'accesskey' => 'o',
					)
				);
				?>
				<?php
		endif;
			else :
				?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Submit for Review', 'radio-station' ); ?>" />
				<?php
				submit_button(
					__( 'Update Playlist' ),
					'primary',
					'publish',
					false,
					array(
						'tabindex'  => '50',
						'accesskey' => 'o',
					)
				);
				?>
				<?php
			endif;
		} else {
			?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'radio-station' ); ?>" />
				<input name="save" type="submit" class="button-primary" id="publish" tabindex="50" accesskey="o" value="<?php esc_attr_e( 'Update Playlist', 'radio-station' ); ?>" />
			<?php
		}
		?>
	</div>

	<?php
}

// --------------------
// Update Playlist Data
// --------------------
// --- When a playlist is saved, saves our custom data ---
add_action( 'save_post', 'radio_station_myplaylist_save_postdata' );
function radio_station_myplaylist_save_postdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;}

	if ( isset( $_POST['playlist'] ) || isset( $_POST['playlist_show_id'] ) ) {

		// --- verify this came from the our screen and with proper authorization ---
		if ( isset( $_POST['dynamicMeta_noncename'] ) ) {
			if ( ! wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) ) {
				return;
			}
		} else {
			return;}

		if ( isset( $_POST['dynamicMetaShow_noncename'] ) ) {
			if ( ! wp_verify_nonce( $_POST['dynamicMetaShow_noncename'], plugin_basename( __FILE__ ) ) ) {
				return;
			}
		} else {
			return;}

		// OK, we are authenticated: we need to find and save the data
		$playlist = isset( $_POST['playlist'] ) ? $_POST['playlist'] : array();

		// move songs that are still queued to the end of the list so that order is maintained
		foreach ( $playlist as $i => $song ) {
			if ( 'queued' === $song['playlist_entry_status'] ) {
				$playlist[] = $song;
				unset( $playlist[ $i ] );
			}
		}
		update_post_meta( $post_id, 'playlist', $playlist );

		// sanitize and save show ID
		$show = $_POST['playlist_show_id'];
		if ( empty( $show ) ) {
			delete_post_meta( $post_id, 'playlist_show_id' );
		} else {
			$show = absint( $show );
			if ( $show > 0 ) {
				update_post_meta( $post_id, 'playlist_show_id', $show );
			}
		}
	}

}


// -------------
// === Shows ===
// -------------

// ------------------------
// Add Related Show Metabox
// ------------------------
// --- Add custom meta box for show assignment on blog posts ---
add_action( 'add_meta_boxes', 'radio_station_add_showblog_box' );
function radio_station_add_showblog_box() {

	// --- make sure a show exists before adding metabox ---
	$args  = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => 'show',
		'post_status' => 'publish',
	);
	$shows = get_posts( $args );

	if ( count( $shows ) > 0 ) {

		// ---- add a filter for which post types to show metabox on ---
		// TODO: add this filter to plugin documentation
		$post_types = apply_filters( 'radio_station_show_related_post_types', array( 'post' ) );

		// --- add the metabox to post types ---
		add_meta_box(
			'dynamicShowBlog_sectionid',
			__( 'Related to Show', 'radio-station' ),
			'radio_station_inner_showblog_custom_box',
			$post_types,
			'side'
		);
	}
}

// --------------------
// Related Show Metabox
// --------------------
// --- Prints the box content for the Show field ---
function radio_station_inner_showblog_custom_box() {
	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaShowBlog_noncename' );

	$args    = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => 'show',
		'post_status' => 'publish',
	);
	$shows   = get_posts( $args );
	$current = get_post_meta( $post->ID, 'post_showblog_id', true );

	?>
	<div id="meta_inner">

	<select name="post_showblog_id">
		<option value=""></option>
	<?php
		// -- output show selection options ---
	foreach ( $shows as $show ) {
		echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current ) . '>' . esc_html( $show->post_title ) . '</option>';
	}
	?>
	</select>
	</div>
	<?php
}

// -------------------
// Update Related Show
// -------------------
// --- When a post is saved, saves our custom data ---
add_action( 'save_post', 'radio_station_save_postdata' );
function radio_station_save_postdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;}

	if ( isset( $_POST['post_showblog_id'] ) ) {
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( isset( $_POST['dynamicMetaShowBlog_noncename'] ) ) {
			if ( ! wp_verify_nonce( $_POST['dynamicMetaShowBlog_noncename'], plugin_basename( __FILE__ ) ) ) {
				return;
			}
		} else {
			return;}

		// OK, we are authenticated: we need to find and save the data
		$show = $_POST['post_showblog_id'];

		if ( empty( $show ) ) {
			// remove show from post
			delete_post_meta( $post_id, 'post_showblog_id' );
		} else {
			// sanitize to numeric before updating
			$show = absint( $show );
			if ( $show > 0 ) {
				update_post_meta( $post_id, 'post_showblog_id', $show );}
		}
	}

}


// -----------------------------------
// Add Assign Playlist to Show Metabox
// -----------------------------------
// --- Add custom meta box for show assigment ---
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_show_box' );
function radio_station_myplaylist_add_show_box() {
	// 2.2.2: add high priority to shift above publish box
	add_meta_box(
		'dynamicShow_sectionid',
		__( 'Show', 'radio-station' ),
		'radio_station_myplaylist_inner_show_custom_box',
		'playlist',
		'side',
		'high'
	);
}

// -------------------------------
// Assign Playlist to Show Metabox
// -------------------------------
// --- Prints the box content for the Show field ---
function radio_station_myplaylist_inner_show_custom_box() {

	global $post, $wpdb;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaShow_noncename' );

	$user = wp_get_current_user();

	// --- allow administrators to do whatever they want ---
	if ( ! in_array( 'administrator', $user->roles, true ) ) {

		// --- get the user lists for all shows ---
		$allowed_shows = array();

		$show_user_lists = $wpdb->get_results( "SELECT pm.meta_value, pm.post_id FROM {$wpdb->postmeta} pm WHERE pm.meta_key = 'show_user_list'" );

		// ---- check each list for the current user ---
		foreach ( $show_user_lists as $list ) {

			$list->meta_value = maybe_unserialize( $list->meta_value );

			// --- if a list has no users, unserialize() will return false instead of an empty array ---
			// (fix that to prevent errors in the foreach loop)
			if ( ! is_array( $list->meta_value ) ) {
				$list->meta_value = array();}

			// --- only include shows the user is assigned to ---
			foreach ( $list->meta_value as $user_id ) {
				if ( $user->ID === $user_id ) {
					$allowed_shows[] = $list->post_id;
				}
			}
		}

		$args = array(
			'numberposts' => -1,
			'offset'      => 0,
			'orderby'     => 'post_title',
			'order'       => 'aSC',
			'post_type'   => 'show',
			'post_status' => 'publish',
			'include'     => implode( ',', $allowed_shows ),
		);

		$shows = get_posts( $args );

	} else {

		// --- for if you are an administrator ---
		$args = array(
			'numberposts' => -1,
			'offset'      => 0,
			'orderby'     => 'post_title',
			'order'       => 'aSC',
			'post_type'   => 'show',
			'post_status' => 'publish',
		);

		$shows = get_posts( $args );
	}

	?>
	<div id="meta_inner">

	<select name="playlist_show_id">
	<?php
		// --- loop playlist selection options ---
		$current = get_post_meta( $post->ID, 'playlist_show_id', true );
		echo '<option value="" ' . selected( $current, false ) . '>' . esc_html__( 'Unassigned', 'radio-station' ) . '</option>';
	foreach ( $shows as $show ) {
		echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current ) . '>' . esc_html( $show->post_title ) . '</option>';
	}
	?>
	</select>
	</div>
	<?php
}

// -------------------------
// Add Playlist Info Metabox
// -------------------------
// --- Adds a box to the side column of the show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_metainfo_box' );
function radio_station_myplaylist_add_metainfo_box() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'dynamicShowMeta_sectionid',
		__( 'Information', 'radio-station' ),
		'radio_station_myplaylist_inner_metainfo_custom_box',
		'show',
		'top', // shift to top
		'high'
	);
}

// ---------------------
// Playlist Info Metabox
// ---------------------
// --- Prints the box for additional meta data for the Show post type ---
function radio_station_myplaylist_inner_metainfo_custom_box() {

	global $post;

	$file   = get_post_meta( $post->ID, 'show_file', true );
	$email  = get_post_meta( $post->ID, 'show_email', true );
	$active = get_post_meta( $post->ID, 'show_active', true );
	$link   = get_post_meta( $post->ID, 'show_link', true );

	// added max-width to prevent metabox overflows
	?>
	<div id="meta_inner">

	<p><label><?php esc_html_e( 'Active', 'radio-station' ); ?></label>
	<input type="checkbox" name="show_active" <?php checked( $active, 'on' ); ?> />
	<em><?php esc_html_e( 'Check this box if show is currently active (Show will not appear on programming schedule if unchecked)', 'radio-station' ); ?></em></p>

	<p><label><?php esc_html_e( 'Current Audio File', 'radio-station' ); ?>:</label><br />
	<input type="text" name="show_file" size="100" style="max-width:100%;" value="<?php echo esc_attr( $file ); ?>" /></p>

	<p><label><?php esc_html_e( 'DJ Email', 'radio-station' ); ?>:</label><br />
	<input type="text" name="show_email" size="100" style="max-width:100%;" value="<?php echo esc_attr( $email ); ?>" /></p>

	<p><label><?php esc_html_e( 'Website Link', 'radio-station' ); ?>:</label><br />
	<input type="text" name="show_link" size="100" style="max-width:100%;" value="<?php echo esc_url( $link ); ?>" /></p>

	</div>
	<?php
}

// ------------------------------
// Add Assign DJs to Show Metabox
// ------------------------------
// --- Adds a box to the side column of the show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_user_box' );
function radio_station_myplaylist_add_user_box() {
	// 2.2.2: add high priority to show at top of edit sidebar
	add_meta_box(
		'dynamicUser_sectionid',
		__( 'DJs', 'radio-station' ),
		'radio_station_myplaylist_inner_user_custom_box',
		'show',
		'side',
		'high'
	);
}

// --------------------------
// Assign DJs to Show Metabox
// --------------------------
// --- Prints the box for user assignement for the Show post type ---
function radio_station_myplaylist_inner_user_custom_box() {

	global $post, $wp_roles, $wpdb;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaUser_noncename' );

	// --- check for roles that have the edit_shows capability enabled ---
	$add_roles = array( 'dj' );
	foreach ( $wp_roles->roles as $name => $role ) {
		foreach ( $role['capabilities'] as $capname => $capstatus ) {
			if ( 'edit_shows' === $capname && $capstatus ) {
				$add_roles[] = $name;
			}
		}
	}
	$add_roles = array_unique( $add_roles );

	// ---- create the meta query for get_users() ---
	$meta_query = array( 'relation' => 'OR' );
	foreach ( $add_roles as $role ) {
		$meta_query[] = array(
			'key'     => $wpdb->prefix . 'capabilities',
			'value'   => $role,
			'compare' => 'like',
		);
	}

	// --- get all eligible users ---
	$args = array(
		'meta_query' => $meta_query,
		'orderby'    => 'display_name',
		'order'      => 'ASC',
		//' fields' => array( 'ID, display_name' ),
	);
	$users = get_users( $args );

	// --- get the DJs currently assigned to the show ---
	$current = get_post_meta( $post->ID, 'show_user_list', true );
	if ( ! $current ) {
		$current = array();}

	// --- move any selected DJs to the top of the list ---
	foreach ( $users as $i => $dj ) {
		if ( in_array( $dj->ID, $current, true ) ) {
			unset( $users[ $i ] ); // unset first, or prepending will change the index numbers and cause you to delete the wrong item
			array_unshift( $users, $dj );  // prepend the user to the beginning of the array
		}
	}

	// 2.2.2: add fix to make DJ multi-select input full metabox width
	?>
	<div id="meta_inner">

	<select name="show_user_list[]" multiple="multiple" style="height: 150px; width: 100%;">
		<option value=""></option>
	<?php
	foreach ( $users as $dj ) {
		// 2.2.2: set DJ display name maybe with username
		$display_name = $dj->display_name;
		if ( $dj->display_name !== $dj->user_login ) {
			$display_name .= ' (' . $dj->user_login . ')';}
		$checking = in_array( $dj->ID, $current, true );
		echo '<option value="' . esc_attr( $dj->ID ) . '" ' . selected( $checking ) . '>' . esc_html( $display_name ) . '</option>';
	}
	?>
	</select>
	</div>
	<?php
}

// -----------------------
// Add Show Shifts Metabox
// -----------------------
// --- Adds schedule box to show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_sched_box' );
function radio_station_myplaylist_add_sched_box() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'dynamicSched_sectionid',
		__( 'Schedules', 'radio-station' ),
		'radio_station_myplaylist_inner_sched_custom_box',
		'show',
		'top', // shift to top
		'low'
	);
}

// -------------------
// Show Shifts Metabox
// -------------------
function radio_station_myplaylist_inner_sched_custom_box() {

	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSched_noncename' );
	?>
		<div id="meta_inner">
		<?php

		// --- get the saved meta as an array ---
		$shifts = get_post_meta( $post->ID, 'show_sched', false );
		// print_r($shifts);

		$c = 0;
		if ( isset( $shifts[0] ) && is_array( $shifts[0] ) ) {
			foreach ( $shifts[0] as $track ) {
				if ( isset( $track['day'] ) || isset( $track['time'] ) ) {
					?>
					<ul style="list-style:none;">

						<li style="display:inline-block;">
							<?php esc_html_e( 'Day', 'radio-station' ); ?>:
							<select name="show_sched[<?php echo esc_attr( $c ); ?>][day]">
								<option value=""></option>
								<option value="Monday"
								<?php
								if ( 'Monday' === $track['day'] ) {
									echo ' selected="selected"';
								}
								?>
								><?php esc_html_e( 'Monday', 'radio-station' ); ?></option>
								<option value="Tuesday"
								<?php
								if ( 'Tuesday' === $track['day'] ) {
									echo ' selected="selected"';
								}
								?>
								><?php esc_html_e( 'Tuesday', 'radio-station' ); ?></option>
								<option value="Wednesday"
								<?php
								if ( 'Wednesday' === $track['day'] ) {
									echo ' selected="selected"';
								}
								?>
								><?php esc_html_e( 'Wednesday', 'radio-station' ); ?></option>
								<option value="Thursday"
								<?php
								if ( 'Thursday' === $track['day'] ) {
									echo ' selected="selected"';
								}
								?>
								><?php esc_html_e( 'Thursday', 'radio-station' ); ?></option>
								<option value="Friday"
								<?php
								if ( 'Friday' === $track['day'] ) {
									echo ' selected="selected"';
								}
								?>
								><?php esc_html_e( 'Friday', 'radio-station' ); ?></option>
								<option value="Saturday"
								<?php
								if ( 'Saturday' === $track['day'] ) {
									echo ' selected="selected"';
								}
								?>
								><?php esc_html_e( 'Saturday', 'radio-station' ); ?></option>
								<option value="Sunday"
								<?php
								if ( 'Sunday' === $track['day'] ) {
									echo ' selected="selected"';
								}
								?>
								><?php esc_html_e( 'Sunday', 'radio-station' ); ?></option>
							</select>
						</li>

						<li style="display:inline-block; margin-left:20px;">
							<?php esc_html_e( 'Start Time', 'radio-station' ); ?>:
							<select name="show_sched[<?php echo esc_attr( $c ); ?>][start_hour]" style="min-width:35px;">
								<option value=""></option>
							<?php
							for ( $i = 1; $i <= 12; $i++ ) {
								echo '<option value="' . esc_attr( $i ) . '" ' . selected( $track['start_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
							}
							?>
							</select>
							<select name="show_sched[<?php echo esc_attr( $c ); ?>][start_min]" style="min-width:35px;">
								<option value=""></option>
							<?php
							for ( $i = 0; $i < 60; $i++ ) {
									$min = $i;
								if ( $i < 10 ) {
									$min = '0' . $i;}
									echo '<option value="' . esc_attr( $min ) . '" ' . selected( $track['start_min'], $min ) . '>' . esc_html( $min ) . '</option>';
							}
							?>
							</select>
							<select name="show_sched[<?php echo esc_attr( $c ); ?>][start_meridian]" style="min-width:35px;">
								<option value=""></option>
								<option value="am"
								<?php
								if ( 'am' === $track['start_meridian'] ) {
									echo ' selected="selected"';
								}
								?>
								>am</option>
								<option value="pm"
								<?php
								if ( 'pm' === $track['start_meridian'] ) {
									echo ' selected="selected"';
								}
								?>
								>pm</option>
							</select>
						</li>

						<li style="display:inline-block; margin-left:20px;">
							<?php esc_html_e( 'End Time', 'radio-station' ); ?>:
							<select name="show_sched[<?php echo esc_attr( $c ); ?>][end_hour]" style="min-width:35px;">
								<option value=""></option>
							<?php
							for ( $i = 1; $i <= 12; $i++ ) {
								echo '<option value="' . esc_attr( $i ) . '"' . selected( $track['end_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
							}
							?>
							</select>
							<select name="show_sched[<?php echo esc_attr( $c ); ?>][end_min]" style="min-width:35px;">
								<option value=""></option>
							<?php
							for ( $i = 0; $i < 60; $i++ ) {
								$min = $i;
								if ( $i < 10 ) {
									$min = '0' . $i;
								}
								echo '<option value="' . esc_attr( $min ) . '" ' . selected( $track['end_min'], $min ) . '>' . esc_html( $min ) . '</option>';
							}
							?>
							</select>
							<select name="show_sched[<?php echo esc_attr( $c ); ?>][end_meridian]" style="min-width:35px;">
								<option value=""></option>
								<option value="am"
								<?php
								if ( 'am' === $track['end_meridian'] ) {
									echo ' selected="selected"';
								}
								?>
								>am</option>
								<option value="pm"
								<?php
								if ( 'pm' === $track['end_meridian'] ) {
									echo ' selected="selected"';
								}
								?>
								>pm</option>
							</select>
						</li>

						<li style="display:inline-block; margin-left:20px;"><input type="checkbox" name="show_sched[<?php echo esc_attr( $c ); ?>][encore]" <?php checked( $track['encore'], 'on' ); ?> /> <?php esc_html_e( 'Encore Presentation', 'radio-station' ); ?></li>

						<li style="display:inline-block; margin-left:20px;"><span class="remove button-secondary" style="cursor: pointer;"><?php esc_html_e( 'Remove', 'radio-station' ); ?></span></li>

					</ul>
					<?php
					$c++;
				}
			}
		}

		?>
	<span id="here"></span>
	<span style="text-align: center;"><a class="add button-primary" style="cursor: pointer; display:block; width: 150px; padding: 8px; text-align: center; line-height: 1em;"><?php echo esc_html__( 'Add Shift', 'radio-station' ); ?></a></span>
	<script>
		var shiftaddb =jQuery.noConflict();
		shiftaddb(document).ready(function() {
			var count = <?php echo esc_attr( $c ); ?>;
			shiftaddb(".add").click(function() {
				count = count + 1;
				output = '<ul style="list-style:none;">';
				output += '<li style="display:inline-block;">';
				output += '<?php esc_html_e( 'Day', 'radio-station' ); ?>: ';
				output += '<select name="show_sched[' + count + '][day]">';
				output += '<option value=""></option>';
				output += '<option value="Monday"><?php esc_html_e( 'Monday', 'radio-station' ); ?></option>';
				output += '<option value="Tuesday"><?php esc_html_e( 'Tuesday', 'radio-station' ); ?></option>';
				output += '<option value="Wednesday"><?php esc_html_e( 'Wednesday', 'radio-station' ); ?></option>';
				output += '<option value="Thursday"><?php esc_html_e( 'Thursday', 'radio-station' ); ?></option>';
				output += '<option value="Friday"><?php esc_html_e( 'Friday', 'radio-station' ); ?></option>';
				output += '<option value="Saturday"><?php esc_html_e( 'Saturday', 'radio-station' ); ?></option>';
				output += '<option value="Sunday"><?php esc_html_e( 'Sunday', 'radio-station' ); ?></option>';
				output += '</select>';
				output += '</li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<?php esc_html_e( 'Start Time', 'radio-station' ); ?>: ';

				output += '<select name="show_sched[' + count + '][start_hour]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php for ( $i = 1; $i <= 12; $i++ ) { ?>
				output += '<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>';
				<?php } ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_min]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php
				for ( $i = 0; $i < 60; $i++ ) :
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;}
					?>
				output += '<option value="<?php echo esc_attr( $min ); ?>"><?php echo esc_html( $min ); ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_meridian]" style="min-width:35px;">';
				output += '<option value=""></option>';
				output += '<option value="am">am</option>';
				output += '<option value="pm">pm</option>';
				output += '</select> ';
				output += '</li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<?php esc_html_e( 'End Time', 'radio-station' ); ?>: ';
				output += '<select name="show_sched[' + count + '][end_hour]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
				output += '<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_min]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php
				for ( $i = 0; $i < 60; $i++ ) :
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;}
					?>
				output += '<option value="<?php echo esc_attr( $min ); ?>"><?php echo esc_html( $min ); ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_meridian]" style="min-width:35px;">';
				output += '<option value=""></option>';
				output += '<option value="am">am</option>';
				output += '<option value="pm">pm</option>';
				output += '</select> ';
				output += '</li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<input type="checkbox" name="show_sched[' + count + '][encore]" /> <?php esc_html_e( 'Encore Presentation', 'radio-station' ); ?></li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<span class="remove button-secondary" style="cursor: pointer;"><?php esc_html_e( 'Remove', 'radio-station' ); ?></span></li>';

				output += '</ul>';
				shiftaddb('#here').append( output );

				return false;
			});
			shiftaddb(".remove").live('click', function() {
				shiftaddb(this).parent().parent().remove();
			});
		});
		</script>
	</div>
	<?php
}

// --------------------
// Update Show Metadata
// --------------------
// --- save the custom fields when a show is saved ---
add_action( 'save_post', 'radio_station_myplaylist_save_showpostdata' );
function radio_station_myplaylist_save_showpostdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;}

	// --- verify this came from the our screen and with proper authorization ---
	if ( isset( $_POST['dynamicMetaUser_noncename'] ) ) {
		if ( ! wp_verify_nonce( $_POST['dynamicMetaUser_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}
	} else {
		return;}

	if ( isset( $_POST['dynamicMetaSched_noncename'] ) ) {
		if ( ! wp_verify_nonce( $_POST['dynamicMetaSched_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}
	} else {
		return;}

	// --- get the post data to be saved ---
	// 2.2.3: added show metadata value sanitization
	$djs = $_POST['show_user_list'];
	if ( ! is_array( $djs ) ) {
		$djs = array();} else {
		foreach ( $djs as $i => $dj ) {
			if ( ! empty( $dj ) ) {
				$userid = get_user_by( 'id', $dj );
				if ( ! $userid ) {
					unset( $djs[ $i ] );}
			}
		}
		}
		$file   = wp_strip_all_tags( trim( $_POST['show_file'] ) );
		$email  = sanitize_email( trim( $_POST['show_email'] ) );
		$active = $_POST['show_active'];
		if ( ! in_array( $active, array( '', 'on' ), true ) ) {
			$active = '';}
		$link = filter_var( trim( $_POST['show_link'] ), FILTER_SANITIZE_URL );

		// --- update the show metadata ---
		update_post_meta( $post_id, 'show_user_list', $djs );
		update_post_meta( $post_id, 'show_file', $file );
		update_post_meta( $post_id, 'show_email', $email );
		update_post_meta( $post_id, 'show_active', $active );
		update_post_meta( $post_id, 'show_link', $link );

		// --- update the show shift metadata
		$scheds = $_POST['show_sched'];

		// --- sanitize the show shift times ---
		$new_scheds = array();
		$days       = array( '', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
		foreach ( $scheds as $i => $sched ) {
			foreach ( $sched as $key => $value ) {

				// --- validate according to key ---
				$isvalid = false;
				if ( 'day' === $key ) {
					if ( in_array( $value, $days, true ) ) {
						$isvalid = true;}
				} elseif ( 'start_hour' === $key || 'end_hour' === $key ) {
					if ( empty( $value ) ) {
						$isvalid = true;
					} elseif ( absint( $value ) > 0 && absint( $value ) < 13 ) {
						$isvalid = true;
					}
				} elseif ( 'start_min' === $key || 'end_min' === $key ) {
					if ( empty( $value ) ) {
						$isvalid = true;
					} elseif ( absint( $value ) > -1 && absint( $value ) < 61 ) {
						$isvalid = true;
					}
				} elseif ( 'start_meridian' === $key || 'end_meridian' === $key ) {
					$valid = array( '', 'am', 'pm' );
					if ( in_array( $value, $valid, true ) ) {
						$isvalid = true;
					}
				} elseif ( 'encore' === $key ) {
					// 2.2.4: fix to missing encore sanitization saving
					$valid = array( '', 'on' );
					if ( in_array( $value, $valid, true ) ) {
						$isvalid = true;
					}
				}

				// --- if valid add to new schedule ---
				if ( $isvalid ) {
					$new_scheds[ $i ][ $key ] = $value;
				} else {
					$new_scheds[ $i ][ $key ] = '';
				}
			}
		}

		update_post_meta( $post_id, 'show_sched', $new_scheds );

}


// --------------------------
// === Schedule Overrides ===
// --------------------------

// -----------------------------
// Add Schedule Override Metabox
// -----------------------------
// --- Add schedule override box to override edit screens ---
add_action( 'add_meta_boxes', 'radio_station_master_override_add_sched_box' );
function radio_station_master_override_add_sched_box() {
	// 2.2.2: add high priority to show at top of edit screen
	add_meta_box(
		'dynamicSchedOver_sectionid',
		__( 'Override Schedule', 'radio-station' ),
		'radio_station_master_override_inner_sched_custom_box',
		'override',
		'normal',
		'high'
	);
}

// -------------------------
// Schedule Override Metabox
// -------------------------
function radio_station_master_override_inner_sched_custom_box() {

	global $post;

	// --- add nonce field for update verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSchedOver_noncename' );
	?>
	<div id="meta_inner" class="sched-override">

		<?php
		// --- get the saved meta as an array ---
		$override = get_post_meta( $post->ID, 'show_override_sched', false );
		if ( $override ) {
			$override = $override[0];}
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#OverrideDate').datepicker({dateFormat : 'yy-mm-dd'});
		});
		</script>

		<ul style="list-style:none;">
			<li style="display:inline-block;">
				<?php esc_html_e( 'Date', 'radio-station' ); ?>:
				<input type="text" id="OverrideDate" name="show_sched[date]" value="
				<?php
				if ( isset( $override['date'] ) && ! empty( $override['date'] ) ) {
					echo esc_html( $override['date'] );
				}
				?>
				"/>
			</li>

			<li style="display:inline-block; margin-left:20px;">
				<?php esc_html_e( 'Start Time', 'radio-station' ); ?>:
				<select name="show_sched[start_hour]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 1; $i <= 12; $i++ ) {
					echo '<option value="' . esc_attr( $i ) . '" ' . selected( $override['start_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[start_min]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 0; $i < 60; $i++ ) {
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;
					}
					echo '<option value="' . esc_attr( $min ) . '"' . selected( $override['start_min'], $min ) . '>' . esc_html( $min ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[start_meridian]" style="min-width:35px;">
					<option value=""></option>
					<option value="am"
					<?php
					if ( isset( $override['start_meridian'] ) && 'am' === $override['start_meridian'] ) {
						echo ' selected="selected"';
					}
					?>
					>am</option>
					<option value="pm"
					<?php
					if ( isset( $override['start_meridian'] ) && 'pm' === $override['start_meridian'] ) {
						echo ' selected="selected"';
					}
					?>
					>pm</option>
				</select>
			</li>

			<li style="display:inline-block; margin-left:20px;">
				<?php esc_html_e( 'End Time', 'radio-station' ); ?>:
				<select name="show_sched[end_hour]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 1; $i <= 12; $i++ ) {
					echo '<option value="' . esc_attr( $i ) . '" ' . selected( $override['end_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[end_min]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 0; $i < 60; $i++ ) {
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;
					}
					echo '<option value="' . esc_attr( $min ) . '"' . selected( $override['end_min'], $min ) . '>' . esc_html( $min ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[end_meridian]" style="min-width:35px;">
					<option value=""></option>
					<option value="am"
					<?php
					if ( isset( $override['end_meridian'] ) &&
						'am' === $override['end_meridian'] ) {
						echo ' selected="selected"'; }
					?>
					>am</option>
					<option value="pm"
					<?php
					if ( isset( $override['end_meridian'] ) && 'pm' === $override['end_meridian'] ) {
						echo ' selected="selected"';
					}
					?>
					>pm</option>
				</select>
			</li>
		</ul>
	</div>
	<?php
}

// ------------------------
// Update Schedule Override
// ------------------------
// --- save the custom fields when a show override is saved ---
add_action( 'save_post', 'radio_station_master_override_save_showpostdata' );
function radio_station_master_override_save_showpostdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;}

	// --- verify this came from the our screen and with proper authorization ---
	if ( isset( $_POST['dynamicMetaSchedOver_noncename'] ) ) {
		if ( ! wp_verify_nonce( $_POST['dynamicMetaSchedOver_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}
	} else {
		return;}

	// --- get the show override data ---
	$sched = $_POST['show_sched'];
	if ( ! is_array( $sched ) ) {
		return;}

	// --- get/set current schedule for merging ---
	// 2.2.2: added to set default keys
	$current_sched = get_post_meta( $post_id, 'show_override_sched', true );
	if ( ! $current_sched || ! is_array( $current_sched ) ) {
		$current_sched = array(
			'date'           => '',
			'start_hour'     => '',
			'start_min'      => '',
			'start_meridian' => '',
			'end_hour'       => '',
			'end_min'        => '',
			'end_meridian'   => '',
		);
	}

	// --- sanitize values before saving ---
	// 2.2.2: loop and validate schedule override values
	$changed = false;
	foreach ( $sched as $key => $value ) {
		$isvalid = false;

		// --- validate according to key ---
		if ( 'date' === $key ) {
			// check posted date format (yyyy-mm-dd) with checkdate (month, date, year)
			$parts = explode( '-', $value );
			if ( checkdate( $parts[1], $parts[2], $parts[0] ) ) {
				$isvalid = true;}
		} elseif ( 'start_hour' === $key || 'end_hour' === $key ) {
			if ( empty( $value ) ) {
				$isvalid = true;
			} elseif ( ( absint( $value ) > 0 ) && ( absint( $value ) < 13 ) ) {
				$isvalid = true;
			}
		} elseif ( 'start_min' === $key || 'end_min' === $key ) {
			// 2.2.3: fix to validate 00 minute value
			if ( empty( $value ) ) {
				$isvalid = true;
			} elseif ( absint( $value ) > -1 && absint( $value ) < 61 ) {
				$isvalid = true;
			}
		} elseif ( 'start_meridian' === $key || 'end_meridian' === $key ) {
			$valid = array( '', 'am', 'pm' );
			if ( in_array( $value, $valid, true ) ) {
				$isvalid = true;
			}
		}

		// --- if valid add to current schedule setting ---
		if ( $isvalid && $value !== $current_sched[ $key ] ) {
			$current_sched[ $key ] = $value;
			$changed               = true;
		}
	}

	// --- save schedule setting if changed ---
	if ( $changed ) {
		update_post_meta( $post_id, 'show_override_sched', $current_sched );}
}
