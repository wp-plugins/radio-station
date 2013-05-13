<?php
/* 
 * Playlist and Show functionality
 * Author: Nikki Blight
 * 
 */

/* Playlists */

//create post types for playlists and shows
function myplaylist_create_post_types() {
	
	register_post_type( 'playlist',
		array(
			'labels' => array(
				'name' => __( 'Playlists' ),
				'singular_name' => __( 'Playlist' ),
				'add_new' => __( 'Add Playlist'),
				'add_new_item' => __( 'Add Playlist'),
				'edit_item' => __( 'Edit Playlist' ),
				'new_item' => __( 'New Playlist' ),
				'view_item' => __( 'View Playlist' )
			),
			'show_ui' => true,
			'description' => 'Post type for Playlist descriptions',
			'menu_position' => 5,
			'menu_icon' => WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/playlist-menu-icon.png',
			'public' => true,
			'hierarchical' => false,
			'supports' => array('title', 'editor'),
			'can_export' => true,
			'has_archive' => 'playlists-archive',
			'rewrite' => array('slug' => 'playlists'),
			'capability_type' => 'playlist',
			'map_meta_cap' => true
		)
	);
	
	register_post_type( 'show',
		array(
			'labels' => array(
				'name' => __( 'Shows' ),
				'singular_name' => __( 'Show' ),
				'add_new' => __( 'Add Show'),
				'add_new_item' => __( 'Add Show'),
				'edit_item' => __( 'Edit Show' ),
				'new_item' => __( 'New Show' ),
				'view_item' => __( 'View Show' )
			),
			'show_ui' => true,
			'description' => 'Post type for Show descriptions',
			'menu_position' => 5,
			'menu_icon' => WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png',
			'public' => true,
			'taxonomies' => array('genres'),
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'thumbnail'),
			'can_export' => true,
			'capability_type' => 'show',
			'map_meta_cap' => true
		)
	);
}
add_action( 'init', 'myplaylist_create_post_types' );

//Add custom repeating meta field for the playlist edit form... Stores multiple associated values as a serialized string
//Borrowed and adapted from http://wordpress.stackexchange.com/questions/19838/create-more-meta-boxes-as-needed/19852#19852
function myplaylist_add_custom_box() {
	add_meta_box(
        'dynamic_sectionid',
	__( 'Playlist Entries', 'myplugin_textdomain' ),
        'myplaylist_inner_custom_box',
        'playlist');
}
add_action( 'add_meta_boxes', 'myplaylist_add_custom_box', 1 );

//Prints the playlist entry box to the main column on the edit screen
function myplaylist_inner_custom_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
	?>
    <div id="meta_inner">
    <?php

    //get the saved meta as an arry
    $entries = get_post_meta($post->ID,'playlist',false);
	//print_r($prices);
    $c = 1;
    
    echo '<table id="here" class="widefat">';
    echo "<tr>";
    echo "<th></th><th>Artist</th><th>Song</th><th>Album</th><th>Record Label</th><th>DJ Comments</th><th>New</th><th>Status</th><th>Remove</th>";
    echo "</tr>";
    
    if (count($entries[0]) > 0){
    	
        foreach($entries[0] as $track ){
            if (isset($track['playlist_entry_artist']) || isset($track['playlist_entry_song']) || isset($track['playlist_entry_album']) || isset($track['playlist_entry_label']) || isset($track['playlist_entry_comments']) || isset($track['playlist_entry_new']) || isset($track['playlist_entry_status'])){
                echo '<tr>';
                echo '<td>'.$c.'</td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_artist]" value="'.$track['playlist_entry_artist'].'" /></td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_song]" value="'.$track['playlist_entry_song'].'" /></td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_album]" value="'.$track['playlist_entry_album'].'" /></td>';
                echo '<td><input type="text" name="playlist['.$c.'][playlist_entry_label]" value="'.$track['playlist_entry_label'].'" /></td>';
                echo '<td><textarea name="playlist['.$c.'][playlist_entry_comments]">'.$track['playlist_entry_comments'].'</textarea></td>';
                
                echo '<td><input type="checkbox" name="playlist['.$c.'][playlist_entry_new]"';
				if($track['playlist_entry_new']) {
					echo ' checked="checked"';
				}
				echo ' /></td>';
				
                echo '<td>';
                echo '<select name="playlist['.$c.'][playlist_entry_status]">';
                
                echo '<option value="queued"';
                if($track['playlist_entry_status'] == "queued") { echo ' selected="selected"'; }
                echo '>Queued</option>';
                
                echo '<option value="played"';
                if($track['playlist_entry_status'] == "played") { echo ' selected="selected"'; }
                echo '>Played</option>';
                
                echo '</select></td>';
                
                echo '<td><span class="remove button-secondary" style="cursor: pointer;">Remove</span></td>';
                echo '</tr>';
                $c = $c +1;
            }
        }
    }
    echo '</table>';

    ?>
