<?php
/** @global $scenario */
$I = new ApiTester( $scenario );
$I->wantTo( 'verify string tagging' );

$I->amOnPage( '/api-demo/' );
$I->see( '{:en}English{:}{:ru}Русский{:}', '#tag_text' );

$I->amOnPage( '/ru/api-demo/' );
$I->see( '{:en}English{:}{:ru}Русский{:}', '#tag_text' );
