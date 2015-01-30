<?php
/**
 * Controller for the WPGlobus_Upgrade actions/filters.
 * @package WPGlobus
 */

add_action( 'admin_notices', [ 'WPGlobus_Upgrade', 'action__mini_warning' ] );
add_action( 'admin_init', [ 'WPGlobus_Upgrade', 'action__mini_hide_warning' ] );

# --- EOF