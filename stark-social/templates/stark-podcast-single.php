<?php
/**
 * Stark podcast episode (v27.0.16)
 * Hero (cover art + details + player) → show notes → transcript →
 * subscribe → related episodes → CTA.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

the_post();
$ep      = stark_podcast_episode();
$show    = stark_podcast_show();
$related = stark_podcast_related( $ep['id'] );
$cta     = array(
    'final_cta' => array(
        'title'  => 'Want this applied to your business?',
        'sub'    => "Tell us what you're working on. We'll tell you what we'd do.",
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

<main id="main" class="stark-page stark-episode">
    <article <?php post_class( 'stark-episode__post' ); ?>>
        <header class="stark-hero stark-hero--episode">
            <div class="stark-home__inner stark-episode__head">
                <?php if ( $ep['cover_id'] ) : ?>
                    <div class="stark-episode__cover"><?php echo stark_attachment_img( $ep['cover_id'], 'medium_large', array( 'fetchpriority' => 'high', 'loading' => 'eager', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper ?></div>
                <?php endif; ?>
                <div class="stark-episode__intro">
                    <p class="stark-eyebrow stark-eyebrow--light"><a href="<?php echo esc_url( get_post_type_archive_link( 'podcast' ) ); ?>"><?php echo esc_html( $show['title'] ); ?></a></p>
                    <h1 class="stark-hero__title stark-article__title"><?php the_title(); ?></h1>
                    <?php if ( $ep['subtitle'] ) : ?>
                        <p class="stark-hero__sub stark-episode__subtitle"><?php echo esc_html( $ep['subtitle'] ); ?></p>
                    <?php endif; ?>
                    <p class="stark-article__meta"><?php echo wp_kses_post( $ep['meta_line'] ); ?></p>
                    <?php echo stark_podcast_player( $ep ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper / plugin output ?>
                </div>
            </div>
        </header>

        <div class="stark-home__inner stark-episode__layout">
            <div class="stark-episode__main">
                <section aria-labelledby="stark-notes-title">
                    <h2 id="stark-notes-title" class="stark-episode__heading">Show notes</h2>
                    <div class="stark-prose"><?php the_content(); ?></div>
                </section>

                <?php if ( $ep['transcript'] ) : ?>
                    <section class="stark-episode__transcript" aria-labelledby="stark-transcript-title">
                        <details>
                            <summary><h2 id="stark-transcript-title" class="stark-episode__heading">Transcript</h2><span class="stark-episode__transcript-hint">Show full transcript</span></summary>
                            <div class="stark-prose stark-episode__transcript-text"><?php echo stark_podcast_transcript_html( $ep['transcript'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per line ?></div>
                        </details>
                    </section>
                <?php endif; ?>
            </div>

            <aside class="stark-episode__aside" aria-label="Subscribe">
                <div class="stark-episode__subscribe">
                    <p class="stark-episode__aside-label">Listen and subscribe</p>
                    <?php get_template_part( 'templates/parts/podcast', 'subscribe' ); ?>
                </div>
            </aside>
        </div>
    </article>

    <?php if ( $related ) : ?>
        <section class="stark-band stark-episode__related" aria-labelledby="stark-related-episodes-title">
            <div class="stark-home__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">Keep listening</p>
                    <h2 id="stark-related-episodes-title" class="stark-section-head__title">More episodes</h2>
                </header>
                <div class="stark-episode-grid">
                    <?php foreach ( $related as $rel ) get_template_part( 'templates/parts/podcast', 'card', $rel ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
