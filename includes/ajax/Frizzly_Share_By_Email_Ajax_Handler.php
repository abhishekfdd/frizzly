<?php
/**
 * Frizzly Share By Email Ajax Handler.
 *
 * @package Frizzly
 */

/**
 * Frizzly Share By Email Ajax Handler.
 */
class Frizzly_Share_By_Email_Ajax_Handler extends Frizzly_Ajax_Handler {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'share_by_email' );
	}

	/**
	 * Handle action.
	 */
	public function handle_action() {
		// Nonce is verified by Frizzly_Ajax_Handler::handle() before this method runs.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$post_id    = isset( $_POST['postId'] ) ? absint( wp_unslash( $_POST['postId'] ) ) : 0;
		$to_email   = isset( $_POST['toEmail'] ) ? sanitize_email( wp_unslash( $_POST['toEmail'] ) ) : '';
		$from_email = isset( $_POST['fromEmail'] ) ? sanitize_email( wp_unslash( $_POST['fromEmail'] ) ) : '';
		$from_name  = isset( $_POST['fromName'] ) ? sanitize_text_field( wp_unslash( $_POST['fromName'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( ! is_email( $to_email ) ) {
			return $this->return_error( __( 'Recipient address is not a valid email.', 'frizzly' ) );
		}
		if ( ! is_email( $from_email ) ) {
			return $this->return_error( __( 'Your address is not a valid email.', 'frizzly' ) );
		}
		if ( '' === $from_name ) {
			return $this->return_error( __( 'Your name is empty.', 'frizzly' ) );
		}
		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
			return $this->return_error( __( 'The post you tried to share could not be found.', 'frizzly' ) );
		}

		$email_content = $this->get_email_content( $post, $from_name, $from_email );
		$this->send_email( $post->post_title, $email_content, $to_email, $from_email, $from_name );
		return $this->return_success( __( 'Thanks for sharing!', 'frizzly' ) );
	}

	/**
	 * Return error.
	 *
	 * @param mixed $message Message.
	 */
	public function return_error( $message ) {
		return array(
			'status'  => 'error',
			'message' => $message,
		);
	}

	/**
	 * Return success.
	 *
	 * @param mixed $message Message.
	 */
	public function return_success( $message ) {
		return array(
			'status'  => 'success',
			'message' => $message,
		);
	}

	/**
	 * Send email.
	 *
	 * @param mixed $post_title Post title.
	 * @param mixed $content Content.
	 * @param mixed $to_email To email.
	 * @param mixed $from_email From email.
	 * @param mixed $from_name From name.
	 */
	public function send_email( $post_title, $content, $to_email, $from_email, $from_name ) {
		// Derived from home_url() rather than $_SERVER['SERVER_NAME'], which follows the
		// Host header and would let a caller choose the sender domain.
		$sitename = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}

		/*
		 * Deliberately not passed through the core `wp_mail_from` filter here: wp_mail()
		 * applies that filter to whatever From address it ends up with, including one
		 * supplied via a header (wp-includes/pluggable.php - the apply_filters() call
		 * sits outside the `if ( ! isset( $from_email ) )` block). Applying it here too
		 * would run every listener twice.
		 */
		$local_email = 'wordpress@' . $sitename;

		$headers   = array();
		$headers[] = sprintf( 'From: %1$s <%2$s>', $from_name, $local_email );
		$headers[] = sprintf( 'Reply-To: %1$s <%2$s>', $from_name, $from_email );
		wp_mail( $to_email, '[' . __( 'Shared Post', 'frizzly' ) . '] ' . $post_title, $content, $headers );
	}

	/**
	 * Get email content.
	 *
	 * @param mixed $post WP_Post.
	 * @param mixed $from_name string.
	 * @param mixed $from_email string.
	 * @return string
	 */
	public function get_email_content( $post, $from_name, $from_email ) {
		/* translators: 1: sender name, 2: sender email address */
		$content  = sprintf( __( '%1$s (%2$s) thinks you may be interested in the following post:', 'frizzly' ), $from_name, $from_email );
		$content .= "\n\n";
		$content .= $post->post_title . "\n";
		$content .= get_permalink( $post->ID ) . "\n";
		return $content;
	}
}
