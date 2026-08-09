<?php
/**
 * Frizzly Html Element.
 *
 * @package Frizzly
 */

/**
 * Frizzly Html Element.
 */
class Frizzly_Html_Element {

	/**
	 * Tag.
	 *
	 * @var mixed
	 */
	private $tag;
	/**
	 * Attributes.
	 *
	 * @var array
	 */
	private $attributes;
	/**
	 * Elements.
	 *
	 * @var Frizzly_Html_Element[]
	 */
	private $elements;

	/**
	 *
	 * Frizzly_Html_Element constructor.
	 *
	 * @param mixed $tag string.
	 */
	public function __construct( $tag ) {
		$this->tag        = $tag;
		$this->attributes = array();
		$this->elements   = array();
	}

	/**
	 *
	 * Add attribute.
	 *
	 * @param mixed $name Name.
	 * @param mixed $value Value.
	 */
	public function add_attribute( $name, $value ) {
		$this->attributes[ $name ] = $value;

		return $this;
	}

	/**
	 *
	 * Add attributes.
	 *
	 * @param mixed $atts Atts.
	 */
	public function add_attributes( $atts ) {
		$this->attributes = array_merge( $this->attributes, $atts );

		return $this;
	}

	/**
	 * Append element.
	 *
	 * @param mixed $elem Frizzly_Html_Element.
	 */
	public function append_element( $elem ) {
		$this->elements[] = $elem;

		return $this;
	}

	/**
	 * Get html.
	 *
	 * @return string
	 */
	public function get_html() {
		$html = sprintf( '<%s', $this->tag );
		foreach ( $this->attributes as $att_name => $att_value ) {
			$html .= sprintf( ' %s="%s"', $att_name, esc_attr( $att_value ) );
		}
		$html .= '>';
		foreach ( $this->elements as $element ) {
			$html .= $element->get_html();
		}
		$html .= sprintf( '</%s>', $this->tag );

		return $html;
	}
}
