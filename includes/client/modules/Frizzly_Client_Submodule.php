<?php
/**
 * Frizzly Client Submodule.
 *
 * @package Frizzly
 */

/**
 * Frizzly Client Submodule.
 */
abstract class Frizzly_Client_Submodule {

	/**
	 * Slug.
	 *
	 * @var mixed
	 */
	public $slug;
	/**
	 * Option.
	 *
	 * @var Frizzly_Options
	 */
	private $option;

	/**
	 *
	 * Constructor.
	 *
	 * @param mixed $slug Slug.
	 * @param mixed $option Option.
	 */
	public function __construct( $slug, $option ) {
		$this->slug   = $slug;
		$this->option = $option;
	}

	/**
	 *
	 * Get i18n.
	 */
	public function get_i18n() {
		return array();
	}

	/**
	 *
	 * Get module options.
	 */
	public function get_module_options() {
		return $this->option->get();
	}

	/**
	 *
	 * Get submodule options.
	 */
	public function get_submodule_options() {
		$options = $this->get_module_options();
		return isset( $options[ $this->slug ] ) ? $options[ $this->slug ] : array();
	}

	/**
	 * Is active.
	 *
	 * @return boolean
	 */
	public function is_active() {
		$module_options = $this->get_module_options();
		return isset( $module_options['general'] [ 'active_' . $this->slug ] ) &&
				$module_options['general'] [ 'active_' . $this->slug ];
	}
}
