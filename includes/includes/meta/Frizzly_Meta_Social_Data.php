<?php
/**
 * Frizzly Meta Social Data.
 *
 * @package Frizzly
 */

/**
 * Frizzly Meta Social Data.
 */
class Frizzly_Meta_Social_Data extends Frizzly_Meta {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'frizzly_social_data' );
	}

	/**
	 * Get defaults.
	 */
	protected function get_defaults() {
		$default_network_settings = array(
			'title'       => '',
			'description' => '',
			'image'       => '',
		);

		return array(
			'facebook'  => $default_network_settings,
			'twitter'   => $default_network_settings,
			'pinterest' => $default_network_settings,
		);
	}
}
