<?php
/**
 * @package WPGlobus\RevSlider
 * @since 1.5.0
 */


if ( ! class_exists( 'WPGlobus_RevSlider' ) ) :

	/**
	 * Class WPGlobus_RevSlider
	 */
	class WPGlobus_RevSlider {

		public static $links = array();

		public static function controller() {

			/**
			 * @see 'revslider_add_layer_html' action
			 */
			add_action( 'revslider_add_layer_html', array ( __CLASS__, 'action__add_layer_html' ), 99, 2 );

			add_action( 'wp_footer', array ( __CLASS__, 'action__wp_footer' ), 99 );

			if ( ! function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {

				function qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage(  $text ) {

					/**
					 * Revslider
					 */
					if ( empty( $text ) ) {
						return $text;
					}

					return WPGlobus_Core::text_filter( $text, WPGlobus::Config()->language );

				}

			}

		}

		/**
		 * @since 1.5.4
		 */
		public static function action__add_layer_html( $slider, $slide ) {
			/**
			 * @todo find how to translate field "params" from "wp_revslider_slides" table
			 * @see output.class.php:620
			 * @see db.class.php:130
			 */
			$link = $slide->getParam( 'link', '' );
			if ( ! empty( $link ) ) {
				self::$links[ $link ] = WPGlobus_Core::text_filter( $link, WPGlobus::Config()->language );
			}
		}

		/**
		 * @since 1.5.4
		 */
		public static function action__wp_footer() {
			if ( empty( self::$links ) ) {
				return;
			}
		?>
<script type="text/javascript">
//<![CDATA[
var WPGlobusRevSliderLinks = <?php echo json_encode( self::$links ); ?>;
jQuery('.rev_slider li').each( function(i,e){
	var $e = jQuery(e);
	var link = $e.data( 'link' );
	jQuery.each( WPGlobusRevSliderLinks, function( source, lnk ) {
		if ( link == source ) {
			$e.data( 'link', lnk );
			return false;
		}
	});
});
//]]>
</script>
		<?php
		}

	}

endif;
