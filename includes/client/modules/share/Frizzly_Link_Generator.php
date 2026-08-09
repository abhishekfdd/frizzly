<?php
/**
 * Frizzly Link Generator.
 *
 * @package Frizzly
 */

/**
 * Frizzly Link Generator.
 */
class Frizzly_Link_Generator {

	/**
	 * Generate.
	 *
	 * @param mixed $network string.
	 * @param mixed $data_provider Frizzly_Social_Data_Provider.
	 * @param null  $additional_data Additional data.
	 * @return string
	 */
	public static function generate( $network, $data_provider, $additional_data = null ) {
		if ( ! $additional_data ) {
			$additional_data = $data_provider->get_additional_data( $network );
		}
		switch ( $network ) {
			case 'digg':
				return sprintf(
					'http://digg.com/submit?url=%s&title=%s',
					self::encode_uri_component( $data_provider->get_url() ),
					self::encode_uri_component( $data_provider->get_title( $network ) )
				);
			case 'email':
				$subject = '[' . __( 'Shared Post', 'frizzly' ) . '] ' . $data_provider->get_title( $network );
				$subject = self::encode_uri_component( $subject );
				$body    = __( 'You may be interested in the following post:', 'frizzly' ) . "\n\n" . $data_provider->get_url();
				$body    = self::encode_uri_component( $body );

				return sprintf(
					'mailto:?subject=%s&body=%s',
					$subject,
					$body
				);
			case 'facebook':
				return sprintf(
					'http://www.facebook.com/sharer.php?u=%s',
					self::encode_uri_component( $data_provider->get_url() )
				);
			case 'googleplus':
				return sprintf(
					'https://plus.google.com/share?url=%s',
					self::encode_uri_component( $data_provider->get_url() )
				);
			case 'linkedin':
				return sprintf(
					'https://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s&summary=%s',
					self::encode_uri_component( $data_provider->get_url() ),
					self::encode_uri_component( $data_provider->get_title( $network ) ),
					self::encode_uri_component( $data_provider->get_description( $network ) )
				);
			case 'pinterest':
				if ( ! isset( $additional_data['image'] ) || ! is_array( $additional_data['image'] ) || ! isset( $additional_data['image']['url'] ) ) {
					return '';
				}
				$source             = $additional_data['source'];
				$data               = $additional_data['image'];
				$data['post_title'] = $data_provider->get_title( $network );
				$pin_description    = self::find_first( $source, $data );

				return sprintf(
					'http://pinterest.com/pin/create/bookmarklet/?is_video=false&url=%s&media=%s&description=%s',
					self::encode_uri_component( $data_provider->get_url() ),
					self::encode_uri_component( $additional_data['image']['url'] ),
					self::encode_uri_component( $pin_description )
				);
			case 'reddit':
				return sprintf(
					'https://www.reddit.com/submit?url=%s',
					self::encode_uri_component( $data_provider->get_url() )
				);
			case 'stumbleupon':
				return sprintf(
					'http://www.stumbleupon.com/submit?url=%s&title=%s',
					self::encode_uri_component( $data_provider->get_url() ),
					self::encode_uri_component( $data_provider->get_title( $network ) )
				);
			case 'twitter':
				$additional_data = $data_provider->get_additional_data( $network );

				return sprintf(
					'https://twitter.com/share?url=%s&text=%s%s',
					self::encode_uri_component( $data_provider->get_url() ),
					self::encode_uri_component( $data_provider->get_title( $network ) ),
					isset( $additional_data['handle'] ) ? sprintf( '&via=%s', self::encode_uri_component( $additional_data['handle'] ) ) : ''
				);
			default:
				return '';
		}
	}

	/**
	 *
	 * Find first.
	 *
	 * @param mixed $seq Seq.
	 * @param mixed $data Data.
	 */
	private static function find_first( $seq, $data ) {
		foreach ( $seq as $key ) {
			if ( isset( $data[ $key ] ) && strlen( $data[ $key ] ) > 0 ) {
				return $data[ $key ];
			}
		}
		return '';
	}

	/**
	 *
	 * EncodeURIComponent.
	 *
	 * @param mixed $str Str.
	 */
	private static function encode_uri_component( $str ) {
		$revert = array(
			'%21' => '!',
			'%2A' => '*',
			'%27' => "'",
			'%28' => '(',
			'%29' => ')',
		);

		return strtr( rawurlencode( $str ), $revert );
	}
}
