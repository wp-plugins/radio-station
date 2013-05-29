=== Radio Station ===
Contributors: kionae
Donate link: http://www.nlb-creations.com/donate
Tags: dj, music, playlist, radio, scheduling
Requires at least: 3.3.1
Tested up to: 3.5.1
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

If you wish to display the schedule in 24-hour time format, user `[master-schedule time="24"]`.

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
		
Example:
`[dj-widget title="Now On-Air" show_avatar="1" show_link="1" default_name="RadioBot" time="12"]`


= Can I display upcoming shows, too? =

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[dj-coming-up-widget]` in your page/post, or you can use
`do_shortcode('[dj-coming-up-widget]');` in your template files.

The following attributes are available for the shortcode:
		'title' => The title you would like to appear over the on-air block 
		'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
		'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
		'limit' => The number of upcoming shows to display.  Default is 1.
		'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.
		
Example:
`[dj-widget title="Coming Up On-Air" show_avatar="1" show_link="1" limit="3" time="12"]`

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

= How do I change the DJ's avatar in the sidebar widget? =

It's the same avatar that's assigned to the email listed in the DJ's user account via Gravatar.

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

== Changelog ==

= 1.0 =
* Initial release

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

= 1.2 =
* Fixed thumbnail bug in sidebar widgets
* Added new widget to display upcoming shows
* Added pagination options for playlists and show blogs

= 1.3 =
* Fixed some minor compatibility issues with WordPress 3.5
* Fixed Shows icon in Dashboard

= 1.3.1 =
* Fixed a major bug in the master schedule output

= 1.3.2 =
* Fixed a bug in the DJ-on-air widget

= 1.3.3 =
* Added the ability to assign any user with the edit_shows capability as a DJ, to accomodate custom and edited roles.

= 1.3.4 =
* By request, added as 24-hour time format option to the master schedule and sidebar widgets.

= 1.3.5 =
* Fixed a time display bug in the DJ On-Air sidebar widget
* Fixed a display bug on the master schedule with overnight shows

= 1.3.6 =
* Fixed issue with shows that run overnight not showing up correctly in the sidebar widgets

== Upgrade Notice ==

= 1.0 =
* Initial release

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

= 1.2 =
* Fixed thumbnail bug in sidebar widgets
* Added new widget to display upcoming shows

= 1.3 =
* Fixed some minor compatibility issues with WordPress 3.5
* Fixed Shows icon in Dashboard

= 1.3.1 =
* Fixed a major bug in the master schedule output

= 1.3.2 =
* Fixed a bug in the DJ-on-air widget
* Fixed show select list for show blog posts

= 1.3.3 =
* Added the ability to assign any user with the edit_shows capability as a DJ, to accomodate custom and edited roles.

= 1.3.4 =
* By request, added as 24-hour time format option to the master schedule and sidebar widgets.

= 1.3.5 =
* Fixed a time display bug in the DJ On-Air sidebar widget
* Fixed a display bug on the master schedule with overnight shows 

= 1.3.6 =
* Fixed issue with shows that run overnight not showing up correctly in the sidebar widgets