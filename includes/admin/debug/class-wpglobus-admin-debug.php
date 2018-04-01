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

			global $post;

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
			$metas = get_metadata( 'post', $post->ID );

			?>
			<div id="wpglobus-admin-debug-box" class="" style="display:none;">
				<h4>WPGlobus debug box</h4>
				<?php
				/**
				 * Get metadata.
				 */
				?>
				<table class="table1" cellspacing="0">
					<caption><?php echo 'get_metadata( "post", ' . esc_html( $post->ID ) . ' )'; ?></caption>
					<thead>
					<tr>
						<th>№</th>
						<th>meta</th>
						<th>value</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$order = 1;

					foreach ( $metas as $meta_key => $meta ) {
						$code = false;
						if ( is_array( $meta ) ) {
							foreach ( $meta as $key => $val ) {
								$meta[ $key ] = htmlspecialchars( $val );
							}
						} elseif ( is_string( $meta ) ) {
							$meta = htmlspecialchars( $meta );
						}
						?>
						<tr>
							<td><?php echo esc_html( $order ); ?></td>
							<td><?php echo esc_html( print_r( $meta_key, true ) ); ?></td>
							<?php if ( $code ) { ?>
								<td>
									<pre><?php echo esc_html( print_r( $meta, true ) ); ?></pre>
								</td>
							<?php } else { ?>
								<td><?php echo esc_html( print_r( $meta, true ) ); ?></td>
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
					<caption><?php echo '"SELECT * FROM $wpdb->options WHERE option_name LIKE \'%wpglobus%\'"'; ?></caption>
					<caption><?php echo 'Option count: ' . count( $results ); ?></caption>
					<thead>
					<tr>
						<th>Option ID</th>
						<th>Option Name</th>
						<th>Option Value</th>
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
