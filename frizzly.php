<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Frizzly - Social Share Buttons
 * Plugin URI:        http://confusedblogger.com/
 * Description:       Great-looking social share icons all over your website.
 * Version:           1.1.1
 * Author:            Abhishek Kumar
 * Author URI:        http://confusedblogger.com/
 * Text Domain:       frizzly
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least:  6.5
 * Requires PHP:      7.4
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

if ( ! class_exists( 'Frizzly_Loader' ) ) :

	final class Frizzly_Loader {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new Frizzly_Loader();
			}
			return self::$instance;
		}

		private function __construct() {
			require_once 'includes/Frizzly.php';
			$version = '1.1.1';
			$name    = 'Frizzly';
			$frizzly = new Frizzly( $name, $version, __FILE__ );
		}
	}

	function frizzly_activation_hook() {
		// Bail if activating from network, or bulk.
		// WordPress has already verified its own nonce to reach the activation hook.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		set_transient( '_frizzly_activation_redirect', true, 30 );
	}
	register_activation_hook( __FILE__, 'frizzly_activation_hook' );

endif; // End if class_exists check

Frizzly_Loader::instance();
