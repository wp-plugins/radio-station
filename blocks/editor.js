
/* === Radio Station Block Editor Scripts === */

const el = window.wp.element.createElement;
const { Icon } = wp.components;

/* --- Add Block Category Icon --- */
( function() {
	const antennaSVG = () => (
		el( Icon, {icon: '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 512 512"><path d="M256 102.4c-56.55 0-102.4 45.85-102.4 102.4 0 30.66 13.747 57.856 35.098 76.621l9.19-26.419c-.239-.273-.051-.503-.171-.768-11.383-13.398-18.517-30.516-18.517-49.434 0-42.351 34.449-76.8 76.8-76.8s76.8 34.449 76.8 76.8c0 18.918-7.134 36.036-18.517 49.434-.12.256.068.495-.171.768l9.19 26.419c21.35-18.765 35.098-45.961 35.098-76.621 0-56.55-45.841-102.4-102.4-102.4z" /><path d="m370.483 495.002-86.118-247.603c13.756-9.182 22.835-24.815 22.835-42.598 0-28.279-22.921-51.2-51.2-51.2-28.279 0-51.2 22.921-51.2 51.2 0 17.784 9.079 33.417 22.835 42.598l-86.127 247.603c-2.321 6.673 1.203 13.978 7.885 16.299 1.391.478 2.807.7 4.207.7 5.299 0 10.249-3.302 12.092-8.602L184.96 448h142.089l19.268 55.398c1.835 5.299 6.784 8.602 12.083 8.602 1.399 0 2.816-.222 4.198-.7 6.691-2.321 10.215-9.625 7.885-16.298zM256 179.2c14.114 0 25.6 11.486 25.6 25.6s-11.486 25.6-25.6 25.6-25.6-11.486-25.6-25.6 11.486-25.6 25.6-25.6zm-4.113 76.382c1.374.111 2.705.418 4.113.418s2.739-.307 4.113-.418L291.43 345.6h-70.852l31.309-90.018zM193.86 422.4l17.809-51.2h88.661l17.809 51.2H193.86z" /><path d="M256 0C142.891 0 51.2 91.691 51.2 204.8c0 75.802 41.293 141.824 102.502 177.237l8.619-24.772C111.113 325.692 76.8 269.252 76.8 204.8c0-98.816 80.393-179.2 179.2-179.2s179.2 80.384 179.2 179.2c0 64.452-34.313 120.892-85.521 152.474l8.61 24.772C419.507 346.633 460.8 280.61 460.8 204.8 460.8 91.691 369.109 0 256 0z" /><path d="M256 51.2c-84.83 0-153.6 68.77-153.6 153.6 0 53.333 27.213 100.292 68.489 127.829l8.9-25.6C148.54 283.674 128 246.724 128 204.8c0-70.579 57.421-128 128-128s128 57.421 128 128c0 41.924-20.54 78.874-51.789 102.238l8.9 25.591C382.387 305.092 409.6 258.142 409.6 204.8c0-84.83-68.77-153.6-153.6-153.6z" /></svg>'}
		)
	);
	wp.blocks.updateCategory( 'radio-station-blocks', { icon: antennaSVG } );
} )();

