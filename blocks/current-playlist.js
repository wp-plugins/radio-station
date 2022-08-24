/**
 * === Radio Current Playlist Block ===
 */
(() => {

	const el = window.wp.element.createElement;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;
	const { __, _e } = window.wp.i18n;

	registerBlockType( 'radio-station/current-playlist', {

		/* --- Block Settings --- */
		title: '[Radio Station] Current Playlist',
		description: __( 'Radio Station current playlist block.', 'radio-station' ),
		icon: 'playlist-audio',
		category: 'widgets',
		example: {},
		attributes: {
			/* --- Loading Options --- */
			ajax: { type: 'string', default: '' },
			/* dynamic: { type: 'string', default: '' }, */
			hide_empty: { type: 'boolean', default: false },

			/* --- Playlist Display Options --- */
			link: { type: 'boolean', default: true },
			countdown: { type: 'boolean', default: true },
			no_playlist: { type: 'string', default: '' },

			/* --- Track Display Options --- */
			song: { type: 'boolean', default: true },
			artist: { type: 'boolean', default: true },
			album: { type: 'boolean', default: false },
			label: { type: 'boolean', default: false },
			comments: { type: 'boolean', default: false },
			
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
					el( ServerSideRender, { block: 'radio-station/current-playlist', className: 'radio-block-schedule', attributes: atts } ),
					el( InspectorControls, {},
						el( Panel, {},
							
							// === Loading Options === */
							el( PanelBody, { title: __( 'Show Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'AJAX Load Block', 'radio-station' ),
										help: __( 'To bypass page caching.', 'radio-station' ),
										options : [
											{ label: __( 'Plugin Setting', 'radio-station' ), value: '' },
											{ label: __( 'On', 'radio-station' ), value: 'on' },
											{ label: __( 'Off', 'radio-station' ), value: 'off' },
										],
										onChange: ( value ) => {
											props.setAttributes( { ajax: value } );
										},
										value: atts.ajax
									})
								),
								/* --- [Pro] Dynamic Reloading --- */
								el( PanelRow, {},
									( ( atts.pro ) && 
										el( SelectControl, {
											label: __( 'Dynamic Reloading', 'radio-station' ),
											help: __( 'Reloads at show changeover times.', 'radio-station' ),
											options : [
												{ label: __( 'Plugin Setting', 'radio-station' ), value: '' },
												{ label: __( 'On', 'radio-station' ), value: 'on' },
												{ label: __( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { dynamic: value } );
											},
											value: atts.dynamic
										})
									), ( ( !atts.pro ) &&
										el( BaseControl, {
											label: __( 'Dynamic Reloading', 'radio-station' ),
											help: __( 'Show changeover reloading available in Pro.', 'radio-station' ),
										})
									)
								),
								/* --- Hide if Empty --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Hide if Empty?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { hide_empty: value } );
										},
										checked: atts.hide_empty,
									})
								),
							),

							/* === Playlist Display Panel === */
							el( PanelBody, { title: __( 'Extra Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Link Playlist --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Link to Playlist Page', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link: value } );
										},
										checked: atts.link,
									})
								),
								/* --- No Playlist Text --- */
								el( PanelRow, {},
									el( TextControl, {
										label: __( 'No Current Playlist Text', 'radio-station' ),
										help: __( 'Blank for default. 0 for none.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { no_playlist: value } );
										},
										value: atts.no_playlist
									})
								),
								/* --- Countdown --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Playlist Countdown', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { countdown: value } );
										},
										checked: atts.countdown,
									})
								),
							),

							/* === Track Display Options === */
							el( PanelBody, { title: __( 'Track Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Song Display --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Song Title', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { song: value } );
										},
										checked: atts.song,
									})
								),
								/* --- Artist Display --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Artist', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { artist: value } );
										},
										checked: atts.artist,
									})
								),
								/* --- Display Album --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Album', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { album: value } );
										},
										checked: atts.album,
									})
								),
								/* --- Display Record Label --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Record Label', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { label: value } );
										},
										checked: atts.label,
									})
								),
								/* --- Display Comments --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Track Comments', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { comments: value } );
										},
										checked: atts.comments,
									})
								),
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
