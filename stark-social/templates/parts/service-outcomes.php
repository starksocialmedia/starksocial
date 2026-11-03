<?php
/**
 * Outcomes — what changes (v27.3.0, from the live pages). Card grid.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$o = $args['outcomes'] ?? null;
if ( empty( $o['items'] ) ) return;
?>
<section class="stark-band stark-outcomes" <?php echo $o['heading'] ? 'aria-labelledby="stark-outcomes-title"' : 'aria-label="' . esc_attr( $o['eyebrow'] ) . '"'; ?>>
    <div class="stark-home__inner">
        <?php if ( $o['heading'] ) : // Some live pages have no heading here (e.g. /services/). ?>
            <header class="stark-section-head">
                <p class="stark-eyebrow"><?php echo esc_html( $o['eyebrow'] ); ?></p>
                <h2 id="stark-outcomes-title" class="stark-section-head__title"><?php echo esc_html( $o['heading'] ); ?></h2>
                <?php if ( $o['intro'] ) : ?><p class="stark-section-head__intro"><?php echo esc_html( $o['intro'] ); ?></p><?php endif; ?>
            </header>
        <?php endif; ?>
        <ul class="stark-cards stark-cards--<?php echo esc_attr( min( 4, count( $o['items'] ) ) ); ?>">
            <?php foreach ( $o['items'] as $item ) : ?>
                <li class="stark-cards__item">
                    <h3 class="stark-cards__title"><?php echo esc_html( $item['title'] ); ?></h3>
                    <?php if ( ! empty( $item['description'] ) ) : ?><p class="stark-cards__desc"><?php echo esc_html( $item['description'] ); ?></p><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if ( $o['note'] ) : ?><p class="stark-section-note"><?php echo esc_html( $o['note'] ); ?></p><?php endif; ?>
    </div>
</section>
