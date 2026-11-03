<?php
// Complianz "configured" flags from live (no license key). Legal page IDs mapped to this site's pages.
$flags = array(
    'cmplz_wizard_completed_once' => 1, 'cmplz_publish_date' => 1769560435, 'cmplz_documents_update_date' => 1769560435,
    'cmplz_purposes_stored' => 1, 'cmplz_serviceTypes_stored' => 1, 'cmplz_run_premium_install' => 'completed',
    'cmplz_synced_cookiedatabase_once' => 1, 'cmplz_sync_cookies_complete' => 1, 'cmplz_sync_services_complete' => 1,
    'cmplz_sync_cookies_after_services_complete' => 1, 'cmplz_first_sync_started' => 1, 'cmplz_cookie_data_verified_date' => 1770421727,
);
foreach ( $flags as $k => $v ) update_option( $k, $v );
$cookie  = get_page_by_path( 'support/cookie-policy' );
$privacy = get_page_by_path( 'support/privacy-policy' );
if ( $cookie )  update_option( 'cmplz_cookie-statement_custom_page', $cookie->ID );
if ( $privacy ) update_option( 'cmplz_privacy-statement_custom_page', $privacy->ID );
WP_CLI::success( 'Complianz flags set; cookie policy ' . ( $cookie->ID ?? '-' ) . ', privacy ' . ( $privacy->ID ?? '-' ) );
