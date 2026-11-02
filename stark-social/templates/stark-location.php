<?php
/**
 * Stark location page (v27.0.5)
 * Service + market page nested under a service page (/web-design/antelope-valley/).
 * Builds the parent service context with local overlays, then renders through
 * the service template so both page types share one set of section partials.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$stark_ctx = stark_location_context( get_queried_object_id() );

include get_stylesheet_directory() . '/templates/stark-service.php';
