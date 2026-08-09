<?php
/**
 * Frizzly Admin.
 *
 * @package Frizzly
 */

/**
 * Frizzly Admin.
 */
class Frizzly_Admin {

	/**
	 * Admin settings screen.
	 *
	 * @var mixed
	 */
	private $admin_settings_screen;
	/**
	 * Admin post edit screen.
	 *
	 * @var mixed
	 */
	private $admin_post_edit_screen;
	/**
	 * Admin modules.
	 *
	 * @var mixed
	 */
	private $admin_modules;

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
	}

	/**
	 *
	 * Init.
	 */
	public function init() {
		require_once 'includes/Frizzly_Ajax_Result_Builder.php';
		require_once 'includes/Frizzly_Admin_Notice.php';
		require_once 'includes/Frizzly_Validator.php';
		require_once 'Frizzly_Admin_Modules.php';
		require_once 'Frizzly_Welcome_Screen.php';
		require_once 'screens/Frizzly_Admin_Settings_Screen.php';
		require_once 'screens/Frizzly_Admin_Post_Edit_Screen.php';

		$this->admin_modules = new Frizzly_Admin_Modules();
		$this->admin_modules->init();

		$this->admin_settings_screen = new Frizzly_Admin_Settings_Screen( $this->name, $this->version, $this->file );
		$this->admin_settings_screen->init();

		$this->admin_post_edit_screen = new Frizzly_Admin_Post_Edit_Screen( $this->name, $this->version, $this->file );
		$this->admin_post_edit_screen->init();

		new Frizzly_Welcome_Screen( $this->file, $this->version );
	}
}
