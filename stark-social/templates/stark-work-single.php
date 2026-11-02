<?php
/**
 * Stark case study (v27.0.12) — /portfolio/{slug}/
 * Hero → large image → project strip → 3-stat row → overview / challenge /
 * approach / results → gallery → testimonial → prev/next → CTA.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

the_post();
$work = stark_portfolio_data();

$sections = array(
    'overview'  => 'Overview',
    'challenge' => 'The challenge',
    'approach'  => 'Our approach',
    'outcome'   => 'Results',
);

$prev = get_adjacent_post( false, '', true );
$next = get_adjacent_post( false, '', false );

$cta = array(
    'final_cta' => array(
        'title'  => 'Want results like these?',
        'sub'    => "Tell us where your business is stuck. We'll show you how we'd fix it.",
        'button' => array( 'url' => '/contact/', 'title' => 'Start a project', 'target' => '' ),
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

<main id="main" class="stark-page stark-case-study">
    <article <?php post_class( 'stark-case-study__post' ); ?>>

        <header class="stark-hero stark-hero--article">
            <div class="stark-home__inner">
                <div class="stark-hero__content stark-article__head">
                    <p class="stark-eyebrow stark-eyebrow--light"><a href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ); ?>">Case study</a></p>
                    <h1 class="stark-hero__title stark-article__title"><?php echo esc_html( $work['client'] ); ?></h1>
                    <?php if ( $work['summary'] ) : ?>
                        <p class="stark-hero__sub"><?php echo esc_html( $work['summary'] ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <?php if ( $work['hero_id'] ) : ?>
            <figure class="stark-article__image stark-case-study__image">
                <?php echo wp_get_attachment_image( $work['hero_id'], 'full', false, array( 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '(min-width: 1300px) 1240px, 100vw' ) ); ?>
            </figure>
        <?php endif; ?>

        <div class="stark-home__inner">
            <dl class="stark-case-study__strip">
                <div><dt>Client</dt><dd><?php echo esc_html( $work['client'] ); ?></dd></div>
                <?php if ( $work['industry'] ) : ?><div><dt>Industry</dt><dd><?php echo esc_html( $work['industry'] ); ?></dd></div><?php endif; ?>
                <?php if ( $work['year'] ) : ?><div><dt>Year</dt><dd><?php echo esc_html( $work['year'] ); ?></dd></div><?php endif; ?>
                <?php if ( $work['services'] ) : ?>
                    <div><dt>Services</dt><dd>
                        <?php echo implode( ', ', array_map( function ( $slug, $name ) {
                            return '<a href="' . esc_url( '/' . $slug . '/' ) . '">' . esc_html( $name ) . '</a>';
                        }, array_keys( $work['services'] ), $work['services'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per item ?>
                    </dd></div>
                <?php endif; ?>
                <?php if ( $work['website'] ) : ?>
                    <div><dt>Website</dt><dd><a href="<?php echo esc_url( $work['website'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( preg_replace( '#^https?://(www\.)?|/$#', '', $work['website'] ) ); ?> &nearr;</a></dd></div>
                <?php endif; ?>
            </dl>

            <?php if ( $work['results'] ) : ?>
                <dl class="stark-case-study__stats stark-case-study__stats--<?php echo esc_attr( count( $work['results'] ) ); ?>">
                    <?php foreach ( $work['results'] as $stat ) : ?>
                        <div class="stark-case-study__stat">
                            <dt><?php echo esc_html( $stat['label'] ); ?></dt>
                            <dd><?php echo esc_html( $stat['value'] ); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
        </div>

        <?php foreach ( $sections as $key => $label ) :
            if ( empty( $work[ $key ] ) ) continue; ?>
            <section class="stark-case-study__section" aria-labelledby="stark-cs-<?php echo esc_attr( $key ); ?>">
                <div class="stark-home__inner stark-story__inner">
                    <h2 id="stark-cs-<?php echo esc_attr( $key ); ?>" class="stark-case-study__heading"><?php echo esc_html( $label ); ?></h2>
                    <div class="stark-prose"><?php echo wp_kses_post( $work[ $key ] ); ?></div>
                </div>
            </section>
        <?php endforeach; ?>

        <?php if ( $work['gallery'] ) : ?>
            <section class="stark-case-study__gallery" aria-label="Project gallery">
                <div class="stark-home__inner">
                    <ul class="stark-gallery stark-gallery--<?php echo esc_attr( min( count( $work['gallery'] ), 3 ) ); ?>">
                        <?php foreach ( stark_field( 'gallery', $work['id'] ) as $row ) :
                            if ( empty( $row['image']['ID'] ) ) continue; ?>
                            <li>
                                <figure>
                                    <a href="<?php echo esc_url( wp_get_attachment_url( $row['image']['ID'] ) ); ?>">
                                        <?php echo wp_get_attachment_image( $row['image']['ID'], 'large', false, array( 'loading' => 'lazy' ) ); ?>
                                    </a>
                                    <?php if ( ! empty( $row['caption'] ) ) : ?>
                                        <figcaption><?php echo esc_html( $row['caption'] ); ?></figcaption>
                                    <?php endif; ?>
                                </figure>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <?php if ( $work['testimonial'] ) : $t = $work['testimonial']; ?>
            <section class="stark-quote-band" aria-label="Client testimonial">
                <div class="stark-home__inner">
                    <figure class="stark-quote">
                        <blockquote><p><?php echo esc_html( $t['quote'] ); ?></p></blockquote>
                        <?php if ( ! empty( $t['name'] ) ) : ?>
                            <figcaption>
                                <strong><?php echo esc_html( $t['name'] ); ?></strong>
                                <?php if ( ! empty( $t['role'] ) ) : ?><span><?php echo esc_html( $t['role'] ); ?></span><?php endif; ?>
                            </figcaption>
                        <?php endif; ?>
                    </figure>
                </div>
            </section>
        <?php endif; ?>

    </article>

    <nav class="stark-case-nav" aria-label="More case studies">
        <div class="stark-home__inner stark-case-nav__inner">
            <?php if ( $prev ) : ?>
                <a class="stark-case-nav__link stark-case-nav__link--prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>"><span>&larr; Previous</span><strong><?php echo esc_html( stark_field( 'client_name', $prev->ID ) ?: get_the_title( $prev ) ); ?></strong></a>
            <?php else : ?><span></span><?php endif; ?>
            <a class="stark-case-nav__all" href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ); ?>">All work</a>
            <?php if ( $next ) : ?>
                <a class="stark-case-nav__link stark-case-nav__link--next" href="<?php echo esc_url( get_permalink( $next ) ); ?>"><span>Next &rarr;</span><strong><?php echo esc_html( stark_field( 'client_name', $next->ID ) ?: get_the_title( $next ) ); ?></strong></a>
            <?php else : ?><span></span><?php endif; ?>
        </div>
    </nav>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