<a class="add button-primary" style="cursor: pointer; float: right; margin-top: 5px;"><?php echo __('Add Entry'); ?></a>
<div style="clear: both;"></div>
<script>
    var $ =jQuery.noConflict();
    $(document).ready(function() {
        var count = <?php echo $c; ?>;
        $(".add").click(function() {
            
            $('#here').append('<tr><td>'+count+'</td><td><input type="text" name="playlist['+count+'][playlist_entry_artist]" value="" /></td><td><input type="text" name="playlist['+count+'][playlist_entry_song]" value="" /></td><td><input type="text" name="playlist['+count+'][playlist_entry_album]" value="" /></td><td><input type="text" name="playlist['+count+'][playlist_entry_label]" value="" /></td><td><textarea name="playlist['+count+'][playlist_entry_comments]"></textarea></td><td><input type="checkbox" name="playlist['+count+'][playlist_entry_new]" /></td><td><select name="playlist['+count+'][playlist_entry_status]"><option value="queued">Queued</option><option value="played">Played</option></select></td><td><span class="remove button-secondary" style="cursor: pointer;">Remove</span></td></tr>' );
            count = count + 1;
            return false;
        });
        $(".remove").live('click', function() {
            $(this).parent().parent().remove();
        });
    });
    </script>
</div>

<div id="publishing-action-bottom">
	<br /><br />
	<?php
	//borrowed from wp-admin/includes/meta-boxes.php
	if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
		if ( $can_publish ) :
		if ( !empty($post->post_date_gmt) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) : ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule') ?>" />
			<?php submit_button( __( 'Schedule' ), 'primary', 'publish', false, array( 'tabindex' => '50', 'accesskey' => 'o' ) ); ?>
	<?php	else : ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
			<?php submit_button( __( 'Publish' ), 'primary', 'publish', false, array( 'tabindex' => '50', 'accesskey' => 'o' ) ); ?>
	<?php	endif;
		else : ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>" />
			<?php submit_button( __( 'Update Playlist' ), 'primary', 'publish', false, array( 'tabindex' => '50', 'accesskey' => 'o' ) ); ?>
	<?php
		endif;
	} else { ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
			<input name="save" type="submit" class="button-primary" id="publish" tabindex="50" accesskey="o" value="<?php esc_attr_e('Update Playlist') ?>" />
	<?php
	} ?>
</div>



<?php

}

//Add custom meta box for show assigment
function myplaylist_add_show_box() {
	add_meta_box(
        'dynamicShow_sectionid',
	__( 'Show', 'myplugin_textdomain' ),
        'myplaylist_inner_show_custom_box',
        'playlist',
		'side');
}
add_action( 'add_meta_boxes', 'myplaylist_add_show_box' );

