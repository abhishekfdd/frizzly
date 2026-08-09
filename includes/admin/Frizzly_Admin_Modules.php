<?php
/**
 * Frizzly Admin Modules.
 *
 * @package Frizzly
 */

/**
 * Frizzly Admin Modules.
 */
class Frizzly_Admin_Modules {
	/**
	 * Init.
	 */
	public function init() {
		require_once 'modules/Frizzly_Admin_Module.php';
		require_once 'modules/Frizzly_Admin_Submodule.php';
		require_once 'modules/Frizzly_Admin_Share_Module.php';
	}
}
