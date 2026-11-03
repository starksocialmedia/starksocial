<?php
/**
 * Stark podcast archive (v27.0.16) — /podcast/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$show = stark_podcast_show();
$cta  = array(
    'final_cta' => array(
        'title'  => 'Rather talk it through?',
        'sub'    => "The podcast covers the general stuff. For your business specifically, we're one call away.",
        'button' => array( 'url' => '/contact/', 'title' => 'Get in touch', 'target' => '' ),
    ),
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

<main id="main" class="stark-page stark-podcast">
    <section class="stark-hero stark-hero--podcast" aria-labelledby="stark-podcast-title">
        <div class="stark-home__inner">
            <div class="stark-hero__content">
                <p class="stark-eyebrow stark-eyebrow--light">Podcast · <?php echo esc_html( $show['subtitle'] ); ?></p>
                <h1 id="stark-podcast-title" class="stark-hero__title"><?php echo esc_html( $show['title'] ); ?></h1>
                <p class="stark-hero__sub"><?php echo esc_html( $show['summary'] ); ?></p>
                <?php get_template_part( 'templates/parts/podcast', 'subscribe', array( 'variant' => 'light' ) ); ?>
            </div>
        </div>
    </section>

    <?php get_template_part( 'templates/parts/breadcrumbs' ); ?>

    <section class="stark-band stark-podcast__list" aria-labelledby="stark-episodes-title">
        <div class="stark-home__inner">
            <h2 id="stark-episodes-title" class="screen-reader-text">Episodes</h2>
            <?php if ( have_posts() ) : ?>
                <div class="stark-episode-grid">
                    <?php while ( have_posts() ) : the_post(); get_template_part( 'templates/parts/podcast', 'card', get_the_ID() ); endwhile; ?>
                </div>
                <?php
                $pagination = paginate_links( array(
                    'type'      => 'list',
                    'mid_size'  => 1,
                    'prev_text' => '<span aria-hidden="true">&larr;</span> Newer',
                    'next_text' => 'Older <span aria-hidden="true">&rarr;</span>',
                ) );
                if ( $pagination ) : ?>
                    <nav class="stark-pagination" aria-label="Episodes pagination"><?php echo wp_kses_post( $pagination ); ?></nav>
                <?php endif; ?>
            <?php else : ?>
                <p class="stark-blog__empty">Episodes are on the way.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
