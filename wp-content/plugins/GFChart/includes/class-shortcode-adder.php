<?php
/*
 * @package   GFChart\GFChart_Shortcode_Adder
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GFChart_Shortcode_Adder
 *
 * Adds GFChart shortcode to WP content
 *
 * @since  0.6
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_Shortcode_Adder {

	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );

		add_action( 'print_media_templates', array( $this, 'action_print_media_templates' ) );

	}

	public function init() {

		if ( is_admin() && $this->page_supports_add_chart_button() ) {

			add_action( 'media_buttons', array( $this, 'add_chart_button' ), 20 );

			add_action( 'admin_footer', array( $this, 'admin_footer' ) );

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'gfchart-admin-style', GFCHART_URL . '/css/admin.css', array(), GFCHART_CURRENT_VERSION );

			wp_enqueue_script( 'gfchart-shortcode-ui', GFCHART_URL . "/js/shortcode-ui{$suffix}.js", array(
				'jquery',
				'wp-backbone'
			), GFCHART_CURRENT_VERSION, true );

			wp_localize_script( 'gfchart-shortcode-ui', 'gfchart_shortcode_ui', array(
				'shortcodes'     => $this->get_shortcodes(),
				'error_messages' => array(
					'no_chart_selected' => esc_html__( 'Please select a chart.', 'gfchart' ),
				)
			) );
		}

	}

	public function page_supports_add_chart_button() {

		$is_post_edit_page = in_array( RG_CURRENT_PAGE, array(
			'post.php',
			'page.php',
			'page-new.php',
			'post-new.php'
		) );

		$display_add_chart_button = apply_filters( 'gform_display_add_chart_button', $is_post_edit_page );

		return $display_add_chart_button;
	}

	private function get_shortcodes() {

		$charts = get_posts( array( 'numberposts' => -1, 'post_type' => 'gfchart' ) );

		$charts_options[ '' ] = __( 'Select a Chart/Calculation', 'gfchart' );

		foreach ( $charts as $chart ) {

			$charts_options[ absint( $chart->ID ) ] = esc_html( $chart->post_title );

		}

		$default_attrs = array(
			array(
				'label'       => '',
				'tooltip'     => '',
				'attr'        => 'id',
				'type'        => 'select',
				'section'     => 'required',
				'description' => __( "Can't find your chart/calculation? Make sure it is published.", 'gfchart' ),
				'options'     => $charts_options,
			),

		);

		$shortcode = array(
			'shortcode_tag' => 'gfchart',
			'action_tag'    => '',
			'label'         => 'GFChart',
			'attrs'         => $default_attrs,
		);

		$shortcodes[ ] = $shortcode;

		return $shortcodes;
	}

	/**
	 * Action target that adds the 'Insert Chart' button to the post/page edit screen
	 */
	public function add_chart_button() {

		$is_add_chart_page = $this->page_supports_add_chart_button();

		if ( ! $is_add_chart_page ) {

			return;

		}

		// display button matching new UI
		echo '<style>.gfchart_media_icon{
	                    background-position: center center;
					    background-repeat: no-repeat;
					    background-size: 16px auto;
					    float: left;
					    height: 16px;
					    margin: 0;
					    text-align: center;
					    width: 16px;
						padding-top:10px;
	                    }
	                    .gfchart_media_icon:before{
	                    color: #999;
					    padding: 7px 0;
					    transition: all 0.1s ease-in-out 0s;
	                    }
	                    .wp-core-ui a.gfchart_media_link{
	                     padding-left: 0.4em;
	                    }
	                 </style>
	                  <a href="#" class="button gfchart_media_link" id="add_gfchart" title="' . esc_attr__( 'Add GFChart', 'gfchart' ) . '"><div class="gfchart_media_icon svg" style="background-image: url(\'' . $this->get_svg_icon() . '\');"><br /></div><div style="padding-left: 20px;">' . esc_html__( 'Add Chart', 'gfchart' ) . '</div></a>';

	}

	private function get_svg_icon() {

		$data_uri = "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuNCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHdpZHRoPSI5Ni4xcHgiIGhlaWdodD0iNzkuNXB4IiB2aWV3Qm94PSIxNTguNyA5NC41IDk2LjEgNzkuNSIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAxNTguNyA5NC41IDk2LjEgNzkuNSIKCSB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGcgaWQ9IkxheWVyXzVfY29weSI+Cgk8Zz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMjQyLjcsMTczLjVIMTcxYy02LjQsMC0xMS41LTUuMi0xMS41LTExLjV2LTU1YzAtNi40LDUuMi0xMS41LDExLjUtMTEuNWg3MS43YzYuNCwwLDExLjUsNS4yLDExLjUsMTEuNQoJCQl2NTVDMjU0LjIsMTY4LjQsMjQ5LDE3My41LDI0Mi43LDE3My41eiBNMTcxLDEwMS41Yy0zLDAtNS41LDIuNS01LjUsNS41djU1YzAsMywyLjUsNS41LDUuNSw1LjVoNzEuN2MzLDAsNS41LTIuNSw1LjUtNS41di01NQoJCQljMC0zLTIuNS01LjUtNS41LTUuNUgxNzF6Ii8+Cgk8L2c+Cgk8cGF0aCBmaWxsPSIjMDBBREM5IiBkPSJNMjEyLjYsMTQ2LjljLTIuNiwxLjktNS43LDMuMS05LjIsMy4xYy04LjQsMC0xNS4yLTYuOC0xNS4yLTE1LjJjMC04LjQsNi44LTE1LjMsMTUuMi0xNS4zCgkJYzMuNCwwLDYuNiwxLjIsOS4yLDMuMWw3LjktNy45Yy00LjUtMy44LTEwLjQtNi0xNi43LTZjLTE0LjUsMC0yNi4yLDExLjgtMjYuMiwyNi4yYzAsMTQuNSwxMS44LDI2LjIsMjYuMiwyNi4yCgkJYzYuNCwwLDEyLjMtMi4zLDE2LjktNi4yTDIxMi42LDE0Ni45eiIvPgo8L2c+CjxnIGlkPSJMYXllcl82X2NvcHkiPgoJPGc+CgkJPHBhdGggZmlsbD0iIzAwQTg1MCIgZD0iTTIzOC44LDEzNS4xYzAtOC4zLTMuOS0xNS43LTkuOS0yMC41bC0yMC4yLDIwLjJsMjAuNiwyMC42QzIzNS4xLDE1MC41LDIzOC44LDE0My4yLDIzOC44LDEzNS4xeiIvPgoJPC9nPgo8L2c+Cjwvc3ZnPg==";
		$icon     = 'data:image/svg+xml;base64,' . $data_uri;

		return $icon;
	}

	public function admin_footer() {

		$this->add_mce_popup();
	}

	/**
	 * Action target that displays the popup to insert a chart to a post/page
	 */
	public function add_mce_popup() {
		?>
		<script>

			function InsertChart() {

				var chart_id = jQuery( "#add_chart_id" ).val();

				if ( chart_id == "" ) {
					alert( <?php echo json_encode( esc_html__( 'Please select a chart', 'gfchart' ) ); ?> );
					return;
				}

				window.send_to_editor( "[gfchart id=\"" + chart_id + "\"]" );
			}
		</script>

		<div id="select_gravity_chart" style="display:none;">

			<div id="gfchart-shortcode-ui-wrap" class="wrap <?php echo GFCommon::get_browser_class() ?>">

				<div id="gfchart-shortcode-ui-container"></div>

			</div>


		</div>

	<?php
	}

	public function action_print_media_templates() {

		echo $this->get_view( 'edit-shortcode-form' );
	}

	public function get_view( $template ) {

		if ( ! file_exists( $template ) ) {

			$template_dir = GFCHART_PATH . 'includes/views/templates/';
			$template     = $template_dir . $template . '.tpl.php';

			if ( ! file_exists( $template ) ) {
				return '';
			}
		}

		ob_start();
		include $template;

		return ob_get_clean();
	}

}