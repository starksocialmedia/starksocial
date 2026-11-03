<?php
/**
 * What's included — 3-5 icon bullets.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! $args['included'] ) return;
?>
<section id="stark-included" class="stark-band stark-included" aria-labelledby="stark-included-title">
    <div class="stark-home__inner">
        <header class="stark-section-head">
            <p class="stark-eyebrow"><?php echo esc_html( $args['service_name'] ); ?></p>
            <h2 id="stark-included-title" class="stark-section-head__title"><?php echo esc_html( $args['included_heading'] ); ?></h2>
            <?php if ( ! empty( $args['included_intro'] ) ) : ?><p class="stark-section-head__intro"><?php echo esc_html( $args['included_intro'] ); ?></p><?php endif; ?>
        </header>
        <ul class="stark-included__list">
            <?php foreach ( $args['included'] as $item ) : ?>
                <li class="stark-included__item">
                    <span class="stark-included__icon"><?php echo stark_service_icon( isset( $item['icon'] ) ? $item['icon'] : 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                    <div>
                        <h3 class="stark-included__title"><?php echo esc_html( $item['title'] ); ?></h3>
                        <?php if ( ! empty( $item['description'] ) ) : ?>
                            <p class="stark-included__desc"><?php echo esc_html( $item['description'] ); ?></p>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if ( ! empty( $args['included_note'] ) ) : ?><p class="stark-section-note"><?php echo esc_html( $args['included_note'] ); ?></p><?php endif; ?>
    </div>
</section>
