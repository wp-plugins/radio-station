/**
 * === Radio Schedule Block ===
 */
(() => {

	const el = window.wp.element.createElement;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;
	const { __, _e } = window.wp.i18n;

	/* --- set schedule view options --- */
	schedule_views = [
		{ label: __( 'Table', 'radio-station' ), value: 'table' },
		{ label: __( 'Tabbed', 'radio-station' ), value: 'tabs' },
		{ label: __( 'List', 'radio-station' ), value: 'list' },
	];
	pro_views = [
		{ label: __( 'Table', 'radio-station' ), value: 'table' },
		{ label: __( 'Tabbed', 'radio-station' ), value: 'tabs' },
		{ label: __( 'Grid', 'radio-station' ), value: 'grid' },
		{ label: __( 'Calendar', 'radio-station' ), value: 'calendar' },
		{ label: __( 'List', 'radio-station' ), value: 'list' },
	];
	default_setting = [ { label: __( 'Plugin Setting', 'radio-station' ), value: '' } ];
	default_views = default_setting.concat(pro_views);

	registerBlockType( 'radio-station/schedule', {

		/* --- Block Settings --- */
		title: '[Radio Station] Program Schedule',
		description: __( 'Radio Station Schedule block.', 'radio-station' ),
		icon: 'calendar-alt',
		category: 'radio-station',
		example: {},
		attributes: {
			
			/* --- Schedule Display --- */
			view: { type: 'string', default: 'table' },
			image_position: { type: 'string', default: 'left' },
			hide_past_shows: { type: 'boolean', default: false },

			/* --- Header Displays --- */
			time_header: { type: 'string', default: 'clock' },
			/* clock: { type: 'boolean', default: true }, */
			/* timezone: { type: 'boolean', default: true }, */
			selector: { type: 'boolean', default: true },

			/* --- Times Display --- */
			display_day: { type: 'string', default: 'short' },
			display_month: { type: 'string', default: 'short' },
			start_day: { type: 'string', default: '' },
			time_format: { type: 'string', default: '' },
			/* days: { type: '', default: false },*/
			/* start_date:  { type: '', default: false }, */
			/* active_date: { type: '', default: false }, */
			/* display_date: { type: 'string', default: 'jS' }, */

			/* --- Show Display --- */
			show_times: { type: 'boolean', default: true },
			show_link: { type: 'boolean', default: true },
			show_image: { type: 'boolean', default: false },
			show_desc: { type: 'boolean', default: false },
			show_hosts: { type: 'boolean', default: false },
			link_hosts: { type: 'boolean', default: false },
			show_genres: { type: 'boolean', default: false },
			show_encore: { type: 'boolean', default: true },
			// show_file: { type: 'boolean', default: false },
			
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
					el( ServerSideRender, { block: 'radio-station/schedule', className: 'radio-block-schedule', attributes: atts } ),
					el( InspectorControls, {},
						el( Panel, {},
							/* === Schedule Display Panel === */
							el( PanelBody, { title: __( 'Schedule Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- View Selection --- */
								( ( !atts.pro ) &&
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Schedule View', 'radio-station' ),
											help: __( 'Grid and Calendar Views available in Pro version.', 'radio-station' ),
											options: schedule_views,
											onChange: ( value ) => {
												props.setAttributes( { view: value } );
											},
											value: atts.view
										})
									)
								),
								( ( !atts.pro ) &&
									el( PanelRow, {},
										el( BaseControl, {
											label: __( 'View Switching', 'radio-station' ),
											help: __( 'Multiple view switching available in Pro version.', 'radio-station' ),
										})
									)
								),
								/* --- [Pro] Multiple View Selection --- */
								/* ( ( atts.pro && atts.multi_view ) && */
								( ( atts.pro ) &&
									el( PanelRow, {},
										el( SelectControl, {
											multiple: true,
											label: __( 'Select Schedule Views', 'radio-station' ),
											help: __( 'Ctrl-Click to select multiple views.', 'radio-station' ),
											options: pro_views,
											onChange: ( value ) => {
												props.setAttributes( { views: value } );
											},
											value: atts.views
										})
									)
								),
								/* --- [Pro] Default View --- */
								( ( atts.pro ) &&
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Default View', 'radio-station' ),
											options: default_views,
											onChange: ( value ) => {
												props.setAttributes( { default_view: value } );
											},
											value: atts.default_view
										})
									)
								),
								/* --- Tab View Options */
								( ( ( !atts.pro && ( atts.view == 'tabs' ) )
								|| ( atts.pro && atts.views.includes('tabs') ) ) &&
									/* --- Image Position --- */
									el( PanelRow, {},
										el( SelectControl, {
											label: __( 'Image Position', 'radio-station' ),
											help: __( 'Affects Tabbed View only.', 'radio-station' ),
											options: [
												{ label: __( 'Left', 'radio-station' ), value: 'left' },
												{ label: __( 'Right', 'radio-station' ), value: 'right' }
											],
											onChange: ( value ) => {
												props.setAttributes( { image_position: value } );
											},
											value: atts.image_position
										})
									)
								),
								( ( ( !atts.pro && ( atts.view == 'tabs' ) )
								|| ( atts.pro && atts.views.includes('tabs') ) ) &&
									/* --- Hide Past Shows */
									el( PanelRow, {},
										el( ToggleControl, {
											label: __( 'Hide Past Shows', 'radio-station' ),
											help: __( 'Affects Tabbed View only.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { hide_past_shows: value } );
											},
											checked: atts.hide_past_shows,
										})
									)
								),
								/* --- [Pro] Grid View Options --- */
								( ( atts.pro && atts.views.includes('grid') ) &&
									/* --- Grid Width --- */
									el( PanelRow, {},
										el( RangeControl, {
											label: __( 'Grid Width', 'radio-station' ),
											help: __( 'Grid view Show column width in pixels.', 'radio-station' ),
											min: 0,
											max: 1000,
											onChange: ( value ) => {
												props.setAttributes( { gridwith: value } );
											},
											value: atts.gridwidth
										})
									)
								),
								( ( atts.pro && atts.views.includes('grid') ) &&
									/* --- Time Spaced Grid --- */
									el( PanelRow, {},
										el( ToggleControl, {
											label: __( 'Time Spaced Grid', 'radio-station' ),
											help: __( 'Line up Shows by times in Grid view.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { time_spaced: value } );
											},
											checked: atts.time_spaced,
										})
									)
								),
								/* --- [Pro] Calendar View Options --- */
								( ( atts.pro && atts.views.includes('calendar') ) &&
									/* --- Calendar Weeks --- */
									el( PanelRow, {},
										el( RangeControl, {
											label: __( 'Calendar Weeks', 'radio-station' ),
											help: __( 'Week rows to display in view.', 'radio-station' ),
											min: 1,
											max: 8,
											onChange: ( value ) => {
												props.setAttributes( { weeks: value } );
											},
											value: atts.weeks
										})
									)
								),
								( ( atts.pro && atts.views.includes('calendar') ) &&
									/* --- Previous Weeks --- */
									el( PanelRow, {},
										el( RangeControl, {
											label: __( 'Previous Weeks', 'radio-station' ),
											help: __( 'Previous Weeks Display', 'radio-station' ),
											min: 0,
											max: 4,
											onChange: ( value ) => {
												props.setAttributes( { previous_weeks: value } );
											},
											value: atts.previous_weeks,
										})
									)
								)
							),

							/* === Header Displays Panel === */
							el( PanelBody, { title: __( 'Header Display Options', 'radio-station' ), initialOpen: false },
								/* --- Clock/Timezone Header --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Radio Time Header', 'radio-station' ),
										options: [
											{ label: __( 'Display Radio Clock', 'radio-station' ), value: 'clock' },
											{ label: __( 'Display Radio Timezone', 'radio-station' ), value: 'timezone' },
											{ label: __( 'No Time Header Display', 'radio-station' ), value: 'none' }
										],
										onChange: ( value ) => {
											props.setAttributes( { time_header: value } );
										},
										value: atts.time_header
									})
								),
								/* --- Genre Highlighter --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Genre Highlighter', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { selector: value } );
										},
										checked: atts.selector,
									})
								),
							),

							/* === Time Display Options === */
							el( PanelBody, { title: __( 'Time Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Day Display --- */
								el( PanelRow, {},
									el( RadioControl, {
										label: __( 'Day Display Format', 'radio-station' ),
										options : [
											{ label: 'Abbreviated', value: 'short' },
											{ label: 'Full Name', value: 'full' }
										],
										onChange: ( value ) => {
											props.setAttributes( { display_day: value } );
										},
										selected: atts.display_day
									})
								),
								/* --- Month Display --- */
								el( PanelRow, {},
									el( RadioControl, {
										label: __( 'Month Display Format', 'radio-station' ),
										options: [
											{ label: __( 'Abbreviated', 'radio-station' ), value: 'short' },
											{ label: __( 'Full Name', 'radio-station' ), value: 'full' }
										],
										onChange: ( value ) => {
											props.setAttributes( { display_month: value } );
										},
										selected: atts.display_month
									})
								),
								/* --- Schedule Start Day --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Schedule Start Day', 'radio-station' ),
										options: [
											{ label: __( 'WP Start of Week', 'radio-station' ), value: '' },
											{ label: __( 'Today', 'radio-station' ), value: 'today' },
											{ label: __( 'Monday', 'radio-station' ), value: 'Monday' },
											{ label: __( 'Tuesday', 'radio-station' ), value: 'Tuesday' },
											{ label: __( 'Wednesday', 'radio-station' ), value: 'Wednesday' },
											{ label: __( 'Thursday', 'radio-station' ), value: 'Thursday' },
											{ label: __( 'Friday', 'radio-station' ), value: 'Friday' },
											{ label: __( 'Saturday', 'radio-station' ), value: 'Saturday' },
											{ label: __( 'Sunday', 'radio-station' ), value: 'Sunday' }							
										],
										onChange: ( value ) => {
											props.setAttributes( { start_day: value } );
										},
										value: atts.start_day
									})
								),
								/* --- Time Format --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Time Display Format', 'radio-station' ),
										options: [
											{ label: __( 'Plugin Setting', 'radio-station' ), value: '' },
											{ label: __( '12 Hour', 'radio-station' ), value: '12' },
											{ label: __( '24 Hour', 'radio-station' ), value: '24' }
										],
										onChange: ( value ) => {
											props.setAttributes( { time_format: value } );
										},
										value: atts.time_format
									})
								)
							),

							/* === Show Display Options === */
							el( PanelBody, { title: __( 'Show Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Show Times --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Time', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_times: value } );
										},
										checked: atts.show_times,
									})
								),
								/* --- Show Link --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Link to Shows', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_link: value } );
										},
										checked: atts.show_link,
									})
								),
								/* --- Show Image --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Image', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_image: value } );
										},
										checked: atts.show_image,
									})
								),
								/* --- Show Description --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Description', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_desc: value } );
										},
										checked: atts.show_desc,
									})
								),
								/* --- Show Hosts --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Hosts', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_hosts: value } );
										},
										checked: atts.show_hosts,
									})
								),
								/* --- Link Hosts --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Link to Hosts', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link_hosts: value } );
										},
										checked: atts.link_hosts,
									})
								),
								/* --- Show Genres --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Show Genres', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_genres: value } );
										},
										checked: atts.show_genres,
									})
								),
								/* --- Show Encore --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Encore', 'radio-station' ),
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
