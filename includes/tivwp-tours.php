<?php
/**
 * Own filter
 * @todo This is a non-standard approach. Should discuss it later.
 */
add_filter( 'wpglobus_get_terms', [ '_WPGlobus_Internal_Example', 'own_filter__get_terms' ], 10, 2 );

/**
 * Class _WPGlobus_Internal_Example
 */
class _WPGlobus_Internal_Example {
	/**
	 * Пример функции, которая создаёт свой tag для CPT и корректно работает с WPGlobus
	 * взята из Plugin Name: TIVWP-Tours
	 */
	/**
	 * The first parameter is ignored. We have it here only because WP requires the 1st parameter in filter
	 *
	 * @param mixed  $terms Ignored
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public static function own_filter__get_terms(
		/** @noinspection PhpUnusedParameterInspection */
		$terms, $taxonomy
	) {
		return WPGlobus::_get_terms( $taxonomy );
	}


	/**
	 * Meta box to select only one taxonomy value
	 *
	 * @param WP_Post $post
	 * @param array   $box
	 */
	public function single_taxonomy_select_meta_box( WP_Post $post, Array $box ) {

		$taxonomy = $box['args']['taxonomy'];

		$term_name_of_the_current_post = '';
		$term_of_the_current_post      = wp_get_object_terms( $post->ID, $taxonomy, [ 'fields' => 'names' ] );
		if ( isset( $term_of_the_current_post[0] ) ) {
			$term_name_of_the_current_post = $term_of_the_current_post[0];
		}

		$all_term_names = apply_filters( 'wpglobus_get_terms', get_terms( $taxonomy, [
			'fields'     => 'names',
			'hide_empty' => false
		] ), $taxonomy );

		?>
		<label>
			<?php esc_html_e( 'Please choose:' ); ?>
			<!--suppress QuirksModeInspectionTool -->
			<select name="tax_input[<?php echo esc_attr( $taxonomy ); ?>]">
				<?php foreach ( $all_term_names as $term_name ) : ?>
					<option
						value="<?php echo $term_name; ?>" <?php selected( $term_name_of_the_current_post, $term_name ); ?>>
						<?php echo esc_html( $term_name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
	<?php
	}
}