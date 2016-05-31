<?php
/**
 * @package WPGlobus
 */

/**
 * Class WPGlobus_MailChimp_For_WP
 */

class WPGlobus_MailChimp_For_WP {
	
	public static function controller() {
		add_action( 'mc4wp_output_form', array ( __CLASS__, 'filter__data' ), 0 );
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
			$form->messages[ $type ]->text = WPGlobus_Core::text_filter( $attrs->text, WPGlobus::Config()->language );
		}			
		
	}

}
