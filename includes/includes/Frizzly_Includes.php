<?php
/**
 * Frizzly Includes.
 *
 * @package Frizzly
 */

/**
 * Frizzly Includes.
 */
class Frizzly_Includes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Load dependencies.
	 */
	public function load_dependencies() {
		require_once 'options/Frizzly_Options.php';
		require_once 'options/Frizzly_Share_Options.php';
		require_once 'meta/Frizzly_Meta.php';
		require_once 'meta/Frizzly_Meta_Social_Data.php';
		require_once 'Frizzly_Version_Updater.php';
	}
}
