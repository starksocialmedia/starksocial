<?php
/**
 * Stark legal page (v27.0.15)
 * Title-only hero, long-form content (.stark-prose), side nav of legal pages.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

the_post();
$current = get_post_field( 'post_name', get_the_ID() );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-legal">
    <header class="stark-hero stark-hero--legal">
        <div class="stark-home__inner">
            <div class="stark-hero__content">
                <p class="stark-eyebrow stark-eyebrow--light">Legal</p>
                <h1 class="stark-hero__title stark-legal__title"><?php the_title(); ?></h1>
            </div>
        </div>
    </header>

    <div class="stark-home__inner stark-legal__layout">
        <nav class="stark-legal__nav" aria-label="Legal pages">
            <p class="stark-legal__nav-label">Legal</p>
            <ul>
                <?php foreach ( stark_get_legal_pages() as $slug => $label ) :
                    $legal_page = get_page_by_path( 'support/' . $slug ) ?: get_page_by_path( $slug );
                    if ( ! $legal_page ) continue; ?>
                    <li><a href="<?php echo esc_url( get_permalink( $legal_page ) ); ?>"<?php echo $slug === $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a></li>
                <?php endforeach; ?>
            </ul>
            <p class="stark-legal__contact">Questions? <a href="/contact/">Contact us</a></p>
        </nav>

        <article class="stark-legal__content stark-prose">
            <?php the_content(); ?>
        </article>
    </div>
</main>

<?php wp_footer(); ?>
</body>
</html>
