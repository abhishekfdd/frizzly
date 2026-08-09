<?php
/**
 * Frizzly Options.
 *
 * @package Frizzly
 */

/**
 * Frizzly Options.
 */
abstract class Frizzly_Options {

	/**
	 *
	 * Get name.
	 */
	abstract public function get_name();

	/**
	 *
	 * Get default.
	 */
	abstract public function get_default();

	/**
	 *
	 * Sanitize.
	 *
	 * @param mixed $input Input.
	 */
	protected function sanitize( $input ) {
		return $input;
	}

	/**
	 *
	 * Get.
	 */
	public function get() {
		$db_options = get_option( $this->get_name() );
		$db_options = is_array( $db_options ) ? $db_options : array();
		$defaults   = $this->get_default();
		$merged     = $this->merge_arrays( $defaults, $db_options );
		return $this->sanitize( $merged );
	}

	/**
	 *
	 * Update.
	 *
	 * @param mixed $new_value New value.
	 */
	public function update( $new_value ) {
		$defaults = $this->get_default();
		$merged   = $this->merge_arrays( $defaults, $new_value );
		$merged   = $this->sanitize( $merged );
		update_option( $this->get_name(), $merged );

		return $merged;
	}

	/**
	 * Merge arrays.
	 *
	 * @param mixed $defaults Defaults.
	 * @param mixed $db_options Db options.
	 * @return array
	 */
	private function merge_arrays( $defaults, $db_options ) {
		$merged = array();
		foreach ( $defaults as $key => $values ) {
			$merged[ $key ] = array_merge( $values, isset( $db_options[ $key ] ) ? $db_options[ $key ] : array() );
		}
		return $merged;
	}
}
