<?php
/**
 * Frizzly.
 *
 * @package Frizzly
 */

/**
 * Frizzly.
 */
class Frizzly {

	/**
	 * Admin.
	 *
	 * @var mixed
	 */
	private $admin;
	/**
	 * Name.
	 *
	 * @var mixed
	 */
	private $name;
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
	 *
	 * Constructor.
	 *
	 * @param mixed $name Name.
	 * @param mixed $version Version.
	 * @param mixed $file File.
	 */
	public function __construct( $name, $version, $file ) {
		$this->name    = $name;
		$this->version = $version;
		$this->file    = $file;
		$this->load_dependencies();

		add_action( 'plugins_loaded', array( $this, 'update_plugin' ) );
	}

	/**
	 *
	 * Load dependencies.
	 */
	private function load_dependencies() {
		require_once 'includes/Frizzly_Includes.php';
		new Frizzly_Includes();

		require_once 'ajax/Frizzly_Ajax.php';
		new Frizzly_Ajax();

		if ( is_admin() ) {
			require_once 'admin/Frizzly_Admin.php';
			$this->admin = new Frizzly_Admin( $this->name, $this->version, $this->file );
			$this->admin->init();
		} else {
			require_once 'client/Frizzly_Client.php';
			new Frizzly_Client( $this->version, $this->file );
		}
	}

	/**
	 *
	 * Update plugin.
	 */
	public function update_plugin() {
		$updater = new Frizzly_Version_Updater( $this->version );
		$updater->update();
	}
}
