/**
 * === Radio Player Block ===
 */
(() => {

	const el = window.wp.element.createElement;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;
	const { __, _e } = window.wp.i18n;

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
					el( ServerSideRender, { block: 'radio-station/player', className: 'radio-block-player', attributes: atts } ),
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
								/* el( PanelRow, {},
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
								), */
							),

							/* === Player Styles === */
							el( PanelBody, { title: __( 'Player Styles', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
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
})();
