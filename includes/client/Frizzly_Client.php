<?php
/**
 * Frizzly Client.
 *
 * @package Frizzly
 */

/**
 * Frizzly Client.
 */
class Frizzly_Client {

	/**
	 * Version.
	 *
	 * @var mixed
	 */
	private $version;
	/**
	 * File.
	 *
	 * @var mixed
	 */
	private $file;

	/**
	 * Share module.
	 *
	 * @var Frizzly_Client_Share_Module
	 */
	private $share_module;

	/**
	 *
	 * Constructor.
	 *
	 * @param mixed $version Version.
	 * @param mixed $file File.
	 */
	public function __construct( $version, $file ) {
		$this->version = $version;
		$this->file    = $file;

		$this->load_dependencies();
		$this->add_actions();
	}

	/**
	 *
	 * Add actions.
	 */
	private function add_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 *
	 * Load dependencies.
	 */
	private function load_dependencies() {
		require_once 'includes/Frizzly_Consts.php';
		require_once 'includes/Frizzly_Html_Element.php';
		require_once 'includes/Frizzly_Meta_Elements.php';
		require_once 'includes/Frizzly_Should_Run.php';
		require_once 'includes/Frizzly_Social_Data_Provider.php';
		require_once 'modules/Frizzly_Client_Submodule.php';
		require_once 'modules/Frizzly_Client_Share_Module.php';

		$this->share_module = new Frizzly_Client_Share_Module();
	}

	/**
	 *
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		$settings = $this->share_module->get_frontend_options();

		$plugin_dir_url = plugin_dir_url( $this->file );
		wp_enqueue_script( 'frizzly-client', $plugin_dir_url . 'js/frizzly.client.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( 'frizzly-client', 'frizzlySettings', $settings );

		wp_enqueue_style( 'frizzly-lib-font-awesome', $plugin_dir_url . 'css/libs/font-awesome/css/font-awesome.css', array(), $this->version );
		wp_enqueue_style( 'frizzly-client', $plugin_dir_url . 'css/frizzly.client.css', array( 'frizzly-lib-font-awesome' ), $this->version );
	}
}
