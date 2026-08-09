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

		/*
		 * Bootstrapping is deferred to `init` because the admin objects call __() from
		 * their constructors. Running that at plugin-load time makes WordPress load the
		 * text domain before `init`, which since 6.7 emits a _doing_it_wrong() notice and
		 * - with WP_DEBUG_DISPLAY on - sends output early enough to break later header()
		 * calls such as the welcome-screen redirect.
		 *
		 * Every hook registered below fires after `init` (admin_menu, admin_init,
		 * wp_enqueue_scripts, the_content, wp_ajax_*), so nothing is missed.
		 */
		add_action( 'init', array( $this, 'load_dependencies' ), 5 );
		add_action( 'init', array( $this, 'update_plugin' ), 5 );
	}

	/**
	 * Load dependencies.
	 */
	public function load_dependencies() {
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
	 * Update plugin.
	 */
	public function update_plugin() {
		$updater = new Frizzly_Version_Updater( $this->version );
		$updater->update();
	}
}