/* --- Subscribe to Block State --- */
/* ref: https://wordpress.stackexchange.com/a/358256/76440 */
( () => {
    let blocksState = wp.data.select( 'core/block-editor' ).getBlocks();
    wp.data.subscribe( _.debounce( ()=> {
        newBlocksState = wp.data.select( 'core/block-editor' ).getBlocks();
        if ( blocksState.length !== newBlocksState.length ) {

            /* --- recheck for needed scripts --- */
			schedule = player = archive = clock = false;
			s_multi = s_table = s_tabs = s_list = s_grid = s_calendar = false;
			for ( i = 0; i < newBlocksState.length; i++ ) {
				block = newBlocksState[i];
				if ( block.name == 'radio-station/clock' ) {
					clock = true;
				} else if ( block.name == 'radio-station/schedule' ) {

					if ( block.attributes.clock ) {clock = true;}

					/* --- Schedule Views --- */
					schedule = true;
					if ( !block.attributes.pro ) {
						if ( block.attributes.view == 'table' ) {console.log('Table View Schedule Found'); s_table = true;}
						else if ( block.attributes.view == 'tabs' ) {console.log('Tab View Schedule Found'); s_tabs = true;}
						else if ( block.attributes.view == 'list' ) {console.log('List View Schedule Found'); s_list = true;}
						else if ( block.attributes.view == 'grid' ) {console.log('Grid View Schedule Found'); s_grid = true;}
						else if ( block.attributes.view == 'calendar' ) {console.log('Calendar View Schedule Found'); s_calendar = true;}
					}
					
					/* --- [Pro] Multiple Views --- */
					if ( block.attributes.pro ) {
						s_multi = true;
						if ( block.attributes.views.includes( 'table' ) ) {console.log('Table View Schedule Found'); s_table = true;}
						if ( block.attributes.views.includes( 'tabs' ) ) {console.log('Tab View Schedule Found'); s_tabs = true;}
						if ( block.attributes.views.includes( 'grid' ) ) {console.log('Grid View Schedule Found'); s_grid = true;}
						if ( block.attributes.views.includes( 'calendar' ) ) {console.log('Calendar View Schedule Found'); s_calendar = true;}
						if ( block.attributes.views.includes( 'list' ) ) {console.log('List View Schedule Found'); s_list = true;}
					}

				} else if ( block.name == 'radio-station/player' ) {
					player = true;
				} else if ( block.name == 'radio-station/archive' ) {
					archive = true;
					if ( block.attributes.pagination ) {
						/* TODO: check pagination type */
					}
				}					
			}
			if (clock && !jQuery('#radio-clock-js').length) {radio_station_load_block_script('clock');}
			if (schedule) {
				/* --- schedule view scripts --- */
				if (s_multi && !jQuery('#radio-schedule-multiview-js').length) {
					radio_station_load_block_script('schedule-multiview');
				}
				if (s_table && !jQuery('#radio-schedule-table-js').length) {
					radio_station_load_block_script('schedule-table');
					var radio_load_table = setInterval(function() { if (typeof radio_table_initialize == 'function') {
						radio_table_initialize(); clearInterval(radio_load_table);
					} }, 1000);
				}
				if (s_tabs && !jQuery('#radio-schedule-tabs-js').length) {
					radio_station_load_block_script('schedule-tabs');
					var radio_load_tabs = setInterval(function() { if (typeof radio_tabs_initialize == 'function') {
						radio_tabs_init = false; radio_tabs_initialize(); clearInterval(radio_load_tabs);
					} }, 1000);
				}
				if (s_list && !jQuery('#radio-schedule-list-js').length) {
					radio_station_load_block_script('schedule-list');
					var radio_load_list = setInterval(function() { if (typeof radio_list_hightlight == 'function') {
						radio_list_hightlight(); clearInterval(radio_load_list);
					} }, 1000);
				}
				if (s_grid && !jQuery('#radio-schedule-grid-js').length) {
					radio_station_load_block_script('schedule-grid');
					var radio_load_grid = setInterval(function() { if (typeof radio_grid_initialize == 'function') {
						radio_grid_init = false; radio_grid_initialize(); radio_grid_time_spacing(); clearInterval(radio_load_grid);
					} }, 1000);
				}
				if (s_calendar && !jQuery('#radio-schedule-calendar-js').length) {
					radio_station_load_block_script('schedule-calendar');
					var radio_load_calendar = setInterval(function() { if (typeof radio_calendar_initialize == 'function') {
						radio_calendar_init = false; radio_calendar_initialize(); radio_sliders_check(); clearInterval(radio_load_calendar);
					} }, 1000);
				}
			}
			if (player) {
				radio_station_load_block_script('player');
				/* TODO: maybe initialize player ?
				var radio_load_player = setInterval(function() { if (typeof ??? == 'function') {
					radio_player_init = false; ???(); clearInterval(radio_load_player);
				} }, 1000); */
			}
			if (archive) {
				/* TODO: check for archive pagination type */
			}
        }
        blocksState = newBlocksState;
    }, 300 ) );
} )();

function radio_station_load_block_script(handle) {
	id = 'radio-'+handle+'-js';
	url = radio.ajax_url+'?action=radio_station_block_script&handle='+handle;
	jQuery('html head').append('<script id="'+id+'" src="'+url+'"></script>');
}
