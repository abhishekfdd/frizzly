<?php
/**
 * Frizzly Admin Post Edit Screen.
 *
 * @package Frizzly
 */

/**
 * Frizzly Admin Post Edit Screen.
 */
class Frizzly_Admin_Post_Edit_Screen {

	/**
	 * Name.
	 *
	 * @var mixed
	 */
	private $name;
	/**
	 * File.
	 *
	 * @var mixed
	 */
	private $file;
	/**
	 * Meta nonce name.
	 *
	 * @var mixed
	 */
	private $meta_nonce_name;
	/**
	 * Meta.
	 *
	 * @var Frizzly_Meta_Social_Data
	 */
	private $meta;
	/**
	 * Networks.
	 *
	 * @var mixed
	 */
	private $networks;
	/**
	 * Version.
	 *
	 * @var mixed
	 */
	private $version;
	/**
	 * Screen hooks.
	 *
	 * @var mixed
	 */
	private $screen_hooks;
	/**
	 * Share options.
	 *
	 * @var Frizzly_Share_Options
	 */
	private $share_options;

	/**
	 * Meta submodules.
	 *
	 * @var string[]
	 */
	private $meta_submodules;

	/**
	 *
	 * Constructor.
	 *
	 * @param mixed $name Name.
	 * @param mixed $version Version.
	 * @param mixed $file File.
	 */
	public function __construct( $name, $version, $file ) {
		$this->name            = $name;
		$this->version         = $version;
		$this->file            = $file;
		$this->meta_nonce_name = 'frizzly_edit_post';
		$this->meta            = new Frizzly_Meta_Social_Data();
		$this->screen_hooks    = array( 'post.php', 'post-new.php' );
		$this->share_options   = new Frizzly_Share_Options();
		$this->meta_submodules = array(
			'image'   => __( 'Image', 'frizzly' ),
			'content' => __( 'Content', 'frizzly' ),
		);

		$this->networks = array(
			'facebook'  => __( 'Facebook', 'frizzly' ),
			'twitter'   => __( 'Twitter', 'frizzly' ),
			'pinterest' => __( 'Pinterest', 'frizzly' ),
		);
	}

	/**
	 *
	 * Add meta box.
	 *
	 * @param mixed $post_type Post type.
	 * @param mixed $post Post.
	 */
	public function add_meta_box( $post_type, $post ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Signature is fixed by the add_meta_boxes action.
		add_meta_box(
			'frizzly-post-meta',
			__( 'Frizzly Post Specific Settings', 'frizzly' ),
			array( $this, 'render_meta' ),
			array( 'post', 'page' )
		);
	}

