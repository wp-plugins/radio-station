=== Radio Station ===
Contributors: kionae
Donate link: http://www.nlb-creations.com/donate
Tags: dj, music, playlist, radio, scheduling
Requires at least: 3.3.1
Tested up to: 3.7.1
Stable tag: trunk

Radio Station is a plugin to run a radio station's website. It's functionality is based on Drupal 6's Station plugin.

== Description ==

Radio Station is a plugin to run a radio station's website. It's functionality is based on Drupal 6's Station plugin, reworked for use in Wordpress.  The plugin 
includes the ability to associate users with "shows" (schedulable blocks of time that contain a description, and other meta information), and generate playlists
associated with those shows.  The plugin contains a widget to display the currently on-air DJ with a link to the DJ's show and current playlist.  A schedule of
all show can also be generated.

== Installation ==

1. Upload plugin .zip file to the `/wp-content/plugins/` directory and unzip.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Give any users who need access to the plugin the role of "DJ".  Only DJ and administrator roles have administrative access.
4. Create shows and set up shifts.
5. Add playlists to your shows.

== Frequently Asked Questions ==

= I'm seeing 404 Not Found errors when I click on the link for a show! = 
Try re-saving your site's permalink settings.  Wordpress sometimes gets confused with a custom post type is added.

= How do I display a full schedule of my station's shows? =

Use the shortcode `[master-schedule]` on any page.  This will generate a table containing your show details.

The following attributes are available for the shortcode:
			'time' => The time format you with to use.  Valid values are 12 and 24.  Default is 12.
			'show_link' => Display the title of the show as a link to its profile page.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'display_show_time' => Display start and end times of each show after the title in the grid.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'list' => If set to a value of 1, the schedule will display in list format rather than table format.  Default value is 0.
			
For example, if you wish to display the schedule in 24-hour time format, use `[master-schedule time="24"]`.

= How do I schedule a show? =

Simply create a new show.  You will be able to aside it to any timeslot you wish on the edit page.

= What if I have a special event? =

If you have a one-off event that you need to show up in the On-Air or Coming Up Next widgets, you can create a Schedule Override by clicking the Schedule Override tab
in the Dashboard menu.  This will allow you to set aside a block of time on a specific date, and will display the title you give it in the widgets.  Please note that 
this will only override the widgets and their corresponding shortcodes.  If you are using the weekly master schedule shortcode on a page, its output will not be altered.

= How do I get the last song played to show up? = 

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[now-playing]` in your page/post, or use `do_shortcode('[now-playing]');` in your template files.

The following attributes are available for the shortcode:
			'title' => The title you would like to appear over the now playing block
			'artist' => Display artist name.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'song' => Display song name.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'album' => Display album name.  Valid values are 0 for hide, 1 for show.  Default is 0.
			'label' => Display label name.  Valid values are 0 for hide, 1 for show.  Default is 0.
			'comments' => Display DJ comments.  Valid values are 0 for hide, 1 for show.  Default is 0.

Example:
`[now-playing title="Current Song" artist="1" song="1" album="1" label="1" comments="0"]`

= What about displaying the current DJ on air? =

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[dj-widget]` in your page/post, or you can use
`do_shortcode('[dj-widget]');` in your template files.

The following attributes are available for the shortcode:
		'title' => The title you would like to appear over the on-air block 
		'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
		'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
		'default_name' => The text you would like to display when no show is schedule for the current time.
		'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.
		'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
		'show_playlist' => Display a link to the show's current playlist.  Valid values are 0 for hide link, 1 for show link.  Default is 1.
		'show_all_sched' => Displays all schedules for a show if it airs on multiple days.  Valid values are 0 for current schedule, 1 for all schedules.  Default is 0.
		
Example:
`[dj-widget title="Now On-Air" show_avatar="1" show_link="1" default_name="RadioBot" time="12" schow_sched="1" show_playlist="1"]`


