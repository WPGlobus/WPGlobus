<?php
/**
 *
 */

if ( defined( 'WPGLOBUS_PHP_COVERAGE_ENABLED' ) ) {
	set_time_limit( 0 );
	$coverage = new PHP_CodeCoverage;
	$coverage->filter()->addDirectoryToBlacklist( $_SERVER['DOCUMENT_ROOT'] . '/../vendor' );
	$coverage->filter()->addDirectoryToBlacklist( $_SERVER['DOCUMENT_ROOT'] );
	$coverage->filter()->addDirectoryToWhitelist( $_SERVER['DOCUMENT_ROOT'] . '/app/plugins/wpglobus/includes' );
	$coverage->start( 'WPG-QA' );
	WPGlobus_QA::api_demo();
	$coverage->stop();
	//$writer = new PHP_CodeCoverage_Report_Clover;
	//$writer->process($coverage, '/tmp/php-coverage/clover.xml');
	$writer = new PHP_CodeCoverage_Report_HTML;
	$writer->process( $coverage, $_SERVER['DOCUMENT_ROOT'] .
	                             '/../internal/php-coverage/wpglobus/code-coverage-report' );
} else {
	WPGlobus_QA::api_demo();
}