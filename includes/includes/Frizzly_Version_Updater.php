<?php
/**
 * Frizzly Version Updater.
 *
 * @package Frizzly
 */

/**
 * Frizzly Version Updater.
 */
class Frizzly_Version_Updater {

	/**
	 * Option name.
	 *
	 * @var mixed
	 */
	private $option_name = 'frizzly_version';
	/**
	 * Version.
	 *
	 * @var mixed
	 */
	private $version;

	/**
	 *
	 * Constructor.
	 *
	 * @param mixed $version Version.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 *
	 * Update.
	 */
	public function update() {
		$version = get_option( $this->option_name, '1.0.1' );

		if ( $this->version === $version ) {
			return;
		}

		if ( version_compare( $version, '1.1.0', 'lt' ) ) {
			$this->update_1_1_0();
		}

		update_option( $this->option_name, $this->version );
	}

	/**
	 *
	 * Update 1 1 0.
	 */
	private function update_1_1_0() {
		set_transient( '_frizzly_activation_redirect', true, 30 );
	}
}