	/**
	 *
	 * Init.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_meta' ), 10, 3 );
	}

	/**
	 *
	 * Enqueue admin scripts.
	 *
	 * @param mixed $hook Hook.
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( ! in_array( $hook, $this->screen_hooks, true ) ) {
			return;
		}
		$plugin_dir_url = plugin_dir_url( $this->file );

		wp_enqueue_script( 'frizzly-meta-js', $plugin_dir_url . 'js/frizzly.meta.js', array( 'jquery' ), $this->version, true );
		$settings = array(
			'i18n' => array(
				'select_image' => array(
					'title' => __( 'Select image', 'frizzly' ),
					'text'  => __( 'Select', 'frizzly' ),
				),
			),
		);
		wp_localize_script( 'frizzly-meta-js', 'frizzly_meta', $settings );
		wp_enqueue_style( 'frizzly-meta-css', $plugin_dir_url . 'css/frizzly.meta.css', array(), $this->version );
	}

	/**
	 *
	 * Render meta.
	 */
	public function render_meta() {
		global $post;
		$id                     = $post->ID;
		$meta                   = $this->meta->get( $id );
		$recommended_image_size = array(
			'facebook' => array( 1024, 512 ),
			'twitter'  => array( 1200, 630 ),
		);
		?>
		<?php wp_nonce_field( $this->meta_nonce_name, $this->meta_nonce_name ); ?>
		<div class="frizzly-tabs-container">
			<ul class="frizzly-tabs">
				<li class="frizzly-tab-active">
					<a href="#" data-frizzly-id="frizzly-general"><?php esc_html_e( 'General', 'frizzly' ); ?></a>
				</li>
				<?php foreach ( $this->networks as $net_slug => $net_name ) : ?>
					<li>
						<a href="#" data-frizzly-id="frizzly-<?php echo esc_attr( $net_slug ); ?>"><?php echo esc_html( $net_name ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div id="frizzly-general" class="frizzly-tab-panel"><?php $this->render_general_tab( $id ); ?></div>
			<?php foreach ( $this->networks as $net_slug => $net_name ) : ?>
				<div id="frizzly-<?php echo esc_attr( $net_slug ); ?>" class="frizzly-tab-panel" style="display:none">
					<?php $this->render_network_tab( $net_slug, $net_name, $meta[ $net_slug ], $recommended_image_size[ $net_slug ] ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 *
	 * Render general tab.
	 *
	 * @param mixed $id Id.
	 */
	public function render_general_tab( $id ) {
		$share_options      = $this->share_options->get();
		$enabled_submodules = array();
		foreach ( $this->meta_submodules as $sub_slug => $sub_name ) {
			$disabled_list  = $share_options[ $sub_slug ]['disabled_on'];
			$disabled_array = explode( ',', $disabled_list );
			if ( ! in_array( (string) $id, $disabled_array, true ) ) {
				$enabled_submodules[] = $sub_slug;
			}
		}
		?>
		<table class="form-table">
			<tbody>
			<tr>
				<th><?php esc_html_e( 'Active Share Modules', 'frizzly' ); ?></th>
				<td>
					<?php foreach ( $this->meta_submodules as $sub_slug => $sub_name ) : ?>
						<label>
							<input name="frizzly-disabled-<?php echo esc_attr( $sub_slug ); ?>" value="1"
									type="checkbox" <?php checked( in_array( $sub_slug, $enabled_submodules, true ) ); ?>/>
							<?php echo esc_html( $sub_name ); ?>
						</label><br/>
					<?php endforeach; ?>
					<p class="description"><?php esc_html_e( 'Share icons from these share modules will be active in this entry.', 'frizzly' ); ?></p>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 *
	 * Render network tab.
	 *
	 * @param mixed $network_slug Network slug.
	 * @param mixed $network_name Network name.
	 * @param mixed $meta Meta.
	 * @param mixed $featured_image_size Featured image size.
	 */
	public function render_network_tab( $network_slug, $network_name, $meta, $featured_image_size = null ) {
		?>
		<p class="description">
		<?php
		printf(
					/* translators: %s: network name */
			esc_html__( 'If you want to use custom settings for sharing this post on %s, fill the form below. Otherwise, leave it empty.', 'frizzly' ),
			esc_html( $network_name )
		);
		?>
								</p>
		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label for="frizzly_<?php echo esc_attr( $network_slug ); ?>_title">
					<?php
					printf(
					/* translators: %s: network name */
						esc_html__( '%s title', 'frizzly' ),
						esc_html( $network_name )
					);
					?>
										</label>
				</th>
				<td><input class="large-text" id="frizzly_<?php echo esc_attr( $network_slug ); ?>_title"
							name="frizzly_<?php echo esc_attr( $network_slug ); ?>_title"
							value="<?php echo esc_attr( $meta['title'] ); ?>"/>
				</td>
			</tr>
			<tr>
				<th>
					<label for="frizzly_<?php echo esc_attr( $network_slug ); ?>_description">
					<?php
					printf(
					/* translators: %s: network name */
						esc_html__( '%s description', 'frizzly' ),
						esc_html( $network_name )
					);
					?>
										</label>
				</th>
				<td>
					<textarea class="large-text" id="frizzly_<?php echo esc_attr( $network_slug ); ?>_description"
								name="frizzly_<?php echo esc_attr( $network_slug ); ?>_description"><?php echo esc_textarea( $meta['description'] ); ?></textarea>
				</td>
			</tr>
			<tr>
				<th>
					<label for="frizzly_<?php echo esc_attr( $network_slug ); ?>_image">
					<?php
					printf(
					/* translators: %s: network name */
						esc_html__( '%s image', 'frizzly' ),
						esc_html( $network_name )
					);
					?>
										</label>
				</th>
				<td>
					<input id="frizzly_<?php echo esc_attr( $network_slug ); ?>_image" size="64"
							name="frizzly_<?php echo esc_attr( $network_slug ); ?>_image"
							value="<?php echo esc_url( $meta['image'] ); ?>"/>
					<input type="button" class="button frizzly-image-selector"
							data-frizzly-network="<?php echo esc_attr( $network_slug ); ?>"
							value="<?php esc_attr_e( 'Upload image', 'frizzly' ); ?>"/>
					<p class="description">
						<?php
						if ( $featured_image_size ) {
							printf(
								/* translators: 1: network name, 2: recommended width in pixels, 3: recommended height in pixels */
								esc_html__( 'Recommended image size for %1$s is %2$s by %3$s pixels.', 'frizzly' ),
								esc_html( $network_name ),
								esc_html( $featured_image_size[0] ),
								esc_html( $featured_image_size[1] )
							);
						}
						?>
					</p>
				</td>
			</tr>
			<?php if ( 'pinterest' === $network_slug ) : ?>
				<?php $this->render_pinterest_rows( $network_slug, $network_name, $meta ); ?>
			<?php endif; ?>
			</tbody>
		</table>
		<p>
		<?php
		printf(
			/* translators: 1: network name, 2: URL of the global settings screen */
			wp_kses(
				/* translators: 1: network name, 2: URL of the global settings screen */
				__(
					'You can edit global %1$s settings <a href="%2$s" target="_blank">here</a>.',
					'frizzly'
				),
				array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			esc_html( $network_name ),
			esc_url( admin_url( 'admin.php?page=frizzly_settings_general&tab=' . $network_slug ) )
		);
		?>
		</p>
		<?php
	}

	/**
	 *
	 * Render pinterest rows.
	 *
	 * @param mixed $network_slug Network slug.
	 * @param mixed $network_name Network name.
	 * @param mixed $meta Meta.
	 */
	public function render_pinterest_rows( $network_slug, $network_name, $meta ) {
		?>
		<tr>
			<th>
				<label for="frizzly_<?php echo esc_attr( $network_slug ); ?>_image_title">
				<?php
				printf(
					/* translators: %s: network name */
					esc_html__( '%s image title', 'frizzly' ),
					esc_html( $network_name )
				);
				?>
									</label>
			</th>
			<td><input class="large-text" id="frizzly_<?php echo esc_attr( $network_slug ); ?>_image_title"
						name="frizzly_<?php echo esc_attr( $network_slug ); ?>_image_title"
						value="<?php echo esc_attr( $meta['image_title'] ); ?>"/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="frizzly_<?php echo esc_attr( $network_slug ); ?>_image_alt">
				<?php
				printf(
					/* translators: %s: network name */
					esc_html__( '%s image alt text', 'frizzly' ),
					esc_html( $network_name )
				);
				?>
									</label>
			</th>
			<td><input class="large-text" id="frizzly_<?php echo esc_attr( $network_slug ); ?>_image_alt"
						name="frizzly_<?php echo esc_attr( $network_slug ); ?>_image_alt"
						value="<?php echo esc_attr( $meta['image_alt'] ); ?>"/>
			</td>
		</tr>
		<?php
	}

	/**
	 *
	 * Save meta.
	 *
	 * @param mixed $post_id Post id.
	 * @param mixed $post Post.
	 * @param mixed $update Update.
	 */
	public function save_meta( $post_id, $post, $update ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Signature is fixed by the save_post action.
		$return_if = ( ! current_user_can( 'edit_post', $post_id ) ) ||
					( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
					( ! isset( $_POST[ $this->meta_nonce_name ] ) ) ||
					( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $this->meta_nonce_name ] ) ), $this->meta_nonce_name ) );
		if ( $return_if ) {
			return $post_id;
		}

		$this->save_meta_network_tabs( $post_id );
		$this->save_meta_general( $post_id );
	}

	/**
	 *
	 * Save meta general.
	 *
	 * @param mixed $post_id Post id.
	 */
	public function save_meta_general( $post_id ) {
		$options = $this->share_options->get();
		foreach ( $this->meta_submodules as $sub_slug => $sub_name ) {
			// Nonce is verified in save_meta(), the only caller of this method.
			$should_be_in_array = ! isset( $_POST[ 'frizzly-disabled-' . $sub_slug ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$disabled_str       = trim( $options[ $sub_slug ]['disabled_on'] );
			$disabled_array     = explode( ',', $disabled_str );
			$is_in_array        = in_array( (string) $post_id, $disabled_array, true );

			if ( $should_be_in_array === $is_in_array ) {
				continue;
			}

			if ( $should_be_in_array ) {
				$disabled_array[] = (string) $post_id;
			} else {
				$disabled_array = array_diff( $disabled_array, array( (string) $post_id ) );
			}
			$disabled_str               = implode( ',', $disabled_array );
			$sub_options['disabled_on'] = $disabled_str;
			$options[ $sub_slug ]       = $sub_options;
		}
		$this->share_options->update( $options );
	}

	/**
	 *
	 * Save meta network tabs.
	 *
	 * @param mixed $post_id Post id.
	 */
	public function save_meta_network_tabs( $post_id ) {
		$settings = array(
			'facebook'  => $this->get_network_settings( 'facebook' ),
			'twitter'   => $this->get_network_settings( 'twitter' ),
			'pinterest' => $this->get_network_settings( 'pinterest' ),
		);
		$this->meta->update( $post_id, $settings );
	}

	/**
	 *
	 * Get network settings.
	 *
	 * @param mixed $network_name Network name.
	 */
	public function get_network_settings( $network_name ) {
		$title_name       = sprintf( 'frizzly_%s_title', $network_name );
		$description_name = sprintf( 'frizzly_%s_description', $network_name );
		$image_name       = sprintf( 'frizzly_%s_image', $network_name );
		$image_title      = sprintf( 'frizzly_%s_image_title', $network_name );
		$image_alt        = sprintf( 'frizzly_%s_image_alt', $network_name );

		// Nonce is verified in save_meta(), which is the only caller of this method.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$arr = array(
			'title'       => ! empty( $_POST[ $title_name ] )
				? sanitize_text_field( wp_unslash( $_POST[ $title_name ] ) )
				: '',
			'description' => ! empty( $_POST[ $description_name ] )
				? sanitize_textarea_field( wp_unslash( $_POST[ $description_name ] ) )
				: '',
			'image'       => ! empty( $_POST[ $image_name ] )
				? esc_url_raw( wp_unslash( $_POST[ $image_name ] ) )
				: '',
		);
		if ( 'pinterest' !== $network_name ) {
			return $arr;
		}
		$arr['image_title'] = ! empty( $_POST[ $image_title ] )
			? sanitize_text_field( wp_unslash( $_POST[ $image_title ] ) )
			: '';
		$arr['image_alt']   = ! empty( $_POST[ $image_alt ] )
			? sanitize_text_field( wp_unslash( $_POST[ $image_alt ] ) )
			: '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return $arr;
	}
}
