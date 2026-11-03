<?php
/**
 * Stark service areas hub (v27.0.6 stub; full hub v27.4.9)
 * Editorial layout: hero, intro with a schematic map, one numbered section
 * per market (stark_market_hub_data(): local pages, case studies, posts —
 * only what exists), "Not on the list?" close. Market context and the intro
 * are [DRAFT] until Nathan writes them.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$markets = stark_get_markets();

$hero = array(
    'eyebrow'       => 'Service areas',
    'title'         => 'Southern California, from the Valley to the Antelope Valley.',
    'title_accent'  => 'Southern California',
    'thesis'        => 'Based in Santa Clarita, working with businesses across five markets.',
    'cta_primary'   => array( 'url' => '/contact/', 'title' => 'Start a project', 'target' => '' ),
    'cta_secondary' => array( 'url' => '/services/', 'title' => 'See the services', 'target' => '' ),
);

$cta = array(
    'final_cta' => array(
        'title'  => 'Not on the list?',
        'sub'    => 'Most of the work happens online, so distance rarely matters. Based in Southern California, supporting businesses nationwide.',
        'button' => array( 'url' => '/contact/', 'title' => 'Get in touch', 'target' => '' ),
    ),
);

// Schematic positions (not to scale) for the map: roughly where each market sits.
$pins = array(
    'bakersfield'         => array( 150, 60 ),
    'antelope-valley'     => array( 380, 215 ),
    'santa-clarita'       => array( 330, 320 ),
    'ventura-county'      => array( 120, 380 ),
    'san-fernando-valley' => array( 320, 420 ),
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

<aside class="stark-draft-banner" aria-label="Draft notice"><strong>[DRAFT - needs Nathan review]</strong> <span class="stark-draft-banner__more">The intro and market descriptions are placeholder copy.</span></aside>

<main id="main" class="stark-page stark-areas">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>

    <section class="stark-band stark-areas__intro" aria-labelledby="stark-areas-intro-title">
        <div class="stark-home__inner stark-areas__intro-inner">
            <div class="stark-areas__intro-copy">
                <p class="stark-eyebrow">Home base: Santa Clarita</p>
                <h2 id="stark-areas-intro-title" class="stark-section-head__title">Five markets, five different playing fields.</h2>
                <p class="stark-areas__lead"><mark class="stark-draft-mark">[DRAFT]</mark> The work happens online, but local markets don't behave the same. The businesses you compete with in Palmdale aren't the ones in Thousand Oaks, and people search differently in Bakersfield than in Burbank. We'd rather know the market than guess at it.</p>
                <nav class="stark-areas__jump" aria-label="Markets">
                    <ul>
                        <?php foreach ( $markets as $slug => $m ) : ?>
                            <li><a href="#<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $m['name'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
            <figure class="stark-areas__map">
                <svg viewBox="0 0 600 480" aria-hidden="true" focusable="false">
                    <?php foreach ( $pins as $slug => $xy ) : if ( 'santa-clarita' === $slug ) continue; ?>
                        <line x1="330" y1="320" x2="<?php echo (int) $xy[0]; ?>" y2="<?php echo (int) $xy[1]; ?>" class="stark-areas__route"/>
                    <?php endforeach; ?>
                    <?php foreach ( $pins as $slug => $xy ) : $home = 'santa-clarita' === $slug; ?>
                        <g class="stark-areas__pin<?php echo $home ? ' stark-areas__pin--home' : ''; ?>">
                            <circle cx="<?php echo (int) $xy[0]; ?>" cy="<?php echo (int) $xy[1]; ?>" r="<?php echo $home ? 14 : 9; ?>"/>
                            <text x="<?php echo (int) $xy[0] + ( $home ? 22 : 16 ); ?>" y="<?php echo (int) $xy[1] + 6; ?>"><?php echo esc_html( $markets[ $slug ]['name'] ); ?></text>
                        </g>
                    <?php endforeach; ?>
                </svg>
                <figcaption>Not to scale</figcaption>
            </figure>
        </div>
    </section>

    <section class="stark-band stark-areas__markets" aria-labelledby="stark-areas-title">
        <div class="stark-home__inner">
            <h2 id="stark-areas-title" class="screen-reader-text">Our markets</h2>
            <ol class="stark-market-list">
                <?php $n = 0; foreach ( $markets as $slug => $m ) : $n++; $d = stark_market_hub_data( $slug ); ?>
                    <li class="stark-market" id="<?php echo esc_attr( $slug ); ?>">
                        <div class="stark-market__head">
                            <p class="stark-market__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $n ) ); ?></p>
                            <h3 id="stark-market-<?php echo esc_attr( $slug ); ?>" class="stark-market__name"><?php echo esc_html( $m['name'] ); ?></h3>
                            <p class="stark-market__desc"><mark class="stark-draft-mark">[DRAFT]</mark> <?php echo esc_html( $m['desc'] ); ?></p>
                        </div>
                        <div class="stark-market__body">
                            <?php if ( $d['local'] ) : ?>
                                <div class="stark-market__group">
                                    <h4 class="stark-market__label">Local pages</h4>
                                    <ul>
                                        <?php foreach ( $d['local'] as $l ) : ?>
                                            <li><a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['title'] ); ?> <span aria-hidden="true">&rarr;</span></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if ( $d['cases'] ) : ?>
                                <div class="stark-market__group">
                                    <h4 class="stark-market__label">Work in <?php echo esc_html( $m['name'] ); ?></h4>
                                    <ul>
                                        <?php foreach ( $d['cases'] as $c ) : ?>
                                            <li><a href="<?php echo esc_url( get_permalink( $c ) ); ?>"><?php echo esc_html( get_the_title( $c ) ); ?></a> <span class="stark-market__meta">Case study</span></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if ( $d['posts'] ) : ?>
                                <div class="stark-market__group">
                                    <h4 class="stark-market__label">On the blog</h4>
                                    <ul>
                                        <?php foreach ( $d['posts'] as $p ) : ?>
                                            <li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <div class="stark-market__group stark-market__group--services">
                                <h4 class="stark-market__label">Services</h4>
                                <p>All seven, run from Santa Clarita. <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">See the services <span aria-hidden="true">&rarr;</span></a></p>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
