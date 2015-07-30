<?php
/*
Plugin Name: WooCommerce (ES)
Plugin URI: http://www.closemarketing.es/portafolio/plugin-woocommerce-espanol/
Description: Extends the WooCommerce plugin and add-ons with the Spanish language: <strong>WooCommerce</strong> 2.0.20 | <strong>WooCommerce EU VAT Number</strong> | <strong>WooCommerce Email Cart</strong> [Download](http://codecanyon.net/item/email-cart-for-woocommerce/5568059?ref=closemarketing) | Send Carts by Email to users | <strong>WooCommerce Product Enquiry Form</strong> | <strong>WooCommerce Shipping Table Rate</strong>

Version: 0.2
Requires at least: 3.0

Author: Closemarketing
Author URI: http://www.closemarketing.es/

Text Domain: woocommerce_es
Domain Path: /languages/

License: GPL
*/

class WooCommerceESPlugin {
	/**
	 * The current langauge
	 *
	 * @var string
	 */
	private $language;

	/**
	 * Flag for the spanish langauge, true if current langauge is spanish, false otherwise
	 *
	 * @var boolean
	 */
	private $is_spa;

	////////////////////////////////////////////////////////////

	/**
	 * Bootstrap
	 */
	public function __construct( $file ) {
		$this->file = $file;

		// Filters and actions
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		add_filter( 'load_textdomain_mofile', array( $this, 'load_mo_file' ), 10, 2 );

		/*
		 * WooThemes/WooCommerce don't execute the load_plugin_textdomain() in the 'init'
		 * action, therefor we have to make sure this plugin will load first
		 *
		 * @see http://stv.whtly.com/2011/09/03/forcing-a-wordpress-plugin-to-be-loaded-before-all-other-plugins/
		 */
		add_action( 'activated_plugin',       array( $this, 'activated_plugin' ) );
	}

	////////////////////////////////////////////////////////////

	/**
	 * Activated plugin
	 */
	public function activated_plugin() {
		$path = str_replace( WP_PLUGIN_DIR . '/', '', $this->file );

		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );

				update_option( 'active_plugins', $plugins );
			}
		}
	}

	////////////////////////////////////////////////////////////

	/**
	 * Plugins loaded
	 */
	public function plugins_loaded() {
		$rel_path = dirname( plugin_basename( $this->file ) ) . '/languages/';

		// Load plugin text domain - WooCommerce Gravity Forms
		// WooCommerce mixed use of 'wc_gf_addons' and 'wc_gravityforms'
		load_plugin_textdomain( 'wc_gravityforms', false, $rel_path );
	}

	////////////////////////////////////////////////////////////

	/**
	 * Load text domain MO file
	 *
	 * @param string $moFile
	 * @param string $domain
	 */
	public function load_mo_file( $mo_file, $domain ) {
		if ( $this->language == null ) {
			$this->language = get_option( 'WPLANG', WPLANG );
			$this->is_spa = ( $this->language == 'es' || $this->language == 'es_ES' );
		}

		// The ICL_LANGUAGE_CODE constant is defined from an plugin, so this constant
		// is not always defined in the first 'load_textdomain_mofile' filter call
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$this->is_spa = ( ICL_LANGUAGE_CODE == 'es' );
		}

		if ( $this->is_spa ) {
			$domains = array(
				// @see https://github.com/woothemes/woocommerce/tree/v2.0.5
				'woocommerce'                => array(
					'i18n/languages/woocommerce-es_ES.mo'       => 'woocommerce/es_ES.mo',
					'i18n/languages/woocommerce-admin-es_ES.mo' => 'woocommerce/admin-es_ES.mo'
				),
				'wc_eu_vat_number'           => array(
					'wc_eu_vat_number-es_ES.mo'                 => 'woocommerce-eu-vat-number/es_ES.mo'
				),
				'woocommerce-shipping-table-rate'               => array(
					'languages/woocommerce-shipping-table-rate-es_ES.mo'           => 'woocommerce-shipping-table-rate/es_ES.mo'
				),
				'woocommerce-product-enquiry-form'            => array(
					'languages/woothemes-es_ES.mo'        => 'woocommerce-product-enquiry-form/es_ES.mo'
				),
				'woocommerce-email-cart' => array(
					'woocommerce-email-cart-es_ES.mo'       => 'woocommerce-email-cart/es_ES.mo'
				)/*,
				'x3m_gf'                     => array(
					'languages/x3m_gf-es_ES.mo'                 => 'woocommerce-gateway-fees/es_ES.mo'
				),
				'woocommerce-delivery-notes' => array(
					'languages/woocommerce-delivery-notes-es_ES.mo' => 'woocommerce-delivery-notes/es_ES.mo'
				)*/
			);

			if ( isset( $domains[$domain] ) ) {
				$paths = $domains[$domain];

				foreach ( $paths as $path => $file ) {
					if ( substr( $mo_file, -strlen( $path ) ) == $path ) {
						$new_file = dirname( $this->file ) . '/languages/' . $file;

						if ( is_readable( $new_file ) ) {
							$mo_file = $new_file;
						}
					}
				}
			}
		}

		return $mo_file;
	}
}

global $woocommerce_es_plugin;

$woocommerce_es_plugin = new WooCommerceESPlugin( __FILE__ );
