<?php
/**
 * Frizzly Ajax Result Builder.
 *
 * @package Frizzly
 */

/**
 * Frizzly Ajax Result Builder.
 */
class Frizzly_Ajax_Result_Builder {

	/**
	 * Model.
	 *
	 * @var mixed
	 */
	private $model;
	/**
	 * Status.
	 *
	 * @var mixed
	 */
	private $status;
	/**
	 * Messages.
	 *
	 * @var mixed
	 */
	private $messages;

	/**
	 *
	 * Constructor.
	 */
	public function __construct() {
		$this->model    = null;
		$this->messages = array();
		$this->status   = 'OK';
	}

	/**
	 *
	 * Set model.
	 *
	 * @param mixed $model Model.
	 */
	public function set_model( $model ) {
		$this->model = $model;
		return $this;
	}

	/**
	 *
	 * Set error.
	 *
	 * @param mixed  $error_msg Error msg.
	 * @param string $error_msg_id Error msg id.
	 */
	public function set_error( $error_msg, $error_msg_id = 'error' ) {
		$arr                  = array();
		$arr[ $error_msg_id ] = $error_msg;
		return $this->set_errors( $arr );
	}

	/**
	 *
	 * Set errors.
	 *
	 * @param mixed $errors Errors.
	 */
	public function set_errors( $errors ) {
		$this->status   = 'ERROR';
		$this->messages = $errors;
		return $this;
	}

	/**
	 *
	 * Set message.
	 *
	 * @param mixed  $msg Msg.
	 * @param string $msg_id Msg id.
	 */
	public function set_message( $msg, $msg_id = 'ok' ) {
		$this->messages = array( $msg_id => $msg );
		return $this;
	}

	/**
	 *
	 * Build.
	 */
	public function build() {
		$res = array(
			'status' => array(
				'status'   => $this->status,
				'messages' => $this->messages,
			),
		);

		if ( null !== $this->model ) {
			$res['model'] = $this->model;
		}

		return $res;
	}
}
