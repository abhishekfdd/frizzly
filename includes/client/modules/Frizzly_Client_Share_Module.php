<?php
/**
 * Frizzly Client Share Module.
 *
 * @package Frizzly
 */

/**
 * Frizzly Client Share Module.
 */
class Frizzly_Client_Share_Module {

	/**
	 * Option.
	 *
	 * @var Frizzly_Share_Options
	 */
	protected $option;
	/**
	 * Submodules.
	 *
	 * @var Frizzly_Client_Submodule[]
	 */
	protected $submodules;
	/**
	 * Slug.
	 *
	 * @var mixed
	 */
	protected $slug;

	/**
	 *
	 * Constructor.
	 */
	public function __construct() {
		$this->option     = new Frizzly_Share_Options();
		$this->slug       = 'share';
		$this->submodules = array();
		$this->load_dependencies();
		$this->add_submodules();
	}

	/**
	 * Add submodule.
	 *
	 * @param mixed $submodule Frizzly_Client_Submodule.
	 */
	public function add_submodule( $submodule ) {
		$this->submodules[ $submodule->slug ] = $submodule;
	}

	/**
	 *
	 * Get options.
	 */
	public function get_options() {
		return $this->option->get();
	}

	/**
	 * Returns the option value from the database with i18n added to each submodule.
	 *
	 * @return array
	 */
	public function get_frontend_options() {
		$result = $this->get_options();
		$subs   = $this->get_active_submodules();
		foreach ( $subs as $slug => $module ) {
			$result[ $slug ]         = isset( $result[ $slug ] ) ? $result[ $slug ] : array();
			$result[ $slug ]['i18n'] = $module->get_i18n();
		}

		return $result;
	}

	/**
	 *
	 * Get slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 *
	 * Load dependencies.
	 */
	private function load_dependencies() {
		require_once 'share/Frizzly_Link_Generator.php';
		require_once 'share/Frizzly_Button_Generator.php';
		require_once 'share/Frizzly_Client_Image_Submodule.php';
		require_once 'share/Frizzly_Client_General_Submodule.php';
		require_once 'share/Frizzly_Client_Content_Submodule.php';
	}

	/**
	 *
	 * Add submodules.
	 */
	private function add_submodules() {
		$this->add_submodule( new Frizzly_Client_General_Submodule( $this->option ) );
		$this->add_submodule( new Frizzly_Client_Image_Submodule( $this->option ) );
		$this->add_submodule( new Frizzly_Client_Content_Submodule( $this->option ) );
	}

	/**
	 * Get active submodules.
	 *
	 * @return Frizzly_Client_Submodule[]
	 */
	public function get_active_submodules() {
		$modules = array();
		foreach ( $this->submodules as $submodule ) {
			if ( $submodule->is_active() ) {
				$modules[ $submodule->slug ] = $submodule;
			}
		}
		return $modules;
	}
}
