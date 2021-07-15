# Radio Station Plugin FAQ

***

### How do I get started with Radio Station?

Read the [Quickstart Guide](./index.md#quickstart-guide) for an introduction to the plugin, what features are available and how to set them up.

### Where can I find the full plugin documentation?

The latest documentation can be found online at [NetMix.com](https://netmix.com/radio-station/docs/). Documentation is also included with for the currently installed version via the Radio Station Help menu. You can find the Markdown-formatted files in the `/docs` folder of the [GitHub Repository](https://github.com/netmix/radio-station/docs/) and in the `/docs` folder of the plugin directory. 

### How do I schedule a Show? 

Simply create a new show via Add Show in the Radio Station plugin menu in the Admin area. You will be able to assign Shift timeslots to it on the Show edit page, as well as add the Show description and other meta fields, including Show images.

### How do I display a full schedule of my Station's shows? 

In the Plugin Settings, you can select a Page on which to automatically display the schedule as well as which View to display (a Table grid by default.) Alternatively, you can use the shortcode `[master-schedule]` on any page (or post.) This option allows you to use further shortcode attributes to control the what is displayed in the Schedule (see [Master Schedule Shortcode Docs](./Shortcodes.md#master-schedule-shortcode) )

### I've scheduled all my Shows, but some are not showing up on the program schedule?

Did you remember to check the "Active" checkbox for each Show? If a Show is not marked active, the plugin assumes that it's not currently in production and it is not shown on the Schedule. A Show will also not be shown if it has a Draft status or has no active Shifts assigned to it.

### What if I want to schedule a special event or one-off schedule change?

If you have a one-off event that you need to show up in the Schedule and Widgets, you can create a Schedule Override by clicking Schedule Override in the Radio Station admin menu. This will allow you to set aside a block of time on a specific date, and when the Schedule or Widget is displaying that date, the override will be used instead of the normally scheduled Show. (Note that Schedule Overrides will not display in the old Legacy Table/Div Views of the Master Schedule.)

### I'm seeing 404 Not Found errors when I click on the link for a Show! 

Try re-saving your site's permalink settings via Settings -> Permalinks.  Wordpress sometimes gets confused with a new custom post type is added. Permalink rewrites are automatically flushed on plugin activation, so you can also just deactivate and reactivate the plugin.

### What if I want to change or style the plugin's displays? 

The default styles for Radio Station have intionally kept fairly minimal so as to be compatible with most themes, so you may wish to add your own styles to suit your site's look and feel. The best way to do this is to add your own `rs-custom.css` to your Child Theme's directory, and add more specific style rules that modify or override the existing styles. Radio Station will automatically detect the presence of this file and enqueue it. You can find the base styles in the `/css/` directory of the plugin.

### What Widgets are available with this plugin?

The following Widgets are available to add via the WordPress Appearance -> Widgets page:
Current Show, Upcoming Shows, Current Playlist, Radio Clock and Streaming Player Widgets. See the [Widget Documentation](./Widgets.md) for more details on these Widgets.

### Do the Widgets reload automatically?

Current Show, Upcoming Shows and Current Playlist widgets do not refresh automatically in this free version. This capability has been added as a new feature to the [Pro version](https://radiostation.pro) however - so that the widgets refresh exactly at Show changeover times.

### What Shortcodes are available with this plugin?

See the [Shortcode Documentation](./Shortcodes.md) for more details and a full list of possible Attributes for these Shortcodes:

* `[master-schedule]` - Master Program Schedule Display
* `[current-show]` - Current Show Widget
* `[upcoming-shows]` - Upcoming Shows Widget
* `[current-playlist]` - Current Playlist Widget
* `[shows-archive]` - Archive List of Shows
* `[genres-archive]` - Archive List of Shows sorted by Genre
* `[languages-archive]` - Archive List of Shows sorted by Language
* `[overrides-archive]` - Archive List of Schedule overrides
* `[playlists-archive]` - Archive List of Show Playlists

Note old shortcode aliases will still work in current and future versions to prevent breakage.

### I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types. How do I do that? 

There are a number of different options depending on what you are wanting to to do. To address this situation, we have added a Show Editor role that can edit Shows without being an Administrator. You can find more information on roles in the [Roles Documentation](./Roles.md)

### How do I change the Show Avatar displayed in the sidebar widget? 

The avatar is whatever image is assigned as the Show's Avatar.  All you have to do is set a new Show Avatar on the Edit page for that Show.

### Why don't any users show up in the Hosts or Producers list on the Show edit page? =

You need to assign the Host or Producer role to the users you want, so that you can select these users on the Show edit page. You can assign roles by editing the User via the WordPress admin. The [Pro version](https://radiostation.pro) has an additional Role Editor interface where you can assign the plugin roles to any number of users at once.

### My Show Hosts and Producers can't edit a Show page.  What do I do? 

The only Hosts and Producers that can edit a show are the ones listed as being Hosts or Producers for that Show in the respective user selection menus. This is to prevent Hosts/Producers from editing other Host/Producer's Shows without permission.

### I don't want to use Gravatar for my Host/Producer's image on their profile page. 

Then you'll need to install a plugin that lets you add a different image to your Host/Producer's user account. As there are a number of plugins that do just this, it's a little out of the scope of this plugin. However, in the [Pro version](https://radiostation.pro) you can create separate profile pages to showcase each of your Hosts and Producers, and assign profile images to these profile pages.

### What languages other than English is the plugin available in? 

Right now:

* Albanian (sq_AL)
* Dutch (nl_NL)
* French (fr_FR)
* German (de_DE)
* Italian (it_IT)
* Russian (ru_RU)
* Serbian (sr_RS)
* Spanish (es_ES)
* Catalan (ca)

### Can the plugin be translated into my language? 

You may translate the plugin into another language. Please visit our [WordPress Translate project page](https://translate.wordpress.org/projects/wp-plugins/radio-station/) for this plugin for further instruction. The `radio-station.pot` file is located in the `/languages` directory of the plugin. Please send the finished translation to `info@netmix.com`. We'd love to include it.

### Can I use this plugin for Podcasts?

While the plugin is not specifically geared toward Podcasting, which is not live programming, some podcaster's have used Radio Station to let their subscribers know when they publish new shows.

### Can I use this plugin for TwitchTV, Facebook Live, or Clubhouse shows?

Sure, there's no reason why you couldn't use the plugin to display a show schedule on a WordPress site for those services. Unfortunately, we are not currently syncing events from these platforms, but may do so in the future. While there may be APIs available from the larger services, Clubhouse does not yet have a public API, so scheduled rooms can't be automated to the Radio Station show scheduling system.

### I use Google Calendar to print a show schedule online. Can I import/sync my Google Calendar with Radio Station?

We haven't built an interface between Google Calendar and Radio Station just yet, but it's on our radar to do so in the foreseeable future.

### How do I install the latest Development version for testing?

If you are having issues with the plugin, we may recommend you install the development version for further bugix testing, as it may contain fixes that are not yet released into the next stable WordPress version. It is recommended you do this on a staging site. Instructions:

1. Download the `develop` branch zip from the Github repository at: 
`https://github.com/netmix/radio-station/tree/develop/`
2. Unzip the downloaded file on your computer and upload it via FTP to the subdirectory of your WordPress install on your web server: `/wp-content/plugins/radio-station-dev/`
3. Rename the subdirectory `/wp-content/plugins/radio-station/` to `/wp-content/plugins/radio-station-old`
4. Rename the subdirectory `/wp-content/plugins/radio-station-dev/` to `/wp-content/plugins/radio-station/`

You can now visit your site to make sure nothing is broken. If you experience issues you can reverse the folder renaming process to activate the old copy of the plugin. If the new development version works fine, at your convenience you can delete the `/wp-content/plugins/radio-station-old/` directory.
