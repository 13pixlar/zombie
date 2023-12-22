<?php
/**
 * GFChart Configuration Metabox — Customiser Tab — Progress Bar Chart Type Basic Settings
 */
?>
<h1><?php _e( 'Progress Bar', 'gfchart' )?></h1>
<table class="form-table striped">
	<tbody>
	<tr>
		<th><?php _e( 'Goal display format', 'gfchart' ) ?></th>
		<td>
			<select name="gfchart_config[goal_display_format]">
				<option value="decimal_dot" <?php selected( rgar( $gfchart_config, 'goal_display_format' ), 'decimal_dot', true );?>>9,999.99</option>
				<option value="decimal_comma" <?php selected( rgar( $gfchart_config, 'goal_display_format' ), 'decimal_comma', true );?>>9.999,99</option>
				<option value="currency" <?php selected( rgar( $gfchart_config, 'goal_display_format' ), 'currency', true );?>><?php esc_html_e( 'Currency', 'gfchart' ) ?></option>
			</select>
		</td>
	</tr>
	</tbody>
</table>