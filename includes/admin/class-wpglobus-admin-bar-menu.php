<?php
/**
 * Show WPGlobus menu in admin bar.
 *
 * @since   2.2.7
 * @package WPGlobus\Admin
 */

if ( ! class_exists( 'WPGlobus_Admin_Bar_Menu' ) ) :

	/**
	 * Class WPGlobus_Admin_Bar_Menu.
	 */
	class WPGlobus_Admin_Bar_Menu {

		/**
		 * Available languages.
		 *
		 * @var array
		 */
		protected static $available_languages = array();
		
		/**
		 * @since 2.2.8
		 */ 
		protected static $run_script = true;

		/**
		 * Static constructor.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar Admin bar.
		 */
		public static function construct( $wp_admin_bar ) {

			$user_id = get_current_user_id();

			if ( ! $user_id ) {
				return;
			}

			self::$available_languages = get_available_languages();

			$user_locale = get_user_meta( $user_id, 'locale', true );
			
			add_action( 'admin_footer', array( __CLASS__, 'on__admin_footer' ), 1000 );

			if ( empty( $user_locale ) ) {
				self::add_language_menu( $wp_admin_bar );
			} else {
				if ( ! self::add_info_menu( $wp_admin_bar, $user_locale ) ) {
					self::$run_script = false;
				}
			}

		}

		/**
		 * @since 2.2.8
		 */
		public static function on__admin_footer() {
			
			if ( ! self::$run_script ) {
				return;
			}
			
			?>
			<!--suppress AnonymousFunctionJS, JSUnusedLocalSymbols -->
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready(function ($) {
					wpglobus_select_lang = function (locale) {
						$.post(ajaxurl, {
							action: 'WPGlobus_process_ajax',
							order: {action: 'wpglobus_select_lang', locale: locale}
						}, function (d) {
						})
							.done(function () {
								window.location.reload();
							});
					}
					$('#wpglobus-default-locale').on('click', function (e) {
						wpglobus_select_lang('<?php echo esc_js( WPGlobus::Config()->locale[ WPGlobus::Config()->language ] ); ?>');
					});

				});
				//]]>
			</script>
			<?php			
		}
		
		/**
		 * Language menu with link to `profile.php` page.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar Admin bar.
		 * @param string[]     $user_locale  User locale.
		 */
		public static function add_info_menu( $wp_admin_bar, $user_locale ) {

			$user_profile_language = array_search( $user_locale, WPGlobus::Config()->locale, true );

			if ( ! $user_profile_language ) {
				return false;
			}

			if ( empty( WPGlobus::Config()->language_name[ $user_profile_language ] ) ) {
				return false;
			}

			$img = '';
			if ( ! empty( WPGlobus::Config()->flag[ $user_profile_language ] ) ) {
				$img = '<span class="ab-icon"><img src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $user_profile_language ] . '" alt=""/></span>';
			}

			$wp_admin_bar->add_menu(
				array(
					'id'     => 'wpglobus-language-select',
					'parent' => 'top-secondary',
					'title'  => $img . '<span class="ab-label">' .
								WPGlobus::Config()->language_name[ $user_profile_language ] .
								'</span>',
				)
			);

			$title = esc_html__( 'Language was set on your profile page', 'wpglobus' );
			$wp_admin_bar->add_menu(
				array(
					'parent' => 'wpglobus-language-select',
					'id'     => 'wpglobus-add-languages',
					'title'  => $title,
					'href'   => admin_url( 'profile.php' ),
					'meta'   => array(
						'tabindex' => -1,
					),
				)
			);
			
			return true;
		}

		/**
		 * Language menu in admin bar.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar Admin bar.
		 */
		public static function add_language_menu( $wp_admin_bar ) {

			$available_languages = self::$available_languages;

			$wp_admin_bar->add_menu(
				array(
					'id'     => 'wpglobus-language-select',
					'parent' => 'top-secondary',
					'title'  => '<span class="ab-icon">' .
								'<img alt="" src="' . WPGlobus::Config()->flags_url .
								WPGlobus::Config()->flag[ WPGlobus::Config()->language ] . '"/>' .
								'</span><span class="ab-label">' .
								WPGlobus::Config()->language_name[ WPGlobus::Config()->language ] .
								'</span>',
				)
			);

			$add_more_languages = array();
			foreach ( WPGlobus::Config()->enabled_languages as $language ) :

				if ( WPGlobus::Config()->language === $language ) {
					continue;
				}

				$locale = WPGlobus::Config()->locale[ $language ];

				if ( 'en_US' !== $locale ) {
					if ( ! in_array( $locale, $available_languages, true ) ) {
						$add_more_languages[] = WPGlobus::Config()->language_name[ $language ];
						continue;
					}
				}

				$wp_admin_bar->add_menu(
					array(
						'parent' => 'wpglobus-language-select',
						'id'     => 'wpglobus-' . $language,
						'title'  => '<span><img alt="" src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $language ] . '" /></span>&nbsp;&nbsp;' . WPGlobus::Config()->language_name[ $language ],
						'href'   => admin_url( 'options-general.php' ),
						'meta'   => array(
							'tabindex' => -1,
							'onclick'  => 'wpglobus_select_lang("' . $locale . '");return false;',
						),
					)
				);

			endforeach;

			if ( ! empty( $add_more_languages ) ) {
				$title = esc_html__( 'Add', 'wpglobus' ) . ' (' . implode( ', ', $add_more_languages ) . ')';
				$wp_admin_bar->add_menu(
					array(
						'parent' => 'wpglobus-language-select',
						'id'     => 'wpglobus-add-languages',
						'title'  => $title,
						'href'   => admin_url( 'options-general.php' ),
						'meta'   => array(
							'tabindex' => -1,
						),
					)
				);
			}
			
			return;

		}
	}

endif;
