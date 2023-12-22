<?php
/*
 * @package   GFChart\GFChart_CPT
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GFChart_CPT
 *
 * Handles CPT
 *
 * @since  0.6
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_CPT {

	/**
	 * Pages that have a GFChart shortcode
	 *
	 * @since  1.3.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @var array
	 */
	private $pages = array();


	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'init', array( $this, 'init' ) );

		add_action( 'in_admin_header', array( $this, 'in_admin_header' ) );

		add_action( 'wp_ajax_gf_save_new_chart', array( $this, 'save_new_chart' ) );

		add_action( 'wp_ajax_gf_dynamically_save_chart_config', array( $this, 'dynamically_save_chart_config' ) );

		add_action( 'wp_ajax_gf_get_preview_chart_data', array( $this, 'get_preview_chart_data' ) );

		add_filter( 'edit_gfchart_per_page', array( $this, 'edit_gfchart_per_page' ), 10, 2 );

		add_filter( 'manage_gfchart_posts_columns', array( $this, 'manage_gfchart_posts_columns' ) );

		add_action( 'manage_gfchart_posts_custom_column', array( $this, 'manage_gfchart_posts_custom_column' ), 10, 2 );

		add_action( 'save_post', array( $this, 'save_post' ) );

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

		add_action( 'admin_action_clone_gfchart', array( $this, 'clone_gfchart' ) );

		add_filter( 'admin_url', array( $this, 'admin_url' ), 10, 2 );

		add_filter( 'bulk_actions-edit-gfchart', array( $this, 'bulk_actions_edit_gfchart' ) );

		add_filter( 'screen_options_show_screen', array( $this, 'screen_options_show_screen' ), 10, 2 );

	}

	public function admin_url( $url, $path ) {

		if ( 'post-new.php?post_type=gfchart' != $path ) {

			return $url;
		}

		global $current_screen;

		if ( 'gfchart' != $current_screen->post_type ) {

			return $url;
		}


		return '';
	}

	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function admin_enqueue_scripts() {

		global $current_screen;

		if ( 'gfchart' == $current_screen->post_type ) {

			add_thickbox();

			wp_enqueue_style( 'gform_admin', GFCommon::get_base_url() . '/css/admin.css' );

			wp_enqueue_style( 'gfchart-admin-style', GFCHART_URL . '/css/admin.css', array( 'gform_admin','gform_font_awesome' ), GFCHART_CURRENT_VERSION );

			wp_register_script( 'gform_json', GFCommon::get_base_url() . '/js/jquery.json.js', array( 'jquery' ), GFForms::$version, false );

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'gfchart-admin-js', GFCHART_URL . "/js/admin{$suffix}.js", array(
				'jquery',
				'thickbox',
				'gform_json'
			), GFCHART_CURRENT_VERSION, false );

			$spinner_url = GFCommon::get_base_url() . '/images/spinner.svg';

			wp_localize_script( 'gfchart-admin-js', 'gfchart_admin_js', array(
				'nonce'   => wp_create_nonce( 'gf_save_new_chart' ),
				'spinner' => $spinner_url
			) );

			global $post;

			if ( ! empty( $post->ID ) ) {

				wp_enqueue_script( 'gform_field_filter', array( 'gform_forms' ) );

				wp_enqueue_script( 'gfchart_google_jsapi', 'https://www.google.com/jsapi', array( 'jquery' ), GFCHART_CURRENT_VERSION, true );

				wp_enqueue_script( 'gfchart-draw', GFCHART_URL . "/js/gfchart-draw{$suffix}.js", array(
					'jquery',
					'gfchart_google_jsapi'
				), GFCHART_CURRENT_VERSION, true );

				wp_enqueue_script( 'gfchart-admin-chart-config-js', GFCHART_URL . "/js/admin-chart-config{$suffix}.js", array(
					'jquery',
					'jquery-ui-datepicker',
					'gfchart_google_jsapi',
					'gfchart-draw'
				), GFCHART_CURRENT_VERSION, true );

				global $post;

				$filter_settings = $this->get_field_filters( $post->ID );

				$chart_type_ids = array();

				$chart_types = self::get_chart_types();

				foreach ( $chart_types as $type ) {

					$chart_type_ids[] = $type['id'];

				}

				wp_localize_script( 'gfchart-admin-chart-config-js', 'gfchart_admin_config', array(
					'gformFieldFilters' => rgar( $filter_settings, 'field_filters', array() ),
					'gformInitFilter'   => rgar( $filter_settings, 'init_filter_vars', array() ),
					'loading_img'       => admin_url( 'images/loading.gif' ),
					'chart_types'       => $chart_type_ids,
					'publish_text'      => __( 'To display chart, copy the folllowing shortcode and paste it in your desired location.', 'gfchart' ),
				) );

			}

			/**
			 * Makes sure scripts are only enqueued on the GFChart post type pages
			 *
			 * @since  1.1.0
			 *
			 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
			 */
			do_action( 'gfchart_admin_enqueue_scripts' );

		}

	}

	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function init() {

		$this->register();

	}

	/**
	 * @since 1.6.0
	 */
	public function admin_init() {

		$this->add_caps();

	}

	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	private function register() {

		$post_type = 'gfchart';

		$labels = array(
			'name'                  => __( 'Charts/Calculations', 'gravityplus-software-delivery' ),
			'singular_name'         => __( 'Chart/Calculation', 'gravityplus-software-delivery' ),
			'edit_item'             => __( 'Edit Chart/Calculation', 'gravityplus-software-delivery' ),
			'view_item'             => __( 'View Chart/Calculation', 'gravityplus-software-delivery' ),
			'name'                  => __( 'Charts/Calculations', 'gfchart' ),
			'singular_name'         => __( 'Chart/Calculation', 'gfchart' ),
			'add_new_item'          => __( 'Add New Chart/Calculation', 'gfchart' ),
			'new_item'              => __( 'New Chart/Calculation', 'gfchart' ),
			'edit_item'             => __( 'Edit Chart/Calculation', 'gfchart' ),
			'view_item'             => __( 'View Chart/Calculation', 'gfchart' ),
			'all_items'             => __( 'All Charts/Calculations', 'gfchart' ),
			'search_items'          => __( 'Search Charts/Calculations', 'gfchart' ),
			'not_found'             => __( 'No charts/calculations found.', 'gfchart' ),
			'not_found_in_trash'    => __( 'No charts/calculations found in Trash.', 'gfchart' ),
			'filter_items_list'     => _x( 'Filter charts/calculations list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'gfchart' ),
			'items_list_navigation' => _x( 'Charts/Calculations list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'gfchart' ),
			'items_list'            => _x( 'Charts/Calculations list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'gfchart' ),
		);

		$options = array(
			'labels'               => $labels,
			'description'          => __( 'A GFChart CPT for storing chart/calculations settings', 'gfchart' ),
			'public'               => false,
			'exclude_from_search'  => true,
			'publicly_queryable'   => false,
			'show_ui'              => true,
			'show_in_nav_menus'    => false,
			'show_in_menu'         => false,
			'show_in_admin_bar'    => false,
			'hierarchical'         => false,
			'supports'             => array(
				'title',
			),
			'register_meta_box_cb' => array( $this, 'register_meta_box' ),
			'has_archive'          => false,
			'rewrite'              => false,
			'query_var'            => false,
			'can_export'           => true,
			'capability_type'      => 'gfchart',
			'map_meta_cap'         => true,
			'show_in_rest'         => true
		);

		register_post_type( $post_type, $options );

		/*$default_meta_args = array(
			'type'      => 'string',
			'description'    => '',
			'single'        => true,
			'show_in_rest'    => true,
		);

		register_meta( $post_type, '_gfchart_type', $default_meta_args );

		register_meta( $post_type, '_gfchart_config', $default_meta_args );

		register_meta( $post_type, 'source_form', $default_meta_args );

		register_meta( $post_type, '_gfchart_filters', $default_meta_args );*/
	}

	/**
	 * Add meta fields to WP REST API request
	 *
	 * @since  1.7.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function rest_api_init() {

		foreach ( array( '_gfchart_type', '_gfchart_config', 'source_form', '_gfchart_filters' ) as $field ) {

			register_rest_field( 'gfchart',
				$field,
				array(
					'get_callback'    => 'GFChart_API::get_rest_field',
					'update_callback' => null,
					'schema'          => null,
				)
			);
		}
	}

	/**
	 * Add capabilities to their respective roles if they don't already exist
	 *
	 * Inspired by GravityView
	 *
	 * @since  1.6.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function add_caps() {

		$wp_roles = wp_roles();

		if ( is_object( $wp_roles ) ) {

			$_use_db_backup = $wp_roles->use_db;

			/**
			 * When $use_db is true, add_cap() performs update_option() every time.
			 * We disable updating the database here, then re-enable it below.
			 */
			$wp_roles->use_db = false;

			$capabilities = self::all_caps();

			foreach ( $capabilities as $role_slug => $role_caps ) {

				foreach ( $role_caps as $cap ) {

					$wp_roles->add_cap( $role_slug, $cap );

				}

			}

			/**
			 * Update the option, as it does in add_cap when $use_db is true
			 *
			 * @see WP_Roles::add_cap() Original code
			 */
			update_option( $wp_roles->role_key, $wp_roles->roles );

			/**
			 * Restore previous $use_db setting
			 */
			$wp_roles->use_db = $_use_db_backup;
		}

	}

	/**
	 * Get an array of capabilities
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.6.0
	 *
	 */
	public static function all_caps() {

		$editor_caps = array(
			'edit_others_gfcharts',
			'read_private_gfcharts',
			'delete_private_gfcharts',
			'delete_others_gfcharts',
			'edit_private_gfcharts',
			'publish_gfcharts',
			'delete_published_gfcharts',
			'edit_published_gfcharts',
		);

		$contributor_caps = array(
			'edit_gfcharts',
			'delete_gfcharts'
		);


		$administrator = $editor = array_merge( $editor_caps, $contributor_caps );

		$contributor = $contributor_caps;


		return compact( 'administrator', 'editor', 'contributor' );
	}

	/**
	 * Get pages before columns are output
	 *
	 * @param $per_page
	 *
	 * @return int
	 * @since  1.3.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function edit_gfchart_per_page( $per_page ) {

		$this->get_pages_with_shortcode();

		return $per_page;
	}

	/**
	 * @param $posts_columns
	 *
	 * @return mixed
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function manage_gfchart_posts_columns( $posts_columns ) {

		unset( $posts_columns['date'] );

		$posts_columns['chart-type'] = __( 'Type', 'gfchart' );
		$posts_columns['form']       = __( 'Form', 'gfchart' );
		$posts_columns['shortcode']  = __( 'Shortcode', 'gfchart' );
		$posts_columns['page']       = __( 'Page', 'gfchart' );

		return $posts_columns;
	}

	/**
	 * @param $column_name
	 * @param $post_id
	 *
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function manage_gfchart_posts_custom_column( $column_name, $post_id ) {

		switch ( $column_name ) {

			case 'id':

				echo $post_id;

				break;

			case 'chart-type':

				$chart_icon = '';

				$chart_type = get_post_meta( $post_id, '_gfchart_type', true );

				if ( empty( $chart_type ) ) {

					$gfchart_config = get_post_meta( $post_id, '_gfchart_config', true );

					$chart_type = empty( $gfchart_config['chart_type'] ) ? '' : $gfchart_config['chart_type'];

					update_post_meta( $post_id, '_gfchart_type', $chart_type );

				}

				if ( ! empty( $chart_type ) ) {

					$chart_type_settings = GFChart_API::get_chart_type_settings( $chart_type );

					if ( ! empty( $chart_type_settings['icon_element'] ) ) {

						$chart_icon = "<span class='gforms_edit_form' style='font-weight:700;font-size:15px;'><{$chart_type_settings[ 'icon_element' ]} class='{$chart_type_settings[ 'icon_class' ]}'></{$chart_type_settings[ 'icon_element' ]}></span>";

					} else {

						$chart_icon = "<span class=\"dashicons dashicons-admin-generic\"></span>";

					}

				}

				echo $chart_icon;

				break;

			case 'form':

				$form_id = get_post_meta( $post_id, 'source_form', true );
				$form    = GFAPI::get_form( $form_id );

				if ( $form ) {

					echo $form['title'];

				}

				break;

			case 'shortcode':

				$shortcode = "[gfchart id=\"{$post_id}\"]";

				echo '<input type="text" class="gfchart_shortcode" value="' . esc_attr( $shortcode ) . '" readonly="readonly" onfocus="jQuery(this).select();" onclick="jQuery(this).select();" />';

				break;

			case 'page':

				$this->get_page_column_value( $post_id );

				break;
		}
	}


	/**
	 * Output page(s) that contain chart
	 *
	 * @param $chart_id
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.3.0
	 *
	 */
	private function get_page_column_value( $chart_id ) {

		$num_of_page_links = 0;

		foreach ( $this->pages as $page ) {

			if ( $page['chart_id'] == $chart_id ) {

				if ( 0 < $num_of_page_links ) {

					echo '<br />';

				}

				echo "<b><a class='row_title' title='{$page['name']}' href='{$page['edit_url']}'>{$page['name']}</a></b> <div class='row-actions'> <span class='edit-page-link'><a title='" . __( 'Edit Page', 'gfchart' ) . "' href='{$page['edit_url']}' target='_blank'>" . __( 'Edit', 'gfchart' ) . "</a> | </span><span class='view-page-link'><a title='" . __( 'View Page', 'gfchart' ) . "' href='{$page['view_url']}' target='_blank'>" . __( 'View', 'gfchart' ) . "</a> </span> </div>";

				$num_of_page_links ++;

			}

		}
	}

	/**
	 * Get all of the pages that have a GFChart shortcode
	 *
	 * @since  1.3.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	private function get_pages_with_shortcode() {

		$query = new WP_Query( array( 's' => '[gfchart', 'nopaging' => true ) );

		if ( $query->have_posts() ) {

			global $post;

			while ( $query->have_posts() ) {

				$query->the_post();

				$shortcode_instances = preg_match_all( "/(\[gfchart)(.*)(id=\")([0-9]*)(\".*)(\])/", $post->post_content, $matches );

				if ( ! empty( $shortcode_instances ) ) {

					foreach ( $matches[4] as $match ) {

						if ( empty( $this->pages ) ) {

							$this->pages[] = array(
								'chart_id' => $match,
								'name'     => $post->post_title,
								'edit_url' => get_edit_post_link(),
								'view_url' => get_permalink()
							);

						} else {

							$multiple_shortcodes_for_same_chart = false;

							foreach ( $this->pages as $page ) {

								if ( $page['chart_id'] == $match && $post->post_title == $page['name'] ) {

									$multiple_shortcodes_for_same_chart = true;

									break;

								}

							}

							if ( ! $multiple_shortcodes_for_same_chart ) {

								$this->pages[] = array(
									'chart_id' => $match,
									'name'     => $post->post_title,
									'edit_url' => get_edit_post_link(),
									'view_url' => get_permalink()
								);

							}

						}

					}

				}

			}

		}

	}

	/**
	 * Add Clone option to row actions and remove quick edit
	 *
	 * @param $actions
	 * @param $post
	 *
	 * @return mixed
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.8.0
	 *
	 */
	public function post_row_actions( $actions, $post ) {

		if ( 'gfchart' == $post->post_type ) {

			unset( $actions['inline hide-if-no-js'] );

			if ( current_user_can( 'edit_gfchart', $post->ID ) ) {

				$action = "?action=clone_gfchart&post={$post->ID}";

				$clone_link = admin_url( 'admin.php' . $action );

				$actions['clone'] = sprintf( '<a href="%s" aria-label="%s">%s</a>', $clone_link, esc_attr( sprintf( __( 'Clone &#8220;%s&#8221;' ), $post->post_title ) ), __( 'Clone' ) );

			}

		}

		return $actions;
	}

	/**
	 * Remove bulk edit. Credit: jakejackson1
	 *
	 * @param array $actions
	 *
	 * @return array
	 *
	 * @since 1.17.0
	 *
	 */
	function bulk_actions_edit_gfchart( $actions ) {

		unset( $actions['edit'] );


		return $actions;
	}

	/**
	 * Remove screen options. Credit: jakejackson1
	 *
	 * @param bool      $show_screen
	 * @param WP_Screen $wp_screen
	 *
	 * @return bool
	 * @since 1.17.0
	 *
	 */
	function screen_options_show_screen( $show_screen, $wp_screen ) {

		if ( 'gfchart' == $wp_screen->post_type ) {

			return false;

		}


		return $show_screen;
	}

	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function in_admin_header() {

		include( trailingslashit( GFCHART_PATH ) . 'includes/views/add-new-chart-modal.php' );

	}

	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public static function save_new_chart() {

		if ( ( ! check_admin_referer( 'gf_save_new_chart', 'gf_save_new_chart' ) ) || ( ! current_user_can( 'edit_gfcharts' ) ) ) {

			wp_send_json_error( __( 'There was an issue creating your chart.', 'gfchart' ) );
		}


		$chart_json = rgpost( 'chart' );

		$chart = json_decode( $chart_json, true );

		if ( empty( $chart['title'] ) ) {

			wp_send_json_error( __( 'Please enter a chart title.', 'gfchart' ) );

		}

		$post = array(
			'post_title'  => wp_strip_all_tags( $chart['title'] ),
			'post_status' => 'publish',
			'post_type'   => 'gfchart',
			'post_author' => get_current_user_id()
		);

		$post_id = wp_insert_post( $post );

		if ( is_int( $post_id ) && $post_id > 0 ) {

			add_post_meta( $post_id, 'source_form', $chart['source_form'] );

			wp_send_json_success( array( 'redirect' => admin_url( "post.php?post={$post_id}&action=edit" ) ) );

		} else {

			wp_send_json_error( __( 'There was an issue creating your chart.', 'gfchart' ) );

		}

	}

	/**
	 * Clone chart
	 *
	 * @since  1.8.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function clone_gfchart() {

		$post_id = rgget( 'post' );

		if ( empty( $post_id ) ) {

			wp_die( __( 'No chart ID', 'gfchart' ) );

		}

		if ( ! current_user_can( 'edit_gfchart', $post_id ) ) {

			wp_die( __( 'You don\'t have permission to clone this chart.', 'gfchart' ) );

		}

		$post = get_post( $post_id );

		if ( ! is_a( $post, 'WP_Post' ) ) {

			wp_die( sprintf( __( 'Unable to find chart with ID #%d' ), 'gfchart' ), $post_id );

		} else {

			$new_post = array(
				'post_title'  => $post->post_title,
				'post_status' => $post->post_status,
				'post_type'   => $post->post_type,
				'post_author' => get_current_user_id(),
				'meta_input'  => array(
					'source_form'      => get_post_meta( $post_id, 'source_form', true ),
					'_gfchart_type'    => get_post_meta( $post_id, '_gfchart_type', true ),
					'_gfchart_config'  => get_post_meta( $post_id, '_gfchart_config', true ),
					'_gfchart_filters' => get_post_meta( $post_id, '_gfchart_filters', true )
				)
			);

			$new_post_id = wp_insert_post( $new_post );

			if ( is_int( $new_post_id ) && $new_post_id > 0 ) {

				wp_redirect( admin_url( 'edit.php?post_type=' . $post->post_type ) );

				exit;

			} else {

				wp_die( __( 'Chart clone failed', 'gfchart' ) );
			}

		}

	}

	/**
	 * @param $post
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.6
	 *
	 */
	public function register_meta_box( $post ) {

		add_meta_box( 'gfchart_config', __( 'GFChart Configuration', 'gfchart' ), array(
			$this,
			'gfchart_configuration_metabox'
		), 'gfchart', 'normal', 'high' );

	}

	/**
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.1.0
	 *
	 */
	public static function get_chart_types() {

		global $gfp_gfchart;

		$default_chart_types = array(
			array(
				'id'                   => 'pie',
				'label'                => __( 'Pie', 'gfchart' ),
				'icon_element'         => 'span',
				'icon_class'           => 'dashicons dashicons-chart-pie',
				'data_retriever'       => array( $gfp_gfchart->get_data_object(), 'get_pie_chart_data' ),
				'data_formatter'       => array( 'GFChart_API', 'format_pie_chart_data' ),
				'format_chart_options' => array( 'GFChart_API', 'format_pie_chart_options' )
			),
			array(
				'id'                   => 'bar',
				'label'                => __( 'Bar', 'gfchart' ),
				'icon_element'         => 'span',
				'icon_class'           => 'dashicons dashicons-chart-bar',
				'data_retriever'       => array( $gfp_gfchart->get_data_object(), 'get_bar_chart_data' ),
				'data_formatter'       => array( 'GFChart_API', 'format_bar_chart_data' ),
				'format_chart_options' => array( 'GFChart_API', 'format_bar_chart_options' )
			),
			array(
				'id'                   => 'calc',
				'label'                => __( 'Calculation', 'gfchart' ),
				'icon_element'         => 'i',
				'icon_class'           => 'fa fa-calculator fa-5-half-x icon-chart-calc',
				'data_retriever'       => array( $gfp_gfchart->get_data_object(), 'get_calc_chart_data' ),
				'data_formatter'       => array( 'GFChart_API', 'format_calc_chart_data' ),
				'format_chart_options' => array( 'GFChart_API', 'format_calc_chart_options' )
			),
			array(
				'id'                   => 'progressbar',
				'label'                => __( 'Progress Bar', 'gfchart' ),
				'icon_element'         => 'i',
				'icon_class'           => 'fa fa-battery-3 fa-5-half-x icon-chart-progressbar',
				'data_retriever'       => array( $gfp_gfchart->get_data_object(), 'get_progressbar_chart_data' ),
				'data_formatter'       => false,
				'format_chart_options' => array( 'GFChart_API', 'format_progressbar_chart_options' )
			)
		);

		return array_merge( $default_chart_types, apply_filters( 'gfchart_chart_types', array() ) );

	}

	/**
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.1.0
	 *
	 */
	public function get_config_tabs() {

		$default_config_tabs = array(
			array( 'id' => 'design', 'label' => __( 'Design', 'gfchart' ) ),
			array( 'id' => 'select-data', 'label' => __( 'Select data', 'gfchart' ) ),
			array( 'id' => 'customiser', 'label' => __( 'Customiser', 'gfchart' ) )
		);

		$last_config_tab = array( array( 'id' => 'preview', 'label' => __( 'Preview', 'gfchart' ) ) );

		return array_merge( $default_config_tabs, apply_filters( 'gfchart_config_tabs', array() ), $last_config_tab );
	}

	/**
	 * Retrieve a template part.
	 *
	 * Inspired by EDD & Gamajo_Template_Loader
	 *
	 * @param string $name Optional. Default null.
	 * @param bool   $load Optional. Default true.
	 *
	 * @return string
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.1.0
	 *
	 */
	public function get_config_section_view( $name, $load = true ) {

		return $this->locate_config_section_view( "{$name}.php", $load, false );
	}

	/**
	 * Retrieve the name of the highest priority template file that exists.
	 *
	 * @param string $name         Template file(s) to search for, in order.
	 * @param bool   $load         If true the template file will be loaded if it is found.
	 * @param bool   $require_once Whether to require_once or require. Default true.
	 *                             Has no effect if $load is false.
	 *
	 * @return string The template filename if one is located.
	 * @since  1.1.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function locate_config_section_view( $name, $load = false, $require_once = true ) {
		// No file found yet
		$located = false;

		// Remove empty entries
		$template_paths = $this->get_config_file_paths();

		// Try to find a template file
		// Trim off any slashes from the template name
		$view_name = ltrim( $name, '/' );

		// Try locating this template file by looping through the template paths
		foreach ( $template_paths as $template_path ) {

			if ( file_exists( $template_path . $view_name ) ) {

				$located = $template_path . $view_name;

				break;

			}

		}

		if ( $load && $located ) {

			load_template( $located, $require_once );

		}

		return $located;
	}

	/**
	 * Get paths to configuration files
	 *
	 * @return string
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.1.0
	 *
	 */
	public function get_config_file_paths() {

		$default_path = GFCHART_PATH . "includes/views/config-sections/";

		/**
		 * Allow ordered list of template paths to be amended.
		 *
		 * @since 1.1.0
		 *
		 */
		return array_merge( array( $default_path ), apply_filters( 'gfchart_config_file_paths', array() ) );

	}

	/**
	 * @param $post
	 * @param $metabox
	 *
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function gfchart_configuration_metabox( $post, $metabox ) {

		$gfchart_config = get_post_meta( $post->ID, '_gfchart_config', true );


		$chart_types = self::get_chart_types();

		$config_tabs = $this->get_config_tabs();


		if ( ! empty( $gfchart_config['chart_type'] ) ) {

			$only_show_chart_type_settings = true;

			$chart_type_settings = GFChart_API::get_chart_type_settings( $gfchart_config['chart_type'] );

		}

		$source_form_id = get_post_meta( $post->ID, 'source_form', true );

		$source_form = GFAPI::get_form( $source_form_id );

		$form_fields = $this->get_form_fields( $source_form );


		include( trailingslashit( GFCHART_PATH ) . 'includes/views/gfchart-configuration-metabox.php' );

	}

	/**
	 * @param $post_id
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.6
	 *
	 */
	public function save_post( $post_id ) {

		if ( empty( $_POST['gfchart_config_nonce'] ) ) {

			return;

		}

		if ( ! wp_verify_nonce( $_POST['gfchart_config_nonce'], 'gfchart_config' ) ) {

			return;

		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {

			return;

		}

		if ( ! isset( $_POST['post_type'] ) ) {

			return;
		}

		if ( 'gfchart' !== $_POST['post_type'] ) {

			return;

		}

		if ( ! current_user_can( 'edit_gfchart', $post_id ) ) {

			return;

		}


		$gfchart_config = empty( $_POST['gfchart_config'] ) ? false : $_POST['gfchart_config'];

		$this->save_chart_config( $gfchart_config, $post_id );

	}

	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function dynamically_save_chart_config() {

		$data = rgpost( 'data' );

		if ( empty( $data ) ) {

			wp_send_json_error( 'Unable to save chart configuration. No data sent' );
		}

		parse_str( $data, $data );

		$_POST = $_POST + $data;

		if ( empty( $data['gfchart_config_nonce'] ) ) {

			wp_send_json_error( 'Unable to save chart configuration. No permissions.' );

		}

		if ( ! wp_verify_nonce( $data['gfchart_config_nonce'], 'gfchart_config' ) ) {

			wp_send_json_error( 'Unable to save chart configuration. No permissions.' );

		}

		if ( ! isset( $data['post_type'] ) ) {

			return;

		}

		if ( 'gfchart' !== $data['post_type'] ) {

			return;

		}

		if ( ! current_user_can( 'edit_gfchart', $data['post_ID'] ) ) {

			wp_send_json_error( 'Unable to save chart configuration. No permissions.' );

		}

		$gfchart_config = empty( $data['gfchart_config'] ) ? false : $data['gfchart_config'];

		$this->save_chart_config( $gfchart_config, $data['post_ID'] );

		wp_update_post( array(
			'ID'         => (int) $data['post_ID'],
			'post_title' => wp_strip_all_tags( $data['post_title'] ),
		) );

		wp_send_json_success();
	}

	/**
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function get_preview_chart_data() {

		global $gfp_gfchart;

		$post_id = rgpost( 'post' );

		if ( empty( $post_id ) ) {

			wp_send_json_error( __( 'Unable to retrieve data. No chart ID given' ) );

			return;
		}

		if (
			empty( $_POST['gfchart_config_nonce'] ) ||
			! wp_verify_nonce( $_POST['gfchart_config_nonce'], 'gfchart_config' ) ||
			! current_user_can( 'edit_gfchart', $post_id )
		) {
			wp_send_json_error( __( 'There was an issue creating your chart.', 'gfchart' ) );
		}

		$gfchart_config = get_post_meta( $post_id, '_gfchart_config', true );

		$source_form_id = get_post_meta( $post_id, 'source_form', true );

		$filters = GFChart_API::get_gfchart_filter_vars( $post_id, true );

		$data = array();

		$chart_type_settings = GFChart_API::get_chart_type_settings( $gfchart_config['chart_type'] );


		if ( is_callable( $chart_type_settings['data_retriever'] ) ) {

			$data = call_user_func( $chart_type_settings['data_retriever'], $gfchart_config, $source_form_id, $filters );

		}

		if ( empty( $data ) || ! is_array( $data ) ) {

			wp_send_json_error( $data );

		}

		if ( is_callable( $chart_type_settings['data_formatter'] ) ) {

			$data = call_user_func( $chart_type_settings['data_formatter'], $data, $gfchart_config );

		}

		$chart_options = GFChart_API::format_chart_options( $gfchart_config );

		wp_send_json_success( array( 'chart_data' => $data, 'chart_options' => $chart_options ) );
	}

	/**
	 * Save chart config to post
	 *
	 * @param $config_data
	 * @param $post_id
	 *
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	private function save_chart_config( $config_data, $post_id ) {

		if ( empty( $config_data ) ) {

			delete_post_meta( $post_id, '_gfchart_config' );

			return;
		}

		foreach ( $config_data as $key => $detail ) {

			if ( is_array( $detail ) ) {

				continue;
			}

			$config_data[ $key ] = sanitize_text_field( $detail );

		}

		$config_data = apply_filters( 'gfchart_save_chart_config', $config_data, $post_id );

		update_post_meta( $post_id, '_gfchart_config', $config_data );

		update_post_meta( $post_id, '_gfchart_type', $config_data['chart_type'] );


		$source_form_id = get_post_meta( $post_id, 'source_form', true );

		$source_form = GFAPI::get_form( $source_form_id );

		$filters = GFCommon::get_field_filters_from_post( $source_form );


		update_post_meta( $post_id, '_gfchart_filters', $filters );

	}

	/**
	 * @param $form
	 *
	 * @return array
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	private function get_form_fields( $form ) {

		$fields = array();

		if ( is_array( $form['fields'] ) ) {

			foreach ( $form['fields'] as $field ) {

				$input_type = $field->get_input_type();

				$inputs = $field->get_entry_inputs();

				if ( is_array( $inputs ) ) {

					if ( $input_type == 'checkbox' ) {

						$fields[] = array(
							$field->id,
							GFCommon::get_label( $field ) . ' (' . esc_html__( 'Selected', 'gravityforms' ) . ')'
						);

					}

					foreach ( $inputs as $input ) {

						$fields[] = array( $input['id'], GFCommon::get_label( $field, $input['id'] ) );

					}

				} else if ( ! rgar( $field, 'displayOnly' ) ) {

					$fields[] = array( $field['id'], GFCommon::get_label( $field ) );

				}

			}

		}

		return $fields;
	}

	/**
	 * Get user role choices for filters
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.17.0
	 *
	 */
	private function get_role_choices() {

		$role_choices = array();

		$editable_roles = array_reverse( get_editable_roles() );

		foreach ( $editable_roles as $role => $details ) {

			$role_choices[] = array( 'text' => $details['name'], 'value' => $role );

		}


		return $role_choices;

	}

	/**
	 * Get GravityView approval status choices for filters
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.17.0
	 *
	 */
	private function get_gravityview_approval_status_choices() {

		$approval_status_choices = array();

		$approval_statuses = GravityView_Entry_Approval_Status::get_all();

		foreach ( $approval_statuses as $status_info ) {

			$approval_status_choices[] = array( 'text' => $status_info['label'], 'value' => $status_info['value'] );

		}


		return $approval_status_choices;

	}

	/**
	 * @param $post_id
	 *
	 * @return array|void
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	private function get_field_filters( $post_id ) {

		$source_form_id = get_post_meta( $post_id, 'source_form', true );

		$source_form = GFAPI::get_form( $source_form_id );

		if ( empty( $source_form ) ) {

			return;

		}

		$field_filters = GFCommon::get_field_filter_settings( $source_form );


		foreach ( $field_filters as $key => $filter_info ) {

			if ( in_array( $filter_info['key'], array( 'workflow_final_status', 'workflow_step', 'created_by' ) ) ) {


				if ( empty( $filter_info['values'] ) ) {

					unset( $field_filters[ $key ] );

					continue;
				}

				if ( 'workflow_final_status' == $filter_info['key'] ) {

					$field_filters[ $key ]['text'] = esc_html__( 'Workflow Status', 'gfchart' );
				}

				if ( 'created_by' == $filter_info['key'] ) {

					$field_filters[ $key ]['text'] = esc_html__( 'Created By User', 'gfchart' );
				}

				continue;
			}

			if ( '0' == $filter_info['key'] || ! is_numeric( $filter_info['key'] ) ) {

				unset( $field_filters[ $key ] );

				continue;
			}

			if ( 'date' == GFAPI::get_field( $source_form, $filter_info['key'] )->type ) {

				unset( $field_filters[ $key ] );

			}
		}

		$field_filters = array_values( $field_filters );

		$field_filters[] =
			array(
				'key'       => 'created_by_user_role',
				'text'      => esc_html__( 'Created By User Role', 'gfchart' ),
				'operators' => array( 'is', 'isnot' ),
				'values'    => $this->get_role_choices()
			);

		if ( class_exists('GravityView_Plugin') ) {

			$field_filters[] =
				array(
					'key'       => 'is_approved',
					'text'      => esc_html__( 'Approval Status', 'gfchart' ),
					'operators' => array( 'is', 'isnot' ),
					'values'    => $this->get_gravityview_approval_status_choices()
				);

		}

		$init_field_id            = 0;
		$init_field_operator      = "is";
		$default_init_filter_vars = array(
			"mode"    => "all",
			"filters" => array(
				array(
					"field"    => $init_field_id,
					"operator" => $init_field_operator,
					"value"    => ''
				)
			)
		);

		$view_filter_vars = GFChart_API::get_gfchart_filter_vars( $post_id, true );

		$init_filter_vars = ! empty( $view_filter_vars ) ? $view_filter_vars : $default_init_filter_vars;


		return array( 'field_filters' => $field_filters, 'init_filter_vars' => $init_filter_vars );

	}

}