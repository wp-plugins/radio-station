# Radio Station Stream Player

***


## Radio Player

### Default Player Settings

Default settings for the Player can be set on the Plugin Settings page on the Player tab. These will be used in widgets wherever the widget options are set to "default". This saves you from setting them twice, but also means that you can override these defaults in individual widgets as needed. (see [Options](./Options.md#player) for a list of these options.)


### Radio Player Widget

A Player widget instance can be added via the WordPress admin Appearance -> Widgets page.

The widget options correspond to the shortcode attributes below, allowing you control over the widget output.

#### [Pro] Sitewide Bar Player

[Radio Station Pro](https://radiostation.pro) includes a Sitewide Bar Streaming Player. It isn't added via the Widgets Page, but is instead configured via the Plugin Settings page under the Player tab. It has the following options:

* Display fixed in the header or footer area (top or bottom of page, unaffected by scrolling.)
* ...



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

[Demo Site Example Output](https://radiostationdemo.com/player-shortcode/)


#### Shortcode in Templates

Remember, if you want to display a Shortcode within a custom Template (or elsewhere in custom code), you can use the WordPress `do_shortcode` function. eg. `do_shortcode('[radio-player]');`

