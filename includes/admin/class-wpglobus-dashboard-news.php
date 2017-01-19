<?php

/**
 * Class WPGlobus_Dashboard_News
 * @since 1.7.7
 */
class WPGlobus_Dashboard_News {

	/**
	 * WPGlobus_Dashboard_News constructor.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array(
			$this,
			'action__wp_dashboard_setup'
		) );

		add_filter( 'wpglobus_localize_feed_url', array(
			$this,
			'filter__wpglobus_localize_feed_url'
		), 0, 2 );

	}

	/**
	 * Do not localize feed URL because we have news only in English.
	 *
	 * @param bool      $need_to_localize
	 * @param SimplePie $feed
	 *
	 * @return bool
	 */
	public function filter__wpglobus_localize_feed_url( $need_to_localize, $feed ) {

		return $need_to_localize && WPGlobus::URL_WPGLOBUS_SITE . 'feed/' !== $feed->feed_url;
	}

	/**
	 * Setup the dashboard widget.
	 */
	public function action__wp_dashboard_setup() {
		add_meta_box( 'wpglobus_dashboard_news',
			esc_html__( 'WPGlobus News', 'wpglobus' ),
			array(
				$this,
				'dashboard_widget'
			),
			'dashboard', 'side', 'high'
		);
	}

	/**
	 * Output the widget content.
	 */
	public function dashboard_widget() {
		echo '<div class="rss-widget">';
		wp_widget_rss_output( array(
			'url'          => WPGlobus::URL_WPGLOBUS_SITE . 'feed/',
			'title'        => esc_html__( 'WPGlobus News', 'wpglobus' ),
			'items'        => 3,
			'show_summary' => 1,
			'show_author'  => 0,
			'show_date'    => 1
		) );
		echo '</div>';
	}
}
