/**
 * === Radio Upcoming Shows Block ===
 */
(() => {

	const el = window.wp.element.createElement;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;
	const { __, _e } = window.wp.i18n;

	/* --- create image size options --- */
	image_size_options = [];
	image_sizes = wp.data.select( 'core/block-editor' ).getSettings().imageSizes;
	for ( i = 0; i < image_sizes.length; i++ ) {
		image_size_options[i] = { label: image_sizes[i].name, value: image_sizes[i].slug };
	}

	registerBlockType( 'radio-station/upcoming-shows', {

		/* --- Block Settings --- */
		title: '[Radio Station] Upcoming Shows',
		description: __( 'Radio Station upcoming shows block.', 'radio-station' ),
		icon: 'controls-forward',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Loading Options --- */
			limit: { type: 'number', default: 1 },
			ajax: { type: 'string', default: '' },
			/* dynamic: { type: 'string', default: '' }, */
			no_shows: { type: 'string', default: '' },
			hide_empty: { type: 'boolean', default: false },

			/* --- Show Display Options --- */
			show_link: { type: 'boolean', default: true },
			title_position: { type: 'string', default: 'right' },
			show_avatar: { type: 'boolean', default: true },
			avatar_size: { type: 'string', default: 'thumbnail' },
			avatar_width: { type: 'number', default: 0 },

			/* --- Show Time Display Options --- */
			show_sched: { type: 'boolean', default: true },
			countdown: { type: 'boolean', default: true },
			time_format: { type: 'string', default: '' },

			/* --- Extra Display Options --- */
			display_hosts: { type: 'boolean', default: false },
			link_hosts: { type: 'boolean', default: true },
			/* display_producers: { type: 'boolean', default: false }, */
			/* link_producers: { type: 'boolean', default: false }, */
			show_encore: { type: 'boolean', default: true },
			
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
					el( ServerSideRender, { block: 'radio-station/upcoming-shows', className: 'radio-block-schedule', attributes: atts } ),
					el( InspectorControls, {},
						el( Panel, {},
							
							// === Loading Options === */
							el( PanelBody, { title: __( 'Show Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Shows to Display --- */
								el( PanelRow, {},
									el( RangeControl, {
										label: __( 'Upcoming Shows to Display', 'radio-station' ),
										min: 1,
										max: 10,
										onChange: ( value ) => {
											props.setAttributes( { limit: value } );
										},
										value: atts.limit
									})
								),
								/* --- AJAX Load --- */
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
								/* --- No Shows Text --- */
								el( PanelRow, {},
									el( TextControl, {
										label: __( 'No Upcoming Shows Text', 'radio-station' ),
										help: __( 'Blank for default. 0 for none.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { no_shows: value } );
										},
										value: atts.no_shows
									})
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

							/* === Show Display Options === */
							el( PanelBody, { title: __( 'Show Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Show Link --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Link to Show Page', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_title: value } );
										},
										checked: atts.show_link,
									})
								),
								/* --- Title Position --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Show Title Position', 'radio-station' ),
										options : [
											{ label: __( 'Above Image', 'radio-station' ), value: 'above' },
											{ label: __( 'Left of Image', 'radio-station' ), value: 'left' },
											{ label: __( 'Right of Image', 'radio-station' ), value: 'right' },
											{ label: __( 'Below Image', 'radio-station' ), value: 'below' },
										],
										onChange: ( value ) => {
											props.setAttributes( { title_position: value } );
										},
										value: atts.title_position
									})
								),
								/* --- Show Avatar --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Image', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_avatar: value } );
										},
										checked: atts.show_avatar,
									})
								),
								/* --- Avatar Size --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Image Size', 'radio-station' ),
										options: image_size_options,
										onChange: ( value ) => {
											props.setAttributes( { avatar_size: value } );
										},
										selected: atts.avatar_size
									})
								),
								/* --- Avatar Width --- */
								el( PanelRow, {},
									el( RangeControl, {
										label: __( 'Image Width Override', 'radio-station' ),
										help: __( '0 for default.', 'radio-station' ),
										min: 0,
										max: 1000,
										onChange: ( value ) => {
											props.setAttributes( { avatar_width: value } );
										},
										value: atts.avatar_width
									})
								),
							),

							/* === Show Time Display Options === */
							el( PanelBody, { title: __( 'Show Time Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Show Time --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Time', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_sched: value } );
										},
										checked: atts.show_sched,
									})
								),
								/* --- Countdown --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Remaining Time Countdown', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { countdown: value } );
										},
										checked: atts.countdown,
									})
								),
								/* --- Time Format --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Time Display Format', 'radio-station' ),
										options: [
											{ label: __( 'Plugin Setting', 'radio-station' ), value: '' },
											{ label: __( '12 Hour', 'radio-station' ), value: '12' },
											{ label: __( '24 Hour', 'radio-station' ), value: '24' },
										],
										onChange: ( value ) => {
											props.setAttributes( { time_format: value } );
										},
										value: atts.time_format
									})
								),
							),				
							
							/* === Extra Displays Panel === */
							el( PanelBody, { title: __( 'Extra Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Display Hosts --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Hosts', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { display_hosts: value } );
										},
										checked: atts.display_hosts,
									})
								),
								/* --- Link Hosts --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Link to Host Profile', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link_hosts: value } );
										},
										checked: atts.link_hosts,
									})
								),
								/* --- Display Producers --- */
								/* el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Producers', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { display_hosts: value } );
										},
										checked: atts.display_hosts,
									})
								), */
								/* --- Link Producers --- */
								/* el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Link to Producer Profile', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link_producers: value } );
										},
										checked: atts.link_producers,
									})
								), */
								/* --- Show Encore --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display if Encore Airing', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_encore: value } );
										},
										checked: atts.show_encore,
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