= Can I display upcoming shows, too? =

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[dj-coming-up-widget]` in your page/post, or you can use
`do_shortcode('[dj-coming-up-widget]');` in your template files.

The following attributes are available for the shortcode:
		'title' => The title you would like to appear over the on-air block 
		'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
		'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
		'limit' => The number of upcoming shows to display.  Default is 1.
		'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.
		'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
		
Example:
`[dj-widget title="Coming Up On-Air" show_avatar="1" show_link="1" limit="3" time="12" schow_sched="1"]`

= Can I change how show pages are laid out/displayed? =

Yes.  Copy the radio-station/templates/single-show.php file into your theme directory, and alter as you wish.  This template, and all of the other templates
in this plugin, are based on the TwentyEleven theme.  If you're using a different theme, you may have to rework them to reflect your theme's layout.

= What about playlist pages? =

Same deal.  Grab the radio-station/templates/single-playlist.php file, copy it to your theme directory, and go to town.

= And playlist archive pages?  =

Same deal.  Grab the radio-station/templates/archive-playlist.php file, copy it to your theme directory, and go to town.

= And the program schedule, too? = 

Because of the complexity of outputting the data, you can't directly alter the template, but you can copy the radio-station/templates/program-schedule.css file
into your theme directory and change the CSS rules for the page.

= What if I want to style the DJ on air sidebar widget? =

Copy the radio-station/templates/djonair.css file to your theme directory.

= How do I get an archive page that lists ALL of the playlists instead of just the archives of individual shows? =

First, grab the radio-station/templates/playlist-archive-template.php file, and copy it to your active theme directory.  Then, create a Page in wordpress
to hold the playlist archive.  Under Page Attributes, set the template to Playlist Archive.  Please note: If you don't copy the template file to your theme first, 
the option to select it will not appear.

= Can show pages link to an archive of related blog posts? =

Yes, in much the same way as the full playlist archive described above. First, grab the radio-station/templates/show-blog-archive-template.php file, and copy it to 
your active theme directory.  Then, create a Page in wordpress to hold the blog archive.  Under Page Attributes, set the template to Show Blog Archive.

= How can I list all of my shows? =

Use the shortcode `[list-shows]` in your page/posts or use `do_shortcode(['list-shows']);` in your template files.  This will output an unordered list element
containing the titles of and links to all shows marked as "Active". 

The following attributes are available for the shortcode:
		'genre' => Displays shows only from the specified genre(s).  Separate multiple genres with a comma, e.g. genre="pop,rock".

Example:
`[list-shows genre="pop"]`
`[list-shows genre="pop,rock,metal"]`

= I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types.  How do I do that? =

Since I'm stongly opposed to reinventing the wheel, I recommend Justin Tadlock's excellent "Members" plugin for that purpose.  You can find it on
Wordpress.org, here: http://wordpress.org/extend/plugins/members/

Add the following capabilities to any role you want to give access to Shows and Playlist:

edit_shows
edit_published_shows
edit_others_shows
read_shows
edit_playlists
edit_published_playlists
read_playlists
publish_playlists
read
upload_files
edit_posts
edit_published_posts
publish_posts

If you want the new role to be able to create or approve new shows, you should also give them the following capabilities:

publish_shows
edit_others_shows

= How do I change the DJ's avatar in the sidebar widget? =

The avatar is whatever image is assigned as the DJ/Show's featured image.  All you have to do is set a new featured image.

= Why don't any users show up in the DJs list on the Show edit page? =

You did remember to assign the DJ role to the users you want to be DJs, right?

= My DJs can't edit a show page.  What do I do? = 

The only DJs that can edit a show are the ones listed as being ON that show in the DJs select menu.  This is to prevent DJs from editing other DJs shows 
without permission.

= How can I export a list of songs played on a given date? =

Under the Playlists menu in the dashboard is an Export link.  Simply specify the a date range, and a text file will be generated for you.

= Can my DJ's have customized user pages in addition to Show pages? = 

Yes.  These pages are the same as any other author page (edit or create the author.php template file in your theme directory).  A sample can be found 
in the radio-station/templates/author.php file.  Like the other theme templates included with this plugin, this file is based on the TwentyEleven theme.

= I don't want to use Gravatar for my DJ's image on their profile page. =

Then you'll need to install a plugin that lets you add a different image to your DJ's user account and edit your author.php theme file accordingly.  That's a 
little out of the scope of this plugin.  I recommend Cimy User Extra Fields:  http://wordpress.org/extend/plugins/cimy-user-extra-fields/

= What languages other than English is the plugin available in? =

Right now:

Spanish (es_ES)
French (fr_FR)
Albanian (sq_AL)
Serbian (sr_RS)
Italian (it_IT)

= Can you translate the plugin into my language? =

My foreign language skills are rather lacking.  I managed a Spanish translation, sheerly due to the fact that I still remember at least some of what 
I learned in high school Spanish class.  But I've included the .pot file in the /languages directory.  If you want to give it a shot, be my guest.  If 
you send me your finished translation, I'd love to include it.

== Changelog ==

= 2.0.0 =
* Major code reorganization for better future development
* PHP warning fix
* Enabled option to add comments on Shows and Playlists
* Added option to show either single or multiple schedules in the On Air widget

= 1.6.2 =
* Minor PHP warning fixes

= 1.6.1 =
* Bug fix: Some of the code added in the previous update uses the array_replace() function that is only available in PHP 5.3+.  Added a fallback for older PHP versions.

= 1.6.0 =
* Added the ability to override the weekly schedule to allow one-off events to be scheduled
* Added a list format option to the master schedule shortcode
* Added Italian translation (it_IT) (thank you to Cristofaro Giuseppe!)

= 1.5.4 =
* Fixed some PHP notices that were being generated when there were no playlist entries in the system.

= 1.5.3 =
* Added Serbian translation (sr_RS) (thank you to Miodarag Zivkovic!)

= 1.5.2.1 =
* Removed some debug code from one of the template files

= 1.5.2 =
* Fixed some localization bugs.
* Added Albanian translation (sq_AL) (thank you to Lorenc!)

= 1.5.1 =
* Fixed some localization bugs.
* Added French translation (fr_FR) (a big thank you to Dan over at BuddyPress France - http://bp-fr.net/)

= 1.5.0 =
* Plugin modified to allow for internationalization.
* Spanish translation (es_ES) added.

= 1.4.6 =
* Fixed a bug with shows that start at midnight not displaying in the on-air sidebar widget.
* Switched DJ/Show avatars in the widgets to use the featured image of the show instead of gravatar.
* Updated show template to get rid of a PHP warning that appeared if the show had no schedules. 
* Fixed some other areas of the code that were generating PHP notices in WordPress 3.6
* Added CSS classes to master program schedule output so CSS rules can be applied to specific shows
* Added new attribute to the list-shows shortcode to allow only specified genres to be displayed

= 1.4.5 =
* Fixed master-schedule shortcode bug that was preventing display of 12 hour time

= 1.4.4 =
* Compatibility fix for Wordpress 3.6 - fixed problem with giving alternative roles DJ capabilities
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6

= 1.4.3 =
* Master schedule shortcode now displays indiviual shows in both 24 and 12 hour time
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6
* Added example of how to display show schedule to single-show.php template
* Added more options to the plugin's widgets
* Added new options to the master-schedule shortcode

= 1.4.2 =
* Fixed a bug in the CSS file override from theme directory

= 1.4.1 =
* Fixed issue with templates copied to the theme directory not overriding the defaults correctly
* Fixed incorrectly implemented wp_enqueue_styles()
* Removed deprecated escape_attribute() function from the plugin widgets
* Fixed some areas of the code that were generating PHP notices

= 1.4.0 =
* Compatibility fix for WordPress 3.6

= 1.3.9 =
* Fixed a bug that was preventing sites using a non-default table prefix from seeing the list of DJs on the add/edit show pages

= 1.3.8 =
* Changes to fix the incorrect list of available shows on the Add Playlist page
* Removing Add Show links from admin menu for DJs, since they don't have permission to use them anyway.

= 1.3.7 =
* Fixed a scheduling bug in the upcoming shows widget
* By popular request, switched the order of artist and song in the now playing widget

= 1.3.6 =
* Fixed issue with shows that run overnight not showing up correctly in the sidebar widgets

= 1.3.5 =
* Fixed a time display bug in the DJ On-Air sidebar widget
* Fixed a display bug on the master schedule with overnight shows

= 1.3.4 =
* By request, added as 24-hour time format option to the master schedule and sidebar widgets.

= 1.3.3 =
* Added the ability to assign any user with the edit_shows capability as a DJ, to accomodate custom and edited roles.

= 1.3.2 =
* Fixed a bug in the DJ-on-air widget

= 1.3.1 =
* Fixed a major bug in the master schedule output

= 1.3 =
* Fixed some minor compatibility issues with WordPress 3.5
* Fixed Shows icon in Dashboard

= 1.2 =
* Fixed thumbnail bug in sidebar widgets
* Added new widget to display upcoming shows
* Added pagination options for playlists and show blogs

= 1.1 =
* Fixed playlist edit screen so that queued songs fall to the bottom of the list to maintain play order
* Reduced the size of the content field in the playlist post type
* Some minor formatting changes to default templates
* Added genre highlighter to the master programming schedule page
* Added a second Update button on the bottom of the playlist edit page for convinience.
* Added sample template for DJ user pages
* Fixed a bug in the master schedule shortcode that messed up the table for shows that are more than two hours in duration
* Fixed a bug in the master schedule shortcode to accomodate shows that run from late night into the following morning.
* Added new field to associate blog posts with shows

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.0.0 =
* Major code reorganization for better future development
* PHP warning fix
* Enabled option to add comments on Shows and Playlists
* Added option to show either single or multiple schedules in the On Air widget

= 1.6.2 =
* Minor PHP warning fixes

= 1.6.1 =
* Bug fix: Some of the code added in the previous update uses the array_replace() function that is only available in PHP 5.3+.  Added a fallback for older PHP versions.

= 1.6.0 =
* Added the ability to override the weekly schedule to allow one-off events to be scheduled
* Added a list format option to the master schedule shortcode
* Added Italian translation (it_IT) (thank you to Cristofaro Giuseppe!)

= 1.5.4 =
* Fixed some PHP notices that were being generated when there were no playlist entries in the system.

= 1.5.3 =
* Added Serbian translation (sr_RS) (thank you to Miodarag Zivkovic!)

= 1.5.2.1 =
* Removed some debug code from one of the template files

= 1.5.2 =
* Fixed some localization bugs.
* Added Albanian translation (sq_AL) (thank you to Lorenc!)

= 1.5.1 =
* Fixed some localization bugs.
* Added French translation (fr_FR) (a big thank you to Dan over at BuddyPress France - http://bp-fr.net/)

= 1.5.0 =
* Plugin modified to allow for internationalization.
* Spanish translation (es_ES) added.

= 1.4.6 =
* Fixed a bug with shows that start at midnight not displaying in the on-air sidebar widget.
* Switched DJ/Show avatars in the widgets to use the featured image of the show instead of gravatar.
* Updated show template to get rid of a PHP warning that appeared if the show had no schedules. 
* Fixed some other areas of the code that were generating PHP notices in WordPress 3.6
* Added CSS classes to master program schedule output so CSS rules can be applied to specific shows
* Added new attribute to the list-shows shortcode to allow only specified genres to be displayed

= 1.4.5 =
* Fixed master-schedule shortcode bug that was preventing display of 12 hour time

= 1.4.4 =
* Compatibility fix for Wordpress 3.6 - fixed problem with giving alternative roles DJ capabilities
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6

= 1.4.3 =
* Master schedule shortcode now displays indiviual shows in both 24 and 12 hour time
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6
* Added example of how to display show schedule to single-show.php template
* Added more options to the plugin's widgets
* Added new options to the master-schedule shortcode

= 1.4.2 =
* Fixed a bug in the CSS file override from theme directory

= 1.4.1 =
* Fixed issue with templates copied to the theme directory not overriding the defaults correctly
* Fixed incorrectly implemented wp_enqueue_styles()
* Removed deprecated escape_attribute() function from the plugin widgets
* Fixed some areas of the code that were generating PHP notices

= 1.4.0 =
* Compatibility fix for WordPress 3.6

= 1.3.9 =
* Fixed a bug that was preventing sites using a non-default table prefix from seeing the list of DJs on the add/edit show pages

= 1.3.8 =
* Changes to fix the incorrect list of available shows on the Add Playlist page
* Removing Add Show links from admin menu for DJs, since they don't have permission to use them anyway.

= 1.3.7 =
* Fixed a scheduling bug in the upcoming shows widget
* By popular request, switched the order of artist and song in the now playing widget

= 1.3.6 =
* Fixed issue with shows that run overnight not showing up correctly in the sidebar widgets

= 1.3.5 =
* Fixed a time display bug in the DJ On-Air sidebar widget
* Fixed a display bug on the master schedule with overnight shows

= 1.3.4 =
* By request, added as 24-hour time format option to the master schedule and sidebar widgets.

= 1.3.3 =
* Added the ability to assign any user with the edit_shows capability as a DJ, to accomodate custom and edited roles.

= 1.3.2 =
* Fixed a bug in the DJ-on-air widget

= 1.3.1 =
* Fixed a major bug in the master schedule output

= 1.3 =
* Fixed some minor compatibility issues with WordPress 3.5
* Fixed Shows icon in Dashboard

= 1.2 =
* Fixed thumbnail bug in sidebar widgets
* Added new widget to display upcoming shows
* Added pagination options for playlists and show blogs

= 1.1 =
* Fixed playlist edit screen so that queued songs fall to the bottom of the list to maintain play order
* Reduced the size of the content field in the playlist post type
* Some minor formatting changes to default templates
* Added genre highlighter to the master programming schedule page
* Added a second Update button on the bottom of the playlist edit page for convinience.
* Added sample template for DJ user pages
* Fixed a bug in the master schedule shortcode that messed up the table for shows that are more than two hours in duration
* Fixed a bug in the master schedule shortcode to accomodate shows that run from late night into the following morning.
* Added new field to associate blog posts with shows

= 1.0 =
* Initial release  