//Prints the box content for the Show field
function myplaylist_inner_show_custom_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaShow_noncename' );

	$args = array(
					'numberposts'     => 0,
					'offset'          => 0,
					'orderby'         => 'post_title',
					'order'           => 'aSC',
					'post_type'       => 'show',
					'post_status'     => 'publish'
	);

	$shows = get_posts($args);
	$current = get_post_meta($post->ID,'playlist_show_id',true);
	
	?>
    <div id="meta_inner">
    
    <select name="playlist_show_id">
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
function myplaylist_save_postdata( $post_id ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;

    if(isset($_POST['playlist']) || isset($_POST['playlist_show_id'])) {
	    // verify this came from the our screen and with proper authorization,
	    // because save_post can be triggered at other times
	    if (isset($_POST['dynamicMeta_noncename'])){
	        if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) )
	            return;
	    }else{return;}
	    
	    if (isset($_POST['dynamicMetaShow_noncename'])){
	    	if ( !wp_verify_nonce( $_POST['dynamicMetaShow_noncename'], plugin_basename( __FILE__ ) ) )
	    	return;
	    }else{return;}
	    
	    // OK, we're authenticated: we need to find and save the data
	    $playlist = $_POST['playlist'];
	   
	    //move songs that are still queued to the end of the list so that order is maintained
	    foreach($playlist as $i => $song) {
	    	if($song['playlist_entry_status'] == 'queued') {
	    		$playlist[] = $song;
	    		unset($playlist[$i]);
	    	}
	    }
	    
	    $show = $_POST['playlist_show_id'];
	    
	    update_post_meta($post_id,'playlist',$playlist);
	    update_post_meta($post_id,'playlist_show_id',$show);
    }
    
}
add_action( 'save_post', 'myplaylist_save_postdata' );

//shortcode for displaying the current song
function myplaylist_now_playing($atts) {
	extract( shortcode_atts( array(
			'title' => '',
			'artist' => 1,
			'song' => 1,
			'album' => 0,
			'label' => 0,
			'comments' => 0
	), $atts ) );
	
	$most_recent = myplaylist_get_now_playing();
	$output = '';
	
	if($most_recent) {
		$class = '';
		if($most_recent['playlist_entry_new'] == 'on') {
			$class = ' class="new"';
		}

		$output .= '<div id="myplaylist-nowplaying"'.$class.'>';
		if($title != '') {
			$output .= '<h3>'.$title.'</h3>';
		}
		
		if($artist == 1) {
			$output .= '<span class="myplaylist-artist">'.$most_recent['playlist_entry_artist'].'</span> ';
		}
		if($song == 1) {
			$output .= '<span class="myplaylist-song">'.$most_recent['playlist_entry_song'].'</span> ';
		}
		if($album == 1) {
			$output .= '<span class="myplaylist-album">'.$most_recent['playlist_entry_album'].'</span> ';
		}
		if($label == 1) {
			$output .= '<span class="myplaylist-label">'.$most_recent['playlist_entry_label'].'</span> ';
		}
		if($comments == 1) {
			$output .= '<span class="myplaylist-comments">'.$most_recent['playlist_entry_comments'].'</span> ';
		}
		$output .= '<span class="myplaylist-link"><a href="'.$most_recent['playlist_permalink'].'">View Playlist</a></span> ';
		$output .= '</div>';
	
	}
	return $output;
}
add_shortcode('now-playing', 'myplaylist_now_playing');

//get the most recently entered song
function myplaylist_get_now_playing() {
	//grab the most recent playlist
	$args = array(
				'numberposts'     => 1,
				'offset'          => 0,
				'orderby'         => 'post_date',
				'order'           => 'DESC',
				'post_type'       => 'playlist',
				'post_status'     => 'publish'
			);
	
	$playlist = get_posts($args);
	
	//if there are no playlists saved, return nothing
	if(!$playlist) {
		return false;
	}
	
	//fetch the tracks for each playlist from the wp_postmeta table
	$songs = get_post_meta($playlist[0]->ID, 'playlist');
	
	//print_r($songs);die();
	
	//removed any entries that are marked as 'queued'
	foreach($songs[0] as $i => $entry) {
		if($entry['playlist_entry_status'] == 'queued') {
			unset($songs[0][$i]);
		}
	}
		
	//pop the last track off the list for display
	$most_recent = array_pop($songs[0]);
	
	//get the permalink for the playlist so it can be displayed
	$most_recent['playlist_permalink'] = get_permalink($playlist[0]->ID);
	
	return $most_recent;
}

/* Sidebar playlist widget functions */
class Playlist_Widget extends WP_Widget {
	
	//define the widget
	function Playlist_Widget() {
		$widget_ops = array('classname' => 'Playlist_Widget', 'description' => 'Display the current song.');
		$this->WP_Widget('Playlist_Widget', 'Radio Station: Now Playing', $widget_ops);
	}
 	
