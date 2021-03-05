# Radio Station Plugin Filters

***

## Data Filters

There are filters throughout the plugin that allow you to override data values and plugin output. We employ the practice of adding as many of these as possible to allow users of the plugin to customize it's behaviour without needing to modify the plugin's code - as these kind of modifications are overwritten with plugin updates.

You can add your own custom filters via a Code Snippets plugin (which has the advantage of checking syntax for you), or in your Child Theme's `functions.php`, or in any file with a PHP extension in your `/wp-content/mu-plugins/` directory. 

## Finding Filters

You can find these filters by searching any of PHP plugin files for `apply_filters( 'radio_`. 

## Filter Values and Arguments

Note the first argument passed to `apply_filters` is the name of the filter, the second argument is the value to be filtered. Additional arguments may also be provided to the filter so that you can match changes to specific contexts.

## Filter Examples

You can find many examples and tutorials of how to use WordPress filters online. Here is a generic filter example to help you get started with filters. This one will add custom HTML to the bottom of the Current Show Widget, regardless of which Show is playing:

```
add_filter( 'radio_station_current_show_custom_display', 'my_custom_function_name' );
function my_custom_function_name( $html ) {
    $html .= "<div>Now taking phone requests!</div>";
    return $html;
}
```

Note if a filter has additional arguments, and you wish to check them, you need to specify the number of arguments. To do this you must also include a filter priority. Here `10` is the (default) priority of when to run the filter and `3` is the number of arguments passed to the filter function. This example will add custom HTML to the bottom of the Current Show widget only if the Show ID is 20:

```
add_filter( 'radio_station_current_show_custom_display', 'my_custom_function_name', 10, 3 );
function my_custom_function_name( $html, $show_id, $atts ) {
    if ( 20 == $show_id ) {
        $html .= "<div>Welcoming our newest DJ!</div>";
    }
    return $html;
}
```

## Filter List

Here is a full list of available filters within the plugin, grouped by file and function for ease of reference. 

