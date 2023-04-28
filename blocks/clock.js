/**
 * === Radio Clock Block ===
 */
(() => {

	const el = window.wp.element.createElement;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;
	const { __, _e } = window.wp.i18n;

	registerBlockType( 'radio-station/clock', {

		/* --- Block Settings --- */
		title: '[Radio Station] Radio Clock',
		description: __( 'Radio Station Clock time display.', 'radio-station' ),
		icon: 'clock',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Clock Display Options --- */
			time_format: { type: 'string', default: '' },
			day: { type: 'string', default: 'full' },
			date: { type: 'boolean', default: true },
			month: { type: 'string', default: 'full' },
			zone: { type: 'boolean', default: true },
			seconds: { type: 'boolean', default: true },
			
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
					el( ServerSideRender, { block: 'radio-station/clock', className: 'radio-clock-block', attributes: atts } ),
					el( InspectorControls, {},
						el ( Panel, {},
							el( PanelBody, { title: __( 'Clock Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* Time Display Format */
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
								/* Day Display Format */
								el( PanelRow, {},
									el( SelectControl, {
										label: __( 'Day Display Format', 'radio-station' ),
										options: [
											{ label: __( 'Full', 'radio-station' ), value: 'full' },
											{ label: __( 'Short', 'radio-station' ), value: 'short' },
											{ label: __( 'None', 'radio-station' ), value: 'none' },
										],
										onChange: ( value ) => {
											props.setAttributes( { day: value } );
										},
										value: atts.day
									})
								),
								/* Date Display */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Date?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { date: value } );
										},
										checked: atts.date,
									})
								),
								/* Month Display Format */
								el( PanelRow, {},
									el( SelectControl, {
										label: 'Month Display Format',
										options: [
											{ label: __( 'Full', 'radio-station' ), value: 'full' },
											{ label: __( 'Short', 'radio-station' ), value: 'short' },
											{ label: __( 'None', 'radio-station' ), value: 'none' },
										],
										onChange: ( value ) => {
											props.setAttributes( { month: value } );
										},
										value: atts.month
									})
								),
								/* Timezone Display */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Timezone?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { zone: value } );
										},
										checked: atts.zone,
									})
								),
								/* Seconds Display */
								el( PanelRow, {},
									el( ToggleControl, {
										label: __( 'Display Seconds?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { seconds: value } );
										},
										checked: atts.seconds,
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