	//build the backend widget form
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array( 'title' => '' ));
		$title = $instance['title'];
		$artist = $instance['artist'];
		$song = $instance['song'];
		$album = $instance['album'];
		$label = $instance['label'];
		$comments = $instance['comments'];
		
		?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
		  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('artist'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('artist'); ?>" name="<?php echo $this->get_field_name('artist'); ?>" type="checkbox" <?php if($artist) { echo 'checked="checked"'; } ?> /> 
		  		Show Artist Name
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('song'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('song'); ?>" name="<?php echo $this->get_field_name('song'); ?>" type="checkbox" <?php if($song) { echo 'checked="checked"'; } ?> /> 
		  		Show Song Title
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('album'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" type="checkbox" <?php if($album) { echo 'checked="checked"'; } ?> /> 
		  		Show Album Name
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('label'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="checkbox" <?php if($label) { echo 'checked="checked"'; } ?> /> 
		  		Show Record Label Name
		  		</label>
		  	</p>
		  	
		  	<p>
		  		<label for="<?php echo $this->get_field_id('comments'); ?>"> 
		  		<input id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" type="checkbox" <?php if($comments) { echo 'checked="checked"'; } ?> /> 
		  		Show DJ Comments
		  		</label>
		  	</p>
		<?php
	}
 
	//handle saves and updates
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['artist'] = ( isset( $new_instance['artist'] ) ? 1 : 0 );
		$instance['song'] = ( isset( $new_instance['song'] ) ? 1 : 0 );
		$instance['album'] = ( isset( $new_instance['album'] ) ? 1 : 0 );
		$instance['label'] = ( isset( $new_instance['label'] ) ? 1 : 0 );
		$instance['comments'] = ( isset( $new_instance['comments'] ) ? 1 : 0 );
		 
		return $instance;
	}
 
	//display the widget in the front end
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$artist = $instance['artist'];
		$song = $instance['song'];
		$album = $instance['album'];
		$label = $instance['label'];
		$comments = $instance['comments'];
 		
		
 		//fetch the current song
		$most_recent = myplaylist_get_now_playing();
		
		?>
		<div class="widget">
			<?php 
				if (!empty($title)) {
					echo $before_title . $title . $after_title;
				}
				else {
					echo $before_title.$after_title;
				}
				
				$class= '';
				if($most_recent['playlist_entry_new'] == 'on') {
					$class= ' class="new"';	
				}
				
				echo '<div id="myplaylist-nowplaying"'.$class.'>';
				
				if($artist) {
					echo '<span class="myplaylist-artist">'.$most_recent['playlist_entry_artist'].'</span> ';
				}
				
				if($song) {
					echo '<span class="myplaylist-song">'.$most_recent['playlist_entry_song'].'</span> ';
				}
				
				if($album) {
					echo '<span class="myplaylist-album">'.$most_recent['playlist_entry_album'].'</span> ';
				}
				
				if($label) {
					echo '<span class="myplaylist-label">'.$most_recent['playlist_entry_label'].'</span> ';
				}
				
				if($comments) {
					echo '<span class="myplaylist-comments">'.$most_recent['playlist_entry_comments'].'</span> ';
				}
				
				echo '<span class="myplaylist-link">';
				echo '<a href="'.$most_recent['playlist_permalink'].'">View Playlist</a>';
				echo '</span>';
				echo '</div>';
			?>
			
			
		</div>
		<?php
 
		echo $after_widget;
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("Playlist_Widget");') );

/* Shows */

//create custom taxonomies for the Show post type
function myplaylist_create_show_taxonomy() {
	register_taxonomy('genres', array('show'),
		array(
				'hierarchical' => true, 
				'label' => 'Genres', 
				'singular_label' => 'Genres',
				'public' => true,
				'show_tagcloud' => false,
				'query_var' => true,
				'rewrite' => array('slug' => 'genre'),
				'capabilities' => array(
					'manage_terms' => 'edit_shows',
					'edit_terms' => 'edit_shows',
					'delete_terms' => 'edit_shows',
					'assign_terms' => 'edit_shows'
				),
		)
	);

}
add_action('init', 'myplaylist_create_show_taxonomy');

//Adds a box to the side column of the show edit screens
function myplaylist_add_metainfo_box() {
	add_meta_box(
        'dynamicShowMeta_sectionid',
	__( 'Information', 'myplugin_textdomain' ),
        'myplaylist_inner_metainfo_custom_box',
        'show');
}
add_action( 'add_meta_boxes', 'myplaylist_add_metainfo_box' );

//Prints the box for additional meta data for the Show post type
function myplaylist_inner_metainfo_custom_box() {
	global $post;

	$file = get_post_meta($post->ID,'show_file',true);
	$email = get_post_meta($post->ID,'show_email',true);
	$active = get_post_meta($post->ID,'show_active',true);
	$link = get_post_meta($post->ID,'show_link',true);

	?>
    <div id="meta_inner">
    
    <p><label>Active</label>
    <input type="checkbox" name="show_active" <?php if($active == 'on') { echo 'checked="checked"'; } ?>  /><br /><em>Check this box if show is currently active (Show will not appear on programming schedule if unchecked)</em></p>
    
    <p><label>Current Audio File:</label><br />
    <input type="text" name="show_file" size="100" value="<?php echo $file; ?>" /></p>
    
    <p><label>DJ Email:</label><br />
    <input type="text" name="show_email" size="100" value="<?php echo $email; ?>" /></p>
    
    <p><label>Website Link:</label><br />
    <input type="text" name="show_link" size="100" value="<?php echo $link; ?>" /></p>
    
	</div>
   <?php
}

//Adds a box to the side column of the show edit screens
function myplaylist_add_user_box() {
	add_meta_box(
        'dynamicUser_sectionid',
	__( 'DJs', 'myplugin_textdomain' ),
        'myplaylist_inner_user_custom_box',
        'show',
		'side');
}
add_action( 'add_meta_boxes', 'myplaylist_add_user_box' );

//Prints the box for user assignement for the Show post type
function myplaylist_inner_user_custom_box() {
	global $post;
	global $wp_roles;
	
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaUser_noncename' );
	
	//check for roles that have the edit_shows capability enabled
	$add_roles = array('dj');
	foreach($wp_roles->roles as $name => $role) {
		foreach($role['capabilities'] as $capname => $capstatus) {
			if($capname == "edit_shows" && $capstatus == 1) {
				$add_roles[] = $name;
			}
		}
	}
	$add_roles = array_unique($add_roles);
	
	//create the meta query for get_users()
	$meta_query = array('relation' => 'OR');
	foreach($add_roles as $role) {
		$meta_query[] = array(
						'key' => 'wp_capabilities',
						'value' => $role,
						'compare' => 'like'
				);
	}
	
	//get all eligible users
	$args = array(
			'meta_query' => $meta_query,
			'orderby' => 'display_name',
			'order' => 'ASC',
			'fields' => array('ID, display_name'),
	);
	
	$users = get_users($args);

	//get the DJs currently assigned to the show
	$current = get_post_meta($post->ID,'show_user_list',true);
	if(!$current) {
		$current = array();
	}
	
	//move any selected DJs to the top of the list
	foreach($users as $i => $dj) {
    	if(in_array($dj->ID, $current)) {
    		unset($users[$i]); //unset first, or prepending will change the index numbers and cause you to delete the wrong item
    		array_unshift($users, $dj);  //prepend the user to the beginning of the array
    	}
	}

	?>
    <div id="meta_inner">
    
    <select name="show_user_list[]" multiple="multiple" style="height: 150px;">
    <?php
    	foreach($users as $dj) {
    		$selected = '';
    		if(in_array($dj->ID, $current)) {
    			$selected = ' selected="selected"';
    		}
    		
    		echo '<option value="'.$dj->ID.'"'.$selected.'>'.$dj->display_name.'</option>';	
    	}
	?>
	</select>
	</div>
   <?php
}

//Adds a box to the side column of the show edit screens
function myplaylist_add_sched_box() {
	add_meta_box(
        'dynamicSched_sectionid',
	__( 'Schedules', 'myplugin_textdomain' ),
        'myplaylist_inner_sched_custom_box',
        'show');
}
add_action( 'add_meta_boxes', 'myplaylist_add_sched_box' );

function myplaylist_inner_sched_custom_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSched_noncename' );
	?>
	    <div id="meta_inner">
	    <?php
	
	    //get the saved meta as an array
	    $shifts = get_post_meta($post->ID,'show_sched',false);
	    
		//print_r($shifts);

	    $c = 0;
	    if (is_array($shifts[0])){
	        foreach($shifts[0] as $track ){
	            if (isset($track['day']) || isset($track['time'])){
	            	?>
	            	<p>
	            		Day: 
	            		<select name="show_sched[<?php echo $c; ?>][day]">
	            			<option value=""></option>
	            			<option value="Monday"<?php if($track['day'] == "Monday") { echo ' selected="selected"'; } ?>>Monday</option>
	            			<option value="Tuesday"<?php if($track['day'] == "Tuesday") { echo ' selected="selected"'; } ?>>Tuesday</option>
	            			<option value="Wednesday"<?php if($track['day'] == "Wednesday") { echo ' selected="selected"'; } ?>>Wednesday</option>
	            			<option value="Thursday"<?php if($track['day'] == "Thursday") { echo ' selected="selected"'; } ?>>Thursday</option>
	            			<option value="Friday"<?php if($track['day'] == "Friday") { echo ' selected="selected"'; } ?>>Friday</option>
	            			<option value="Saturday"<?php if($track['day'] == "Saturday") { echo ' selected="selected"'; } ?>>Saturday</option>
	            			<option value="Sunday"<?php if($track['day'] == "Sunday") { echo ' selected="selected"'; } ?>>Sunday</option>
	            		</select>
	            		 - 
	            		Start Time: 
	            		<select name="show_sched[<?php echo $c; ?>][start_hour]">
	            			<option value=""></option>
	            		<?php for($i=1; $i<=12; $i++): ?>
	            			<option value="<?php echo $i; ?>"<?php if($track['start_hour'] == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
	            		<?php endfor; ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][start_min]">
	            			<option value=""></option>
	            		<?php for($i=0; $i<60; $i++): ?>
	            			<?php 
								$min = $i;
								if($i < 10) {
									$min = '0'.$i;
								}
							?>
	            			<option value="<?php echo $min; ?>"<?php if($track['start_min'] == $min) { echo ' selected="selected"'; } ?>><?php echo $min; ?></option>
	            		<?php endfor; ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][start_meridian]">
	            			<option value=""></option>
	            			<option value="am"<?php if($track['start_meridian'] == "am") { echo ' selected="selected"'; } ?>>am</option>
	            			<option value="pm"<?php if($track['start_meridian'] == "pm") { echo ' selected="selected"'; } ?>>pm</option>
	            		</select>
	            		
	            		 -  
	            		End Time: 
	            		<select name="show_sched[<?php echo $c; ?>][end_hour]">
	            			<option value=""></option>
	            		<?php for($i=1; $i<=12; $i++): ?>
	            			<option value="<?php echo $i; ?>"<?php if($track['end_hour'] == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
	            		<?php endfor; ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][end_min]">
	            			<option value=""></option>
	            		<?php for($i=0; $i<60; $i++): ?>
	            			<?php 
								$min = $i;
								if($i < 10) {
									$min = '0'.$i;
								}
							?>
	            			<option value="<?php echo $min; ?>"<?php if($track['end_min'] == $min) { echo ' selected="selected"'; } ?>><?php echo $min; ?></option>
	            		<?php endfor; ?>
	            		</select>
	            		<select name="show_sched[<?php echo $c; ?>][end_meridian]">
	            			<option value=""></option>
	            			<option value="am"<?php if($track['end_meridian'] == "am") { echo ' selected="selected"'; } ?>>am</option>
	            			<option value="pm"<?php if($track['end_meridian'] == "pm") { echo ' selected="selected"'; } ?>>pm</option>
	            		</select>
	            		<input type="checkbox" name="show_sched[<?php echo $c; ?>][encore]" <?php if($track['encore'] == 'on') { echo 'checked="checked"'; } ?>/> Encore Presentation
	            		<span class="remove button-secondary" style="cursor: pointer;">Remove</span>
	            	</p>
	            	<?php 
	                $c = $c +1;
	            }
	        }
	    }
	
	    ?>
	<span id="here"></span>
	<a class="add button-primary" style="cursor: pointer; display:block; width: 75px; padding: 8px; text-align: center; line-height: 1em;"><?php echo __('Add Shift'); ?></a>
	<script>
	    var $ =jQuery.noConflict();
	    $(document).ready(function() {
	        var count = <?php echo $c; ?>;
	        $(".add").click(function() {
	            count = count + 1;
				output = '<p>Day: '; 
				output += '<select name="show_sched[' + count + '][day]">';
				output += '<option value=""></option>';
				output += '<option value="Monday">Monday</option>';
				output += '<option value="Tuesday">Tuesday</option>';
				output += '<option value="Wednesday">Wednesday</option>';
				output += '<option value="Thursday">Thursday</option>';
				output += '<option value="Friday">Friday</option>';
				output += '<option value="Saturday">Saturday</option>';
				output += '<option value="Sunday">Sunday</option>';
				output += '</select>';
				output += ' - Start Time: ';
				
				output += '<select name="show_sched[' + count + '][start_hour]">';
				output += '<option value=""></option>';
	    		<?php for($i=1; $i<=12; $i++): ?>
    			output += '<option value="<?php echo $i; ?>"><?php echo $i; ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_min]">';
				output += '<option value=""></option>';
				<?php for($i=0; $i<60; $i++): ?>
				<?php 
					$min = $i;
					if($i < 10) {
						$min = '0'.$i;
					}
				?>
    			output += '<option value="<?php echo $min; ?>"><?php echo $min; ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_meridian]">';
				output += '<option value=""></option>';
    			output += '<option value="am">am</option>';
    			output += '<option value="pm">pm</option>';
				output += '</select> ';

				output += ' - End Time: ';
				output += '<select name="show_sched[' + count + '][end_hour]">';
				output += '<option value=""></option>';
				<?php for($i=1; $i<=12; $i++): ?>
    			output += '<option value="<?php echo $i; ?>"><?php echo $i; ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_min]">';
				output += '<option value=""></option>';
				<?php for($i=0; $i<60; $i++): ?>
				<?php 
					$min = $i;
					if($i < 10) {
						$min = '0'.$i;
					}
				?>
    			output += '<option value="<?php echo $min; ?>"><?php echo $min; ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_meridian]">';
				output += '<option value=""></option>';
    			output += '<option value="am">am</option>';
    			output += '<option value="pm">pm</option>';
				output += '</select> ';

				output += '<input type="checkbox" name="show_sched[' + count + '][encore]" /> Encore Presentation ';

				output += '<span class="remove button-secondary" style="cursor: pointer;">Remove</span></p>';
	            $('#here').append( output );

	            return false;
	        });
	        $(".remove").live('click', function() {
	            $(this).parent().remove();
	        });
	    });
	    </script>
	</div>
<?php
}

