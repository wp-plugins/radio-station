# Radio Station Stream Player

***


## Radio Player

The Radio Player is available as a Shortcode, Widget or Block. In Pro it is also available as a Sitewide Bar Player, and as an Elementor Widget and Beaver Builder Module.

### Default Player Settings

Default settings for the Player can be set on the Plugin Settings page on the Player tab. These will be used in widgets wherever the widget options are set to "Default". This saves you from setting them twice, but also means that you can override these defaults in individual widgets as needed. (see [Options](./Options.md#player) for a list of these options.)

### Radio Player Widget

A Player widget instance can be added via the WordPress Admin Appearance -> Widgets page.

The widget options correspond to the shortcode attributes below, allowing you control over the widget output.

### Radio Player Block

A Player block can be added via the Block Editor by clicking the blue + icon. The `[Radio Station] Stream Player` Block can be found in the Media category.

#### [Pro] Sitewide Bar Player

[Radio Station Pro](https://radiostation.pro) includes a Sitewide Bar Streaming Player. It isn't added via the Widgets Page, but is instead configured via the Plugin Settings page under the Player tab. It has the following options:

* Display fixed in the header or footer area (top or bottom of page, unaffected by scrolling.)
* ...
* Popup Player Button : adds a button to open the player in a separate window.




// --- [Pro/Player] Playing Highlight Color ---
	'player_playing_color' => array(
		'type'    => 'color',
		'label'   => __( 'Playing Icon Highlight Color', 'radio-station' ),
		'default' => '#70E070',
		'helper'  => __( 'Default highlight color to use for Play button icon when playing.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// --- [Pro/Player] Control Icons Highlight Color ---
	'player_buttons_color' => array(
		'type'    => 'color',
		'label'   => __( 'Control Icons Highlight Color', 'radio-station' ),
		'default' => '#00A0E0',
		'helper'  => __( 'Default highlight color to use for Control button icons when active.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// --- [Pro/Player] Volume Knob Color ---
	'player_thumb_color' => array(
		'type'    => 'color',
		'label'   => __( 'Volume Knob Color', 'radio-station' ),
		'default' => '#80C080',
		'helper'  => __( 'Default Knob Color for Player Volume Slider.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// --- [Pro/Player] Volume Track Color ---
	'player_range_color' => array(
		'type'    => 'coloralpha',
		'label'   => __( 'Volume Track Color', 'radio-station' ),
		'default' => '#80C080',
		'helper'  => __( 'Default Track Color for Player Volume Slider.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// === Advanced Stream Player ===

	// --- [Player] Player Volume ---
	'player_volume' => array(
		'type'    => 'number',
		'label'   => __( 'Player Start Volume', 'radio-station' ),
		'default' => 77,
		'min'     => 0,
		'step'    => 1,
		'max'     => 100,
		'helper'  => __( 'Initial volume for when the Player starts playback.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'advanced',
		'pro'     => false,
	),

	// --- [Player] Single Player ---
	'player_single' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Single Player at Once', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Stop any existing Players on the page or in other windows or tabs when a Player is started.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'advanced',
		'pro'     => false,
	),

	// --- [Pro/Player] Player Autoresume ---
	'player_autoresume' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Autoresume Playback', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Attempt to resume playback if visitor was playing. Only triggered when the user first interacts with the page.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'advanced',
		'pro'     => true,
	),


	Bar Player Text Color
	Text color for the fixed position Sitewide Bar Player.

	Bar Player Background Color
	Background color for the fixed position Sitewide Bar Player.

	Current Show Display
	Display the Current Show in the Player Bar.

	Now Playing Metadata
	Display the currently playing song in the Player Bar, if a supported metadata format is available. (Icy Meta, Icecast, Shoutcast 1/2, Current Playlist

	Metadata URL
	Now playing metadata is normally retrieved via the Stream URL. Use this setting if you need to provide an alternative metadata location.




### Radio Player Shortcode

`[radio-player]`

The following attributes are available for this shortcode:

* *url* : Stream or file URL. Default: plugin setting.
* *script* : Default audio script. 'amplitude', 'jplayer', or 'howler'. Default: amplitude
* *layout* : Player section layout. 'horizontal', 'vertical'. Default: 'vertical'
* *theme* : Player Buttom theme. 'light' or 'dark'. Default: 'light'.
* *buttons* : Control buttons shape. 'circular', 'rounded' or 'square'. Default 'rounded'.
* *title* : Player/Station Title. String. 0 for none
* *image* : Player/Station Image. URL (recommended size 256x256). Default none.
* *volume* : Initial Player Volume. 0-100. Default: 77

[Demo Site Example Output](https://demo.radiostation.pro/player-shortcode/)

#### Using the Shortcode in Templates

Remember, if you want to display a Shortcode within a custom Template (or elsewhere in custom code), you can use the WordPress `do_shortcode` function. eg. `do_shortcode('[radio-player]');`

#### [Pro] Extra Shortcode Options

* **Color Options**
* `text_color` : . Default none (inherit.)
* `background_color` : . Default none (inherit.)
* `playing_color` : . Default Plugin Setting.
* `buttons_color` : . Default Plugin Setting.
* `track_color` : . Default Plugin Setting.
* `thumb_color` : . Default Plugin Setting.
* `popup` : Add button to open Popup Player in separate window. 0 or 1. Default 0.



