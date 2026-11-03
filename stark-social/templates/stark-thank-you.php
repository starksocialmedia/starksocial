<?php
/**
 * Stark thank-you page (v27.3.2) — /thank-you/, the contact form's confirmation.
 * noindex (set by tools/gravity-forms/import-contact-form.php).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$hero = array(
    'eyebrow'       => 'Message received',
    'title'         => 'Thanks — we’ve got it.',
    'thesis'        => 'We read every message ourselves and respond within one business day. You’ll hear directly from the people doing the work.',
    'cta_primary'   => array( 'url' => '/services/', 'title' => 'Explore our services', 'target' => '' ),
    'cta_secondary' => array( 'url' => '/work/', 'title' => 'See our work', 'target' => '' ),
);

$next = array(
    'extra_sections' => array( array(
        'eyebrow' => 'While you wait',
        'heading' => 'A few places to start',
        'intro'   => '',
        'note'    => '',
        'items'   => array(
            array( 'title' => 'Services', 'description' => 'How we approach websites, social, SEO, ads, content and brand.', 'url' => '/services/' ),
            array( 'title' => 'Our work', 'description' => 'Case studies from recent projects.', 'url' => '/work/' ),
            array( 'title' => 'The blog', 'description' => 'Practical marketing advice for small and mid-sized businesses.', 'url' => '/blog/' ),
        ),
    ) ),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-thank-you">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>
    <?php get_template_part( 'templates/parts/service', 'extra', $next ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