//save the custom fields when a show is saved
function myplaylist_save_showpostdata( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if (isset($_POST['dynamicMetaUser_noncename'])){
		if ( !wp_verify_nonce( $_POST['dynamicMetaUser_noncename'], plugin_basename( __FILE__ ) ) )
		return;
	}else{return;
	}
	
	if (isset($_POST['dynamicMetaSched_noncename'])){
		if ( !wp_verify_nonce( $_POST['dynamicMetaSched_noncename'], plugin_basename( __FILE__ ) ) )
		return;
	}else{return;
	}
	
	// OK, we're authenticated: we need to find and save the data
	$djs = $_POST['show_user_list'];
	$sched = $_POST['show_sched'];
	$file = $_POST['show_file'];
	$email = $_POST['show_email'];
	$active = $_POST['show_active'];
	$link = $_POST['show_link'];
	
	update_post_meta($post_id,'show_user_list',$djs);
	update_post_meta($post_id,'show_sched',$sched);
	update_post_meta($post_id,'show_file',$file);
	update_post_meta($post_id,'show_email',$email);
	update_post_meta($post_id,'show_active',$active);
	update_post_meta($post_id,'show_link',$link);
}
add_action( 'save_post', 'myplaylist_save_showpostdata' );

//shortcode to fetch all playlists for a given show id
function myplaylist_get_playlists_for_show($atts) {
	extract( shortcode_atts( array(
		'show' => '',
		'limit' => -1
	), $atts ) );

	//don't return anything if we don't have a show
	if($show == '') {
		return false;
	}

	$args = array(
				'numberposts' => $limit,
				'offset' => 0,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'post_type' => 'playlist',
				'post_status' => 'publish',
				'meta_key' => 'playlist_show_id',
				'meta_value' => $show
	);

	$playlists = get_posts($args);

	$output = '';

	$output .= '<div id="myplaylist-playlistlinks">';
	$output .= '<ul class="myplaylist-linklist">';
	foreach($playlists as $playlist) {
		$output .= '<li><a href="';
		$output .= get_permalink($playlist->ID);
		$output .= '">'.$playlist->post_title.'</a></li>';
	}
	$output .= '</ul>';
	
	$playlist_archive = get_post_type_archive_link('playlist');
	$params = array( 'show_id' => $show );
	$playlist_archive = add_query_arg( $params, $playlist_archive );
	
	$output .= '<a href="'.$playlist_archive.'">More Playlists</a>';
	
	$output .= '</div>';

	return $output;
}
add_shortcode('get-playlists', 'myplaylist_get_playlists_for_show');

