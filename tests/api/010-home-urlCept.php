<?php
/** @global $scenario */
$I = new ApiTester( $scenario );
$I->wantTo( 'verify home_url localization' );

$I->amOnPage( '/api-demo/' );
$I->see( 'api demo' );
$I->seeLink( 'home_url' );
$I->seeLink( 'home_url', 'http://www.wpglobus.com' );

$I->amOnPage( '/ru/api-demo/' );
$I->see( 'api demo' );
$I->seeLink( 'home_url' );
$I->seeLink( 'home_url', 'http://www.wpglobus.com/ru' );