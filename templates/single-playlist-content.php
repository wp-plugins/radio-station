<?php

// ---------------------
// === Radio Station ===
// ---------------------
// -- Single Playlist --
// -- Content Template -
// ---------------------

// --- link show playlists (top) ---
$post_id = get_the_ID();
$related_show = get_post_meta( $post_id, 'playlist_show_id', true );
if ( $related_show ) {
	$show = get_post( $related_show );
	$permalink = get_permalink( $show->ID );
	$show_link = '<a href="' . esc_url( $permalink ) . '">' . $show->post_title . '</a>';
	$before = __( 'Playlist for Show', 'radio-station' ) . ': ' . $show_link . '<br>';
	$before = apply_filters( 'radio_station_link_playlist_to_show_before', $before, $post, $show );
	echo $before;
}

// --- output the playlist post content ---
echo '<br>';
the_content();
		
// --- get the playlist data ---
$playlist = get_post_meta( $post_id, 'playlist', true );

if ( $playlist ) {

	// 2.4.0.3: added check for played tracks
	$found = false;
	foreach ( $playlist as $i => $track ) {
		if ( 'played' == $track['playlist_entry_status'] ) {
			$found = true;
		}
	}

	if ( $found ) {

		echo '<div class="myplaylist-playlist-entries">' . PHP_EOL;
			echo '<table>' . PHP_EOL;
			echo '<tr>' . PHP_EOL;
				echo '<th>#</th>' . PHP_EOL;
				echo '<th>' . esc_html( __( 'Artist', 'radio-station' ) ) . '</th>' . PHP_EOL;
				echo '<th>' . esc_html( __( 'Song', 'radio-station' ) ) . '</th>' . PHP_EOL;
				echo '<th>' . esc_html( __( 'Album', 'radio-station' ) ) . '</th>' . PHP_EOL;
				echo '<th>' . esc_html( __( 'Label', 'radio-station' ) ) . '</th>' . PHP_EOL;
				echo '<th>' . esc_html( __( 'Comments', 'radio-station' ) ) . '</th>' . PHP_EOL;
			echo '</tr>' . PHP_EOL;

			$count = 1;
			foreach ( $playlist as $entry ) {
				if ( 'played' === $entry['playlist_entry_status'] ) {

					// 2.4.0.3: remove new class it behavior changed
					// $new_class = '';
					// if ( isset( $entry['playlist_entry_new'] ) && 'on' === $entry['playlist_entry_new'] ) {
					// 	$new_class = 'class="new"';
					// }

					echo '<tr>' . PHP_EOL;
						echo '<td>' . esc_attr( $count ) . '</td>' . PHP_EOL;
						echo '<td>' . esc_html( $entry['playlist_entry_artist'] ) . '</td>' . PHP_EOL;
						echo '<td>' . esc_html( $entry['playlist_entry_song'] ) . '</td>' . PHP_EOL;
						echo '<td>' . esc_html( $entry['playlist_entry_album'] ) . '</td>' . PHP_EOL;
						echo '<td>' . esc_html( $entry['playlist_entry_label'] ) . '</td>' . PHP_EOL;
						echo '<td>' . esc_html( $entry['playlist_entry_comments'] ) . '</td>' . PHP_EOL;
					echo '</tr>' . PHP_EOL;
					$count++;
				}
			}
			echo '</table>' . PHP_EOL;
		echo '</div>' . PHP_EOL;

	}  else {
	
		// 2.4.0.3: added text to indicate no played tracks
		echo '<div class="myplaylist-no-entries">' . PHP_EOL;
			echo esc_html( __( 'No played tracks found for this Playlist yet.', 'radio-station' ) );
		echo '</div>' . PHP_EOL;
	}

} else {

	// --- not playlist entries message ---
	echo '<div class="myplaylist-no-entries">' . PHP_EOL;
		echo esc_html( __( 'No entries found for this Playlist', 'radio-station' ) ) . PHP_EOL;
	echo '</div>' . PHP_EOL;

} 

// --- link show playlists (bottom) ---
if ( $related_show ) {
	$show_playlists_link = '<a href="' . esc_url( $permalink ) . '#show-playlists">&larr; ' . __( 'All Playlists for Show', 'radio-station' ) . ': ' . $show->post_title . '</a>';
	$after = '<br>' . $show_playlists_link . PHP_EOL;;
	// 2.4.0.3: fix filter name to after not before
	$after = apply_filters( 'radio_station_link_playlist_to_show_after', $after, $post, $show );
	echo $after;
}

