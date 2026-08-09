<?php

class Frizzly_Validator {

	private $current;
	private $default;
	private $settings;

	private $result;
	/**
	 * @var string[]
	 */
	private $errors;

	function __construct( $current, $default, $settings ) {
		$this->current  = $current;
		$this->default  = $default;
		$this->settings = $settings;

		$this->process();
	}

	private function process() {
		$this->errors = array();
		$this->result = array();

		foreach ( $this->default as $key => $def_value ) {
			if ( ! isset( $this->settings[ $key ] ) ) {
				continue;
			}
			$setting = $this->settings[ $key ];

			$value             = ! isset( $this->current[ $key ] )
				? ( 'boolean' === $setting['type'] ? '' : $def_value )
				: $this->current[ $key ];
			$value             = $this->try_sanitize_field( $value, $setting );
			$validation_result = $this->validate_field( $value, $this->settings[ $key ] );
			if ( true !== $validation_result ) {
				$this->errors[ $key ] = $validation_result;
			}
			$this->result[ $key ] = $value;
		}
	}

	function get_errors() {
		return $this->errors;
	}

	function get_result() {
		return $this->result;
	}

	/**
	 * @param $error_type string
	 * @param $error_default_format string
	 * @param $setting array
	 *
	 * @return string
	 */
	private function get_error_text( $error_type, $error_default_format, $setting ) {
		if ( isset( $setting['error_messages'] ) && isset( $setting['error_messages'][ $error_type ] ) ) {
			return $setting['error_messages'][ $error_type ];
		}
		$error_label = isset( $setting['error_label'] )
			? $setting['error_label']
			: ( isset( $setting['label'] ) ? $setting['label'] : '' );

		return sprintf( $error_default_format, $error_label );
	}

	private function validate_field( $value, $setting ) {
		if ( ! isset( $setting['type'] ) ) {
			return true;
		}
		$func_name = 'validate_' . $setting['type'];

		if ( is_callable( array( $this, $func_name ) ) ) {
			return call_user_func( array( $this, $func_name ), $value, $setting );
		}

		return true;
	}

	private function validate_float( $value, $setting ) {
		if ( ! is_float( $value ) ) {
			/* translators: %1$s: setting label */
			return $this->get_error_text( 'type', __( '%1$s value is not a number.', 'frizzly' ), $setting );
		}

		return $this->validate_mix_max( $value, $setting );
	}

	private function validate_int( $value, $setting ) {
		if ( ! is_int( $value ) ) {
			/* translators: %1$s: setting label */
			return $this->get_error_text( 'type', __( '%1$s value is not a number.', 'frizzly' ), $setting );
		}

		return $this->validate_mix_max( $value, $setting );
	}

	private function validate_mix_max( $value, $setting ) {
		if ( isset( $setting['min'] ) && $value < $setting['min'] ) {
			/* translators: 1: setting label, 2: minimum allowed value */
			return $this->get_error_text( 'min', __( '%1$s  value is less than the minimum value of %2$s.', 'frizzly' ), $setting );
		}
		if ( isset( $setting['max'] ) && $value > $setting['max'] ) {
			/* translators: 1: setting label, 2: maximum allowed value */
			return $this->get_error_text( 'max', __( '%1$s value is greater than the minimum value of %2$s.', 'frizzly' ), $setting );
		}

		return true;
	}

	private function validate_multiselect( $value, $setting ) {
		/* translators: %1$s: setting label */
		$err = __( '%1$s value is invalid.', 'frizzly' );
		if ( ! is_array( $value ) ) {
			return $this->get_error_text( 'type', $err, $setting );
		}
		if ( isset( $setting['min'] ) && count( $value ) < $setting['min'] ) {
			/* translators: %1$s: setting label */
			return $this->get_error_text( 'min', __( '%1$s does not have enough selected records.', 'frizzly' ), $setting );
		}
		$options_keys = array_keys( $setting['options'] );
		foreach ( $value as $name ) {
			if ( ! in_array( $name, $options_keys ) ) {
				return $this->get_error_text( 'type', $err, $setting );
			}
		}

		return true;
	}

	private function validate_select( $value, $setting ) {
		$options_keys = array_keys( $setting['options'] );
		if ( ! in_array( $value, $options_keys ) ) {
			return sprintf(
				/* translators: %1$s: setting label */
				__( '%1$s value is invalid.', 'frizzly' ),
				$this->get_error_label( $setting )
			);
		}

		return true;
	}

	private function try_sanitize_field( $value, $setting ) {
		if ( ! isset( $setting['type'] ) ) {
			return $value;
		}
		switch ( $setting['type'] ) {
			case 'int':
				return is_numeric( $value ) ? intval( $value ) : $value;
			case 'float':
				return is_numeric( $value ) ? floatval( $value ) : $value;
			case 'boolean':
				return 'on' === $value;
			case 'multiselect':
				return '' === $value ? array() : explode( ',', $value );
			case 'text':
				// Free-text settings are stored and later re-emitted (meta tags, localized
				// script data), so strip markup here rather than relying on output escaping.
				return is_string( $value ) ? sanitize_text_field( $value ) : $value;
			default:
				return $value;
		}
	}
}
