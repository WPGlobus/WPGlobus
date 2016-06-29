<?php
/**
 * @var TIVWP_Updater_Core $this
 */
$_slug = sanitize_title( $this->slug );
?>
<tr id="<?php echo $_slug; ?>_licence_key_row" class="active plugin-update-tr">
	<td class="plugin-update" colspan="3">
		<div>
			<?php esc_html_e( 'A valid license is required for automatic updates.', 'tivwp-updater' ); ?>
		</div>
		<div>
			<?php esc_html_e( 'Instance', 'tivwp-updater' ); ?>:
			<?php echo esc_html( $this->instance ); ?>
		</div>
		<div>
			<label for="<?php echo $_slug; ?>_licence_key">
				<?php esc_html_e( 'License', 'tivwp-updater' ); ?>:
			</label>
			<input type="text" id="<?php echo $_slug; ?>_licence_key"
			       name="<?php echo $_slug; ?>_licence_key"
			       placeholder="<?php esc_attr_e( 'key', 'tivwp-updater' ); ?>"
			       value="<?php echo esc_attr( $this->licence_key ); ?>" />
			<input type="email" id="<?php echo $_slug; ?>_email"
			       name="<?php echo $_slug; ?>_email"
			       placeholder="<?php esc_attr_e( 'email', 'tivwp-updater' ); ?>"
			       value="<?php echo esc_attr( $this->email ); ?>" />
			<button type="submit"
			        name="<?php echo $_slug; ?>_action"
			        value="status">
				<?php esc_html_e( 'Validate', 'tivwp-updater' ); ?>
			</button>
			<button type="submit"
			        name="<?php echo $_slug; ?>_action"
			        value="activate">
				<?php esc_html_e( 'Activate', 'tivwp-updater' ); ?>
			</button>
			<button type="submit"
			        name="<?php echo $_slug; ?>_action"
			        value="deactivate">
				<?php esc_html_e( 'Deactivate', 'tivwp-updater' ); ?>
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
</tr>
