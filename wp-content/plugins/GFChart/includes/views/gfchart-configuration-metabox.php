<?php
/**
 * GFChart Configuration Metabox
 */
?>
<?php wp_nonce_field( 'gfchart_config', 'gfchart_config_nonce' ); ?>
<style type="text/css">
	a.nav-tab {
		margin-bottom: -4px;
	}

	.nav-tab-active, .nav-tab-active:hover {
		border-bottom: 1px solid #ffffff;
		background: #ffffff;
	}
</style>
<div id="gfchart-view-configuration-tabs">

	<?php

    $payment_status = rgar( $gfchart_config, 'payment_status' );

	if ( ( strlen( $payment_status ) > 0 ) && ( ! in_array( $payment_status, GFCommon::get_entry_payment_statuses(), true ) ) ): ?>

        <div class="notice notice-error">

            <p><strong><?php _e( 'Error:', 'gfchart' ) ?></strong> <?php echo sprintf( __( 'Unknown Payment Status option "%s". To fix, go to the "Select data" tab, pick a valid status from the dropdown menu, and then save.', 'gfchart' ), $payment_status ) ?></p>

        </div>

    <?php endif ?>

	<h2 class="nav-tab-wrapper current">

		<?php foreach ( $config_tabs as $tab ) { ?>

			<a id="gfchart-config-tab-<?php echo $tab[ 'id' ] ?>" href="#<?php echo $tab[ 'id' ] ?>"
			   class="nav-tab <?php echo ( 'design' == $tab[ 'id' ] ) ? 'nav-tab-active' : '' ?>">
				<?php echo esc_html( $tab[ 'label' ] ); ?>
			</a>

		<?php } ?>
		<?php unset( $tab ); ?>

	</h2>

	<form>

		<?php for ( $i = 0; $i < sizeof( $config_tabs ) - 1; $i ++ ) { ?>

			<div id="<?php echo $config_tabs[ $i ][ 'id' ] ?>"
			     class="inside <?php echo ( 0 == $i ) ? '' : 'hidden' ?> gfchart-config-section">

				<?php $view = $this->get_config_section_view( "{$config_tabs[$i]['id']}", false );

				if ( $view ) { include( $view ); }

				unset( $view );
				?>

				<div class="gfchart-config-tab-nav">

					<div class="gfchart-config-tab-nav-item gfchart-config-tab-nav-prev">

						<?php if ( 0 < $i ) { ?>
							<a href="#<?php echo $config_tabs[ $i - 1 ][ 'id' ] ?>"
							   class="gfchart-config-tab-nav-button gfchart-config-tab-nav-button-prev"
							   data-gfconfig-tab="<?php echo $config_tabs[ $i - 1 ][ 'id' ] ?>"><?php echo $config_tabs[ $i - 1 ][ 'label' ] ?></a>

						<?php } else { ?>
							&nbsp;
						<?php } ?>
					</div>

					<div class="gfchart-config-tab-nav-item gfchart-config-tab-nav-save">

						<input type="button" value="Save" class="button-secondary gfchart-config-tab-nav-button-save"
						       data-gfconfig-tab="save">

					</div>

					<div class="gfchart-config-tab-nav-item gfchart-config-tab-nav-next">

						<a href="#<?php echo $config_tabs[ $i + 1 ][ 'id' ] ?>"
						   class="gfchart-config-tab-nav-button gfchart-config-tab-nav-button-next"
						   data-gfconfig-tab="<?php echo $config_tabs[ $i + 1 ][ 'id' ] ?>"><?php echo $config_tabs[ $i + 1 ][ 'label' ] ?></a>

					</div>

				</div>

			</div>

		<?php } ?>

	</form>


	<div id="preview" class="inside hidden gfchart-config-section">

		<h4><?php esc_html_e( 'Preview chart/calculation', 'gfchart' ); ?></h4>

		<?php include( GFCHART_PATH . 'includes/views/config-sections/preview.php' ); ?>

		<div class="gfchart-config-tab-nav">

			<div class="gfchart-config-tab-nav-item gfchart-config-tab-nav-prev">

				<a href="#<?php echo $config_tabs[ $i - 1 ][ 'id' ] ?>"
				   class="gfchart-config-tab-nav-button gfchart-config-tab-nav-button-prev"
				   data-gfconfig-tab="<?php echo $config_tabs[ $i - 1 ][ 'id' ] ?>"><?php echo $config_tabs[ $i - 1 ][ 'label' ] ?></a>

			</div>

			<div class="gfchart-config-tab-nav-item gfchart-config-tab-nav-save">

				<input type="button" value="Save" class="button-secondary gfchart-config-tab-nav-button-save"
				       data-gfconfig-tab="save">

			</div>

			<div class="gfchart-config-tab-nav-item gfchart-config-tab-nav-next">

				&nbsp;
			</div>

		</div>

	</div>

</div>
<script>
	(function ( $ ) {
		'use strict';

		$( function () {

			// Grab the wrapper for the Navigation Tabs
			var navTabs = $( '#gfchart-view-configuration-tabs' ).children( '.nav-tab-wrapper' ),
				tabIndex = null;

			/* Whenever each of the navigation tabs is clicked, check to see if it has the 'nav-tab-active'
			 * class name. If not, then mark it as active; otherwise, don't do anything (as it's already
			 * marked as active.
			 *
			 * Next, when a new tab is marked as active, the corresponding child view needs to be marked
			 * as visible. We do this by toggling the 'hidden' class attribute of the corresponding variables.
			 */
			navTabs.children().each( function () {

				$( this ).on( 'click', function ( evt ) {

					evt.preventDefault();

					// If this tab is not active...
					if ( !$( this ).hasClass( 'nav-tab-active' ) ) {

						// Unmark the current tab and mark the new one as active
						var old_tab_object = $( '.nav-tab-active' );

						old_tab_object.removeClass( 'nav-tab-active' );

						$( this ).addClass( 'nav-tab-active' );

						// Save the index of the tab that's just been marked as active. It will be 0 - 3.
						tabIndex = $( this ).index();

						// Hide the old active content
						$( '#gfchart-view-configuration-tabs' )
							.children( 'div:not( .inside.hidden )' )
							.addClass( 'hidden' );

						$( '#gfchart-view-configuration-tabs' )
							.children( 'div:nth-child(' + ( tabIndex ) + ')' )
							.addClass( 'hidden' );

						// And display the new content
						$( '#gfchart-view-configuration-tabs' )
							.children( 'div:nth-child( ' + ( tabIndex + 2 ) + ')' )
							.removeClass( 'hidden' );

						var old_tab = old_tab_object.prop( 'href' ).split( '#' ).pop();

						var new_tab = $( this ).prop( 'href' ).split( '#' ).pop();

						$( document ).trigger( 'gfchart_config_switch_tab', [new_tab, old_tab] );

					}


				} );
			} );

		} );

	})( jQuery );
</script>
