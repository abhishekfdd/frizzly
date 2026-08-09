<?php

class Frizzly_Share_By_Email_Ajax_Handler extends Frizzly_Ajax_Handler {

	function __construct() {
		parent::__construct( 'share_by_email' );
	}

	function handle_action() {
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

	function return_error( $message ) {
		return array(
			'status'  => 'error',
			'message' => $message,
		);
	}

	function return_success( $message ) {
		return array(
			'status'  => 'success',
			'message' => $message,
		);
	}

	function send_email( $post_title, $content, $to_email, $from_email, $from_name ) {
		// Derived from home_url() rather than $_SERVER['SERVER_NAME'], which follows the
		// Host header and would let a caller choose the sender domain.
		$sitename = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}
		$local_email = apply_filters( 'wp_mail_from', 'wordpress@' . $sitename );
		$headers[]   = sprintf( 'From: %1$s <%2$s>', $from_name, $local_email );
		$headers[]   = sprintf( 'Reply-To: %1$s <%2$s>', $from_name, $from_email );
		wp_mail( $to_email, '[' . __( 'Shared Post', 'frizzly' ) . '] ' . $post_title, $content, $headers );
	}

	/**
	 * @param $post WP_Post
	 * @param $from_name string
	 * @param $from_email string
	 *
	 * @return string
	 */
	function get_email_content( $post, $from_name, $from_email ) {
		/* translators: 1: sender name, 2: sender email address */
		$content  = sprintf( __( '%1$s (%2$s) thinks you may be interested in the following post:', 'frizzly' ), $from_name, $from_email );
		$content .= "\n\n";
		$content .= $post->post_title . "\n";
		$content .= get_permalink( $post->ID ) . "\n";
		return $content;
	}
}
