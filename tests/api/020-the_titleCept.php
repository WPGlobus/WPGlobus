<?php
/** @global $scenario */
$I = new ApiTester( $scenario );
$I->wantTo( 'verify string parsing' );

$I->amOnPage( '/api-demo/' );
$I->see( 'English', '#filter__the_title' );

$I->amOnPage( '/ru/api-demo/' );
$I->see( 'Русский', '#filter__the_title' );
