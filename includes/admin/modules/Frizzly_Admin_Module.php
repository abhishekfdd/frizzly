<?php
/**
 * Frizzly Admin Module.
 *
 * @package Frizzly
 */

/**
 * Frizzly Admin Module.
 */
abstract class Frizzly_Admin_Module {
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
	 * Options.
	 *
	 * @var Frizzly_Options
	 */
	protected $options;

	/**
	 * Notices.
	 *
	 * @var Frizzly_Admin_Notice[]
	 */
	private $notices;
	/**
	 * Submodules.
	 *
	 * @var Frizzly_Admin_Submodule[]
	 */
	protected $submodules;

	/**
	 *
	 * Constructor.
	 *
	 * @param mixed $slug Slug.
	 * @param mixed $name Name.
	 * @param mixed $options Options.
	 */
	public function __construct( $slug, $name, $options ) {
		$this->name       = $name;
		$this->slug       = $slug;
		$this->options    = $options;
		$this->submodules = array();
		$this->notices    = array();
	}

	/**
	 *
	 * Add submodule.
	 *
	 * @param mixed $submodule Submodule.
	 */
	public function add_submodule( $submodule ) {
		$this->submodules[ $submodule->slug ] = $submodule;
	}

	/**
	 *
	 * Get submodule.
	 *
	 * @param mixed $name Name.
	 */
	public function get_submodule( $name ) {
		return $this->submodules[ $name ];
	}

	/**
	 *
	 * Get tabs.
	 */
	public function get_tabs() {
		$tabs = array();
		foreach ( $this->submodules as $slug => $sub ) {
			$tabs[] = array(
				'slug' => $sub->slug,
				'name' => $sub->name,
			);
		}

		return $tabs;
	}

	/**
	 *
	 * Get page i18n.
	 *
	 * @param mixed $slug Slug.
	 */
	public function get_page_i18n( $slug ) {
		return $this->submodules[ $slug ]->get_page_i18n();
	}

	/**
	 *
	 * Get page settings.
	 *
	 * @param mixed $slug Slug.
	 */
	public function get_page_settings( $slug ) {
		$options_value     = $this->options->get();
		$options_tab_value = $options_value[ $slug ];

		return $this->submodules[ $slug ]->get_page_settings( $options_tab_value );
	}

	/**
	 *
	 * Save settings.
	 *
	 * @param mixed $submodule Submodule.
	 * @param mixed $current_value Current value.
	 */
	public function save_settings( $submodule, $current_value ) {
		$validator = $this->validate( $submodule, $current_value );
		$errors    = $validator->get_errors();

		if ( count( $errors ) > 0 ) {
			$error_messages  = array_merge(
				array( '<strong>' . __( 'Settings not saved.', 'frizzly' ) . '</strong>' ),
				$errors
			);
			$this->notices[] = new Frizzly_Admin_Notice( 'error', true, join( '<br/>', $error_messages ) );
		} else {
			$this->update_settings_section( $submodule, $validator->get_result() );
			$this->notices[] = new Frizzly_Admin_Notice( 'success', true, '<strong>' . __( 'Settings saved.', 'frizzly' ) . '</strong>' );
		}
	}

	/**
	 *
	 * Show notices.
	 *
	 * @param mixed $is_share_module_screen Is share module screen.
	 */
	public function show_notices( $is_share_module_screen ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Subclasses use the argument; kept for a consistent signature.
		foreach ( $this->notices as $notice ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Frizzly_Admin_Notice::get_html() escapes the class and runs the message through wp_kses_post().
			echo $notice->get_html();
		}
	}

	/**
	 *
	 * Update settings section.
	 *
	 * @param mixed $section Section.
	 * @param mixed $updated Updated.
	 */
	public function update_settings_section( $section, $updated ) {
		$options             = $this->options->get();
		$options[ $section ] = $updated;
		$after_update        = $this->options->update( $options );

		return $after_update[ $section ];
	}

	/**
	 * Validate.
	 *
	 * @param mixed $slug $string.
	 * @param mixed $current_value array.
	 * @return Frizzly_Validator
	 */
	public function validate( $slug, $current_value ) {
		$defaults = $this->options->get_default();

		return $this->submodules[ $slug ]->validate( $current_value, $defaults[ $slug ] );
	}
}
