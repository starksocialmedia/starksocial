<?php
/**
 * Final CTA band — reuses the homepage .stark-cta-band.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$cta = $args['final_cta'];
?>
<section class="stark-cta-band" aria-labelledby="stark-cta-title">
    <div class="stark-home__inner stark-cta-band__inner">
        <h2 id="stark-cta-title" class="stark-cta-band__title"><?php echo esc_html( $cta['title'] ); ?></h2>
        <?php if ( $cta['sub'] ) : ?>
            <p class="stark-cta-band__sub"><?php echo esc_html( $cta['sub'] ); ?></p>
        <?php endif; ?>
        <?php if ( $cta['button'] ) : ?>
            <a href="<?php echo esc_url( $cta['button']['url'] ); ?>" class="stark-btn stark-btn--primary"><?php echo esc_html( $cta['button']['title'] ); ?></a>
        <?php endif; ?>
        <?php if ( ! empty( $cta['note'] ) ) : ?>
            <p class="stark-cta-band__note"><?php echo esc_html( $cta['note'] ); ?></p>
        <?php endif; ?>
    </div>
</section>
