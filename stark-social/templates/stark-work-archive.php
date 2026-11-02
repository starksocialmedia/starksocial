<?php
/**
 * Stark work archive (v27.0.12) — /work/
 * Hero, industry + service filters (js/stark-work-filter.js), case study grid.
 * Without JS every card shows; filters only hide.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$items = array();
while ( have_posts() ) {
    the_post();
    $items[] = stark_portfolio_data();
}
wp_reset_postdata();

$industries = array();
$services   = array();
foreach ( $items as $item ) {
    if ( $item['industry'] ) $industries[ sanitize_title( $item['industry'] ) ] = $item['industry'];
    $services += $item['services'];
}
ksort( $industries );
// Keep service filters in registry order.
$services = array_intersect_key( wp_list_pluck( stark_get_services(), 'name' ), $services );

$hero = array(
    'eyebrow'       => 'Our work',
    'title'         => 'Selected work',
    'thesis'        => 'Websites, brands and marketing for businesses across Southern California. Real clients, real problems, and what we did about them.',
    'cta_primary'   => null,
    'cta_secondary' => null,
);

$cta = array(
    'final_cta' => array(
        'title'  => 'Your business could be next.',
        'sub'    => "Tell us what you're working on. We'll tell you honestly how we'd approach it.",
        'button' => array( 'url' => '/contact/', 'title' => 'Start a project', 'target' => '' ),
    ),
);

$filter_groups = array(
    'industry' => array( 'label' => 'Industry', 'options' => $industries ),
    'service'  => array( 'label' => 'Service', 'options' => $services ),
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

<main id="main" class="stark-page stark-work">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>

    <section class="stark-band stark-work__list" aria-label="Case studies">
        <div class="stark-home__inner">

            <?php if ( count( $items ) > 1 ) : ?>
                <div class="stark-work__filters" data-stark-work-filters>
                    <?php foreach ( $filter_groups as $key => $group ) :
                        if ( count( $group['options'] ) < 2 ) continue; ?>
                        <div class="stark-work__filter" role="group" aria-label="Filter by <?php echo esc_attr( strtolower( $group['label'] ) ); ?>">
                            <span class="stark-work__filter-label"><?php echo esc_html( $group['label'] ); ?></span>
                            <button type="button" class="stark-chip" data-filter="<?php echo esc_attr( $key ); ?>" data-value="all" aria-pressed="true">All</button>
                            <?php foreach ( $group['options'] as $value => $label ) : ?>
                                <button type="button" class="stark-chip" data-filter="<?php echo esc_attr( $key ); ?>" data-value="<?php echo esc_attr( $value ); ?>" aria-pressed="false"><?php echo esc_html( $label ); ?></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <p class="screen-reader-text" aria-live="polite" data-stark-work-count></p>
                </div>
            <?php endif; ?>

            <?php if ( $items ) : ?>
                <div class="stark-work-grid">
                    <?php foreach ( $items as $item ) : ?>
                        <article class="stark-work-card" data-industry="<?php echo esc_attr( $item['industry'] ? sanitize_title( $item['industry'] ) : '' ); ?>" data-service="<?php echo esc_attr( implode( ' ', array_keys( $item['services'] ) ) ); ?>">
                            <div class="stark-work-card__media">
                                <?php if ( $item['card_id'] ) echo wp_get_attachment_image( $item['card_id'], 'large', false, array( 'loading' => 'lazy', 'alt' => '' ) ); ?>
                            </div>
                            <div class="stark-work-card__body">
                                <p class="stark-work-card__meta">
                                    <?php echo esc_html( implode( ' · ', array_filter( array( $item['industry'], $item['year'] ) ) ) ); ?>
                                </p>
                                <h2 class="stark-work-card__title"><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['client'] ); ?></a></h2>
                                <?php if ( $item['summary'] ) : ?>
                                    <p class="stark-work-card__summary"><?php echo esc_html( $item['summary'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( $item['results'] ) : ?>
                                    <dl class="stark-work-card__stats">
                                        <?php foreach ( array_slice( $item['results'], 0, 2 ) as $stat ) : ?>
                                            <div>
                                                <dt><?php echo esc_html( $stat['label'] ); ?></dt>
                                                <dd><?php echo esc_html( $stat['value'] ); ?></dd>
                                            </div>
                                        <?php endforeach; ?>
                                    </dl>
                                <?php endif; ?>
                                <span class="stark-work-card__more" aria-hidden="true">View case study &rarr;</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <p class="stark-work__empty" data-stark-work-empty hidden>No case studies match those filters yet. <button type="button" class="stark-link-button" data-stark-work-reset>Show all work</button></p>
            <?php else : ?>
                <p class="stark-blog__empty">Case studies are on the way.</p>
            <?php endif; ?>

        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
