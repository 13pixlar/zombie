<?php
/**
 * GFChart Configuration Metabox — Customiser Tab — Pie Chart
 */
?>
<h1><?php _e( 'Pie', 'gfchart' ) ?></h1>
<table class="form-table striped">
	<tbody>
	<tr>
		<th><?php _e( 'Chart title', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-pie-chart-title" name="gfchart_config[title]"
			       value="<?php esc_attr_e( rgar( $gfchart_config, 'title' ) ) ?>"/>
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Chart height', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-pie-chart-height" name="gfchart_config[height]"
			       value="<?php esc_attr_e(  ( '' == rgar( $gfchart_config, 'height' ) ) ? 300 : rgar( $gfchart_config, 'height' ) ) ?>"/>
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Chart width', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-pie-chart-width" name="gfchart_config[width]"
			       value="<?php esc_attr_e(  ( '' == rgar( $gfchart_config, 'width' ) ) ? 400 : rgar( $gfchart_config, 'width' ) ) ?>"/>
		</td>
	</tr>
    <tr>
        <th><?php _e( 'Responsive', 'gfchart' ) ?></th>
        <td>
            <input type="checkbox" id="gfchart-pie-responsive" name="gfchart_config[responsive]"
                   value="1" <?php checked( rgar( $gfchart_config, 'responsive' ), '1' ) ?>" />

        </td>
    </tr>
	<tr>
		<th><?php _e( 'Additional code', 'gfchart' ) ?></th>
		<td>
			<textarea id="gfchart-pie-additional-code"
			          name="gfchart_config[additional_code]"><?php echo rgar( $gfchart_config, 'additional_code' ) ?></textarea>
			<span class="instruction" style="display: block;font-weight:normal;">
				Example Formatting: <br/>
				<code>{"height": 300, <br/>"width": 400,<br/>"backgroundColor": "white"}</code>
			</span>
		</td>
	</tr>

	<?php do_action( 'gfchart_config_section_customiser_pie_settings_after', $gfchart_config, $source_form ); ?>

    </tbody>
</table>