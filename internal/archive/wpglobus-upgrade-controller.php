<?php
/**
 * Controller for the WPGlobus_Upgrade actions/filters.
 * @package WPGlobus
 */

add_action( 'admin_notices', array( 'WPGlobus_Upgrade', 'action__mini_warning' ) );
add_action( 'admin_init', array( 'WPGlobus_Upgrade', 'action__mini_hide_warning' ) );

/** @todo Move to Upgrade class? */
add_action( 'upgrader_process_complete', array( 'WPGlobus_Config', 'on_activate' ), 10, 2 );

# --- EOF