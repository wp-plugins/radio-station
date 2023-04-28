/**
 * === Radio Archive Block ===
 */
(() => {

	const el = window.wp.element.createElement;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;
	const { __, _e } = window.wp.i18n;

	archive_options = [
		{ label: __( 'Shows', 'radio-station' ), value: 'shows' },
		{ label: __( 'Overrides', 'radio-station' ), value: 'overrides' },
		{ label: __( 'Playlists', 'radio-station' ), value: 'playlists' },
		{ label: __( 'Shows by Genre', 'radio-station' ), value: 'genres' },
		{ label: __( 'Shows by Language', 'radio-station' ), value: 'languages' },
	];
	pro_archive_options = archive_options;
	pro_archive_options[5] = { label: __( 'Episodes', 'radio-station' ), value: 'episodes' };
	pro_archive_options[6] = { label: __( 'Hosts', 'radio-station' ), value: 'hosts' };
	pro_archive_options[7] = { label: __( 'Producers', 'radio-station' ), value: 'producers' };
	/* pro_archive_options[8] = { label: __( 'Team', 'radio-station' ), value: 'team' }; */
			
	registerBlockType( 'radio-station/archive', {

		/* --- Block Settings --- */
		title: '[Radio Station] Archive List',
		description: __( 'Archive list for Radio Station record types.', 'radio-station' ),
		icon: 'media-audio',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Archive List Details --- */
			archive_type: { type: 'string', default: 'shows' },
			view: { type: 'string', default: 'list' },
			perpage: { type: 'number', default: 10 },
			pagination: { type: 'boolean', default: true },
			hide_empty: { type: 'boolean', default: false },

			/* --- Archive Record Query --- */
			orderby: { type: 'string', default: 'title' },
			order: { type: 'string', default: 'ASC' },
			status: { type: 'string', default: 'publish' },
			genre: { type: 'string', default: '' },
			language: { type: 'string', default: '' },

			/* === Archive Record Display === */
			description: { type: 'string', default: 'excerpt' },
			time_format: { type: 'string', default: '' },
			show_avatars: { type: 'boolean', default: true }, /* shows and overrides only */
			with_shifts: { type: 'boolean', default: true }, /* shows only */
			show_dates: { type: 'boolean', default: true },	/* overrides only */
			
			/* --- Hidden Switches --- */
			block: { type: 'boolean', default: true },
			pro: { type: 'boolean', default: false }
		},

		/**
		 * Edit Block Controls
		 */
		edit: (props) => {
			const atts = props.attributes;
			if ( atts.pro ) {
				archive_type_options = pro_archive_options;
				archive_type_help = __( 'Which type of records to display.', 'radio-station' );
			} else {
				archive_type_options = archive_options;
				archive_type_help = __( 'Episodes, Hosts and Producer archives available in Pro version.', 'radio-station' );
			}

			return (
				el( Fragment, {},
					el( ServerSideRender, { block: 'radio-station/archive', className: 'radio-archive-block', attributes: atts } ),
					el( InspectorControls, {},
						el( Panel, {},
							/* === Archive List Details === */
							el( PanelBody, { title: __( 'Archive List Details', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Archive Type --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Archive Type', 'radio-station' ),
										help: archive_type_help,
										options: archive_type_options,
										onChange: ( value ) => {
											props.setAttributes( { archive_type: value } );
										},
										value: atts.archive_type,
									})
								),
								/* --- Archive View --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Archive View', 'radio-station' ),
										options : [
											{ label: __( 'List View', 'radio-station' ), value: 'list' },
											{ label: __( 'Grid View', 'radio-station' ), value: 'grid' },
										],
										onChange: ( value ) => {
											props.setAttributes( { view: value } );
										},
										value: atts.view
									})
								),
								/* --- Per Page --- */
								el( PanelRow, {},
									el( RangeControl, {
										label: __( 'Records Per Page', 'radio-station' ),
										help: __( 'Use 0 for all records.', 'radio-station' ),
										min: 0,
										max: 100,
										onChange: ( value ) => {
											props.setAttributes( { perpage: value } );
										},
										value: atts.perpage
									})
								),
								/* --- Pagination --- */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Pagination?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { pagination: value } );
										},
										checked: atts.pagination,
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

							/* === Archive Record Query === */
							el( PanelBody, { title: __( 'Archive Record Query', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								el( SelectControl, {
									label: __( 'Order By', 'radio-station' ),
									options: [
										{ label: __( 'Title', 'radio-station' ), value: 'title' },
										{ label: __( 'Publish Date', 'radio-station' ), value: 'date' },
										{ label: __( 'Modified Date', 'radio-station' ), value: 'modified' },
									],
									onChange: ( value ) => {
										props.setAttributes( { orderby: value } );
									},
									value: atts.orderby
								}),
								el( RadioControl, {
									label: __( 'Order', 'radio-station' ),
									options: [
										{ label: __( 'Ascending', 'radio-station' ), value: 'ASC' },
										{ label: __( 'Descending', 'radio-station' ), value: 'DESC' },
									],
									onChange: ( value ) => {
										props.setAttributes( { order: value } );
									},
									selected: atts.order
								}),
								/* TODO: --- Status Picker ? --- */						
								/* TODO: --- Genre Picker ? --- */
								/* TODO: --- Language Picker ? --- */
							),

							/* === Archive Record Display === */
							el( PanelBody, { title: __( 'Archive Record Display', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
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
								/* --- Description --- */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Description Display Format', 'radio-station' ),
										options: [
											{ label: __( 'View Default', 'radio-station' ), value: '' },
											{ label: __( 'None', 'radio-station' ), value: 'none' },
											{ label: __( 'Excerpt', 'radio-station' ), value: 'excerpt' },
											{ label: __( 'Full', 'radio-station' ), value: 'full' },
										],
										onChange: ( value ) => {
											props.setAttributes( { description: value } );
										},
										value: atts.description
									})
								),
								/* --- Image Display (conditional) --- */
								( ( atts.archive_type == 'shows' || atts.archive_type == 'overrides' ) &&
									el( PanelRow, { className: 'shows-only overrides-only' },
										el( ToggleControl, {
											label: __( 'Display Image?', 'radio-station' ),
											help: __( 'This setting is for Shows and Overrides.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { show_avatars: value } );
											},
											checked: atts.show_avatars
										})
									)
								),
								/* --- With Shifts Only (conditional) --- */
								( ( atts.archive_type == 'shows' ) &&
									el( PanelRow, { className: 'shows-only' },
										el( ToggleControl, {
											label: __( 'Only Shows with Shifts?', 'radio-station' ),
											help: __( 'This setting is for Shows only.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { with_shifts: value } );
											},
											checked: atts.with_shifts
										})
									)
								),
								/* --- Override Dates (conditional) --- */
								( ( atts.archive_type == 'overrides' ) &&
									el( PanelRow, { className: 'overrides-only' },
										el( ToggleControl, {
											label: __( 'Display Override Dates?', 'radio-station' ),
											help: __( 'This setting is for Overrides only.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { show_dates: value } );
											},
											checked: atts.show_dates
										})
									)
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
