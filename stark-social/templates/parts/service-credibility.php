<?php
/**
 * Local credibility block — location pages only (v27.0.5).
 * Heading + body, stat row, areas served, optional local testimonial.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$cred = $args['credibility'];
if ( ! $cred ) return;
?>
<section class="stark-band stark-cred" aria-labelledby="stark-cred-title">
    <div class="stark-home__inner stark-cred__inner">
        <div class="stark-cred__text">
            <p class="stark-eyebrow"><?php echo esc_html( $args['market'] ); ?></p>
            <h2 id="stark-cred-title" class="stark-section-head__title"><?php echo esc_html( $cred['heading'] ); ?></h2>
            <?php if ( $cred['body'] ) : ?>
                <div class="stark-cred__body"><?php echo wp_kses_post( wpautop( $cred['body'] ) ); ?></div>
            <?php endif; ?>
            <?php if ( $cred['areas'] ) : ?>
                <p class="stark-cred__areas-label">Areas we serve</p>
                <ul class="stark-cred__areas">
                    <?php foreach ( $cred['areas'] as $area ) : ?>
                        <li><?php echo esc_html( $area ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <?php if ( $cred['stats'] || $cred['quote'] ) : ?>
            <div class="stark-cred__aside">
                <?php if ( $cred['stats'] ) : ?>
                    <dl class="stark-cred__stats">
                        <?php foreach ( $cred['stats'] as $stat ) : ?>
                            <div class="stark-cred__stat">
                                <dt><?php echo esc_html( $stat['label'] ); ?></dt>
                                <dd><?php echo esc_html( $stat['value'] ); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
                <?php if ( $cred['quote'] ) : ?>
                    <figure class="stark-cred__quote">
                        <blockquote><p><?php echo esc_html( $cred['quote'] ); ?></p></blockquote>
                        <?php if ( $cred['quote_name'] ) : ?>
                            <figcaption><?php echo esc_html( $cred['quote_name'] ); ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
