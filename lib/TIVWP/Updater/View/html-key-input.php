<?php
/**
 * @var TIVWP_Updater_Core $this
 */
?>
<tr id="<?php echo esc_attr( sanitize_title( $this->slug . '_licence_key_row' ) ); ?>" class="active plugin-update-tr wpjm-updater-licence-key-tr">
	<td class="plugin-update" colspan="3">
		<div>
			<?php esc_html_e( 'Instance:', 'tivwp-updater' ); ?>
			<?php echo esc_html( $this->instance ); ?>
		</div>
		<div class="wpjm-updater-licence-key">
			<label for="<?php echo sanitize_title( $this->slug ); ?>_licence_key"><?php _e( 'Licence' ); ?>:</label>
			<input type="text" id="<?php echo sanitize_title( $this->slug ); ?>_licence_key" name="<?php echo esc_attr( $this->slug ); ?>_licence_key" placeholder="Licence key"
			       value="<?php echo esc_attr( $this->licence_key ); ?>" />
			<input type="email" id="<?php echo sanitize_title( $this->slug ); ?>_email" name="<?php echo esc_attr( $this->slug ); ?>_email" placeholder="Email address" value="<?php echo esc_attr( $this->email ); ?>" />
			<span class="description"><?php _e( 'Enter your licence key and email and hit return. A valid key is required for automatic updates.' ); ?></span>
			<button type="submit"
			        name="<?php echo esc_attr( $this->slug ); ?>_action"
			        value="activate">Activate
			</button>
		</div>
		<div>
			<?php if ( $this->status ) {
				echo esc_html( $this->status );
			} ?>
		</div>
		<div>
			<?php if ( $this->notifications ) {
				echo implode( '<br>', $this->notifications );
			} ?>
		</div>
	</td>
	<script>
		//		jQuery(function () {
		//			jQuery('tr#<?php //echo esc_attr( $this->slug ); ?>//_licence_key_row').prev().addClass('wpjm-updater-licenced');
		//		});
	</script>
</tr>
