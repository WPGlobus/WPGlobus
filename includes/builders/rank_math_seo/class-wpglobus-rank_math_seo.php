<?php
/**
 * File: class-wpglobus-rank_math_seo.php
 *
 * @since 2.4.3
 *
 * @package WPGlobus\Builders\RankMathSEO.
 * @author  Alex Gor(alexgff)
 */
 
if ( ! class_exists( 'WPGlobus_RankMathSEO' ) ) :

	/**
	 * Class WPGlobus_RankMathSEO.
	 */
	class WPGlobus_RankMathSEO extends WPGlobus_Builder {
		
		/**
		 * Current taxonomy.
		 */
		protected $taxonomy = null;
		
		/**
		 * Constructor.
		 */
		public function __construct( $id = null ) {
			
			if ( is_null($id) ) {
				$id = WPGlobus::Config()->builder->get_id();
			}

			parent::__construct( $id );
			
			$this->taxonomy = WPGlobus::Config()->builder->get('taxonomy'); 
			
			if ( ! is_null($this->taxonomy) ) {

				add_action( "{$this->taxonomy}_pre_edit_form", array( $this, 'on__pre_edit_form' ), 10, 2 );
				
				add_action( "{$this->taxonomy}_term_edit_form_top", array( $this, 'on__edit_form' ), 10, 2 );
			}			

		}
		
		/**
		 * Fires before the Edit Term form for all taxonomies.
		 *
		 * @param object $tag      Current taxonomy term object.
		 * @param string $taxonomy Current $taxonomy slug.
		 */
		public function on__pre_edit_form( $tag, $taxonomy ) {
			/**
			 * Init Name and Description in current language.
			 */
			$tag->description	= WPGlobus_Core::text_filter( $tag->description, $this->get_current_language(), WPGlobus::RETURN_EMPTY );
			$tag->name 			= WPGlobus_Core::text_filter( $tag->name, $this->get_current_language(), WPGlobus::RETURN_EMPTY );
		}

		/**
		 * Add language field to taxonomy edit form.
		 * Fires at the beginning of the Edit Term form.
		 *
		 * @param object $tag      Current taxonomy term object.
		 * @param string $taxonomy Current $taxonomy slug.
		 */
		public function on__edit_form( $tag, $taxonomy ) {
			echo '<input type="hidden" name="'.WPGlobus::get_language_meta_key().'" value="'.$this->get_current_language().'">';
		}
	}

endif;

# --- EOF