//fetch all blog posts for a show's DJs
function myplaylist_get_posts_for_show($show_id = null, $title = '', $limit = 10) {
	global $wpdb;
	
	
	//don't return anything if we don't have a show
	if(!$show_id) {
		return false;
	}
	
	$fetch_posts = $wpdb->get_results("SELECT `meta`.`post_id` FROM ".$wpdb->prefix."postmeta AS `meta`
			WHERE `meta`.`meta_key` = 'post_showblog_id' AND `meta`.`meta_value` = ".$show_id.";");

	$blog_array = array();
	foreach($fetch_posts as $f) {
		$blog_array[] = $f->post_id;
	}
	
	$blog_array = implode(",", $blog_array);
	
	$blogposts = $wpdb->get_results("SELECT `posts`.`ID`, `posts`.`post_title` FROM ".$wpdb->prefix."posts AS `posts`
			WHERE `posts`.`ID` IN(".$blog_array.")
			AND `posts`.`post_status` = 'publish'
			ORDER BY `posts`.`post_date` DESC
			LIMIT ".$limit.";");
	
	$output = '';

	$output .= '<div id="myplaylist-blog-posts">';
	$output .= '<h3>'.$title.'</h3>';
	$output .= '<ul class="myplaylist-post-list">';
	foreach($blogposts as $p) {
		$output .= '<li><a href="';
		$output .= get_permalink($p->ID);
		$output .= '">'.$p->post_title.'</a></li>';
	}
	$output .= '</ul>';
	$output .= '</div>';

	//if the blog archive page has been created, add a link to the archive for this show
	$page = $wpdb->get_results("SELECT `meta`.`post_id` FROM ".$wpdb->prefix."postmeta AS `meta`
			WHERE `meta`.`meta_key` = '_wp_page_template' 
			AND `meta`.`meta_value` = 'show-blog-archive-template.php'
			LIMIT 1;");
	
	if($page) {
		$blog_archive = get_permalink($page[0]->post_id);
		$params = array( 'show_id' => $show_id );
		$blog_archive = add_query_arg( $params, $blog_archive );
		
		$output .= '<a href="'.$blog_archive.'">More Blog Posts</a>';
	}
	
	return $output;
}

//shortcode for displaying a list of all shows
function myplaylist_list_shows() {
	//grab the published shows
	$args = array(
					'numberposts'     => -1,
					'offset'          => 0,
					'orderby'         => 'title',
					'order'           => 'ASC',
					'post_type'       => 'show',
					'post_status'     => 'publish',
					'meta_query' => array(
										array(
											'key' => 'show_active',
											'value' => 'on',
										)
									)
	);
	
	$shows = get_posts($args);
	
	//if there are no shows saved, return nothing
	if(!$shows) {
		return false;
	}
	
	$output = '';
	
	$output .= '<div id="station-show-list">';
	$output .= '<ul>';
	foreach($shows as $show) {
		$output .= '<li>';
		$output .= '<a href="'.get_permalink($show->ID).'">'.get_the_title($show->ID).'</a>';
		$output .= '</li>';
	}
	$output .= '</ul>';
	$output .= '</div>';
	return $output;
}
add_shortcode('list-shows', 'myplaylist_list_shows');

?>