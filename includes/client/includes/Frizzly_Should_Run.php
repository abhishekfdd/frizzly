<?php
/**
 * Frizzly Should Run.
 *
 * @package Frizzly
 */

/**
 * Frizzly Should Run.
 */
class Frizzly_Should_Run {

	/**
	 * Should execute.
	 *
	 * @param mixed $enabled string.
	 * @param mixed $disabled string.
	 * @return bool
	 */
	public static function should_execute( $enabled, $disabled ) {
		$should         = false;
		$enabled_array  = explode( ',', $enabled );
		$disabled_array = explode( ',', $disabled );

		foreach ( $enabled_array as $tag ) {
			if ( self::is_tag( $tag ) ) {
				$should = true;
				break;
			}
		}

		if ( ! $should ) {
			return false;
		}

		foreach ( $disabled_array as $tag ) {
			if ( self::is_tag( $tag ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Is tag.
	 *
	 * @param mixed $tag Tag.
	 */
	private static function is_tag( $tag ) {
		$tag = trim( $tag );
		if ( is_numeric( $tag ) ) {
			$int = intval( $tag );

			return get_the_ID() === $int;
		}
		switch ( strtolower( $tag ) ) {
			case '[front]':
				return is_front_page();
			case '[single]':
				return is_single();
			case '[page]':
				return is_page();
			case '[archive]':
				return is_archive();
			case '[search]':
				return is_search();
			case '[category]':
				return is_category();
			case '[home]':
				return is_home();
			default:
				return false;
		}
	}
}
