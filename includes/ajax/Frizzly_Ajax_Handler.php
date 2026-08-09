<?php
/**
 * Frizzly Ajax Handler.
 *
 * @package Frizzly
 */

/**
 * Frizzly Ajax Handler.
 */
abstract class Frizzly_Ajax_Handler {

	/**
	 * Action name.
	 *
	 * @var mixed
	 */
	public $action_name;

	/**
	 * Constructor.
	 *
	 * @param mixed $action_name Action name.
	 */
	public function __construct( $action_name ) {
		$this->action_name = 'frizzly_' . $action_name;
		add_action( 'wp_ajax_nopriv_' . $this->action_name, array( $this, 'handle' ) );
		add_action( 'wp_ajax_' . $this->action_name, array( $this, 'handle' ) );
	}

	/**
	 * Handle.
	 */
	public function handle() {
		check_ajax_referer( $this->action_name, 'nonce' );
		$result = $this->handle_action();
		wp_send_json( $result );
	}

	/**
	 * Handle action.
	 */
	abstract public function handle_action();
}
