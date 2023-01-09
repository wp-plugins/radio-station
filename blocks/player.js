/**
 * === Radio Player Block ===
 */
(() => {

	/* --- Import Modules/Components --- */
	const el = window.wp.element.createElement;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, ColorPicker, Dropdown, Button, Panel, PanelBody, PanelRow } = window.wp.components;
	const { __, _e } = window.wp.i18n;
	
	/* --- Register Block --- */
	if ( !getBlockType('radio-station/player' ) ) {
	 registerBlockType( 'radio-station/player', {

		/* --- Block Settings --- */
		title: __( '[Radio Station] Stream Player', 'radio-station' ),
		description: __( 'Audio stream player block.', 'radio-station' ),
		icon: 'controls-volumeon',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Player Content --- */
			url: { type: 'string', default: '' },
			title: { type: 'string', default: '' },
			image: { type: 'string', default: 'default' },
			/* --- Player Options --- */
			script: { type: 'string', default: 'default' },
			volume: { type: 'number', default: 77 },
			volumes: { type: 'array', default: ['slider'] },
			default: { type: 'boolean', default: false },
			/* --- Player Styles --- */
			layout: { type: 'string', default: 'horizontal' },
			theme: { type: 'string', default: 'default' },
			buttons: { type: 'string', default: 'default' },
			/* --- Hidden Switches --- */
			block: { type: 'boolean', default: true },
			pro: { type: 'boolean', default: false }
		},

		/**
		 * Edit Block Control
		 */
		edit: (props) => {
			const atts = props.attributes;
			return (
				el( Fragment, {},
					el( ServerSideRender, { block: 'radio-station/player', className: 'radio-player-block', attributes: atts } ),
					el( InspectorControls, {},
						el( Panel, {},
							/* === Player Content === */
							el( PanelBody, { title: __( 'Player Content', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Stream URL --- */
								el( PanelRow, {},
									el( TextControl, {
										label: __( 'Stream URL', 'radio-station' ),
										help: __( 'Leave blank to use default stream.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { url: value } );
										},
										value: atts.url,
									})
								),
								/* --- Player Title Text --- */
								el( PanelRow, {},
									el( TextControl, {
										label: __( 'Player Title Text', 'radio-station' ),
										help: __( 'Empty for default, 0 for none.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { title: value } );
										},
										value: atts.title
									})
								),
								/* --- Image --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Player Image', 'radio-station' ),
										options : [
											{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
											{ label: __( 'Display Station Image', 'radio-station' ), value: '1' },
											{ label: __( 'Do Not Display Station Image', 'radio-station' ), value: '0' },
											/* { label: __( 'Display Custom Image', 'radio-station' ), value: 'custom' }, */
										],
										onChange: ( value ) => {
											props.setAttributes( { image: value } );
										},
										value: atts.image
									})
								)
							),

							/* === Player Options === */
							el( PanelBody, { title: __( 'Player Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Script --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Player Script', 'radio-station' ),
										options : [
											{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
											{ label: __( 'Amplitude', 'radio-station' ), value: 'amplitude' },
											{ label: __( 'Howler', 'radio-station' ), value: 'howler' },
											{ label: __( 'jPlayer', 'radio-station' ), value: 'jplayer' },
										],
										onChange: ( value ) => {
											props.setAttributes( { script: value } );
										},
										value: atts.script
									})
								),
								/* --- Volume --- */
								el( PanelRow, {},
									el( RangeControl, {
										label: __( 'Initial Volume', 'radio-station' ),
										min: 0,
										max: 100,
										onChange: ( value ) => {
											props.setAttributes( { volume: value } );
										},
										value: atts.volume
									})
								),
								/* --- Volume controls --- */
								el( PanelRow, {},
									el( SelectControl, {
										multiple: true,
										label: __( 'Volume Controls', 'radio-station' ),
										help: __( 'Ctrl-Click to select multiple controls.', 'radio-station' ),
										options: [
											{ label: __( 'Volume Slider', 'radio-station' ), value: 'slider' },
											{ label: __( 'Up and Down Buttons', 'radio-station' ), value: 'updown' },
											{ label: __( 'Mute Button', 'radio-station' ), value: 'mute' },
											{ label: __( 'Maximize Button', 'radio-station' ), value: 'max' },
										],
										onChange: ( value ) => {
											props.setAttributes( { volumes: value } );
										},
										value: atts.volumes
									})
								),
								/* --- Default Player --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Use as Default Player', 'radio-station' ),
										help: __( 'Make this the default player on this page.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { default: value } );
										},
										checked: atts.default,
									})
								),
								/* --- Popup Player Button --- */
								el( PanelRow, {},
									( ( atts.pro ) && 
										el( SelectControl, {
											label: __( 'Popup Player', 'radio-station' ),
											help: __( 'Enables button to open Player in separate window.', 'radio-station' ),
											options : [
												{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: __( 'On', 'radio-station' ), value: 'on' },
												{ label: __( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { popup: value } );
											},
											value: atts.popup
										})
									), ( ( !atts.pro ) &&
										el( BaseControl, {
											label: __( 'Popup Player', 'radio-station' ),
											help: __( 'Popup Player Button available in Pro.', 'radio-station' ),
										})
									)
								),
							),

							/* === Player Styles === */
							el( PanelBody, { title: __( 'Player Design', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Player Layout --- */
								el( PanelRow, {},
									el( RadioControl, {
										label: __( 'Player Layout', 'radio-station' ),
										options : [
											{ label: __( 'Vertical (Stacked)', 'radio-station' ), value: 'vertical' },
											{ label: __( 'Horizontal (Inline)', 'radio-station' ), value: 'horizontal' },
										],
										onChange: ( value ) => {
											props.setAttributes( { layout: value } );
										},
										checked: atts.layout
									})
								),
								/* --- Player Theme --- */
								( ( !atts.pro ) &&
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Player Theme', 'radio-station' ),
											options : [
												{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: __( 'Light', 'radio-station' ), value: 'light' },
												{ label: __( 'Dark', 'radio-station' ), value: 'dark' },
											],
											onChange: ( value ) => {
												props.setAttributes( { theme: value } );
											},
											value: atts.theme
										})
									)
								),
								/* [Pro] Extra Theme Color Options */
								( ( atts.pro ) &&
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Player Theme', 'radio-station' ),
											options : [
												{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: __( 'Light', 'radio-station' ), value: 'light' },
												{ label: __( 'Dark', 'radio-station' ), value: 'dark' },
												{ label: __( 'Red', 'radio-station' ), value: 'red' },
												{ label: __( 'Orange', 'radio-station' ), value: 'orange' },
												{ label: __( 'Yellow', 'radio-station' ), value: 'yellow' },
												{ label: __( 'Light Green', 'radio-station' ), value: 'light-green' },
												{ label: __( 'Green', 'radio-station' ), value: 'green' },
												{ label: __( 'Cyan', 'radio-station' ), value: 'cyan' },
												{ label: __( 'Light Blue', 'radio-station' ), value: 'light-blue' },
												{ label: __( 'Blue', 'radio-station' ), value: 'blue' },
												{ label: __( 'Purple', 'radio-station' ), value: 'purple' },
												{ label: __( 'Magenta', 'radio-station' ), value: 'magenta' },
											],
											onChange: ( value ) => {
												props.setAttributes( { theme: value } );
											},
											value: atts.theme
										})
									)
								),
								/* --- Player Buttons --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Player Buttons', 'radio-station' ),
										options : [
											{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
											{ label: __( 'Circular', 'radio-station' ), value: 'circular' },
											{ label: __( 'Rounded', 'radio-station' ), value: 'rounded' },
											{ label: __( 'Square', 'radio-station' ), value: 'square' },
										],
										onChange: ( value ) => {
											props.setAttributes( { buttons: value } );
										},
										value: atts.buttons
									})
								)					
							),
							
							/* === [Pro] Player Colors === */
							( ( atts.pro ) &&
								el( PanelBody, { title: __( 'Player Colors', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								
									/* --- Text Color --- */
									el( PanelRow, {},
										el( BaseControl, {
											label: __( 'Text Color', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											el( Dropdown, {
												renderContent: () => (
													el( ColorPicker, {
														disableAlpha: true,
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {text_color: color.hex} );
														},
														color: atts.text_color
													})
												),
												renderToggle: (args) => (
													el( 'div', {className: 'color-dropdown-buttons'},
														el ( Button, {
															className: 'color-dropdown-text_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': __( 'Select Text Color', 'radio-station' )
														},
														( ('' != atts.text_color) ? atts.text_color : __( 'Select', 'radio-station' ) )
														),
														el( Button, {
															onClick: () => {
																props.setAttributes( {text_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': __( 'Clear Text Color Selection', 'radio-station' )
														},
														__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.text_color ) &&
															el( 'style', {}, '.components-button.is-secondary.color-dropdown-text_color {background-color:'+atts.text_color+'}' )
														)
													)
												)
											} ) 
										)
									),

									/* --- Background Color --- */
									el( PanelRow, {},
										el( BaseControl, {
											label: __( 'Background Color', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											el( Dropdown, {
												renderContent: () => (
													el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {background_color: color.hex} );
														},
														color: atts.background_color
													})
												),
												renderToggle: (args) => (
													el( 'div', {className: 'color-dropdown-buttons'},
														el ( Button, {
															className: 'color-dropdown-background_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': __( 'Select Background Color', 'radio-station' )
														},
														( ('' != atts.background_color) ? atts.background_color : __( 'Select', 'radio-station' ) )
														),
														el( Button, {
															onClick: () => {
																props.setAttributes( {background_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': __( 'Clear Background Color Selection', 'radio-station' )
														},
														__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.background_color ) &&
															el( 'style', {}, '.components-button.is-secondary.color-dropdown-background_color {background-color:'+atts.background_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Playing Color --- */
									el( PanelRow, {},
										el( BaseControl, {
											label: __( 'Playing Highlight', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											el( Dropdown, {
												renderContent: () => (
													el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {playing_color: color.hex} );
														},
														color: atts.playing_color
													})
												),
												renderToggle: (args) => (
													el( 'div', {className: 'color-dropdown-buttons'},
														el ( Button, {
															className: 'color-dropdown-playing_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': __( 'Select Playing Highlight Color', 'radio-station' )
														},
														( ('' != atts.playing_color) ? atts.playing_color : __( 'Select', 'radio-station' ) )
														),
														el( Button, {
															onClick: () => {
																props.setAttributes( {playing_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': __( 'Clear Playing Color Selection', 'radio-station' )
														},
														__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.playing_color ) &&
															el( 'style', {}, '.components-button.is-secondary.color-dropdown-playing_color {background-color:'+atts.playing_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Buttons Color --- */
									el( PanelRow, {},
										el( BaseControl, {
											label: __( 'Buttons Highlight', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											el( Dropdown, {
												renderContent: () => (
													el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {buttons_color: color.hex} );
														},
														color: atts.buttons_color
													})
												),
												renderToggle: (args) => (
													el( 'div', {className: 'color-dropdown-buttons'},
														el ( Button, {
															className: 'color-dropdown-buttons_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': __( 'Select Button Highlight Color', 'radio-station' )
														},
														( ('' != atts.buttons_color) ? atts.buttons_color : __( 'Select', 'radio-station' ) )
														),
														el( Button, {
															onClick: () => {
																props.setAttributes( {buttons_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': __( 'Clear Button Highlight Color Selection', 'radio-station' )
														},
														__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.buttons_color ) &&
															el( 'style', {}, '.components-button.is-secondary.color-dropdown-buttons_color {background-color:'+atts.buttons_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Track Color --- */
									el( PanelRow, {},
										el( BaseControl, {
											label: __( 'Volume Track', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											el( Dropdown, {
												renderContent: () => (
													el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {track_color: color.hex} );
														},
														color: atts.track_color
													})
												),
												renderToggle: (args) => (
													el( 'div', {className: 'color-dropdown-buttons'},
														el ( Button, {
															className: 'color-dropdown-track_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': __( 'Select Volume Track Color', 'radio-station' )
														},
														( ('' != atts.track_color) ? atts.track_color : __( 'Select', 'radio-station' ) )
														),
														el( Button, {
															onClick: () => {
																props.setAttributes( {track_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': __( 'Clear Volume Track Color Selection', 'radio-station' )
														},
														__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.track_color ) &&
															el( 'style', {}, '.components-button.is-secondary.color-dropdown-track_color {background-color:'+atts.track_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Thumb Color --- */
									el( PanelRow, {},
										el( BaseControl, {
											label: __( 'Volume Thumb', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											el( Dropdown, {
												renderContent: () => (
													el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {thumb_color: color.hex} );
														},
														color: atts.thumb_color
													})
												),
												renderToggle: (args) => (
													el( 'div', {className: 'color-dropdown-buttons'},
														el ( Button, {
															className: 'color-dropdown-thumb_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': __( 'Select Volume Thumb Color', 'radio-station' )
														},
														( ('' != atts.thumb_color) ? atts.thumb_color : __( 'Select', 'radio-station' ) )
														),
														el( Button, {
															onClick: () => {
																props.setAttributes( {thumb_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': __( 'Clear Volume Thumb Color Selection', 'radio-station' )
														},
														__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.thumb_color ) &&
															el( 'style', {}, '.components-button.is-secondary.color-dropdown-thumb_color {background-color:'+atts.thumb_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									/* --- end color options --- */
								)
							),

							/* === Advanced Options === */
							( ( atts.pro ) &&
								el( PanelBody, { title: __( 'Advanced Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
									/* --- Current Show Display --- */
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Current Show Display', 'radio-station' ),
											options : [
												{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: __( 'On', 'radio-station' ), value: 'on' },
												{ label: __( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { currentshow: value } );
											},
											value: atts.currentshow
										})
									),
									/* ---Now Playing Display --- */
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Now Playing Track Display', 'radio-station' ),
											options : [
												{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: __( 'On', 'radio-station' ), value: 'on' },
												{ label: __( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { nowplaying: value } );
											},
											value: atts.nowplaying
										})
									),
									/* --- Track Animation --- */
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Track Animation', 'radio-station' ),
											options : [
												{ label: __( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: __( 'No Animation', 'radio-station' ), value: 'none' },
												{ label: __( 'Left to Right Ticker', 'radio-station' ), value: 'lefttoright' },
												{ label: __( 'Right to Left Ticker', 'radio-station' ), value: 'righttoleft' },
												{ label: __( 'Back and Forth', 'radio-station' ), value: 'backandforth' },
												{ label: __( '', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { animation: value } );
											},
											value: atts.animation
										})
									),
									/* --- Metadata URL --- */
									el( PanelRow, {},
										el( TextControl, {
											label: __( 'Metadata Source URL', 'radio-station' ),
											help: __( 'Defaults to Stream URL.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { metadata: value } );
											},
											value: atts.metadata
										})
									),
								)
							)
							/* end panels */
						)
					)
				)
			);
		},

		/**
		 * Returns nothing because this is a dynamic block rendered via PHP
		 */
		save: () => null,
	 });
	}
})();
