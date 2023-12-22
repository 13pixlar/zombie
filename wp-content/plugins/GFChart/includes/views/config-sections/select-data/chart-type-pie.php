<?php
/**
 * GFChart Configuration Metabox — Select data Tab — Pie Chart
 */
?>

<h1><?php _e( 'Pie', 'gfchart' ) ?></h1>

<table class="form-table striped">

	<tbody>

	<tr>
		<th><?php _e( 'Source Form', 'gfchart' ) ?></th>
		<td colspan="2">
			<?php esc_html_e( $source_form[ 'title' ] ) ?>
		</td>
	</tr>

	<tr>
		<th><?php _e( 'Field', 'gfchart' ) ?></th>
		<td colspan="2">
			<select id="gfchart-pie-source-field" name="gfchart_config[source-field]">
				<option value=""></option>
				<?php foreach ( $form_fields as $field ) {

					$field_id    = $field[ 0 ];
					$field_label = esc_html( GFCommon::truncate_middle( $field[ 1 ], 40 ) );
					?>
					<option
						value="<?php esc_attr_e( $field_id ) ?>" <?php selected( rgar( $gfchart_config, 'source-field' ), $field_id, true ) ?>><?php esc_html_e( $field_label ) ?></option>

				<?php } ?>
			</select>
		</td>
	</tr>

	<tr>
		<th><?php _e( 'Entries for logged-in user only?', 'gfchart' ) ?></th>
		<td colspan="2">
			<input type="checkbox" id="gfchart-pie-user-only" name="gfchart_config[user_only]"
			       value="1" <?php checked( rgar( $gfchart_config, 'user_only' ), '1' ) ?>" />
		</td>
	</tr>

	<?php if(! empty( GFAPI::get_fields_by_type( $source_form, array( 'product' ), false ) ))  { ?>

    <tr>
			<th><?php _e( 'Payment Status', 'gfchart' ) ?></th>
        <td>
            <select id="gfchart-pie-payment-status" name="gfchart_config[payment_status]">
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
				<input type="text" id="gfchart-pie-date-filter-start" name="gfchart_config[date_filter_start]"
				       value="<?php esc_attr_e( rgar( $gfchart_config, 'date_filter_start' ) ) ?>" />
				<strong><label for="gfchart-pie-date-filter-start"
				               style="display:block;"><?php esc_html_e( 'Start', 'gfchart' ); ?></label></strong>
</div>
			<div class="column">
				<input type="text" id="gfchart-pie-date-filter-end" name="gfchart_config[date_filter_end]"
				       value="<?php esc_attr_e( rgar( $gfchart_config, 'date_filter_end' ) ) ?>" />
				<strong><label for="gfchart-pie-date-filter-end"
				               style="display:block;"><?php esc_html_e( 'End', 'gfchart' ); ?></label></strong>
			</div>
		</td>

	</tr>

	<tr>
		<th><?php _e( 'Enable additional filters?', 'gfchart' ) ?></th>
		<td colspan="2">
			<input type="checkbox" id="gfchart-pie-additional-filters" name="gfchart_config[additional_filters]"
			       value="1" <?php checked( rgar( $gfchart_config, 'additional_filters' ), '1' ) ?>" />

			<div id="gfchart_pie_entry_filters" class="hide-if-js"
			     style="<?php echo rgar( $gfchart_config, 'additional_filters' ) ? '' : 'display:none;' ?>"><?php esc_html_e( 'This requires Javascript to be enabled.', 'gfchart' ); ?></div>
		</td>
	</tr>

	</tbody>

</table>