<?php
/**
 * Frizzly Meta.
 *
 * @package Frizzly
 */

/**
 * Frizzly Meta.
 */
abstract class Frizzly_Meta {

	/**
	 * Meta key.
	 *
	 * @var mixed
	 */
	private $meta_key;

	/**
	 * Constructor.
	 *
	 * @param mixed $meta_key Meta key.
	 */
	public function __construct( $meta_key ) {
		$this->meta_key = $meta_key;
	}

	/**
	 * Get defaults.
	 */
	abstract protected function get_defaults();

	/**
	 * Get.
	 *
	 * @param mixed $post_id Post id.
	 */
	public function get( $post_id ) {
		$def  = $this->get_defaults();
		$meta = get_post_meta( $post_id, $this->meta_key, true );

		return $this->merge_arrays( $def, $meta );
	}

	/**
	 * Get key.
	 */
	public function get_key() {
		return $this->meta_key;
	}

	/**
	 * Merge arrays.
	 *
	 * @param mixed $defaults Defaults.
	 * @param mixed $db_options Db options.
	 */
	private function merge_arrays( $defaults, $db_options ) {
		$merged = array();
		foreach ( $defaults as $key => $values ) {
			$merged[ $key ] = is_array( $values )
				? ( array_merge( $values, isset( $db_options[ $key ] ) ? $db_options[ $key ] : array() ) )
				: ( isset( $db_options[ $key ] ) ? $db_options[ $key ] : $values );
		}

		return $merged;
	}

	/**
	 * Update.
	 *
	 * @param mixed $post_id Post id.
	 * @param mixed $new_value New value.
	 */
	public function update( $post_id, $new_value ) {
		$defaults      = $this->get_defaults();
		$updated_value = $this->merge_arrays( $defaults, $new_value );
		update_post_meta( $post_id, $this->meta_key, $updated_value );
	}
}
