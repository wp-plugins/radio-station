<div style="width: 620px; padding: 10px">
	<h2><?php _e( 'Export Playlists', 'radio-station' ); ?></h2>
	<form action="" method="post" id="export_form" accept-charset="utf-8" style="position:relative">

		<?php wp_nonce_field( 'station_export_valid' ); ?>

		<input type="hidden" name="export_action" value="station_playlist_export" />
		<table class="form-table">

			<tr valign="top">
				<?php $smonth = isset( $_POST['station_export_start_month'] ) ? $_POST['station_export_start_month'] : ''; ?>
				<th scope="row"><?php _e( 'Start Date', 'radio-station' ); ?></th>
				<td>
					<select name="station_export_start_month" id="station_export_start_month">
						<option value="01" <?php if ( $smonth == '01' ) {echo 'selected="selected"';} ?>>01 (<?php echo radio_station_translate_month('Jan', true); ?>)</option>
						<option value="02" <?php if ( $smonth == '02' ) {echo 'selected="selected"';} ?>>02 (<?php echo radio_station_translate_month('Feb', true); ?>)</option>
						<option value="03" <?php if ( $smonth == '03' ) {echo 'selected="selected"';} ?>>03 (<?php echo radio_station_translate_month('Mar', true); ?>)</option>
						<option value="04" <?php if ( $smonth == '04' ) {echo 'selected="selected"';} ?>>04 (<?php echo radio_station_translate_month('Apr', true); ?>)</option>
						<option value="05" <?php if ( $smonth == '05' ) {echo 'selected="selected"';} ?>>05 (<?php echo radio_station_translate_month('May', true); ?>)</option>
						<option value="06" <?php if ( $smonth == '06' ) {echo 'selected="selected"';} ?>>06 (<?php echo radio_station_translate_month('Jun', true); ?>)</option>
						<option value="07" <?php if ( $smonth == '07' ) {echo 'selected="selected"';} ?>>07 (<?php echo radio_station_translate_month('Jul', true); ?>)</option>
						<option value="08" <?php if ( $smonth == '08' ) {echo 'selected="selected"';} ?>>08 (<?php echo radio_station_translate_month('Aug', true); ?>)</option>
						<option value="09" <?php if ( $smonth == '09' ) {echo 'selected="selected"';} ?>>09 (<?php echo radio_station_translate_month('Sep', true); ?>)</option>
						<option value="10" <?php if ( $smonth == '10' ) {echo 'selected="selected"';} ?>>10 (<?php echo radio_station_translate_month('Oct', true); ?>)</option>
						<option value="11" <?php if ( $smonth == '11' ) {echo 'selected="selected"';} ?>>11 (<?php echo radio_station_translate_month('Nov', true); ?>)</option>
						<option value="12" <?php if ( $smonth == '12' ) {echo 'selected="selected"';} ?>>12 (<?php echo radio_station_translate_month('Dec', true); ?>)</option>
					</select>

					<?php $sday = isset( $_POST['station_export_start_day'] ) ? $_POST['station_export_start_day'] : ''; ?>
					<select name="station_export_start_day" id="station_export_start_day">
						<?php
							for ( $i = 1; $i <= 31; $i++ ) {
								$day = $i;
								if ( $i < 10 ) {$day = '0'.$day;}
								if ( $sday == $day ) {$selected = ' selected="selected"';} else {$selected = '';}
								echo '<option value="'.$day.'"'.$selected.'>'.$i.'</option>';
							}
						?>
					</select>

					<?php $syear = isset( $_POST['station_export_start_year'] ) ? $_POST['station_export_start_year'] : ''; ?>
					<select name="station_export_start_year" id="station_export_start_year">
						<?php
							$year = date( 'Y' );
							for($i = $year - 5; $i <= ($year + 5); $i++ ) {
								$selected = '';
								if ( $i == $syear ) {$selected = ' selected="selected"';}
								elseif ( ( $i == $year ) && ( $syear == '' ) ) {
									$selected = ' selected="selected"';
								}
								echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
							}
						?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'End Date', 'radio-station' ); ?></th>
				<td>
					<?php $emonth = isset( $_POST['station_export_end_month'] ) ? $_POST['station_export_end_month'] : ''; ?>
					<select name="station_export_end_month" id="station_export_end_month">
						<option value="01" <?php if ( $emonth == '01' ) {echo 'selected="selected"';} ?>>01 (<?php echo radio_station_translate_month('Jan', true); ?>)</option>
						<option value="02" <?php if ( $emonth == '02' ) {echo 'selected="selected"';} ?>>02 (<?php echo radio_station_translate_month('Feb', true); ?>)</option>
						<option value="03" <?php if ( $emonth == '03' ) {echo 'selected="selected"';} ?>>03 (<?php echo radio_station_translate_month('Mar', true); ?>)</option>
						<option value="04" <?php if ( $emonth == '04' ) {echo 'selected="selected"';} ?>>04 (<?php echo radio_station_translate_month('Apr', true); ?>)</option>
						<option value="05" <?php if ( $emonth == '05' ) {echo 'selected="selected"';} ?>>05 (<?php echo radio_station_translate_month('May', true); ?>)</option>
						<option value="06" <?php if ( $emonth == '06' ) {echo 'selected="selected"';} ?>>06 (<?php echo radio_station_translate_month('Jun', true); ?>)</option>
						<option value="07" <?php if ( $emonth == '07' ) {echo 'selected="selected"';} ?>>07 (<?php echo radio_station_translate_month('Jul', true); ?>)</option>
						<option value="08" <?php if ( $emonth == '08' ) {echo 'selected="selected"';} ?>>08 (<?php echo radio_station_translate_month('Aug', true); ?>)</option>
						<option value="09" <?php if ( $emonth == '09' ) {echo 'selected="selected"';} ?>>09 (<?php echo radio_station_translate_month('Sep', true); ?>)</option>
						<option value="10" <?php if ( $emonth == '10' ) {echo 'selected="selected"';} ?>>10 (<?php echo radio_station_translate_month('Oct', true); ?>)</option>
						<option value="11" <?php if ( $emonth == '11' ) {echo 'selected="selected"';} ?>>11 (<?php echo radio_station_translate_month('Nov', true); ?>)</option>
						<option value="12" <?php if ( $emonth == '12' ) {echo 'selected="selected"';} ?>>12 (<?php echo radio_station_translate_month('Dec', true); ?>)</option>
					</select>

					<?php $eday = isset($_POST['station_export_end_day']) ? $_POST['station_export_end_day'] : ''; ?>
					<select name="station_export_end_day" id="station_export_end_day">
						<?php
							for ( $i = 1; $i <= 31; $i++ ) {
								$day = $i;
								if ( $i < 10 ) {$day = '0'.$day;}
								if ( $eday == $day ) {$selected = ' selected="selected"';} else {$selected = '';}
								echo '<option value="'.$day.'"'.$selected.'>'.$i.'</option>';
							}
						?>
					</select>

					<?php $eyear = isset( $_POST['station_export_end_year'] ) ? $_POST['station_export_end_year'] : ''; ?>
					<select name="station_export_end_year" id="station_export_end_year">
						<?php
							$year = date( 'Y' );
							for ( $i = $year - 5; $i <= ($year + 5); $i++ ) {
								$selected = '';
								if ( $i == $eyear ) {$selected = ' selected="selected"';}
								elseif ( ( $i == $year ) && ( $eyear == '' ) ) {
									$selected = ' selected="selected"';
								}
								echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
							}
						?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td>
					<input type="submit" name="Submit" class="button-primary" value="<?php _e('Export', 'radio-station'); ?>"/>
				</td>
			</tr>
		</table>
	</form>
</div>