<?php
/**
 * @package WPGlobus\The-Events-Calendar
 */

/**
 * Class WPGlobus_The_Events_Calendar
 */

class WPGlobus_The_Events_Calendar {

	/**
	 * Filter for event data
	 *
	 * @since 1.2.2
	 * @param array $json
	 * @param WP_Post object $event
	 * @param array $additional
	 *
	 * @return array
	 */
	public static function filter__events_data( $json, $event, $additional ) {

		if ( ! empty( $json['title'] ) ) {
			$json['title']	= WPGlobus_Core::text_filter( $json['title'], WPGlobus::Config()->language );
		}
		return $json;

	}

}
