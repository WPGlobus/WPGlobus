<?php
/**
 * @package WPGlobus\MailChimp
 */

/**
 * Class WPGlobus_MailChimp_For_WP
 */

class WPGlobus_MailChimp_For_WP {

	public static function controller() {

		if ( ! is_admin() ) {

			/**
			 * @scope front
			 */
			add_action( 'mc4wp_output_form', array ( __CLASS__, 'filter__data' ), 0 );

			/**
			 * @scope front
			 */
			add_filter( 'get_post_metadata', array ( __CLASS__, 'filter__get_post_metadata' ), 0, 4 );

		}

	}

	/**
	 * Filter meta data for MailChimp for WordPress.
	 * @see get_post_meta() in constructor of MC4WP_Form class ( \mailchimp-for-wp\includes\forms\class-form.php )
	 *
	 * @since 1.6.1
	 * @since 1.6.2
	 *
	 * @param string|array $value     Null is passed. We set the value.
	 * @param int          $object_id Post ID
	 * @param string       $meta_key  Passed by the filter.
	 * @param string|array $single    Meta value, or an array of values.
	 *
	 * @return string|array
	 */
	public static function filter__get_post_metadata( $value, $object_id, $meta_key, $single ) {

		$post = get_post( $object_id );

		if( ! is_object( $post ) || ! isset( $post->post_type ) || $post->post_type !== 'mc4wp-form' ) {
			return $value;
		}

		/** @global wpdb $wpdb */
		global $wpdb;
		$meta_data = $wpdb->get_results( $wpdb->prepare(
			"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d",
			$object_id ) );

		if ( $meta_data ) :
			foreach( $meta_data as $data ) {
				if ( false === strpos( $data->meta_key, 'text_' ) ) {
					$value[ $data->meta_key ][0] = $data->meta_value;
				} else {
					$value[ $data->meta_key ][0] = WPGlobus_Core::text_filter( $data->meta_value, WPGlobus::Config()->language );
				}
			}
		endif;

		return $value;

	}

	/**
	 * Hook for event data
	 *
	 * @since 1.5.4
	 *
	 * @param MC4WP_Form object $form
	 *
	 * @return void
	 */
	public static function filter__data( $form ) {

		$matches = array();
		preg_match_all( '/{:[a-z]{2}}(.*){:}/m', $form->content, $matches );

		$matches = $matches[0];

		/**
		 * @see tab Forms from 'Edit Form' page of MailChimp for WP
		 * for example: wp-admin/admin.php?page=mailchimp-for-wp-forms&view=edit-form&form_id=%POST_ID%
		 */
		foreach( $matches as $match ) {

			$form->content = str_replace( $match, WPGlobus_Core::text_filter($match, WPGlobus::Config()->language), $form->content );

			$form->post->post_content = str_replace(
				$match,
				WPGlobus_Core::text_filter($match, WPGlobus::Config()->language),
				$form->post->post_content
			);

		}

		/**
		 * @see tab Messages from Edit Form page of MailChimp for WP
		 */
		foreach( $form->messages as $type=>$attrs ) {
			if ( is_object( $form->messages[ $type ] ) ) {
				$form->messages[ $type ]->text = WPGlobus_Core::text_filter( $attrs->text, WPGlobus::Config()->language );
			} else if ( is_string( $form->messages[ $type ]	) ) {
				/**
				 * We don't need to filter string because in this case $form->messages array contains keys for meta.
				 * @see $form->post_meta:protected
				 * @see file default-form-messages.php
				 *
				 * @since 1.6.1
				 */
				//$form->messages[ $type ] = WPGlobus_Core::text_filter( $attrs, WPGlobus::Config()->language );
			}
		}

	}

}
