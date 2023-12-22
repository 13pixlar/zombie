<?php
?>
<div id="gf_new_chart_modal" style="display:none;">

    <form id="gf_new_chart_modal_form">

			<div class="gf_new_chart_modal_container">

				<div class="setting-row">

                    <label for="new_chart_title"><?php esc_html_e( 'Chart/Calculation Title', 'gfchart' ); ?>

                        <span class="gfield_required">*</span></label><br />

                    <input type="text" class="regular-text" value="" id="new_chart_title" tabindex="9000">

                </div>

				<div class="setting-row">

                    <label for="new_chart_source_form"><?php esc_html_e( 'Source Form', 'gfchart' ); ?></label><br />

                    <select id="new_chart_source_form" tabindex="9001">

                        <?php foreach( GFAPI::get_forms() as $form ) { ?>

                            <option value="<?php echo $form['id']?>"><?php echo $form['title']?></option>

                        <?php } ?>

                    </select>

                </div>

				<div class="submit-row">

                    <?php echo apply_filters( 'gform_new_chart_button', '<input id="save_new_chart" type="submit" class="button button-large button-primary" value="' . esc_html__( 'Create Chart/Calculation', 'gfchart' ) . '" tabindex="9002" />' ); ?>

                    <div id="gf_new_chart_error_message" style="display:inline-block;"></div>

                </div>

			</div>

    </form>

</div>