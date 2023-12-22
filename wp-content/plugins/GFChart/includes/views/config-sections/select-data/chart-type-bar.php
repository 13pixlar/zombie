<?php
/**
 * GFChart Configuration Metabox — Select Data Tab — Bar Chart Type Basic Settings
 */
?>
<h1><?php _e( 'Bar', 'gfchart' ) ?></h1>

<table class="form-table striped">

	<tbody>

	<tr>
		<th><?php _e( 'Source Form', 'gfchart' ) ?></th>
		<td colspan="4">
			<?php esc_html_e( $source_form[ 'title' ] ) ?>
		</td>
	</tr>

	<tr>
		<th><?php _e( 'x-axis', 'gfchart' ) ?> (<?php _e( 'main field', 'gfchart' ) ?>)</th>
		<td colspan="4">
			<select id="gfchart-bar-xaxis-main-field" name="gfchart_config[xaxis-main-field]">
				<option value=""></option>
				<?php foreach ( $form_fields as $field ) {

					$field_id    = $field[ 0 ];
					$field_label = esc_html( GFCommon::truncate_middle( $field[ 1 ], 40 ) );
					?>
					<option
						value="<?php esc_attr_e( $field_id ) ?>" <?php selected( rgar( $gfchart_config, 'xaxis-main-field' ), $field_id, true ) ?>><?php esc_html_e( $field_label ) ?></option>

				<?php } ?>
			</select>
		<a id="gfchart-bar-xaxis-segment-toggle-on" href="">
            <img class="gfchart-add" src="<?php echo GFCommon::get_base_url() ?>/images/add.png" alt="Add a segment field" title="Add a segment field"/>
        </a>
            <a id="gfchart-bar-xaxis-segment-toggle-off" href="" style="display:none;">
                <img class="gfchart-remove"
                     src="<?php echo GFCommon::get_base_url() ?>/images/remove.png"
                     alt="Remove segment field"
                     title="Remove segment field">
            </a>
		</td>
	</tr>

	<tr id="gfchart-bar-xaxis-segment-container" style="display:none;">
		<th><?php _e( 'x-axis', 'gfchart' ) ?> (<?php _e( 'segment', 'gfchart' ) ?>)</th>
		<td colspan="4">
			<select id="gfchart-bar-xaxis-segment-field" name="gfchart_config[xaxis-segment-field]">
				<option value=""></option>
				<?php foreach ( $form_fields as $field ) {

					$field_id    = $field[ 0 ];
					$field_label = esc_html( GFCommon::truncate_middle( $field[ 1 ], 40 ) );
					?>
					<option
						value="<?php esc_attr_e( $field_id ) ?>" <?php selected( rgar( $gfchart_config, 'xaxis-segment-field' ), $field_id, true ) ?>><?php esc_html_e( $field_label ) ?></option>

				<?php } ?>
			</select>

			<select id="gfchart-bar-xaxis-segment-display" name="gfchart_config[xaxis-segment-display]">
				<option
					value="beside" <?php selected( rgar( $gfchart_config, 'xaxis-segment-display' ), 'beside', true ); ?>><?php _e( 'beside', 'gfchart' ) ?>
				</option>
				<option
					value="stackabsolute" <?php selected( rgar( $gfchart_config, 'xaxis-segment-display' ), 'stackabsolute', true ); ?>><?php _e( 'stack absolute', 'gfchart' ) ?>
				</option>
				<option
					value="stackpercent" <?php selected( rgar( $gfchart_config, 'xaxis-segment-display' ), 'stackpercent', true ); ?>><?php _e( 'stack percent', 'gfchart' ) ?></option>
			</select>
		</td>

	</tr>

	<tr>
		<th><?php _e( 'y-axis', 'gfchart' ) ?></th>
		<td colspan="4"><?php _e( 'Count or sum entries?', 'gfchart' ) ?><br/>
			<select id="gfchart-bar-yaxis" name="gfchart_config[yaxis]">
				<option value="count" <?php selected( rgar( $gfchart_config, 'yaxis' ), 'count', true ) ?>><?php _e( 'count', 'gfchart' ) ?></option>
				<option value="sum" <?php selected( rgar( $gfchart_config, 'yaxis' ), 'sum', true ) ?>><?php _e( 'sum', 'gfchart' ) ?></option>
			</select>
		</td>
	</tr>

	<tr id="gfchart-bar-xaxis-sum-field-container" style="display:none;">
			<th><?php _e( 'Field to sum', 'gfchart' ) ?>&nbsp;(<?php _e( 'if different from x-axis main field', 'gfchart' ) ?>)</th>
			<td colspan="4">
				<select id="gfchart-bar-xaxis-sum-field" name="gfchart_config[xaxis-sum-field]">
					<option value=""></option>
					<?php foreach ( $form_fields as $field ) {

						$field_id    = $field[ 0 ];
						$field_label = esc_html( GFCommon::truncate_middle( $field[ 1 ], 40 ) );
						?>
						<option
							value="<?php esc_attr_e( $field_id ) ?>" <?php selected( rgar( $gfchart_config, 'xaxis-sum-field' ), $field_id, true ) ?>><?php esc_html_e( $field_label ) ?></option>

					<?php } ?>
				</select>
			</td>
		</tr>

	<tr>
		<th><?php _e( 'Sort by', 'gfchart' ) ?></th>
		<td colspan="4">
			<label for="gfchart-chart-type-bar-sortby-label"><?php _e( 'Label', 'gfchart' ) ?></label> <input type="radio"
			                                                                                             id="gfchart-chart-type-bar-sortby-label"
			                                                                                             name="gfchart_config[sortby]"
			                                                                                             value="label" <?php ( '' == rgar( $gfchart_config, 'sortby' ) ) ? checked( '', '', true ) : checked( rgar( $gfchart_config, 'sortby' ), 'label', true ); ?> />
			<label for="gfchart-chart-type-bar-sortby-value"><?php _e( 'Value', 'gfchart' ) ?></label> <input type="radio"
			                                                                                             id="gfchart-chart-type-bar-sortby-value"
			                                                                                             name="gfchart_config[sortby]"
			                                                                                             value="value" <?php checked( rgar( $gfchart_config, 'sortby' ), 'value', true ); ?> />

		</td>
	</tr>
	<tr>
		<th><?php _e( 'Sort type', 'gfchart' ) ?></th>
		<td colspan="4">
			<label for="gfchart-chart-type-bar-sort-type-asc"><?php _e( 'Ascending', 'gfchart' ) ?></label>
			<input
				type="radio" id="gfchart-chart-type-bar-sort-type-asc" name="gfchart_config[sort_type]"
				value="asc" <?php  ( '' == rgar( $gfchart_config, 'sort_type' ) ) ? checked( '', '', true ) : checked( rgar( $gfchart_config, 'sort_type' ), 'asc', true ); ?> />
			<label
				for="gfchart-chart-type-bar-sort-type-desc"><?php _e( 'Descending', 'gfchart' ) ?></label>
			<input
				type="radio" id="gfchart-chart-type-bar-sort-type-desc" name="gfchart_config[sort_type]"
				value="desc" <?php checked( rgar( $gfchart_config, 'sort_type' ), 'desc', true ); ?> />

		</td>
	</tr>
	<tr>
		<th><?php _e( 'Show zero values?', 'gfchart' ) ?>&nbsp;(<?php _e( 'unsegmented data only', 'gfchart' ) ?>)</th>
		<td colspan="4">
			<input type="checkbox" id="gfchart-bar-show-zero-values" name="gfchart_config[show_zero_values]"
			       value="1" <?php checked( rgar( $gfchart_config, 'show_zero_values' ), '1' ) ?>" />
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Entries for logged-in user only?', 'gfchart' ) ?></th>
		<td colspan="4">
			<input type="checkbox" id="gfchart-bar-user-only" name="gfchart_config[user_only]"
			       value="1" <?php checked( rgar( $gfchart_config, 'user_only' ), '1' ) ?>" />
		</td>
	</tr>

    <?php if(! empty( GFAPI::get_fields_by_type( $source_form, array( 'product' ), false ) ))  { ?>
	<tr>
		<th><?php _e( 'Payment Status', 'gfchart' ) ?></th>
				<td>
                    <select id="gfchart-bar-payment-status" name="gfchart_config[payment_status]">
                        <option value=""></option>
						<?php foreach( GFCommon::get_entry_payment_statuses() as $value => $text ): ?>
                            <option value="<?php esc_attr_e( $value ) ?>" <?php selected( rgar( $gfchart_config, 'payment_status' ), $value, true );?>><?php esc_html_e( $text ) ?></option>
						<?php endforeach; ?>
                    </select>
				</td>

		</tr>
    <?php } ?>

	<tr>
		<th><?php _e( 'Date Range (optional)', 'gfchart' ) ?></th>
		<td colspan="4">

            <div class="column">
			<input type="text" id="gfchart-bar-date-filter-start" name="gfchart_config[date_filter_start]"
			       value="<?php esc_attr_e( rgar( $gfchart_config, 'date_filter_start' ) ) ?>"/> <strong><label
					for="gfchart-bar-date-filter-start"
					style="display:block;"><?php esc_html_e( 'Start', 'gfchart' ); ?></label></strong>
            </div>
            <div class="column">
			<input type="text" id="gfchart-bar-date-filter-end" name="gfchart_config[date_filter_end]"
			       value="<?php esc_attr_e( rgar( $gfchart_config, 'date_filter_end' ) ) ?>"/> <strong><label
					for="gfchart-bar-date-filter-end"
					style="display:block;"><?php esc_html_e( 'End', 'gfchart' ); ?></label></strong>
            </div>
		</td>

	</tr>

	<tr>
		<th><?php _e( 'Enable additional filters?', 'gfchart' ) ?></th>
		<td colspan="4">
			<input type="checkbox" id="gfchart-bar-additional-filters" name="gfchart_config[additional_filters]"
			       value="1" <?php checked( rgar( $gfchart_config, 'additional_filters' ), '1' ) ?>" />

			<div id="gfchart_bar_entry_filters" class="hide-if-js"
			     style="<?php echo rgar( $gfchart_config, 'additional_filters' ) ? '' : 'display:none;' ?>"><?php esc_html_e( 'This requires Javascript to be enabled.', 'gfchart' ); ?></div>
		</td>
	</tr>

	</tbody>

</table>