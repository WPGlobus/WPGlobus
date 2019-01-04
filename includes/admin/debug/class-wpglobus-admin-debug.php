<?php
/**
 * WPGlobus / Admin / Debug
 *
 * @package WPGlobus\Admin
 */

// .
if ( ! class_exists( 'WPGlobus_Admin_Debug' ) ) :

	/**
	 * Class WPGlobus_Admin_Debug.
	 *
	 * @since 1.8.1
	 */
	class WPGlobus_Admin_Debug {

		/**
		 * Instance.
		 *
		 * @var WPGlobus_Admin_Debug
		 */
		protected static $instance;

		/**
		 * Get instance.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {

			/**
			 * Action.
			 *
			 * @scope admin
			 * @since 1.8.1
			 */
			add_action( 'admin_print_scripts', array( $this, 'on__admin_scripts' ), 99 );

			/**
			 * Action.
			 *
			 * @scope admin
			 * @since 1.8.1
			 */
			add_action( 'admin_print_styles', array( $this, 'on__admin_styles' ), 99 );

			/**
			 * Action.
			 *
			 * @scope admin
			 * @since 1.8.1
			 */
			add_action( 'admin_footer', array( $this, 'on__admin_footer' ), 9999 );

		}

		/**
		 * Enqueue admin styles.
		 *
		 * @scope  admin
		 * @since  1.8.1
		 */
		public function on__admin_styles() {

			wp_register_style(
				'wpglobus-admin-debug',
				WPGlobus::plugin_dir_url() . 'includes/css/wpglobus-admin-debug.css',
				array(),
				WPGLOBUS_VERSION
			);
			wp_enqueue_style( 'wpglobus-admin-debug' );

		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @scope  admin
		 * @since  1.8.1
		 */
		public function on__admin_scripts() {

			wp_register_script(
				'wpglobus-admin-debug',
				WPGlobus::plugin_dir_url() . 'includes/js/wpglobus-admin-debug' . WPGlobus::SCRIPT_SUFFIX() . '.js',
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-admin-debug' );
			wp_localize_script(
				'wpglobus-admin-debug',
				'WPGlobusAdminDebug',
				array(
					'version' => WPGLOBUS_VERSION,
					'data'    => '',
				)
			);

		}

		/**
		 * Output table.
		 *
		 * @scope  admin
		 * @since  1.8.1
		 */
		public function on__admin_footer() {

			global $wpdb, $post;

			if ( ! is_object( $post ) ) {
				return;
			}

			if ( empty( $post->ID ) || 0 === (int) $post->ID ) {
				return;
			}

			/**
			 * Get metadata.
			 *
			 * @var array $metas
			 */
			$query = $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post->ID );
			$metas = $wpdb->get_results( $query, ARRAY_A );

			?>
			<div id="wpglobus-admin-debug-box" class="" style="display:none;">
				<h4>WPGlobus debug box</h4>
				<?php
				/**
				 * Output metadata.
				 */
				?>
				<table class="table1" cellspacing="0">
					<caption><strong><?php echo '"' . $query . '"'; ?></strong></caption>
					<thead>
					<tr>
						<th><strong>№</strong></th>
						<th><strong>meta</strong></th>
						<th><strong>value</strong></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$order = 1;

					foreach ( $metas as $key=>$meta ) {
						$code = false;
						if ( is_array( $meta ) ) {
							$metas[$key]['meta_key'] = htmlspecialchars( $meta['meta_value'] );
						}
						?>
						<tr>
							<td><?php echo esc_html( $order ); ?></td>
							<td><?php echo esc_html( print_r( $meta[ 'meta_key' ], true ) ); ?></td>
							<?php if ( $code ) { ?>
								<td>
									<pre><?php echo esc_html( print_r( $meta[ 'meta_value' ], true ) ); ?></pre>
								</td>
							<?php } else { ?>
								<td><?php echo esc_html( print_r( $meta[ 'meta_value' ], true ) ); ?></td>
							<?php } ?>
						</tr>
						<?php $order ++; ?>
					<?php } ?>
					</tbody>
				</table>
				<?php
				/**
				 * Get options.
				 */
				global $wpdb;
				$query   = "SELECT * FROM $wpdb->options WHERE option_name LIKE '%wpglobus%'";
				$results = $wpdb->get_results( $query );
				?>
				<table class="table2" cellspacing="0">
					<caption><strong><?php echo '"SELECT * FROM $wpdb->options WHERE option_name LIKE \'%wpglobus%\'"'; ?></strong></caption>
					<caption><?php echo 'Option count: ' . count( $results ); ?></caption>
					<thead>
					<tr>
						<th><strong>Option ID</strong></th>
						<th><strong>Option Name</strong></th>
						<th><strong>Option Value</strong></th>
					</tr>
					</thead>
					<tbody>
					<?php

					$order = 1;

					foreach ( $results as $option_key => $option ) {
						$code = false;
						if ( is_array( $option->option_value ) ) {
							foreach ( $option->option_value as $key => $value ) {
								$option->option_value[ $key ] = htmlspecialchars( $value );
							}
						} elseif ( is_string( $option->option_value ) ) {
							$option->option_value = htmlspecialchars( $option->option_value );
						}
						?>
						<tr>
							<td><?php echo esc_html( $option->option_id ); ?></td>
							<td><?php echo esc_html( print_r( $option->option_name, true ) ); ?></td>
							<?php if ( $code ) { ?>
								<td>
									<pre><?php echo esc_html( print_r( $option->option_value, true ) ); ?></pre>
								</td>
							<?php } else { ?>
								<td><?php echo esc_html( print_r( $option->option_value, true ) ); ?></td>
							<?php } ?>
						</tr>
						<?php $order ++; ?>
					<?php } ?>
					</tbody>
				</table>
			</div>

			<?php
		}

	}

endif;
