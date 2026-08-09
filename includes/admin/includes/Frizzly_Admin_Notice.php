<?php
/**
 * Frizzly Admin Notice.
 *
 * @package Frizzly
 */

/**
 * Frizzly Admin Notice.
 */
class Frizzly_Admin_Notice {
	/**
	 * Type.
	 *
	 * @var mixed
	 */
	private $type;
	/**
	 * Is dismissible.
	 *
	 * @var mixed
	 */
	private $is_dismissible;
	/**
	 * Message.
	 *
	 * @var mixed
	 */
	private $message;


	/**
	 *
	 * Frizzly_Admin_Notice constructor.
	 *
	 * @param mixed $type string.
	 * @param mixed $is_dismissible boolean.
	 * @param mixed $message string.
	 */
	public function __construct( $type, $is_dismissible, $message ) {
		$this->type           = $type;
		$this->is_dismissible = $is_dismissible;
		$this->message        = $message;
	}

	/**
	 *
	 * Get html.
	 */
	public function get_html() {
		$class = sprintf(
			'notice%1$s%2$s',
			$this->is_dismissible ? ' is-dismissible' : '',
			' notice-' . $this->type
		);
		return sprintf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), wp_kses_post( $this->message ) );
	}
}
