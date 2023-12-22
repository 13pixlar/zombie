<?php
/**
 * GFChart Configuration Metabox — Customiser Tab — Calculation Chart Type Basic Settings
 */
?>
<h1><?php _e( 'Calculation', 'gfchart' )?></h1>
<table class="form-table striped">
	<tbody>
	<tr>
		<th><?php _e( 'Number format', 'gfchart' ) ?></th>
		<td>
			<select name="gfchart_config[number_format]">
				<option value="decimal_dot" <?php selected( rgar( $gfchart_config, 'number_format' ), 'decimal_dot', true );?>>9,999.99</option>
				<option value="decimal_comma" <?php selected( rgar( $gfchart_config, 'number_format' ), 'decimal_comma', true );?>>9.999,99</option>
				<option value="currency" <?php selected( rgar( $gfchart_config, 'number_format' ), 'currency', true );?>><?php esc_html_e( 'Currency', 'gfchart' ) ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Rounding', 'gravityforms' ); ?></th>
		<td>
		<select name="gfchart_config[number_rounding]">
			<option value="0" <?php selected( rgar( $gfchart_config, 'number_rounding' ), '0', true );?>>0</option>
			<option value="1" <?php selected( rgar( $gfchart_config, 'number_rounding' ), '1', true );?>>1</option>
			<option value="2" <?php selected( rgar( $gfchart_config, 'number_rounding' ), '2', true );?>>2</option>
			<option value="3" <?php selected( rgar( $gfchart_config, 'number_rounding' ), '3', true );?>>3</option>
			<option value="4" <?php selected( rgar( $gfchart_config, 'number_rounding' ), '4', true );?>>4</option>
			<option value="norounding" <?php selected( rgar( $gfchart_config, 'number_rounding' ), 'norounding', true );?>><?php esc_html_e( 'Do not round', 'gfchart' ); ?></option>
		</select>
		</td>
	</tr>
	</tbody>
</table>