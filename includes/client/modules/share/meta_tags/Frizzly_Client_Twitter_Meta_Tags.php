<?php
/**
 * Frizzly Client Twitter Meta Tags.
 *
 * @package Frizzly
 */

/**
 * Frizzly Client Twitter Meta Tags.
 */
class Frizzly_Client_Twitter_Meta_Tags {

	/**
	 * Network name.
	 *
	 * @var mixed
	 */
	private $network_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->network_name = 'twitter';
	}

	/**
	 * Print tags.
	 *
	 * @param mixed $post_id Post id.
	 * @param mixed $options Options.
	 */
	public function print_tags( $post_id, $options ) {
		$provider = new Frizzly_Social_Data_Provider( $post_id );
		$elements = new Frizzly_Meta_Elements();
		$elements
			->add_element( 'twitter:card', $options['meta_twitter_card_type'] )
			->add_element( 'twitter:site', $options['twitter_handle'] )
			->add_element( 'twitter:description', $provider->get_description( $this->network_name ) )
			->add_element( 'twitter:title', $provider->get_title( $this->network_name ) );
		$img = $provider->get_image_url( $this->network_name );
		if ( false !== $img ) {
			$elements->add_element( 'twitter:image', $img );
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Frizzly_Meta_Elements::get_html() escapes every property and content value with esc_attr().
		echo $elements->get_html();
	}
}
