<?php
/**
 * Stark service page (v27.0.4)
 * Loaded for top-level pages whose slug is in stark_get_services().
 * Location pages (stark-location.php) set $stark_ctx and include this file.
 * Sections render only when their ACF fields have content.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$ctx = isset( $stark_ctx ) ? $stark_ctx : stark_service_context( get_queried_object_id() );

$sections = array( 'hero', 'credibility', 'included', 'process', 'case-study', 'faq', 'related', 'cta' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( ! empty( $ctx['draft'] ) ) : ?>
    <div class="stark-draft-banner" role="note"><strong>[DRAFT - needs Nathan review]</strong> <span class="stark-draft-banner__more">Set <em>Copy status</em> to Approved in the page editor when the copy is final.</span></div>
<?php endif; ?>

<main id="main" class="stark-page stark-service">
    <?php foreach ( $sections as $section ) get_template_part( 'templates/parts/service', $section, $ctx ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