| File / *Function* | Filter | Value | Extra Args |
| - | - |
|**radio-station.php**||||
|*radio_station_get_template*|`radio_station_template_dir_hierarchy` | ` $dirs` | `$template`, `$paths`|
|*radio_station_automatic_pages_content_set*|`radio_station_automatic_schedule_atts` | ` $atts` | |
| |`radio_station_automatic_show_archive_atts` | ` $atts` | |
| |`radio_station_automatic_override_archive_atts` | ` $atts` | |
| |`radio_station_automatic_playlist_archive_atts` | ` $atts` | |
| |`radio_station_automatic_genre_archive_atts` | ` $atts` | |
|*radio_station_single_content_template*|`radio_station_'.$post_type.'_content_templates` | ` $templates` | `$post_type`|
| |`radio_station_content_'.$post_type` | ` $output` | `$post_id`|
|*radio_station_get_host_template*|`radio_station_host_templates` | ` $templates` | |
|*radio_station_get_producer_template*|`radio_station_producer_templates` | ` $templates` | |
|*radio_station_add_show_links*|`radio_station_show_related_post_types` | ` $post_types` | |
| |`radio_station_link_to_show_positions` | ` $positions` | `$post_type`, `$post`|
| |`radio_station_link_to_show_before` | ` $before` | `$post`, `$related_shows`|
| |`radio_station_link_to_show_after` | ` $after` | `$post`, `$related_shows`|
|*radio_station_get_show_post_link*|`radio_station_show_related_post_types` | ` $related_post_types` | |
| |`radio_station_link_show_posts` | ` true` | `$post`|
|**radio-station-admin.php**||||
|*radio_station_settings_cap_check*|`radio_station_settings_capability` | ` 'manage_options'` | |
|*radio_station_add_admin_menus*|`radio_station_menu_position` | ` 5` | |
| |`radio_station_manage_options_capability` | ` 'manage_options'` | |
| |`radio_station_export_playlists` | ` false` | |
|*radio_station_role_editor*|`radio_station_role_editor_message` | ` true` | |
|**includes/data-feeds.php**||||
|*radio_station_api_link_header*|`radio_station_api_discovery_header` | ` $header` | |
|*radio_station_api_discovery_link*|`radio_station_api_discovery_link` | ` $link` | |
|*radio_station_api_discovery_rsd*|`radio_station_api_discovery_rsd` | ` $link` | |
|*radio_station_get_station_data*|`radio_station_station_data` | ` $station_data` | |
|*radio_station_get_broadcast_data*|`radio_station_broadcast_data` | ` $broadcast` | |
|*radio_station_get_shows_data*|`radio_station_shows_data` | ` $shows` | `$show`|
|*radio_station_get_genres_data*|`radio_station_genres_data` | ` $genres` | `$genre`|
|*radio_station_get_languages_data*|`radio_station_languages_data` | ` $languages_data` | `$language`|
|*radio_station_register_rest_routes*|`radio_station_route_slug_base` | ` 'radio'` | |
| |`radio_station_route_slug_station` | ` 'station'` | |
| |`radio_station_route_slug_broadcast` | ` 'broadcast'` | |
| |`radio_station_route_slug_schedule` | ` 'schedule'` | |
| |`radio_station_route_slug_shows` | ` 'shows'` | |
| |`radio_station_route_slug_genres` | ` 'genres'` | |
| |`radio_station_route_slug_languages` | ` 'languages'` | |
|*radio_station_get_route_urls*|`radio_station_route_urls` | ` $routes` | |
|*radio_station_route_radio*|`radio_station_route_slug_base` | ` 'radio'` | |
|*radio_station_route_station*|`radio_station_route_station` | ` $station` | `$request`|
|*radio_station_route_broadcast*|`radio_station_route_broadcast` | ` $broadcast` | `$request`|
|*radio_station_route_schedule*|`radio_station_route_schedule` | ` $schedule` | `$request`|
|*radio_station_route_shows*|`radio_station_route_shows` | ` $show_list` | `$request`|
|*radio_station_route_genres*|`radio_station_route_genres` | ` $genre_list` | `$request`|
|*radio_station_route_languages*|`radio_station_route_languages` | ` $language_list` | `$request`|
|*radio_station_add_feeds*|`radio_station_feed_slug_base` | ` 'radio'` | |
| |`radio_station_feed_slug_station` | ` 'station'` | |
| |`radio_station_feed_slug_broadcast` | ` 'broadcast'` | |
| |`radio_station_feed_slug_schedule` | ` 'schedule'` | |
| |`radio_station_feed_slug_shows` | ` 'shows'` | |
| |`radio_station_feed_slug_genres` | ` 'genres'` | |
| |`radio_station_feed_slug_languages` | ` 'languages'` | |
| |`radio_station_feed_slugs` | ` $feeds` | |
|*radio_station_get_feed_urls*|`radio_station_feed_urls` | ` $feeds` | |
|*radio_station_feed_radio*|`radio_station_feed_slug_base` | ` 'radio'` | |
| |`radio_station_feed_radio` | ` $radio` | |
|*radio_station_feed_station*|`radio_station_feed_station` | ` $station` | |
|*radio_station_feed_broadcast*|`radio_station_feed_broadcast` | ` $broadcast` | |
|*radio_station_feed_schedule*|`radio_station_feed_schedule` | ` $schedule` | |
|*radio_station_feed_shows*|`radio_station_feed_shows` | ` $show_list` | |
|*radio_station_feed_genres*|`radio_station_feed_genres` | ` $genre_list` | |
|*radio_station_feed_languages*|`radio_station_feed_languages` | ` $language_list` | |
|**includes/master-schedule.php**||||
|*radio_station_master_schedule*|`radio_station_schedule_clock` | ` array()` | `$atts`|
| |`radio_station_schedule_clock` | ` array()` | `$atts`|
| |`radio_station_schedule_control_order` | ` $control_order` | `$atts`|
| |`radio_station_schedule_controls` | ` $controls` | `$atts`|
| |`radio_station_schedule_override` | ` ''` | `$atts`|
| |`master_schedule_table_view` | ` $output` | `$atts`|
| |`master_schedule_tabs_view` | ` $output` | `$atts`|
| |`master_schedule_list_view` | ` $output` | `$atts`|
|**includes/post-types.php**||||
|*radio_station_create_post_types*|`radio_station_post_type_show` | ` $post_type` | |
| |`radio_station_post_type_playlist` | ` $post_type` | |
| |`radio_station_post_type_override` | ` $post_type` | |
| |`radio_station_host_interface` | ` false` | |
| |`radio_station_post_type_host` | ` $post_type` | |
| |`radio_station_producer_interface` | ` false` | |
| |`radio_station_post_type_producer` | ` $post_type` | |
|*radio_station_register_show_taxonomies*|`radio_station_genre_taxonomy_args` | ` $args` | |
| |`radio_station_language_taxonomy_args` | ` $args` | |
|**includes/post-types-admin.php**||||
|*radio_station_add_playlist_metabox*|`radio_station_metabox_position` | ` 'rstop'` | `'playlist'`|
|*radio_station_add_post_show_metabox*|`radio_station_show_related_post_types` | ` $post_types` | |
|*radio_station_add_show_info_metabox*|`radio_station_metabox_position` | ` 'rstop'` | `'shows'`|
|*radio_station_add_show_shifts_metabox*|`radio_station_metabox_position` | ` 'rstop'` | `'shifts'`|
|*radio_station_add_show_helper_box*|`radio_station_metabox_position` | ` 'rstop'` | `'helper'`|
|*radio_station_add_schedule_override_metabox*|`radio_station_metabox_position` | ` 'rstop'` | `'overrides'`|
|*radio_station_override_past_future_filter*|`radio_station_overrides_past_future_default` | ` $pastfuture` | |
|**includes/shortcodes.php**||||
|*radio_station_timezone_shortcode*|`radio_station_timezone_select` | ` ''` | `'radio-station-timezone-'.$instance`, `$atts`|
| |`radio_station_timezone_shortcode` | ` $output` | `$atts`|
|*radio_station_clock_shortcode*|`radio_station_clock_timezone_select` | ` ''` | `'radio-station-clock-'.$instance`, `$atts`|
| |`radio_station_clock` | ` $clock` | `$atts`|
|*radio_station_archive_list_shortcode*|`radio_station_'.$type.'_archive_post_args` | ` $args` | |
| |`radio_station_'.$type.'_archive_posts` | ` $archive_posts` | |
| |`radio_station_time_format_start` | ` $start_data_format` | `$type.'-archive'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `$type.'-archive'`, `$atts`|
| |`radio_station_archive_'.$type.'_list_excerpt_length` | ` false` | |
| |`radio_station_archive_'.$type.'_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_episode_archive_meta` | ` ''` | |
| |`radio_station_host_archive_meta` | ` ''` | |
| |`radio_station_producer_archive_meta` | ` ''` | |
| |`radio_station_'.$type.'_archive_content` | ` $post_content` | `$post_id`|
| |`radio_station_'.$type.'_archive_excerpt` | ` $excerpt` | `$post_id`|
| |`radio_station_'.$type.'_archive_list` | ` $list` | `$atts`|
|*radio_station_genre_archive_list*|`radio_station_genre_archive_post_args` | ` $args` | |
| |`radio_station_genre_archive_posts` | ` $posts` | |
| |`radio_station_genre_image` | ` false` | `$genre`|
| |`radio_station_genre_archive_list` | ` $list` | `$atts`|
|*radio_station_language_archive_list*|`radio_station_language_archive_list` | ` $list` | `$atts`|
|*radio_station_show_list_shortcode*|`radio_station_get_show_episodes` | ` false` | `$show_id`, `$args`|
| |`radio_station_show_'.$type.'_list_excerpt_length` | ` false` | |
| |`radio_station_show_'.$type.'_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_show_'.$type.'_content` | ` $post_content` | `$post_id`|
| |`radio_station_show_'.$type.'_excerpt` | ` $excerpt` | `$post_id`|
| |`radio_station_show_'.$type.'_list` | ` $list` | `$atts`|
|*radio_station_current_show_shortcode*|`radio_station_current_show_dynamic` | ` 0` | `$atts`|
| |`radio_station_widgets_ajax_override` | ` $ajax` | `'current-show'`, `$widget`|
| |`radio_station_current_show_widget_excerpt_length` | ` false` | |
| |`radio_station_current_show_widget_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_current_show_shortcode_excerpt_length` | ` false` | |
| |`radio_station_current_show_shortcode_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_time_format_start` | ` $start_data_format` | `'current-show'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'current-show'`, `$atts`|
| |`radio_station_current_show_link` | ` $show_link` | `$show_id`, `$atts`|
| |`radio_station_current_show_title_display` | ` $title` | `$show_id`, `$atts`|
| |`radio_station_current_show_avatar` | ` $show_avatar` | `$show_id`, `$atts`|
| |`radio_station_current_show_avatar_display` | ` $avatar` | `$show_id`, `$atts`|
| |`radio_station_dj_link` | ` $host_link` | `$host`|
| |`radio_station_current_show_hosts_display` | ` $hosts` | `$show_id`, `$atts`|
| |`radio_station_current_show_encore_display` | ` $encore` | `$show_id`, `$atts`|
| |`radio_station_current_show_playlist_display` | ` $playlist` | `$show_id`, `$atts`|
| |`radio_station_current_show_widget_excerpt` | ` $excerpt` | `$show_id`, `$atts`|
| |`radio_station_current_show_shortcode_excerpt` | ` $excerpt` | `$show_id`, `$atts`|
| |`radio_station_current_show_description_display` | ` $description` | `$show_id`, `$atts`|
| |`radio_station_current_show_shifts_display` | ` $shift_display` | `$show_id`, `$atts`|
| |`radio_station_current_show_custom_display` | ` ''` | `$show_id`, `$atts`|
| |`radio_station_current_show_section_order` | ` $order` | `$atts`|
| |`radio_station_no_current_show_text` | ` $no_current_show` | `$atts`|
| |`radio_station_countdown_dynamic` | ` false` | `'current-show'`, `$atts`, `$current_shift_end`|
|*radio_station_current_show*|`radio_station_current_show_load_script` | ` $js` | `$atts`|
|*radio_station_upcoming_shows_shortcode*|`radio_station_upcoming_shows_dynamic` | ` 0` | `$atts`|
| |`radio_station_widgets_ajax_override` | ` $ajax` | `'upcoming-shows'`, `$widget`|
| |`radio_station_upcoming_shows_section_order` | ` $order` | `$atts`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'upcoming-shows'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'upcoming-shows'`, `$atts`|
| |`radio_station_upcoming_show_link` | ` $show_link` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_title_display` | ` $title` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_avatar` | ` $show_avatar` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_avatar_display` | ` $avatar` | `$show_id`, `$atts`|
| |`radio_station_dj_link` | ` $host_link` | `$host`|
| |`radio_station_upcoming_show_hosts_display` | ` $hosts` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_encore_display` | ` $encore` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_shifts_display` | ` $shift_display` | `$show_id`, `$atts`|
| |`radio_station_upcoming_shows_custom_display` | ` ''` | `$show_id`, `$atts`|
| |`radio_station_no_upcoming_shows_text` | ` $no_upcoming_shows` | `$atts`|
| |`radio_station_countdown_dynamic` | ` false` | `'upcoming-shows'`, `$atts`, `$next_start_time`|
|*radio_station_upcoming_shows*|`radio_station_upcoming_shows_load_script` | ` $js` | `$atts`|
|*radio_station_current_playlist_shortcode*|`radio_station_current_playlist_dynamic` | ` 0` | `$atts`|
| |`radio_station_widgets_ajax_override` | ` $ajax` | `'current-playlist'`, `$widget`|
| |`radio_station_countdown_dynamic` | ` false` | `'current-playlist'`, `$atts`, `$shift_end_time`|
| |`radio_station_current_playlist_tracks_display` | ` $tracks` | `$playlist`, `$atts`|
| |`radio_station_current_playlist_link_display` | ` $link` | `$playlist`, `$atts`|
| |`radio_station_current_playlist_custom_display` | ` ''` | `$playlist`, `$atts`|
| |`radio_station_current_playlist_section_order` | ` $order` | `$atts`|
| |`radio_station_no_current_playlist_text` | ` $no_current_playlist` | `$atts`|
| |`radio_station_current_playlist_no_playlist_display` | ` $no_playlist` | `$atts`|
|*radio_station_current_playlist*|`radio_station_current_playlist_load_script` | ` $js` | `$atts`|
|**includes/support-functions.php**||||
|*radio_station_get_shows*|`radio_station_get_shows` | ` $shows` | `$defaults`|
|*radio_station_get_show_shifts*|`radio_station_show_shifts` | ` $day_shifts` | |
|*radio_station_get_overrides*|`radio_station_get_overrides` | ` $override_list` | `$start_date`, `$end_date`|
|*radio_station_get_show_data*|`radio_station_cached_data` | ` false` | `$datatype`, `$show_id`|
| |`radio_station_show_'.$datatype` | ` $results` | `$show_id`, `$args`|
|*radio_station_get_show_data_meta*|`radio_station_show_data_meta` | ` $show_data` | `$show_id`|
|*radio_station_get_show_description*|`radio_station_show_data_excerpt_length` | ` false` | |
| |`radio_station_show_data_excerpt_more` | ` ''` | |
| |`radio_station_show_data_description` | ` $description` | `$show_id`|
| |`radio_station_show_data_excerpt` | ` $excerpt` | `$show_id`|
|*radio_station_get_override_data_meta*|`radio_station_override_data` | ` $override_data` | `$override_id`|
|*radio_station_get_current_schedule*|`radio_station_previous_show` | ` $prev_shift` | `$time`|
| |`radio_station_previous_show` | ` $prev_shift` | `$time`|
| |`radio_station_current_schedule` | ` $show_shifts` | `$time`|
|*radio_station_get_current_show*|`radio_station_previous_show` | ` $prev_shift` | `$time`|
| |`radio_station_current_show` | ` $current_show` | `$time`|
|*radio_station_get_next_show*|`radio_station_next_show` | ` $next_show` | `$time`|
|*radio_station_get_next_shows*|`radio_station_next_show` | ` $next_show` | `$time`|
| |`radio_station_next_shows` | ` $next_shows` | `$limit`, `$show_shifts`|
| |`radio_station_next_shows` | ` $next_shows` | `$limit`, `$show_shifts`|
|*radio_station_get_genres*|`radio_station_get_genres` | ` $genres` | `$args`|
|*radio_station_get_genre_shows*|`radio_station_show_genres_query_args` | ` $args` | `$genre`|
|*radio_station_get_language_shows*|`radio_station_show_languages_query_args` | ` $args` | `$language`|
|*radio_station_get_show_avatar_id*|`radio_station_show_avatar_id` | ` $avatar_id` | `$show_id`|
|*radio_station_get_show_avatar_url*|`radio_station_show_avatar_url` | ` $avatar_url` | `$show_id`|
|*radio_station_get_show_avatar*|`radio_station_show_avatar` | ` $avatar` | `$show_id`|
|*radio_station_get_stream_url*|`radio_station_stream_url` | ` $streaming_url` | |
|*radio_station_get_stream_formats*|`radio_station_stream_formats` | ` $formats` | |
|*radio_station_get_station_url*|`radio_station_station_url` | ` $station_url` | |
|*radio_station_get_station_image_url*|`radio_station_station_image_url` | ` $station_image` | |
|*radio_station_get_schedule_url*|`radio_station_schedule_url` | ` $schedule_url` | |
|*radio_station_get_api_url*|`radio_station_api_url` | ` $api_url` | |
|*radio_station_get_route_url*|`radio_station_route_slug_base` | ` 'radio'` | |
| |`radio_station_route_slug_'.$route` | ` $route` | |
|*radio_station_get_feed_url*|`radio_station_feed_slug_'.$feedname` | ` $feedname` | |
|*radio_station_get_host_url*|`radio_station_host_url` | ` $host_url` | `$host_id`|
|*radio_station_get_producer_url*|`radio_station_producer_url` | ` $producer_url` | `$producer_id`|
|*radio_station_patreon_button*|`radio_station_patreon_button` | ` $button` | `$page`|
|*radio_station_get_timezone_options*|`radio_station_get_timezone_options` | ` $options` | `$include_wp_timezone`|
|*radio_station_get_schedule_weekdays*|`radio_station_schedule_weekday_start` | ` $weekstart` | |
|*radio_station_get_languages*|`radio_station_get_languages` | ` $translations` | |
|*radio_station_get_language_options*|`radio_station_get_language_options` | ` $languages` | `$include_wp_default`|
|*radio_station_trim_excerpt*|`radio_station_excerpt_length` | ` $length` | |
| |`radio_station_excerpt_more` | ` ' [&hellip;]'` | |
| |`radio_station_trim_excerpt` | ` $excerpt` | `$raw_content`, `$length`, `$more`, `$permalink`|
|**includes/class-current-show-widget.php**||||
|*form*|`radio_station_current_show_widget_fields` | ` $fields` | `$this`, `$instance`|
|*update*|`radio_station_current_show_widget_update` | ` $instance` | `$new_instance`, `$old_instance`|
|*widget*|`radio_station_current_show_widget_override` | ` $output` | `$args`, `$atts`|
|**includes/class-upcoming-shows-widget.php**||||
|*form*|`radio_station_upcoming_shows_widget_fields` | ` $fields` | `$this`, `$instance`|
|*update*|`radio_station_upcoming_shows_widget_update` | ` $instance` | `$new_instance`, `$old_instance`|
|*widget*|`radio_station_upcoming_shows_widget_override` | ` $output` | `$args`, `$atts`|
|**includes/class-current-playlist-widget.php**||||
|*form*|`radio_station_playlist_widget_fields` | ` $fields` | `$this`, `$instance`|
|*update*|`radio_station_playlist_widget_update` | ` $instance` | `$new_instance`, `$old_instance`|
|*widget*|`radio_station_current_playlist_widget_override` | ` $output` | `$args`, `$atts`|
|**includes/class-radio-clock-widget.php**||||
| |`radio_station_radio_clock_widget_override` | ` $output` | `$args`, `$atts`|
|**templates/master-schedule-table.php**||||
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-table'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-table'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'table'`|
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'table'`|
| |`radio_station_schedule_table_excerpt_length` | ` false` | |
| |`radio_station_schedule_table_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_schedule_table_info_order` | ` $infokeys` | |
| |`radio_station_schedule_arrows` | ` $arrows` | `'table'`|
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_title_display` | ` $title` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'table'`, `$shift`|
| |`radio_station_schedule_show_time_display` | ` true` | `$show_id`, `'table'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'table'`|
| |`radio_station_schedule_show_excerpt` | ` $show_excerpt` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_excerpt_display` | ` $excerpy` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'table'`|
|**templates/master-schedule-tabs.php**||||
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-tabs'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-tabs'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'tabs'`|
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'tabs'`|
| |`radio_station_schedule_tabs_excerpt_length` | ` false` | |
| |`radio_station_schedule_tabs_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_schedule_tabs_info_order` | ` $infokeys` | |
| |`radio_station_schedule_arrows` | ` $arrows` | `'tabs'`|
| |`radio_station_schedule_tabs_avatar_position_start` | ` $avatar_position` | |
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_title_display` | ` $title` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'tabs'`, `$shift`|
| |`radio_station_schedule_show_times_display` | ` true` | `$show_id`, `'tabs'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_genres` | ` $genres` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_excerpt` | ` $show_excerpt` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_excerpt_display` | ` $excerpt` | `$show_id`, `'tabs'`|
|**templates/master-schedule-legacy.php**||||
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'legacy'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show['id']`, `'legacy'`|
| |`radio_station_schedule_show_link` | ` $show_link` | `$show['id']`, `'legacy'`|
| |`radio_station_schedule_show_time` | ` $times` | `$show['id']`, `'legacy'`|
| |`radio_station_schedule_show_encore` | ` $encore` | `$show['id']`, `'legacy'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show['id']`, `'legacy'`|
|**templates/master-schedule-list.php**||||
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-list'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-list'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'list'`|
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'list'`|
| |`radio_station_schedule_list_excerpt_length` | ` false` | |
| |`radio_station_schedule_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_schedule_list_info_order` | ` $infokeys` | |
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_title` | ` $title` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'list'`, `$shift`|
| |`radio_station_schedule_show_time_display` | ` true` | `$show_id`, `'list'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'list'`|
| |`radio_station_schedule_show_genres_display` | ` $genres` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_excerpt` | ` $show_excerpt` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'list'`|
|**templates/single-playlist-content.php**||||
| |`radio_station_link_playlist_to_show_before` | ` $before` | `$post`, `$show`|
| |`radio_station_link_playlist_to_show_before` | ` $after` | `$post`, `$show`|
|**templates/single-show-content.php**||||
| |`radio_station_show_title` | ` $show_title` | `$post_id`|
| |`radio_station_show_header` | ` $header_id` | `$post_id`|
| |`radio_station_show_avatar` | ` $avatar_id` | `$post_id`|
| |`radio_station_show_thumbnail` | ` $thumbnail_id` | `$post_id`|
| |`radio_station_show_genres` | ` $genres` | `$post_id`|
| |`radio_station_show_languages` | ` $languages` | `$post_id`|
| |`radio_station_show_hosts` | ` $hosts` | `$post_id`|
| |`radio_station_show_producers` | ` $producers` | `$post_id`|
| |`radio_station_show_active` | ` $active` | `$post_id`|
| |`radio_station_show_shifts` | ` $shifts` | `$post_id`|
| |`radio_station_show_file` | ` $show_file` | `$post_id`|
| |`radio_station_show_download` | ` $show_download` | `$post_id`|
| |`radio_station_show_link` | ` $show_link` | `$post_id`|
| |`radio_station_show_email` | ` $show_email` | `$post_id`|
| |`radio_station_show_phone` | ` $show_phone` | `$post_id`|
| |`radio_station_show_patreon` | ` $show_patreon` | `$post_id`|
| |`radio_station_show_rss` | ` $show_rss` | `$post_id`|
| |`radio_station_show_social_icons` | ` false` | `$post_id`|
| |`radio_station_show_website_title` | ` $title` | `$post_id`|
| |`radio_station_show_home_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_phone_title` | ` $title` | `$post_id`|
| |`radio_station_show_phone_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_email_title` | ` $title` | `$post_id`|
| |`radio_station_show_email_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_rss_title` | ` $title` | `$post_id`|
| |`radio_station_show_rss_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_page_icons` | ` $show_icons` | `$post_id`|
| |`radio_station_show_page_latest_limit` | ` $latest_limit` | `$post_id`|
| |`radio_station_show_page_posts_limit` | ` false` | `$post_id`|
| |`radio_station_show_page_playlist_limit` | ` false` | `$post_id`|
| |`radio_station_show_page_episodes` | ` false` | `$post_id`|
| |`radio_station_show_jump_links` | ` 'yes'` | `$post_id`|
| |`radio_station_show_avatar_size` | ` 'medium'` | `$post_id`, `'show-page'`|
| |`radio_station_show_social_icons_display` | ` ''` | |
| |`radio_station_show_patreon_title` | ` $title` | `$post_id`|
| |`radio_station_show_patreon_button` | ` $patreon_button` | `$post_id`|
| |`radio_station_show_player_label` | ` ''` | `$post_id`|
| |`radio_station_show_download_title` | ` $title` | `$post_id`|
| |`radio_station_show_images_blocks` | ` $image_blocks` | `$post_id`|
| |`radio_station_show_image_block_order` | ` $image_block_order` | `$post_id`|
| |`radio_station_show_info_label` | ` $label` | `$post_id`|
| |`radio_station_show_hosts_label` | ` $label` | `$post_id`|
| |`radio_station_show_producers_label` | ` $label` | `$post_id`|
| |`radio_station_show_genres_label` | ` $label` | `$post_id`|
| |`radio_station_show_languages_label` | ` $label` | `$post_id`|
| |`radio_station_show_phone_label` | ` $label` | `$post_id`|
| |`radio_station_show_meta_blocks` | ` $meta_blocks` | `$post_id`|
| |`radio_station_show_meta_block_order` | ` $meta_block_order` | `$post_id`|
| |`radio_station_show_times_label` | ` $label` | `$post_id`|
| |`radio_station_show_no_shifts_label` | ` $label` | `$post_id`|
| |`radio_station_show_timezone_label` | ` $label` | `$post_id`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'show-template'`, `$post_id`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'show-template'`, `$post_id`|
| |`radio_station_show_encore_label` | ` $label` | `$post_id`|
| |`radio_station_show_schedule_link_title` | ` $title` | `$post_id`|
| |`radio_station_show_schedule_link_anchor` | ` $label` | `$post_id`|
| |`radio_station_show_page_blocks` | ` $blocks` | `$post_id`|
| |`radio_station_show_more_label` | ` $label` | `$post_id`|
| |`radio_station_show_less_label` | ` $label` | `$post_id`|
| |`radio_station_show_description_label` | ` $label` | `$post_id`|
| |`radio_station_show_description_anchor` | ` $anchor` | `$post_id`|
| |`radio_station_show_episodes_label` | ` $label` | `$post_id`|
| |`radio_station_show_episodes_anchor` | ` $anchor` | `$post_id`|
| |`radio_station_show_page_episodes_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_show_posts_label` | ` $label` | `$post_id`|
| |`radio_station_show_posts_anchor` | ` $anchor` | `$post_id`|
| |`radio_station_show_page_posts_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station-show_playlists_label` | ` $label` | `$post_id`|
| |`radio_station_show_playlists_anchor` | ` $anchor` | `$post_id`|
| |`radio_station_show_page_playlists_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_show_page_sections` | ` $sections` | `$post_id`|
| |`radio_station_show_header_size` | ` 'full'` | `$post_id`|
| |`radio_station_show_page_header_image` | ` $header_image` | `$post_id`|
| |`radio_station_show_page_block_order` | ` $block_order` | `$post_id`|
| |`radio_station_show_latest_posts_label` | ` $label` | `$post_id`|
| |`radio_station_show_page_latest_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_show_page_section_order` | ` $section_order` | `$post_id`|
