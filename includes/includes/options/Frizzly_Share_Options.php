<?php
/**
 * Frizzly Share Options.
 *
 * @package Frizzly
 */

/**
 * Frizzly Share Options.
 */
class Frizzly_Share_Options extends Frizzly_Options {

	/**
	 * Add handle to tweets.
	 */
	public function add_handle_to_tweets() {
		$val = $this->get();

		return $val['general']['add_handle_to_tweets'];
	}

	/**
	 * Get name.
	 */
	public function get_name() {
		return 'frizzly_share';
	}

	/**
	 * Get default.
	 */
	public function get_default() {
		$defaults = array(
			'image'   => array(
				'image_selector'         => '.frizzly_container img',
				'networks'               => array( 'pinterest', 'facebook', 'twitter' ),
				'show'                   => 'hover',
				'button_size'            => 'normal',
				'button_shape'           => 'square',
				'button_position'        => 'center',
				'button_margin_top'      => 0,
				'button_margin_left'     => 0,
				'button_margin_right'    => 0,
				'button_margin_bottom'   => 0,
				'desktop_min_height'     => 200,
				'desktop_min_width'      => 200,
				'image_classes'          => '',
				'image_classes_positive' => true,
				'enabled_on'             => '[front],[home],[single],[page],[archive],[search],[category]',
				'disabled_on'            => '',
			),
			'content' => array(
				'align'              => 'left',
				'where'              => 'before_after',
				'pinterest_behavior' => 'user',
				'networks'           => array( 'pinterest', 'facebook', 'twitter' ),
				'button_size'        => 'normal',
				'button_shape'       => 'square',
				'enabled_on'         => '[front],[home],[single],[page],[archive],[search],[category]',
				'disabled_on'        => '',
			),
			'general' => array(
				// Active modules.
				'active_image'           => false,
				'active_content'         => false,
				// Twitter.
				'twitter_handle'         => '',
				'add_handle_to_tweets'   => true,
				// Pinterest.
				'pinterest_source'       => array( 'image_title', 'image_alt', 'post_title' ),
				// Meta tags.
				'meta_open_graph'        => true,
				'meta_twitter'           => true,
				'meta_twitter_card_type' => 'summary_large_image',
			),
		);

		return $defaults;
	}


	/**
	 * Get pinterest source.
	 */
	public function get_pinterest_source() {
		$val = $this->get();

		return $val['general']['pinterest_source'];
	}

	/**
	 * Get pinterest behavior.
	 */
	public function get_pinterest_behavior() {
		$val = $this->get();

		return $val['content']['pinterest_behavior'];
	}
}
