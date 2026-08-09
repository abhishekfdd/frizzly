<?php
/**
 * Frizzly Social Data Provider.
 *
 * @package Frizzly
 */

/**
 * Frizzly Social Data Provider.
 */
class Frizzly_Social_Data_Provider {

	/**
	 * Id.
	 *
	 * @var mixed
	 */
	private $id;
	/**
	 * Social data.
	 *
	 * @var mixed
	 */
	private $social_data;

	/**
	 *
	 * Frizzly_Social_Data_Provider constructor.
	 *
	 * @param mixed $id int.
	 */
	public function __construct( $id ) {
		$this->id          = $id;
		$this->social_data = null;
	}

	/**
	 *
	 * Get description.
	 *
	 * @param mixed $network Network.
	 */
	public function get_description( $network ) {
		$meta_desc = $this->get_network_property( $network, 'description' );

		if ( '' !== $meta_desc ) {
			return $meta_desc;
		}

		global $post;
		$excerpt = $post->post_excerpt;

		return wp_trim_words( $excerpt );
	}

	/**
	 *
	 * Get image info.
	 *
	 * @param mixed $network Network.
	 */
	public function get_image_info( $network ) {
		$meta_image = $this->get_network_property( $network, 'image' );
		if ( '' === $meta_image && ! has_post_thumbnail( $this->id ) ) {
			return false;
		}
		$thumb_id = get_post_thumbnail_id( $this->id );
		$title    = $this->get_network_property( $network, 'image_title' );
		if ( '' === $title ) {
			$attachment = get_post( $thumb_id );
			$title      = $attachment->post_title;
		}
		$alt = $this->get_network_property( $network, 'image_alt' );
		$alt = '' === $alt ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : $alt;

		return array(
			'url'         => '' === $meta_image ? get_the_post_thumbnail_url( $this->id, 'full' ) : $meta_image,
			'image_title' => $title,
			'image_alt'   => $alt,
		);
	}

	/**
	 *
	 * Get image url.
	 *
	 * @param mixed $network Network.
	 */
	public function get_image_url( $network ) {
		$meta_image = $this->get_network_property( $network, 'image' );

		if ( '' !== $meta_image ) {
			return $meta_image;
		}

		return has_post_thumbnail( $this->id ) ? get_the_post_thumbnail_url( $this->id, 'full' ) : false;
	}

	/**
	 *
	 * Get site name.
	 *
	 * @param mixed $network Network.
	 */
	public function get_site_name( $network ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Kept for symmetry with the other get_*( $network ) accessors.
		return get_bloginfo( 'description' );
	}

	/**
	 *
	 * Get url.
	 */
	public function get_url() {
		return get_permalink( $this->id );
	}

	/**
	 *
	 * Get title.
	 *
	 * @param mixed $network Network.
	 */
	public function get_title( $network ) {
		$meta_title = $this->get_network_property( $network, 'title' );
		if ( '' !== $meta_title ) {
			return $meta_title;
		}

		return get_the_title( $this->id );
	}

	/**
	 *
	 * Get additional data.
	 *
	 * @param mixed $network Network.
	 */
	public function get_additional_data( $network ) {
		$share_options     = new Frizzly_Share_Options();
		$share_options_val = $share_options->get();
		switch ( $network ) {
			case 'twitter':
				if ( ! $share_options->add_handle_to_tweets() ) {
					return array();
				}
				return array(
					'handle' => $share_options_val['general']['twitter_handle'],
				);
			case 'pinterest':
				if ( 'user' === $share_options->get_pinterest_behavior() ) {
					return array();
				}

				return array(
					'image'  => $this->get_image_info( $network ),
					'source' => $share_options->get_pinterest_source(),
				);
			default:
				return array();
		}
	}

	/**
	 *
	 * Initialize social data.
	 */
	private function initialize_social_data() {
		$meta              = new Frizzly_Meta_Social_Data();
		$this->social_data = $meta->get( $this->id );
	}

	/**
	 *
	 * Get network property.
	 *
	 * @param mixed $network Network.
	 * @param mixed $prop_name Prop name.
	 */
	public function get_network_property( $network, $prop_name ) {
		if ( null === $this->social_data ) {
			$this->initialize_social_data();
		}
		if ( isset( $this->social_data[ $network ][ $prop_name ] ) ) {
			return $this->social_data[ $network ][ $prop_name ];
		}

		return '';
	}
}
