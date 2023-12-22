<?php
/*
 * @package   GFChart\GFChart_Addon
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.53
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Class GFChart_Addon
 *
 * Gravity Forms Add-On Framework settings
 *
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_Addon extends GFAddOn {

	/**
	 * @var string Version number of the Add-On
	 */
	protected $_version;
	/**
	 * @var string Gravity Forms minimum version requirement
	 */
	protected $_min_gravityforms_version;
	/**
	 * @var string URL-friendly identifier used for form settings, add-on settings, text domain localization...
	 */
	protected $_slug;
	/**
	 * @var string Relative path to the plugin from the plugins folder
	 */
	protected $_path;
	/**
	 * @var string Full path to the plugin. Example: __FILE__
	 */
	protected $_full_path;
	/**
	 * @var string URL to the App website.
	 */
	protected $_url;
	/**
	 * @var string Title of the plugin to be used on the settings page, form settings and plugins page.
	 */
	protected $_title;
	/**
	 * @var string Short version of the plugin title to be used on menus and other places where a less verbose string is useful.
	 */
	protected $_short_title;
	/**
	 * @var array Members plugin integration. List of capabilities to add to roles.
	 */
	protected $_capabilities = array();

	// ------------ Permissions -----------
	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the settings page
	 */
	protected $_capabilities_settings_page = array();

	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the plugin page
	 */
	protected $_capabilities_plugin_page = array();
	/**
	 * @var string|array A string or an array of capabilities or roles that can uninstall the plugin
	 */
	protected $_capabilities_uninstall = array();

	function __construct( $args ) {

		$this->_version                    = $args[ 'version' ];
		$this->_slug                       = $args[ 'plugin_slug' ];
		$this->_min_gravityforms_version   = $args[ 'min_gf_version' ];
		$this->_path                       = $args[ 'path' ];
		$this->_full_path                  = $args[ 'full_path' ];
		$this->_url                        = $args[ 'url' ];
		$this->_title                      = $args[ 'title' ];
		$this->_short_title                = $args[ 'short_title' ];
		$this->_capabilities               = $args[ 'capabilities' ];
		$this->_capabilities_settings_page = $args[ 'capabilities_settings_page' ];
		$this->_capabilities_plugin_page   = $args[ 'capabilities_plugin_page' ];
		$this->_capabilities_uninstall     = $args[ 'capabilities_uninstall' ];

		parent::__construct();

	}

	public function init_admin() {

		$license_key = trim( $this->get_plugin_setting( 'license_key' ) );

		if ( ! empty( $license_key ) ) {

			$edd_updater = new GFChart_EDD_SL_Plugin_Updater( GFCHART_EDD_STORE_URL, GFCHART_FILE, array(
				                                                               'version'   => GFCHART_CURRENT_VERSION,
				                                                               'license'   => $license_key,
				                                                               'item_id' => GFCHART_EDD_ITEM_ID,
				                                                               'author'    => 'Mensard with gravity+',
				                                                               'url'     => home_url()
			                                                               )
			);

		}

		add_filter( 'parent_file', array( $this, 'parent_file' ) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 30, 0 );


		parent::init_admin();

	}


	public function get_menu_icon() {

		return GFCHART_URL . 'images/icon.svg';
	}


	public function plugin_settings_fields() {

		$settings_fields = $fields = array();

		if( version_compare( GFForms::$version, '2.5-rc-1', '<' ) ) {

			$fields[] = array(
				'name'                => 'license_key',
				'tooltip'             => __( 'Enter your license key that was emailed with your purchase.', 'gfchart' ),
				'label'               => __( 'License Key', 'gfchart' ),
				'type'                => 'text',
				'validation_callback' => array( $this, 'validate_license_key' ),
				'feedback_callback'   => array( $this, 'check_license_key' ),
				'class'               => ( 'valid' == get_option( 'gfchart_license_status') ) ? 'deactivate' : 'activate',
			);

			$fields[] = array(
				'name' => 'clear-license-key',
				'type'     => 'button',
				'default_value'    => __( 'Clear', 'gfchart' ),
				'dependency' =>function(){
					return ( '' == $this->get_plugin_setting( 'license_key' ) ) ?  false : true;
				},
				'onclick' => "jQuery('#tab_GFChart').find('#license_key').val('');jQuery('#tab_GFChart').find('#gform-settings-save').click();",
				'messages' => array(
					'success' => __( 'License key updated', 'gfchart' )
				)
			);

		} else {

			$fields[] = $this->get_default_license_key_field( ( '' == $this->get_plugin_setting( 'license_key' ) ) ? 'activate' : 'deactivate');

			$fields[] = array(
				'name' => 'clear-license-key',
				'type'     => 'button',
				'value'    => __( 'Clear', 'gfchart' ),
				'dependency' =>function(){
					return ( '' == $this->get_plugin_setting( 'license_key' ) ) ?  false : true;
				},
				'callback' => array( $this, 'render_clear_button' ),
				'onclick' => "jQuery('#tab_GFChart').find('#license_key').val('');jQuery('#tab_GFChart').find('#gform-settings-save').click();",
				'messages' => array(
					'success' => __( 'License key cleared', 'gfchart' )
				)
			);

		}

        $fields[] = array(
	        'type'     => 'save',
	        'value'    => ( 'valid' == get_option( 'gfchart_license_status') ) ? __( 'Deactivate', 'gfchart' ) : __( 'Activate', 'gfchart' ),
	        'messages' => array(
		        'success' => ( 'valid' == get_option( 'gfchart_license_status') ) ? __( 'License key updated', 'gfchart' ) : ( ( '' == $this->get_plugin_setting( 'license_key' ) ) ?   __( 'License key activated', 'gfchart' ) : __( 'License key updated', 'gfchart' ))
	        )
        );


			$settings_fields[ ] = array(
			'title'       => __( 'License', 'gfchart' ),
			'description' => __( 'This provides you access to support and automatic updates', 'gfchart' ),
			'fields'      => $fields
		);


		return $settings_fields;
	}

	private function get_default_license_key_field( $class = 'activate') {

		return array(
			'name'                => 'license_key',
			'tooltip'             => __( 'Enter your license key that was emailed with your purchase.', 'gfchart' ),
			'label'               => __( 'License Key', 'gfchart' ),
			'type'                => 'text',
			'validation_callback' => array( $this, 'validate_license_key' ),
			'save_callback'       => array( $this, 'save_license_key'),
			'feedback_callback'   => array( $this, 'check_license_key' ),
			'class'               => $class,
		);
	}

	public function render_clear_button( $field, $echo ) {

		$field->value = __( 'Clear', 'gfchart' );

		if ( $echo ) {

			echo $field->markup();
		}

		return $field->markup();
	}

	public function save_license_key( $field, $field_value ) {

		$settings_renderer = $this->get_settings_renderer();

		if ( 'deactivate' == $field->class ) {

			$field_value = '';

			$settings_renderer->replace_field( 'license_key', $this->get_default_license_key_field() );

			$settings_renderer->set_postback_message_callback( function() { return __( 'License key cleared', 'gfchart'); } );

		}
		else {

			$settings_renderer->replace_field( 'license_key', $this->get_default_license_key_field('deactivate'));

			$settings_renderer->set_postback_message_callback( function( $message ) { return __( 'License key updated', 'gfchart'); } );
		}

		add_filter( 'gform_settings_save_button', array( $this, 'gform_settings_save_button' ), 10, 2 );


		return $field_value;
	}

	public function gform_settings_save_button( $html, $settings_renderer ) {

		if ( '' == $this->get_plugin_setting( 'license_key' ) ) {

			$html = str_replace( 'Deactivate', 'Activate', $html );
		}
		else {

			$html = str_replace( 'Activate', 'Deactivate', $html );
		}

		remove_filter( 'gform_settings_save_button', array( $this, 'gform_settings_save_button' ) );


		return $html;
	}



	public function settings_button( $field, $echo = true ) {

		$field['type']  = 'button';
		$field['class'] = 'button-secondary gfbutton';

		if ( ! rgar( $field, 'value' ) ) {
			$field['value'] = esc_html__( 'Update Settings', 'gravityforms' );
		}

		$attributes = $this->get_field_attributes( $field );

		$html = '<input
					type="' . esc_attr( $field['type'] ) . '"
					name="' . esc_attr( $field['name'] ) . '"
					value="' . esc_attr( $field['default_value'] ) . '" ' . implode( ' ', $attributes ) . ' />';

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Validate license key when settings are submitted
	 *
	 * @since  1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $field
	 * @param $field_setting
	 */
	public function validate_license_key( $field, $field_setting ) {

		delete_option( 'gfchart_license_status' );

		if ( ! empty( $field_setting ) && ( 'activate' == $field[ 'class' ] ) ) {

			$activated = $this->activate_license( $field_setting );

			$error = $this->get_activation_error( $activated );

			if ( ! empty( $error ) ) {

				$this->set_field_error( $field, $error );

			}
			else {

				update_option( 'gfchart_license_status', $activated->license );
			}

		} else if ( ! empty( $field_setting ) && ( 'deactivate' == $field[ 'class' ] ) ) {

			$deactivated = $this->deactivate_license( $field_setting );

			$error = $this->get_deactivation_error( $deactivated );

			if ( ! empty( $error ) ) {

				$this->set_field_error( $field, $error );

			}

		}
		
	}

	/**
	 * Check if license key is valid or not
	 *
	 * @since  1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $license_key
	 * @param $field
	 *
	 * @return bool
	 */
	public function check_license_key( $license_key, $field ) {

	    if ( empty( $license_key ) ) {

	        return false;
        }

		$license_check_result = $this->check_license( $license_key );

	    if ( 'valid' == $license_check_result ) {

	        return true;
        }

        $this->log_error( __METHOD__ . ' ' . $license_check_result  );

		return false;
	}


	/**
	 * Override Render uninstall but call the parent
	 *
	*/
	public function render_uninstall() {

		do_action( "gform_{$this->_slug}_render_uninstall", $this );

		echo "<span class='render-{$this->_slug}-uninstall'>";

		parent::render_uninstall();

		echo "</span>";

		echo "<script>

				jQuery(document).ready(function(){

					jQuery('span.render-{$this->_slug}-uninstall div.alert.error').addClass('inline');
					
				})
				
			</script>";

	}

	/**
	 * Activate GFChart license
	 *
	 * $license_data->license will be either "valid" or "invalid"
	 *
	 * @param $license
	 *
	 * @return bool
	 */
	private function activate_license( $license ) {

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( GFCHART_EDD_ITEM_NAME ),
			'item_id'    => GFCHART_EDD_ITEM_ID,
			'url'        => home_url()
		);

		$response = wp_remote_post( GFCHART_EDD_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			return false;

		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );


		return $license_data;

	}

	private function get_activation_error( $result ) {

		$activation_error = '';

		if ( empty( $result ) ) {

			$activation_error = __( 'Error receiving a response from license server', 'gfchart' );

		} 
		else if ( false === $result->success ) {

			switch( $result->error ) {

				case 'expired' :

					$activation_error = sprintf(
						__( 'Your license key expired on %s.', 'gfchart' ),
						date_i18n( get_option( 'date_format' ), strtotime( $result->expires, current_time( 'timestamp' ) ) )
					);
					
					break;

				case 'revoked' :

					$activation_error = __( 'Your license key has been disabled.', 'gfchart' );
					
					break;

				case 'missing' :

					$activation_error = __( 'Invalid license.', 'gfchart' );
					
					break;

				case 'invalid' :
				case 'site_inactive' :

					$activation_error = __( 'Your license is not active for this URL.', 'gfchart' );
					
					break;

				case 'item_name_mismatch' :

					$activation_error = sprintf( __( 'This appears to be an invalid license key for %s.', 'gfchart' ), GFCHART_EDD_ITEM_NAME );
					
					break;

				case 'no_activations_left':

					$activation_error = __( 'Your license key has reached its activation limit.', 'gfchart' );
					
					break;

				default :

					$activation_error = __( 'An error occurred, please try again.', 'gfchart' );
					
					break;
			
			}

		}

		return $activation_error;

	}

	private function get_deactivation_error( $result ) {

		$deactivation_error = '';

		if ( empty( $result ) ) {

			$deactivation_error = __( 'Error receiving a response from license server', 'gfchart' );

		} else if ( 'failed' == $result ) {

			$deactivation_error = __( 'Unable to deactivate key', 'gfchart' );

		} else if ( 'deactivated' !== $result ) {

			$deactivation_error = __( 'Unknown error', 'gfchart' );

		}

		return $deactivation_error;
	}

	/**
	 * Deactivate GFChart license
	 *
	 * $license_data->license will be either "deactivated" or "failed"
	 *
	 * @since  1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $license
	 *
	 * @return bool
	 */
	private function deactivate_license( $license ) {

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( GFCHART_EDD_ITEM_NAME ),
			'item_id'    => GFCHART_EDD_ITEM_ID,
			'url'        => home_url()
		);

		$response = wp_remote_post( GFCHART_EDD_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );


		return $license_data->license;

	}

	/**
	 * Check whether license key is valid or not
	 *
	 * @since  1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $license
	 *
	 * @return string
	 */
	private function check_license( $license ) {

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode( GFCHART_EDD_ITEM_NAME ),
			'item_id'    => GFCHART_EDD_ITEM_ID,
			'url'        => home_url()
		);

		$response = wp_remote_post( GFCHART_EDD_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );


		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );


		return $license_data->license;

	}

	/**
	 * Override this function to create a custom plugin page
	 */
	public function plugin_page() {
	}

	/**
	 * Creates plugin page menu item
	 * Target of gform_addon_navigation filter. Creates a menu item in the left nav, linking to the plugin page
	 *
	 * @param $menus - Current list of menu items
	 *
	 * @return array - Returns a new list of menu items
	 */
	public function create_plugin_page_menu( $menus ) {

		$menus[ ] = array(
			'name'       => $this->_slug,
			'label'      => 'Charts/Calculations',
			'callback'   => '',
			'permission' => $this->_capabilities_plugin_page[0]
		);

		return $menus;
	}

	/**
	 * Correct submenu slug
	 *
	 * Gravity Forms does not provide the option to set the menu slug and there isn't another filter so we have to
	 * brute-force it. We could also register our page separate from the Gravity Forms add-on page registration, but want
	 * to stay plugged into the Gravity Forms registration system for now.
	 *
	 * We used to do this in the parent_file filter but it was incompatible with the Admin Menu Editor plugin so they suggested this hook instead
	 *
	 * @since  2.1.0
	 *
	 * @author Naomi C. Bush for gravity+
	 */
	public function admin_menu() {

		global $submenu;

		if( ! empty( $submenu['gf_edit_forms'] ) ) {
		
			foreach( $submenu['gf_edit_forms'] as $key=>$submenu_info ) {

				if ( 'GFChart' == $submenu_info[2] ) {

					$submenu['gf_edit_forms'][$key][2] = 'edit.php?post_type=gfchart';

				}

			}

		}

	}

	/**
	 * Make sure navigation stays open and menu item shows as active when on the page
	 *
	 * @since  1.0.0
	 *
	 * @author Naomi C. Bush for gravity+, with Jake Jackson
	 *
	 * @param string $file
	 *
	 * @return string
	 *
	 */
	public function parent_file( $file ) {

		global $post_type;

		if ( $post_type === 'gfchart' ) {

			return 'gf_edit_forms';

		}


		return $file;
	}

}