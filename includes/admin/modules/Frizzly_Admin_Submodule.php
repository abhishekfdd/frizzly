<?php
/**
 * Frizzly Admin Submodule.
 *
 * @package Frizzly
 */

/**
 * Frizzly Admin Submodule.
 */
abstract class Frizzly_Admin_Submodule {
	/**
	 * Name.
	 *
	 * @var mixed
	 */
	public $name;
	/**
	 * Slug.
	 *
	 * @var mixed
	 */
	public $slug;

	/**
	 *
	 * Constructor.
	 *
	 * @param mixed $slug Slug.
	 * @param mixed $name Name.
	 */
	public function __construct( $slug, $name ) {
		$this->name = $name;
		$this->slug = $slug;
	}

	/**
	 *
	 * Get page i18n.
	 */
	public function get_page_i18n() {
		return array();
	}

	/**
	 *
	 * Get page settings.
	 *
	 * @param mixed $db_value Db value.
	 */
	abstract public function get_page_settings( $db_value );

	/**
	 *
	 * Is current tab.
	 */
	public function is_current_tab() {
		// Read-only view selector on a manage_options screen; no nonce applies.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';

		return '' !== $tab && $this->slug === $tab;
	}

	/**
	 *
	 * Is current tab or empty.
	 */
	public function is_current_tab_or_empty() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $this->is_current_tab() || ! isset( $_GET['tab'] );
	}

	/**
	 *
	 * Show notice.
	 *
	 * @param mixed $is_current_settings_screen Is current settings screen.
	 * @param mixed $options Options.
	 */
	abstract public function show_notice( $is_current_settings_screen, $options );

	/**
	 * Validate.
	 *
	 * @param mixed $current_value Current value.
	 * @param mixed $default_value Default value.
	 * @return Frizzly_Validator
	 */
	public function validate( $current_value, $default_value ) {
		return new Frizzly_Validator( $current_value, $default_value, $this->get_page_settings( $default_value ) );
	}

	/**
	 * Create image selector.
	 *
	 * @param mixed $args array.
	 * @return array
	 */
	protected function create_image_selector( $args ) {
		$base_args = array(
			'button_text' => __( 'Select image', 'frizzly' ),
			'title_text'  => __( 'Choose image', 'frizzly' ),
		);

		return array_merge( $base_args, $args );
	}
}
