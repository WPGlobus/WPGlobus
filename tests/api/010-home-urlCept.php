<?php
/** @global $scenario */
$I = new ApiTester( $scenario );
$I->wantTo( 'verify home_url localization' );

$I->amOnPage( '/api-demo/' );
$I->see( 'api demo', 'h1' );
$I->seeLink( 'home_url', 'http://www.wpglobus.com' );

$I->amOnPage( '/ru/api-demo/' );
/**
 * Note: non-English texts should be entered here with the proper capitalization.
 * Codeception does not apply UTF lowercase.
 */
$I->see( 'Демонстрация API', 'h1' );
$I->seeLink( 'home_url', 'http://www.wpglobus.com/ru' );