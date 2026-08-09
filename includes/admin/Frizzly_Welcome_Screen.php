<?php
/**
 * Frizzly Welcome Screen.
 *
 * @package Frizzly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frizzly Welcome Screen.
 */
class Frizzly_Welcome_Screen {
	/**
	 * Minimum capability.
	 *
	 * @var mixed
	 */
	private $minimum_capability = 'manage_options';
	/**
	 * Transient name.
	 *
	 * @var mixed
	 */
	private $transient_name = '_frizzly_activation_redirect';
	/**
	 * Plugin name.
	 *
	 * @var mixed
	 */
	private $plugin_name;
	/**
	 * File.
	 *
	 * @var mixed
	 */
	private $file;
	/**
	 * Version.
	 *
	 * @var mixed
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @param mixed $file File.
	 * @param mixed $version Version.
	 */
	public function __construct( $file, $version ) {
		$this->file        = $file;
		$this->version     = $version;
		$this->plugin_name = 'Frizzly';
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 11 );
	}

	/**
	 * Admin menus.
	 */
	public function admin_menus() {
		// About page.
		add_dashboard_page(
			__( 'Welcome to Frizzly', 'frizzly' ),
			__( 'Welcome to Frizzly', 'frizzly' ),
			$this->minimum_capability,
			'frizzly-welcome',
			array( $this, 'welcome_message' )
		);

		// Now remove them from the menus so plugins that allow customizing the admin menu don't show them.
		remove_submenu_page( 'index.php', 'frizzly-welcome' );
	}

	/**
	 * Welcome message.
	 */
	public function welcome_message() {
		?>
		<div class="wrap about-wrap">
			<h1>
			<?php
			printf(
				/* translators: 1: plugin name, 2: plugin version */
				esc_html__( 'Welcome to %1$s&nbsp;%2$s', 'frizzly' ),
				esc_html( $this->plugin_name ),
				esc_html( $this->version )
			);
			?>
				</h1>

			<p class="about-text">
			<?php
			printf(
				/* translators: %s: plugin name */
				esc_html__( 'Thank you for updating to the latest version! %s helps your readers share your posts and images using a number of sharing networks.', 'frizzly' ),
				esc_html( $this->plugin_name )
			);
			?>
									</p>

			<hr/>

			<div class="feature-section one-col">
				<h2><?php esc_html_e( 'Settings', 'frizzly' ); ?></h2>
				<p class="lead-description"><?php esc_html_e( 'The most important part of the plugin is the settings panel.', 'frizzly' ); ?></p>
			</div>

			<div class="feature-section two-col">
				<div class="col">
					<h3><?php esc_html_e( 'Finding the settings panel', 'frizzly' ); ?></h3>
					<p>
					<?php
					printf(
						wp_kses(
							/* translators: %s: plugin name */
							__( 'You can find the plugin\'s settings panel in the <b>Settings</b> submenu under the name <b>%s</b>. There you can find all the settings the plugin allows you to adjust. All settings are divided into several tabs so you can find what you\'re looking for easily.', 'frizzly' ),
							array(
								'a' => array(
									'href'   => array(),
									'class'  => array(),
									'target' => array(),
								),
								'b' => array(),
							)
						),
						esc_html( $this->plugin_name )
					);
					?>
						</p>
				</div>
				<div class="col">
					<?php
					$file_name = 'settings_link.png';
					?>
					<img src="<?php echo esc_url( plugin_dir_url( $this->file ) . '/images/' . $file_name ); ?>"
						title="<?php esc_attr_e( 'Settings link', 'frizzly' ); ?>"/>
				</div>
			</div>
			<div class="feature-section one-col">
				<h3><?php esc_html_e( 'Everything at hand', 'frizzly' ); ?></h3>
				<p style="margin-left: 0; margin-right: 0;"><?php esc_html_e( 'You can find all the links mentioned below in the settings panel.', 'frizzly' ); ?></p>
				<p style="text-align: center;"><img
							src="<?php echo esc_url( plugin_dir_url( $this->file ) . '/images/settings_tabs.png' ); ?>"
							title="<?php esc_attr_e( 'Settings tabs', 'frizzly' ); ?>"/>
				</p>
				<h3><?php esc_html_e( 'General', 'frizzly' ); ?></h3>
				<p style="margin-left: 0; margin-right: 0;"><?php esc_html_e( 'In this tab you can do a variety of things. The most important is the ability to activate and deactivate various modules of the plugin. Besides that, plugin-wide settings like meta data or social network details are available in this tab too.', 'frizzly' ); ?></p>
				<?php
				printf(
					wp_kses(
						/* translators: %s: URL of the General settings tab */
						__( '<a href="%s" class="button button-primary">Go to General settings</a>', 'frizzly' ),
						array(
							'a' => array(
								'href'   => array(),
								'class'  => array(),
								'target' => array(),
							),
							'b' => array(),
						)
					),
					esc_url( admin_url( 'options-general.php?page=frizzly_settings' ) )
				);
				?>
			</div>
			<div class="feature-section two-col">
				<div class="col">
					<h3><?php esc_html_e( 'Image', 'frizzly' ); ?></h3>
					<p><?php esc_html_e( 'Image module is responsible for adding share buttons over your images. You can configure which buttons you want to show up on your images, how should they look like and where they should be placed. You can also set up which images should have the buttons available  and on which pages of your website should this module be active.', 'frizzly' ); ?></p>
					<p style="text-align: center;"><img
								src="<?php echo esc_url( plugin_dir_url( $this->file ) . '/images/image_module.jpg' ); ?>"
								title="<?php esc_attr_e( 'Settings tabs', 'frizzly' ); ?>"/>
					</p>
					<?php
					printf(
						wp_kses(
							/* translators: %s: URL of the Image settings tab */
							__( '<a href="%s" class="button button-primary">Go to Image module settings</a>', 'frizzly' ),
							array(
								'a' => array(
									'href'   => array(),
									'class'  => array(),
									'target' => array(),
								),
								'b' => array(),
							)
						),
						esc_url( admin_url( 'options-general.php?page=frizzly_settings&tab=image' ) )
					);
					?>
				</div>
				<div class="col">
					<h3><?php esc_html_e( 'Content', 'frizzly' ); ?></h3>
					<p><?php esc_html_e( 'Content module allows you to add share buttons before, after or before and after your content. You can configure which buttons should show up and how they should look like. You can also choose on which pages of your website should this module be active.', 'frizzly' ); ?></p>
					<p style="text-align: center;"><img
								src="<?php echo esc_url( plugin_dir_url( $this->file ) . '/images/content_module.jpg' ); ?>"
								title="<?php esc_attr_e( 'Settings tabs', 'frizzly' ); ?>"/>
					</p>
					<?php
					printf(
						wp_kses(
							/* translators: %s: URL of the Content settings tab */
							__( '<a href="%s" class="button button-primary">Go to Content module settings</a>', 'frizzly' ),
							array(
								'a' => array(
									'href'   => array(),
									'class'  => array(),
									'target' => array(),
								),
								'b' => array(),
							)
						),
						esc_url( admin_url( 'options-general.php?page=frizzly_settings&tab=content' ) )
					);
					?>
				</div>
			</div>

			<hr/>

			<div class="feature-section one-col">
				<h2><?php esc_html_e( 'Finding help', 'frizzly' ); ?></h2>
				<p class="lead-description"><?php esc_html_e( 'If you\'re stuck and can\'t get the plugin to work the way you want it to, get help!', 'frizzly' ); ?></p>
			</div>

			<div class="feature-section two-col">
				<div class="col">
					<h3><?php esc_html_e( 'Documentation', 'frizzly' ); ?></h3>
					<p>
					<?php
					printf(
						wp_kses(
							/* translators: %s: documentation URL */
							__( 'If you are having difficulties with some aspects of the plugin, the first place to look for help is <a href="%s" target="_blank">the documentation</a> of the plugin. Chances are you will find what you are looking for there. If something is not covered in the documentation, use the support forum.', 'frizzly' ),
							array(
								'a' => array(
									'href'   => array(),
									'class'  => array(),
									'target' => array(),
								),
								'b' => array(),
							)
						),
						esc_url( 'https://highfiveplugins.com/frizzly/frizzly-documentation/' )
					);
					?>
						</p>
				</div>
				<div class="col">
					<h3><?php esc_html_e( 'Support', 'frizzly' ); ?></h3>
					<p>
					<?php
						printf(
							wp_kses(
								/* translators: %s: support forum URL */
								__( 'You can find support <a href="%s" target="_blank">in the support forum</a>. When posting to the support forum, make sure you include the URL of your website.', 'frizzly' ),
								array(
									'a' => array(
										'href'   => array(),
										'class'  => array(),
										'target' => array(),
									),
									'b' => array(),
								)
							),
							esc_url( 'https://wordpress.org/support/plugin/frizzly' )
						);
					?>
					</p>
				</div>
			</div>
			<hr/>
			<div class="feature-section one-col">
				<h2><?php esc_html_e( 'Next steps', 'frizzly' ); ?></h2>
				<?php
				$next_steps_text = sprintf(
					wp_kses(
						/* translators: %s: URL of the General settings tab */
						__( 'Go to <a href="%s">General settings</a> and choose which modules you wish to run.', 'frizzly' ),
						array(
							'a' => array(
								'href'   => array(),
								'class'  => array(),
								'target' => array(),
							),
							'b' => array(),
						)
					),
					esc_url( admin_url( 'options-general.php?page=frizzly_settings' ) )
				);
				?>
				<p class="lead-description">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built above with wp_kses() and esc_url().
					echo $next_steps_text;
					?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Redirect.
	 */
	public function redirect() {
		if ( ! get_transient( $this->transient_name ) ) {
			return;
		}
		delete_transient( $this->transient_name );

		// Bail if activating from network, or bulk.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading a flag WordPress itself sets on the plugins screen; no form data is processed.
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}
		wp_safe_redirect( admin_url( 'index.php?page=frizzly-welcome' ) );
		exit;
	}
}
