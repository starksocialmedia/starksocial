<?php
/**
 * Related services — 3 cards, same card as the homepage services preview.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! $args['related'] ) return;
?>
<section class="stark-band stark-related" aria-labelledby="stark-related-title">
    <div class="stark-home__inner">
        <header class="stark-section-head">
            <p class="stark-eyebrow">Keep exploring</p>
            <h2 id="stark-related-title" class="stark-section-head__title">Related services</h2>
        </header>
        <ul class="stark-services__grid stark-services__grid--3">
            <?php foreach ( $args['related'] as $card ) : ?>
                <li class="stark-service-card">
                    <span class="stark-service-card__icon" aria-hidden="true"></span>
                    <h3 class="stark-service-card__title"><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['name'] ); ?></a></h3>
                    <p class="stark-service-card__desc"><?php echo esc_html( $card['desc'] ); ?></p>
                    <span class="stark-service-card__more" aria-hidden="true">Learn more &rarr;</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
