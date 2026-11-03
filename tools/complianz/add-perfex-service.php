<?php
// Register the Perfex PRChat widget as a Complianz service (v27.3.4) so it's listed
// in the cookie policy under Preferences. Run as an admin: wp --user=1 eval-file add-perfex-service.php
if ( ! class_exists( 'CMPLZ_SERVICE' ) ) WP_CLI::error( 'Complianz is not active.' );
$service = new CMPLZ_SERVICE( 'Perfex Chat' );
$service->name                = 'Perfex Chat';
$service->serviceType         = 'chat support';
$service->category            = 'preferences';
$service->thirdParty          = true;
$service->sharesData          = true;
$service->sync                = false;
$service->privacyStatementURL = home_url( '/support/privacy-policy/' );
$service->save( false, false );
WP_CLI::success( 'Perfex Chat service ID ' . $service->ID );
